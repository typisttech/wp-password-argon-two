# WP Password Argon Two

[![Latest Stable Version](https://poser.pugx.org/typisttech/wp-password-argon-two/v/stable)](https://packagist.org/packages/typisttech/wp-password-argon-two)
[![Total Downloads](https://poser.pugx.org/typisttech/wp-password-argon-two/downloads)](https://packagist.org/packages/typisttech/wp-password-argon-two)
[![Build Status](https://travis-ci.org/TypistTech/wp-password-argon-two.svg?branch=master)](https://travis-ci.org/TypistTech/wp-password-argon-two)
[![StyleCI](https://styleci.io/repos/121093174/shield?branch=master)](https://styleci.io/repos/121093174)
[![License](https://poser.pugx.org/typisttech/wp-password-argon-two/license)](https://packagist.org/packages/typisttech/wp-password-argon-two)
[![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://typist.tech/donate/wp-password-argon-two/)
[![Hire Typist Tech](https://img.shields.io/badge/Hire-Typist%20Tech-ff69b4.svg)](https://typist.tech/contact/)

Securely store WordPress user passwords in database with Argon2i hashing and SHA-512 HMAC using PHP's native functions.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Goal](#goal)
- [Magic Moments](#magic-moments)
- [Requirements](#requirements)
  - [Do Your Homework](#do-your-homework)
  - [PHP 7.2+ and compiled `--with-password-argon2`](#php-72-and-compiled---with-password-argon2)
- [Installation](#installation)
    - [Step 0](#step-0)
    - [Step 1](#step-1)
    - [Option A: Via Composer Autoload (Recommended)](#option-a-via-composer-autoload-recommended)
    - [Option B: As a Must-use Plugin (Last Resort)](#option-b-as-a-must-use-plugin-last-resort)
    - [Step 2](#step-2)
- [Usage](#usage)
    - [Pepper Migration](#pepper-migration)
    - [Argon2i Options](#argon2i-options)
- [Uninstallation](#uninstallation)
- [Frequently Asked Questions](#frequently-asked-questions)
  - [What have you done with the passwords?](#what-have-you-done-with-the-passwords)
  - [I have installed this plugin. Does it mean my WordPress site is *unhackable*?](#i-have-installed-this-plugin-does-it-mean-my-wordpress-site-is-unhackable)
  - [Did you reinvent the cryptographic functions?](#did-you-reinvent-the-cryptographic-functions)
  - [Pepper migration look great. Does it mean that I can keep as many pepper keys as I want?](#pepper-migration-look-great-does-it-mean-that-i-can-keep-as-many-pepper-keys-as-i-want)
  - [What if my pepper is compromised?](#what-if-my-pepper-is-compromised)
  - [Is pepper-ing perfect?](#is-pepper-ing-perfect)
  - [Is WordPress' phpass hasher or Bcrypt insecure?](#is-wordpress-phpass-hasher-or-bcrypt-insecure)
  - [Why use Argon2i over the others?](#why-use-argon2i-over-the-others)
  - [Does this plugin has 72-character limit like Bcrypt?](#does-this-plugin-has-72-character-limit-like-bcrypt)
  - [This plugin isn't on wp.org. Where can I give a :star::star::star::star::star: review?](#this-plugin-isnt-on-wporg-where-can-i-give-a-starstarstarstarstar-review)
  - [This plugin isn't on wp.org. Where can I make a complaint?](#this-plugin-isnt-on-wporg-where-can-i-make-a-complaint)
- [Alternatives](#alternatives)
- [Support!](#support)
  - [Donate](#donate)
  - [Why don't you hire me?](#why-dont-you-hire-me)
  - [Want to help in other way? Want to be a sponsor?](#want-to-help-in-other-way-want-to-be-a-sponsor)
- [Developing](#developing)
- [Feedback](#feedback)
- [Change Log](#change-log)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Goal

Replace WordPress' [phpass](http://openwall.com/phpass) hasher with Argon2i hashing and SHA-512 HMAC.

Adopted from [Mozilla secure coding guidelines](https://wiki.mozilla.org/WebAppSec/Secure_Coding_Guidelines#Password_Storage):

* Passwords stored in a database should using the hmac+argon2i function.

The purpose of HMAC and Argon2i storage is as follows:

* Argon2i provides a hashing mechanism which can be configured to consume sufficient time to prevent brute forcing of hash values even with many computers
* Argon2i can be easily adjusted at any time to increase the amount of work and thus provide protection against more powerful systems
* The nonce(pepper) for the HMAC value is designed to be stored on the file system and not in the databases storing the password hashes. In the event of a compromise of hash values due to SQL injection, the nonce(pepper) will still be an unknown value since it would not be compromised from the file system. This significantly increases the complexity of brute forcing the compromised hashes considering both Argon2i and a large unknown nonce(pepper) value
* The HMAC operation is simply used as a secondary defense in the event there is a design weakness with Argon2i that could leak information about the password or aid an attacker

## Magic Moments

WP Password Argon Two just works when:
* upgrading from extremely old WordPress versions

  user passwords were hashed with MD5

* upgrading from recent WordPress versions

  user passwords were hashed with [phpass](http://openwall.com/phpass) hasher

* upgrading from [WP Password Bcrypt](https://github.com/roots/wp-password-bcrypt)

  user passwords were hashed with Bcrypt

* changing Argon2i options

* using new pepper while moving the old ones into `WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS`

User passwords will be rehashed during the next login.

## Requirements

### Do Your Homework

Don't blindly trust any random security guide/plugin on the scary internet - including this one!

Do your research:
* Read the whole [readme](./README.md)
* Read the [source code](./src)
* Compare with other [alternatives](#alternatives)

### PHP 7.2+ and compiled `--with-password-argon2`

To check whether PHP is compiled with Argon2:
```bash
# Good: Compiled with Argon2
➜ php -r 'print_r(get_defined_constants());' | grep -i argon
    [PASSWORD_ARGON2I] => 2
    [PASSWORD_ARGON2_DEFAULT_MEMORY_COST] => 1024
    [PASSWORD_ARGON2_DEFAULT_TIME_COST] => 2
    [PASSWORD_ARGON2_DEFAULT_THREADS] => 2
    [SODIUM_CRYPTO_PWHASH_ALG_ARGON2I13] => 1
    [SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13] => 2
    [SODIUM_CRYPTO_PWHASH_STRPREFIX] => $argon2id$
```

If you don't get the above output, either re-compile PHP 7.2+ with the flag `--with-password-argon2` or:

* Ubuntu
    ```bash
    ➜ sudo add-apt-repository ppa:ondrej/php
    ➜ sudo apt-get update
    ➜ sudo apt-get install php7.2
    ```
* macOS
    ```bash
    ➜ brew update
    ➜ brew install php
    ```

## Installation

#### Step 0

Read the whole [readme](./README.md) and the [source code](./src) before going any further.

#### Step 1

This plugin **should not** be installed as a normal WordPress plugin.

#### Option A: Via Composer Autoload (Recommended)

```bash
➜ composer require typisttech/wp-password-argon-two
```

Note: Files in [`src`](./src) will be autoloaded by composer. WP Password Argon Two **won't** appear in the WP admin dashboard.

#### Option B: As a Must-use Plugin (Last Resort)

Manually copy [`wp-password-argon-two.php`](./wp-password-argon-two.php) and the whole [`src`](./src) directory into [`mu-plugins` folder](https://codex.wordpress.org/Must_Use_Plugins).

```bash
# Example
➜ tree ./wp-content/mu-plugins
./wp-content/mu-plugins
├── src
│   ├── Manager.php
│   ├── ManagerFactory.php
│   ├── PasswordLock.php
│   ├── Validator.php
│   ├── ValidatorInterface.php
│   ├── WordPressValidator.php
│   └── pluggable.php
└── wp-password-argon-two.php
```

#### Step 2

##### Option A - Use Constants

Add these constants into `wp-config.php`:
```php
define('WP_PASSWORD_ARGON_TWO_PEPPER', 'your-long-and-random-pepper');
define('WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS', []);
define('WP_PASSWORD_ARGON_TWO_OPTIONS', []);
```

##### Option B - Use Environment Variables

Defining the required constants in application code violates [12-factor principle](https://12factor.net/). The [`typisttech/wp-password-argon-two-env`](https://github.com/TypistTech/wp-password-argon-two-env) package allows you to configure with environment variables.

Recommended for all [Trellis](https://github.com/roots/trellis) users.

## Usage

#### Pepper Migration

In some cases, you want to change the pepper without changing all user passwords.

```php
define('WP_PASSWORD_ARGON_TWO_PEPPER', 'new-pepper');
define('WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS', [
  'old-pepper-2',
  'old-pepper-1',
]);
```

During the next user login, his/her password will be rehashed with `new-pepper`.

#### Argon2i Options

> Due to the variety of platforms PHP runs on, the cost factors are deliberately set low as to not accidentally exhaust system resources on shared or low resource systems when using the default cost parameters. Consequently, users should adjust the cost factors to match the system they're working on. As Argon2 doesn't have any "bad" values, however consuming more resources is considered better than consuming less. Users are encouraged to adjust the cost factors for the platform they're developing for.
>
> -- [PHP RFC](https://wiki.php.net/rfc/argon2_password_hash#discussion_issues)

You can adjust the options via `WP_PASSWORD_ARGON_TWO_OPTIONS`:
```php
// Example
define('WP_PASSWORD_ARGON_TWO_OPTIONS', [
    'memory_cost' => 1<<17, // 128 Mb
    'time_cost'   => 4,
    'threads'     => 3,
]);
```

Learn more about [available options](https://secure.php.net/manual/en/function.password-hash.php) and [picking appropriate options](https://stackoverflow.com/a/48322039).

## Uninstallation

You have to regenerate all user passwords after uninstallation because we can't rehash without knowing the passwords in plain text.

## Frequently Asked Questions

### What have you done with the passwords?

In a nutshell:
```php
password_hash(
    hash_hmac('sha512', $userPassword, WP_PASSWORD_ARGON_TWO_PEPPER),
    PASSWORD_ARGON2I,
    WP_PASSWORD_ARGON_TWO_OPTIONS
);
```

Don't take my word for it. Read the [source code](./src)!

### I have installed this plugin. Does it mean my WordPress site is *unhackable*?

No website is *unhackable*.

To have a secure WordPress site, you have to keep all these up-to-date:
* WordPress core
* PHP
* this plugin
* all other WordPress themes and plugins
* everything on the server
* other security practices
* your mindset

### Did you reinvent the cryptographic functions?

Of course not! This plugin use PHP's native functions.

Repeat: Read the [source code](./src)!

### Pepper migration look great. Does it mean that I can keep as many pepper keys as I want?

In a sense, yes, you could do that. However, each pepper slows down the login process a little bit.

To test the worst case, log in with an incorrect password.

### What if my pepper is compromised?

1. Remove that pepper from `WP_PASSWORD_ARGON_TWO_PEPPER` and `WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS`
1. Regenerate all user passwords

### Is pepper-ing perfect?

No! Read [paragonie's explaination](https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence#pepper).

For those who can't stand with the drawbacks, use one of the [alternatives](#alternatives) instead.

### Is WordPress' phpass hasher or Bcrypt insecure?

Both WordPress' [phpass](http://openwall.com/phpass) hasher and Bcrypt are secure. There is no emergent reason to upgrade.

Learn more about the [reasons](https://roots.io/wordpress-password-security-follow-up/) about not using WordPress' default.

### Why use Argon2i over the others?

Argon2 password-based key derivation function is the winner of the [Password Hashing Competition](https://password-hashing.net) in July 2015, ranked better than Bcrypt and PBKDF2.

Argon2 comes with 3 different modes: Argon2d, Argon2i, Argon2id. Argon2i is the one for password hashing. See: https://crypto.stackexchange.com/a/49969

### Does this plugin has 72-character limit like Bcrypt?

No. Read [the test](https://github.com/TypistTech/wp-password-argon-two/blob/6ec33700ab80e700045063895459212dd52b30b7/tests/wpunit/PasswordLockTest.php#L46-L57).

### This plugin isn't on wp.org. Where can I give a :star::star::star::star::star: review?

Thanks!

Consider writing a blog post, submitting pull requests, [donating](https://typist.tech/donation/) or [hiring me](https://typist.tech/contact/) instead.

### This plugin isn't on wp.org. Where can I make a complaint?

To be honest, I don't care.

If you really want to share your 1-star review, send me an email - in the first paragraph, state how many times I have told you to read the plugin source code.

## Alternatives

* [paragonie/halite](https://github.com/paragonie/halite/blob/55706ac843d8ee90426b455ea28673cf85e4a1e2/doc/Examples/01-passwords.php)
* [paragonie/password_lock](https://github.com/paragonie/password_lock)
* [roots/wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt)
* [PHP Native password hash](https://wordpress.org/plugins/password-hash/)

## Support!

### Donate

Love WP Password Argon Two? Help me maintain it, a [donation here](https://typist.tech/donation/) can help with it.

### Why don't you hire me?

Ready to take freelance WordPress jobs. Contact me via the contact form [here](https://typist.tech/contact/) or, via email [info@typist.tech](mailto:info@typist.tech)

### Want to help in other way? Want to be a sponsor?

Contact: [Tang Rufus](mailto:tangrufus@gmail.com)

## Developing

To setup a developer workable version you should run these commands:

```bash
$ composer create-project --keep-vcs --no-install typisttech/wp-password-argon-two:dev-master
$ cd wp-password-argon-two
$ composer install
```

To run the tests:
``` bash
$ composer test
```

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/wp-password-argon-two/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change Log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email [wp-password-argon-two@typist.tech](mailto:wp-password-argon-two@typist.tech) instead of using the issue tracker.

## Credits

[WP Password Argon Two](https://github.com/TypistTech/wp-password-argon-two) is a [Typist Tech](https://typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus), freelance developer for [hire](https://typist.tech/contact/).

Full list of contributors can be found [here](https://github.com/TypistTech/wp-password-argon-two/graphs/contributors).

## License

The MIT License (MIT). Please see [License File](./LICENSE) for more information.
