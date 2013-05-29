<?php

/**
 * Provides database access using mysqli
 *
 * @package     dutu
 * @subpackage  database
 *
 * @author      Tafadzwa Gonera <tafadzwagonera@gmail.com>
 * @version     v1.0.0
 * @since       v1.0.0
 *
 * @filesource      MysqliImpl.php
 */
require_once 'Database.php';

class MysqliImpl implements Database {

    /**
     * @var stmt - a mysqli statement
     *
     * @access private
     */
    private $stmt;

    /**
     * @var mysqli - a mysqli object
     *
     * @access private
     */
    private $mysqli;

    /**
     * @var sql - a query statement
     *
     * @access private
     */
    private $sql;

    /**
     * @var fields - an array to hold actual parameters for placeholders
     *
     * @access private
     */
    private $params;

    /**
     * @var fetchStyle - determines how mysqli returns the row
     *
     * @access private
     */
    private $fetchStyle = MYSQLI_BOTH;
    
     /**
     * Prepares a query and binds an array of values to its placeholders
     *
     * @access private
     * @throws PDOException 
     * @author Tafadzwa Gonera
     */
    private function prepareAndBind() {
        $this->stmt = $this->mysqli->prepare($this->sql);
        if (!$this->stmt)
            echo $this->mysqli->error;
        if (!empty($this->params)) {
           $params = array('');
           foreach($this->params as $key => $value) {
               $params[0] .= $this->determineType($value);
               array_push($params, $value);
           }
           
           call_user_func_array(array($this->stmt, 'bind_param'), $this->refValues($params));
        }
    }
    
    /**
     * Determines the type of a value     
     * This method is needed for prepared statements. They require
     * the data type of the field to be bound with "i" s", etc.
     *
     * @access private
     * @param mixed  $item  a value input to determine the type
     * @return string  joined parameter types
     * @author Tafadzwa Gonera
     */
    protected function determineType($item) {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }
    
    /**
     * Converts an array's keys into reference keys
     * 
     * @access private
     * @param array  $params  an array holding values bound to placeholders
     * @return array
     * @author Tafadzwa Gonera
     */
    private function refValues($params) {
        
        // reference is required for PHP 5.3+
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($params as $key => $value) 
                $refs[$key] = & $params[$key];
            return $refs;
        }
        
        return $params;
    }

    /**
     * Constructs a MysqliImpl object
     * 
     * @access public
     * @param object  $config  a configuration object
     * @author Tafadzwa Gonera
     */
    function __construct(Config $config) {
        $this->mysqli = new mysqli($config->host(), $config->username(), $config->password(), $config->dbname());
        if ($this->mysqli->connect_errno)
            echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
        $this->mysqli->set_charset('utf8');
    }

    /**
     *Controls how the next row will be returned to the caller
     *
     * @access public
     * @param integer  $fetchStyle  determines how a row is returned
     * @author Tafadzwa Gonera
     */
    public function setFetchStyle($fetchStyle) {
        switch ($fetchStyle) {
            case 1:
                $this->fetchStyle = MYSQLI_ASSOC;
                break;

            case 2:
                $this->fetchStyle = MYSQLI_NUM;
                break;

            default:
                $this->fetchStyle = MYSQLI_BOTH;
                break;
        }
    }

    /**
     * Builds up a query to insert a new row in a table
     *
     * @access public
     * @param string             $table   a table name
     * @param associative array  $fields  fields and values to be inserted
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function insert($table, array $fields) {
        $fieldValues = null;
        $fieldNames = implode(' , ', array_keys($fields));
        foreach ($fields as $field) {
            if (is_string($field))
                $field = "'$field'";
            $fieldValues .= $field . ' , ';
        }

        $fieldValues = rtrim($fieldValues, ' , ');
        $this->sql = "INSERT INTO $table  ($fieldNames) VALUES ($fieldValues)";
        return $this;
    }

    /**
     * Builds up a query to update one or more rows that satisfies a
     * condition or some conditions
     *
     * @access public
     * @param string             $table   a table name
     * @param associative array  $fields  fields and values forming the SET clause
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function update($table, array $fields) {
        $fieldDetails = null;
        foreach ($fields as $key => $value) {
            if (is_string($value))
                $value = "'$value'";
            $fieldDetails .= "$key = $value, ";
        }

        $fieldDetails = rtrim($fieldDetails, ' , ');
        $this->sql = "UPDATE $table SET $fieldDetails";
        return $this;
    }

    /**
     * Builds up a query to delete one or more rows that satisfies a
     * condition or some conditions
     *
     * @access public
     * @param string             $table  a table name
     * @param associative array  $where  a field and its value forming the WHERE clause
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function delete($table, array $where = null) {
        $this->sql = "DELETE FROM $table";
        if (empty($where))
            return $this;
        foreach ($where as $key => $value) {
            if (is_string($value))
                $value = "'$value'";
            $this->sql .= " WHERE $key = $value";
        }

        return $this;
    }

    /**
     * Builds up a query that returns a result set of one or more rows
     * that satisfies a condition or some conditions
     *
     * @access public
     * @param string  $table  a table name
     * @param array  $fields  fields to be selected 
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function select($table, array $fields = null) {
        $fieldNames = null;
        if (empty($fields)) {
            $fieldNames = '*';
        } else {
            foreach ($fields as $field)
                $fieldNames .= "$field, ";
            $fieldNames = rtrim($fieldNames, ' , ');
        }

        $this->sql = "SELECT $fieldNames FROM $table";
        return $this;
    }

    /**
     * Specifies the removal of duplicate rows from the result set returned
     * by a SELECT statement
     *
     * @access public
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function distinct() {
        $mod = " DISTINCT ";
        $pos = strpos($this->sql, " ");
        $this->sql = substr_replace($this->sql, $mod, $pos, 0);
        return $this;
    }

    /**
     * Specifies the number of rows in the result set returned by a SELECT
     * statement 
     * 
     * @access public
     * @param string  $field  the field to apply COUNT to
     * @param string  $as     a name for the output field
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function count($field = "*", $as = null) {
        $this->modify("COUNT", $field, $as);
        return $this;
    }

    /**
     * Specifies the conditions or conditions that a row or rows must satisfy
     * to be selected
     *
     * @access public
     * @param string  $clause  a condition or conditions that a row or rows must satisfy to be selected
     * @param array   $params  an array of values to be bound to placeholders in the clause
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function where($clause, array $params) {
        $this->params = $params;        
        $this->sql .= " WHERE $clause";
        return $this;
    }

    /**
     * Aggregates and sorts output rows according to GROUP BY fields
     *
     * @access public
     * @param array   $fields  the GROUP BY fields
     * @param string  $order   the order in which the output rows are sorted, default is ASC
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function groupBy(array $fields, $order = null) {
        $this->sql .= " GROUP BY ";
        foreach ($fields as $field)
            $this->sql .= "$field, ";
        $this->sql = rtrim($this->sql, ' , ');
        if (!empty($order))
            $this->sql .= " $order";
        return $this;
    }

    /**
     * Specifies a search condition to filter or restrict output rows
     * formed by the GROUP BY clause
     *
     * @access public
     * @param string  $clause  a condition or conditions that a row or rows must satisfy to be selected
     * @param array   $params  an array of values to be bound to placeholders in the clause
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function having($clause, array $params) {
        if (!empty($this->params)) {
            $this->params = array_merge($this->params, $params);
        } else {
            $this->params = $params;  
        }
        
        $this->sql .= " HAVING $clause";
        return $this;
    }

    /**
     * Sorts output rows according to ORDER BY fields
     *
     * @access public
     * @param array   $fields the ORDER BY fields
     * @param string  $order  the order in which the output rows are sorted, default is ASC
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function orderBy(array $fields, $order = null) {
        $this->sql .= " ORDER BY ";
        foreach ($fields as $field)
            $this->sql .= "$field, ";
        $this->sql = rtrim($this->sql, ' , ');
        if (!empty($order))
            $this->sql .= " $order";
        return $this;
    }

    /**
     * Constrains the number of rows returned by a SELECT statement
     *
     * @access public
     * @param non-negative integer  $offset  the offset of the first row to return
     * @param non-negative integer  $max     the maximum number of rows to return
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function limit($offset, $max = null) {
        if (!empty($max)) {
            $this->sql .= " LIMIT $offset, $max";
            return $this;
        }

        $this->sql .= " LIMIT $offset";
        return $this;
    }

    /**
     * Executes the query and returns a boolean value
     *
     * @access public
     * @return boolean  returns true if the query was excuted, false otherwise
     * @author Tafadzwa Gonera
     */
    public function execute() {
        $this->prepareAndBind();
        return $this->stmt->execute();
    }

    /**
     * Executes the query and returns the number of rows affected
     * by the last SQL statement
     *
     * @access public
     * @return non-negative integer  the number of rows affected by the last SQL statement
     * @author Tafadzwa Gonera
     */
    public function affectedRows() {
        $this->prepareAndBind();
        $this->stmt->execute();
        $this->stmt->store_result();
        return $this->stmt->affected_rows;
    }

    /**
     * Executes the query and returns the next row from a result set
     *
     * @access public
     * @param string  $fetchStyle  determines how a row is returned
     * @return array  one row
     * @author Tafadzwa Gonera
     */
    public function fetch($fetchStyle = null) {
        if (!empty($fetchStyle))
            $this->fetchStyle = $fetchStyle;
        $this->prepareAndBind();
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $row = $result->fetch_array($this->fetchStyle);
        return $row;
    }

    /**
     * Executes the query and returns an array containing all of the
     * result set rows
     *
     * @access public
     * @param string  $fetchStyle  determines how a row is returned
     * @return array of arrays  a number of rows
     * @author Tafadzwa Gonera
     */
    public function fetchAll($fetchStyle = null) {
        if (!empty($fetchStyle))
            $this->fetchStyle = $fetchStyle;
        $this->prepareAndBind();
        $this->stmt->execute();
        $result = $this->stmt->get_result();

        $rows = array();
        while ($row = $result->fetch_all($this->fetchStyle))
            $rows[] = $row;
        return $rows;
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @access public
     * @return integer  the ID of the last inserted row or sequence value
     * @author Tafadzwa Gonera
     */
    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    /**
     * Returns the generated query
     *
     * @access public
     * @return string  the generated query
     * @author Tafadzwa Gonera
     */
    public function query() {
        return $this->sql;
    }

    /**
     * Modifies a SQL statement
     *
     * @access public
     * @param string  $clause  the modifying clause
     * @param string  $sql     the SQL statement
     * @param string  $as      a field specified by AS clause
     * @author Tafadzwa Gonera
     */
    public function modify($clause, $field, $as) {
        $sql = explode(" ", $this->sql);
        $query = array_shift($sql);
        $table = array_pop($sql);
        $from = array_pop($sql);
        $fieldNames = implode(" ", $sql);
        $this->sql = "$query";       

        $buildQuery = function () use($clause, $field, $as) {
            if (!empty($as)) {
                return " $clause($field) AS $as";
            } else {
                return " $clause($field)";
            }
        };

        if ($fieldNames === "*") {
            $this->sql .= $buildQuery();
        } else {
            $this->sql .= " $fieldNames,";
            $this->sql .= $buildQuery();
        }
        
        $this->sql .= " $from $table";
    }   

    /**
     * Closes the database connection explicitly
     *
     * @access public
     * @author Tafadzwa Gonera
     */
    public function __destruct() {
        $this->mysqli->close();
    }

}

