<meta name=viewport content="width=device-width,initial-scale=1.0"> 
<link rel=stylesheet href=/cee_ess_ess>

<title>Threaded Programming Language Abstractions</title> <tag>programming</tag>

<pre>
<co>/* reproduced from
   The Unix Historical Society mailing list */</co>

From: "Steve Johnson" scj@yaccman.com
Subject: [TUHS] Future Languages (was Pascal not Favorite...)

"Toby Thain: We will never reach a point where
programming language evolution stops, imho."

I may just be a grumpy old fart, but I think
programming languages today are holding us
back. Nearly all of them...

I'm currently working for a hardware company (Wave
Computing). We are building a chip with 16K 8-bit
processors on it, and have plans to build systems
with up to 1/4 million processors from these chips.

Nevertheless, most programs today are still written
pretty much like they were 25 years ago. And they
are, for the most part, based on threads where the
programming task is to set out a number of steps:
do this, do that, do something else, test this and if
true do this, ... A single serial thread. Things like
multicore CPUs are a desperate attempt to preserve
this model while the hardware world has blown past us.

Recall that parallelism is the natural state of
hardware. It takes effort to make things work
sequentially. In the old days, when hardware and
software used pretty much the same model, many
if not most of the hardware innovations came from
first being done in software, and then moved into
hardware -- index registers, floating point, caches,
etc. etc. That process has effectively stopped. The
single thread model simply no longer fits the sweet
spot of today's hardware technology.

Just to underscore how far hardware has advanced:
If cars had become as much cheaper and faster as
computers from 1970 to today, we could buy 1000 Tesla
Model S's for a penny and they would go 0-60,000
mph! A petabyte of data, if punched onto punch cards,
would make a card deck whose height would be 6 times
the distance to the moon. If the recent estimate of
the number of bytes of data produced by the human
race every day (2.5 quintillion bytes) is correct,
when punched up that card deck would be 9 times the
distance to the sun.

I'm not saying that there isn't a place for languages
like GO and Python. Most people will continue to
think serially and design things that way. But when
it comes to implementing these designs, the current
"systems" languages are left at the starting gate. In
the same way that we invented abstraction methods
like functions and processes for the old computers, we
need to invent newer abstraction methods that go far
beyond co-routines and threads and message passing. If
we get bogged down in telling tens of thousands of
processors "do this, do that" we will perish long
before our program works. Of particular relevance is
the role that abstractions play in debugging --they
partition the job into pieces with known interfaces
and behavior that can be tested in isolation before
being assembled into complete systems.

Yes, I have some ideas (and not much time to work
on them...) but, even if I had a perfect solution
available today, I suspect it would take decades
before it caught on. In order to accept a solution,
you first have to believe there is a problem...

Steve

~~~~
<co>my own note: it's better if we can take advantage of
both massive multi-threading and gpu at the programming
language level, with intuitive abstractions or
constructs, rather than at an OS or library level</co>

see also:

~ <a href=https://code.facebook.com/posts/745068642270222/fighting-spam-with-haskell/>facebook's use of haskell</a>
~ <a href=https://en.wikipedia.org/wiki/List_of_programming_languages_for_artificial_intelligence>list of programming languages for artificial intelligence</a>
~ <a href=https://en.wikipedia.org/wiki/Cassowary_(software)>cassowary constraints</a>
~ <a href=http://joeduffyblog.com/2015/11/19/asynchronous-everything/>midori's "asynchronous everything"</a>

<author>Brian Zick</author> <time>2017-09-01T13:50:43-05:00</time> <?php require '../../../engine/comment_link.php'; ?>

