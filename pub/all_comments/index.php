<title>all comments</title>
<link rel=stylesheet href=/cee_ess_ess>
<pre>
<?php
include dirname(dirname(dirname(__FILE__))) . '/engine/vendor/autoload.php';
$pages = r()->scan($cursor, "comments:*", 1000);
foreach ($pages[1] as $p) {
    $c = r()->llen($p);
    $p = substr($p, strlen("comments:"));
    echo "<a href=/comments/$p>$p</a> ($c)\n";
}
