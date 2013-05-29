<?php

/**
 * Describes a set of operations that a database implementation
 * provides to its clients
 *
 * @package   dutu  
 * @subpackage  fieldbase
 *
 * @author      Tafadzwa Gonera <tafadzwagonera@gmail.com>
 * @version     v1.0.0
 * @since       v1.0.0
 *
 * @filesource      Database.php
 */
interface Database {

    /**
     * Determines how the next row will be returned to the caller
     *
     * @access public
     * @param integer  $fetchStyle  determines how a row is returned
     * @author Tafadzwa Gonera
     */
    public function setFetchStyle($fetchStyle);

    /**
     * Builds up a query to insert a new row in a table
     *
     * @access public
     * @param string             $table   a table name
     * @param associative array  $fields  fields and values to be inserted
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function insert($table, array $fields);

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
    public function update($table, array $fields);

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
    public function delete($table, array $where = null);

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
    public function select($table, array $fields = null);

    /**
     * Specifies the removal of duplicate rows from the result set returned
     * by a SELECT statement
     *
     * @access public
     * @param array   $fields  the fields to apply DISTINCT to
     * @param string  $as      a name for the output field 
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function distinct(array $fields, $as = null);

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
    public function count($field = "*", $as = null);

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
    public function where($clause, array $params);

    /**
     * Aggregates and sorts output rows according to GROUP BY fields
     *
     * @access public
     * @param array   $fields  the GROUP BY fields
     * @param string  $order   the order in which the output rows are sorted, default is ASC
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function groupBy(array $fields, $order = null);

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
    public function having($clause, array $params);

    /**
     * Sorts output rows according to ORDER BY fields
     *
     * @access public
     * @param array   $fields the ORDER BY fields
     * @param string  $order  the order in which the output rows are sorted, default is ASC
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function orderBy(array $fields, $order = null);

    /**
     * Constrains the number of rows returned by a SELECT statement
     *
     * @access public
     * @param non-negative integer  $offset  the offset of the first row to return
     * @param non-negative integer  $max     the maximum number of rows to return
     * @return object  Database
     * @author Tafadzwa Gonera
     */
    public function limit($offset, $max = null);

    /**
     * Executes the query and returns a boolean value
     *
     * @access public
     * @return boolean  returns true if the query was excuted, false otherwise
     * @author Tafadzwa Gonera
     */
    public function execute();

    /**
     * Executes the query and returns the number of rows affected
     * by the last SQL statement
     *
     * @access public
     * @return non-negative integer  the number of rows affected by the last SQL statement
     * @author Tafadzwa Gonera
     */
    public function affectedRows();

    /**
     * Executes the query and returns the next row from a result set
     *
     * @access public
     * @param string  $fetchStyle  determines how a row is returned
     * @return array  one row
     * @author Tafadzwa Gonera
     */
    public function fetch($fetchStyle= null);

    /**
     * Executes the query and returns an array containing all of the
     * result set rows
     *
     * @access public
     * @param string  $fetchStyle  determines how a row is returned
     * @return array of arrays  a number of rows
     * @author Tafadzwa Gonera
     */
    public function fetchAll($fetchStyle= null);

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @access public
     * @return integer  the ID of the last inserted row or sequence value
     * @author Tafadzwa Gonera
     */
    public function lastInsertId();

    /**
     * Returns the generated query
     *
     * @access public
     * @return string  the generated query
     * @author Tafadzwa Gonera
     */
    public function query();
}

