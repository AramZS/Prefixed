<?php
//This adds the library we're going to use to pull and parse Open Graph data from a page. 
require_once("OpenGraph.php");

//Here's the function to set up the input boxes on your post screen. 
include 'generate-the-meta-boxes.php';

//We're going to store the set_me_as_featured() function in this external file. 
include 'set-me-as-featured-file.php';

//Here's where we will pull in the function to pull relevent tweets, called get_some_link_relevent_tweets().
include 'get-some-tweets.php';

//Here's the code to set up a menu to control settings. 
include 'make-menus.php';

function og_additive($content) {
	
	//In order to do anything with post_metas we are going to need to get the ID of the post we're working in.
	$postID = get_the_ID();

	//And let's get the variable to check against
	$oguserlink = get_post_meta($postID, 'oglink', true);

	//This if checks three things
	//1. If it is a page or a post (as opposed to within a larger loop)
	//2. If it is in the primary query on the page. 
	//3. If the userlink field is empty?
	if( is_singular() && is_main_query() && (!empty($oguserlink)) ) {

		add_post_meta($postID, 'opengraph_image_cache', '', true);
		add_post_meta($postID, 'opengraph_title_cache', '', true);
		add_post_meta($postID, 'opengraph_descrip_cache', '', true);

		$checkogcache = get_post_meta($postID, 'opengraph_image_cache', true);
		
		$oguserlink = get_post_meta($postID, 'oglink', true);
	

		if (empty($checkogcache)){

			$page = $oguserlink;
			
			$node = OpenGraph::fetch($page);
			
			$ogImage = $node->image;
			$ogTitle = $node->title;
			$ogDescrip = $node->description;
			
			update_post_meta($postID, 'opengraph_title_cache', $ogTitle);
			update_post_meta($postID, 'opengraph_descrip_cache', $ogDescrip);
			


			
			
			if ( (strlen($ogImage)) > 0 ){
			
				$imgParts = pathinfo($ogImage);
				$imgExt = $imgParts['extension'];
				$imgTitle = $imgParts['filename'];

				
				//'/' . get_option(upload_path, 'wp-content/uploads') . '/' . date("o") 
				$uploads = wp_upload_dir();
				$ogCacheImg = 'wp-content/uploads' . $uploads[subdir] . "/" . $postID . "-" . $imgTitle . "." . $imgExt;
				
				
				if ( !file_exists($ogCacheImg) ) {
				

					copy($ogImage, $ogCacheImg);
				
				}
				//$ogCacheImg = $ogImage;
				
			} else {
			
				$oglinkpath = plugin_dir_url(__FILE__);
			
				$ogCacheImg = $oglinkpath . 'link.png';
			
			}
			
			update_post_meta($postID, 'opengraph_image_cache', $ogCacheImg);
			
		} else {
		
			$ogCacheImg = get_post_meta($postID, 'opengraph_image_cache', true);
			
		}
		
		
		
		$ogCacheImg = get_post_meta($postID, 'opengraph_image_cache', true);
		$ogTitle = get_post_meta($postID, 'opengraph_title_cache', true);
		$ogDescrip = get_post_meta($postID, 'opengraph_descrip_cache', true);
		
		//Let's get the current WordPress version. We'll need it later. 
		$wpver = get_bloginfo('version');
		
		//Once again, we are going to need to know what version WP is
		//in order to know how to tream the output of get_some_link_relevent_tweets
		//More info on this in get-some-tweets.php, line 65.
		$wpver = get_bloginfo('version');
		$floatWPVer = floatval($wpver);
		
		get_some_link_relevent_tweets($postID, $oguserlink);
		$tweetone = get_post_meta($postID, 'related_tweet_one', true);
		$tweettwo = get_post_meta($postID, 'related_tweet_two', true);
		
		if ($floatWPVer >= 3.4){
		
			//This calls WordPress's open embed function in order to feed it the Twitter links.
			//Then stores the generated embed code back in the needed variables.
			//If you want it anything other than the default width of your content.
			//You'll need to send some args here, see http://codex.wordpress.org/Embeds
			$tweetone = wp_oembed_get($tweetone);
			$tweettwo = wp_oembed_get($tweettwo);

		}
		
		if (!empty($ogCacheImg)){
			set_me_as_featured($postID, $ogCacheImg, $ogTitle);
		}

		$new_content = 
		'<div class="oglinkbox">
			<h5 class="oghead"><a href="#">Open Graph Data</a></h5>
			<div class="ogcontainer">
				<div class="oglinkimg">
					<a href="' . $oguserlink . '" title="' . $ogTitle . '"><img alt="' .  $ogTitle . '" src="' . $ogCacheImg . '" /></a>
				</div>
				<div class="oglinkcontent">
					<h4><a href="' . $oguserlink . '" title="' . $ogTitle . '">' . $ogTitle . '</a></h4>
					<p>' . $ogDescrip . '</p>
				</div>
				<div class="og-related-tweets">
				' . $tweetone . '
				' . $tweettwo . '
				</div>
			</div>
		</div>';
			
		$content .= $new_content;	
	}	
	return $content;
}
add_filter('the_content', 'og_additive', 2);

function syndication_additive() {
	//Pull down our custom options to see if the user wants to do this.
	$options = get_option('tc_options');
	
	if( is_singular() && is_main_query() && ( $options['accordion'] == 'yes') ) {

		$postID = get_the_ID();
		
		$oguserlink = get_post_meta($postID, 'oglink', true);
	
		echo '<meta name="syndication-source" content="' . $oguserlink . '"/>'; 
		
	}
	
}
add_action('wp_head', 'syndication_additive');


function accordion_it() {
	
	//Pull down our custom options to see if the user wants to do this.
	$options = get_option('tc_options');
	
	if ( $options['accordion'] == 'yes'){
		//Here we say that we want WordPress to load jQuery
		wp_enqueue_script('jquery');
		//And here we tell it to load our custom javascript file as well. 
		//But the final parameter tells WordPress that our script is dependent on jQuery, 
		//so it should load that first.
		wp_enqueue_script('accordion', plugins_url('accordion.js', __FILE__), array('jquery'));
		//By the way, __FILE__ is the full path and filename of this file
		
		/** The jQuery file we load is basically http://docs.jquery.com/UI/Accordion
		But it has one *important* difference. Where the jQuery script (like many
		you'll see on the web) has dollar signs ('$'), mine says 'jQuery'. 
		
		This is required! WordPress doesn't like the shortcut (which is what the $ is)
		because it may conflict with other JavaScript loaded in the site. They're right 
		to worry, so don't forget to make the switch! **/
	
	}

}

//Let's attach this to WordPress's call to scripts when it generates the site. 
add_action('wp_enqueue_scripts', 'accordion_it');

/**Let's look at how to pass OG info into the head of our theme.
The is a pretty general example, but the principle works for all 
relevent data. First we need to add some information to our HTML tag. 
**/

//Via http://www.wpbeginner.com/wp-themes/how-to-add-facebook-open-graph-meta-data-in-wordpress-themes/
//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
		//This takes the standard output used for notifying browsers what language your page is in
		// and adds in the Facebook tags, since all that info goes into the HTML tag. 
		return $output . ' xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
add_filter('language_attributes', 'add_opengraph_doctype');

//Now, we're just going to add a single OG element here 

function add_opengraph_single() {
	
	if (is_single()){
		//We are not in the loop with this function, but we need to get something from the loop. 
		//Sometimes we need to get a bit weird to get the post ID. Here's another way.
		//Get an array of information about the post.
		global $post;
		//If it is a post with an ID, set the ID.
		if (is_object($postID)) $postID = $post->ID;
		
		if (class_exists('All_in_One_SEO_Pack')){
			$AIOSEOP = new All_in_One_SEO_Pack;
			$AIOSEOPDescrip = $AIOSEOP->get_post_description($post);
		}
		
		if (class_exists('WDS_OnPage')){
			$WDS_OnPage = new WDS_OnPage;
		}

		# OG Title
		
		//Get the meta data containing the information
		if (!empty($ogNativePostTitle = get_post_meta($postID, 'ogposttitle', true)){
			//If there is anything there, output it.
			$ogposttitle = $ogNativePostTitle;
		} 
		elseif (class_exists('WDS_OnPage')) {
			$ogposttitle = $WDS_OnPage->wds_title('');
		} elseif (!empty(get_post_meta($post->ID, "_aioseop_title", true))){
			$title = $AIOSEOP->internationalize(get_post_meta($post->ID, "_aioseop_title", true));
		}
		else {
			$ogposttitle = strip_tags($post->post_title);
		}
		echo '<meta property="og:title" content="' . $ogposttitle . '"/>';
		
		# OG Descrip
		if (!empty($AIOSEOPDescrip) {
			$ogdescrip = $AIOSEOPDescrip;
			?><meta property="og:description" content="<?php echo $ogdescrip; ?>" /><?php
		} elseif (!empty($WDS_OnPage->wds_metadesc())) {
			$WDS_OnPage->wds_metadesc();
		} elseif (!empty($post->post_excerpt)) {
			$theexcerptuse = get_the_excerpt();
			$theexcerptclean = strip_tags($theexcerptuse);
					
			if (strlen($theexcerptclean) > 300){
				$nativedescription = substr($theexcerptclean, 0, 295) . '...'; 
			} else {
				$nativedescription = $theexcerptclean;	
			}
					
			?><meta property="og:description" content="<?php echo $nativedescription; ?>" /><?php		
		} else {
			$thecontentforexcerpt = $post->post_content;
			$theexcerptclean = strip_tags($thecontentforexcerpt);
			$nativedescription = substr($theexcerptclean, 0, 295) . '...';
			?><meta property="og:description" content="<?php echo $nativedescription; ?>" /><?php		
		}
		
		?><meta property="og:type" content="article" /><?php
		?><meta property="og:site_name" content="<?php bloginfo('name'); ?>"/><?php
	}
}

//Add this to the head tag of your WordPress site. 
add_action('wp_head', 'add_opengraph_title');
?>