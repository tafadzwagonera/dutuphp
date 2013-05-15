DutuPHP
=======

Have you ever wished to query the database in PHP without writing 
straight SQL but using objects and methods only? Have you ever 
wished that if mysqli and PDO weren't so disparage they would have
an identical interface API which could acccess the database
regardless of which database access strategy you use and perhaps
learning them wouldn't be so much of a hassle? If you were wishing
badly for these to happen then look no further, DutuPHP is the
right tool for you.

**What is DutuPHP?** It's a unified, object oriented database access API
for both PDO and mysqli. DutuPHP supports 5.3 and later versions. If
you want to know more about DutuPHP visit dutuphp.com_, for the most
part this article focuses on bringing you up to speed with using
DutuPHP so let's dive into that.

.. _dutuphp.com: http://www.dutuphp.com/

Suppose we want to find out how many rows we have in a table
"data"

This is how we do it using mysqli::

  //some long boring code
  
and this how we do it using PDO::

  //some long boring code
  
but using DutuPHP, we just do it this way::

  // configuration object
  $config = new MyConfig();
  
  // for mysqli
  $db = new MysqliImpl($config);
  $db->select("data")->rowCount();
  
  // ... boom! ... switch to PDO
  $db = new PDOImpl($config);
  $db->select("data")->rowCount();
  
  // Awesome, isn't it? Oh, and don't forget to wrap the PDO version
  // inside the try {} catch() {} block
  
  
Installation
============


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



