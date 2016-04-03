# php-calendly
[![Latest Version](https://img.shields.io/github/release/tylercd100/php-calendly.svg?style=flat-square)](https://github.com/tylercd100/php-calendly/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/tylercd100/php-calendly.svg?branch=master)](https://travis-ci.org/tylercd100/php-calendly)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tylercd100/php-calendly/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/php-calendly/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tylercd100/php-calendly/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/php-calendly/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187)
[![Total Downloads](https://img.shields.io/packagist/dt/tylercd100/php-calendly.svg?style=flat-square)](https://packagist.org/packages/tylercd100/php-calendly)

This package will let you easily register webhooks with the [Calendly API](http://developer.calendly.com/)

## Installation

Install via [composer](https://getcomposer.org/) - In the terminal:
```bash
composer require tylercd100/php-calendly
```

## Usage

Register a webhook for Invitee Created
```php
use Tylercd100\Calendly\Calendly;
$c = new Calendly("Your API Token");
$response = $c->registerInviteeCreated("http://foo.com/bar/calendly");
/* When successful it will return:
[
    "id" => 1234
]
 */
```

Register a webhook for Invitee Canceled
```php
use Tylercd100\Calendly\Calendly;
$c = new Calendly("Your API Token");
$response = $c->registerInviteeCanceled("http://foo.com/bar/calendly");
/* When successful it will return:
[
    "id" => 1234
]
 */
```

Unregister a webhook
```php
use Tylercd100\Calendly\Calendly;
$c = new Calendly("Your API Token");
$idOfWebhook = 1234;
$response = $c->unregistered($idOfWebhook);
/* When successful it will return:
null
 */
```

