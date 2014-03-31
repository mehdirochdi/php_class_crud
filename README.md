PHP Class crud.class.php

how us it : require("lib/crud.class.php");

// save data in table
$save = $db->save(array(
			"table"  => "table name",
			"name"   => "guest",
			"age"    => "30",
			"date"   => date("Y/m/d")
		)
);
//update data
$save = $db->save(array(
			"id" 	=> "1"
			"table" => "table name",
			"name"  => "field1",
			"age"   => "20"
		)
);

// Delete Data 
$delete = $db->delete(array(
			'table' => 'table name',
			'id'    => 1
		)
);
// SELECT DATA WITH one Table
Fonction find
 ======= Minimum params ======
 $db->find('all/first', array('table'  => 'table name')); // Like " SELECT * FROM TABLE "

all ====> renvoi tous les enregistrement
first===> renvoi le premier enregistrement

 ======== More Params ========
  $db->find('all/first', array(
 		'table'  => 'table name',
 		'fields' => array('field1', 'field2'),
 		'conditions' => array('field1' => 1)
 	)
 );
  
); 
======== example More Params (3 tables )======== 
$find2 = $db->find('all', array(
				'table' => 'detailles_commandes',
				'alias' => 'dc',
				'fields'=> array('clt.id','clt.nom', 'clt.prenom', 'art.designation','dc.qt','art.pu','cde.date'),
				'joins' => array(
						'table'		=> array('articles','commandes','clients'),
						'alias'		=> array('art','cde','clt'),
						'type'		=> array('LEFT','LEFT','LEFT'),
						'condition'=> array('art.id = dc.articles_id','cde.id = dc.cde_id','clt.id = cde.clt_id')
				),
				'conditions'=>array('WHERE cde.id= 1')
		)
); 