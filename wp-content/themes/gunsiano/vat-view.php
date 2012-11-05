<?php
/*
Template Name: Vat View
Copyright (c) 2010-2012 Katherine Erickson
*/
include("katherine_functions.php");
include("katherine_connect.php");
get_header(); ?>
		<div id="primary">
			<div id="content" role="main">
			    					
				<h1 class="entry-title no-clear">Frozen Stocks</h1>
				<h4>Click to see contents</h4>
				
                <?php
					$query = "SELECT storage_vat.vat_name, storage_vat.id AS vat_id, storage_vat.vat_image FROM storage_vat
					";
					$result = mysql_query($query);
					if (!$result) {
						echo 'Could not run query: ' . mysql_error();
						exit;
					}

					while ($row = mysql_fetch_assoc($result)) {
						$vat_name = $row['vat_name'];
						$vat_id = $row['vat_id'];
						$vat_image = $row['vat_image'];
						if ($vat_name) {
							echo "
								<div class='vat-image' style='float:left; padding:20px;'>
									<a href='/storage-racks/?vat_id=$vat_id'>
										<img src='/wp-content/themes/gunsiano/images/$vat_image' height='160px'>
										<br>$vat_name
									</a>
								</div>
							";
						}
					}
    			?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>