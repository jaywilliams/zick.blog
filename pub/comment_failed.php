<?php include dirname(dirname(__FILE__)) . '/engine/vendor/autoload.php'; ?>
<title>failed comments (<?=r()->llen("failed_comments")?>)</title>
<link rel=stylesheet href=/cee_ess_ess>
<pre>
<?php
foreach (r()->lrange("failed_comments", 0, 100) as $l) {
    echo "$l\n";
}
