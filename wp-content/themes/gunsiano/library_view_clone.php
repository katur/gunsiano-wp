<?php
include("katherine_functions.php");
include("katherine_connect.php");
/*
Template Name: Library View Clone
Copyright (c) 2010-2013 Katherine Erickson
*/

get_header(); ?>
		<div id="primary">
			<div id="content" role="main">
    			<?php
    				include("../includes/top_bar.php");
    				include("../includes/clone_search_form.php");

    				if (mysql_real_escape_string($_GET["search_term"])) {
    					$search_term = mysql_real_escape_string($_GET["search_term"]);
    					$query = "SELECT library.plate_id, library.well_position, library.clone, library.node_primary_name, library.gene
    						FROM library
    						WHERE library.clone LIKE '$search_term'
    						OR library.node_primary_name LIKE '$search_term'
    						OR library.gene LIKE '$search_term'
    						LIMIT 1
    					";

    					$result = mysql_query($query);

    					if (!$result) {
    						echo 'Could not run query: ' . mysql_error();
    						exit;
    					}

    					if (mysql_num_rows($result) != 0) {
    						while ($row = mysql_fetch_assoc($result)) {
    							$plate_id = $row['plate_id'];
    							$well_position = $row['well_position'];
    							$clone = $row['clone'];
    							$node_primary_name = $row['node_primary_name'];
    							$gene = $row['gene'];

    							if (preg_match("/-/", $plate_id)) {
    								echo "<h1>$gene: well $well_position of $plate_id</h1>";
    							} else {
    								echo "<h1>$gene: well $well_position of Vidal $plate_id</h1>";
    							}
    						}

    						echo "<div class='plate'>";

    						$query = "SELECT library.well_position, library.clone, library.gene
    							FROM library
    							WHERE library.plate_id = '$plate_id'
    							ORDER BY library.well_position
    						";

    						$result = mysql_query($query);

    						if (!$result) {
    							echo 'Could not run query: ' . mysql_error();
    							exit;
    						}

    						while ($row=mysql_fetch_assoc($result)) {
    							$plate = $row['plate_id'];
    							$well_position = $row['well_position'];
    							$clone = $row['clone'];
    							$gene = $row['gene'];
    							if ($clone == NULL) {
    								$well_class = 'status0';
    							} else {
    								$well_class = 'status1';
    							}
    							//create a div per well to visualize each as empty or colony.  Then create an invisible div per well with the clone and gene, which will appear in the pop-up on hover
    							echo "
    								<div class='well $well_class' id='$well_position'>&nbsp;$well_position</div>
    								<div class='invisible'>$clone<br>$gene</div>
    							";
    						}

    						//create the pop-up window that will show the clone and gene name on hover
    						echo "</div>

    						<div id='hoverClone'></div>
    						";
    					} else {
    						echo "<h1>Sorry; no plate matched your query!</h1><br><img src='../images/sad-puppy.jpg' style='margin-left:280px'>";
    					}
    				}
    			?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>