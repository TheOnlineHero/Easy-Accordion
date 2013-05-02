jQuery(function() {
	if (jQuery( "#slide_column" ).length > 0) {
    jQuery( "#slide_column table tbody" ).sortable({
      update: function( event, ui ) {
        jQuery("#slide_column table tbody tr").each(function() {
			    jQuery.ajax({
			      type: 'POST',
			      url: EasyAccordionAjax.sort_slides_url,
			      data: {SID: jQuery.trim(jQuery(this).find(".sid").html()), slide_order: jQuery(this).index(), action: "update_order"}
			    });
			  });

        jQuery("table.data tr").removeClass("even").removeClass("odd");
        jQuery("table.data tr:odd").addClass("odd");
        jQuery("table.data tr:even").addClass("even");
      }
    });    
  }

  jQuery("#css_file_selection").change(function() {
    jQuery.ajax({
      type: "post",url: EasyAccordionAjax.ajax_url,data: {css_file_selection: jQuery("#css_file_selection").val(), action: "easy_accordion_css_file_selector"},
      success: function(response){
        jQuery("#css_content").html(response);
      }
    });
  });

  jQuery("table.data tr:odd").addClass("odd");
  jQuery("table.data tr:even").addClass("even");

});