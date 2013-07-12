<?php get_header();
include("katherine_functions.php");
include("katherine_connect.php");
/*
Template Name: New Stamp
Copyright (c) 2010-2013 Katherine Erickson
 */
?>

		<div id="primary">
			<div id="content" role="main">
			    
    			<?php
    				include ("../includes/top_bar.php");
    				include ("../includes/new_stamp_search_form.php");

    				if (mysql_real_escape_string($_GET["date"])) {
    					$date = mysql_real_escape_string($_GET["date"]);
    				}

    				if (mysql_real_escape_string($_GET["search_term"])) {
    					$search_term = mysql_real_escape_string($_GET["search_term"]);
    					$query = "SELECT library.plate_id
    						FROM library
    						WHERE library.plate_id = '$search_term'
    						LIMIT 1
    					";

    					$result = mysql_query($query);

    					if (!$result) {
    						echo 'Could not run query: ' . mysql_error();
    						exit;
    					}

    					if (mysql_num_rows($result) != 0) {
    						while ($row=mysql_fetch_assoc($result)) {
    							$plate_id = $row['plate_id'];
    							if (preg_match("/-/", $plate_id)) {
    								echo "<h1>Add a new stamp record for $plate_id</h1>";
    							} else {
    								echo "<h1>Add a new stamp record for Vidal $plate_id</h1>";
    							}
    						}

    						echo "
    							<h1 class='detail'>(Click well to change status)</h1>

    							<form method='POST' action='/GIlibrary/process_stamp.php'>
    								<input class='invisible' type='text' name='plate_id' value='$plate_id'></input>

    								Date:&nbsp;<input type='text' name='date' value='$date'></input>

    								Source:&nbsp;
    									<select name='source_id'>
    										<option value='1'>Copy 1</option>
    										<option value='2' selected='selected'>Copy 2</option>
    										<option value='3'>S/K Copy A</option>
    										<option value='4'>S/K Copy B</option>
    										<option value='5'>Deep well for frozen (from Copy 2)</option>
    										<option value='6'>Original 384</option>
    										<option value='7'>Julie copy 1</option>
    										<option value='8'>Julie copy 2</option>
    										<option value='11'>Fitch384</option>
    										<option value='12'>Hubbard384</option>
    										<option value='10'>from Eliana's frozens (from Fitch)</option>
    										<option value='13'>from S/K's frozens (from Fitch/Hubbard combo)</option>
    									</select>

    								<br><span style='color: #666; font-size: 13px;'>(in format YYYYMMDD)</span>

    								<div class='plateMedium'>
    						";

    						$query = "SELECT library.well_position, library.clone
    							FROM library
    							WHERE library.plate_id = '$search_term'
    							ORDER BY library.well_position
    						";

    						$result = mysql_query($query);

    						if (!$result) {
    							echo 'Could not run query: ' . mysql_error();
    							exit;
    						}

    						while ($row = mysql_fetch_assoc($result)) {
    							$well_position = $row['well_position'];
    							$clone = $row['clone'];
    							if ($clone == NULL) {
    								$well_status = 'status0';
    							} else {
    								$well_status = 'status1';
    							}
    							echo "
    									<div class='wellMedium $well_status' id='$well_position'>
    										&nbsp;$well_position
    									</div>
    									<input class='invisible' type='text' name='$well_position"."status' value='".preg_replace('/status/', '', $well_status)."'></input>
    									<div class='commentButton' id='$well_position'></div>
    									<div class='commentPopup' id='$well_position'>
    										<span>
    											$well_position&nbsp;comments:
    										</span>
    										<br>
    										<textarea class='comments' type='text' style='margin: 5px;' name='$well_position"."comments'></textarea>
    										<span class='closeButton' style='color: #F11; text-decoration: none; cursor: default;'>
    											<b>X</b>
    										</span>
    									</div>
    							";
    						}

    						echo "
    								</div>
    								<br>
    								<input type='submit' value='Submit to Database'></input>
    							</form>";
    					} else {
    						echo "<h1>Sorry; no plate matched your query!</h1><br><img src='../images/sad-puppy.jpg' style='margin-left:280px'>";
    					}
    				}
    			?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
