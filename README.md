# Calends

[![Latest Version](https://img.shields.io/github/release/danhunsaker/calends.svg?style=flat-square)](https://github.com/danhunsaker/calends/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/danhunsaker/calends/master.svg?style=flat-square)](https://travis-ci.org/danhunsaker/calends)
[![Total Downloads](https://img.shields.io/packagist/dt/danhunsaker/calends.svg?style=flat-square)](https://packagist.org/packages/danhunsaker/calends)

Arbitrary calendar systems in PHP.

## Installation

Use Composer:

```bash
composer require danhunsaker/calends
```

## Usage

- [ ] Setup
  - [ ] Laravel
  - [ ] Vanilla PHP
- [x] Dates
- [x] Conversion
- [x] Storage
- [x] Compare
- [ ] Modify
- [ ] Ranges
- [ ] New Calendars
  - [x] Class Definitions
  - [ ] Database Definitions

### Setup

#### Laravel

* ***__TO DO__***

#### Other Projects

* ***__TO DO__***

### Dates

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

// UNIX Epoch - the following are equivalent:
$epoch = new Calends(0);
$epoch = new Calends(0, 'unix');
$epoch = new Calends(2440587.5, 'jdc');
$epoch = new Calends('1970-01-01 00:00:00 UTC', 'gregorian');
```

### Conversion

Now you can convert that date to any other supported calendar system:

```php
use Danhunsaker\Calends\Calends;

$now = new Calends();

// Using getDate():
$unix = $now->getDate('unix');     // 1451165670.329400000000000000
// Or just use as a function - __invoke() calls getDate()
$unix = $now('unix');              // 1451165670.329400000000000000
// The default 'calendar' for getDate() is also 'unix'
$unix = $now();                    // 1451165670.329400000000000000

$julianDayCount = $now('jdc');     // 2457383.398962145833333333
$gregorian = $now('gregorian');    // Sat Dec 26 14:34:30 2015
$julianCalendar = $now('julian');  // 12/13/2015 14:34:30 GMT-07:00
```

### Storage

You can technically store Calends date values in any of the supported output
formats, however this is not recommended for various reasons, performance among
them.  Instead, save and restore `Calends` objects using the built-in `tai`
'calendar' (alternately, save by casting the object to `string`):

```php
use Danhunsaker\Calends\Calends;

$now = new Calends();

$tai = $now->getDate('tai');       // 40000000567f07e613a23ec000000000
$tai = $now('tai');                // 40000000567f07e613a23ec000000000
$tai = (string) $now;              // 40000000567f07e613a23ec000000000

// Save the value of $tai in your database, or wherever makes sense for your app
```

Then, any time you need to recreate the saved `Calends` object:

```php
use Danhunsaker\Calends\Calends;

// Retrieve the previously-stored value of $tai...

$date = new Calends($tai, 'tai');
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
itself to (and from, in the case of `unserialize()`) the `tai` date.

### Compare

Often it is useful to compare two dates to see which came first.  One good
example of this is sorting.  Calends is designed with this in mind, supporting
four different methods for doing date comparisons.  Since sorting is so common,
we'll start with the method designed for that:

```php
use Danhunsaker\Calends\Calends;

$times = [];
for ($i = 0; $i < 10; $i++)
{
    $times[] =  new Calends(mt_rand(0 - mt_getrandmax(), mt_getrandmax()));
}

print_r($times);
$sorted = usort($times, [Calends::class, 'compare']);
print_r($sorted);
```

`Calends::compare()` accepts two `Calends` objects to compare, and returns -1 if
the first is before the second, 0 if they are equal, and +1 if the first is
after the second.  This is compatible with PHP's sorting functions and their
expectations for the behavior of sorting callbacks.

The other three methods provide more focused comparisons, returning `true` or
`false` instead of lesser/equal/greater:

```php
use Danhunsaker\Calends\Calends;

$epoch = new Calends(0);
$now   = new Calends();

print_r([
    $epoch::isBefore($now),    // true
    $epoch::isSame($now),      // false
    $epoch::isAfter($now),     // false
]);
```

Each of these methods accepts the `Calends` object to compare the current one
to, and returns a boolean value, as mentioned above.

### Modify

* ***__TO DO__***

### Ranges

* ***__TO DO__***

### New Calendars

#### Class Definitions

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

#### Database Definitions

The other way is by storing your definition in a database.  To use this
approach, you need to include `illuminate/database` in your project.  (This
library is part of the Laravel framework, so you may already have it available.)
It takes a bit more work to use this approach, but it can be extremely useful in
cases where you wish to allow your users to define their own calendar systems in
your project, without expecting them to write any code.

* ***__TO DO:__*** implement database definitions, and document them here.

## Contributions

Pull requests, bug reports, and so forth are all welcome on [GitHub][].

Security issues should be reported directly to [danhunsaker (plus) calends (at)
gmail (dot) com](mailto:danhunsaker+calends@gmail.com).

And head to [GitHub][] for everything else.

[GitHub]:https://github.com/danhunsaker/calends
[TAI64NA]:http://cr.yp.to/libtai/tai64.html
