<?php 
	$json_array = array();
	array_push($json_array, 
		array(
			'id'=>'1',
			'nombre'=>'Joel'
		)
	);
	array_push($json_array, 
		array(
			'id'=>'2',
			'nombre'=>'pato'
		)
	);

	array_push($json_array, 
		array(
			'id'=>'3',
			'nombre'=>'tercero'
		)
	);
	echo json_encode($json_array);
 ?>