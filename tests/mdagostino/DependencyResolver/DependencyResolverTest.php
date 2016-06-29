<?php

namespace mdagostino\DependencyResolver;

class DependencyResolverTest extends \PHPUnit_Framework_TestCase {

  public function testBasicBehaviour() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('A')
      ->component('B')->requires('A')
      ->component('C')->requires('B');

    $this->assertEquals($resolver->resolveDependencies(),
                  array('A', 'B', 'C'));
  }


  public function testSuffle() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('E')->requires('B', 'C')
      ->component('A')
      ->component('B')
      ->component('C')->requires('A', 'D')
      ->component('D');

    $this->assertEquals($resolver->resolveDependencies(),
                  array('B', 'A', 'D', 'C', 'E'));
  }


  /**
   * @expectedException Exception
   * @expectedExceptionMessage Circular dependency detected
   */
  public function testCircularDependency() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('A')->requires('C')
      ->component('B')->requires('A')
      ->component('C')->requires('B');

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage There is a component not defined: C
   */
  public function testIdentifierMissing() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('A')->requires('C')
      ->component('B')->requires('C');

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage The identifier must be an scalar (example: string, int)
   */
  public function testInvalidIdentifier() {
    $resolver = new DependencyResolver();
    $resolver
      ->component(array('A'))->requires('C')
      ->component('B')->requires('C');

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Dependencies of A must be scalar.
   */
  public function testInvalidDependencies() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('A')->requires(['B'])
      ->component('B');

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Dependencies of A must be scalar.
   */
  public function testNoScalarDependencies() {
    $resolver = new DependencyResolver();
    $resolver
      ->component('A')->requires('B', array('C'))
      ->component('B');

    $resolver->resolveDependencies();
  }

  public function testComplexTree() {
    $resolver = new DependencyResolver();
    $resolver->component('Component: 0');
    $components = array();
    $components[] = array(
     'id' => 'Component: 0',
     'dependencies' => array(),
    );

    // Generate 1000 components, create some ramdom dependencies
    // on the previous generated components.
    for ($i = 1; $i < 1000; $i++) {
      $dependencies = array();
      // Variable number of dependencies
      for ($z = 0; $z < rand() % 10; $z++) {
        $dependencies[] = 'Component: ' . (rand() % $i);
      }
      $components[] = array(
        'id' => 'Component: ' . $i,
        'dependencies' => $dependencies,
      );
      $resolver->component('Component: ' . $i);
      foreach ($dependencies as $dependency) {
        $resolver->requires($dependency);
      }
    }

    $ordered = $resolver->resolveDependencies();
    foreach ($components as $component) {
      $this->assertTrue(in_array($component['id'], $ordered));
    }
    $this->assertEquals(count($ordered), count($components));
  }
}
