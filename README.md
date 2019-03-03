# Php DB Helper Library

[![Latest Version on Packagist](https://img.shields.io/badge/Version-1.0.0-blue.svg)](https://packagist.org/packages/moom/db)
[![Build Status](https://scrutinizer-ci.com/g/moustafaomar/DB/badges/build.png?b=master)](https://scrutinizer-ci.com/g/moustafaomar/DB/badges/build.png?b=master)
[![Quality Score](https://scrutinizer-ci.com/g/moustafaomar/DB/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/moom/db)
[![Total Downloads](https://img.shields.io/packagist/dt/moom/db.svg?style=flat-square)](https://packagist.org/packages/moom/db)
[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)

This package enables you to perform CRUD actions on DB without typing queries.

## Installation

You can install the package via composer:

```bash
composer require moom/db --prefer-dist
```

## Usage

``` php
<?php
require_once 'vendor/autoload.php';

use MOOM\DB;

$data = DB::fetchAll('users',[]);


var_dump($data);
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email moustafaomar200@gmail.com instead of using the issue tracker.

## Credits

- [Mostafa Omar](https://github.com/mo)
- [All Contributors](../../contributors)

## License

The DB package is open-source software licensed under the MIT license.