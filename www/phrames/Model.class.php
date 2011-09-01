<?php
echo 'a';
  require_once("QuerySet.class.php");
  require_once("Expression.class.php");
  require_once("Object.class.php");echo 'b';
  require_once("Field.class.php");
  require_once("Manager.class.php");

  class Model extends Object {

    /**
     * Manually define SQL table name
     */
    const table_name = "";

    public function __construct($init = null) {
      if (is_int($init)) {

        // has pre-determined id for this object
        parent::__construct($init);

      } elseif (is_array($init)) {

        /**
         * initialized with an array of format
         * array("field" => value, {...})
         * for pre-initializing an object with values
         */
        parent::__construct();
        foreach($init as $field => $value)
          $this->__set($field, $value);

      } else {
        // no inital value, just return an empty object
        parent::__construct();
      }
    }

    /**
     * Return this object as a string
     *
     * @return string
     */
    public function __toString() {
      return get_called_class();
    }

    /**
     * Sets the value of a particular field, e.g.
     * $obj->some_field = 'value';
     *
     * @param string $field
     * @param mixed $value
     * @return Object
     */
    public function __set($field, $value) {
      // type of field they are trying to assign
      $type = self::get_field_type($field);

      if ($type instanceof ForeignKey) {
        // trying to set value of a foreign key field

        $class_of_value = $type->getClass();
        if ($value instanceof $class_of_value || is_int($value)) {
          // allow to assign integer value of an id or a proper
          // object of the type which the foreign key represents
          return parent::__set($field, $value);
        } elseif (!($value instanceof $class_of_value)) {
          // trying to assign an object to this field which is
          // not of the same type the foreign key represents
          throw new Exception(sprintf("Cannot assign a %s object to " .
                "ForeignKey field of type %s of a %s",
                get_class($value),
                $type->getClass(),
                get_class($this))
              );
        } else {
          throw new Exception(sprintf("Invalid field assignment value: " .
                "value of %s field must be of type %s or an integer",
                $field,
                $type->getClass())
              );
        }
      } elseif ($type instanceof ManyToManyField) {
        // TODO: ManyToManyField setting

      } else {
        return parent::__set($field, $value);
      }
    }

    /**
     * Retrieves the value of a particular field, e.g.
     * print $obj->some_field
     *
     * @param string $field
     * @return mixed
     */
    public function __get($field) {

      // check if field is defined as a specific type
      $type = $this->get_field_type($field);
      $find_field = null;

      if ($type instanceof OneToManyField) {

        $find = $type->getClass();

        foreach($find::get_field_types() as $field => $type) {
          if ($find::get_field_type($field) instanceof ForeignKey &&
                  $type->getClass() == get_class($this)) {
            $find_field = $field;
            break;
          }
        }

        if (!$find_field) {
          throw new Exception("Could not find matching " . get_class($this) .
                  " field reference in {$find}");
        } else {
          return $find::objects()->filter(
                  call_user_func("Field::{$find_field}__exact", $this->id));
        }

      } elseif ($type instanceof ManyToManyField) {
        // Example outlined based on finding the Toppings of a Pizza
        // through a PizzaTopping class where 'toppings' is a ManyToManyField
        // defined as Topping:PizzaTopping
        // e.g. Pizza::get(1)->toppings

        $find = $type->getClass(); // class we want to return, e.g. Topping
        $through = $type->getThroughClass(); // the "membership" class, e.g. PizzaTopping

        $find_field = $through_field = null;

        foreach($through::get_field_types() as $field => $type) {
          if ($type->getClass() == $find
                  && $through::get_field_type($field) instanceof ForeignKey
                  && !$find_field)
            $find_field = $field; // the field of PizzaTopping we want to return
                                  // (the ID list of toppings)
          elseif ($type->getClass() == get_class($this)
                  && $through::get_field_type($field) instanceof ForeignKey
                  && !$through_field)
            $through_field = $field; // the field of PizzaTopping we are searching
                                     // by (reference to Pizza)
        }

        if (!$find_field) {
          throw new Exception("Could not find matching {$find} ".
            "field reference in {$through}");
        } elseif (!$through_field) {
          throw new Exception("Could not find matching " . get_class($this) .
            "field reference in {$through}");
        } else {
          return $find::objects()->filter(Field::__callback(
                $through::objects()->filter(call_user_func(
                    "Field::{$through_field}__exact",
                    $this->id)), $find_field));
        }
      } elseif ($type instanceof ForeignKey) {
        $value = parent::__get($field);
        if ($value instanceof Object)
          return $value;
        else
          return $type->getObject(parent::__get($field));
      } else {
        return parent::__get($field);
      }

    }

    /**
     * Returns a QuerySet Manager object for creating queries
     *
     * @return Manager
     */
    public static function objects() {
      // THE FOLLOWING IS EXPERIMENTAL AND LIKELY VERY BAD PRACTICE

      $functions = array();
      $ignore = array();
      $class = get_called_class();

      // define functions through ForeignKey fields
      foreach($class::get_field_types() as $field => $type) {
        if ($type instanceof ForeignKey) {
          $ignore[] = $field;
          foreach(Database::modelGetFields($type->getClass()) as $s_field) {
            foreach(QuerySet::$operators as $operator) {
              $functions[] = "{$field}__{$s_field}__" . strtolower($operator);
            }
          }
        }
      }

      // define base functions
      foreach(Database::modelGetFields($class) as $field)
        if (!in_array($field, $ignore))
          foreach(QuerySet::$operators as $operator)
            $functions[] = "{$field}__" . strtolower($operator);

      foreach($functions as $function)
        if (!function_exists($function))
          eval("
            function {$function}(\$value) {
              return Field::{$function}(\$value);
            }
          ");

      return new Manager(get_called_class());
    }

    /**
     * Determines the SQL table name used by this object.
     * It can be manually defined by the table_name constant
     * or be automatically determined by lowercasing and
     * pluralizing the class name.
     *
     * i.e. "User" becomes "users", etc
     *
     * @return string
     */
    public static function table_name() {

      $called_class = get_called_class();
      $default = eval("return {$called_class}::table_name;");

      if ($default) {
        // use manual table name
        return $default;
      } else {
        $table = strtolower($called_class);
        if (substr($table, -1) == "y") {
          // ends with Y ('bunny' should become 'bunnies')
          $table = substr_replace($table, "ies", -1);
        } elseif (substr($table, -1) == "s") {
          // ends with S ('class' should become 'classes')
          $table = substr_replace($table, "es", -1);
        } else {
          // just append an s
          $table .= "s";
        }
        return $table;
      }

    }

    /**
     * Returns the field type of a custom-defined field, such as
     * ManyToManyFields or OneToManyFields
     * Returns false if not definied (a regular database field)
     *
     * @param string $field
     * @return mixed
     */
    public static function get_field_type($field) {
      $type = @constant(get_called_class() . "::{$field}");
      if (strpos($type, ":") !== false) {
        // manytomany, "FindClass:ThroughClass"
        $classes = split(":", $type, 2);
        if ($classes[0] && $classes[1])
          return new ManyToManyField($classes[0], $classes[1]);
        else
          return new OneToManyField($classes[0]);
      } elseif(class_exists($type)) {
        // foreignkey
        return new ForeignKey($type);
      } else {
        // just a regular field
        return false;
      }
    }

    /**
     * Returns an array of all fields types specifically defined
     * under a certain model
     *
     * @return array
     */
    public static function get_field_types() {
      $class = get_called_class();
      $refl = new ReflectionClass($class);
      $types = array();
      foreach($refl->getConstants() as $field => $val) {
        $type = $class::get_field_type($field);
        if ($type !== false) $types[$field] = $type;
      }
      return $types;
    }

    /**
     * Wrapper for the static get() method to return all items
     *
     * @return QuerySet
     */
    public static function all() {
      return static::get();
    }

    public static function get_or_create($args) {

    }

    public static function latest($fields) {

    }

  }
