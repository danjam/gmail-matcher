# gmail-matcher

[![Build Status](https://travis-ci.org/danjam/gmail-matcher.svg?branch=master)](https://travis-ci.org/danjam/gmail-matcher) [![Coverage Status](https://coveralls.io/repos/github/danjam/gmail-matcher/badge.svg?branch=master)](https://coveralls.io/github/danjam/gmail-matcher?branch=master) [![Infection MSI](https://badge.stryker-mutator.io/github.com/danjam/gmail-matcher/master)](https://infection.github.io)

**TL;DR: This is a PHP 7.1+ class that can normalize and compare different formats of the same Gmail address.**

Gmail address rules have some caveats:

 * Dots in email addresses are disregarded (`foobar@gmail.com` is the same as `foo.bar@gmail.com`). ([Source](https://support.google.com/mail/answer/7436150))
 * Addresses are case insensitive (`FOOBAR@gmail.com` is the same as `foobar@gmail.com`)
 * The `gmail.com` domain is interchangeable with `googlemail.com` (`foobar@gmail.com` is the same as `foobar@googlemail.com`) ([Source](https://support.google.com/mail/answer/10313))
 * Pluses are allowed in emails (`foobar+baz@gmail.com`)
 
Because of this it is very possible for a system to have multiple variations of the same Gmail address associated with different users.

This class attempts to address this by providing a `match(…)` method to check variations against each other and a `normalize(…)` method to retrieve a normalized version of the address. Also exposed is a method for determining if an address is a Gamil address, `isGmailAddress(…)`.

Between these three methods it should be possible to check, normalize and store Gmail addresses, for example to prevent duplicate signups within a system.

## Install

Via [Composer](https://getcomposer.org/)

```
$ composer require danjam/gmail-matcher
```

## Usage

```php
// instantiate new matcher
$gmailMatcher = new \danjam\GmailMatcher\GmailMatcher();

// you can also specify the domain used when normalizing. Must be one of gmail.com, googlemail.com. Defaults to gmail.com
$gmailMatcher = new \danjam\GmailMatcher\GmailMatcher('googlemail.com');

// normalizes the address - returns foo@gmail.com
$gmailMatcher->normalize('F.O.O@gmail.com');

// check if two addresses are the same - returns true
$gmailMatcher->match('F.O.O@gmail.com', 'foo@gmail.com');

// returns false
$gmailMatcher->match('foo@gmail.com', 'bar@gmail.com');

// returns false - addresses with + should NOT be treated as the same address
$mailMatcher->match('foo@gmail', 'foo+bar@gmail.com');

// multiple addresses
$gmailMatcher->match('bar@gmail.com', 'b.a.r@gmail.com', 'bar@googlemail.com', ...);

// validate the address is a Gmail address - returns true
$gmailMatcher->isGmailAddress('foo@gmail.com');

// returns false
$gmailMatcher->isGmailAddress('bar@example.com');
```
