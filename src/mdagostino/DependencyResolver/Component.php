<?php


namespace mdagostino\DependencyResolver;


class Component {

  protected $id;
  protected $dependencies;
  protected $required_by;

  public function __construct($id, $dependencies = array()) {
    $this->id = $id;
    $this->dependencies = array();

    if (!is_scalar($id)) {
      throw new \Exception("The identifier must be an scalar (example: string, int)");
    }
    if (!is_array($dependencies)) {
      throw new \Exception("Dependencies for " . $id . " must be an array.");
    }

    foreach ($dependencies as $dependency) {
      if (!is_scalar($dependency)) {
        throw new \Exception("Dependencies of " . $id . " must be an array of scalars.");
      }
      $this->dependencies[$dependency] = TRUE;
    }
    $this->required_by = array();
  }

  public function id() {
    return $this->id;
  }

  public function dependencies() {
    return array_keys($this->dependencies);
  }

  public function requiredBy() {
    return array_keys($this->required_by);
  }

  public function addDependency($dependency) {
    $this->dependencies[$dependency] = TRUE;
  }

  public function reset() {
    foreach (array_keys($this->dependencies) as $id){
      $this->dependencies[$id] = TRUE;
    }
    $this->required_by = array();
  }

  public function addRequiredBy($required_by) {
    $this->required_by[$required_by] = TRUE;
  }

  public function dependencyProcesed($dependency){
    $this->dependencies[$dependency] = FALSE;
  }

  public function pendingDependenciesCount() {
    $count = 0;
    foreach ($this->dependencies as $value) {
      if (!empty($value)) {
        $count++;
      }
    }
    return $count;
  }

}
