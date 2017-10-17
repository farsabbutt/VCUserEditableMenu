!function($) {
    jQuery("#sortable_items").sortable();
    jQuery("#sortable_items").disableSelection();
    
    
     jQuery("#sortable_items").sortable(
            {stop: function( event, ui ) {
               // console.log(jQuery(ui.item).text());
                //console.log(jQuery(ui.item).data('pageid'));
                jQuery('input[name="uem_items"]').val("");
                jQuery("#vc_custom_sortable li").each(function(i,v){
//                    if(i == (jQuery("#vc_custom_sortable li").length-1))
//                        return;
                        
                    console.log(i);
                    console.log(jQuery(v).text());
                    jQuery('input[name="uem_items"]').val(jQuery('input[name="uem_items"]').val() + jQuery(v).data('pageid')+','+ jQuery(v).text()+ ';');
                });
                
                
            }         
            });
    
    
}(window.jQuery);