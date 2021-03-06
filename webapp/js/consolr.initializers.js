(function($) {
    var DATE_FORMAT = "dd NNN yyyy HH:mm:ss";

    $.fn.initTooltipPhotoPost = function(settings) {
        var config = {datePropName: 'consolr-date',
                        captionMaxChars : 60,
                        tagsMaxChars : 60,
                        dateFormat: "HH:mm:ss"};

        if (settings) {
            $.extend(config, settings);
        }
        this.tooltip({
            extraClass: 'ui-state-highlight',
            bodyHandler: function() {
                var post = consolr.findPost(this.id);
                if (!post) {
                    return "";
                }
                // different post types use different property
                var postCaption = post.caption || post.title || post.quote || '';
                var caption = $(postCaption).text();
                // If text() returns an empty string uses the caption
                caption = $.cropText(caption || postCaption, config.captionMaxChars);

                var tags = consolr.tags.fetchTagsLastPublishTime(tumblrName, post['tags']);
                var tagPublishDaysAgo = consolr.tags.formatTagsPublishDaysAgo(tags.tags);

                return $("<div>"
                         + "<span class='tooltip-caption'>" + caption + "</span>"
                         + "<span class='tooltip-tags'>" + tagPublishDaysAgo + "</span>"
                         + "</div>").html();
            },
            showURL: false
        });
        return this;
    };

    $.fn.initDraggableImage = function(settings) {
        var config = {datePropName: 'consolr-date',
                        connectWith: '.date-image-container',
                        placeholder: 'date-image date-image-drop-placeholder ui-state-highlight',
                        postTimeSpan : 2};

        if (settings) {
            $.extend(config, settings);
        }

        var dragSource;
        this.sortable({
            connectWith: config.connectWith,
            placeholder: config.placeholder,
            forcePlaceholderSize : true,
            receive: function(event, ui) {
                // save sender used inside the stop() method
                dragSource = ui.sender;
            },
            start: function(event, ui) {
                $('#imageMenuHandler').trigger('menuhandler.enable', [false]);
            },
            stop: function(event, ui) {
                var prevTime = null;
                var nextTime = null;
                var post = consolr.findPost(ui.item.get(0).id);
                var currTime = post[config.datePropName];

                $('#imageMenuHandler').trigger('menuhandler.enable', [true]);
                if (ui.item.prev().length) {
                    prevTime = consolr.findPost(ui.item.prev()
                                            .get(0).id)[config.datePropName];
                }
                if (ui.item.next().length) {
                    nextTime = consolr.findPost(ui.item.next()
                                            .get(0).id)[config.datePropName];
                }

                var newDate;
                if (prevTime || nextTime) {
                    newDate = consolr.adjustTime(prevTime,
                                                 nextTime,
                                                 config.postTimeSpan);
                } else {
                    var id = ui.item.parent('ul').attr('id');
                    newDate = new Date(parseInt(id.substring(2, 6), 10),
                                parseInt(id.substring(6, 8), 10) - 1,
                                parseInt(id.substring(8), 10),
                                currTime.getHours(),
                                currTime.getMinutes(),
                                currTime.getSeconds());
                }

                var params = {
                    postId : post['id'],
                    publishDate : newDate.format(DATE_FORMAT),
                    caption : post.caption || post.title || post.quote || '',
                    clickThroughLink : post['link_url'],
                    tags : post['tags'] ? post['tags'].join(", ") : ""
                };
                consolr.updateQueuedPost(params, {
                        success: function(params) {
                            consolr.refreshImagePosition(params, false);
                            },
                        error : function() {
                            // stop doesn't receive a valid sender so get the item parent
                            // if dragSource isn't set then the item has been moved
                            // inside same container otherwise has been set
                            // inside receive() method
                            $(dragSource ? dragSource : ui.item.parent('ul')).sortable('cancel');
                            dragSource = undefined;
                        }
                        });
                consolr.updateMessagePanel();
            }
        }).disableSelection();
        return this;
    };

    $.extend({
        initEditor : function() {
            tinyMCE.init({
                mode : "none",
                theme : "advanced",
                convert_newlines_to_brs : false,
                remove_trailing_nbsp : true,
                theme_advanced_toolbar_location : "top",
                theme_advanced_buttons1 :
                    "bold,italic,separator,bullist,numlist,separator,image,link,unlink,code",
                theme_advanced_buttons2 : "",
                theme_advanced_buttons3 : "",
                width: '100%'
            });
        }
    });

    var menuHandler = {enable: true};

    function createMenuHandler(settings) {
        if (menuHandler.element) {
            return;
        }
        menuHandler.element = $('<span id="' + settings.id + '" class="' + settings.cssClasses + '"></span>')
            .appendTo(document.body)
            // item is always visible but out of screen, this prevents problems
            // setting position using offset() on webkit
            .css('left', '-9999px')
            .bind('menuhandler.enable', function(e, enable) {
                menuHandler.enable = enable === true;
                if (!menuHandler.enable) {
                    $(this).css('left', '-9999px');
                }
            });
    }

    $.fn.initImageMenu = function(settings) {
        var config = {id: 'imageMenuHandler',
                        cssClasses: 'menu-handle',
                        showOnClick: false,
                        showOnHover: true};

        if (settings) {
            $.extend(config, settings);
        }
        createMenuHandler(config);
        var menuEls = $([]);

        if (config.showOnClick) {
            menuEls = menuEls.add(this);
        }
        
        if (config.showOnHover) {
            menuEls = menuEls.add(menuHandler.element);
            this.hover(function(e) {
                    if (!menuHandler.enable) {
                        return;
                    }
                    var el = $(this);
                    menuHandler.current = el;
                    var menuPos = el.offset();

                    // on webkit offsets() doesn't set correctly the position
                    // at first call time so we set manually left and top
                    menuPos.left += el.width() - menuHandler.element.width();
                    menuHandler.element.css('left', menuPos.left + 'px');
                    menuHandler.element.css('top', menuPos.top + 'px');
                },
                function(e) {
                    // don't hide if we are on menuhandler
                    if (e.relatedTarget !== menuHandler.element[0]) {
                        menuHandler.element.css('left', '-9999px');
                    }
                });
        }

        menuEls.contextMenu({
                menu: 'imageMenu',
                buttons: "L"
                },

                function(action, el, pos, e) {
                    // el is valid for showOnClick, menuHandler.current is valid for showOnHover
                    var li = menuHandler.current || el;
                    switch (action) {
                        case 'edit':
                            $('#dialog-form').dialog('option', 'postInfo', li);
                            $('#dialog-form').dialog('open');
                            break;
                        case 'showImage':
                            var post = consolr.findPost(li.attr('id'));
                            window.open(post.photos[0].alt_sizes[0].url);
                            break;
                        case 'delete':
                            if (confirm("Do you want to delete this post?")) {
                                var post = consolr.findPost(li.attr('id'));
                                consolr.deletePost(post);
                            }
                            break;
                        case 'publish':
                            //if (e.shiftKey || confirm("Do you want to publish this post now?")) {
                                var post = consolr.findPost(li.attr('id'));
                                consolr.publishPost(post);
                                consolr.tags.evictTagsLastPublishTime(post.tags);
                            //}
                            break;
                        case 'showInfo':
                            var post = consolr.findPost(li.attr('id'));
                            $("#dialogs-container").showInfoDialog({post:post});
                            break;
                        default:
                            alert(action + " is not implemented");
                            break;
                    }
                });
        return this;
    };
})(jQuery);