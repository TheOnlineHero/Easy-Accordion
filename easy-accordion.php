<?php
/*
Plugin Name: Easy Accordion
Plugin URI: http://wordpress.org/extend/plugins/easy-accordion/
Description: Adds an accordion slider to your wordpress site.

Installation:

1) Install WordPress 3.5.1 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/tom-m8te 

http://wordpress.org/extend/plugins/easy-accordion

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 1.0
Author: TheOnlineHero - Tom Skroza, Nicola Hibbert - http://accordionpro.nicolahibbert.com/, Andrea Cima Serniotti - http://www.madeincima.it/en/articles/resources-and-tools/easy-accordion-plugin/, Slide Deck - http://wordpress.org/extend/plugins/slidedeck-lite-for-wordpress/, The Marketing Mix
License: GPL2
*/

require_once("easy-accordion-slider.php");
require_once("easy-accordion-slides.php");
require_once("easy-accordion-path.php");
include_once (dirname (__FILE__) . '/tinymce/tinymce.php'); 

define(__EASY_ACCORDION_DEFAULT_LIMIT__, "10");

function easy_accordion_activate() {
  global $wpdb;

  $easy_accordion_table = $wpdb->prefix . "easy_accordion_slider";
  $checktable = $wpdb->query("SHOW TABLES LIKE '$easy_accordion_table'");
  if ($checktable == 0) {

    $sql = "CREATE TABLE $easy_accordion_table (
      ID mediumint(9) NOT NULL AUTO_INCREMENT, 
      accordion_name varchar(255) NOT NULL DEFAULT '',
      slide_speed mediumint(9) DEFAULT 500, 
      auto_play smallint(2) DEFAULT 0,
      pause_on_hover smallint(2) DEFAULT 0,
      theme varchar(255) NOT NULL DEFAULT 'stitch',
      rounded smallint(2) DEFAULT 0,
      enumerate_slides smallint(2) DEFAULT 1,
      header_width mediumint(9) DEFAULT 30,
      container_width mediumint(9) DEFAULT 960,
      container_height mediumint(9) DEFAULT 300,
      easing varchar(255) NOT NULL DEFAULT 'easeInOutQuart',
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (ID)
    )";

    $wpdb->query($sql); 

    $easy_accordion_slide_table = $wpdb->prefix . "easy_accordion_slide";
    $sql = "CREATE TABLE $easy_accordion_slide_table (
      SID mediumint(9) NOT NULL AUTO_INCREMENT, 
      slide_name varchar(255) NOT NULL DEFAULT '',
      background_image_url VARCHAR(255) DEFAULT '', 
			content longtext,
      secondary_content longtext,
			slide_order mediumint(9) DEFAULT 0,
			slide_id mediumint(9) NOT NULL,
			created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (SID)
    )";

    $wpdb->query($sql);

  }

  if (!is_dir(get_template_directory()."/easy_accordion_css")) {
    easy_accordion_copy_directory(EasyAccordionPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  
  } else {
    add_option("easy_accordion_current_css_file", "default.css");
  }

}

register_activation_hook( __FILE__, 'easy_accordion_activate' );

//call register settings function
add_action( 'admin_init', 'register_easy_accordion_settings' );
function register_easy_accordion_settings() {

  $msg_content = "<div class='updated'><p>Sorry for the confusion but you must <a href='".get_option("siteurl")."/wp-admin/plugin-install.php?tab=plugin-information&plugin=tom-m8te&_wpnonce=fff6cb9759&TB_iframe=true&width=640&height=876'>install and activate Tom M8te</a> before you can use Easy Accordion.</p></div>";
  if (!is_plugin_active("tom-m8te/tom-m8te.php")) {
    deactivate_plugins(__FILE__, true);
    echo($msg_content);
  } 

}

add_action('admin_menu', 'register_easy_accordion_page');
function register_easy_accordion_page() {
  add_menu_page('Easy Accordion', 'Easy Accordion', 'manage_options', 'easy_accordion/easy_accordion.php', 'easy_accordion_initial_page');
  add_submenu_page('easy_accordion/easy_accordion.php', 'Styling', 'Styling', 'update_themes', 'easy-accordion/easy-accordion-styling.php');
}

add_action('wp_ajax_easy_accordion_css_file_selector', 'easy_accordion_css_file_selector');
function easy_accordion_css_file_selector() {
  update_option("easy_accordion_current_css_file", $_POST["css_file_selection"]);
  echo(@file_get_contents(get_template_directory()."/easy_accordion_css/".$_POST["css_file_selection"]));
  die();  
}

add_action('wp_ajax_easy_accordion_tinymce', 'easy_accordion_tinymce');
/**
 * Call TinyMCE window content via admin-ajax
 * 
 * @since 1.7.0 
 * @return html content
 */
function easy_accordion_tinymce() {

    // check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
      die(__("You are not allowed to be here"));
          
    include_once( dirname( dirname(__FILE__) ) . '/easy-accordion/tinymce/window.php');
    
    die();  
}

function easy_accordion_initial_page() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');

  wp_register_script("admin-easy-accordion", plugins_url("/js/application.js", __FILE__));
  wp_enqueue_script("admin-easy-accordion");

  wp_localize_script( 'admin-easy-accordion', 'EasyAccordionAjax', array(
    "ajax_url" => admin_url('admin-ajax.php'),
    "sort_slides_url" => get_option('siteurl')."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&easy_accordion_page=slide",
  ));

  wp_register_style("admin-easy-accordion", plugins_url("/admin_css/style.css", __FILE__));
  wp_enqueue_style("admin-easy-accordion");

  if (tom_get_query_string_value("easy_accordion_page") == "") {
	  if (isset($_POST["action"])) {
	    if ($_POST["action"] == "Update") {
	      EasyAccordionSlider::update();
	    }
	    if ($_POST["action"] == "Create") {
	      EasyAccordionSlider::create();
	    }
	  } else {
      if (isset($_GET["action"]) && $_GET["action"] == "new") {
        // Set default values for the form on New action.
        if ($_POST["slide_speed"] == "") {
          $_POST["slide_speed"] = "500";
        }
        if ($_POST["header_width"] == "") {
          $_POST["header_width"] = "30";
        }
        if ($_POST["container_width"] == "") {
          $_POST["container_width"] = "960";
        }
        if ($_POST["container_height"] == "") {
          $_POST["container_height"] = "300";
        }
      }
    }
	  if ($_GET["action"] == "delete") {
	    EasyAccordionSlider::delete();
	  }
	  easy_accordion_page();
	} else if (tom_get_query_string_value("easy_accordion_page") == "slide") {
	  if (isset($_POST["action"])) {
      if ($_POST["action"] == "update_order") {
        tom_update_record_by_id("easy_accordion_slide", array("slide_order" => $_POST["slide_order"]), "SID", $_POST["SID"]);
        exit;
      }
	    if ($_POST["action"] == "Update") {
	      EasyAccordionSlides::update();
	    }
	    if ($_POST["action"] == "Create") {
	      EasyAccordionSlides::create();
	    }
	  }
	  if ($_GET["action"] == "delete") {
	    EasyAccordionSlides::delete();
	  }
	  easy_accordion_slide_page(tom_get_query_string_value("id"));
	}

  tom_add_social_share_links("http://wordpress.org/extend/plugins/easy-accordion/");
}


function easy_accordion_page(){
  ?>
  
  <div class="wrap">
  <h2>Easy Accordion <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&action=new">Add New Slider</a></h2>
  <?php

  if (isset($_GET["message"]) && $_GET["message"] != "") {
    echo("<div class='updated below-h2'><p>".$_GET["message"]."</p></div>");
  }

  if (isset($_GET["action"]) && $_GET["action"] != "delete") {
	  if ($_GET["action"] == "edit") {
	    // Display Edit Page
	    $easy_accordion_form = tom_get_row_by_id("easy_accordion_slider", "*", "ID", $_GET["id"]); ?>

	      <div class="postbox " style="display: block; ">
	      <div class="inside">
	        <form action="" method="post">
	          <?php EasyAccordionSlider::render_admin_easy_accordion_form($easy_accordion_form, "Update"); ?>
	        </form>
	      </div>
	      </div>
	  	<?php 
	  }
	  
	  if ($_GET["action"] == "new") { // Display New Page ?>
	      <div class="postbox " style="display: block; ">
	      <div class="inside">
	        <form action="" method="post">
	          <?php EasyAccordionSlider::render_admin_easy_accordion_form(null, "Create"); ?>
	        </form>
	      </div>
	      </div>

	    <?php 
	  }

  } else { ?>

    <div class="postbox " style="display: block; ">
    <div class="inside">
      <?php
	      $easy_accordions = tom_get_results("easy_accordion_slider", "*", "");
	      if (count($easy_accordions) == 0) {
	        $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&action=new";
	        tom_javascript_redirect_to($url, "<p>Start by creating a slider.</p>");
	      } else {
	        tom_generate_datatable("easy_accordion_slider", array("ID", "accordion_name"), "ID", "", array("accordion_name ASC"), __EASY_ACCORDION_DEFAULT_LIMIT__, get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php", false, true, true, true, true);   
	      }
      ?>
    </div>
    </div>

    <?php
  }
  ?>
  </div>
  <?php
}

function easy_accordion_slide_page($slider_id) {
	$slider = tom_get_row_by_id("easy_accordion_slider", "*", "ID", $slider_id);
	?>
  
  <div class="wrap">
  <h2>Easy Accordion - <?php echo($slider->accordion_name); ?></h2>
  <?php

  if (isset($_GET["message"]) && $_GET["message"] != "") {
    echo("<div class='updated below-h2'><p>".$_GET["message"]."</p></div>");
  }

  if (isset($_GET["action"]) && $_GET["action"] != "delete") {
	  if ($_GET["action"] == "edit") {
	    // Display Edit Page
	    $easy_accordion_slide_form = tom_get_row_by_id("easy_accordion_slide", "*", "SID", $_GET["id"]); 
	    ?>
	      <div class="postbox " style="display: block; ">
	      <div class="inside">
	        <form action="" method="post">
	          <?php EasyAccordionSlides::render_admin_easy_accordion_form($easy_accordion_slide_form, "Update"); ?>
	        </form>
	      </div>
	      </div>
	  	<?php 
	  }
	  
	  if ($_GET["action"] == "new") { // Display New Page ?>
	      <div class="postbox " style="display: block; ">
	      <div class="inside">
	        <form action="" method="post">
	          <?php EasyAccordionSlides::render_admin_easy_accordion_form(null, "Create"); ?>
	        </form>
	      </div>
	      </div>

	    <?php 
	  }

  } ?>
  </div>
  <?php
}

add_shortcode( 'easy-accordion', 'easy_accordion_shortcode' );

function easy_accordion_shortcode($atts) {
  $slides = tom_get_results("easy_accordion_slide", "*", "slide_id=".$atts["id"]);
  $slider = tom_get_row_by_id("easy_accordion_slider", "*", "ID", $atts["id"]);
  ?>
  <div class="easy-accordion" id="easy_accordion_<?php echo($atts['id']); ?>">
    <ol>
      <?php foreach ($slides as $slide) { ?>
        <li>
          <h2><span><?php echo($slide->slide_name); ?></span></h2>
              <div>
                  <div class="primary">
                      <?php echo(wpautop($slide->content)); ?>
                  </div>
                  <?php if ($slide->secondary_content != "") { ?>
                    <div class="secondary">
                      <?php echo(wpautop($slide->secondary_content)); ?>
                    </div>
                  <?php } ?>
              </div>
          </li>
      <?php } ?>
    </ol>
    <noscript>
      <p>Please enable JavaScript to get the full experience.</p>
    </noscript>
  </div>

  <?php
}


add_action('wp_head', 'add_easy_accordion_js_and_css');
function add_easy_accordion_js_and_css() { 
  wp_enqueue_script('jquery');

  wp_register_script("jquery-easing", plugins_url("/js/jquery.easing.1.3.js", __FILE__));
  wp_enqueue_script("jquery-easing");

  wp_register_script("easy-accordion", plugins_url("/js/liteaccordion.jquery.js", __FILE__));
  wp_enqueue_script("easy-accordion");

  wp_register_script("easy-accordion-app", plugins_url("/js/application.js", __FILE__));
  wp_enqueue_script("easy-accordion-app");

  wp_register_style("easy-accordion", get_template_directory_uri().'/easy_accordion_css/'.get_option("easy_accordion_current_css_file"));
  wp_enqueue_style("easy-accordion");


  ?>
  <script language="javascript">
      jQuery(function() {
        <?php
          $sliders = tom_get_results("easy_accordion_slider", "*", "");
          foreach ($sliders as $slider) { ?>
            jQuery("#easy_accordion_<?php echo($slider->ID); ?>").liteAccordion({
              onTriggerSlide : function() {
                  this.find('figcaption').fadeOut();
              },
              onSlideAnimComplete : function() {
                  this.find('figcaption').fadeIn();
              },
              slideSpeed : <?php echo($slider->slide_speed); ?>, 
              autoPlay : <?php echo($slider->auto_play); ?>,
              pauseOnHover : <?php echo($slider->pause_on_hover); ?>,
              theme : '<?php echo($slider->theme); ?>',
              rounded : <?php echo($slider->rounded); ?>,
              enumerateSlides : <?php echo($slider->enumerate_slides); ?>,
              headerWidth: <?php echo($slider->header_width); ?>,
              containerWidth: <?php echo($slider->container_width); ?>,
              containerHeight: <?php echo($slider->container_height); ?>,
              easing : '<?php echo($slider->easing); ?>'
            }).find('figcaption:first').show(); 
          <?php } 
        ?>
      });
  </script>
  <?php
} 

// Copy directory to another location.
function easy_accordion_copy_directory($src,$dst) { 
    $dir = opendir($src); 
    try{
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    easy_accordion_copy_directory($src . '/' . $file,$dst . '/' . $file); 
                } else { 
                    copy($src . '/' . $file,$dst . '/' . $file);
                } 
            }   
        }
        closedir($dir); 
    } catch(Exception $ex) {
        return false;
    }
    return true;
}

?>