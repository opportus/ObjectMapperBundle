# Object Mapper Bundle [![SensioLabsInsight](https://insight.sensiolabs.com/projects/648e763e-751a-413e-9327-89bb416f83a3/mini.png)](https://insight.sensiolabs.com/projects/648e763e-751a-413e-9327-89bb416f83a3)

This bundle integrates into your Symfony project [`opportus/object-mapper`](https://github.com/opportus/object-mapper), a library providing a flexible and extensible object mapping system.

Contributions are welcome.

To do:

- Introduce Annotations/YAML/XML object mapping configuration
- Introduce debug object mapping console command

## Index

- [Installation](#installation)
    - [Applications that use Symfony Flex](#applications-that-use-symfony-flex)
    - [Applications that do not use Symfony Flex](#applications-that-do-not-use-symfony-flex)
- [Mapping Objects](#mapping-objects)
    - [Automatic Mapping](#automatic-mapping)
    - [Manual Mapping](#manual-mapping)
    - [Static Mapping](#static-mapping)
    - [Target Point Value Assignment Event](#target-point-value-assignment-event)

## Installation

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper-bundle
```

### Applications that do not use Symfony Flex

#### Step 1 - Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
$ composer require opportus/object-mapper-bundle
```

#### Step 2 - Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Opportus\ObjectMapperBundle\OpportusObjectMapperBundle(),
        );

        // ...
    }

    // ...
}
```

## Mapping Objects

Mapping objects to objects is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) service's method:

```php
ObjectMapper::map($source, $target, ?MapInterface $map = null): ?object
```

**Parameters**

`$source` must be an object to map data from.

`$target` must be either an object or a fully qualified class name to map data to.

`$map` must be a *null* or an instance of *`MapInterface`*.

**Returns**

- A *null* if no route connecting source points with target points are found.
- An *object* which is the instantiated/updated target.

### Automatic Mapping

A basic example about how to *automatically* map one `User` to one `UserDto` and vice-versa:

```php
$objectMapper; // Opportus\ObjectMapper\ObjectMapper service instance...

class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername() : string
    {
        return $this->username;
    }
}

class UserDto
{
    public $username;
}

$user    = new User('foobar');
$userDto = new UserDto();

// Map the User instance to the UserDto instance...
$objectMapper->map($user, $userDto);

echo $userDto->username; // Outputs 'foobar'...

// Map back the UserDto instance to one new User...
$user = $objectMapper->map($userDto, 'User');

echo $user->getUsername(); // Outputs 'foobar'...
```

The automatic mapping allows to map seemlessly objects to objects.

Calling the `ObjectMapper::map()` method presented earlier, with its `$map` parameter set on `null` makes the method build then use a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default `PathFindingStrategy` behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected `SourcePoint` and `TargetPoint` compose then a [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) which is followed by the `ObjectMapper::map()`.

For the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php), a `TargetPoint` can be:

- A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A parameter of a public setter or a public constructor ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding `SourcePoint` can be:

- A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

#### Custom PathFindingStrategy

Sometime, the default `PathFindingStrategy` may not be the most appropriate behavior anymore. In this case, you can implement your own [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), and in order to make it guess the appropriate routes, reverse-engineer the classes passed as argument to the strategy's single method:

```php
PathFindingStrategyInterface::getRouteCollection(object $source, $target): RouteCollection;
```

**Parameters**

`$source` must be an object to map data from.

`$target` must be either an object or a fully qualified class name to map data to.


**Returns**

A [`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php) connecting the source's points with the tagets's points.

**Example**

```php
class MyPathFindingStrategy implements PathFindingStrategyInterface
{
    // ...

    public function getRouteCollection(object $source, $target): RouteCollection
    {
        // Custom algorithm...
    }

    // ...
}

// Pass to the map builder the strategy you want it to compose the map of...
$map = $objectMapper->getMapBuilder()->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyType(); // Outputs 'MyPathFindingStrategy'

// Use the map...
$user = $objectMapper->map($userDto, 'User', $map);

// ...
```

### Manual Mapping

A basic example about how to *manually* map one `User` to one `ContributorDto` and vice-versa:

```php
$objectMapper; // Opportus\ObjectMapper\ObjectMapper service instance...

class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

class ContributorDto
{
    public $name;
}

$user           = new User('foobar');
$contributorDto = new ContributorDto();

// Build the map...
$map = $objectMapper->getMapBuilder()
    ->prepareMap()
    ->addRoute('User::getUsername()', 'ContributorDto::$name')
    ->buildMap()
;

// Map the User instance to the ContributorDto instance...
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // Outputs 'foobar'...

// Build the map...
$map = $objectMapper->getMapBuilder()
    ->prepareMap()
    ->addRoute('ContributorDto::$name', 'User::__construct()::$username')
    ->buildMap()
;

// Map back the ContributorDto instance to one new User...
$user = $objectMapper->map($contributorDto, 'User', $map);

echo $user->getUsername(); // Outputs 'foobar'...
```

The manual mapping requires a little more effort than the automatic mapping but gives you unlimited control over which source point to map to which target point.

Building a map manually requires you to use the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. The `MapBuilder` is an immutable service which implement a fluent interface.

Building a map manually is actually nothing more than adding routes to a map via the following method:

```php
MapBuilder::addRoute(string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface
```

**Parameters**

The `$sourcePointFqn` parameter is a *string* representing the Fully Qualified Name of a source point which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'Class::$property'`
- A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'Class::method()'`

The `$targetPointFqn` parameter is a *string* representing the Fully Qualified Name of a target point which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'Class::$property'`
- A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'Class::method()::$parameter'`

**Returns**

The method returns a **new** instance of the `MapBuilder`.

### Static Mapping

Unavailable yet.

### Target Point Value Assignment Event

In some cases you may need to manipulate or filter the value from the source point before it is assigned to its corresponding target point. This value is available and manipulable via the [`TargetPointValueAssignmentEvent`](https://github.com/opportus/ObjectMapperBundle/blob/master/Event/TargetPointValueAssignmentEvent.php) dispatched on `ObjectMapperEvents::SET_NON_INSTANTIATED_TARGET_POINT_VALUE` and on `ObjectMapperEvents::SET_INSTANTIATED_TARGET_POINT_VALUE`.

**Example**

```php
use Opportus\ObjectMapperBundle\Event\TargetPointValueAssignmentEvent as Event;
use Doctrine\Common\Collections\Collection;

class ObjectMapperListener
{
    public function onSetTargetPointValue(Event $event)
    {
        $route = $event->getRoute();

        $sourcePoint = $route->getSourcePoint();
        $targetPoint = $route->getTargetPoint();

        if ('SourceClass' !== $sourcePoint->getClassFqn() && 'getCollection' !== $sourcePoint->getName()) {
            return;
        }

        if ('TargetClass' !== $targetPoint->getClassFqn() && 'getArray' !== $targetPoint->getName()) {
            return;
        }

        $value = $event->getTargetPointValueToAssign(); // Throws an exception because you have not assigned any value yet...

        if ($event->hasTargetPointValueToAssign()) {
            $value = $event->getTargetPointValueToAssign();

        } else {
            $value = $event->getSourcePointValue();
        }

        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        $event->setTargetPointValueToAssign($value);
    }
}
```
