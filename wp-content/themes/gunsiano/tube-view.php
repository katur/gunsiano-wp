<?php get_header();
/*
Template Name: Tube View
Copyright (c) 2010-2012 Katherine Erickson
*/
include("katherine_functions.php");
include("katherine_connect.php");
?>

		<div id="primary">
			<div id="content" role="main">					      
    			<?php					
					$box_id = mysql_real_escape_string($_GET["box_id"]);

					// get vat name and rack name and box name for page header //
					$query = "SELECT storage_vat.vat_name, storage_rack.rack_name, 
							storage_box.box_name, storage_box_type.tubes_horizontal, 
							storage_box_type.tubes_vertical, storage_box_type.box_type
						FROM storage_box
						LEFT JOIN storage_rack
							ON storage_rack.id = storage_box.rack_id
						LEFT JOIN storage_vat
							ON storage_vat.id = storage_rack.vat_id
						LEFT JOIN storage_box_type
							ON storage_box_type.id = storage_box.box_type_id
						WHERE storage_box.id = $box_id
					";
					$result = mysql_query($query);
					if (!$result) {
						echo 'Could not run query: ' . mysql_error();
						exit;
					}

					while ($row = mysql_fetch_assoc($result)) {
						// Assign variables //
						$vat_name = $row['vat_name'];
						$rack_name = $row['rack_name'];
						$box_name = $row['box_name'];
						$tubes_horizontal = $row['tubes_horizontal'];
						$tubes_vertical = $row['tubes_vertical'];
						$box_type = $row['box_type'];
					}						

					$letter_array = array('A','B','C','D','E','F','G','H','I');					

					echo "<h1 class='entry-title no-clear'>$vat_name, Rack $rack_name,&nbsp;";

					if ($box_type == 'box') {
						echo "Box $box_name: Tube View";
					} else if ($box_type == 'plate') {
						echo "Plate $box_name: Well View";
					} else {
						echo "Contents";
					}

					echo "</h1>
					<table id='grid' class='center'>";
						$vertical_count = 1;
						while ($vertical_count <= $tubes_vertical) {
							echo "<tr>";
								$horizontal_count = 1;
								while ($horizontal_count <= $tubes_horizontal) {
										$query = "SELECT storage_tube.id, storage_tube.vertical_position, 
												storage_tube.horizontal_position, storage_tube_ref.tube_contents, 
												storage_tube_ref.freeze_date, authors.initials, strains.strain
											FROM storage_tube
											LEFT JOIN storage_tube_ref
												ON storage_tube_ref.id = storage_tube.storage_tube_ref_id
											LEFT JOIN authors
												ON authors.id = storage_tube_ref.frozen_by
											LEFT JOIN strains
												ON strains.id = storage_tube_ref.strain_id
											WHERE storage_tube.box_id = $box_id
												AND storage_tube.horizontal_position = $horizontal_count
												AND storage_tube.vertical_position = $vertical_count
												AND storage_tube.thawed = 0
										";
										$result = mysql_query($query);
										if (!$result) {
											echo 'Could not run query: ' . mysql_error();
											exit;
										}

										// If no result
										if (mysql_num_rows($result) == 0) {
											echo "<td class='no-record'>No Record</td>";

										// If result
										} else {
										    echo "<td>";
											while ($row = mysql_fetch_assoc($result)) {
												// Assign variables //
												$tube_id = $row['id'];
												$tube_contents = $row['tube_contents'];
												$freeze_date = reconfigure_date($row['freeze_date']);
												$initials = $row['initials'];
												$strain = $row['strain'];
												$horizontal_position = $row['horizontal_position'];
												$vertical_position = $letter_array[$row['vertical_position'] - 1];
												$bang = NULL;

												if ($horizontal_position && $vertical_position) {
												    echo "$vertical_position$horizontal_position:<br>";
													if ($strain)
														echo "<a href='/worm-strain/?strain=$strain'>$strain</a><br>";
													else if ($tube_contents)
													    echo "$tube_contents";
													if ($freeze_date || $initials)
													    echo "<span class='freeze-date'>frozen $freeze_date";
													    if ($initials)
													        echo " by $initials";
													    echo "</span>";						
												}		
											}
										}
									echo "</td>";
									$horizontal_count++;
								}		
							echo "</tr>";
							$vertical_count++;
						}	
					echo "</table>";			
    			?>
    			
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
