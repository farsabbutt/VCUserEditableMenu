!function($) {
    
var $sortable_items = jQuery('input[name="uem_items"]').val();    
var re = /([0-9,a-zA-Z ]+)/g;
var m;

do {
    m = re.exec($sortable_items);
    if (m) {
        
        console.log(m[1]);
        var v = m[1].split(',');
        jQuery("#vc_custom_sortable").append('<li data-pageid="'+v[0]+'">'+v[1]+'</li>');
    }
} while (m);
   
    jQuery("#vc_custom_sortable").sortable(
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
    jQuery("#vc_custom_sortable").disableSelection();

}(window.jQuery);