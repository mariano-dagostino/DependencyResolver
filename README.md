# Dependency Resolver

[![Build Status](https://travis-ci.org/mariano-dagostino/DependencyResolver.svg?branch=master)](https://travis-ci.org/mariano-dagostino/DependencyResolver)

This package allows to define a set of generic components that depends on other components. The DependencyResolver will then define the order of loading of those components.

## Basic usage

```php

use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->addComponent('ITEM 1', array('ITEM 3', 'ITEM 4')) // Item 1 requires first 3 and 4.
  ->addComponent('ITEM 2', array('ITEM 1'))           // Item 2 requires first 1.
  ->addComponent('ITEM 3')                            // Item 3 doesn't have dependencies.
  ->addComponent('ITEM 4');                           // Item 4 doesn't have dependencies.

$ordered = $resolver->resolveDependencies();
print_r($ordered);
// Prints:
// ITEM 3
// ITEM 4
// ITEM 1
// ITEM 2
```

## Features

### Circular dependency detection.

Example:

```php
use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->addComponent('A', array('B'))
  ->addComponent('B', array('A'));

$ordered = $resolver->resolveDependencies();
  
// Trow Exception: "Circular dependency detected"
```


### Check that all the components have been defined.

Example:

```php
use mdagostino\DependencyResolver\DependencyResolver;

$resolver = new DependencyResolver();
$resolver
  ->addComponent('A', array('B', 'C'))
  ->addComponent('B');

  $ordered = $resolver->resolveDependencies();
  // Trow Exception: "There is a component not defined: C"
```
