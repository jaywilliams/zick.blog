<?php
include dirname(dirname(__FILE__)) . '/engine/vendor/autoload.php';

$r = urldecode($_SERVER{'REQUEST_URI'});

if (strpos($r, '/comments/') === 0) {
    header('HTTP/1.1 200 OK');
    include '../engine/comment_list.php';
    exit;
}

$code = $_SERVER{'REDIRECT_STATUS'};
$status = [401=>"unauthorized", 403=>"forbidden", 404=>"not found"];
?>
<title><?=$code?></title>
<pre>derp. <?=$status[$code]?:"Error"?>.
