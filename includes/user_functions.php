<?php

function font_setup() {	

	?>
	
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>	
	
	<?php

}
			
add_action('wp_head', 'font_setup', 2);

//Via http://www.wpbeginner.com/wp-themes/how-to-add-facebook-open-graph-meta-data-in-wordpress-themes/
//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
		//This takes the standard output used for notifying browsers what language your page is in
		// and adds in the Facebook tags, since all that info goes into the HTML tag. 
		return $output . ' xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
add_filter('language_attributes', 'add_opengraph_doctype');

require ( get_template_directory() . '/includes/theme-options.php' );

if ($floatWPVer >= 3.4){

		function jq_setup() {
				
				wp_enqueue_script('jquery');
			#	wp_enqueue_script('header-imp', get_stylesheet_directory_uri() . '/includes/header-imp.js', array('waypoint'));
				wp_enqueue_script('infiniscroll', get_stylesheet_directory_uri() . '/js/jquery.infinitescroll.js', array('jquery'));
				wp_enqueue_script('scrollimp', get_stylesheet_directory_uri() . '/includes/scroll-imp.js', array('infiniscroll'));
				
		}

		add_action('wp_enqueue_scripts', 'jq_setup');
	} else {
			
		
		function jq_enqueue() {
		
						wp_dequeue_script( 'jquery' );
						wp_deregister_script( 'jquery' );
						wp_register_script('jquery', 'http://code.jquery.com/jquery-latest.min.js', '', '1.7.2');
						wp_enqueue_script('jquery');
					#	wp_enqueue_script('header-imp', get_stylesheet_directory_uri() . '/includes/header-imp.js', array('waypoint'));
						wp_enqueue_script('infiniscroll', get_stylesheet_directory_uri() . '/js/jquery.infinitescroll.js', array('jquery'));
						wp_enqueue_script('scrollimp', get_stylesheet_directory_uri() . '/includes/scroll-imp.js', array('infiniscroll'));
						
		}
		add_action('wp_enqueue_scripts', 'jq_enqueue');
}


?>