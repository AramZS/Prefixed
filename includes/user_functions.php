<?php

function font_setup() {	

	?>
	
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>	
	
	<?php

}
			
add_action('wp_head', 'font_setup', 2);

?>