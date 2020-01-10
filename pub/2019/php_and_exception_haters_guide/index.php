<meta name=viewport content="width=device-width,initial-scale=1.0">
<link rel=stylesheet href=/cee_ess_ess>

<title>safe php and exceptions</title>

<tag>rant</tag>
<tag>programming</tag>

<pre>
<co>/* this was originally part of an email thread about a php
library called Safe */</co>

let's talk about <a href=https://thecodingmachine.io/introducing-safe-php>safe</a>. i have some mixed thoughts on this
topic in general. probably rant territory

my first thought, not about the impetus for Safe but the
outcome. i think that having a consistent interface is a
huge boon, and one of the most important (and sometimes
overlooked) interfaces is the error interface. i would
commend this effort as far as it goes. it could go
further. for example, these functions don't just differ
from the newer parts of SPL in how they handle errors,
they differ in every other way. for this reason, i think
projects along these lines offer a better approach in
terms of what they try to achieve

<a href=https://github.com/Anahkiasen/underscore-php>Underscore.php</a>
<a href=https://github.com/nette/utils>nette/utils</a>

that said, i don't like adding a lot of (often large)
dependencies that i then need to audit, be aware of
changes, often debug. it's my policy that whenever i can,
i read the _full source_ of any dependency i add. if it's
not possible to (or it's not possible to in addition
to an already large list of dependencies), i avoid.
i also like to keep the php i work with as "vanilla" as
possible because i can't expect _everyone_ on my team to be
familiar with a huge number of libraries, especially ones
that fundamentally change the coding style of whatever it
depends on. for that reason, i think Safe limited itself
just to making the errors use exceptions consistently.

that aside, my next concern is with impetus. i do admire
the intent, but i don't think this library will work well
with my style of programming in php. take a look at the
first example:

    <code>$content = file_get_contents('foobar.json');
    $foobar  = json_decode($content);</code>

which, is then translated to this to be "safe":

    <code>$content = file_get_contents('foobar.json');
    if ($content === false) {
        throw new FileLoadingException('Could not load file foobar.json');
    }
    $foobar = json_decode($content);
    if ($foobar === null) {
        throw new FileLoadingException('foobar.json does not contain valid JSON: '.json_last_error());
    }</code>

this is a red herring? i have _no idea_ why anybody
would write that particular piece of code, certainly not
in something that would be used by a consumer and not a
developer. usually, i write in a more declarative style,
and would have likely written that as

    <code>$content = file_get_contents('foobar.json') ?: 'false';
    $foobar  = json_decode($content);</code>

(with a json false string being a reasonable default
that would allow the code to run, although normally you
would supply some smart default that makes sense for
the situation)

another way this might be written (and this is also a
common case), would be as

    <code>$path = 'foobar.json';
    if (is_readable($path)) {
        $content = file_get_contents($path) ?: 'false';
        $foobar  = json_decode($content);
    }</code>

so this brings me to the first major assumption Safe makes:
that SPL's "errors" that silently return false really
are errors and should throw an exception, and not control
flow conditions. the original example is an area where
i _wouldn't_ want to throw exceptions in most cases. what
do i mean? i mean these are _expected_ code paths that i
should be handling, and static analysis like phan issuing
a warning is useful. is a file not existing an error? not
usually. and the exception that would throw is not one
i can just display to someone looking at a browser, so
i would have to add a lot of extra "catch" clauses and
regex filtering. simply put, although the first example
has problems, i don't think the solution is necessarily
_more exceptions_. you could still do similar wrapping,
(e.g. if is_readable) with Safe, and that seems comparable
to just using vanilla php, although getting analysis
warning is worthwhile.

a minor miff with php exceptions in general is that
there isn't a good way to suppress them in a selective
way. the old php `@` operator, granted it's a blunt tool,
was actually useful. unfortunately, it could only suppress
_all_ or _none_, so it's practical usefulness was very
limited. but in theory, it or something simple could be
implemented to take advantage of the exception grouping
(by class), and that would be a valid feature to allow
programming "the old way" -

    <code>@FileLoadingException $content = file_get_contents('foobar.json');
    <co>// or</co>
    suppress (FileLoadingException) {
        $content = file_get_contents('foobar.json') ?: 'false';
        $foobar  = json_decode($content);
    }
    <co>// not super different from an empty catch,
    // but it has a certain merit</co></code>

the second major assumption that Safe makes (and not just
Safe, the entire php community at large) seems to make
is that Exceptions are a better way to handle errors.
i'll use Go as an example. it purposely does _not_
use exceptions, rather it favors explicitly handling
errors. this is not so different from C, except that Go
enforces this style. it does allow you to ignore errors,
and this is an excepted pattern in certain situations,
but this again is also explicit. (i'm talking about
`result, _ = ...` vs `result, err = ...` here). Go does
still offer something akin to exceptions, but with a much
more specific use, the defer/panic/recover set, somewhat
like try/throw/catch. however the naming reveals it's
limited intended use. in performant code, throwing and
catching exceptions, at least in current implementations,
isn't a great idea since it unwinds the stack.

there are ways still to get around this in php
land. obviously this pattern is quite clunky, and easy
to forget to use (or even to forget the names of the
functions involved)

    <code>$foobar = json_decode($content);
    if ($foobar === null) {
        $error = json_last_error_msg();
        <co>// handle errors here</co>
    }</code>

although, to be fair this is really just as clunky as
try/catch, and although with try catch you don't need to
remember the name of a function like json_last_error_msg(),
you do still need to remember the name of the various
exception classes. if you use an IDE that helps with both
equally. this doesn't unwind the stack though. but it's
only helpful in situations perf is needed

another option is this pattern. i've used this with
success as well, although it trips some folks up if they
aren't used to it. it's also a common pattern amongst C
developers. really, it's a way to trick multiple return
values, like Go has

    <code>function loadMyFile(string $path, &$error = null): ?string
    {
        if (is_readable($path)) {
                return file_get_contents($path) ?: '';
        }
        $error = "Could not load file $path";
    }

    $result = loadMyFile('foobar.json', $err);
    if ($err) {
        print "ERROR: $err";
    }</code>

this might be arbitrary but it illustrates the idea
of using a nullable return value, and then passing the
actual error by reference in the arguments. not everyone
is comfortable in working this way, but it is one way of
approximating Go-similar error handling in php.

another way i've used is to simulate multiple return
arguments using arrays. this i think is the point of the
new short form for list().

    <code>function loadMyFile(string $path): array
    {
        if (is_readable($path)) {
            return [file_get_contents($path) ?: '', null];
        }
        return [null, new FileLoadingException("Could not load file $path")];
    }

    [$result, $err] = loadMyFile('foobar.json');
    if ($err) {
        print $err; // could also `throw $err` if you like
    }</code>

note you can actually create and pass around exceptions
in php without throwing them. this can be useful in
a case like this, or if you have an error handler that
logs externally or sends alerts which takes an exception
as an argument, because it's normally used in a `catch`,
but you don't want to throw an exception in that case (i
use this when i need the alert sent and logged silently
without interrupting flow or alerting users). you can also
create an exception in one function, and defer throwing
it to another place entirely.

you also could just return the exception and check type

    <code>function loadMyFile(string $path)
    {
        if (is_readable($path)) {
                return file_get_contents($path);
        }
        return new FileLoadingException("Could not load file $path");
    }
    $result = loadMyFile('foobar.json');
    if ($result instanceof Exception) {
        <co>/* ... */</co>
    }</code>

i'll now interrupt my book, _the php and exception haters
guide_ to provide you with an interlude exploring the
merits of exceptions.

1. it allows you to handle _classes_ of errors, rather
than *all* or just one specific type. imo this is the
biggest upside

2. convenience. this takes two parts:

    a) it means you can approach code without thinking
    about errors, while stopping if there is one,
    akin to shell-scripting with `set -e`

    b) it allows you to jump between contexts, and
    handle an error from one context somewhere else
    (put a pin in this)

3. development/debugging. specifically, if i'm learning a
new system, i will find a string in an input name or error
message, grep for it, and then make an exception there
and inspect the trace. this way i can explore a new code
base from where i need to work very very quickly. there
are also some other creative ways to use exceptions for
development too.

the convenience level is important here, because that's
really one of php's core identity traits, as far as i can
see, although php slowly moved from that toward java land
for a time, and now it's moving toward functional land
a bit. idk what this means completely, but i think as a
language php should be embracing it's scriptiness while
making concessions for reliability like stronger typing
and more consistent interfaces. these goals aren't at odds
with each other, php just gets blown in the wind a lot,
always seeming like it's in a pseudo identity crisis.

interestingly, exceptions are a strikingly OO way of
handling errors. by this i mean a few things. first of all,
they have a similar pattern of holding and mutating shared
state, and hiding imperative code in this state. this
isn't necessarily good or bad, but if you are doing it
you should be aware and also aware that there are other
ways of doing this, immutable objects, explicitly passing
state through arguments, pure functions, among others.

but also, exceptions, in the context of class-based
OO, were invented to solve a problem unique to that
sphere. class-based OO in the modern vein comes from C++,
the strongest invention here is the idea of constructors
and destructors. why? because you can define your own
types. not just a struct or renaming existing primitive
types, but you can _define your own primitive types_. the
rub here is that, if you allocate a type, you need to free
it later or you'll get memory leaks. the other known option
is gc. in the case of C++, high perf code is important
and it's hard to implement high perf gc.

for C code, this might look like

    <code>str = (char *) malloc(15);  <co>/* do stuff */</co>  free(str);</code>

equivalent roughly speaking to this php

    <code>$f = fopen('x', 'rw'); <co>/* do stuff */</co> fclose($f);</code>

but what happens if the *do stuff* part fails before the
memory is freed or the file is closed? well, normally,
this would create a memory leak, but in php perf is
basically not a concern, so they use gc to track all open
resources or objects and close them later. this is not
a good option for high perf low-level code. so instead,
try/catch/finally was invented in C++, this way you can
define any type you want but still have the perf benefits
of non garbage collected code, by having the C++ runtime
always perform the "finally" or other cleanup in the
"catch" even if there's an issue.

interestingly, php's objects are really loose C structs
with methods attached, and they use gc, so it's somewhat
odd that they thought it was necessary to add exceptions
at all. although they did add them with the class system,
so i assume they thought you can't have classes without
exceptions

back to hating on exceptions i guess. remember jumping
between contexts?  that's part of the primary point of
exceptions. here is an example so you can see what i mean:

    <code>function exceptionExample() {
        $content = file_get_contents('foobar.json');
        if ($content === false) {
            $error = 'Could not load file foobar.json';
            goto error_cleanup;
        }
        $foobar = json_decode($content);
        if ($foobar === null) {
            $error = 'foobar.json does not contain valid JSON: ' . json_last_error();
            goto error_cleanup;
        }
        return $foobar;

        error_cleanup:
        error_log($error);
        return [];
    }</code>

darn it! i just made an example of goto, not exceptions. so
hard to keep those two straight these days. i put them
both in the box "jump from current context to another so i
can handle code or logic outside the parent context cause
i'm too lazy to handle it there". is this comparison a
stretch? not really, in fact, in the case of php, gotos
are actually constrained so that they are *safer* than
exceptions, because they don't allow you to jump scope,
however exceptions let you jump as many layers of scope
as you want. this is very powerful, and it also can be
dangerous. if you've had to debug a deeply nested exception
stack, you know what i mean. especially when there are
many catch/rethrow cases.

since i have to switch from the light side to the dark
side of the force every once in a while to get work done
quickly (fellow pragmatists know what i mean), i have
harnessed this darker aspect. even though this is not
the intended use, i can use exceptions for control flow
_instead of error handling_, the same exact way i would
cheat the system, instead of reflowing an entire program,
by using a goto in C. does it break the normal control
flow? sure. can it trip other programmers up because
it's not really expected? sure. is it way slower since
it unwinds the whole stack?  sure. never stopped me, an
accomplished sith lord. when the jedi finally ended my
Empire called Goto, I simply created a new First Order i
like to call Exceptions.

i can't have spaghetti? fine. i replaced it with
vermicellini, fooled you all!

in fact, the most achieved exception system must
have been <a href=https://www.tutorialspoint.com/lisp/lisp_error_handling.htm>lisps</a>. not only can you define classes of
errors (conditions), throw them (error), catch them
(handler-bind), you can jump back into the "try" part at
specified points (invoke-restart), and you can also bind
several handlers to try in order.

alas, lisps parens, the fact that a function or variable
name can be any collection of characters and symbols,
and the differing naming convention seems to trip
up coders used to C-style languages, which makes it
difficult to illustrate the _full_ _glorious_ _power_ of
unhindered ability to jump within the structured confines
of exceptions.

let's talk about php. one of the upsides of shell scripting
is the ability to chain (pipe) commands or methods together
to quickly hobble something together. although php is
often lauded as a way to quickly prototype, it actually
doesn't have this feature, although some libraries do
design method-chaining in. if the php team had decided
they wanted to wrangle the old library into a consistent
interface, making the error handling consistent, fixing
that old complaint about never knowing what order to put
the $needle or $haystack in and whatnot, they actually
could do it, while preserving the old library.

if they made a new unified interface, that allowed
primitives, resources, and objects to share a method-like
call syntax, then you might be able to (like python, ruby,
or javascript)

    <code>"a,b,c"<sy>-></sy>explode<sy>(</sy>","<sy>)-></sy>filter<sy>(</sy>fn<sy>(</sy>$x<sy>) =&gt;</sy> $x <sy>!=</sy> "b"<sy>)-></sy>implode<sy>(</sy>' '<sy>);</sy>
    new File<sy>(</sy>"foobar"<sy>)-&gt;</sy>getContents<sy>()-&gt;</sy>jsonDecode<sy>();</sy></code>

you can assume it would work with the current php method
of error handling, although it wouldn't be complex to make
a non-exception option, it's just that exceptions are too
ingrained into php at this point.

however, if you had a sort of "safe method operator" (&amp;&gt;)
that would collapse if the argument types didn't match,
and/or a "null method operator" (?&gt;) that would collapse
if the return value was null, that's an interesting option
that would handle several styles of coding at once

    <code><co>// stop if we get a null</co>
    new File<sy>(</sy>"foobar"<sy>)?&gt;</sy>getContents<sy>()?&gt;</sy>jsonDecode<sy>();</sy>

    <co>// stop if the argument type doesn't match</co>
    new File<sy>(</sy>"foobar"<sy>)&amp;&gt;</sy>getContents<sy>()&amp;&gt;</sy>jsonDecode<sy>();</sy>

i don't think these changes would be super likely in php,
but otoh, there are a lot of things i though were very
unlikely in php that happened in the last four versions,
given that someone was able to develop and propose them.

but i digress, many times over. i hope you enjoyed my book.
perhaps i should start a blog......meh


<author>Brian Zick</author> <time>2019-12-20T23:57:10-06:00</time> <?php require('../../../engine/comment_link.php'); ?>
