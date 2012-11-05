<!-- Copyright (c) 2010-2012 Katherine Erickson -->

<?php
    /*  include a search form
        accepts the action url, the message to display before the search button,
        and an option message for input format. */
        function include_search_form($action_url, $message, $search_term, $small_message) {
            echo "
                <form class='search-form' method='GET' action='$action_url'>
                	$message &nbsp;
                	<input type='text' name='search_term' value='";
                	mysql_real_escape_string($_GET['search_term']);
                	echo "';></input>
                	<input type='submit' value='$search_term'></input><br>
                	<span class='small-message'>$small_message</span>
                </form>
            ";
        }
    
    
    
	/*  reconfigure a date.
	    accepts date in format: yearmonthdate.
	    returns date in format: month/date/year. */
	    
	function reconfigure_date($date){
		if ($date) {
			$times = explode('-', $date);
			return $times[1] . "/" . $times[2] . "/" . $times[0];
		} else {
			return "";
		}
	}
	
	
	
	/*  generate a wormbase link from a strain name.
	    returns wormbase link as string. */
	    
	function generate_wormbase($strain) {
		return preg_replace('/strain_fill/', $strain, 'http://wormbase.org/species/c_elegans/strain/strain_fill#01--9');
	}
	
	
	
	/*  generate genotype using pieces in the database.
	    this function takes the genotype and transgene_id fields from 
	        the 'strains' table.
	    in some cases the genotype field represents the complete genotype; in 
	        these cases, the unaltered genotype is returned.
	    if the transgene_id field is populated, then the genotype serves as
	        a genotype template, in which case the other pieces from other
	        tables are assembled.
	    returns the genotype as a string. */
	
	function generate_genotype($genotype, $transgene_id) {
		// retrieve and define the genotype template
		$query = "SELECT genotype FROM genotype WHERE id = $genotype";
		$result = mysql_query($query);
		if (!$result) {
			echo 'Could not run query: ' . mysql_error();
			exit;
		}
		while ($row = mysql_fetch_assoc($result)) {
			$genotype = $row['genotype'];
		}
		
		// if there is a transgene id provided
		if ($transgene_id) {
			// retrieve and define the transgene template
			$query = "SELECT transgene.name AS transgene_name, 
					vector.gene, vector.sequence,
					vector.promotor, vector.threePrimeUTR,
					vector_template.genotype AS vector_template_genotype
				FROM transgene
				LEFT JOIN vector
					ON transgene.vector_id = vector.id
				LEFT JOIN vector_template
					ON vector.vector_template_id = vector_template.id
				WHERE transgene.id = $transgene_id
			";
			$result = mysql_query($query);
			if (!$result) {
				echo 'Could not run query: ' . mysql_error();
				exit;
			}
			while ($row = mysql_fetch_assoc($result)) {
				$transgene_name = $row['transgene_name'];
				$vector_genotype = $row['vector_template_genotype'];
				$gene = $row['gene'];
				$sequence = $row['sequence'];
				$promotor = $row['promotor'];
				$threePrimeUTR = $row['threePrimeUTR'];
			}
			
			// if the gene is null, use the sequence
			if (!$gene) {
				$gene = $sequence;
			}
			
			// replace holes in vector (gene, promotor, and/or threePrimeUTR)
			$vector_genotype = str_replace('gene', $gene, $vector_genotype);
			$vector_genotype = str_replace('promotor', $promotor, $vector_genotype);
			$vector_genotype = str_replace('threePrimeUTR', $threePrimeUTR, $vector_genotype);
			
			// replace holes in genotype
			$genotype = str_replace('vector_genotype', $vector_genotype, $genotype);
			$genotype = str_replace('transgene_name', $transgene_name, $genotype);			
		}
		
		// return the genotype
		return $genotype;
	}
	
	
	
	/*  display contents of a freezer rack.
	    returns nothing. */

	function rack_contents($rack_id, $slots_horizontal_count, $slots_vertical_count) {
		
		// get box information for all boxes in the rack //
		$query = "SELECT storage_box.box_name, storage_box.id AS box_id, 
				storage_box.old_location, authors.author
			FROM storage_box
			LEFT JOIN authors
				ON authors.id = storage_box.author_id
			WHERE storage_box.rack_id = $rack_id
				AND storage_box.horizontal_order = $slots_horizontal_count
				AND storage_box.vertical_order = $slots_vertical_count
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
				$box_name = $row['box_name'];
				$box_id = $row['box_id'];
				$old_location = $row['old_location'];
				$author = $row['author'];
				
				if ($box_name || $author) {
				    echo "<td><a href='/storage-tubes/?box_id=$box_id'>";
				    if ($box_name) 
				        echo "$box_name<br>";
				    if ($author)
				        echo "$author<br>";
				    if ($old_location)
				        echo "prev:$old_location";
				    echo "</a></td>";
				} else
				    echo "<td>Empty Space</td>";
			}
		}
	}
	
	// HAVEN'T IMPLEMENTED THE FOLLOWING FUNCTIONS //
	// sets a word limit on a string (for display purposes)
	function string_limit_words($string, $word_limit) {
        $words = explode(' ', $string);
        return implode(' ', array_slice($words, 0, $word_limit));
    }
?>