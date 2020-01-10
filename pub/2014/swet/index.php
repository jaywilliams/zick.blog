<meta name=viewport content="width=device-width,initial-scale=1.0"> 
<link rel=stylesheet href=/cee_ess_ess>

<title>Ideas for a web language</title> <tag>programming</tag> <tag>ideas</tag>

<pre>
#<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<#

~~~~~~~~ A try at something perhaps a little too minimal ~~~~~~~~
~~~~~~~~~~~~~~~~~~~~~ or not minimal enough ~~~~~~~~~~~~~~~~~~~~~

Swet tries to accomplish several things.

*  Be as succinct as possible, sometimes making code denser,
   but preferably actually making the code less dense.

*  Swet believes in overloading, using the same construct
   to accomplish multiple, but mostly similar tasks.

*  The last point leads us to context, which is also very
   important to the way Swet works. The same set of charactars
   in a different context makes a different operation happen.

*  This also allows little chasms that appear to be DSLs,
   or perhaps something entirely different.

*  In Swet, it could be said that there should be one way,
   and preferably only one way, to do something. But this is
   not entirely true. There is one "regular" way to do
   a task, which can often have both tall and wide variations, 
   and several "irregular" ways, which tend to act as a 
   shorthand that does the same or a very similar task.

#<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<#

Let's get started with the syntax.

*  Much effort is taken to remove the amount of keywords
   that the language gobbles up from the namespace to a minimum.

   At present these are:

   in as do end use loop stop

   Along with several primitive types, whose names, for
   some reason, tend to contain just 3 letters

   #str #obj #num #raw #tag

   And several methods that sort of look like keywords:

   *.new *.exec *.log *.for *.true *.false

*  Some of Swet's literal primitives look like this:

   () list
   {} block
   "" string
   <> soft tag
   [] hard tag

   And to start comments:

   -- line
   <! block
   #< doc

#>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>#


#< Declaration >#

-- To declare words or variables, use only :, unlike other
-- languages which use =, but also often also utilize : within
-- hashes or associative arrays.

food: "chocolate bar"
chocolate-bar: "induces energy"
log food --> prints "chocolate bar" to the log

-- So there you have it. Now lets talk about functions, also
-- known as subroutines, methods, gates, and lambdas, 
-- probably among other things. These are generally defined
-- with the following syntax, which you should be familiar with:

hello-world() {
  log "Hi there, world!"
}

hello-world() -- "Hi there, world!"

-- Note several things: the () is optional, so it could have been
-- written as `hello-world { ... }` just as well.


#< Objects, classes, namespaces, etc >#

-- Now let's talk about objects, classes, namespaces, modules, and
-- so on. In Swet these are all the same thing. In fact, in Swet,
-- methods are also simple objects. Declaring one is very, very simple:

World {
  say-hi(){ log "Hi there, world!" }
}
World.say-hi --> "Hi there, world!"

-- Objects can also take parameters, in which case they act a little
-- like a "constructor" function:

Planet(planet-name) {
  name: planet-name

  say-hi {
    log "Hi {{@name}}"
  }
}

my-planet: new Planet("Mars")
my-planet.say-hi() --> "Hi Mars"

-- Note that the constructor MUST run regardless of whether
-- you use it as a "static" or "dynamic"/"instance" class:

Demo {
  log "Hello "
  hi(name) { log name }
}

d = new Demo --> outputs "Hello "
d.hi("George") --> outputs "George"
-- or --
Demo.hi("Bob") --> "Hello Bob"
Demo.hi("Bob") --> "Bob" (the contructor is only run the first time, unless you `Demo()`)

-- In Swet, because of the "implied" keywords, the same construct can
-- create a function, method, class, namespace, module, and so on. Technically
-- these are all objects, or members of the #obj type, as are lists.

MyNameSpace {
  MyModule {
    MyClass {
      my-method() {
      }
    }
  }
}


#< Logic >#

-- As with functions and classes above, Swet also dispenses with
-- extra keywords with logic. There are several ways to control logic
-- flow. The most basic is the equivalent of the if/else statement.

foo = "bar" {
  log "foo equals bar"
}

-- This is an expression followed by a literal block. The "or" is
-- optional. Probably the favored way to do simple operations
-- is with the ->, which is maybe pronounced "and do", and
-- means about the same thing as && in shell scripting.

people: (1,2,3)
people.count > 3 -> log "there are more than two people"

-- And the opposite is also possible, a bit like "if" in Perl

hungry: 1
eat <- hungry

-- I know, it's missing the else. That's on purpose. Before
-- going any further with control flow, allow me to introduce you
-- to loops. We have two loops in Swet: `loop` and `for`

loop { log "howdy" } -- prints "howdy" to the log forever

people : ("Steve", "Robert", "Johnny")

for people {
  log .
}

-- The for loop is the interesting bit. It loops over every
-- iteratible property in an item. The "." might look a little
-- funny, this is the "subject". It's a bit like "this" or "self"
-- in other languages but not quite. Most methods are by default
-- operated on a "subject". In the case of Swet the for loop
-- and control flow are tied together, as this acts as both
-- a switch and an if/else.

-- Instead of writing `hungry: true; if hungry { log "eat" } else { log "code" }`
-- we can do

hunger: true

for hunger {
  true -> log "eat"
  !    -> log "code"
}

-- for to write about:
-- comparators
-- the two stacks
-- ways to access object properties and methods
-- lists
-- tall and wide
-- finer points about the difference between ,; and newline


#<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<#

See also: <a href=https://gist.github.com/anoxic/19aaab24205fc422df25>this gist</a>

#<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<#


<author>Brian Zick</author> <time>2014-03-24T15:34:43-08:00</time> <?php require '../../../engine/comment_link.php'; ?>
