# Calends #

[![Latest Version](https://img.shields.io/github/release/danhunsaker/calends.svg?style=flat-square)](https://github.com/danhunsaker/calends/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/danhunsaker/calends/master.svg?style=flat-square)](https://travis-ci.org/danhunsaker/calends)
[![Total Downloads](https://img.shields.io/packagist/dt/danhunsaker/calends.svg?style=flat-square)](https://packagist.org/packages/danhunsaker/calends)

Arbitrary calendar systems in PHP.

## Installation ##

Use Composer:

```bash
composer require danhunsaker/calends
```

## Usage ##

- [ ] Setup
  - [ ] Laravel
  - [ ] Other Projects
- [x] Dates
- [x] Conversion
- [x] Storage
- [x] Compare
- [x] Modify
- [x] Ranges
- [ ] New Calendars
  - [x] Class Definitions
  - [ ] Database Definitions
  - [x] Object Definitions
- [x] New Converters

### Setup ###

#### Laravel ####

* **_TO DO_**

#### Other Projects ####

* **_TO DO_**

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
$gregorian = $now('gregorian');    // Sat Dec 26 14:34:30 2015
$julianCalendar = $now('julian');  // 12/13/2015 14:34:30 GMT-07:00
```

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

Supported conversions include `DateTime` (and its child, `Carbon`),
`IntlCalendar`, and `Period`.  See below for how to add support for others.

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
             // yesterday to today
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

$last24hrs = $now->setDate($yesterday->getDate('gregorian'), 'gregorian');

print_r([
    $now->startsBefore($last24hrs),   // false
    $now->isBefore($last24hrs),       // false
    $now->endsBefore($last24hrs),     // true
    $now->isSame($last24hrs),         // false
    $now->startsDuring($last24hrs),   // true
    $now->isDuring($last24hrs),       // true
    $now->endsDuring($last24hrs),     // true
    $now->contains($last24hrs),       // false
    $now->overlaps($last24hrs),       // true
    $now->abuts($last24hrs),          // true
    $now->startsAfter($last24hrs),    // false
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

$next7days      = Calends::create(['start' => 'now', 'end' => 'now +7 days'], 'gregorian');
$last7days      = Calends::create(['start' => 'now -7 days', 'end' => 'now'], 'gregorian');

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

* **_TO DO:_** implement database definitions, and document them here.

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
your project, simply register it with `Calends::registerClassConverter()`:

```php
use Danhunsaker\Calends\Calends;

Calends::registerClassConverter('myDateTimeClass', MyConverter::class);
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
