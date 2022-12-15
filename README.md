# nested-accessor

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/smoren/nested-accessor)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Smoren/nested-accessor-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Smoren/nested-accessor-php/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Smoren/nested-accessor-php/badge.svg?branch=master)](https://coveralls.io/github/Smoren/nested-accessor-php?branch=master)
![Build and test](https://github.com/Smoren/nested-accessor-php/actions/workflows/test_master.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Accessor for getting and setting values of nested data structures (arrays or objects).

### How to install to your project
```
composer require smoren/nested-accessor
```

### Unit testing
```
composer install
composer test-init
composer test
```

### Usage

#### NestedAccessor

```php

use Smoren\NestedAccessor\Components\NestedAccessor;

$source = [
    'data' => [
        'id' => 1,
        'name' => 'Countries classifier',
        'extra' => null,
        'country_names' => ['Russia', 'Belarus'],
    ],
    'countries' => [
        [
            'name' => 'Russia',
            'cities' => [
                [
                    'name' => 'Moscow',
                    'extra' => [
                        'codes' => [
                            ['value' => 7495],
                            ['value' => 7499],
                        ],
                    ],
                ],
                [
                    'name' => 'Petersburg',
                    'extra' => [
                        'codes' => [
                            ['value' => 7812],
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Belarus',
            'cities' => [
                [
                    'name' => 'Minsk',
                    'extra' => [
                        'codes' => [
                            ['value' => 375017],
                        ],
                    ],
                ],
            ],
        ],
    ]
];

$accessor = new NestedAccessor($input);

echo $accessor->get('data.name'); // 'Countries classifier'
print_r($accessor->get('countries.name')); // ['Russia', 'Belarus']
print_r($accessor->get('countries.cities.name')); // ['Moscow', 'Petersburg', 'Minsk']
print_r($accessor->get('countries.cities.extra.codes.value')); // [7495, 7499, 7812, 375017]

var_dump($accessor->isset('data.name')); // true
var_dump($accessor->isset('data.extra')); // false
var_dump($accessor->isset('this.path.not.exist')); // false

var_dump($accessor->exist('data.name')); // true
var_dump($accessor->exist('data.extra')); // true
var_dump($accessor->exist('this.path.not.exist')); // false

$accessor->set('data.name', 'New name');
echo $accessor->get('data.name'); // 'New name'

$accessor->append('data.country_names', 'Mexico');
echo $accessor->get('data.country_names'); // ['Russia', 'Belarus', 'Mexico']

$accessor->delete('data.name');
var_dump($accessor->exist('data.name')); // false
```
