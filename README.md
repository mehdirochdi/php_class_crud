PHP Crud Class V1
=============
by Mehdi rochdi

Class Crud extends PDO . working for MySQL. It uses PDO driver.

This is a class of CRUD (Creat, Read, Update and Delete) you guessed it,
it's interraction with your mysql database, with easy methods inspire since framworks.

### Setup
-----------------

##### Initialize Data base connection . (Crud.class.php)
```php
define("DATA_BASE", "your_data"); // DATA NAME
define("USER", "user");   // USER
define("PASSWORD", "password");  // PASSWORD
```

### Getting Started
-----------------

##### Create index.php file . (index.php)
```php
require 'lib/Crud.class.php';
```

### Examples
-----------------

```php
<?php
	//ADDING DATA
	$datas = $db->save(array(
		'table'    => 'table_name',
		'fields1'  => 'value1',
		'fields2'  => 'value2',
		'fields3'  => 'value3'
		)
	);
	if($datas !==FALSE){
		echo "data success"; // data is added successfully
	}
?>
```
##### Update data.
```php
<?php
	//Updating DATA
	$update = $db->save(array(
		'table'  => 'table_name',
		'id'  => 1, // you need to pass a id (int)
		'fields1' => 'value1'
		)
	);
	if($update !==FALSE){
		echo "successfully updated data"; // data is updated successfully
	}
?>
```
##### Delete data with 3 functions ( delete(), deleteById() and deleteAll() )
```php
<?php
	//deleting with delete()
	$delete = $db->delete(array(
		'table' => 'table_name',
		'id'    => 1 // int value
		)
	);
	
	//delelete By Id()
	$db->table('table_Name'); // name of table 
	$db->deleteById(1); // int value
	
	//delete all data
	$db->table('table Name'); // name of table 
	$db->deleteAll();
?>
```
##### Read data with mutliple parameters, tables joins are accepted
```php
<?php
	//Like SELECT * FROM table_name
	$data1 = $db->find('all', array(
		'table'  => 'table_name'
		)
	);
	debug($data1);
	
	//Like SELECT * FROM table_name LIMIT 1
	$data2 = $db->find('first', array(
		'table'  => 'table_name'
		)
	);
	debug($data2);
	
	//Like SELECT field2, field3 FROM table_name WHERE field1 = 'value'
	$data3 = $db->find('all', array(
		'table'      => 'table_name',
		'fields'     => array('field2', 'field3'), // 
		'conditions' => array('field3' => 'value') //
		)
	);
	debug($data3);
	
	//Like SELECT field1, field2 FROM table_name ORDER BY field1 DESC
	$data4 = $db->find('all', array(
		'table'      => 'table_name',
		'fields'     => array('field1', 'field2'),
		'order' => array('field1' => 'desc') // descending
		)
	);
	debug($data4);
	
	// 3 Tables Joins with different parameters
	$data5 = $db->find('all', array(
		'table' 	=> 'foreign_table',
		'alias'  	=> 'ft',
		'fields' 	=> array('ft.field1', 'pt1.field1', 'pt1.field2', 'pt2.field1'),
		'joins'  	=> array(
						'tables' 	=> array('primary_table1','primary_table1'),
						'alias' 	=> array('pt1','pt2'),
						'type'  	=> array('LEFT','LEFT'),
						'condition' => array('pt1.key. = ft.key_primary_table1', 'pt2.key. = ft.key_primary_table2')
			),
		'conditions' => array('ft.key_primary_table1' => 'value')
		)
	);
	//  SELECT ft.field1, pt1.field1, pt1.field2, pt2.field1 
	//  FROM `foreign_table` AS `ft` 
	//  LEFT JOIN `primary_table1` AS `pt1` ON pt1.key = ft.key_primary_table1 
	//  LEFT JOIN `primary_table2` AS `pt2` ON pt2.key = ft.key_primary_table2  
	//  WHERE ft.key_primary_table1 = 'value'

	debug($data5); 
?>
```
##### if you want to write your own queries in SQL
```php
<?php
$data = "SELECT * FROM table_name";
debug($data);
?>
```


