<?php include dirname(dirname(__FILE__)) . '/engine/vendor/autoload.php'; ?>
<meta name=viewport content="width=device-width,initial-scale=1.0"> 
<link rel=alternate type=application/json href=feed.json>
<link rel=alternate type=application/atom+xml href=feed.atom>
<link rel=alternate type=application/rss+xml href=feed.rss>
<link rel=stylesheet href=cee_ess_ess>
<title>bloggy blog blog</title> (<a href=feed.json>json</a>, <a href=feed.atom>atom</a>, <a href=feed.rss>rss</a>)
<pre>
<?php
if (!r()->get('hold_index')) {
//if (true) {
    $lemmatizer = new Skyeng\Lemmatizer();
    $posts = [];
    $tags = [];
    $tags_links = [];
    $words = [];
    foreach (glob("{19,20}*/*/index.php", GLOB_BRACE) as $k=>$f) {
        $c = file_get_contents($f);

        if (strpos($c, "<draft>") !== false || strpos($c, "<hidden>") !== false) {
            continue;
        }

        $delims = " \n\t/-~*.;:,!#(){}";
        $word = strtok(strip_tags($c), $delims);
        while ($word) {
            foreach($lemmatizer->getOnlyLemmas(strtolower($word)) as $l) {
                r(1)->sadd("word:$l", dirname($f));
                metaphone($l) ? r(1)->sadd("mp:" . metaphone($l), dirname($f)) : null;
            }
            $word = strtok($delims);
        }

        if (preg_match_all("#<title>([^<]+)</title>|<tag>([^<]+)</tag>|<author>([^<]+)</author>|<time>([^<]+)</time>#", $c, $matches)) {
            $title = array_slice(array_filter($matches[1]), 0, 1)[0];
            $ttags = array_filter($matches[2]);
            $author = array_slice(array_filter($matches[3]), 0, 1)[0];
            $time  = array_slice(array_filter($matches[4]), 0, 1)[0];
            $posts["$time.$f"] = compact('title','ttags','author','time','f');
            foreach ($ttags as $t) {
                $tags[$t]++;
            }
        }
    }
    ksort($posts);
    asort($tags);
    $posts = array_reverse($posts);
    $tags = array_reverse($tags);

    $site_url = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]";
    $json = [];
    $atom = fopen("feed.atom", "w");
    $rss = fopen("feed.rss", "w");

    fwrite($atom, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<feed xmlns=\"http://www.w3.org/2005/Atom\">\n");
    fwrite($atom, "<title>" . config("title") . "</title>\n");
    fwrite($atom, "<id>$site_url/</id>\n");
    fwrite($atom, "<link href=\"$site_url/feed.atom\" rel=\"self\"/>\n");
    fwrite($atom, "<updated>" . date(DATE_ATOM) . "</updated>\n");

    fwrite($rss, "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n<channel>\n");
    fwrite($rss, "<title>" . config("title") . "</title>\n");
    fwrite($rss, "<description>" . config("desc") . "</description>\n");
    fwrite($rss, "<link>$site_url</link>\n");
    fwrite($rss, "<atom:link href=\"$site_url/feed.rss\" rel=\"self\" type=\"application/rss+xml\"/>\n");

    foreach ($posts as $p) {
        $f = dirname($p['f']);
        $u = "$site_url/$f/";
        $i = bucket($f);

        foreach ($p['ttags'] as $t) {
            $tags_links[bucket($t)][] = $f;
        }

        fwrite($rss, "<item><link>$site_url/$f</link><title>$p[title]</title><guid isPermaLink=\"false\">$i</guid></item>\n");

        fwrite($atom, "<entry>\n");
        fwrite($atom, "    <id>$u</id>\n");
        fwrite($atom, "    <updated>$p[time]</updated>\n");
        fwrite($atom, "    <link href=\"$u\"/ rel=\"alternate\">\n");
        fwrite($atom, "    <title>$p[title]</title>\n");
        fwrite($atom, "    <author><name>$p[author]</name></author>\n");
        fwrite($atom, "</entry>\n");

        $json[] = [
            "id"    => $i,
            "url"   => $u,
            "title" => $p['title'],
            "tags"  => array_values($p['ttags']),
            "date_published" => $p['time'],
            "content_html" => "<a href=\"$u\">Full Post</a>",
            "content_text" => "full post: $u",
        ];
    }

    $json_feed = [
        "version"       => "https://jsonfeed.org/version/1",
        "title"         => config("title"),
        "home_page_url" => $site_url,
        "feed_url"      => "$site_url/feed.json",
        "items"         => $json,
    ];

    file_put_contents('feed.json', json_encode($json_feed));
    fwrite($atom, "</feed>");
    fclose($atom);
    fwrite($rss, "</channel>\n</rss>");
    fclose($rss);

    r()->set('post_index', json_encode($posts));
    r()->set('tag_index', json_encode($tags));
    r()->set('tag_link_index', json_encode($tags_links));
    r()->setex('hold_index', 60*60*24, 1);
} else {
    $posts = json_decode(r()->get('post_index')?:'[]', 1);
    $tags = json_decode(r()->get('tag_index')?:'[]', 1);
}

foreach ($posts as $p) {
    echo substr($p['time'], 0, 10) . ' <a href=/' . dirname($p['f']) . '/>' . strtolower($p['title']) . '</a>';
    //echo '<a href=/' . dirname($p['f']) . "/>" . dirname($p['f']) . "</a>";
    //foreach ($p['ttags'] as $t) echo " <tag>$t</tag>";
    echo "\n";
}

echo "\n<h1>tags</h1>\n";

foreach ($tags as $t => $len) {
    $t = preg_replace("/[^a-z0-9]/", "_", $t);
    echo "<a href=query?tag=$t>$t</a> ($len)\n";
}

echo "\n<h1>search</h1>\n";
?><form action=query><input name=search required><input type=submit></form>
this is a cookie free space
if you want tracking cookies, browse elsewhere
