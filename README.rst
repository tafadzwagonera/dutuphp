DutuPHP
=======

Have you ever wished to query the database in PHP without writing 
straight SQL but using objects and methods only? Have you ever 
wished that if mysqli and PDO weren't so disparate they would have
an identical interface API which could acccess the database
regardless of which database access class you use and perhaps
learning them wouldn't be so much of a hassle? If you were wishing
badly for these to happen then look no further, DutuPHP is the
right tool for you.

So, what is DutuPHP? It's a unified, object oriented database access API
for both PDO and mysqli. DutuPHP supports PHP 5.3 and later versions. If
you want to know more about DutuPHP visit dutuphp.com_, for the most
part this article focuses on bringing you up to speed with using
DutuPHP so let's dive into that.

.. _dutuphp.com: http://www.dutuphp.com/

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
  $db->select("data")->rowCount();
  
  // ... boom!... switch to PDO 
  $db = new PDOImpl($config);
  $db->select("data")->rowCount();
  
  // Awesome, isn't it? Oh, and don't forget to wrap the PDO version
  // inside the try {} catch() {} block
  
  
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

  $db->select|update|delete|insert(table, [fields])[->modifiers()]->execute|rowCount|fetch|fetchAll(...);
  
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

``$db->select(table, [fields])`` maps to ``"SELECT [fields] FROM table WHERE ..."``

``$db->insert(table, [fields])`` maps to ``"INSERT INTO table ([fields' keys]) VALUES ..."``

``$db->update(table, [fields])`` maps to ``"UPDATE table SET [field key] = [field value], etc WHERE ..."``

``$db->delete(table, [where])`` maps to ``"DELETE FROM table WHERE [where]"``

Modifiers
_________

**Modifiers** change the result set returned by SQLstatement for the most
part we use modifiers to alter the result set returned by SELECT statement.

``$db->select("data")->where("id = 3")...`` maps to ``"SELECT * FROM data WHERE id = 3"``

``$db->select("data")->count()...`` maps to ``"SELECT COUNT(*) FROM data"``

``$db->select("data", [fields])->distinct()...`` maps to ``"SELECT DISTINCT [fields] FROM data"``

``$db->select("data")->groupBy([fields])...`` maps to ``"SELECT * FROM data GROUP BY [fields]"``

``$db->select("data")->orderBy([fields])...`` maps to ``"SELECT * FROM data ORDER BY [fields]"``

Visit dutuphp.com_ to see a catalogue of all modifiers.

.. _dutuphp.com: http://www.dutuphp.com/

The ellipsis "..." at the end of each expression above means that the
modifiers are part of a chained call which eventually ends with an
executor. Since modifiers help in building up the query, they never execute
it.

Executors
_________


``execute|rowCount|fetch|fetchAll(...)`` are **executors**. They execute the
queries built up by builders and altered by modifiers. Each executor returns
*something* when it's invoked: That *something* could be a boolean, array or
just a integer depending on which method was invoked.

``...->execute(); returns {boolean} a true or false value;``

``...->rowCount(); returns {integer} the number of rows satisfying the condition of the query``

``...->fetch(); returns {array} a row satisfying the condition of the query``

``...->fetchAll(); returns {array of arrays} a number of rows satisfying the condition of the query``

``...->query(); returns {string} the generated query (useful for debugging purposes)``

**NB: The** ``query()`` **is NOT an executor. It's just a helper method for displaying
a generated query and this can be helpful for debugging purposes.**

You can change the return type of ``fetch()`` and ``fetchAll`` using the
``setFetchStyle()`` or you can passing in a fetch style to any one of the
methods directly.The executor is the guy you want to call at the end of your
chained call everytime otherwise you won't get any results.

Using the table structure in the following section_ let's wrap it up with
an example:

.. _section: https://github.com/tafadzwagonera/dutuphp/edit/master/README.rst#examples-you-can-try-out

SQL: ``"SELECT * FROM data WHERE id = 3"``::

  $db->select("data")->where("id = 3")->execute()\\  Return boolean true if there's a row from data where id = 3
  $db->select("data")->where("id = 3")->fetch()  \\  Fetch a row from data where id = 3
  $db->select("data")->where("id = 3")->rowCount()\\ Count the number of rows from data where id = 3
  $db->select("data")->where("id = 3")->fetchAll()\\ Fetch all rows from data where id = 3
  

Examples you can try out
========================

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

``$db->select("data", array('text'))->distinct()->fetchAll();`` maps to ``"SELECT DISTINCT text FROM data"``

``$db->select("data", array('text'))->count()->fetch();`` maps to ``"SELECT text, COUNT(*) FROM data"``

``$db->select("data")->fetchAll();`` maps to ``"SELECT * FROM data"``

**NB: Something important to note**

SQL: ``"SELECT COUNT(*) FROM data WHERE id = 4 AND name = 'Tanaka'"``::

  //correct
  $db->select("data")->where("id = 4 AND name = 'Tanaka'")->rowCount(); 

  //wrong, in fact you get an error
  $db->select("data")->where("id = 4 AND name = 'Tanaka'")->count()->rowCount();

  //wrong, although it executes
  $db->select("data")->count("id = 4 AND name = 'Tanaka'")->rowCount();


Insert
______

``$fields = array('id' => '', 'text' => 'Tanya');``

``$db->insert('data', $fields)->rowCount();`` maps to ``"INSERT INTO data(id, text) VALUES('', 'Tanya')"``

Update
______

``$fields = array('text' => 'Tapiwa');``

``$db->update('data', $fields)->where("id = 4")->rowCount();`` maps to ``"UPDATE data SET text = 'Tapiwa' WHERE id = 4"``

Delete
______

``$db->delete("data", array("id" => 3))->rowCount();`` maps to ``"DELETE FROM data WHERE id = 3"``

``$db->delete("data")->rowCount();`` maps to ``"DELETE FROM data"``

Remember that when we use rowCount() we get the number of rows affected
by the last query. So if we echo the first expression we get a "1"
assuming that the row with an id 3 was found.if we echo the second expression
we get whatever number of rows the table had that were deleted.::

  //use ...->where() when you want to run complex matching expressions
  $db->delete("data")->where("id <= 3 AND ... ")->rowCount();// maps to "DELETE FROM data WHERE id <= 3 AND ... "

Conclusion
==========

DutuPHP is an upcoming database access API for PHP 5.3x and later releases
which is still under development. Using the API comes with the caveats that
several features are either incomplete or not yet implemented and users may
encounter bugs. These and other issues which will be identified and brought
to our attention will be resolved by later versions of DutuPHP.

Visit dutuphp.com_ to learn more about DutuPHP.

.. _dutuphp.com: http://www.dutuphp.com/






















