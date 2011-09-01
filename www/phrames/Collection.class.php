<?php

  require_once("QuerySet.class.php");

  class Collection implements Countable, Iterator, ArrayAccess  {

    private $query = null;

    private $keys = array();

    private $loaded = false;

    private $pos = 0;

    public function __construct(QuerySet $query) {
      $this->query = $query;
    }

    public function load() {
      $this->keys = Database::queryGetKeys($this->query);
      $this->loaded = true;
    }

    /**
     * Returns an ORM object (Model) using the class
     * that this Collection represents
     * 
     * @param integer $key
     * @return Model
     */
    public function getItem($key) {
      if (!$this->loaded) $this->load();
      $class = $this->query->get_class();
      return new $class((int) $this->keys[$key]);
    }
    
    /**
     * COUNTABLE
     */

    public function count() {
      if ($this->loaded)
              return sizeof($this->keys);
      else
        return Database::queryGetLength($this->query);
    }

    /**
     * ITERATOR
     */

    public function rewind() {
      $this->pos = 0;
    }

    public function valid() {
      return $this->pos < sizeof($this);
    }

    public function key() {
      return $this->pos;
    }

    public function current() {
      return $this->getItem($this->pos);
    }

    public function next() {
      $this->pos++;
    }

    /**
     * ARRAYACCESS
     */

    public function offsetSet($offset, $value) {
      throw new Exception("Cannot add to Collection.");
    }

    public function offsetExists($offset) {
      return ($offset < sizeof($this) && $offset >= 0 ? true : false);
    }

    public function offsetUnset($offset) {
      $this->getItem($offset)->delete();
      unset($this->keys[$offset]);
    }

    public function offsetGet($offset) {
      return $this->getItem($offset);
    }
    
  }
