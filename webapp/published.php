<?php
require_once 'lib/loginUtils.php';
require_once 'lib/tumblr/tumblrUtils.php';

$tumblr_name = login_utils::is_logged()
    ? login_utils::get_tumblr()->get_tumblr_name()
    : (isset($_GET['tumblrName']) ? $_GET['tumblrName'] : "");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>Consolr - Published Posts</title>

        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>

        <link href="css/consolr.css" type="text/css" rel="stylesheet"/>
        <link href="css/dialogs.css" type="text/css" rel="stylesheet"/>
        <link type="text/css" href="css/consolr/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.tooltip.min.js"></script>
        <script type="text/javascript" src="js/jquery.strings.js"></script>

        <script type="text/javascript" src="js/date.js"></script>
        <script type="text/javascript" src="js/consolr.js"></script>
        <script type="text/javascript" src="js/consolr.dialogs.js"></script>
        <script type="text/javascript" src="js/consolr.tooltips.js"></script>

        <script type="text/javascript" src="http://www.google.com/jsapi"></script>

        <script type="text/javascript">
        <!--//
            var tumblrName = "<?php echo $tumblr_name ?>";
            var apiUrl = 'http://' + tumblrName + '.tumblr.com/api/read/json';
            var consolrPosts = {};

            $(function() {
                if (!tumblrName) {
                    consolr.setMessageText("Invalid tumblr name");
                    return;
                }
                consolr.setMessageText("Reading...");
                $("#message-progress-container").show();

                consolr.readPublicPhotoPosts(apiUrl, {
                    start : 0,
                    posts : [],
                    progress : function(data, posts) {
                        consolr.setMessageText("Read posts " + posts.length + "/" + data['posts-total']);
                    },
                    complete : function(posts) {
                        consolrPosts.posts = posts;
                        // This ensure dates are normalized with client side timezone
                        $(consolrPosts['posts']).each(function(i, el) {
                            el['publish-unix-timestamp'] = new Date(el['unix-timestamp']).getTime();
                        });
                        consolrPosts["group-date"] = consolr.groupPostsByDate(consolrPosts.posts, 'date');
                        $("#date-container").html(consolr.getDateContainerHTML({
                                        dateProperty : 'date'}));

                        consolr.updatePostsCount();
                        // add tooltip when date container is filled
                        $("li").initTooltipPhotoPost({
                            datePropName: 'date'});
                        $("#message-progress-container").hide();
                    }
                    });

                $("#dialog-tags").initDialogTagsChart();
                $("#show-tags-chart").click(function() {
                    $('#dialog-tags').dialog('open');
                });
            });
        -->
        </script>
    </head>
    <body>
        <noscript>
            <div>
                <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=12654">Javascript</a> is required to view this site.
            </div>
        </noscript>
        <?php if (login_utils::is_logged()) include('inc/menu.php') ?>

        <div id="message-panel">
            <span id="message-progress-container" style="display: none">
                <img class="message-progress-indicator" src="images/progress.gif" alt="..."/>
            </span>
            <span id="message-text" class="message-text"></span>
        </div>

        <input type="button" id="show-tags-chart" value="Show Tags"/>

        <div id="date-container">
        </div>

<div style="display:none">
    <div id="dialog-tags" title="Tags Chart">
        <div id="tags-chart"></div>
    </div>
</div>

    </body>
</html>
