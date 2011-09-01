<?php
  /**
   * ExpressionNodes act as a linked-list where each
   * node can have a child node that is another ExpressionNode.
   * This link of nodes can be used to create complex queries.
   */
  abstract class ExpressionNode {
    /**
     * Attached ExpressionNode objects with operators
     *
     * @var ExpressionNode
     */
    protected $child;

    /**
     * Defines what field of the database should be
     * referenced
     *
     * @var string
     */
    protected $field;

    /**
     * The modified for this field reference
     *
     * @var char
     */
    protected $modifier;

    /**
     * Construct a new ExpressionNode
     *
     * @param mixed $field
     */
    public function __construct($field) {
      $this->field = $field;
    }

    /**
     * Store child element of linked list
     *
     * @param ExpressionNode $child
     */
    protected function setChild($child) {
      $this->child = $child;
    }

    /**
     * Store the modifier (either &, | for Q, or
     * +, -, *, / for F)
     *
     * @param char $modifier
     */
    protected function setModifier($modifier) {
      $this->modifier .= $modifier;
    }

    /**
     * Create a linked list of F objects
     *
     * @param F|int $f
     * @param char $modifier
     * @return F
     */
    public function add($s, $modifier) {
      $s->setModifier($modifier);
      $s->setChild($this);
      return $s;
    }

  }

  /**
   * TODO: Fix more complicated statements such as
   * F('make') + F('model') * 3
   */
  class F extends ExpressionNode {

    /**
     * Add new object as linked list element along with it's
     * math operator
     *
     * @param F $f
     * @param char $modifier
     * @return F
     */
    public function add($f, $modifier) {
      if (!($f instanceof F))
        $f = new F($f);

      if ($this->child instanceof F) {
        $this->child->setChild($f);
        $this->child->setModifier($modifier);
        return $this;
      } else {
        return parent::add($f, $modifier);
      }
    }

    /**
     * Create the partial SQL query that this node represents
     *
     * @return array Returns both the created "where" statement and any joins required
     */
    public function create_where($query) {      
      $joins = array();
      $stmt = "";

      if ($this->child) {
        list($arg, $this_joins) = $this->child->create_where($query);
        $joins = array_merge($joins, $this_joins);
        $stmt .= strtolower($query->get_class()) . ".{$arg}";
      }

      if ($this->modifier)
        $stmt .= " {$this->modifier} ";

      /**
       * If this field of this F object is another F object,
       * then use that fields' create_where() method to build
       * this statement through recursion
       */
      $stmt .= $this->field;
      
      return array($stmt, $joins);
    }

  }

  /**
   * Q nodes are used to create complex queries using chained
   * AND/OR operations
   */
  class Q extends ExpressionNode {

    protected $not = false;

    /**
     * Create new Q object with given argument/test
     *
     * @param array $arg
     */
    public function __construct($arg) {
      parent::__construct($arg);
    }

    /**
     * Use this Q object and it's child Q object to create
     * a chained statement
     *
     * @return array Returns both the created "where" statement and any joins required
     */
    public function create_where($query) {
      $stmt = "";
      $joins = array();

      // prepend child statement
      if ($this->child) {
        list($arg, $this_joins) = $this->child->create_where($query);
        $stmt .= $arg;
        $joins = array_merge($joins, $this_joins);
      }

      // use 'AND' or 'OR' modifier if applicable (when chained)
      switch($this->modifier) {
        case "&":
          $stmt .= " AND ";
          break;
        case "|":
          $stmt .= " OR ";
          break;
      }

      if ($this->field instanceof Q) {
        list($arg, $this_joins) = $this->field->create_where($query);
        $arg = "({$arg})";
      } else {
        list($arg, $this_joins) = QuerySet::format_argument($this->field, $query);
      }
      $joins = array_merge($joins, $this_joins);
      $stmt .= $arg;

      return array($stmt, $joins);
    }

    /**
     * Returns whether or not this Expression should lead with
     * 'NOT'
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
      if (!$this->not) $this->not = true;
      else $this->not = false;
      return $this;
    }

  }

  /**
   * Create a set of Q nodes using 'AND'
   */
  function _AND_($args) {
    $q = false;
    $args = func_get_args(); 

    if (is_array($args[0]) && sizeof($args)) {
      // trying to pass as array of args
      $args = $args[0];
      if (!sizeof($args))
        throw new Exception("Cannot create _AND_ expression without " .
                "arguments.");
    }

    foreach($args as $arg)
      if (!$q)
        $q = new Q($arg);
      else
        $q = $q->add(new Q($arg), "&");

    return $q;
  }

  /**
   * Create a set of Q nodes using 'OR'
   */
  function _OR_($args) {
    $q = false;
    $args = func_get_args(); 

    if (is_array($args[0]) && sizeof($args)) {
      // trying to pass as array of args
      $args = $args[0];
      if (!sizeof($args))
        throw new Exception("Cannot create _OR_ expression without arguments.");
    }

    foreach($args as $arg)
      if (!$q)
        $q = new Q($arg);
      else
        $q = $q->add(new Q($arg), "|");

    return $q;
  }
