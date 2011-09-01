<?php

  /**
   * Each Expression defines how a QuerySet is built and how a database
   * query is execution. Expressions can be used in conjunction with
   * ExpressionNode objects to create complex queries.
   */
  class Expression {

    /**
     * Defines whether this expression should be reversed with a NOT expression
     * 
     * @var boolean
     */
    protected $not = false;

    /**
     * What table field the operator is testing against
     *
     * @var string
     */
    protected $field = null;

    /**
     * Defines what SQL comparison operator should be used
     * (=, LIKE, IN, etc)
     *
     * @var string
     */
    protected $operator = null;

    /**
     * What value the field and operator are testing
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * The field of $field to search in where $field is a ForeignKey
     * 
     * @var string
     */
    private $subfield = null;

    /**
     * Create a new Expression object
     * 
     * @see Field::__callStatic()
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     */
    public function __construct($field, $operator, $value, $subfield = null) {
      
      // requires a value that is not null
      if (!$value) {
        throw new Exception("Cannot create Expression without test value.");
        return null;
      } else {
        $this->field = $field;
        // defualt operator: "exact", for field = 'value' queries
        $this->operator = (!$operator ? "exact" : $operator);
        $this->value = $value;
        $this->subfield = $subfield;
      }

    }
    
    /**
     * Returns this Expressions' operator
     *
     * @return string
     */
    public function getOperator() {
      return $this->operator;
    }

    /**
     * Returns this Expressions' field
     * 
     * @return string
     */
    public function getField() {
      return $this->field;
    }

    /**
     * Returns this Expressions' value
     *
     * @return mixed
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Returns this Expressions' subvalue
     *
     * @return string
     */
    public function getSubfield() {
      return $this->subfield;
    }

    /**
     * Returns whether or not this Expression should lead with
     * 'NOT';
     *
     * @return boolean
     */
    public function isNot() {
      return $this->not;
    }

    /**
     * Toggles the $not property of this Q depending on it's
     * existing value
     *
     * @see Q::$not
     */
    public function set_not() {
      $this->not = ($this->not ? false : true);
      return $this;
    }

  }

  /**
   * Set this Q or Expression as a 'NOT' query
   * Cannot be applied to an F field reference object
   */
  function _NOT_($expression) {    
    if ($expression instanceof F)
      throw new Exception("Cannot modify a field reference expression as NOT.");
    else
      $expression->set_not();
    
    return $expression;
  }

  class CallbackExpression extends Expression {

    public function __construct(QuerySet $query, $field) {
      $this->value = $query;
      $this->field = $field;
      $this->operator = "IN";
    }

  }
