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

		// default settings
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

// pass the Config object into MysqliImpl constructor
$db = new MysqliImpl($config);
print_r($db->select("data")->count()->fetch(MYSQLI_ASSOC));

echo '<br><br>';

// ... boom!... switch to PDO 
echo "PDO <br>";

// pass the Config object into PDOImpl constructor
$db = new PDOImpl($config);
print_r($db->select("data")->count()->fetch(PDO::FETCH_ASSOC));

// don't forget to wrap the PDO version
// inside a try {} catch() {} block;

echo "<br><br>";

// EXAMPLES
// ========

// (a) to use any one of the database implementations uncomment the code

// mysqli:
// $db = new MysqliImpl($config);

// PDO:
// $db = new PDOImpl($config);

// (b) to run any one of the queries below uncomment the code

// INSERT:
// ------
// $db->insert('data', array('id' => '', 'name' => 'Jimmy'))->affectedRows();

// $db->insert('data', array('id' => '', 'name' => 'Jane'))->affectedRows();

// $db->insert('data', array('id' => '', 'name' => 'Matty'))->affectedRows(); 

// UPDATE:
// ------
// $db->update('data', array('name' => 'Kelly'))->where("id = ?", array(5))->affectedRows(); // MysqliImpl
// $db->update('data', array('name' => 'Kelly'))->where("id = :id", array('id' => 5))->affectedRows(); // PDOImpl

// SELECT:
// ------
// $db->select("data")->fetch();

// $db->select("data")->fetchAll();

// $db->select("data")->where("id = ?", array(1))->affectedRows(); // MysqliImpl
// $db->select("data")->where("id = :id", array('id' => 1))->affectedRows(); // PDOImpl

// $db->select("data")->where("id = ?", array(2))->fetch(); //MysqliImpl
// $db->select("data")->where("id = :id", array('id' => 2))->fetch(); // PDOImpl

// $db->select("data")->where("id = ? AND name = ?", array(1, 'John'))->affectedRows(); // MysqliImpl
// $db->select("data")->where("id = :id AND name = :name", array('id' => 1, 'name' => 'John'))->affectedRows(); // PDOImpl

// $db->select("data")->where("id = ? AND name = ?", array(1, 'John'))->fetch(); // MysqliImpl
// $db->select("data")->where("id = :id AND name = :name", array('id' => 1, 'name' => 'John'))->fetch(); // PDOImpl

// $db->select("data")->where("id = ? AND name = ?", array(7, 'Jean'))->affectedRows(); // MysqliImpl
// $db->select("data")->where("id = :id AND name = :name", array('id' => 7, 'name' => 'Jean'))->affectedRows(); // PDOImpl

// $db->select("data")->where("id = ? AND name = ?", array(7, 'Jean'))->fetch(); // MysqliImpl
// $db->select("data")->where("id = :id AND name = :name", array('id' => 7, 'name' => 'Jean'))->fetch(); // PDOImpl

// $db->select("data")->where("id = ? OR name = ?", array(2, 'Peter'))->fetchAll(); // MysqliImpl
// $db->select("data")->where("id = :id OR name = :name", array('id' => 2, 'name' => 'Peter'))->fetchAll(); // PDOImpl

// $db->select("data")->count()->fetch();

// $db->select("data")->count('name')->fetch();

// $db->select("data")->count('name', 'students')->fetch();

// $db->select("data")->count('DISTINCT(name)')->fetch();

// $db->select("data")->count('DISTINCT(name)', 'students')->fetch();

// $db->select("data", array('name'))->distinct()->fetchAll();

// $db->select("data", array('id', 'name'))->distinct()->fetchAll();

// $db->select("data")->limit(2)->fetchAll();

// $db->select("data")->limit(1, 0)->fetchAll();

// $db->select("data")->groupBy(array('name'))->fetchAll();

// $db->select("data")->groupBy(array('id', 'name'))->fetchAll();

// $db->select("data")->groupBy(array('name'))->having("name LIKE ?", array('%oh%'))->fetchAll(); // MysqliImpl
// $db->select("data")->groupBy(array('name'))->having("name LIKE :str", array('str' => '%oh%'))->fetchAll(); // PDOImpl

// $db->select("data")->orderBy(array('name'))->fetchAll();

// $db->select("data")->orderBy(array('name'), "DESC")->fetchAll();

// DELETE:
// ------
// $db->delete("data", array('id' => 1))->affectedRows();

// $db->delete("data")->where("id = ?", array(2))->affectedRows(); // MysqliImpl
// $db->delete("data")->where("id = :id", array('id' => 2))->affectedRows(); // PDOImpl

// $db->delete("data")->where("name = ?", array('Peter'))->affectedRows(); // MysqliImpl
// $db->delete("data")->where("name = :name", array('name' => 'Peter'))->affectedRows(); // PDOImpl

// $db->delete("data")->affectedRows();
