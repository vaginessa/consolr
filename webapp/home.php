<?php
require 'lib/loginUtils.php';
require 'inc/dbconfig.php';
require 'lib/db.php';

$tumblr = login_utils::get_tumblr();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>Consolr - Home</title>
    
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>

        <link type="text/css" href="css/consolr.css" rel="stylesheet"/>
        <link type="text/css" href="css/dialogs.css" rel="stylesheet"/>
        <link type="text/css" href="css/consolr/jquery-ui.css" rel="stylesheet" />
        <link type="text/css" href="css/contextMenus.css" rel="stylesheet"/>
        <style>
        .toolbar {
            width: 500px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 40px;
        }
    
        .toolbar li {
            margin: 22px 0 15px 40px;
        }
        .toolbar .label {
            text-align:center
        }
    
        .logo-container {
          width: 35em;
          margin: 0 auto;
        }
        </style>
    </head>
    <body>
        <noscript>
            <div class="ui-state-error">
                <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=12654">Javascript</a> is required to view this site.
            </div>
        </noscript>
        <?php include('inc/menu.php') ?>
        <div class="logo-container ui-corner-all ui-state-highlight">
            <h1 style="padding: 0pt 0.7em; margin-top: 20px;">Consolr</h1>
        </div>
        <div class="toolbar">
        <ul class="date-image-container ui-corner-all">
            <li class="date-image"><a href="multiq.php"><img src="images/tb_upload.png" width="75" height="75"/><div class="date-image-time label">Multiple Upload</div></a></li>
            <li class="date-image"><a href="queue.php"><img src="images/tb_queue.png" width="75" height="75"/><div class="date-image-time label">Scheduled Posts</div></a></li>
            <li class="date-image"><a href="published.php"><img src="images/tb_published.png" width="75" height="75"/><div class="date-image-time label">Published Posts</div></a></li>
            <li class="date-image"><a href="settings.php"><img src="images/tb_settings.png" width="75" height="75"/><div class="date-image-time label">Settings</div></a></li>
        </li>
        </div>
    <?php include('inc/footer.php'); ?>
    </body>
</html>
