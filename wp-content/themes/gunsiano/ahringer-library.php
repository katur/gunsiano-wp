<?php get_header();
include("katherine_functions.php");
include("katherine_connect.php");
?>
/*
Template Name: Ahringer Library
Copyright (c) 2010-2013 Katherine Erickson
*/

		<div id="primary">
			<div id="content" class="tall-content" role="main">

			    <?php include_search_form("", "", "search plate / clone", ""); ?>
			    
			    			    <h1 class="entry-title no-clear">Ahringer RNAi Library</h1>
			    <?php
			                        
                    // if there is a search term
    				if (mysql_real_escape_string($_GET["search_term"])) {
    					$search_term = mysql_real_escape_string($_GET["search_term"]);
    					$query = "SELECT library.plate_id
    						FROM library
    						WHERE library.plate_id = '$search_term'
    						LIMIT 1
    					";
    					/* if clone search: $query = "SELECT library.plate_id, library.well_position, library.clone, library.node_primary_name, library.gene
    						FROM library
    						WHERE library.clone LIKE '$search_term'
    						OR library.node_primary_name LIKE '$search_term'
    						OR library.gene LIKE '$search_term'
    						LIMIT 1
    					";*/
    					$result = mysql_query($query);
    					if (!$result) {
    						echo 'Could not run query: ' . mysql_error();
    						exit;
    					}
    					

    					
    					// if search term is a valid plate name
    					if (mysql_num_rows($result) != 0) {	
    						while ($row = mysql_fetch_assoc($result)) {
    							$plate_id = $row['plate_id'];
    							if (preg_match("/-/", $plate_id))
    								echo "<h2>$plate_id</h2>";
    							else 
    								echo "<h2>Vidal $plate_id</h2>";
    							/* if clone search: $plate_id = $row['plate_id'];
    							$well_position = $row['well_position'];
    							$clone = $row['clone'];
    							$node_primary_name = $row['node_primary_name'];
    							$gene = $row['gene'];
    							if (preg_match("/-/", $plate_id))
    								echo "<h1>$gene: well $well_position of $plate_id</h1>";
    							else
    								echo "<h1>$gene: well $well_position of Vidal $plate_id</h1>"; */
    						}

    						echo "<div class='plate'>";
                            
                            // query library reference table
    						$query = "SELECT library.well_position, library.clone, library.gene
    							FROM library
    							WHERE library.plate_id = '$search_term'
    							ORDER BY library.well_position
    						";
    						$result = mysql_query($query);
    						if (!$result) {
    							echo 'Could not run query: ' . mysql_error();
    							exit;
    						}

    						// create array for the 96 wells
    						// nothing in parentheses creates default integer keys (starting with 0)
    						$referenceArray = array();

    						while ($row=mysql_fetch_assoc($result)) {
    							$plate_id = $row['plate_id'];
    							$well_position = $row['well_position'];
    							$clone = $row['clone'];
    							$gene = $row['gene'];
    							if ($clone == NULL) {
    								$well_class = 'status0';
    								//add 0 to array if well did not grow
    								array_push($referenceArray,"0");
    							} else {
    								$well_class = 'status1';
    								//add 1 to array if well grew
    								array_push($referenceArray,"1");
    							}

                                // create visible and invisible (for popup on hover) divs per well
    							echo "
    								<div class='well $well_class' id='$well_position'>$well_position</div>
    								<div class='invisible'>$clone<br>$gene</div>
    							";
    						}

    						//create pop-up window that will dispay clone and gene on hover (javascript)
    						echo "</div><div id='hover-clone'></div>";

    						//select dates that the plate has been stamped
    						$query = "SELECT DISTINCT stamps.date, stamps.plate_id, stamps.source_id, stamp_source.source
    							FROM stamps
    							LEFT JOIN stamp_source
    							ON stamp_source.id = stamps.source_id
    							WHERE stamps.plate_id = '$search_term'
    							ORDER BY stamps.date
    						";
    						$result = mysql_query($query);
    						if (!$result) {
    							echo 'Could not run query: ' . mysql_error();
    							exit;
    						}

    						// start a boolean for display where 1 is left, 0 is right
    						$left = 1;

    						// for each date
    						while ($row=mysql_fetch_assoc($result)) {
    							$date = $row['date'];
    							$dateDisplay = reconfigure_date($row['date']);
    							$plate_id = $row['plate_id'];
    							$source = $row['source'];
    							$source_id = $row['source_id'];

    							if ($left == 1)
    								echo "<div class='plate-row'><div class='left-plate-wrap'>";
    							else
    							    echo "<div class='right-plate-wrap'>";

								//display date and create div for plate image
								echo "$dateDisplay<span class='stamp-source'>($source)</span><div class='plate-small'>";

								//query data for each well
								$innerQuery = "SELECT stamps.well_position, stamps.status_id, library.clone, library.gene
									FROM stamps
									LEFT JOIN library
									ON library.well_position = stamps.well_position 
										AND library.plate_id = stamps.plate_id
									WHERE stamps.date = '$date' 
										AND stamps.plate_id = '$plate_id' 
										AND stamps.source_id = '$source_id'
									ORDER BY stamps.well_position
								";
								$innerResult = mysql_query($innerQuery);
								if (!$innerResult) {
									echo 'Could not run query: ' . mysql_error();
									exit;
								}

								//start a counter to be able to compare each well to the referenceArray
								$arrayleft = 0;

								// start a counter of discrepancies from the reference plate
								$discrepancyleft = 0;

								// find growth status of each well
								while ($innerRow = mysql_fetch_assoc($innerResult)) {
									$well_position = $innerRow['well_position'];
									$clone = $innerRow['clone'];
									$gene = $innerRow['gene'];
									$status = $innerRow['status_id'];
									if ($referenceArray[$arrayleft] == $status)
										$wellBorder = '';
									else {
										$wellBorder = 'badMatch';
										$discrepancyleft = $discrepancyleft + 1;
									}

									// create visible and invisible (for popup on hover) divs per well
									echo "
										<div class='well-small status$status $wellBorder' id='$well_position-$date'>$well_position</div>
										<div class='invisible'>$clone<br>$gene</div>
									";
									$arrayleft++;
								}

    							echo "</div>"; // plate

								//total discrepancies
								echo "total discrepancies = $discrepancyleft";

								// comments
								echo "<div class='comments'><b>Comments:</b></div>";
								$innerQuery = "SELECT stamps.comments, stamps.well_position
									FROM stamps
									WHERE stamps.date = '$date' 
										AND stamps.plate_id = '$plate_id'
										AND stamps.source_id = '$source_id'
									ORDER BY stamps.well_position
								";
								$innerResult = mysql_query($innerQuery);
								if (!$innerResult) {
									echo 'Could not run query: ' . mysql_error();
									exit;
								}

								while ($innerRow=mysql_fetch_assoc($innerResult)) {
									$comments = $innerRow['comments'];
									$well_position = $innerRow['well_position'];
									if ($comments != NULL)
										echo "<div class='comments'>$well_position: $comments</div>";
								}

    							echo "</div>"; // plate wrapper

    							if ($left == 0) {
    								echo "</div>"; // row
    								$left = 1;
    							} else
    								$left = 0;
    						    
    					    } // end while for each date
    					} else
    						echo "<h2>Sorry; no plate matched your query!</h2>
    							<img src='/wp-content/themes/gunsiano/images/quokka.jpg' class='centered-block'>
    						";
    				}
    			?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
