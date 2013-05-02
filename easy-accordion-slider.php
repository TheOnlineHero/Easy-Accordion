<?php
final class EasyAccordionSlider {

  public static function array_validation_rules() {
    return array(
      "accordion_name" => "required",
      "slide_speed" => "required integer",
      "header_width" => "required integer",
      "container_width" => "required integer",
      "container_height" => "required integer"
    );
  }

	public static function update() {
    $form_valid = tom_validate_form(EasyAccordionSlider::array_validation_rules());

		if ($form_valid) {

      $valid = tom_update_record_by_id("easy_accordion_slider", 
      tom_get_form_query_strings("easy_accordion_slider", array("created_at", "updated_at"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "ID", $_POST["ID"]);
      
      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Update Complete&action=edit&id=".$_POST["ID"]."";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Update Complete";
        }
        
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
	}
	public static function create() {
    $form_valid = tom_validate_form(EasyAccordionSlider::array_validation_rules());

    if ($form_valid) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = tom_insert_record("easy_accordion_slider", 
        tom_get_form_query_strings("easy_accordion_slider", array("ID", "created_at", "updated_at"), array("created_at" => $current_datetime)));

      if ($valid) {
        global $wpdb;
        $slider_id = $wpdb->insert_id;
        tom_insert_record("easy_accordion_slide", 
          array("slide_name" => "Slide 1",
                "slide_id" => $slider_id, 
                "created_at" => $current_datetime
               )
        );

        $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&action=edit&id=".$slider_id."&message=Record Created";
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }

    }
	}
	public static function delete() {
	  // Delete record by id.
    tom_delete_record_by_id("easy_accordion_slider", "ID", $_GET["id"]);
    tom_delete_record_by_id("easy_accordion_slide", "slide_id", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Record Deleted";
    tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
	}

	public static function render_admin_easy_accordion_form($instance, $action) { ?>
    <div id="setting_column">
	  <?php
		  tom_add_form_field($instance, "hidden", "ID *", "ID", "ID", array(), "span", array("class" => "hidden"));
		  tom_add_form_field($instance, "text", "Name *", "accordion_name", "accordion_name", array("class" => "text"), "p", array());

      tom_add_form_field($instance, "text", "Slide Speed *", "slide_speed", "slide_speed", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "checkbox", "Play", "auto_play", "auto_play", array("class" => "checkbox"), "p", array(), array("1" => "Auto"));
      tom_add_form_field($instance, "checkbox", "On Hover", "pause_on_hover", "pause_on_hover", array("class" => "checkbox"), "p", array(), array("1" => "Pause"));
      tom_add_form_field($instance, "select", "Theme *", "theme", "theme", array("class" => "select"), "p", array(), array("stitch" => "Stitch", "light" => "Light", "dark" => "Dark"));
      tom_add_form_field($instance, "checkbox", "Rounded", "rounded", "rounded", array("class" => "checkbox"), "p", array(), array("1" => "On"));
      tom_add_form_field($instance, "checkbox", "Enumerate Slides", "enumerate_slides", "enumerate_slides", array("class" => "checkbox"), "p", array(), array("1" => "On"));
      tom_add_form_field($instance, "text", "Header Width (px) *", "header_width", "header_width", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "Container Width (px) *", "container_width", "container_width", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "Container Height (px) *", "container_height", "container_height", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "select", "Easing *", "easing", "easing", array("class" => "select"), "p", array(), 
        array(
          "swing" => "Swing",
          "easeInQuad" => "Ease In Quad",
          "easeOutQuad" => "Ease Out Quad",
          "easeInOutQuad" => "Ease In Out Quad",
          "easeInCubic" => "Ease In Cubic",
          "easeOutCubic" => "Ease Out Cubic",
          "easeInOutCubic" => "Ease In Out Cubic",
          "easeInQuart" => "Ease In Quart",
          "easeOutQuart" => "Ease Out Quart",
          "easeInOutQuart" => "Ease In Out Quart",
          "easeInQuint" => "Ease In Quint",
          "easeOutQuint" => "Ease Out Quint",
          "easeInOutQuint" => "Ease In Out Quint",
          "easeInSine" => "Ease In Sine",
          "easeOutSine" => "Ease Out Sine",
          "easeInOutSine" => "Ease In Out Sine",
          "easeInExpo" => "Ease In Expo",
          "easeOutExpo" => "Ease Out Expo",
          "easeInOutExpo" => "Ease In Out Expo",
          "easeInCirc" => "Ease In Circ",
          "easeOutCirc" => "Ease Out Circ",
          "easeInOutCirc" => "Ease In Out Circ",
          "easeInElastic" => "Ease In Elastic",
          "easeOutElastic" => "Ease Out Elastic",
          "easeInOutElastic" => "Ease In Out Elastic",
          "easeInBack" => "Ease In Back",
          "easeOutBack" => "Ease Out Back",
          "easeInOutBack" => "Ease In Out Back",
          "easeInBounce" => "Ease In Bounce",
          "easeOutBounce" => "Ease Out Bounce",
          "easeInOutBounce" => "Ease In Out Bounce"
        ));

	  ?>
    <input type="hidden" name="action" value="<?php echo($action); ?>" />
	  <p><input type="submit" name="sub_action" value="<?php echo($action); ?>" /> <?php if ($instance != null) { ?><input type="submit" name="sub_action" value="Save and Finish" /><?php } ?></p>
    </div>
    <div id="slide_column">
      <?php if ($action == "Update") { ?>
        <h2>Slides <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&action=new&easy_accordion_page=slide&slide_id=<?php echo($instance->ID); ?>">Add New Slide</a></h2>
  	    <?php tom_generate_datatable("easy_accordion_slide", array("SID", "slide_name"), "SID", "slide_id = ".$instance->ID, array("slide_order ASC"), "", get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&easy_accordion_page=slide", false, true, true);
      } ?>
    </div>
    <?php
	}

}

?>