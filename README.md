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

- Setup
  - [ ] Vanilla PHP
  - [ ] Laravel
- [x] Dates
- [x] Conversion
- [x] Storage
- [ ] Compare
- [ ] Modify
- [ ] Ranges
- [ ] New Calendars
  - [x] Class Definitions
  - [ ] Database Definitions

### Setup

* To Do

### Dates

Create a `Calends` object with the date and calendar system as arguments to the
constructor:

```php
use Danhunsaker\Calends\Calends;

$now = new Calends();

// UNIX Epoch - the following are equivalent:
$epoch = new Calends(0, 'unix');
$epoch = new Calends(2440587.5, 'jdc');
$epoch = new Calends('1970-01-01 00:00:00 UTC', 'gregorian');
```

### Conversion

Now you can convert that date to any other supported calendar system:

```php
use Danhunsaker\Calends\Calends;

$now = new Calends();

$unix = $now->getDate('unix');              // 1451165670.329400000000000000
$julianDayCount = $now->getDate('jdc');     // 2457383.398962145833333333
$gregorian = $now->getDate('gregorian');    // Sat Dec 26 14:34:30 2015
$julianCalendar = $now->getDate('julian');  // 12/13/2015 14:34:30 GMT-07:00
```

### Storage

You can technically store Calends date values in any of the supported output
formats, however this is not recommended for various reasons, performance among
them.  Instead, save and restore `Calends` objects using the built-in `tai`
'calendar' (alternately, save by casting the object to `string`):

```php
use Danhunsaker\Calends\Calends;

$now = new Calends();

$tai = $now->getDate('tai');                // 40000000567f07e613a23ec000000000
$tai = (string) $now;                       // Same as above

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

### Modify

### Ranges

### New Calendars

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

The other way is with by storing your definition in a database.  To use this
approach, you need to include `illuminate/database` in your project.  (This
library is part of the Laravel framework, so you may already have it available.)
It takes a bit more work to use this approach, but it can be extremely useful in
cases where you wish to allow your users to define their own calendar systems in
your project, without expecting them to write any code.

***__TODO:__*** implement database definitions, and document them here.

## Contributions

Pull requests, bug reports, and so forth are all welcome on [GitHub][].

Security issues should be reported directly to [danhunsaker (plus) calends (at)
gmail (dot) com](mailto:danhunsaker+calends@gmail.com).

And head to [GitHub][] for everything else.

[GitHub]:https://github.com/danhunsaker/calends
[TAI64NA]:http://cr.yp.to/libtai/tai64.html
