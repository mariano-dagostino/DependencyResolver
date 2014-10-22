<?php

namespace mdagostino\DependencyResolver;

class DependencyResolverTest extends \PHPUnit_Framework_TestCase {

  public function testBasicBehaviour() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent('A')
      ->addComponent('B', array('A'))
      ->addComponent('C', array('B'));

    $this->assertEquals($resolver->resolveDependencies(),
                  array('A', 'B', 'C'));
  }


  public function testSuffle() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent('E', array('B', 'C'))
      ->addComponent('A')
      ->addComponent('B')
      ->addComponent('C', array('A', 'D'))
      ->addComponent('D');

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
      ->addComponent('A', array('C'))
      ->addComponent('B', array('A'))
      ->addComponent('C', array('B'));

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage There is a component not defined: C 
   */
  public function testIdentifierMissing() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent('A', array('C'))
      ->addComponent('B', array('C'));

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage The identifier must be an scalar (example: string, int)
   */
  public function testInvalidIdentifier() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent(array('A'), array('C'))
      ->addComponent('B', array('C'));

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Dependencies for A must be an array.
   */
  public function testInvalidDependencies() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent('A', 'B')
      ->addComponent('B');

    $resolver->resolveDependencies();
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Dependencies of A must be an array of scalars.
   */
  public function testNoScalarDependencies() {
    $resolver = new DependencyResolver();
    $resolver
      ->addComponent('A', array('B', array('C')))
      ->addComponent('B');

    $resolver->resolveDependencies();
  }

  public function testComplexTree() {
    $resolver = new DependencyResolver();
    $resolver->addComponent('Component: 0');
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
      $resolver->addComponent('Component: ' . $i, $dependencies);
    }

    $ordered = $resolver->resolveDependencies();
    foreach ($components as $component) {
      $this->assertTrue(in_array($component['id'], $ordered));
    }
    $this->assertEquals(count($ordered), count($components));
  }
}
