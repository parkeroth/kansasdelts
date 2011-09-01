<?php
echo 'I';
  require_once("Database.class.php");
echo 'II';
  class Object {

    /**
     * @var int
     */
    private $id = 0;

    /**
     * Database fields for this object
     *
     * @var array
     */
    private $fields = array();

    /**
     * Fields that have been modified
     *
     * @var array
     */
    private $modified_fields = array();

    /**
     * @var boolean
     */
    private $loaded = false;

    /**
     * Create new Object
     *
     * @param int $id
     */
    public function __construct($id = null) {
      if (method_exists($this, "init"))
              $this->init();
      if ($id) $this->id = $id;
    }

    /**
     * Get a fields value from this Object
     *
     * $obj->field
     *
     * @see getField()
     *
     * @param string $field
     * @return mixed
     */
    public function __get($field) {
      return $this->getField($field);
    }

    /**
     * Update the value of a field
     *
     * $obj->field = value
     *
     * @see setField()
     *
     * @param string $field
     * @param mixed $value
     */
    public function __set($field, $value) {
      $this->setField($field, $value);
    }

    /**
     * Handles retrieving fields
     *
     * @param string $field
     * @return mixed
     */
    private function getField($field) {
      if ($field == "id") {
        return $this->id;
      } else {
        if (!$this->loaded)
                $this->load();

        if (isset($this->fields[$field]) || $this->fields[$field] === null)
                return $this->fields[$field];
        else
          throw new Exception("Property '{$field}' does not exist in " .
            get_class($this));
      }
    }

    /**
     * Handles modifying fields
     *
     * @param string $field
     * @param mixed $value
     */
    private function setField($field, $value) {
      if ($field == "id")
        throw new Exception("Cannot change ID field.");

      $this->fields[$field] = $value;
      if (!in_array($field, $this->modified_fields))
              $this->modified_fields[] = $field;

    }

    public function getAllFields() {
      if (!$this->loaded) $this->load();
      return $this->fields;
    }

    /**
     * Load data into this Object
     */
    public function load() {
      if (!$this->loaded && $this->id) {
        foreach(Database::objectLoad($this) as $field => $value)
          // only load fields that haven't been modified yet
          if (!isset($this->modified_fields[$field])
                  && $field != "id")
                  $this->fields[$field] = $value;
        $this->loaded = true;
      }
    }

    /**
     * Save changes of this Object to the database
     * @return Object
     */
    public function save() {
      // compile fields to save(only those that are modified)
      if (sizeof($this->modified_fields)) {
        $fields = array();
        foreach($this->modified_fields as $field) {
          // new value
          $value = $this->fields[$field];
          if ($value instanceof Object) {
            // new value is an object
            // need to save any changes made to that object
            $value->save();
            $value = $value->id;
            $this->fields[$field] = $value;
          }
          $fields[$field] = $value;
        }
        $this->id = Database::objectSave($this, $fields);
        $this->modified_fields = array();
      }
      return $this;
    }

    /**
     * Remove this Object from the database
     */
    public function delete() {
      Database::objectDelete($this);
      // return new/empty Object of this same class
      $class = get_class($this);
      return new $class();
    }

  }
