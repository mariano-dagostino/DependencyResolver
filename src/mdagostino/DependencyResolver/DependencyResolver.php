<?php

namespace mdagostino\DependencyResolver;

class DependencyResolver {

  protected $components;

  public function __construct() {
    $this->components = array();
  }

  public function component($id) {
    $component = new Component($id);

    if (empty($this->components[$component->id()])) {
      $this->components[$component->id()] = $component;
    }
    $this->lastComponent = $component;
    return $this;
  }

  public function requires() {
    $dependencies = func_get_args();
    if (empty($dependencies)) {
      return $this;
    }
    $component = $this->lastComponent;

    foreach ($dependencies as $dependency) {
      $component->addDependency($dependency);
    }

    // Set all the dependencies to FALSE to detect missing dependencies later.
    foreach ($component->dependencies() as $dependency) {
      if (empty($this->components[$dependency])) {
        $this->components[$dependency] = FALSE;
      }
    }
    return $this;
  }

  public function resolveDependencies() {

    // Check if all the components are defined.
    foreach ($this->components as $key => $component) {
      if ($component === FALSE) {
        throw new \Exception("There is a component not defined: " . $key);
      }
      else {
        $component->reset();
      }
    }

    // Create the reverse dependency references.
    foreach ($this->components as $key => $component) {
      foreach ($component->dependencies() as $dependency) {
        $this->components[$dependency]->addRequiredBy($component->id());
      }
    }

    $ordered_components = array();
    $components = $this->components;

    while (count($components) > 0) {
      // In each iteration, there should be new components will its dependencies
      // already proccesed.
      $new_component_proccesed = FALSE;
      $to_delete = array();
      foreach ($components as $id => $component) {
        // If all the dependencies has been already proccesed. This component
        // is ready to be proccessed too.
        if ($component->pendingDependenciesCount() == 0) {
          $new_component_proccesed = TRUE;
          $ordered_components[] = $component->id();
          $to_delete[] = $component->id();
        }
      }
      // Let know components that require the current component that it has been
      // proccesed
      foreach ($to_delete as $id) {
        foreach ($components[$id]->requiredBy() as $required_by) {
          $components[$required_by]->dependencyProcesed($id);
        }
        unset($components[$id]);
      }

      if (!$new_component_proccesed) {
        throw new \Exception("Circular dependency detected");
      }
    }

    return $ordered_components;
  }

}
