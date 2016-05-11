# Calends #

[![Software License](https://img.shields.io/packagist/l/danhunsaker/calends.svg?style=flat-square)](LICENSE)
[![Gitter](https://img.shields.io/gitter/room/danhunsaker/calends.svg?style=flat-square)](https://gitter.im/danhunsaker/calends)

[![Latest Version](https://img.shields.io/github/release/danhunsaker/calends.svg?style=flat-square)](https://github.com/danhunsaker/calends/releases)
[![Build Status](https://img.shields.io/travis/danhunsaker/calends.svg?style=flat-square)](https://travis-ci.org/danhunsaker/calends)
[![Codecov](https://img.shields.io/codecov/c/github/danhunsaker/calends.svg?style=flat-square)](https://codecov.io/gh/danhunsaker/calends)
[![Total Downloads](https://img.shields.io/packagist/dt/danhunsaker/calends.svg?style=flat-square)](https://packagist.org/packages/danhunsaker/calends)

Arbitrary calendar systems in PHP.

## Installation ##

Use Composer:

```bash
composer require danhunsaker/calends
```

## Setup ##

### Laravel ###

A Laravel service provider is included.  Add it to your app's service providers
list, publish the migrations for Eloquent calendars, run the migrations, and
enjoy!

In `config/app.php`:

```php
    'providers' => [
        // ...
        Danhunsaker\Calends\Laravel\ServiceProvider::class,
        // ...
    ],
```

Then on the command line:

```bash
php artisan vendor:publish --tag=migrations --provider=Danhunsaker\\Calends\\Laravel\\ServiceProvider
php artisan migrate
```

Note that this is entirely optional - you can easily skip the service provider
and migrations, and use Calends without the Eloquent calendar support.  This
will limit you to calendars defined as PHP classes, but if you don't need
support for dynamically- and/or user-defined calendars, that shouldn't be a
concern.

You can also register a class alias like you would for a Facade:

```php
    'aliases' => [
        // ...
        'Calends' => Danhunsaker\Calends\Calends::class,
        // ...
    ],    
```

Of course, this isn't actually a Facade.  It simply sets up an alias (using
PHP's `class_alias()` function, though for the sake of being lightweight, only
when the alias is first accessed) so you can skip the namespace when accessing
the class elsewhere in your project.  The only real advantage, here, is avoiding
writing and maintaining `use` statements all over your app.

### Other Projects ###

Once you have installed Calends, using it is generally as easy as creating a new
`Calends` object and manipulating it from there.  If you want to use custom
calendar systems or class converters, be sure to register them first (details
below), but otherwise, everything should work just fine out of the box.

The main situation where you'd want to do more setup is with Eloquent calendars.
You'll need to ensure you have `illuminate/database` installed in your project,
that it is properly configured to connect to the right database, and that the
database is properly initialized with the tables Calends needs to store calendar
definitions.

Check `extra/calends-init-db.php` for the appropriate environment variables to
set for DB access, and ensure they are properly set up.  There are many ways to
do this, depending on how your project will be run, so consult your web server
or shell documentation for more details on how to proceed.  You'll also need to
set these in your command line shell, so we'll show one way to do that below
(but still check `extra/calends-init-db.php`, because we won't be setting all of
them).  Once your environment is set up, `include()` or `require()` that file
somewhere in your project's initialization code:

```php
require_once('vendor/danhunsaker/calends/extra/calends-init-db.php');
```

Finally, on the command line:

```bash
export DB_DRIVER='mysql'
export DB_HOST='localhost'
export DB_USER='username'
export DB_PASS='password'
export DB_NAME='database'
php vendor/danhunsaker/calends/extra/non-laravel-migrate.php
```

That should be it - your database is now set up, and your project is configured
to properly connect to it so that Eloquent calendars will work correctly without
further effort on your part.  See below for how to actually create Eloquent
calendars for use in your projects.

## To Do ##

Calends is functional and supports a handful of calendar systems out of the box,
but it isn't quite production ready.  Following is a list of known issues, and
plans for future versions.

- [ ] More robust support for existing calendars
  - [ ] The Hebrew and Julian calendars:
    - Both rely on PHP's built-in (Gregorian) DateTime class to parse dates,
    then convert the resulting values into timestamps for internal use.  They
    should, instead, each have their own parsers, capable of handling the
    nuances of each calendar.
  - [ ] Honor the `$format` parameter:
    - Currently, only the Gregorian and Elquent calendars honor the `$format`
    parameter to methods that provide it.  For some, multiple formats don't make
    sense, such as TAI64, Unix, and Julian Day Count, but the rest should
    properly support this (admittedly optional) argument.
- [ ] Eloquent calendar intercalation support (see below for more on this)
- [ ] More calendar systems (via external libraries)
  - [ ] Chinese (several variants)
  - [ ] Discordian
  - [ ] Meso-American (commonly called Mayan)
  - [ ] Persian
  - [ ] Stardate (Yes, the ones from Star Trek&trade;; several variants exist)

## Usage ##

### Dates ###

Create a `Calends` object with the date and calendar system as arguments to the
constructor:

```php
use Danhunsaker\Calends\Calends;

// The default date is the value of microtime(true), and the default
// calendar is 'unix' - the following are equivalent:
$now = new Calends();
$now = new Calends(null, 'unix');
$now = new Calends(microtime(true));
$now = new Calends(microtime(true), 'unix');

// You can also use Calends::create() instead of new Calends(), which
// can be pretty useful when using method chaining:
$now = Calends::create();
$now = Calends::create(null, 'unix');
$now = Calends::create(microtime(true));
$now = Calends::create(microtime(true), 'unix');

// UNIX Epoch - the following are equivalent:
$epoch = Calends::create(0);
$epoch = Calends::create(0, 'unix');
$epoch = Calends::create(2440587.5, 'jdc');
$epoch = Calends::create('1970-01-01 00:00:00 UTC', 'gregorian');
$epoch = Calends::create('1970-01-01', 'gregorian', 'Y-m-d');
```

### Conversion ###

Now you can convert that date to any other supported calendar system:

```php
use Danhunsaker\Calends\Calends;

$now = Calends::create();

// Using getDate():
$unix = $now->getDate('unix');     // 1451165670.329400000000000000
// Or just use as a function - __invoke() calls getDate() (but see below...)
$unix = $now('unix');              // 1451165670.329400000000000000
// The default 'calendar' for getDate() is also 'unix'
$unix = $now();                    // 1451165670.329400000000000000

$julianDayCount = $now('jdc');     // 2457383.398962145833333333
$gregorian = $now('gregorian');    // Sat, 26 Dec 2015 14:34:30.3294 -07:00
$gregorian = $now('gregorian', 'Y-m-d_H-i-s-u'); // 2015-12-26_14-34-30-3294
$julianCalendar = $now('julian');  // 12/13/2015 14:34:30 GMT-07:00
```

> Note that while you can pass a format along to `Calends::create()` or
> `Calends::getDate()` (and their respective variants), not all calendars will
> pay any attention to them, and the formatting codes supported by each are
> entirely defined by the calendar itself.

You may also be interested in converting a `Calends` object into a different
kind of date/time object.  Gotcha covered there, too:

```php
use Danhunsaker\Calends\Calends;

$now = Calends::create();

$dt = $now->convert('DateTime');
```

And lest you think we forgot to let you convert the other way, from other
date/time objects into `Calends` objects, fear not:

```php
use Danhunsaker\Calends\Calends;

$now = Calends::import(new DateTime());
```

Supported conversions include `DateTime` (plus a few derivatives, like
`Carbon\Carbon`, `Jenssegers\Date\Date`, and `Moment\Moment`), `IntlCalendar`
(which of course means `IntlGregorianCalendar`, too), and
`League\Period\Period`.  Of course, since `IntlCalendar` requires at least PHP
5.5, you won't be able to convert to/from it or `IntlGregorianCalendar` in 5.4.
But see below for how to add support for other date/time classes.

### Storage ###

You can technically store Calends date values in any of the supported output
formats, however this is not recommended for various reasons, performance among
them.  Instead, save and restore `Calends` objects using the built-in `tai`
'calendar' (alternately, save by casting the object to `string`):

```php
use Danhunsaker\Calends\Calends;

$now = Calends::create();

$tai = $now->getDate('tai');       // 40000000567f07e613a23ec000000000
$tai = $now('tai');                // 40000000567f07e613a23ec000000000
$tai = (string) $now;              // 40000000567f07e613a23ec000000000

// Save the value of $tai in your database, or wherever makes sense for your app
```

Then, any time you need to recreate the saved `Calends` object:

```php
use Danhunsaker\Calends\Calends;

// Retrieve the previously-stored value of $tai...

$date = Calends::create($tai, 'tai');
```

The external [TAI64NA][] format is used internally (or rather, an unserialized
version is used internally) for all date/time values, so using it is
considerably faster than converting between any of the other supported
calendars. **Note, however, that only the TAI64NA *format* is used - the seconds
represented are still UTC seconds, not TAI seconds**, as PHP currently lacks a
reliable mechanism for calculating the associated offset between the two.

For convenience, `Calends` implements the `Serializable` and `JsonSerializable`
interfaces, which means you can `serialize()`, `unserialize()`, and
`json_encode()` a `Calends` object safely, too - it will automatically convert
itself to (and from, in the case of `unserialize()`) the `tai` date.  (More on
this below, though...)

### Compare ###

Often it is useful to compare two dates to see which came first.  One good
example of this is sorting.  Calends is designed with this in mind, supporting
four different methods for doing date comparisons.  Since sorting is so common,
we'll start with the method designed for that:

```php
use Danhunsaker\Calends\Calends;

$times = [];
for ($i = 0; $i < 10; $i++)
{
    $times[] =  Calends::create(mt_rand(0 - mt_getrandmax(), mt_getrandmax()));
}

print_r($times);
$sorted = usort($times, [Calends::class, 'compare']);
print_r($sorted);
```

`Calends::compare()` accepts two `Calends` objects to compare, and returns -1 if
the first is before the second, 0 if they are equal, and +1 if the first is
after the second.  This is compatible with PHP's sorting functions and their
expectations for the behavior of sorting callbacks.

The next three methods provide more focused comparisons, returning `true` or
`false` instead of lesser/equal/greater:

```php
use Danhunsaker\Calends\Calends;

$epoch = Calends::create(0);
$now   = Calends::create();

print_r([
    $epoch::isBefore($now),    // true
    $epoch::isSame($now),      // false
    $epoch::isAfter($now),     // false
]);
```

Each of these methods accepts the `Calends` object to compare the current one
to, and returns a boolean value, as mentioned above.

There's another method you can use to compare `Calends` objects, which returns
the amount, in seconds, by which they are different, rather than just which
direction they differ in:

```php
use Danhunsaker\Calends\Calends;

$epoch = Calends::create(0);
$now   = Calends::create();

echo $now->difference($epoch); // Seconds between $epoch and $now
```

### Modify ###

`Calends` objects are immutable - that is, you cannot modify them directly.
Instead, any action which would change the object's values actually creates and
returns a whole new `Calends` object.  This has some advantages, such as
preserving the original object, but can be a bit unexpected if you aren't aware
of it.  The examples below take this into account.

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$tomorrow  = $now->add('1 day', 'gregorian');
$yesterday = $now->subtract('1 day', 'gregorian');
$last24hrs = $now->setDate($yesterday->getDate());
```

### Ranges ###

That last example actually introduces a feature of `Calends` objects we haven't
previously touched on.  Every date value can be thought of as an instant in
time - a range of time both starting and ending at the same time value.  The
duration of such a range would be zero.  Calends takes advantage of this fact to
treat every `Calends` object as a range as easily as a single point in time.
That means times and ranges can coexist in a single class, allowing complex
operations to be simplified considerably.

It also means that not only are there several other methods you can use to
perform range-related operations, but many of the methods you've already learned
have additional ways they can be used when you want to work with ranges instead
of simple dates.

Let's start with the last example, giving some more detail about what it's
doing, and use it to introduce some other methods:

```php
use Danhunsaker\Calends\Calends;

$now           = Calends::create();

$tomorrow      = $now->add('1 day', 'gregorian');              // tomorrow to today
                                                               // (duration: -1 day)
$yesterday     = $now->subtract('1 day', 'gregorian');         // yesterday to today
                                                               // (duration: 1 day)
$endsTomorrow  = $now->addFromEnd('1 day', 'gregorian');       // today to tomorrow
                                                               // (duration: 1 day)
$endsYesterday = $now->subtractFromEnd('1 day', 'gregorian');  // today to yesterday
                                                               // (duration: -1 day)
```

`setDate()` and `setEndDate()` also accept a calendar, which defaults to `unix`.
Whichever endpoint isn't being set is copied over from the calling instance:

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$tomorrow  = $now->add('1 day', 'gregorian');
$yesterday = $now->subtract('1 day', 'gregorian');

$last24hrs = $now->setDate($yesterday->getDate('gregorian'), 'gregorian');
             // yesterday to today; same as $yesterday
$next24hrs = $now->setEndDate($tomorrow->getDate('gregorian'), 'gregorian');
             // today to tomorrow
$next72hrs = $now->setDuration('72 hours', 'gregorian');
             // today to three days from now
$last72hrs = $now->setDurationFromEnd('72 hours', 'gregorian');
             // three days ago to today
```

You can also create a full range in one step:

```php
use Danhunsaker\Calends\Calends;

$next7days = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');
```

Of course, you'll want to be able to retrieve end dates and durations as well as
start dates:

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$next72hrs = $now->setDuration('72 hours', 'gregorian');

$endArray  = $next72hrs->getInternalEndTime();     // Like getInternalTime()
$dateIn72  = $next72hrs->getEndDate('gregorian');  // Like getDate()
$secsIn72  = $next72hrs->getDuration();            // In seconds
```

Alternately, calling the `Calends` object as a function, when the duration is
not 0, will return both the start *and* end points of the object (we mentioned
we'd have more on this usage "below" - here it is!):

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$next72hrs = $now->setDuration('72 hours', 'gregorian');

$endpoints = $next72hrs('gregorian');          // ['start' => ..., 'end' => ...]
```

While the new `Calends` object from `setDate()` inherits the end date of the
object that created it, and the new one from `setEndDate()` inherits the
creator's start date (meaning these new objects overlap), sometimes you want to
create new objects that instead abut the creating object.  Here's how:

```php
use Danhunsaker\Calends\Calends;

$next7days      = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days      = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');

$followingWeek  = $next7days->next();
$precedingWeek  = $last7days->previous();
$followingMonth = $next7days->next('1 month', 'gregorian');
$precedingMonth = $last7days->previous('1 month', 'gregorian');
```

If you want to work with composite ranges, we've got you covered:

```php
use Danhunsaker\Calends\Calends;

$next7days      = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days      = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');
$precedingWeek  = $last7days->previous();
$followingMonth = $next7days->next('1 month', 'gregorian');
$precedingMonth = $last7days->previous('1 month', 'gregorian');

$bothMonths     = $precedingMonth->merge($followingMonth);      // 2.5 months
$commonTime     = $precedingMonth->intersect($precedingWeek);   // 1 week
$betweenMonths  = $precedingMonth->gap($followingMonth);        // 2 weeks
```

But keep in mind that an `InvalidCompositeRangeException` is thrown if you call
`intersect()` without an overlap, or `gap()` when an overlap exists:

```php
use Danhunsaker\Calends\Calends;

$next7days      = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days      = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');
$precedingWeek  = $last7days->previous();
$followingMonth = $next7days->next('1 month', 'gregorian');
$precedingMonth = $last7days->previous('1 month', 'gregorian');

$invalidRange   = $precedingMonth->intersect($followingMonth);  // Exception
$invalidRange   = $precedingMonth->gap($precedingWeek);         // Exception
```

And what would a date range library be without range comparisons?

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$last24hrs = $now->subtract('1 day', 'gregorian');

print_r([
    $now->startsBefore($last24hrs),   // false
    $now->isBefore($last24hrs),       // false
    $now->endsBefore($last24hrs),     // false
    $now->isSame($last24hrs),         // false
    $now->startsDuring($last24hrs),   // true
    $now->isDuring($last24hrs),       // true
    $now->endsDuring($last24hrs),     // true
    $now->contains($last24hrs),       // false
    $now->overlaps($last24hrs),       // true
    $now->abuts($last24hrs),          // false
    $now->startsAfter($last24hrs),    // true
    $now->isAfter($last24hrs),        // false
    $now->endsAfter($last24hrs),      // false
    $now->isLonger($last24hrs),       // false
    $now->isShorter($last24hrs),      // true
    $now->isSameDuration($last24hrs), // false
]);
```

For all of that to work, we need a more flexible `compare()` method:

```php
use Danhunsaker\Calends\Calends;

$times          = [];
for ($i = 0; $i < 10; $i++)
{
    $times[]    = Calends::create([
        'start' => mt_rand(0 - mt_getrandmax(), mt_getrandmax()),
        'end'   => mt_rand(0 - mt_getrandmax(), mt_getrandmax())
    ]);
}
print_r($times);

$sorted         = usort($times, function($a, $b) {
    return Calends::compare($a, $b, 'start');
});
print_r($sorted);             // Sorted by start date, which is the default

$endSorted      = usort($times, function($a, $b) {
    return Calends::compare($a, $b, 'end');
});
print_r($endSorted);          // Sorted by end date

$endStartSorted = usort($times, function($a, $b) {
    return Calends::compare($a, $b, 'end-start');
});
print_r($endStartSorted);     // Ranges that start before others end are earlier
                              // in this sort

$startEndSorted = usort($times, function($a, $b) {
    return Calends::compare($a, $b, 'start-end');
});
print_r($startEndSorted);     // Ranges that end before others start are earlier
                              // in this sort

$durationSorted = usort($times, function($a, $b) {
    return Calends::compare($a, $b, 'duration');
});
print_r($durationSorted);     // Sorted by duration
```

Which of course means we'd want the same flexibility for our `difference()`
method:

```php
use Danhunsaker\Calends\Calends;

$now       = Calends::create();

$next7days = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');

echo $now->difference($next7days, 'start');           // 0
echo $now->difference($next7days, 'end');             // 604800
echo $last7days->difference($next7days, 'start-end'); // 1209600
echo $last7days->difference($next7days, 'end-start'); // 0
echo $last7days->difference($next7days, 'duration');  // 0
```

Now, up in the Storage section, we said there'd be more on serializing `Calends`
objects below.  Well, this is 'below'.  Because `Calends` objects are actually
ranges, the value that gets serialized may not always be just a simple TAI64NA
string.  If the end date doesn't match the start date, an array will be
serialized instead, with both a `'start'` and an `'end'` key.  This allows even
`Calends` ranges to be recovered correctly without difficulty.  Generally
speaking, though, you shouldn't have to worry about this behavior much.

### New Calendars ###

#### Class Definitions ####

There are two ways to provide new calendar definitions.  The first, and most
flexible, is with a class implementing
[`Danhunsaker\Calends\Calendar\DefinitionInterface`](src/Calendar/DefinitionInterface.php).
This is, in fact, the way the calendars which ship with Calends are built.  Once
your calendar definition class is available in your project, you need to
register it with `Calends::registerCalendar()`:

```php
use Danhunsaker\Calends\Calends;

Calends::registerCalendar('myCustomCalendar', MyCustomCalendar::class);
```

This will make your calendar system available to all `Calends` objects
throughout your project.

> Note that while Calends will automatically find and register class definitions
> in the `Danhunsaker\Calends\Calendar` namespace, it is considered bad form to
> create your classes there unless they're officially recognized by the main
> project, since a namespace implies official support and/or endorsement.

#### Database Definitions ####

The other way is by storing your definition in a database.  To use this
approach, you need to include `illuminate/database` in your project.  (This
library is part of the Laravel framework, so you may already have it available.)
It takes a bit more work to use this approach, but it can be extremely useful in
cases where you wish to allow your users to define their own calendar systems in
your project, without expecting them to write any code.

> A quick reminder to Laravel users to follow the setup instructions at the top
> of this README to get the proper migrations installed, etc, before using
> database definitions in your apps!

There are two basic approaches to defining calendars in your database.  The
first is to directly set the appropriate values in the database, be it via DB
seed file, manual entry using tools like the MySQL client or phpMySQL, using one
or more .sql dump files, or some other method.  This approach is flexible, but
not terribly user-friendly for anyone who isn't a developer.  Enter the second
approach - programmatic creation using the Eloquent models which form the core
of the database definition functionality.  This README will focus on the latter;
the former approach is pretty straightforward to work out from the way Eloquent
works, and you can always check out [the TestHelpers class file][tests/TestHelpers.php]
for a more elaborate example of defining calendars without Eloquent.

##### Calendar #####

Everything starts with `Danhunsaker\Calends\Eloquent\Calendar`.  This class
serves as both the `ObjectDefinitionInterface` implementation (see below) and
the core Eloquent model through which the other models are accessed.  Start by
using the `Calendar` class to create a new (empty) calendar definition:

```php
use Danhunsaker\Calends\Eloquent\Calendar;

$calendar = Calendar::create([
    'name'        => 'example',
    'description' => 'A simple example calendar.',
]);
```

The `name` is pretty much exactly what it says - you'll use it to tell `Calends`
which calendar definition to use.  The `description` is optional, so feel free
to skip it if you like.

##### Unit #####

Now that we have a `Calendar` created, we can start adding `Unit`s to it:

```php
$second = $calendar->units()->create([
    'internal_name' => 'second',
    'scale_amount' => 1,
    'scale_inverse' => false,
    'scale_to' => 0,
    'uses_zero' => true,
    'unix_epoch' => 0,
    'is_auxiliary' => false,
]);
```

- The `internal_name` should be unique within the calendar, since it will be
used to keep track of which values belong with which units.  Something
human-readable is strongly recommended, as it's used in offset parsing (more
on this below).

- `scale_amount`, `scale_inverse`, and `scale_to` all work together to define
how this `Unit` should be calculated relative to other `Unit`s.  `scale_to`
specifies which `Unit` to calculate against, and will usually be the `id` of
the appropriate `Unit`.  However, there is a special case for specifying the
`Unit`'s relationship with UNIX timestamp seconds.  In this case, you'll set
`scale_to` directly, to the value `0`, instead of adding another `Unit` via
Eloquent relationships.  `scale_amount` specifies how many `scale_to`s are in
a single `Unit`; `scale_inverse` specifies that the `scale_amount` is actually
the number of `Unit`s in a single `scale_to`.  Don't worry if that doesn't
make much sense, yet; there are more examples below that should help make this
clearer.

- `uses_zero` specifies whether the `Unit` starts counting from 0 or not.  If
set to false, the `Unit` is assumed to start counting from 1 instead.  As an
example of when this is useful, consider that seconds start counting from 0,
but months start counting from 1.  (NOTE: While it's tempting to use this to
indicate that something like years or hours don't use zero, the math starts to
break down if used this way.  Instead, set up `Era`s to cover these scenarios
(see below).)

- `unix_epoch` specifies what the value of this `Unit` was at the UNIX Epoch, or
`01 Jan 1970 00:00:00 UTC (Gregorian calendar)`.  This lets `Calends` determine
where a given date is relative to every other, and is vital to being able to
do things like convert dates to other calendar systems once they've been
parsed in. That said, the value is optional, because not every `Unit` needs to
specify its Epoch value (specifically, auxiliary `Unit` Epoch values should
generally be calculated instead).

- `is_auxiliary` tells `Calends` that the given `Unit` is a derived one, and not
fundamental to the value of the date.  This is useful for things like weeks
(for calendar systems where the week isn't vital to the date), centuries, and
attoseconds, where it's useful to be able to compute the values, but not vital
to the date calculations themselves.

Let's add a few more, so we have something more substantial to work with:

```php
$minute = $calendar->units()->create([
    'internal_name' => 'minute',
    'scale_amount' => 60,
    'scale_inverse' => false,
    'uses_zero' => true,
    'unix_epoch' => 0,
    'is_auxiliary' => false,
]);
$minute->scaleMeTo()->save($second);

$hour = $calendar->units()->create([
    'internal_name' => 'hour',
    'scale_amount' => 60,
    'scale_inverse' => false,
    'uses_zero' => true,
    'unix_epoch' => 0,
    'is_auxiliary' => false,
]);
$hour->scaleMeTo()->save($minute);

$day = $calendar->units()->create([
    'internal_name' => 'day',
    'scale_amount' => 24,
    'scale_inverse' => false,
    'uses_zero' => false,
    'unix_epoch' => 1,
    'is_auxiliary' => false,
]);
$day->scaleMeTo()->save($hour);

$month = $calendar->units()->create([
    'internal_name' => 'month',
    'scale_amount' => null,         // See below for why this works
    'scale_inverse' => false,
    'uses_zero' => false,
    'unix_epoch' => 1,
    'is_auxiliary' => false,
]);
$month->scaleMeTo()->save($day);

$year = $calendar->units()->create([
    'internal_name' => 'year',
    'scale_amount' => 12,
    'scale_inverse' => false,
    'uses_zero' => true,
    'unix_epoch' => 1970,
    'is_auxiliary' => false,
]);
$year->scaleMeTo()->save($month);

$century = $calendar->units()->create([
    'internal_name' => 'century',
    'scale_amount' => 100,
    'scale_inverse' => false,
    'uses_zero' => true,
    'unix_epoch' => null,
    'is_auxiliary' => true,
]);
$century->scaleMeTo()->save($year);

$millisecond = $calendar->units()->create([
    'internal_name' => 'millisecond',
    'scale_amount' => 1000,
    'scale_inverse' => true,
    'uses_zero' => true,
    'unix_epoch' => null,
    'is_auxiliary' => true,
]);
$millisecond->scaleMeTo()->save($second);
```

That looks like a lot going on, there, but most of it is the same thing repeated
over and over for each `Unit`.  Such is the nature of databases.  In there are
some examples of auxiliary `Unit`s, when to use `uses_zero` or not, and even an
inverted scale.  Also in there, though, is a special case we haven't discussed,
yet.  For the month `Unit`, `scale_amount` is set to `null`.  This is because
the number of day `Unit`s in a month `Unit` varies by month.  So we need to have
some way to tell `Calends` the length isn't fixed.  That way it will know to
check the `UnitLength`s instead, and handle them accordingly.

##### UnitLength #####

Let's define our month `UnitLength`s:

```php
$month->lengths()->createMany([
    ['unit_value' => 1,  'scale_amount' => 31],
    ['unit_value' => 2,  'scale_amount' => 28],
    ['unit_value' => 3,  'scale_amount' => 31],
    ['unit_value' => 4,  'scale_amount' => 30],
    ['unit_value' => 5,  'scale_amount' => 31],
    ['unit_value' => 6,  'scale_amount' => 30],
    ['unit_value' => 7,  'scale_amount' => 31],
    ['unit_value' => 8,  'scale_amount' => 31],
    ['unit_value' => 9,  'scale_amount' => 30],
    ['unit_value' => 10, 'scale_amount' => 31],
    ['unit_value' => 11, 'scale_amount' => 30],
    ['unit_value' => 12, 'scale_amount' => 31],
]);
```

Pretty straightforward, there.  `unit_value` is the value of `Unit` for which
the `scale_amount` specifies the correct length.

##### Formatting and Parsing #####

Now that we have all that set up, `Calends` can already start calculating dates
in our new calendar system.  Of course, that's not terribly useful unless we can
see and work with those dates, so next we need to define some formats.

`Calends` formats use a layered approach.  `CalendarFormat`s specify full
`format_string`s for complete dates, using a syntax similar to PHP's `date()`
function. `FragmentFormat`s actually define the single-character formatting
codes used by the `CalendarFormat`s, with `FragmentText`s providing a way to map
numeric values to arbitrary strings of text.  But why call them
`FragmentFormat`s?

###### Era and EraRange ######

Some `Unit`s are displayed and written using a nonlinear numbering.  For
example, years in the Gregorian calendar system are numbered normally for years
1 and higher, which are assigned the AD era.  But for years before 1, they are
numbered in descending order starting from 1, not 0, resulting in year 0 being
shown as 1 BC.  We need to properly handle these numbering schemes; enter `Era`
and `EraRange`.

`EraRange` is used to specify the `start_value` and `end_value` of an era, the
`direction` in which displayed values increment, and the `start_display` value
to map the starting `Unit` value to.  These attributes are also associated with
an internal `range_code` identifying which era the given range belongs to - this
is useful in a number of cases we'll explore in a moment.  `Era` simply groups
`EraRange`s together, gives them a common `internal_name`, and specifies which
internal era code to use when a given date being parsed doesn't include the code
explicitly (that is, a `default_range`).

Let's create a couple of `Era`s:

```php
$yearsEra = $year->eras()->create([
    'internal_name' => 'gregorian-years',
    'default_range' => 'ad'
]);
$hoursEra = $hour->eras()->create([
    'internal_name' => '12-hour-time',
    'default_range' => 'am'
]);

$yearsEra->ranges()->createMany([
    [
        'range_code'    => 'bc',
        'start_value'   => 0,
        'end_value'     => null,
        'start_display' => 1,
        'direction'     => 'desc'
    ],
    [
        'range_code'    => 'ad',
        'start_value'   => 1,
        'end_value'     => null,
        'start_display' => 1,
        'direction'     => 'asc'
    ]
]);

$hoursEra->ranges()->createMany([
    [
        'range_code'    => 'am',
        'start_value'   => 0,
        'end_value'     => 0,
        'start_display' => 12,
        'direction'     => 'asc'
    ],
    [
        'range_code'    => 'am',
        'start_value'   => 1,
        'end_value'     => 11,
        'start_display' => 1,
        'direction'     => 'asc'
    ],
    [
        'range_code'    => 'pm',
        'start_value'   => 12,
        'end_value'     => 12,
        'start_display' => 12,
        'direction'     => 'asc'
    ],
    [
        'range_code'    => 'pm',
        'start_value'   => 13,
        'end_value'     => 23,
        'start_display' => 1,
        'direction'     => 'asc'
    ],
    [
        'range_code'    => 'am',
        'start_value'   => 24,
        'end_value'     => 24,
        'start_display' => 12,
        'direction'     => 'asc'
    ]
]);

```

###### FragmentFormat and FragmentText ######

Now back to the question of `FragmentFormat`.  An `Era` can be the target of a
format just as much as a `Unit`, so a formatting approach that supports both
equally makes sense, here.  Each is a fragment of a complete date, so calling
it `FragmentFormat` also seems to make sense.  Let's build out a subset of the
format codes supported by PHP's `date()`:

```php
$fragments = [
    'd' => $calendar->fragments()->create([
        'format_code'   => 'd',
        'format_string' => '%{value}$02d',
        'description'   => 'Day of the month, 2 digits with leading zeros',
    ]),
    'j' => $calendar->fragments()->create([
        'format_code'   => 'j',
        'format_string' => '%{value}$d',
        'description'   => 'Day of the month without leading zeros',
    ]),
    'F' => $calendar->fragments()->create([
        'format_code'   => 'F',
        'format_string' => '%{value}$s',
        'description'   => 'A full textual representation of a month, such as January or March',
    ]),
    'm' => $calendar->fragments()->create([
        'format_code'   => 'm',
        'format_string' => '%{value}$02d',
        'description'   => 'Numeric representation of a month, with leading zeros',
    ]),
    'M' => $calendar->fragments()->create([
        'format_code'   => 'M',
        'format_string' => '%{value}$s',
        'description'   => 'A short textual representation of a month, three letters',
    ]),
    'n' => $calendar->fragments()->create([
        'format_code'   => 'n',
        'format_string' => '%{value}$d',
        'description'   => 'Numeric representation of a month, without leading zeros',
    ]),
    't' => $calendar->fragments()->create([
        'format_code'   => 't',
        'format_string' => '%{length}$d',
        'description'   => 'Number of days in the given month',
    ]),
    'Y' => $calendar->fragments()->create([
        'format_code'   => 'Y',
        'format_string' => '%{value}$04d',
        'description'   => 'A full numeric representation of a year, 4 digits',
    ]),
    'y' => $calendar->fragments()->create([
        'format_code'   => 'y',
        'format_string' => '%{value}%100$02d',
        'description'   => 'A two digit representation of a year',
    ]),
    'E' => $calendar->fragments()->create([
        'format_code'   => 'E',
        'format_string' => '%{code}$s',
        'description'   => 'The calendar epoch (BC/AD)',
    ]),
    'a' => $calendar->fragments()->create([
        'format_code'   => 'a',
        'format_string' => '%{code}$s',
        'description'   => 'Lowercase Ante meridiem and Post meridiem',
    ]),
    'A' => $calendar->fragments()->create([
        'format_code'   => 'A',
        'format_string' => '%{code}$s',
        'description'   => 'Uppercase Ante meridiem and Post meridiem',
    ]),
    'g' => $calendar->fragments()->create([
        'format_code'   => 'g',
        'format_string' => '%{value}$d',
        'description'   => '12-hour format of an hour without leading zeros',
    ]),
    'G' => $calendar->fragments()->create([
        'format_code'   => 'G',
        'format_string' => '%{value}$d',
        'description'   => '24-hour format of an hour without leading zeros',
    ]),
    'h' => $calendar->fragments()->create([
        'format_code'   => 'h',
        'format_string' => '%{value}$02d',
        'description'   => '12-hour format of an hour with leading zeros',
    ]),
    'H' => $calendar->fragments()->create([
        'format_code'   => 'H',
        'format_string' => '%{value}$02d',
        'description'   => '24-hour format of an hour with leading zeros',
    ]),
    'i' => $calendar->fragments()->create([
        'format_code'   => 'i',
        'format_string' => '%{value}$02d',
        'description'   => 'Minutes with leading zeros',
    ]),
    's' => $calendar->fragments()->create([
        'format_code'   => 's',
        'format_string' => '%{value}$02d',
        'description'   => 'Seconds, with leading zeros',
    ]),
];

$fragments['d']->fragment()->save($day);
$fragments['j']->fragment()->save($day);
$fragments['F']->fragment()->save($month);
$fragments['m']->fragment()->save($month);
$fragments['M']->fragment()->save($month);
$fragments['n']->fragment()->save($month);
$fragments['t']->fragment()->save($month);
$fragments['Y']->fragment()->save($yearsEra);
$fragments['y']->fragment()->save($yearsEra);
$fragments['E']->fragment()->save($yearsEra);
$fragments['a']->fragment()->save($hoursEra);
$fragments['A']->fragment()->save($hoursEra);
$fragments['g']->fragment()->save($hoursEra);
$fragments['G']->fragment()->save($hour);
$fragments['h']->fragment()->save($hoursEra);
$fragments['H']->fragment()->save($hour);
$fragments['i']->fragment()->save($minute);
$fragments['s']->fragment()->save($second);

$fragments['F']->texts()->createMany([
    ['fragment_value' => 1, 'fragment_text' => 'January'],
    ['fragment_value' => 2, 'fragment_text' => 'February'],
    ['fragment_value' => 3, 'fragment_text' => 'March'],
    ['fragment_value' => 4, 'fragment_text' => 'April'],
    ['fragment_value' => 5, 'fragment_text' => 'May'],
    ['fragment_value' => 6, 'fragment_text' => 'June'],
    ['fragment_value' => 7, 'fragment_text' => 'July'],
    ['fragment_value' => 8, 'fragment_text' => 'August'],
    ['fragment_value' => 9, 'fragment_text' => 'September'],
    ['fragment_value' => 10, 'fragment_text' => 'October'],
    ['fragment_value' => 11, 'fragment_text' => 'November'],
    ['fragment_value' => 12, 'fragment_text' => 'December']
]);

$fragments['M']->texts()->createMany([
    ['fragment_value' => 1, 'fragment_text' => 'Jan'],
    ['fragment_value' => 2, 'fragment_text' => 'Feb'],
    ['fragment_value' => 3, 'fragment_text' => 'Mar'],
    ['fragment_value' => 4, 'fragment_text' => 'Apr'],
    ['fragment_value' => 5, 'fragment_text' => 'May'],
    ['fragment_value' => 6, 'fragment_text' => 'Jun'],
    ['fragment_value' => 7, 'fragment_text' => 'Jul'],
    ['fragment_value' => 8, 'fragment_text' => 'Aug'],
    ['fragment_value' => 9, 'fragment_text' => 'Sep'],
    ['fragment_value' => 10, 'fragment_text' => 'Oct'],
    ['fragment_value' => 11, 'fragment_text' => 'Nov'],
    ['fragment_value' => 12, 'fragment_text' => 'Dec']
]);

$fragments['E']->texts()->createMany([
    ['fragment_value' => 'bc', 'fragment_text' => 'BC'],
    ['fragment_value' => 'ad', 'fragment_text' => 'AD']
]);

$fragments['a']->texts()->createMany([
    ['fragment_value' => 'am', 'fragment_text' => 'am'],
    ['fragment_value' => 'pm', 'fragment_text' => 'pm']
]);

$fragments['A']->texts()->createMany([
    ['fragment_value' => 'am', 'fragment_text' => 'AM'],
    ['fragment_value' => 'pm', 'fragment_text' => 'PM']
]);
```

###### CalendarFormat ######

And of course the `CalendarFormat`s:

```php
$defaultFormat = $calendar->formats()->create([
    'format_name' => 'eloquent',
    'format_string' => 'd M Y H:i:s',
    'description' => 'A basic date format'
]);
$calendar->formats()->create([
    'format_name' => 'mod8601',
    'format_string' => 'Y-m-d H:i:s',
    'description' => 'A modified ISO 8601 date'
]);
$calendar->formats()->create([
    'format_name' => 'filestr',
    'format_string' => 'Y-m-d_H-i-s',
    'description' => 'A date suitable for use in filenames'
]);

$calendar->defaultFormat->save($defaultFormat);
```

###### More on Formatting ######

Again, a lot of repetition.  `FragmentFormat` has a `format_code`, which is the
single-character date formatting code mentioned earlier, a `format_string`,
which tells `Calends` how to render the value (more on that in a moment), and an
optional `description`.

The `format_string` is an expanded variant of the format used by PHP's
`sprintf()` family of functions.  Where that spec places an integer value
specifying which numbered argument to use in the given part of the expression,
`Calends` expects a formula compatible with BC::math's `BC::parse()` method
(BC::math is included as a dependency of Calends).  It will pass in a few
properties of the fragment being rendered, such as the `length` and `value`, and
in the case of an `Era` fragment, the range `code`.  It is the result of this
expression that is actually rendered into the appropriate part of the date.
Several examples are given above.

It is important to actually assign a fragment object to each `FragmentFormat`,
or the entire thing will fall apart.  This is done in the
`$fragments[<code>]->fragment()->save(<fragment object>)` statements above.

`FragmentText`s are pretty straightforward - as mentioned earlier, a
`fragment_value` to be transformed into an associated `fragment_text`.

That leaves the `CalendarFormat`s.  A `format_name` to provide an
easily-remembered alias for `getDate()`, the actual (PHP `date()`-compatible)
`format_string` that instructs `Calends` in the correct way to render the
format, and an optional `description`.

Now you can easily parse and format dates in your new calendar system!  The
rendering formats are automatically reverse-engineered into parsing formats as
needed, so no need to worry about defining those.

##### Date Offsets and UnitName #####

Of course, there is one scenario still unexplored, at this stage: date offsets.
The most basic of offsets are already parsable thanks to the `internal_name` on
your `Unit`s.  But what of plural forms and other alternative names for your
units?  No worries, `UnitName` is available for just this purpose.  Let's add
a few to our calendar definition:

```php
$second->names()->create([
    'unit_name' => 'seconds',
    'name_context' => 'plural'
]);
$minute->names()->create([
    'unit_name' => 'minutes',
    'name_context' => 'plural'
]);
$hour->names()->create([
    'unit_name' => 'hours',
    'name_context' => 'plural'
]);
$day->names()->create([
    'unit_name' => 'days',
    'name_context' => 'plural'
]);
$month->names()->create([
    'unit_name' => 'months',
    'name_context' => 'plural'
]);
$year->names()->create([
    'unit_name' => 'years',
    'name_context' => 'plural'
]);
$century->names()->create([
    'unit_name' => 'centuries',
    'name_context' => 'plural'
]);
$millisecond->names()->create([
    'unit_name' => 'milliseconds',
    'name_context' => 'plural'
]);
```

Each `UnitName` provides an alternative `unit_name` for offset parsing, and an
optional `name_context`, which is currently unused within `Calends` itself, but
could be useful in cases like internationalization.

##### Still To Come #####

The observant reader will probably have noticed that we never touched on another
form of special case encountered often in calendar systems, and which makes date
libraries like this one particularly tricky to write.  This special case is
called "intercalation", and refers to when units of time are inserted, removed,
or otherwise changed from the basic calendar.  Perhaps the best known example is
the one many of you will have noticed missing above - leap days.  As
intercalations go, February 29th (actually the 24th due to the way the Romans
set it up way back in Julius Caesar's day, but still) is pretty basic.  It
wouldn't take too much to support that specific intercalation, but there are
other, much more complex intercalations in use around the world, from the
Hebrew calendar's intercalary month and varying month lengths, to the
oft-overlooked leap second, and the goal is to support those kinds of
intercalation as well.  So for the time being, intercalations are entirely
unimplemented, and will be included in a future release.

* **_TO DO:_** implement intercalations, and document them here.

If you notice anything else missing, please feel free to open an issue on
[GitHub][] and let me know about it.  Some features are outside the scope of the
project, but I'd love to consider all options!

#### Object Definitions ####

Of course, you could also implement the interface used by the Eloquent model
([`Danhunsaker\Calends\Calendar\ObjectDefinitionInterface`](src/Calendar/ObjectDefinitionInterface.php))
directly on *any* class you wanted to, and register instances of *that* to
handle various calendars.  Just because the interface is designed for database
use doesn't mean it can't be used elsewhere.  Using such classes would look
something like this:

```php
use Danhunsaker\Calends\Calends;

Calends::registerCalendar('myCustomCalendar', new MyCustomCalendar($params));
```

### New Converters ###

Start by building a class that implements
[`Danhunsaker\Calends\Converter\ConverterInterface`](src/Converter/ConverterInterface.php),
just like the bulit-in converters do.  Once your converter class is available in
your project, simply register it with `Calends::registerConverter()`:

```php
use Danhunsaker\Calends\Calends;

Calends::registerConverter('myDateTimeClass', MyConverter::class);
```

Just like with new calendars, this will make your converter available to all
`Calends` objects throughout your project.

> Note that while Calends will automatically find and register converters in the
> `Danhunsaker\Calends\Converter` namespace, it is considered bad form to create
> your classes there unless they're officially recognized by the main project,
> since a namespace implies official support and/or endorsement.

## Contributions ##

Pull requests, bug reports, and so forth are all welcome on [GitHub][].

Security issues should be reported directly to [danhunsaker (plus) calends (at)
gmail (dot) com](mailto:danhunsaker+calends@gmail.com).

And head to [GitHub][] for everything else.

[GitHub]:https://github.com/danhunsaker/calends
[TAI64NA]:http://cr.yp.to/libtai/tai64.html
