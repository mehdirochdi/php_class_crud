<?php
require('lib/crud.class.php');
/*===============================================================
							ADD DATA
=================================================================*/
/*$db->save(array(
		'table'       => 'posts',
		'title'       => 'my four title',
		'slug'        => 'my-four-title',
		'description' =>'description',
		'date'        => date('Y/m/d')
	)
);*/
/*===============================================================
							UPDATE DATA
=================================================================*/
/*$update = $db->save(array(
		'table'  => 'posts',
		'id'  => 2,
		'description' => 'lorem ipsum lorem ipsum'
	)
);
/*===============================================================
							DELETE DATA
=================================================================*/
/*$delete = $db->delete(array(
		'table' => 'posts',
		'id'    => 2
	)
);*/
/*===============================================================
							DELETE By ID
=================================================================*/
/*$db->table('table Name'); // name of table 
$db->deleteById(1); // int value*/
/*===============================================================
							DELETE ALL
=================================================================*/
/*$db->table('table Name'); // name of table 
$db->deleteAll(); */
/*===============================================================
							Read DATA 
=================================================================*/
// ##exemple1
/*$data1 = $db->find('all', array(
		'table'  => 'posts'
	)
);
debug($data1);*/

// ##exemple2 with some fields
/*$data2 = $db->find('all', array(
		'table'  => 'posts',
		'fields' => array('id', 'title')
	)
);
debug($data2);*/

// ##exemple3 with other params 'conditions'
/*$data3 = $db->find('all', array(
		'table'      => 'posts',
		'fields'     => array('id', 'title'),
		'conditions' => array('id' => 1)
	)
);
debug($data3);*/

// ##exemple4 with other params Like 'order'
/*$data4 = $db->find('all', array(
		'table'      => 'posts',
		'fields'     => array('id', 'title'),
		'order' => array('id' => 'desc')
	)
);
debug($data4);*/

// ##exemple5 with other possibility to add joins table , Alias, type (Left joint or Right joint) and Condition
/*$data5 = $db->find('all', array(
		'table' 	=> 'posts',
		'alias'  	=> 'ps',
		'fields' 	=> array('ps.id','ps.title','ps.slug', 'cat.name', 'at.firstLast'),
		'joins'  	=> array(
						'tables' 	=> array('category','author'),
						'alias' 	=> array('cat','at'),
						'type'  	=> array('LEFT','LEFT'),
						'condition' => array('cat.id = ps.id_category', 'at.id = ps.id_author')
			),
		'conditions' => array('ps.id_category' => 1)
	)
);
debug($data5);*/

/*$data6 = $db->query('SELECT * FROM postus');
debug($data6);*/
?>