<?php
require_once 'lib/loginUtils.php';
require_once 'lib/tumblr/tumblrUtils.php';

define("CONSOLR_UPLOAD_OK", 0);
define("CONSOLR_UPLOAD_ERR_URL_MANDATORY", 1);
define("CONSOLR_UPLOAD_ERR_INVALID_DATE_FORMAT", 2);

// There is an upload error and the message is generic,
// the msg field contains the error description
define("CONSOLR_UPLOAD_ERR_GENERIC", 3);

$tumblr = login_utils::get_tumblr();

/**
 * Post photo by url
 * @return array (status, msg) msg is valid only for some statuses
 */
function post_photo_by_url($url, $caption, $str_time, $tags, $tumblr) {
    if (!isset($url)) {
        return array('status' => CONSOLR_UPLOAD_ERR_URL_MANDATORY);
    }
    $time = strtotime($str_time);
    if ($time === false) {
        return array('status' => CONSOLR_UPLOAD_ERR_INVALID_DATE_FORMAT, 'msg', $str_time);
    }

    $results = $tumblr->post_photo_to_queue($url,
                                            $caption,
                                            $str_time,
                                            explode(",", $tags));
    //$results = array('status' => '201', 'result' => 'TEST WITHOUT REAL POST');
    if ($results['status'] != 201) {
        return array('status' => CONSOLR_UPLOAD_ERR_GENERIC, 'msg' => $results['result']);
    }
    return array('status' => CONSOLR_UPLOAD_OK);
}

$url = $_POST['url'];
$caption = $_POST['caption'];
$date = $_POST['date'];
$tags = $_POST['tags'];

$results = post_photo_by_url($url, $caption, $date, $tags, $tumblr);
//$results = array('status' => (rand() % 2) != 0 ? CONSOLR_UPLOAD_OK : CONSOLR_UPLOAD_ERR_GENERIC, 'msg' => 'TEST WITHOUT REAL POST');
$msg;
switch ($results['status']) {
    case CONSOLR_UPLOAD_OK:
        break;
    case CONSOLR_UPLOAD_ERR_URL_MANDATORY:
        $msg = 'Url is mandatory';
        break;
    case CONSOLR_UPLOAD_ERR_INVALID_DATE_FORMAT:
        $msg = 'Invalid date format ' . $results['msg'];
        break;
    case CONSOLR_UPLOAD_ERR_GENERIC:
        $msg = $results['msg'];
        break;
}

if (isset($msg)) {
    header("HTTP/1.x 400 " . $msg);
}
?>