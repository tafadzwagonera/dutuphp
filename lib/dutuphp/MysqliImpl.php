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
     * @var fetchStyle - determines how mysqli returns the row
     *
     * @access private
     */
    private $fetchStyle = MYSQLI_BOTH;

    /**
     * Constructs a MysqliImpl object
     * 
     * @access public
     * @param config - a configuration object
     * @author Tafadzwa Gonera
     */
    function __construct(Config $config) {
        $this->mysqli = new mysqli($config->host(), $config->username(), $config->password(), $config->dbname());
        if ($this->mysqli->connect_errno)
            echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
    }

    /**
     *Controls how the next row will be returned to the caller
     *
     * @access public
     * @param fetchStyle - determines how mysqli returns the row
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
     * @param table - a table name
     * @param fields - associative array of fields and values to be inserted
     * @return Database object
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
     * @param table - a table name
     * @param fields - an associative array of fields and values
     *  forming the SET clause
     * @return Database object
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
     * @param table - a table name
     * @param where - an associative array of a field and its value
     *  forming the WHERE clause
     * @author Tafadzwa Gonera
     */
    public function delete($table, array $where = null) {
        $field = null;
        $this->sql = "DELETE FROM $table";
        if (empty($where))
            return $this;
        foreach ($where as $key => $value) {
            if (is_string($value))
                $value = "'$value'";
            $field = "$key = $value";
        }
        
        $this->sql .= " WHERE $field";
        return $this;
    }

    /**
     * Builds up a query that returns a result set of one or more rows
     * that satisfies a condition or some conditions
     *
     * @access public
     * @param table - a table name
     * @param fields - an array of fields to be selected 
     * @return Database object
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
     * @return Database object
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
     * @param field - the field to apply COUNT to
     * @param as - a name for the output field
     * @return Database object
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
     * @param clause - the condition or conditions that a row or rows must
     *  satisfy to be selected
     * @return Database object
     * @author Tafadzwa Gonera
     */
    public function where($clause) {
        $this->sql .= " WHERE $clause";
        return $this;
    }

    /**
     * Aggregates and sorts output rows according to GROUP BY fields
     *
     * @access public
     * @param fields - the GROUP BY fields
     * @param order - the order in which the output rows are sorted
     * , default is ASC
     * @return Database object
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
     * @param clause - the condition or conditions that a row or rows must
     *  satisfy to be selected
     * @return Database object
     * @author Tafadzwa Gonera
     */
    public function having($clause) {
        $this->sql .= " HAVING $clause";
        return $this;
    }

    /**
     * Sorts output rows according to ORDER BY fields
     *
     * @access public
     * @param fields - the ORDER BY fields
     * @param order - the order in which the output rows are sorted
     * , default is ASC
     * @return Database object
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
     * @param offset - a non-negative integer which represents the
     *  offset of the first row to return
     * @param  max - a non-negative integer which represents the
     *  maximum number of rows to return
     * @return Database object
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
     * @return boolean - returns true if the query was excuted, false otherwise
     * @author Tafadzwa Gonera
     */
    public function execute() {
        $this->stmt = $this->mysqli->prepare($this->sql);
        return $this->stmt->execute();
    }

    /**
     * Executes the query and returns the number of rows affected
     * by the last SQL statement
     *
     * @access public
     * @return integer - the number of rows affected by the last SQL statement
     * @author Tafadzwa Gonera
     */
    public function rowCount() {
        $this->stmt = $this->mysqli->prepare($this->sql);
        $this->stmt->execute();
        $this->stmt->store_result();
        return $this->stmt->affected_rows;
    }

    /**
     * Executes the query and returns the next row from a result set
     *
     * @access public
     * @param fetchStyle - determines how mysqli returns the row
     * @return array - the next row from a result set
     * @author Tafadzwa Gonera
     */
    public function fetch($fetchStyle= null) {
        if (!empty($fetchStyle))
            $this->fetchStyle = $fetchStyle;
        $result = $this->mysqli->query($this->sql);
        if (!$result) {
            echo $this->mysqli->error;
            return;
        }
        
        $row = $result->fetch_array($this->fetchStyle);
         return $row;
    }

    /**
     * Executes the query and returns an array of arrays containing all of the
     * result set rows
     *
     * @access public
     * @param fetchStyle - determines how mysqli returns the row
     * @return array of arrays - an array of arrays containing all of the result
     * set rows
     * @author Tafadzwa Gonera
     */
    public function fetchAll($fetchStyle=null) {
        if (!empty($fetchStyle))
            $this->fetchStyle = $fetchStyle;
        $result = $this->mysqli->query($this->sql);
        if (!$result) {
            echo $this->mysqli->error;
            return;
        }
        
        $rows = array();
        while ($row = $result->fetch_array($this->fetchStyle))
            $rows[] = $row;
        return $rows;
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @access public
     * @return integer - the ID of the last inserted row or sequence value
     * @author Tafadzwa Gonera
     */
    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    /**
     * Modifies a SQL statement
     *
     * @access public
     * @param clause - the modifying clause
     * @param sql - the SQL statement
     * @param as - a field specified by AS clause
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
     * Returns the generated query
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function query() {
        return $this->sql;
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

