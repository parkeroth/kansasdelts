<?php

  require_once("QuerySet.class.php");

  class Manager {
    
    private $model = null;
    
    public function __construct($model) {
      $this->model = $model;
    }
    
    public function get_query_set() {
      return new QuerySet($this->model);
    }
    
    public function all() {
      return $this->get_query_set();
    }
    
    public function count() {
      return $this->get_query_set()->count();
    }
    
    public function get() {
      return call_user_func_array(array($this->get_query_set(), "get"), func_get_args());
    }
    
    public function create($args) {
      return $this->get_query_set($args)->create($args);
    }
    
    public function filter() {
      return call_user_func_array(array($this->get_query_set(), "filter"), func_get_args());
    }

    public function exclude() {
      return call_user_func_array(array($this->get_query_set(), "exclude"), func_get_args());
    }
    
  }
