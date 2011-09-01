<?php

  class ValuesQuerySet extends Collection {
    
    private $query = null;
    private $fields = array();
    
    private $collection = null;
    
    public function __construct(QuerySet $query, $fields) {
      $this->query = $query;
      $this->fields = $fields;
      parent::__construct($query);
    }
    
    public function current() {
      $current = array();
      $obj = parent::current();
        
    }
    
  }