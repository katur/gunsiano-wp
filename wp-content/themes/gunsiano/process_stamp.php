<!-- Copyright (c) 2010-2012 Katherine Erickson -->

<?php
	include('../includes/global.php');
	
	//create an array of all 96 well positions to run the foreach loop
	$wellArray = array('A01', 'A02', 'A03', 'A04', 'A05', 'A06', 'A07', 'A08', 'A09', 'A10', 'A11', 'A12', 'B01', 'B02', 'B03', 'B04', 'B05', 'B06', 'B07', 'B08', 'B09', 'B10', 'B11', 'B12', 'C01', 'C02', 'C03', 'C04', 'C05', 'C06', 'C07', 'C08', 'C09', 'C10', 'C11', 'C12', 'D01', 'D02', 'D03', 'D04', 'D05', 'D06', 'D07', 'D08', 'D09', 'D10', 'D11', 'D12', 'E01', 'E02', 'E03', 'E04', 'E05', 'E06', 'E07', 'E08', 'E09', 'E10', 'E11', 'E12', 'F01', 'F02', 'F03', 'F04', 'F05', 'F06', 'F07', 'F08', 'F09', 'F10', 'F11', 'F12', 'G01', 'G02', 'G03', 'G04', 'G05', 'G06', 'G07', 'G08', 'G09', 'G10', 'G11', 'G12', 'H01', 'H02', 'H03', 'H04', 'H05', 'H06', 'H07', 'H08', 'H09', 'H10', 'H11', 'H12');
	
	//extract the date from the form
	$date = mysql_real_escape_string($_POST['date']);
	
	//extract the plate_id from the form
	$plate_id = mysql_real_escape_string($_POST['plate_id']);
	
	//extract the plate's source (which copy of library it's from) from the form
	$source_id = mysql_real_escape_string($_POST['source_id']);
	
	//extract the well_status and comments for each of the 96 wells, defining the position of each as $value using the array
	foreach($wellArray as $key => $value) {
		$well_status = mysql_real_escape_string($_POST[$value.'status']);
		$comments = mysql_real_escape_string($_POST[$value.'comments']);
		
		//insert data into the stamps SQL table
		$query = "INSERT INTO stamps (date, plate_id, source_id, well_position, status_id, comments)
		VALUES ('$date', '$plate_id', '$source_id', '$value', '$well_status', '$comments')";
		
		$result = mysql_query($query);
		
		if (!$result) {
			echo 'Could not run query: ' . mysql_error();
			exit;
		}
	}
	
	//query to find the library_id associated with that plate
	$query = "SELECT library.id AS library_id
		FROM library
		WHERE library.plate_id = '$plate_id'
	";

	$result = mysql_query($query);
	
	if (!$result) {
		echo 'Could not run query: ' . mysql_error();
		exit;
	}
	
	//define the variable $library_id
	while ($row = mysql_fetch_assoc($result)) {
		$library_id = $row['library_id'];
	}
	
	//define the subsequent library id
	$next_library_id = $library_id + 1;
	
	//query to find the plate_id of the subsequent library_id
	$query = "SELECT library.plate_id
		FROM library
		WHERE library.id = '$next_library_id'
	";
		
	$result = mysql_query($query);
	
	if (!$result) {
		echo 'Could not run query: ' . mysql_error();
		exit;
	}
		
	//define the variable $next_plate_id
	while ($row = mysql_fetch_assoc($result)) {
		$next_plate_id = $row['plate_id'];
	}		
  
	// redirect home, with date and nextPlate included in URL
	header("Location: ./new_stamp.php?date=$date&search_term=$next_plate_id");
?>