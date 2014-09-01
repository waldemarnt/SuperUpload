<?php
		
		/*
			Setup where files should be uploaded to

			DEFAULT upload is goin to /webroot/files/current_year/current_month/file_name
		*/

	$options = array(
		'uploadPath' => 'files'.DS.date('Y').DS
	);
	Configure::write('uploadOptions', $options);
?>