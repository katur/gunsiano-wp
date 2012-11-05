<?php
/*
Template Name: Rack View
Copyright (c) 2010-2012 Katherine Erickson
*/
include("katherine_functions.php");
include("katherine_connect.php");
get_header(); ?>
		<div id="primary">
			<div id="content" role="main">					

                <?php			
					$vat_id = mysql_real_escape_string($_GET["vat_id"]);			
					$query = "SELECT storage_vat.vat_name, storage_vat.shelves, storage_vat.shelf_rack_total
						FROM storage_vat
						WHERE storage_vat.id = $vat_id
					";			
					$result = mysql_query($query);
					if (!$result) {
						echo 'Could not run query: ' . mysql_error();
						exit;
					}		
						
					if (mysql_num_rows($result) == 0)
						echo '<center>No racks recorded in this storage vat.</center>';

					while ($row = mysql_fetch_assoc($result)) {
						// Assign variables //
						$vat_name = $row['vat_name'];
						$shelves = $row['shelves'];
						$shelf_rack_total = $row['shelf_rack_total'];
					}
							
					echo "<h1 class='entry-title no-clear'>$vat_name: Rack View</h1>";			
					echo "<table id='grid' class='center'>";
						$shelf_count = 1;
						while ($shelf_count <= $shelves) {
							echo "<tr>";
								$rack_count = 1;
								while ($rack_count <= $shelf_rack_total) {
									$query = "SELECT storage_rack.id AS rack_id, storage_rack.rack_name, storage_rack.rack_contents
										FROM storage_rack 
										WHERE storage_rack.order_on_shelf = $rack_count
											AND storage_rack.vat_shelf = $shelf_count
											AND storage_rack.vat_id = $vat_id
									";
									$result = mysql_query($query);
									if (!$result) {
										echo 'Could not run query: ' . mysql_error();
										exit;
									}
									
									if (mysql_num_rows($result) == 0)
										echo "<td>No Record</td>";
										
									else {
										while ($row = mysql_fetch_assoc($result)) {
											// Assign variables //
											$rack_name = $row['rack_name'];
											$rack_id = $row['rack_id'];
											$rack_contents = $row['rack_contents'];
											if ($rack_name) {
												echo "<td>
													<a href='/storage-boxes/?rack_id=$rack_id'><b>$rack_name</b>";
													if ($rack_contents)
													    echo "<br>$rack_contents";
												echo "</a></td>";
											} else
												echo "<td>EMPTY RACK SPACE</td>";					
										}
									}
									$rack_count++;
								}
							echo "</tr>";
							$shelf_count++;
						}			
					echo "</table>";					
    			?>
			
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>