 jQuery(document).ready(function(){	
	function IsEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	} 
	// jQuery(function() {
		// jQuery( "#accordion" ).accordion();
	// });
	jQuery('.tips-flexslider').flexslider({
		    animation: "slide"
		});
	jQuery('.wp-overview-description').click(function(){
			jQuery('.wp-overview-content').children().hide();
			if(jQuery('.wp-overview-description-content').hasClass('invisible')){
				jQuery('.wp-overview-description-content').removeClass('invisible');
				jQuery('.wp-overview-description-content').addClass('visible');
			}
			jQuery('.wp-overview-description-content').show();
		});
		jQuery('.wp-overview-upgrade').click(function(){
			jQuery('.wp-overview-content').children().hide();
			if(jQuery('.wp-overview-upgrade-content').hasClass('invisible')){
				jQuery('.wp-overview-upgrade-content').removeClass('invisible');
				jQuery('.wp-overview-upgrade-content').addClass('visible');
			}
			jQuery('.wp-overview-upgrade-content').show();
		});
		jQuery('.wp-overview-metrics').click(function(){
			jQuery('.wp-overview-content').children().hide();
			if(jQuery('.wp-overview-metrics-content').hasClass('invisible')){
				jQuery('.wp-overview-metrics-content').removeClass('invisible');
				jQuery('.wp-overview-metrics-content').addClass('visible');
			}
			jQuery('.wp-overview-metrics-content').show();
		});
		// filters by level, alphabetical and chronological
		jQuery('#fs-users-log-level').click(function(){
			jQuery('.fs-users-log-level').each(function(){
				var curr = jQuery(this).html();
				var obj = jQuery(this).parent();
				jQuery('.fs-users-log-level').each(function(){
					var comp = jQuery(this).html();
					var comp_obj = jQuery(this).parent();
					if (comp >= curr)
					{
						obj.before(comp_obj);
					}
				});
			});
		});
		jQuery('#fs-users-log-chronological').click(function(){
			jQuery('.fs-users-log').each(function(){
				var obj = jQuery(this);
				jQuery('.fs-users-log').each(function(){
					var comp = jQuery(this);
					var chron1 = parseInt(obj.find('.fs-users-activity').val());
					var chron2 = parseInt(comp.find('.fs-users-activity').val());
					if( chron1 < chron2){
						obj.before(comp);
						return true;
					}
				});
			});
		});
		jQuery('#fs-users-log-alphabetical').click(function(){
			jQuery('.fs-users-log-name').each(function(){
				var curr = jQuery(this).html();
				var obj = jQuery(this).parent();
				jQuery('.fs-users-log-name').each(function(){
					var comp = jQuery(this).html();
					var comp_obj = jQuery(this).parent();
					if (curr > comp)
					{
						obj.before(comp_obj);
					}
				});
			});
			
		});
		jQuery('.fs-users-log').click(function(){
			var id = jQuery(this).attr('id').replace('fs-users-log-','');
			jQuery('.fs-users-name-activities-'+id).toggle();
		});
		
			var fs_api_base = jQuery('#fs-api-base').val();
	
		
		jQuery('#fs-register-admin-submit').click(function(){
			var fs_api_base = jQuery('#fs-api-base').val();
			var link = jQuery('.fs-siteurl').val();
			//var code = jQuery('.fs-secret-key').val();
			var first_name = jQuery('.fs-input-first-name').val();
			var last_name = jQuery('.fs-input-last-name').val();
			var email = jQuery('.fs-input-email').val();
			var password = jQuery('.fs-input-password').val();
		
			
			if(!jQuery('#rewardial-accept-claim').is(':checked')) jQuery(".fs-register-messages").html('<div class="error">Please accept our claim!</div>');
			else if(!IsEmail(email)) jQuery(".fs-register-messages").html('<div class="error">The email address is invalid!</div>');
			 else if(password.length == 0) jQuery(".fs-register-messages").html('<div class="error">Please insert password!</div>'); 
			 else if(password.length < 6) jQuery(".fs-register-messages").html('<div class="error">Your password should have more than 5 characters!</div>');
			  else if(first_name.length == 0) jQuery(".fs-register-messages").html('<div class="error">Please insert first name!</div>'); 
			  else if(last_name.length == 0) jQuery(".fs-register-messages").html('<div class="error">Please insert last name!</div>'); 
			  else {
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action:'connect_rewardial'},
					success: function(data){
						var code = data;
						if(code == 'error'){
							jQuery(".fs-register-messages").html('<div class="error">Invalid data or website inactive. Please try again.</div>'); 
						}else{
							jQuery.ajax({
								url: fs_api_base +'/admin_register',
								data: {"link":link,"code":code,"first_name":first_name,"last_name":last_name,"email":email,"password":password},
								type: "post",
								success:function(r){
									var response = jQuery.parseJSON(r);
									if(response.status == 'success'){
										jQuery('.fs-register-messages').html('<div class="fs-success">Account created. You can now login on <a href="http://rewardial.com" target="_blank">Rewardial</a> and customize your plugin!</div>');
										window.location = "/wp-admin/admin.php?page=fs-settings";
										
									}else if(response.status == 'existent'){
										jQuery('.fs-register-messages').html('<div class="error">This email is already used for an account. Please use the Add your account form to make this account an admin.</div>');
										jQuery.ajax({
											url: ajaxurl,
											type: 'POST',
											data: {action:'disconnect_rewardial'},
											success: function(data){
												
											}
										});
									}
								}
							});
						}
					}
				});
			}
		});
		jQuery('#fs-add-admin-submit').click(function(){
			var email = jQuery('.fs-add-email').val();
			var password = jQuery('.fs-add-password').val();
			var fs_api_base = jQuery('#fs-api-base').val();
			var link = jQuery('.fs-siteurl').val();
			if(!jQuery('#rewardial-accept-claim').is(':checked')) jQuery(".fs-add-account-messages").html('<div class="error">Please accept our claim!</div>');
			else{
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action:'connect_rewardial'},
					success: function(data){
						var code = data;
						if(code == 'error'){
							jQuery('.fs-add-account-messages').html('<div class="error">Invalid account or website inactive. Please try again.</div>');
						}else{
							jQuery.ajax({
								url: fs_api_base+'/add_admin_account',
								data: {"link":link,"code":code,"email":email,"password":password},
								type:"post",
								success:function(r){
									var response = jQuery.parseJSON(r);
									if(response.status == 'success'){
										jQuery('.fs-add-account-messages').html('<div class="fs-success">Account associated. You can now login on <a href="http://rewardial.com" target="_blank">Rewardial</a> and customize your plugin!</div>');
										jQuery('.fs-add-email').val('');
										jQuery('.fs-add-password').val('');
										window.location = "/wp-admin/admin.php?page=fs-settings";
									}else{
										jQuery('.fs-add-account-messages').html('<div class="error">Invalid account or website inactive. Please try again.</div>');
										jQuery.ajax({
											url: ajaxurl,
											type: 'POST',
											data: {action:'disconnect_rewardial'},
											success: function(data){
												
											}
										});
									}
								}
							});
						}
					}
				});
			}
		});
	//jQuery('.focused-stamps_page_fs-quests .wrap').tabs();
	jQuery('.welcome-skip').click(function(){
		jQuery(this).parent().hide();
		jQuery('.settings-overlay').hide();
	});
		
	jQuery('.welcome-continue').click(function(){
		jQuery(this).parent().hide();
		jQuery('.settings-overlay').hide();
	});
		
	jQuery(document).keyup(function(e) {

	  if (e.keyCode == 27) { 
			jQuery('.welcome-skip').parent().hide();
			jQuery('.settings-overlay').hide();
		  }
	  
	});
	
	jQuery('#rewardial-reset-blog').click(function(){
		
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action:'reset_blog_options'},
			success: function(data){
				window.location.replace(data);
			}
		});
	});
	
	jQuery('.rwd-order-status-button').each(function(){
		jQuery(this).click(function(){

			var order_id = jQuery(this).attr('id').replace('rwd-order-status-','');
			var value_selected = jQuery('#rwd-order-status-select-'+order_id).val();
			var link = jQuery('.fs-siteurl').val();
			
			jQuery.ajax({
				url: ajaxurl,
				data: {"action":'change_order_status',"order_id":order_id,"status":value_selected},
				type:"post",
				success:function(r){
					var response = jQuery.parseJSON(r);
					if(response.status == 200){
						jQuery('.rwd-order-status-message').html('');
						jQuery('#rwd-order-status-message-'+order_id).html('<span class="rwd-success">'+response.message+'</span>');
						
					}else{
						jQuery('.rwd-order-status-message').html('');
						jQuery('#rwd-order-status-message-'+order_id).html('<span class="rwd-error">'+response.message+'</span>');
					}
				}
			});
		});
	});
	
	jQuery('#rwd-orders-search-submit').click(function(){
		
		var search_val = jQuery('#rwd-orders-search').val();
		var link = jQuery('#rwd-orders-search-link').val();
		var final_link = link+'&rwd_search='+search_val;
		
		window.location.replace(final_link);
		
	});
	



	
	//jQuery('.toplevel_page_fs-overview .wp-submenu li:nth-child(4) a').attr('href',jQuery('#fs_api_base_hidden').val()).attr('target','_blank');
});