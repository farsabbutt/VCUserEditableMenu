!function($) {

   
    
    jQuery(document).on('click', "#vc_add_page", function(){
        var $title = jQuery('input[name="add_new_page"]').val();
        var $base_url = jQuery('input[name="base_url"]').val();
        jQuery.post(
                ajaxurl,
                {
                    'action': 'vc_uem_add_page',
                    'data': {title: $title,
                             base_url: $base_url}
                },
        function (response) {
            //alert('The server responded: ' + response);
            var url = window.location.hostname + '/wp/wp-content/plugins/vc-user-editable-menu/assets/img/icon_close.png';
            console.log(url);
            var resp = response;
            jQuery("#vc_custom_sortable").append('<li data-pageid="'+resp.id+'">'+resp.title+'<img src="'+url+'" />'+'</li>');
            jQuery("#vc_custom_sortable").sortable();
            jQuery("#vc_custom_sortable").disableSelection();
            jQuery('input[name="uem_items"]').val(jQuery('input[name="uem_items"]').val() + resp.id + ','+ resp.title + ';');
            
        },'json'
        );
    });
    
    
    jQuery(document).on('dblclick', '#vc_custom_sortable li', function(){
         var $this = this;
       var $id = jQuery(this).data('pageid');
       jQuery.post(
                ajaxurl,
                {
                    'action': 'vc_uem_remove_page',
                    'data': {id: $id}
                },
        function (response) {
        
            //var resp = response;
            jQuery($this).remove();
            
            jQuery('input[name="uem_items"]').val("");
                jQuery("#vc_custom_sortable li").each(function(i,v){
                    jQuery('input[name="uem_items"]').val(jQuery('input[name="uem_items"]').val() + jQuery(v).data('pageid')+','+ jQuery(v).text()+ ';');
                });
            
            jQuery("#vc_custom_sortable").sortable();
            jQuery("#vc_custom_sortable").disableSelection();
            
            
        },'json'
        );
    });
    
    
    jQuery(document).on('click', ".remove_item", function(){
       
     
    });
    
    
    
}(window.jQuery);