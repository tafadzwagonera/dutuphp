<?php

/**
 * Describes the a set of configuration operations for a Config
 * implementation
 *
 * @package     dutu
 * @subpackage  config
 *
 * @author      Tafadzwa Gonera <tafadzwagonera@gmail.com>
 * @version     v1.0.0
 * @since       v1.0.0
 *
 * @filesource      Config.php
 */
interface Config {

    /**
     * Returns the host name on which the database server resides
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function host();

    /**
     * Returns the data source name (DSN) prefix
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function prefix();

    /**
     * Returns the name of the database
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function dbname();

    /**
     * Returns the DSN
     * The DSN is returned as a string in the following format:
     * "mysql:host=localhost;dbname=your_dbname"
     *
     * @access public
     * @return string 
     * @author Tafadzwa Gonera
     */
    public function dsn();

    /**
     * Returns the MySQL user name of a database 
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function username();

    /**
     * Returns the password of a MySQL user
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function password();

    /**
     * Returns the port number where the database server is
     * listening
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function port();

    /**
     * Returns the MySQL Unix socket
     *
     * @access public
     * @return integer
     * @author Tafadzwa Gonera
     */
    public function socket();

}

