PHP Crud Class
=============

This is a class of CRUD (Creat, Read, Update and Delete) you guessed it,
it's interraction with your mysql database, with easy methods inspire since framworks.

### Setup
-----------------

```php
require 'lib/Crud.class.php';
```

### Getting Started
-----------------
```php
require 'lib/Crud.class.php';

define("DATA_BASE", "your_data"); // DATA NAME
define("USER", "user");   // USER
define("PASSWORD", "password");  // PASSWORD
```

### Examples
-----------------

##### Add data.
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
##### Delete data.
```php
<?php
	//Updating DATA
	$delete = $db->delete(array(
		'table' => 'table_name',
		'id'    => 2
		)
	);
	if($delete !==FALSE){
		echo "successfully deleted data"; // data is deleted successfully
	}
?>
```



