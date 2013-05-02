<?php

final class EasyAccordionSlides {

  public static function array_validation_rules() {
    return array(
      "slide_name" => "required"
    );
  }

  public static function update() {

    $_POST["content"] = str_replace('\"', "\"", $_POST["content"]);
    $_POST["content"] = str_replace("\'", '\'', $_POST["content"]);

    $form_valid = tom_validate_form(EasyAccordionSlides::array_validation_rules());

    if ($form_valid) {
      $valid = tom_update_record_by_id("easy_accordion_slide", 
      tom_get_form_query_strings("easy_accordion_slide", array("created_at", "updated_at", "slide_id"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "SID", $_POST["SID"]);
      
      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Update Complete&action=edit&id=".$_POST["SID"]."&easy_accordion_page=slide";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Update Complete&action=edit&id=".$_POST["slide_id"];
        }
        
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
  }
  public static function create() {
    
    $_POST["content"] = str_replace('\"', "\"", $_POST["content"]);
    $_POST["content"] = str_replace("\'", '\'', $_POST["content"]);

    $_POST["secondary_content"] = str_replace('\"', "\"", $_POST["secondary_content"]);
    $_POST["secondary_content"] = str_replace("\'", '\'', $_POST["secondary_content"]);

    $form_valid = tom_validate_form(EasyAccordionSlides::array_validation_rules());

    if ($form_valid) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = tom_insert_record("easy_accordion_slide", 
        tom_get_form_query_strings("easy_accordion_slide", array("SID", "created_at", "updated_at"), array("created_at" => $current_datetime, "slide_order" => 9999999)));

      if ($valid) {
        $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&action=edit&id=".$_POST["slide_id"]."&message=Record Created";
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }

    }
  }
  public static function delete() {
    // Delete record by id.
    $slide = tom_get_row_by_id("easy_accordion_slide", "*", "SID", $_GET["id"]);
    $slider_id = $slide->slide_id;
    tom_delete_record_by_id("easy_accordion_slide", "SID", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=easy_accordion/easy_accordion.php&message=Record Deleted&action=edit&id=".$slider_id;
    tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
  }

  public static function render_admin_easy_accordion_form($instance, $action) { 
    ?>
    <div id="setting_column">
      <?php
        
        tom_add_form_field($instance, "hidden", "SID *", "SID", "SID", array(), "span", array("class" => "hidden"));
        tom_add_form_field($instance, "hidden", "Slider ID *", "slide_id", "slide_id", array(), "span", array("class" => "hidden"));
        tom_add_form_field($instance, "text", "Name *", "slide_name", "slide_name", array("class" => "text"), "p", array());
        
        echo("<label for=''>Content:</label>");
        wp_editor( $instance->content, "content" );

        echo("<label for=''>Secondary Content:</label>");
        wp_editor( $instance->secondary_content, "secondary_content" );
      ?>
      <input type="hidden" name="easy_accordion_page" value="slide" />
      <input type="hidden" name="action" value="<?php echo($action); ?>" />
      <p>
        <input type="submit" name="sub_action" value="<?php echo($action); ?>" /> 
        <?php if ($instance != null) { ?>
          <input type="submit" name="sub_action" value="Save and Finish" />
        <?php } ?>
      </p>
    </div>
    <?php
  }

}

?>