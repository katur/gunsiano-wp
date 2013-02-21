<?php
include("katherine_functions.php");
include("katherine_connect.php");
/*
Template Name: Strain
Copyright (c) 2010-2012 Katherine Erickson
*/

get_header(); ?>
		<div id="primary">
			<div id="content" role="main">
			    
				<?php 
				    include_search_form("/worm-strains/", "", "search strains", "");
                	// get the strain name from the URL
    				$strain = mysql_real_escape_string($_GET["strain"]);

    				// query for strain fields of interest
    				$query = "SELECT strains.id AS strain_id, 
    						strains.strain, species.species, strains.genotype, strains.transgene_id,
    						strains.date_created, authors.author, lab_codes.lab, mutagen.mutagen, strains.outcrossed,
    						strains.culture, strains.remarks, strains.wormbase, 
    						strains.received_from, strains.received_by, strains.date_received
    					FROM strains
    					LEFT JOIN authors
    						ON authors.id = strains.author_id
    					LEFT JOIN species
    						ON species.id = strains.species_id
    					LEFT JOIN mutagen
    						ON mutagen.id = strains.mutagen_id
    					LEFT JOIN lab_codes
    					ON strains.strain REGEXP CONCAT('^', lab_codes.strain_code, '[0-9]')
    					WHERE strains.strain = '$strain'
    				";

    				// run the query
    				$result = mysql_query($query);
    				if (!$result) {
    					echo 'Could not run query: ' . mysql_error();
    					exit;
    				}

    				// retrieve results
    				while ($row = mysql_fetch_assoc($result)) {
    					$strain_id = $row['strain_id'];
    					$strain = $row['strain'];
    					$species = $row['species'];
    					$genotype = $row['genotype'];
    					$transgene_id = $row['transgene_id'];

    					$date_created = reconfigure_date($row['date_created']);
    					$author = $row['author'];
    					$lab = $row['lab'];
    					$mutagen = $row['mutagen'];
    					$outcrossed = $row['outcrossed'];

    					$culture = $row['culture'];
    					$remarks = $row['remarks'];
    					$wormbase = $row['wormbase'];
    					$received_from = $row['received_from'];
    					$received_by = $row['received_by'];
    					$date_received = reconfigure_date($row['date_received']);

    					// If genotype template code provided
    					if (strlen($genotype) <= 2 && strlen($genotype) >= 1) {
    						// generate genotype using the template and any relevant pieces
    						$genotype = generate_genotype($genotype, $transgene_id);
    					}
    				}
    			?>

    			<!-- Display the title -->	
				<h1 class="entry-title no-clear">
					<?php echo $strain;?>
				</h1>

    			<!-- STRAIN OVERVIEW SECTION-->

				<div class="strain-section">
					<?php
						if ($strain)
							echo "<div class='strain-data'><b>Strain:</b>&nbsp;$strain</div>";

						if ($species)
							echo "<div class='strain-data'><b>Species:</b>&nbsp;<i>$species</i></div>";

						if ($wormbase == 1) {
							// create link to wormbase using generate_wormbase()
							echo "<div class='strain-data'>
								<a href='" . generate_wormbase($strain) . "' target='_blank'>
									See strain on WormBase.org
								</a>
							</div>";
						}
						
						if (preg_match('/fx/i', $strain)) { /* i indicates case-insensitive */
                	        $sub_strain = substr($strain,2);
                	        $mitani_url = preg_replace('/strain_fill/', $sub_strain,	     'http://www.shigen.nig.ac.jp/c.elegans/mutants/DetailsSearch?lang=english&seq=strain_fill');
                	        echo "<div class='strain-data'>
								<a href='" . $mitani_url . "' target='_blank'>
									See strain on NBRP
								</a>
							</div>";
                	    }

						if ($genotype) 
							echo "<div class='strain-data'><b>Genotype:</b>&nbsp;$genotype</div>";

						if ($culture)
							echo "<div class='strain-data'><b>Culture:</b>&nbsp;$culture</div>";
					?>
				</div>

				<!-- ORIGIN SECTION-->

				<?php
					if ($author || $lab || $date_created || $mutagen || $outcrossed) {
						echo "
							<div class='line'></div>
							<div class='strain-section'>
								<h3>Origin</h3>
							";
						if ($author)
							echo "<div class='strain-data'><b>Made By:</b>&nbsp;$author</div>";
						
						if ($lab)
							echo "<div class='strain-data'><b>Lab:</b>&nbsp;$lab</div>";
						
						if ($date_created)
							echo "<div class='strain-data'><b>Date Created:</b>&nbsp;$date_created</div>";
						
						if ($mutagen)
							echo "<div class='strain-data'><b>Mutagen or Method Used:</b>&nbsp;$mutagen</div>";
						
						if ($outcrossed)
							// Add an "x" to the number of times outcrossed
							echo "<div class='strain-data'><b>Outcrossed:</b>&nbsp;" . $outcrossed . "x</div>";
						
						echo "</div>";
					}

					if ($remarks) {
						echo "
							<div class='line'></div>
							<div class='strain-section'>
								<h3>Remarks</h3>
								<div class='strain-data'>$remarks</div>
							</div>
						";
					}


					// STORAGE SECTION

					$query = "SELECT storage_vat.vat_name, storage_rack.rack_name, storage_box.box_name, 
							storage_tube.horizontal_position, storage_tube.vertical_position, 
							storage_tube_ref.freeze_date, authors.author
						FROM storage_tube
						LEFT JOIN storage_tube_ref
							ON storage_tube_ref.id = storage_tube.storage_tube_ref_id
						LEFT JOIN storage_box
							ON storage_box.id = storage_tube.box_id
						LEFT JOIN storage_rack
							ON storage_rack.id = storage_box.rack_id
						LEFT JOIN storage_vat
							ON storage_vat.id = storage_rack.vat_id
						LEFT JOIN authors
							ON authors.id = storage_tube_ref.frozen_by
						WHERE storage_tube_ref.strain_id = '$strain_id'
						AND storage_tube.thawed = '0'
					";

					$result = mysql_query($query);

					if (!$result) {
						echo 'Could not run query: ' . mysql_error();
						exit;
					}

					$numrows = mysql_num_rows($result);

					if ($received_from || $received_by || $date_received || $numrows > 0) {
						echo " 
							<div class='line'></div>
							<div class='strain-section'>
								<h3>Stock</h3>";

						if ($received_from)
							echo "<div class='strain-data'><b>Received From:</b>&nbsp;$received_from</div>";

						if ($received_by) {
						    $query2 = "SELECT author FROM authors WHERE id = '$received_by'";
						    $result2 = mysql_query($query2);
        					if (!$result2) {
        						echo 'Could not run query: ' . mysql_error();
        						exit;
        					}
        					while ($row2 = mysql_fetch_assoc($result2))
        					    $received_author = $row2['author'];

							echo "<div class='strain-data'><b>Received By:</b>&nbsp;$received_author</div>";
						}

						if ($date_received)
							echo "<div class='strain-data'><b>Date Received:</b>&nbsp;$date_received</div>";
                        
                        echo "</div>";
                        
						$letter_array=array('A','B','C','D','E','F','G','H','I');
                        
                        echo "<div class='strain-section'>";
    						while ($row = mysql_fetch_assoc($result)) {
    							// Assign variables //
    							$vat_name = $row['vat_name'];
    							$rack_name = $row['rack_name'];
    							$box_name = $row['box_name'];
    							$horizontal_position = $row['horizontal_position'];
    							$vertical_position = $letter_array[$row['vertical_position']-1];						
    							$freeze_date = $row['freeze_date'];
    							$frozen_by = $row['author'];

    							if ($freeze_date)
    								$freeze_date = reconfigure_date($freeze_date);

    							echo "
    								<div class='freeze-data'>
    									<b>$vat_name:</b>
    									$rack_name-$box_name-$vertical_position$horizontal_position<br>
    									frozen $freeze_date by $frozen_by<br>	
    								</div>
    							";	
    						}
						echo "</div>";	
					}
				?>	

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>
