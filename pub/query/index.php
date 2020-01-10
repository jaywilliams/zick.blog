<meta name=viewport content="width=device-width,initial-scale=1.0"> 
<link rel=stylesheet href=/cee_ess_ess>
<?php
include dirname(dirname(dirname(__FILE__))) . '/engine/vendor/autoload.php';

if ($_REQUEST{'tag'}) {
    $tags = json_decode(r()->get('tag_link_index')?:'[]', 1);
    echo "<title>posts tagged " . bucket($_REQUEST{'tag'}) . "</title>";
    echo "<pre>";
    echo "<a href=..>..</a>\n";
    foreach ($tags{bucket($_REQUEST{'tag'})} as $t) {
            echo "<a href=/$t>$t</a>\n";
    }
} elseif ($_REQUEST{'search'}) {
    $lemmatizer = new Skyeng\Lemmatizer();
    $delims = " \n\t/-~*.;:,!#(){}";
    $word = strtok(strip_tags($_REQUEST{'search'}), $delims);
    $w = [];
    $mp = [];
    $t = [];
    while ($word) {
        foreach($lemmatizer->getOnlyLemmas(strtolower($word)) as $l) {
            $t[] = $l;
            $w += r(1)->smembers("word:$l");
            $mp += r(1)->smembers("mp:" . metaphone($l));
        }
        $word = strtok($delims);
    }

    echo "<title>searching for ";
    echo implode(array_map('strtoupper', $t), ",") . "</title>";
    echo "<pre>";
    echo "<a href=..>..</a>\n";
    foreach (array_unique($w) as $t) {
        echo "<a href=/$t>$t</a>\n";
    }
    foreach (array_unique(array_diff($mp, $w)) as $t) {
        echo "<a href=/$t>$t</a> <sy>(phonetic)</sy>\n";
    }
} else {
    echo "<title>400</title>";
    echo "<pre>derp. don't know how to make that query.";
    echo "<style>*{color:#433838}";
}

