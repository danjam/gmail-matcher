# gmail-matcher

[![Build Status](https://travis-ci.org/danjam/gmail-matcher.svg?branch=master)](https://travis-ci.org/danjam/gmail-matcher) [![Coverage Status](https://coveralls.io/repos/github/danjam/gmail-matcher/badge.svg?branch=master)](https://coveralls.io/github/danjam/gmail-matcher?branch=master)

WIP matcher for Gmail addresses. Real docs coming soon

Rules:
 * Dots in email addresses are disregarded (`foobar@gmail.com` is the same as `foo.bar@gmail.com`). ([Source](https://support.google.com/mail/answer/7436150))
 * Addresses are case insensitive (`FOOBAR@gmail.com` is the same as `foobar@gmail.com`)
 * The `gmail.com` domain is interchangeable with `googlemail.com` (`foobar@gmail.com` is the same as `foobar@googlemail.com`) ([Source](https://support.google.com/mail/answer/10313))
 * Pluses are allowed in emails (`foobar+baz@gmail.com`)

## Usage

```php

// instantiate new matcher
$gmailMatcher = new \danjam\GmailMatcher\GmailMatcher();

// foo@gmail.com
$gmailMatcher->normalize('F.O.O@gmail.com');

// true
$gmailMatcher->match('F.O.O@gmail.com', 'foo@gmail.com');

// false
$gmailMatcher->match('bar@gmail.com', 'bar@gmail.com');

// multiple addresses
$gmailMatcher->match('bar@gmail.com', 'b.a.r@gmail.com', 'bar@googlemail.com', ...);


```
