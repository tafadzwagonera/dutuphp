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
     *Controls how the next row will be returned to the caller
     *
     * @access public
     * @param fetchStyle - determines how a row is returned
     * @author Tafadzwa Gonera
     */
    public function setFetchStyle($fetchStyle);

    /**
     * Builds up a query to insert a new row in a table
     *
     * @access public
     * @param table - a table name
     * @param fields - an associative array of fields and values to be inserted
     * @return Database object
     * @author Tafadzwa Gonera
     */
    public function insert($table, array $fields);

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
    public function update($table, array $fields);

    /**
     * Builds up a query to delete one or more rows that satisfies a
     * condition or some conditions
     *
     * @access public
     * @param table - a table name
     * @param where - an associative array of a field and its value
     *  forming the WHERE clause
     * @return Database object
     * @author Tafadzwa Gonera
     */
    public function delete($table, array $where = null);

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
    public function select($table, array $fields = null);

    /**
     * Specifies the removal of duplicate rows from the result set returned
     * by a SELECT statement
     *
     * @access public
     * @return Database object
     * @author Tafadzwa Gonera
     */
    public function distinct();

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
    public function count($field = "*", $as = null);

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
    public function where($clause);

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
    public function groupBy(array $fields, $order = null);

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
    public function having($clause);

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
    public function orderBy(array $fields, $order = null);

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
    public function limit($offset, $max = null);

    /**
     * Executes the query and returns a boolean value
     *
     * @access public
     * @return boolean - returns true if the query was excuted, false otherwise
     * @author Tafadzwa Gonera
     */
    public function execute();

    /**
     * Executes the query and returns the number of rows affected
     * by the last SQL statement
     *
     * @access public
     * @return integer - the number of rows affected by the last SQL statement
     * @author Tafadzwa Gonera
     */
    public function rowCount();

    /**
     * Executes the query and returns the next row from a result set
     *
     * @access public
     * @param fetchStyle - determines how a row is returned
     * @return array - the next row from a result set
     * @author Tafadzwa Gonera
     */
    public function fetch($fetchStyle= null);

    /**
     * Executes the query and returns an array containing all of the
     * result set rows
     *
     * @access public
     * @param fetchStyle - determines how a row is returned
     * @return array of arrays - an array of arrays containing all of the result
     * set rows
     * @author Tafadzwa Gonera
     */
    public function fetchAll($fetchStyle= null);

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @access public
     * @return integer - the ID of the last inserted row or sequence value
     * @author Tafadzwa Gonera
     */
    public function lastInsertId();

    /**
     * Returns the generated query
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function query();
}

