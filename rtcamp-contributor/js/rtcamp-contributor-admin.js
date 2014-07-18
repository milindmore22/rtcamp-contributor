jQuery(document).ready(function(){
	
	jQuery( ".tag" ).autocomplete({
		source:function( request, response ) { 
		    jQuery.getJSON( rtcamp_contributor_object.ajaxurl + "?callback=?&action=rtcamp_contributor_submit", request, function( data ) {  
		        response( jQuery.map( data, function( item ) {
		            jQuery.each( item, function( i, val ) {
		                val.label = val.whatever; // build result for autocomplete from suggestion array data
		            } );
		            return item;
		        } ) );
		  });  
		},
		select: function( event, ui ) {
			
			var userid=ui.item.id;
			var username=ui.item.value;
			
			var n = jQuery("input[name^='rtcamp_contributor']").length;
			var array = jQuery("input[name^='rtcamp_contributor']");
			
			if(n==0){ // check for first value
					jQuery(".tagcloud").append("<div class='tagval'>"+username+"<span class='removebox'></span><input type='hidden' name='rtcamp_contributor[]' value='"+userid+"'></div>");
					jQuery(this).val('');
			}else{
				var card_value=[]; //create array to collect values in single array
				for(i=0;i<n;i++)
				{
					card_value.push(array.eq(i).val()); // push each value in array
				}
				if(jQuery.inArray(userid,card_value)!="-1"){ // check if value is present in array
						jQuery(this).val('');
						alert("user aleready present !"); // if user already present return alert box
						
				}else{
					//else add it to tagcloud
					jQuery(".tagcloud").append("<div class='tagval'>"+username+"<span class='removebox'></span><input type='hidden' name='rtcamp_contributor[]' value='"+userid+"'></div>")
					jQuery(this).val('');
				}
				
			}
			return false;
		}
	});
	
	jQuery(".removebox").live('click',function(){
		jQuery(this).parent().remove();
	});
	
});