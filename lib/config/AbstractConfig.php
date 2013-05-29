<?php

/**
 * Provides default configuration information for the application
 *  
 *
 * @package     dutu
 * @subpackage  config
 *
 * @author      Tafadzwa Gonera <tafadzwagonera@gmail.com>
 * @version     v1.0.0
 * @since       v1.0.0
 *
 * @filesource      AbstractConfig.php
 */
require 'Config.php';

abstract class AbstractConfig implements Config {

    /**
     * @var db - array to hold database configuration information
     *
     * @access protected
     */
    protected $db = array(
        
        //default settings
        'prefix' => 'mysql',
        'host' => '127.0.0.1',
        'dbname' => 'test',
        'username' => 'root',
        'password' => '',
        'socket' => '3306',
        'port' => ''
    );

    /**
     * Constructs a Config object
     *
     * @access public 
     * @author Tafadzwa Gonera
     */
    function __construct() {
        //invoke the init method of the config object in context
        $this->init();
    }

    /**
     * Performs config object initialization
     *
     * @access public
     * @author Tafadzwa Gonera
     */
    public function init() {
        //TODO override and put config intialization code here
    }

    /**
     * Returns the host name on which the database server resides
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function host() {
        return $this->db['host'];
    }

    /**
     * Returns the data source name (DSN) prefix
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function prefix() {
        return $this->db['prefix'];
    }

    /**
     * Returns the name of the database.
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function dbname() {
        return $this->db['dbname'];
    }

    /**
     * Returns the DSN
     * The DSN is returned as a string in the following format:
     * mysql:host=localhost;dbname=test
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function dsn() {       
        $dsn = null;
        $dsn .= $this->db['prefix'];
        $dsn .= ':host=' . $this->db['host'];
        $dsn .= ';dbname=' . $this->db['dbname'];
        return $dsn;
    }

    /**
     * Returns the MySQL user name of a database 
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function username() {
        return $this->db['username'];
    }

    /**
     * Returns the password of a MySQL user
     *
     * @access public
     * @return string
     * @author Tafadzwa Gonera
     */
    public function password() {
        return $this->db['password'];
    }

    /**
     * Returns the port number where the database server is
     * listening.
     *
     * @access public
     * @return integer
     * @author Tafadzwa Gonera
     */
    public function port() {
        return $this->db['port'];
    }

    /**
     * Returns the MySQL Unix socket
     *
     * @access public
     * @return integer
     * @author Tafadzwa Gonera
     */
    public function socket() {
        return $this->db['socket'];
    }

}

