<?php

  // requires PEAR DB class
  // TODO: upgrade to MDB2 class
echo 'i';
  require_once("DB.php");
echo 'ii';
  class Database {

    private $conn;

    const DATEFORMAT_LONG = "Y-m-d H:i:s";
    const DATEFORMAT_SHORT = "Y-m-d";

    const DSN = "mysqli://root:@localhost/test";

    public function __construct() {
      $this->conn = DB::connect(self::DSN);
      if (PEAR::isError($this->conn)) {
        throw new Exception("Could not connect to database.");
      } else {
        $this->conn->setOption("autofree", true);
        $this->conn->setOption("persistent", true);
        $this->conn->setFetchMode(DB_FETCHMODE_ASSOC);
      }
    }

    public function conn() {
      return $this->conn;
    }

    public static function instance() {
      return new self();
    }

    public static function quoteSmart($str) {
      return mysql_real_escape_string($str);
    }

    public static function queryDelete($query) {
      $keys = self::queryGetKeys($query);
      $class = $query->get_class();
      // unfortunately mysql doesn't support join deletes
      // need to delete by the returned keys
      self::instance()->conn()->query(sprintf("DELETE FROM %s WHERE id IN (%s)",
                                      $class::table_name(),
                                      implode(", ", $keys)));
      return $query;
    }

    public static function queryUpdate($query, $args) {
      $fields = array();
      foreach($args as $field => $value)
        $fields[] = "{$field} = '" . self::quoteSmart($value) . "'";
      $fields = implode(",", $fields);
      list($sql_joins, $sql_query, $sql_order, $sql_limit) = $query->create_statement();
      self::instance()->conn()->query(sprintf("UPDATE %s SET %s %s %s",
                                              $sql_joins,
                                              $fields,
                                              $sql_query,
                                              $sql_limit));
      return $query;
    }

    public static function queryGetLength($query) {
      // the number of rows that "should" be returned given a limited query
      $theoretical_count = $query->getLimitCount();

      // remove limiting and get a full, "actual" count
      $clone = clone $query;
      $clone = $clone->limit(0, 0);

      // athe physical number of rows returns by the query
      list($sql_joins, $sql_query, $sql_order, $sql_limit) = $query->create_statement();

      $actual_count = self::instance()->conn()->getOne(sprintf("SELECT COUNT(%s.id) FROM %s %s %s %s",
                                              strtolower($query->get_class()),
                                              $sql_joins,
                                              $sql_query,
                                              $sql_order,
                                              $sql_limit));

      if (!$theoretical_count || $actual_count < $theoretical_count)
        return $actual_count;
      else
        return $theoretical_count;
    }

    public static function queryGetKeys($query) {
      list($sql_joins, $sql_query, $sql_order, $sql_limit) = $query->create_statement();
      return self::instance()->conn()->getCol(sprintf("SELECT %s.id FROM %s %s %s %s",
                                              strtolower($query->get_class()),
                                              $sql_joins,
                                              $sql_query,
                                              $sql_order,
                                              $sql_limit));
    }

    public static function objectDelete($obj) {
      if ($obj->id)
        return self::instance()->conn()->query(sprintf("DELETE FROM %s WHERE id=%s",
                                               $obj::table_name(),
                                               $obj->id))->numRows();
      else
        return 0;
    }

    public static function objectSave($obj, $args) {
      $fields = array();
      foreach($args as $field => $value)
        $fields[] = "{$field} = '" . self::quoteSmart($value) . "'";

      $fields = implode(", ", $fields);

      if ($obj->id) {
        // update
        self::instance()->conn()->query(sprintf("UPDATE %s SET %s WHERE id=%d",
                                                $obj::table_name(),
                                                $fields,
                                                $obj->id));
        return $obj->id;
      } else {
        // create
        self::instance()->conn()->query(sprintf("INSERT INTO %s SET %s",
                                                $obj::table_name(),
                                                $fields));
        // get last id
        return self::instance()->conn()->getOne("SELECT MAX(id) FROM {$obj::table_name()}");
      }
    }

    public static function objectLoad($obj) {
      if ($obj->id)
        return self::instance()->conn()->getRow(sprintf("SELECT * FROM %s WHERE id=%d",
              $obj::table_name(),
              $obj->id));
      else
        return array();
    }

    public static function modelGetFields($class) {
      if (!class_exists($class) || !in_array("Model", class_parents($class))) {
        throw new Exception("Cannot fetch fields of non-Model class {$class}.");
        return array();
      } else {
        $stmt = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE " .
          "table_name = '{$class::table_name()}'";
        return self::instance()->conn()->getCol($stmt);
      }
    }

  }
