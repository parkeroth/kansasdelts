<?php

  require_once("ExpressionNode.class.php");
  require_once("Collection.class.php");

  /**
   * The base class for creating database queries on the table
   * of a particular Model class
   */
  class QuerySet implements IteratorAggregate, ArrayAccess, Countable {
    
    public static $operators = array(
      "EXACT",
      "IEXACT",
      "CONTAINS",
      "ICONTAINS",
      "IN",
      "GT",
      "GTE",
      "LT",
      "LTE",
      "STARTSWITH",
      "ISTARTSWITH",
      "ENDSWITH",
      "IENDSWITH",
      "RANGE",
      "YEAR",
      "MONTH",
      "DAY",
      "WEEK_DAY",
      "ISNULL",
      "SEARCH",
      "REGEX",
      "IREGEX",
      "TIMES",
      "DIVIDED_BY",
      "PLUS",
      "MINUS"
    );

    /**
     * Store the query arguments for this query
     *
     * @var array
     */
    protected $args = array();

    /**
     * Store the parent query that is being filtered/excluded
     * or store the class name of the Model for which this QuerySet
     * references
     *
     * @var string|QuerySet
     */
    protected $parent = null;

    /**
     * Store the SQL query orders that should be used
     * default is ascending, descending is defined by - in front of field name
     * i.e. order_by('make') or order_by('-make')
     *
     * @var array
     */
    protected $order_by = array();

    /**
     * Store the SQL query limit using the ArrayAccess interface
     * such as
     *
     * $test = SomeClass::get();
     * $test["5:10"]
     *
     * @see http://docs.djangoproject.com/en/1.2/topics/db/queries/#limiting-querysets
     *
     * @var string
     */
    protected $limit = null;

    /**
     * Stores the Collection object for this QuerySet and caches it
     * 
     * @var Collection
     */
    private $collection = null;

    /**
     * Create a new query using provided parent (either class name for root
     * QuerySet object, or another QuerySet object) and arguments
     *
     * @param QuerySet $parent
     * @param array $args
     */
    public function __construct($parent, $args = null) {
      if (!($parent instanceof QuerySet) && !class_exists($parent))
        throw new Exception("Invalid QuerySet parent. Must either be valid class " .
                "name or QuerySet object.");

      $this->parent = $parent;
      // perfect valid to have a query that returns all items
      if (sizeof($args)) {
        // loop through each provided argument
        foreach($args as $arg) {
          if (is_int($arg))
            $arg = Field::id__exact($arg);
          if (($arg instanceof Expression) || ($arg instanceof ExpressionNode)) {
            // Q or F object
            $this->args[] = $arg;
          } else {
            throw new Exception("Invalid QuerySet argument: " . var_export($arg, true));
          }
        }
      }
    }
    
    /**
     * A nice wrapper for the values_list() method when obtaining an array of
     * values for a particular field for every object in a QuerySet
     * 
     * @param string $field
     * @return array
     */
    public function __get($field) {
      return $this->values_list($field);
    }
    
    /**
     * Returns a debugging reference string of all the objects
     * returned by this QuerySet
     * 
     * @return type string
     */
    public function __toString() {
      $objs = array();
      foreach($this as $obj)
        $objs[] = "<" . get_class($obj) . ": {$obj}>";
      return htmlentities("[" . implode(", ", $objs) . "]");
    }
    
    /**
     * Returns a single Object given particular query arguments
     * if the query returns a single result
     * 
     * @param mixed $args
     * @return Object
     */
    public function get($args) {
      $clone = $this->filter($args);
      $num = sizeof($clone);
      
      if ($num == 1)
        return $clone[0];
      
      if (!$num)
        throw new Exception(sprintf("%s matching query does not exist.",
                $this->get_class()));
      
      throw new Exception(sprintf("get() returned more than one %s -- it " .
              "returned %s!", $this->get_class(), $num));
    }

    /**
     * Filter this query
     *
     * @return QueryFilter
     */
    public function filter($args) {
      if ($this->limit || $this->order_by)
        throw new Exception("Cannot further filter query after limiting or ordering.");
      return new QueryFilter($this, func_get_args());
    }

    /**
     * Exclude items from this query
     *
     * @return QueryExclude
     */
    public function exclude() {
      if ($this->limit || $this->order_by)
        throw new Exception("Cannot further exclude query after limiting or ordering.");
      return new QueryExclude($this, func_get_args());
    }

    /**
     * Set what field to order by for this query, using
     * the - symbol to define ASC/DESC
     * i.e. order_by("make") or order_by("-make")
     * Also accepts multiple ordering schemes, i.e.
     * order_by("username", "-password")
     * represents
     * ORDER BY username ASC, password DESC
     *
     * @param mixed $field
     */
    public function order_by($fields) {
      // reset the collection
      $this->collection = null;
      $this->order_by = func_get_args();
      return $this;
    }
    
    /**
     * Reverses the orders of a QuerySet
     * 
     * @return QuerySet 
     */
    function reverse() {
      // reset the collection
      $this->collection = null;
      $new = array();
      foreach($this->order_by as $order)
        if (substr($order, 0, 1) == "-")
                $new[] = substr($order, 1);
        else
          $new[] = "-{$order}";
      $this->order_by = $new;
      return $this;
    }

    /**
     * Returns what Model class this QuerySet pertains to
     * by recursively finding the top-most QuerySet object
     * 
     * @return string
     */
    public function get_class() {
      if ($this->parent instanceof QuerySet)
              return $this->parent->get_class();
      else
        return $this->parent;
    }

    /**
     * Format a query expression (i.e. Field::fieldname__contains('value'))
     * into a MySQL compatible WHERE test
     * 
     * Returns an array of the argument created and any joins necessary
     * to make the argument valid
     *
     * @param Expression $arg
     * @return array
     */
    public static function format_argument(Expression $arg, $query) {
      $class = $query->get_class();

      $field = $arg->getField();
      $subfield = $arg->getSubfield();
      $value = $arg->getValue();
      $operator = $arg->getOperator();
      $joins = array();

      if ($subfield) {
        $joins[] = $field;
        // try to join a subfield
        if (!($class::get_field_type($field) instanceof ForeignKey))
            throw new Exception("Cannot perform subfield query on a field that is not a foreign key.");
        $table = $field;
        // subfield is now the field
        $field = $subfield;
      } else {
        // if no joins, table alias is the class name
        $table = strtolower($class);
      }

      // callback expressions (querying using another query) uses its own
      // joins
      if ((!$arg instanceof CallbackExpression))
        $field = "{$table}.{$field}";
      
      $stmt = "";

      if ($arg instanceof CallbackExpression) {
        
        list($sql_joins, $sql_query, $sql_order, $sql_limit) =
                $value->create_statement();
        $sub_stmt = trim(sprintf("SELECT %s.%s FROM %s %s %s %s",
              strtolower($value->get_class()),
              $field,
              $sql_joins,
              $sql_query,
              $sql_order,
              $sql_limit));
        // TODO: custom defined id fields
        $stmt = "{$table}.id IN ({$sub_stmt})";
        
      } elseif ($value instanceof F) {
        
        list($value, $joins) = $value->create_where($query);

        switch(strtoupper($operator)) {
          case "EXACT":
            $stmt = "{$field} = {$value}";
            break;
          case "GT":
            $stmt = "{$field} > {$value}";
            break;
          case "GTE":
            $stmt = "{$field} >= {$value}";
            break;
          case "LT":
            $stmt = "{$field} < {$value}";
            break;
          case "LTE":
            $stmt = "{$field} <= {$value}";
            break;
          default:
            throw new Exception("Invalid operator on expression {$field}.");
        }

      } else {

        // properly escape quotes
        if (!is_array($value) && !is_object($value))
          $value = Database::quoteSmart($value);

        /**
         * @see http://docs.djangoproject.com/en/1.2/ref/models/querysets/#field-lookups
         */
        switch(strtoupper($operator)) {
          case "EXACT":
            if ($value == null)
              $stmt = "{$field} IS NULL";
            else
              $stmt = "{$field} = '{$value}'";
            break;
          case "IEXACT":
            $stmt = "{$field} ILIKE '{$value}'";
            break;
          case "CONTAINS":
            $stmt = "{$field} LIKE '%{$value}%'";
            break;
          case "ICONTAINS":
            $stmt = "{$field} ILIKE '%{$value}%'";
            break;
          case "IN":
            if (is_array($value)) {
              $ids = array();
              foreach($value as $v)
                $ids[] = "'" . Database::quoteSmart($v) . "'";
              $stmt = "{$field} IN (" . implode (", ", $ids) . ")";
            } else {
              throw new Exception("Invalid value for {$field} IN " . var_export($value, true));
            }
            break;
          case "GT":
            $stmt = "{$field} > '{$value}'";
            break;
          case "GTE":
            $stmt = "{$field} >= '{$value}'";
            break;
          case "LT":
            $stmt = "{$field} < '{$value}'";
            break;
          case "LTE":
            $stmt = "{$field} <= '{$value}'";
            break;
          case "STARTSWITH":
            $stmt = "{$field} LIKE '{$value}%'";
            break;
          case "ISTARTSWITH":
            $stmt = "{$field} ILIKE '{$value}%'";
            break;
          case "ENDSWITH":
            $stmt = "{$field} LIKE '%{$value}'";
            break;
          case "IENDSWITH":
            $stmt = "{$field} ILIKE '%{$value}'";
            break;
          case "RANGE":
            if (is_array($value) && sizeof($value) == 2)
              $stmt = "{$field} BETWEEN '{$value[0]}' AND '{$value[1]}'";
            else
              throw new Exception("Invalid value for {$field} RANGE " . var_export($value, true));
            break;
          case "YEAR":
            $stmt = "EXTRACT('year' FROM {$field}) = '{$value}'";
            break;
          case "MONTH":
            $stmt = "EXTRACT('month' FROM {$field}) = '{$value}'";
            break;
          case "DAY":
            $stmt = "EXTRACT ('day' FROM {$field}) = '{$value}'";
            break;
          case "WEEK_DAY":
            $stmt = "EXTRACT('dayofweek' FROM {$field}) = '{$value}'";
            break;
          case "ISNULL":
            if ($value === true)
              $stmt = "{$field} IS NULL";
            else
              $stmt = "{$field} IS NOT NULL";
            break;
          case "SEARCH":
            $stmt = "MATCH({$table}, {$field}) AGAINST ('{$value}' IN BOOLEAN MODE)";
            break;
          case "REGEX":
            $stmt = "{$field} REGEXP BINARY '{$value}'";
            break;
          case "IREGEX":
            $stmt = "{$field} REGEXP '{$value}'";
            break;
        }
      }
      
      if ($arg->isNot()) $stmt = "NOT ({$stmt})";
      
      return array($stmt, $joins);
    }
    /**
     * Create only the portion of the MySQL statement after the 'WHERE' clause
     * 
     * Returns an array of the WHERE clause and any joins necessary to properly
     * execute it
     * 
     * @return array
     */
    public function create_where() {
      $where = "";
      $joins = array();

      if (sizeof($this->args)) {

        if (!($this->parent instanceof QuerySet))
                $where .= " WHERE ";

        $tests = array();

        if (sizeof($this->args) > 1) $where .= "(";

        foreach($this->args as $arg) {
          if ($arg instanceof ExpressionNode) {
            // Q or F object
            list($stmt, $this_joins) = $arg->create_where($this);
            $joins = array_merge($joins, $this_joins);
            if ($arg->isNot()) $test = " NOT (";
            else $test = "(";
            $test .= $stmt;
            $test .= ")";
            $tests[] = $test;
          } else {
            list($stmt, $this_joins) = QuerySet::format_argument($arg, $this);
            $tests[] = $stmt;
            $joins = array_merge($joins, $this_joins);
          }
        }

        $where .= implode(" AND ", $tests);

        if (sizeof($this->args) > 1) $where .= ")";

      }
      return array($where, $joins);
    }

    /**
     * Create a full or partial MySQL statement using this Query
     * object
     * 
     * Returns an array of each part of the full MySQL statement:
     * 
     * array(
     *  table and joins
     *  WHERE query/clause
     *  ORDER BY clause
     *  LIMIT clause
     * );
     *
     * @return array
     */
    public function create_statement($joins = array(), $query = "") {
      // TODO: lock this function out from being called by anything
      // other than the DBHandler? Leaving for now for debugging

      $parent = $this->parent;
      $class = $this->get_class();
      
      $sql_joins = "";
      $sql_query = "";
      $sql_order = "";
      $sql_limit = "";
      
      list($stmt, $this_joins) = $this->create_where();
      $joins = array_merge($joins, $this_joins);
      $sql_query = $query;

      /**
       * ORDER BY
       */
      if (sizeof($this->order_by)) {

        $sql_order .= "ORDER BY ";

        $orders = array();
        foreach($this->order_by as $order) {
          if ($order[0] == "-") {
            $field = substr($order, 1);
            $order = "DESC";
          } else {
            $field = $order;
            $order = "ASC";
          }

          $dot_pos = strpos($field, ".");
          if ($dot_pos !== false) {
            // ordering by another table/foreignkey fields's field
            $table = substr($field, 0, $dot_pos);
            if (!($class::get_field_type($table) instanceof ForeignKey)) {
              throw new Exception("Cannot order by the field of another table ({$field}) " .
                  "if it is not a ForeignKey.");
            } else {
              $field = substr($field, $dot_pos + 1);
              $joins[] = $table;
            }
          } else {
            // table alias to use is the class name
            $table = strtolower($class);
          }

          $orders[] = "{$table}.{$field} {$order}";
        }
        $sql_order .= implode(", ", $orders);
      }

      /**
       * LIMIT
       */
      if ($this->limit) {
        list($offset, $limit) = split(":", $this->limit);
        if ($offset && is_int($limit)) {
          $sql_limit = " LIMIT " . ($limit - $offset) . " OFFSET {$offset} ";
        } elseif ($offset) {
          $sql_limit = " LIMIT {$limit} OFFSET {$offset}";
        } else {
          $sql_limit .= " LIMIT {$limit}";
        }
      }

      /**
       * BUILD THE QUERY
       */
      if (!($parent instanceof QuerySet)) {
        $sql_joins .= "{$parent::table_name()} " . strtolower($class);
        // we've collected all needed joins, but dump the dupes
        // I've done this last just in case I need them later
        $joins = array_unique($joins);
        foreach($joins as $join) {
          $type = $parent::get_field_type($join);
          $sql_joins .= " {$type->createJoin($parent, $join)}";
        }
      } else {
        list($this_joins, $this_query, $this_order, $this_limit) = $parent->create_statement($joins, $sql_query);
        $sql_joins = $this_joins;
        $sql_query .= $stmt;
      }

      return array(trim($sql_joins), trim($sql_query), trim($sql_order), trim($sql_limit));
    }
    
    /**
     * Limit this query set using offset and limit parameters
     *
     * @see http://docs.djangoproject.com/en/1.2/topics/db/queries/#limiting-querysets
     *
     * @param int $offset
     * @param int $limit
     * @return QuerySet
     */
    public function limit($offset = null, $limit = null) {
      $offset = (int) $offset;
      $limit = (int) $limit;

      if ($offset) $offset--;
      
      if (!$limit)
        $limit = "18446744073709551610";

      if ($offset && $limit > 0 && $limit <= $offset) {
        throw new Exception("Limit must be greater than offset");
      }

      $this->limit = "{$offset}:{$limit}";
      return $this;
    }

    /**
     * Returns the number of rows that _should_ be returned
     * given the current limit of this query. For example:
     *
     * $query[":10"]
     *
     * Should theoretically return 10 results. However if the
     * actual number of rows in the database is less, there are
     * less than 10 results. This will help determine what is real.
     *
     * @return integer
     */
    public function getLimitCount() {
      if (strpos($this->limit, ":") !== false) {
        list($offset, $limit) = split(":", $this->limit);
        if ($offset)
          return $limit - $offset;
        else
          return $limit;
      }
    }

    /**
     * Returns an array of values for a set of given fields
     * of all objects from this QuerySet
     * 
     * List of fields can be passed as parameters, e.g.
     * ->values_list("field1", "field2")
     * or by an array
     * ->values_list(array("field1", "field2"))
     * 
     * @param $fields
     * @return array
     */
    public function values_list($fields) {
      // TODO: direct database query for these fields?
      if (!is_array($fields))
        $fields = func_get_args();
      
      $list = array();
      foreach($this as $obj) {
        if (sizeof($fields) > 1) {
          $this_list = array();
          foreach($fields as $field)
            if (sizeof($fields) > 1)
              $this_list[$field] = $obj->$field;
            else
              $this_list = $obj->$field;
          
          $list[] = $this_list;
        } else {
          $list[] = $obj->$fields[0];
        }
      }
      return $list;
    }

    /**
     * Update all the objects returned by a query in one shot, basically
     * they same as looping through each returned object and updating
     * one or more fields, except it performs a direct database update
     * 
     * e.g. ->update(array("field" => "new value"))
     * 
     * @param array $args
     * @return QuerySet
     */
    public function update($args) {
      return Database::queryUpdate($this, $args);
    }
    
    /**
     * Deletes all of the returned objects of a query
     * Same as update() method, where except deleting returned objects 1 by 1,
     * it performs a direct DELETE statement on the database given the
     * built query
     * 
     * @return QuerySet
     */
    public function delete() {
      return Database::queryDelete($this);
    }

    /**
     * Creates a new object given $args, an array of predefined default
     * values
     * 
     * @param array $args
     * @return Object
     */
    public function create($args) {
      $class = $this->get_class();
      $obj = new $class($args);
      $obj->save();
      return $obj;
    }

    /**
     * ITERATORAGGREGATE
     */

    /**
     * Returns a Collection object that is iteratable by a foreach loop
     * 
     * @return Collection
     */
    public function getIterator() {
      if ($this->collection) {
        return $this->collection;
      } else {
        $this->collection = new Collection($this);
        return $this->getIterator();
      }
    }
    
    /**
     * ARRAYACCESS
     */
    
    public function offsetSet($offset, $value) {
      throw new Exception("Cannot arbitrarily set the object at a current " .
              "position in a QuerySet");
      return false;
    }

    public function offsetExists($offset) {
      return ($offset < sizeof($this) && $offset >= 0 ? true : false);
    }

    public function offsetUnset($offset) {
      return $this->collection[$offset]->delete();
    }

    /**
     * Handles array access when using array shortform, i.e.
     * $somequery["5:10"]
     *
     * @param string $limit
     * @return QuerySet
     */
    public function offsetGet($limit) {
      if (strpos($limit, ":") !== false) {
        list($offset, $limit) = split(":", $limit);
        $this->limit($offset, $limit);
        return $this;
      } else {
        return $this->getIterator()->offsetGet($limit);
      }
    }

    /**
     * COUNTABLE
     */

    /**
     * Returns the number of results this QuerySet contains
     * using it's Collection object, useable by sizeof() and count()
     * PHP functions
     *
     * @return integer
     */
    public function count() {
      return sizeof($this->getIterator());
    }
    
  }

  abstract class QuerySub extends QuerySet {

    /**
     * Create new subquery (as from ->filter() and ->exclude())
     */
    public function __construct($parent, $args) {
      // subqueries require arguments
      if (!sizeof($args))
        throw new Exception("Cannot exclude or filter query without arguments.");
      parent::__construct($parent, $args);
    }

  }

  class QueryFilter extends QuerySub {

    /**
     * Return the where portion of the statement prepended
     * this QueryFilter object
     *
     * @return string
     */
    public function create_where() {
      list($where, $joins) = parent::create_where();
      if (!($this->parent->parent instanceof QuerySet))
        $where = " WHERE " . $where;
      else
        $where = " AND " . $where;
     
      return array($where, $joins);
    }

  }

  class QueryExclude extends QuerySub {

     /**
     * Return the where portion of the statement prepended
     * this QueryExclude object
     *
     * @return string
     */
    public function create_where() {
      list($where, $joins) = parent::create_where();
      if (!($this->parent->parent instanceof QuerySet))
        $where = " WHERE NOT " . $where;
      else
        $where = " AND NOT " . $where;
      
      return array($where, $joins);
    }
    
  }
