<?php

	// Set some theme specific variables for the options panel
	$childthemename = "Prefixed";
	$childshortname = "pf";
	$childoptions = array();

function childtheme_options() {
  global $childthemename, $childshortname, $childoptions;
 
	//Create array to store the categories to be used in the drop-down select box. 
	$categories_obj = get_categories('hide_empty=0');
	$categories = array();
	foreach ($categories_obj as $cat) {
		$categories[$cat->cat_ID] = $cat->cat_name;
	}
	
	$childoptions = array(
	//Change link color.
	array(	"name" => __('Featured Head Story','thematic'),
			"desc" => __('Find the post ID by mousing over the post in the All Posts section.', 'thematic'),
			"id" => "head_post",
			"std" => "12",
			"type" => "text"
		),
		
 
	array( 	"name" => __('Featured Category','thematic'),
			"desc" => __('A category of posts to be featured on the front page.','thematic'),
			"id" => "feature_cat",
			"std" => $default_cat,
			"type" => "select",
			"options" => $categories
		),
		
	array( 	"name" => __('Slider Category','thematic'),
			"desc" => __('Type in the Category ID numbers of the categories you want featured. Seperate with commas, no spaces. Find after tag_id in the URL.','thematic'),
			"id" => "slider_cat",
			"std" => "1",
			"type" => "text"
		),
		
	array( 	"name" => __('Show RSS Social Icon','thematic'),
			"desc" => __('Check to display.','thematic'),
			"id" => "social_rss",
			"std" => "false",
			"type" => "checkbox"
		),
		
	array( 	"name" => __('Show Twitter Social Icon','thematic'),
			"desc" => __('Check to display.','thematic'),
			"id" => "social_twit",
			"std" => "false",
			"type" => "checkbox"
		),		

	array( 	"name" => __('Twitter Username','thematic'),
			"desc" => __('Enter the Twitter username to work with the social icon.','thematic'),
			"id" => "social_twit_un",
			"std" => "",
			"type" => "text"
		),
		
	array( 	"name" => __('Show Facebook Social Icon','thematic'),
			"desc" => __('Check to display.','thematic'),
			"id" => "social_fb",
			"std" => "false",
			"type" => "checkbox"
		),		

	array( 	"name" => __('Facebook Username','thematic'),
			"desc" => __('Enter the Facebook page username to work with the social icon.','thematic'),
			"id" => "social_fb_un",
			"std" => "",
			"type" => "text"
		),

	array( 	"name" => __('Show Write for Us Icon','thematic'),
			"desc" => __('Check to display.','thematic'),
			"id" => "social_contribute",
			"std" => "false",
			"type" => "checkbox"
		),		

	array( 	"name" => __('Additional Social Media Icons','thematic'),
			"desc" => __('Enter the URLs of the social media icons seperate with commas, no spaces. Should be 32x32px.','thematic'),
			"id" => "social_addl_icons",
			"std" => "",
			"type" => "text"
		),

	array( 	"name" => __('Additional Social URLs','thematic'),
			"desc" => __('Enter social network URLs in order with the icons above. Comma seperated, no spaces.','thematic'),
			"id" => "social_addl_urls",
			"std" => "",
			"type" => "text"
		),

	array ( "name" => __('Enter default site thumbnail','thematic'),
			"desc" => __('Enter a URL for a default thumbnail for the website. This will be used by Facebook and other Open Graph applications.','thematic'),
			"id" => "fb_thumb",
			"std" => "",
			"type" => "text"
		),
		
	array ( "name" => __('Facebook User IDs','thematic'),
			"desc" => __('Enter the Facebook User IDs that administrate this page. Comma seperated, no spaces.','thematic'),
			"id" => "fb_IDs",
			"std" => "",
			"type" => "text"
		),
	
	array ( "name" => __('Facebook App ID','thematic'),
			"desc" => __('If you are using a Facebook app to help admin your page, enter the ID here.','thematic'),
			"id" => "fb_app_ID",
			"std" => "",
			"type" => "text"
		),

	array ( "name" => __('Facebook OpenGraph Description','thematic'),
			"desc" => __('This field contains the text that will show up below the site name when the homepage is shared on Facebook. No longer than 300 characters.','thematic'),
			"id" => "fb_og_descrip",
			"std" => "",
			"type" => "text"
		)
		
		
 );
}

add_action('init', 'childtheme_options');

//Make a theme options page
function childtheme_add_admin() {

	global $childthemename, $childshortname, $childoptions;
	
	if ($_GET['page'] == basename(__FILE__) ) {
		if ( 'save' == $_REQUEST['action'] ) {
			//protect against request forgery
			check_admin_referer('childtheme-save');
			//save options
			foreach ($childoptions as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ] );
				} else {
					delete_option( $value['id'] );
				}
			}
			
			header("Location: themes.php?page=options.php&saved=true");
			die;
			
		} else if ( 'reset' == $_REQUEST['action'] ) {
			//protext against request forgery
			check_admin_referer('childtheme-reset');
			//delete the options
			foreach ($childoptions as $value) {
				delete_option( $value['id'] ); }
				
				header("Location: themes.php?page=options.php&reset=true");
				die;
				
			}
		}
		add_theme_page($childthemename." Options", "$childthemename Options", 'edit_themes', basename(__FILE__), 'childtheme_admin');
}

add_action('admin_menu', 'childtheme_add_admin');



function childtheme_admin() {

  global $childthemename, $childshortname, $childoptions;

  // Saved or Updated message
  if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade">
<p><strong>'.$childthemename.' settings saved.</strong></p></div>';
  if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade">
<p><strong>'.$childthemename.' settings reset.</strong></p></div>';

  // The form
  ?>

  <div class="wrap">
  <h2><?php echo $childthemename; ?> Options</h2>

  <form method="post">

  <?php wp_nonce_field('childtheme-save'); ?>
  <table class="form-table">

  <?php foreach ($childoptions as $value) {

    // Output the appropriate form element
    switch ( $value['type'] ) {
      
      case 'text':
      ?>
      <tr valign="top">
        <th scope="row"><?php echo $value['name']; ?>:</th>
        <td>
          <input name="<?php echo $value['id']; ?>" 
                 id="<?php echo $value['id']; ?>" 
                 type="text" 
                 value="<?php echo stripslashes(get_option( $value['id'], 
$value['std'] )); ?>" 
          />
          <?php echo $value['desc']; ?>
        </td>
      </tr>
      <?php
      break;

      case 'select':
      ?>
      <tr valign="top">
        <th scope="row"><?php echo $value['name']; ?></th>
        <td>
          <select name="<?php echo $value['id']; ?>" 
                  id="<?php echo $value['id']; ?>">
            <option value="">--</option>
            <?php foreach ($value['options'] as $key=>$option) {
              if ($key == get_option($value['id'], $value['std']) ) {
                $selected = "selected=\"selected\"";
              } else {
                $selected = "";
              }
            ?>
            <option value="<?php echo $key ?>" <?php echo $selected ?>>
<?php echo $option; ?></option>
          <?php } ?>
          </select>
          <?php echo $value['desc']; ?>
        </td>
      </tr>
      <?php
      break;

      case 'textarea':
      $ta_options = $value['options'];
      ?>
      <tr valign="top">
        <th scope="row"><?php echo $value['name']; ?>:</th>
        <td>
          <?php echo $value['desc']; ?>
          <textarea name="<?php echo $value['id']; ?>" 
                    id="<?php echo $value['id']; ?>" 
                    cols="<?php echo $ta_options['cols']; ?>" 
                    rows="<?php echo $ta_options['rows']; ?>"><?php
            echo stripslashes(get_option($value['id'], $value['std']));
          ?></textarea>
        </td>
      </tr>
      <?php
      break;

      case "radio":
      ?>
      <tr valign="top">
        <th scope="row"><?php echo $value['name']; ?>:</th>
        <td>
          <?php foreach ($value['options'] as $key=>$option) {
            if ($key == get_option($value['id'], $value['std']) ) {
              $checked = "checked=\"checked\"";
            } else {
              $checked = "";
            }
            ?>
            <input type="radio" 
                   name="<?php echo $value['id']; ?>" 
                   value="<?php echo $key; ?>" 
                   <?php echo $checked; ?> 
            /><?php echo $option; ?>
            <br />
          <?php } ?>
          <?php echo $value['desc']; ?>
        </td>
      </tr>
      <?php
      break;

      case "checkbox":
      ?>
      <tr valign="top">
        <th scope="row"><?php echo $value['name']; ?></th>
        <td>
          <?php
          if(get_option($value['id'])){
            $checked = "checked=\"checked\"";
          } else {
            $checked = "";
          }
          ?>
          <input type="checkbox" 
                 name="<?php echo $value['id']; ?>" 
                 id="<?php echo $value['id']; ?>" 
                 value="true" 
                 <?php echo $checked; ?> 
          />
          <?php echo $value['desc']; ?>
        </td>
      </tr>
      <?php
      break;

      default:
      break;
    }
  }
  ?>

  </table>

  <p class="submit">
    <input name="save" type="submit" value="Save changes" class="button-primary" />
    <input type="hidden" name="action" value="save" />
  </p>

  </form>

  <form method="post">
    <?php wp_nonce_field('childtheme-reset'); ?>
    <p class="submit">
      <input name="reset" type="submit" value="Reset" />
      <input type="hidden" name="action" value="reset" />
    </p>
  </form>

  <p><?php _e('For more information ... ', 'thematic'); ?></p>

  <?php
} // end function

?>