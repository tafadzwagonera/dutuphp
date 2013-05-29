<?php

/**
 * Provides database access using PDO
 *
 * @package  dutu   
 * @subpackage  database
 *
 * @author      Tafadzwa Gonera <tafadzwagonera@gmail.com>
 * @version      v1.0.0
 * @since       v1.0.0
 *
 * @filesource      PDOImpl.php
 */
require_once 'Database.php';

class PDOImpl implements Database {

    /**
     * @var stmt - a PDO statement
     *
     * @access private
     */
    private $stmt;

    /**
     * @var pdo - a PDO object
     *
     * @access private
     */
    private $pdo;

    /**
     * @var sql - a query statement
     *
     * @access private
     */
    private $sql;

    /**
     * @var fields - an array to hold fields with their associated values
     *
     * @access private
     */
    private $fields;
    
     /**
     * @var fields - an array to hold actual parameters for placeholders
     *
     * @access private
     */
    private $params;

    /**
     * @var fetchStyle - determines how PDO returns the row
     *
     * @access private
     */
    private $fetchStyle = PDO::FETCH_BOTH;

    /**
     * Prepares a query and binds an array of its parameters to values
     *
     * @access private
     * @throws PDOException 
     * @author Tafadzwa Gonera
     */
    private function prepareAndBind() {
        $this->stmt = $this->pdo->prepare($this->sql);
        if (!empty($this->fields)) {
            foreach ($this->fields as $key => $value)
                $this->stmt->bindValue(":$key", $value);
        }
        
        if (!empty($this->params)) {
            foreach ($this->params as $key => $value)
                $this->stmt->bindValue(":$key", $value);
        }
    }
    
    /**
     * Constructs a PDOImpl object
     * 
     * @access public
     * @param object  $config  a configuration object
     * @author Tafadzwa Gonera
     */
    function __construct(Config $config) {
        try {
            $this->pdo = new PDO($config->dsn(), $config->username(), $config->password());
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * Determines how the next row will be returned to the caller
     *
     * @access public
     * @param integer  $fetchStyle  determines how a row is returned
     * @author Tafadzwa Gonera
     */
    public function setFetchStyle($fetchStyle) {
        switch ($fetchStyle) {
            case 1:
            case 'PDO::FETCH_LAZY':
                $this->fetchStyle = PDO::FETCH_LAZY;
                break;

            case 2:
            case 'PDO::FETCH_ASSOC':
                $this->fetchStyle = PDO::FETCH_ASSOC;
                break;

            case 3:
            case 'PDO::FETCH_NUM':
                $this->fetchStyle = PDO::FETCH_NUM;
                break;

            case 5:
            case 'PDO::FETCH_OBJ':
                $this->fetchStyle = PDO::FETCH_OBJ;
                break;

            case 6:
            case 'PDO::FETCH_BOUND':
                $this->fetchStyle = PDO::FETCH_BOUND;
                break;

            case 7:
            case 'PDO::FETCH_COLUMN':
                $this->fetchStyle = PDO::FETCH_COLUMN;
                break;

            case 8:
            case 'PDO::FETCH_CLASS':
                $this->fetchStyle = PDO::FETCH_CLASS;
                break;

            default:
                $this->fetchStyle = PDO::FETCH_BOTH;
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
        $this->fields = $fields;
        $fieldNames = implode(" , ", array_keys($fields));
        $fieldValues = ':' . implode(' , :', array_keys($fields));
        $this->sql = "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)";
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
        $this->fields = $fields;
        $fieldNames = null;
        foreach ($fields as $key => $value)
            $fieldNames .= "$key = :$key, ";
        $fieldNames = rtrim($fieldNames, ' , ');
        $this->sql = "UPDATE $table SET $fieldNames";
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
        return $this->stmt->rowCount();
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
        $rows = $this->stmt->fetch($this->fetchStyle);
        return $rows;
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
        $rows = $this->stmt->fetchAll($this->fetchStyle);
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
        return $this->pdo->lastInsertId();
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
        $this->pdo = null;
    }

}
