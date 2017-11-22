# Marshal

[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](https://github.com/Kingson-de/marshal-serializer/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/Kingson-de/marshal-serializer.svg?branch=master)](https://travis-ci.org/Kingson-de/marshal-serializer)
[![Code Coverage](https://scrutinizer-ci.com/g/Kingson-de/marshal-serializer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Kingson-de/marshal-serializer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Kingson-de/marshal-serializer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Kingson-de/marshal-serializer/?branch=master)

## Introduction

Marshal is [serializing](https://en.wikipedia.org/wiki/Serialization) / [marshalling](https://en.wikipedia.org/wiki/Marshalling_(computer_science)) data structures to a format that can be used to build messages for transferring data through the wires.

Especially useful for building the raw response for web services which then can be formatted to JSON for example.

## Installation

Easiest way to install the library is via composer:
```
composer require kingson-de/marshal-serializer
```

The following PHP versions are supported:
* PHP 7.0
* PHP 7.1
* PHP 7.2

## Execute tests
Just run:
```
composer test
```

Or without code coverage:
```
composer quicktest
```

## Usage

### Mappers

The first thing to do is to create a mapper that takes care of mapping your entities / models to the correct format.

You always need to inherit from the abstract Mapper class and implement a `map` function with your type hinting.

```php

use KingsonDe\Marshal\Mapper;

class UserMapper extends Mapper {

    public function map(User $user) {
        return [
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'birthday' => $user->getBirthday()->format('Y-m-d'),
            'followers => count($user->getFollowers()),
        ];
    }
}
```

### Data Structures

Next step is to create the desired data structure either being an item/object or a collection.

#### Item/Object 
```php
use KingsonDe\Marshal\Data\Item;

$item = new Item(new UserMapper(), $user);
```

#### Collection
```php
use KingsonDe\Marshal\Data\Collection;

$userCollection = [$user1, $user2, $user3];
$item           = new Collection(new UserMapper(), $userCollection);
```

### Serializing / Marshalling

The final step is to map the data structures to the actual format.

```php
use KingsonDe\Marshal\Marshal;

$data = Marshal::serialize($item);
```

### Symfony Example
```php
use KingsonDe\Marshal\Data\Item;
use KingsonDe\Marshal\Marshal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller {

    public function indexAction(User $user) {
        $item = new Item(new UserMapper(), $user);
        $data = Marshal::serialize($item);
        
        return new JsonResponse($data);
    }
}
```

### Advanced Mappers

#### Nested Data Structures

Mappers can even include other mappers with different data structures.

Therefore you can use `item` or `collection` function from the abstract Mapper class.

```php

use KingsonDe\Marshal\Mapper;

class UserMapper extends Mapper {

    public function map(User $user) {
        return [
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'birthday' => $user->getBirthday()->format('Y-m-d'),
            'followers => $this->collection(new FollowerMapper(), $user->getFollowers),
            'location' => $this->item(new LocationMapper(), $user->getLocation()),
        ];
    }
}
```

#### Pass as many parameters as you like to the mappers

```php
use KingsonDe\Marshal\Data\Item;
use KingsonDe\Marshal\Marshal;

$item = new Item(new UserMapper(), $user, $followers, $location);
$data = Marshal::serialize($item);
```

```php

use KingsonDe\Marshal\Mapper;

class UserMapper extends Mapper {

    public function map(User $user, FollowerCollection $followers, Location $location) {
        return [
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'birthday' => $user->getBirthday()->format('Y-m-d'),
            'followers => $this->collection(new FollowerMapper(), $followers),
            'location' => $this->item(new LocationMapper(), $location),
        ];
    }
}
```

For collections the first parameter passed is the one which Marshal will use for iterating.

All other parameters in a collection will stay as it is.

For items/objects all parameters retain.

## License

This project is released under the terms of the [Apache 2.0 license](https://github.com/Kingson-de/marshal-serializer/blob/master/LICENSE).
