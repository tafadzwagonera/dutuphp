<?php

// step 3: require AbstractConfig.php, MysqliImpl.php and PDOImpl.php
// require 'path/to/lib/directory/[file.php]';

require 'lib/config/AbstractConfig.php';
require 'lib/dutuphp/MysqliImpl.php';
require 'lib/dutuphp/PDOImpl.php';

// step 4: Create a configuration object by extending AbstractConfig

class MyConfig extends AbstractConfig {
	public function init()	{

		// to overwrite the default settings
		// remove '/*' and initialize $db with your values
		/*
		$this->db = array(

			//default settings
	        'prefix' => 'mysql',
	        'host' => '127.0.0.1',
	        'dbname' => 'test',
	        'username' => 'root',
	        'password' => '',
	        'socket' => '3306',
	        'port' => '',
		);       
		// */
	}
}


$config = new MyConfig();

// use mysqli
echo "mysqli <br>";

// pass in the Config object to MysqliImpl constructor
$db = new MysqliImpl($config);
print_r($db->select("data")->count()->fetch(MYSQLI_ASSOC));

echo '<br><br>';

// ... boom!... switch to PDO 
echo "PDO <br>";

// pass in the Config object to PDOImpl constructor
$db = new PDOImpl($config);
print_r($db->select("data")->count()->fetch(PDO::FETCH_ASSOC));

// don't forget to wrap the PDO version
// inside a try {} catch() {} block;

echo "<br><br>";

// EXTRA QUERIES
// =============

// (a) to use any one of the database implementations uncomment the code

// mysqli:
// $db = new MysqliImpl($config);

// PDO:
// $db = new PDOImpl($config);

// (b) to run any one of the queries below uncomment the code

// INSERT:
// ------
// print_r($db->insert('data', array('id' => '', 'name' => 'Jimmy'))->rowCount());

// print_r($db->insert('data', array('id' => '', 'name' => 'Jane'))->rowCount());

// print_r($db->insert('data', array('id' => '', 'name' => 'Matty'))->rowCount()); 

// UPDATE:
// ------
// print_r($db->update('data', array('name' => 'Kelly'))->where("id = 1")->rowCount());

// SELECT:
// ------
// print_r($db->select("data")->fetch());

// print_r($db->select("data")->fetchAll());

// print_r($db->select("data")->where("id = 1")->rowCount());

// print_r($db->select("data")->where("id = 2")->fetch());

// print_r($db->select("data")->where("id = 1 AND name = 'John'")->rowCount());

// print_r($db->select("data")->where("id = 1 AND name = 'John'")->fetch());

// print_r($db->select("data")->where("id = 7 AND name = 'Abracadabra'")->rowCount());

// print_r($db->select("data")->where("id = 7 AND name = 'Abracadabra'")->fetch());

// print_r($db->select("data")->where("id = 2 OR id = 3")->fetchAll());

// print_r($db->select("data")->count()->fetch());

// print_r($db->select("data")->count('name')->fetch());

// print_r($db->select("data")->count('name', 'voters')->fetch());

// print_r($db->select("data")->count('DISTINCT(name)')->fetch());

// print_r($db->select("data")->count('DISTINCT(name)', 'voters')->fetch());

// print_r($db->select("data", array('name'))->distinct()->fetchAll());

// print_r($db->select("data", array('id', 'name'))->distinct()->fetchAll());

// print_r($db->select("data")->limit(2)->fetchAll());

// print_r($db->select("data")->limit(1, 0)->fetchAll());

// print_r($db->select("data")->groupBy(array('name'))->fetchAll());

// print_r($db->select("data")->groupBy(array('id', 'name'))->fetchAll());

// print_r($db->select("data")->groupBy(array('name'))->having("name LIKE '%oh%'")->fetchAll());

// print_r($db->select("data")->orderBy(array('name'))->fetchAll());

// print_r($db->select("data")->orderBy(array('name'), "DESC")->fetchAll());

// DELETE:
// ------
// print_r($db->delete("data")->where("id = 1")->rowCount());

// print_r($db->delete("data")->where("name = 'Peter'")->rowCount());

// print_r($db->delete("data")->rowCount());
