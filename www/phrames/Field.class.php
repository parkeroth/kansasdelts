<?php

  require_once("Expression.class.php");

  class Field {

    /**
     * Returns a callback type expression for creating complex queries
     * where a certain field needs to be tested against a subquery, e.g.
     * 
     * Field::id__in(Field::__callback($somequery, "other_id"))
     * 
     * would create a statement where the ID field of one table is checked
     * if it exists in the other_id field of another query
     * 
     * This is used for creating complex callbacks in ManyToManyField
     * retrieval
     * 
     * @param QuerySet $query
     * @param string $field
     * @return CallbackExpression
     */
    public static function __callback(QuerySet $query, $field) {
      return new CallbackExpression($query, $field);
    }

    /**
     * Create a new Expression for QuerySet objects
     *
     * @param string $func
     * @param string|Expression $value
     * @return Expression
     */
    public static function __callStatic($func, $value) {
      $num_split = substr_count($func, "__");
      if ($num_split == 2) {
        // subfield query using foreign key, for example
        // user__username__contains("a")
        list($field, $subfield, $operator) = split("__", $func);
      } elseif ($num_split == 1) {
        // standard query using fields from an object's own table
        // e.g. username__contains("a")
        list($field, $operator) = split("__", $func);
        $subfield = null;
      } else {
        // can't have an expression that goes deeper than this
        throw new Exception(sprintf("Invalid Expression: %s", $func));
      }
      $value = $value[0];

      /**
       * if the provided value is an expression itself, this is a complex
       * qurey that requires use of an F expression node (for self-referencing
       * fields), e.g:
       *
       * Field::price__gt(Field::cost__times(2))
       *
       * Should produce a query like:
       *
       * price > cost * 2
       *
       * where 'Field::cost__times(2)' is the subexpression
       */
      if (in_array(strtoupper($operator), array("TIMES", "DIVIDED_BY", "PLUS", "MINUS"))) {

        switch(strtoupper($operator)) {
          case "TIMES":
            $operator = "*";
            break;
          case "DIVIDED_BY":
            $operator = "/";
            break;
          case "PLUS":
            $operator = "+";
            break;
          case "MINUS":
            $operator = "-";
            break;
        }

        if ($value instanceof F) {
          $f = $value->add($field, $operator);
        } else {
          $f = new F($field);
          $f = $f->add($value, $operator);
        }

        return $f;
      } else {
        return new Expression($field, $operator, $value, $subfield);
      }
    }

  }

  abstract class ExternalField extends Field {

    protected $class = "";

    public function __construct($class) {
      if (!class_exists($class))
        throw new Exception("Could not create ForeignKey relationship:" .
                " class {$class} does not exist.");
      $this->class = $class;
    }

    public function getClass() {
      return $this->class;
    }

  }

  class ForeignKey extends ExternalField {
    
    public function getClass() {
      return $this->class;
    }

    public function getObject($id) {
      return new $this->class((int) $id);
    }

    public function getTable() {
      $class = $this->class;
      return $class::table_name();
    }

    public function createJoin($class, $field) {
      return sprintf("LEFT JOIN %s %s ON %s.id = %s.%s",
              $this->getTable(),
              $field,
              $field,
              strtolower($class),
              $field);
    }

  }

  class ManyToManyField extends ExternalField {

    private $through = "";

    public function __construct($class, $through) {
      parent::__construct($class);
      if (!class_exists($through))
        throw new Exception("Could not create ManyToManyField relationship:" .
                " class {$through} does not exist.");
      $this->through = $through;
    }

    public function getThroughClass() {
      return $this->through;
    }

  }
  
  class OneToManyField extends ExternalField {
    
  }
