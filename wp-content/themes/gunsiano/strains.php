<?php

/*
Template Name: Strains
Copyright (c) 2010-2012 Katherine Erickson
*/
include("katherine_functions.php");
include("katherine_connect.php");
get_header(); ?>
		<div id="primary">
			<div id="content" role="main">
				<?php include_search_form("/worm-strains/", "", "search strains", "");?>
				
				<!-- Display the title -->	
				<h1 class="entry-title no-clear">Worm Strains</h1>
				
				<table id="strains">
    				<tr id="topRow">
    						<td>Strain</td>
    						<td>Species</td>
    						<td>Genotype</td>
    						<td>Made By</td>
    				</tr>
    				<?php
    					// Get search term if there is one
    					if (mysql_real_escape_string($_GET["search_term"])) {
    						$search_term = mysql_real_escape_string($_GET["search_term"]);
    					}

    					// Query all rows in strains
    					$query = "SELECT strains.strain, strains.genotype, strains.transgene_id,
    							strains.remarks, strains.culture, strains.received_from,
    							species.species, authors.author, mutagen.mutagen
    						FROM strains
    						LEFT JOIN authors
    							ON authors.id = strains.author_id
    						LEFT JOIN species
    							ON species.id = strains.species_id
    						LEFT JOIN mutagen
    							ON mutagen.id = strains.mutagen_id
    						ORDER BY strains.strain_sort
    					";

    					// Run the query
    					$result = mysql_query($query);
    					if (!$result) {
    						echo 'Could not run query: ' . mysql_error();
    						exit;
    					}

    					// Set counter to count the total results
    					$base_counter = 0;
    					$search_counter = 0;

    					// Retrieve results
    					while ($row = mysql_fetch_assoc($result)) {
    						$base_counter++;

    						$strain = $row['strain'];
    						$species = $row['species'];
    						$genotype = $row['genotype'];
    						$transgene_id = $row['transgene_id'];
    						$remarks = $row['remarks'];
    						$culture = $row['culture'];
    						$author = $row['author'];
    						$mutagen = $row['mutagen'];
    						$received_from = $row['received_from'];

    						// If genotype template code provided
    						if (strlen($genotype) <= 2 && strlen($genotype) >= 1) {
    							// generate genotype using the template and any relevant pieces
    							$genotype = generate_genotype($genotype, $transgene_id);
    						}
    						
    						if ($species == "Caenorhabditis elegans") {
    						    $species = "C.elegans";
    						}

    						// If there is a search term, only display if matches search term
    						if ($search_term && $strain) {
    						    $search_term_lower = strtolower($search_term);
    							if (preg_match('/'.$search_term_lower.'/', strtolower($strain)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($species)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($genotype)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($remarks)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($culture)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($author)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($mutagen)) ||
    								preg_match('/'.$search_term_lower.'/', strtolower($received_from))
    							){
    								$search_counter++;
    								echo "
    									<tr>
    										<td><a href='/worm-strain/?strain=$strain'>$strain</a></td>
    										<td><i>$species</i></td>
    										<td>$genotype</td>
    										<td>$author</td>
    									</tr>
    								";
    							}

    						// If there isn't a search term
    						} else {
    							// if strain isn't null, print all fields
    							if ($strain != NULL) {
    								echo "
    									<tr>
    										<td><a href='/worm-strain/?strain=$strain'>$strain</a></td>
    										<td><i>$species</i></td>
    										<td>$genotype</td>
    										<td>$author</td>
    									</tr>
    								";
    							}
    						}
    					}
    					echo "<span class=strainSearch>";
    					if ($search_counter) {
    						echo "$search_counter out of $base_counter strains match search term '$search_term'";
    					} else {
    						echo "$base_counter strains";
    					}
    					echo "</span>";

    				?>								
    			</table>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>