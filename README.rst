DutuPHP
=======

DutuPHP is a unified, object oriented API for PDO and mysqli under
MIT open source license. DutuPHP supports PHP 5.3 and later versions.
If you want to know more about DutuPHP visit dutuphp.com_ *(site
is still under construction so some content may not be available)* ,
for the most part this article focuses on bringing you up to speed
with using DutuPHP so let's dive into that.

.. _dutuphp.com: http://www.dutuphp.com/about

Suppose we want to find out how many rows we have in a table
"data"

This is how we do it using mysqli::

  // create a mysqli object: $db
  $result = $db->query("SELECT COUNT(*) FROM data");
  $row = $result->fetch_row();
  echo '#: ', $row[0];
  
and this how we do it using PDO::

  // create a PDO object: $db
  $sth = $db->prepare("SELECT count(*) FROM FROM data");
  $sth->execute();
  $rows = $sth->fetch(PDO::FETCH_NUM);
  echo $rows[0];
  
but using DutuPHP's unified OO API, we just do it this way::

  // configuration object
  $config = new MyConfig();
  
  // use mysqli
  $db = new MysqliImpl($config);
  $db->select("data")->count()->fetch();
  
  // ... boom!... switch to PDO 
  $db = new PDOImpl($config);
  $db->select("data")->count()->fetch();
  
  // there's no need to change our code during the switch
  // don't forget to wrap the PDO version inside the try {} catch() {} block
  
  
Installation
============

1. Download_ DutuPHP
2. Copy and paste the ``lib`` directory in your project's library folder.
3. ``include`` these files: ``AbstractConfig.php``, ``MysqliImpl.php`` and ``PDOImpl.php``  
4. Create a configuration object by extending ``AbstractConfig.php``, overwrite the default database settings and pass it to a ``MysqliImpl or PDOImpl constructor`` 


An illustration of steps 3-4 can be found here_.

.. _Download: https://github.com/tafadzwagonera/dutuphp/archive/master.zip
.. _here: https://github.com/tafadzwagonera/dutuphp/blob/master/tests.php

A few points to note
====================

The golden rule of DutuPHP is::

  $db->select|update|delete|insert(table, [fields])[->modifiers()]->execute|affectedRows|fetch|fetchAll(...);
  
Yes, it looks long and ugly but it will get clear once I start explaining
how it works. For now just remember 3 words: **builders**, **modifiers**
and **executors**. DutuPHP has 3 types of methods: **builders**, **modifiers**
and **executors**. These methods work in that way respectively because
DutuPHP "builds up" a query, "modifies it" if need be and then "executes"
it. It's that straight forward.

Builders
________

``select|update|delete|insert(table, [fields])`` methods are the **builders**.
Builders build up queries.

``$db->select(table, [fields])`` maps to ``"SELECT [fields] FROM table"``

``$db->insert(table, [fields])`` maps to ``"INSERT INTO table ([fields' keys]) VALUES ([fields' values])"``

``$db->update(table, [fields])`` maps to ``"UPDATE table SET field1 = value1, field2 = value2, etc"``

``$db->delete(table, [where])`` maps to ``"DELETE FROM table WHERE [where]"``

Modifiers
_________

**Modifiers** change the result set returned by SQL statement for the most
part we use modifiers to alter the result set returned by SELECT statement.

``$db->select("data", [fields])->distinct()...`` maps to ``"SELECT DISTINCT [fields] FROM data"``

``$db->select("data")->count()...`` maps to ``"SELECT COUNT(*) FROM data"``

MysqliImpl and PDOImpl, respectively::

  $db->select("data")->where("id = ?", array(3))...           maps to "SELECT * FROM data WHERE id = 3"
  $db->select("data")->where("id = :id", array('id' => 3))... maps to "SELECT * FROM data WHERE id = 3"

``$db->select("data")->groupBy([fields])...`` maps to ``"SELECT * FROM data GROUP BY [fields]"``

``$db->select("data")->orderBy([fields])...`` maps to ``"SELECT * FROM data ORDER BY [fields]"``

The ellipsis "..." at the end of each expression above means that the
modifiers are part of a chained call which eventually ends with an
executor. Since modifiers help in building up the query, they never execute
it.

Here's a list of modifiers currently supported by DutuPHP::

  distinct(array $fields, [$as])

  count($field, [$as])

  where($clause, array $values)

  groupBy(array $fields, [$order])

  having($clause, array $values)

  orderBy(array $fields, [$order])
  
  limit($offset, [$max])
  
An illustration of how other modifiers work can be found here_.

.. _here: https://github.com/tafadzwagonera/dutuphp/blob/master/tests.php

Executors
_________


``execute|affectedRows|fetch|fetchAll(...)`` are **executors**. They execute the
queries built up by builders and altered by modifiers. Each executor returns
*something* when it's invoked. That *something* could be a boolean, array or
just a integer depending on which method was invoked.

``...->execute();      returns {boolean} a true value on successful execution otherwise false;``

``...->affectedRows(); returns {integer} the number of rows affected by the last query``

``...->fetch();        returns {array} a row satisfying the query``

``...->fetchAll();     returns {array of arrays} a number of rows satisfying the query``

``...->query();        returns {string} the generated query (useful for debugging purposes)``

**NB: The** ``query()`` **is NOT an executor. It's just a helper method for displaying
a generated query and this can be helpful for debugging purposes.**

You can change the return type of ``fetch()`` and ``fetchAll`` using the
``setFetchStyle()`` or you can pass in a fetch style to any one of the
methods directly. For example, if we were using ``MysqliImpl``'s ``fetchAll()``
or ``fetch()`` we can pass in ``MYSQLI_ASSOC`` or ``MYSQLI_NUM`` to get our
result set as an associative array or numeric indexed array.
Here's a code sample for that::

  $db->select("data")->fetchAll(MYSQLI_NUM);  // return result set as a numeric indexed array  
  $db->select("data")->fetchAll(MYSQLI_ASSOC);// return result set as an associative array  
  $db->select("data")->fetchAll();            // return result set as both an associative array and a numeric indexed array
  
And what about PDO?::

  $db->select("data")->fetchAll(PDO::FETCH_NUM;); // return result set as a numeric indexed array  
  $db->select("data")->fetchAll(PDO::FETCH_ASSOC);// return result set as an associative array
  $db->select("data")->fetchAll();                // return result set as both an associative array and a numeric indexed array
  
Notice that we hardly changed the code at all. In fact, the only thing that we changed
were the fetch style constants and nothing more. The fetch style constants can also b
applied to ``fetch()`` of both ``MysqliImpl`` and ``PDOImpl``.

**NB**: Remember that the executor is the guy you want to call at the end of your chained call
everytime otherwise you won't get any results.

Using the table structure in the following section_ let's wrap it up with
an example:

.. _section: https://github.com/tafadzwagonera/dutuphp/blob/master/README.rst#examples

**MysqliImpl**

SQL: ``"SELECT * FROM data WHERE id = 3"``::

  $db->select("data")->where("id = ?", array(3))->execute();      \\ Return boolean true if there's a row from data where id = 3
  $db->select("data")->where("id = ?", array(3))->fetch();        \\ Fetch a row from data where id = 3
  $db->select("data")->where("id = ?", array(3))->affectedRows(); \\ Count the number of rows from data where id = 3
  $db->select("data")->where("id = ?", array(3))->fetchAll();     \\ Fetch all rows from data where id = 3
  $db->select("data")->where("id = ?", array(3))->query();        \\ Returns {string} "SELECT * FROM data WHERE id = ?"

**PDOImpl**

SQL: ``"SELECT * FROM data WHERE id = 3"``::

  $db->select("data")->where("id = :id", array('id' => 3))->execute();      \\ Return boolean true if there's a row from data where id = 3
  $db->select("data")->where("id = :id", array('id' => 3))->fetch();        \\ Fetch a row from data where id = 3
  $db->select("data")->where("id = :id", array('id' => 3))->affectedRows(); \\ Count the number of rows from data where id = 3
  $db->select("data")->where("id = :id", array('id' => 3))->fetchAll();     \\ Fetch all rows from data where id = 3
  $db->select("data")->where("id = :id", array('id' => 3))->query();        \\ Returns {string} "SELECT * FROM data WHERE id = :id"
  

Examples 
========

Suppose we have the following table structure::

  --
  -- Table structure for table `data`
  --

  CREATE TABLE IF NOT EXISTS `data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

with the following data::

  INSERT INTO `data` (`id`, `name`) VALUES
  (1, 'Tanya'),
  (2, 'Tadiwa'),
  (3, 'Tinashe'),
  (4, 'Tanaka'),
  (5, 'Tanya'),
  (6, 'Tapiwa');

Select
______

``$db->select("data")->distinct(array('name'))->fetchAll();`` maps to ``"SELECT DISTINCT name FROM data"``

``$db->select("data", array('name'))->count()->fetch();`` maps to ``"SELECT name, COUNT(*) FROM data"``

``$db->select("data")->fetchAll();`` maps to ``"SELECT * FROM data"``

MysqliImpl and PDOImpl, respectively::

  $db->select("data")->count()->where("id = ? AND name = ?", array(4, 'Tanaka'))->fetch();                        maps to "SELECT COUNT(*) FROM data WHERE id = 4 AND name = 'Tanaka'"
  $db->select("data")->count()->where("id = :id AND name = :name", array('id' => 4, 'name'=> 'Tanaka'))->fetch(); maps to "SELECT COUNT(*) FROM data WHERE id = 4 AND name = 'Tanaka'"

Insert
______

``$fields = array('id' => '', 'name' => 'Tanya');``

``$db->insert('data', $fields)->affectedRows();`` maps to ``"INSERT INTO data(id, name) VALUES('', 'Tanya')"``

Update
______


MysqliImpl and PDOImpl, respectively::

  $fields = array('name' => 'Tapiwa');
  $db->update('data', $fields)->where("id = ?", array(2))->affectedRows();           maps to "UPDATE data SET name = 'Tapiwa' WHERE id = 4"
  $db->update('data', $fields)->where("id = :id", array('id' => 2))->affectedRows(); maps to "UPDATE data SET name = 'Tapiwa' WHERE id = 4"

Delete
______

``$db->delete("data", array("id" => 3))->affectedRows();`` maps to ``"DELETE FROM data WHERE id = 3"``

is the convenient form of MysqliImpl and PDOImpl, respectively::

  $db->delete("data")->where("id = ?", array(3))->affectedRows();           maps to "DELETE FROM data WHERE id = 3"
  $db->delete("data")->where("id = :id", array('id' => 3))->affectedRows(); maps to "DELETE FROM data WHERE id = 3"

``$db->delete("data")->affectedRows();`` maps to ``"DELETE FROM data"``

Remember that when we use affectedRows() we get the number of rows affected
by the last query. So if we echo the first expression we get a "1"
assuming that the row with an id 3 was found.if we echo the second expression
we get whatever number of rows the table had that were deleted.::

  //use ...->where() when you want to run complex matching expressions
  $db->delete("data")->where("id <= ? AND ... ", array(2, ...))->affectedRows();// maps to "DELETE FROM data WHERE id <= 3 AND ... "
  
See more examples here_.

.. _here: https://github.com/tafadzwagonera/dutuphp/blob/master/tests.php

Wrap up
=======

DutuPHP is an upcoming API for PDO and mysqli that is still under
development. Using the API comes with the caveats that several features
are either incomplete or not yet implemented and users may encounter bugs.
These and other issues which will be identified and brought to our
attention will be resolved by later versions of DutuPHP.
























