<meta name=viewport content="width=device-width,initial-scale=1.0"> 
<link rel=stylesheet href=/cee_ess_ess>

<title>CSS and Vim Bookmarks for March</title> <tag>bookmarks</tag>

<pre>
<co>## CSS</co>

* <a href=http://meiert.com/en/blog/20080515/css-organization-and-efficiency/>Simple Rules for Organization and Efficiency</a>
* <a href=http://ianstormtaylor.com/oocss-plus-sass-is-the-best-way-to-css/>OOCSS + Sass = The best way to CSS</a>
* <a href=http://css-tricks.com/snippets/css/clear-fix/>The famous "clear-fix"</a>
* <a href=http://css-tricks.com/snippets/css/css-diagnostics/>Diagnostic CSS</a>
* <a href=http://www.zeldman.com/2012/03/01/replacing-the-9999px-hack-new-image-replacement/>Very simple image replacement hack</a>


<co>## Vim</co>

* <a href=http://www.oualline.com/vim-cook.html>Vim cookbook</a>
* <a href=http://www.vimninjas.com/2012/09/19/replace-multiple/>Replace in Multiple File</a>
* <a href=http://www.vimninjas.com/2012/09/03/5-plugins-you-should-put-in-your-vimrc/>5 Plugins You Should Put in Your Vimrc</a>


I've also been working on some useful vim regexes:

    In Vim, search for selectors starting with "#main"
    and make them also select ".window":

      s/\(#main \)\(.\+\)\([,|{]\)/\1\2, .window \2\3/g

    For adding an HTML "value" attribute built from the
    "name" attribute:

      s/\(name="\)\([a-z\[\]]\+\)\("\)/\1\2\3 value="{{\2}}"/g

    Join verbose-ish CSS into one line:

      s/\([;{]\)\n[ ^I]*/\1 /g

    Split one-line CSS into verbose-ish:

      s/\([{;]\)[ ^I]*\(}*\)/\1\r\2  /g


Cheers!

<co>~~</co>
<author>Brian Zick</author> <time>2013-03-20T11:27:22-04:00</time> <?php require '../../../engine/comment_link.php'; ?>
