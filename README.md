# Dependency Resolver

[![Build Status](https://travis-ci.org/mariano-dagostino/DependencyResolver.svg?branch=master)](https://travis-ci.org/mariano-dagostino/DependencyResolver)

This package allows to define a set of generic components that depends on other
components. The DependencyResolver will then define the order of loading of
those components.

## Basic usage

```php

use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->component('ITEM 1')->requires('ITEM 3', 'ITEM 4') // Item 1 requires item 3 and 4.
  ->component('ITEM 2')->requires('ITEM 1')           // Item 2 requires item 1.
  ->component('ITEM 3')                               // Item 3 doesn't have dependencies.
  ->component('ITEM 4');          ;                   // Item 4 doesn't have dependencies.

$ordered = $resolver->resolveDependencies();
print_r($ordered);
// Prints:
// ITEM 3
// ITEM 4
// ITEM 1
// ITEM 2
```

## Features.

### Circular dependency detection.

Example:

```php
use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->component('A')->requires('B')
  ->component('B')->requires('A');

$ordered = $resolver->resolveDependencies();

// Trow Exception: "Circular dependency detected"
```


### Check that all the components have been defined.

Example:

```php
use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->component('A')->requires('B', 'C')
  ->component('B'));

  $ordered = $resolver->resolveDependencies();
  // Trow Exception: "There is a component not defined: C"
```
