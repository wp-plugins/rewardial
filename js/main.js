// load the whole plugin through ajax
var wp_url = jQuery('#fs-plugin-url').val()+'/';
jQuery(window).load(function() {

		jQuery.ajax({
			url: fs_ajax,
			type: 'post',
			data: {action: 'add_rewardial_plugin'},
			beforeSend:function(){
				jQuery('#rewardial-plugin-loader img').show();
			},
			complete:function(response){
				jQuery('body').append(response.responseText);
				jQuery('#rewardial-plugin-loader img').hide();
				rewardial_actions();
			}
		});
		
});	
function rewardial_actions(){
	// this function loads the entire plugin in the page after the page is fully loaded



	var ajaxurl = jQuery('#ajaxurl-link').val();

// var webroot_url = 'http://rwd.andreil.complimentmedia.net/img';
var webroot_url = 'http://www.rewardial.com/img';

var img_url = webroot_url+'/uploads';
var my_code = jQuery('#fs-code').val();
var my_time = jQuery('#fs-time').val();
var my_link = jQuery('#fs-wordpress-link').val();

var server_inactive = '<div class="rewardial-server-inactive"><div class="rewardial-server-message">Server Inactive</div></div>';
//console.log(fs_api_base);
//Attach class for iefix
if (window.matchMedia("screen and (-ms-high-contrast: active), (-ms-high-contrast: none)").matches) {
    jQuery('body').addClass('ie10');
}
// Check if user is logged in
	var logged_in = jQuery('#check_logged_in').val();
	if(logged_in == 'on'){
		jQuery('.fs-login').hide();
	}else{
		jQuery('.fs-logged').hide();
	}
 

	function login_button_press(){
		if(!jQuery('.fs-login-ttip .fs-pop-account').is(":visible")){
			jQuery('.fs-login-ttip .fs-pop-account').fadeIn();
			jQuery.ajax({
				url: fs_ajax,
				type: 'post',
				data: {action: 'unique_visitor'},
				success:function(r){
					
				}
			});
				
		}else{
			jQuery('.fs-login-ttip .fs-pop-account').fadeOut();
		}
		jQuery('#fs-notifier').hide();
		
		// login by pressing enter
		if(jQuery('.fs-login-ttip .fs-pop-account').is(":visible")){
			
			jQuery(document).keypress(function(e) {
				if(e.which == 13) {
					jQuery('.fs-submit').trigger('click');
				}
			});
			
		//}else{
			jQuery('#fs-overlay').removeClass('fs-active');
			jQuery('#fs-overlay').addClass('fs-inactive');
			jQuery('#fs-shop-box').removeClass('fs-active');
			jQuery('#fs-shop-box').addClass('fs-inactive');
			jQuery('#fs-community-box').removeClass('fs-active');
			jQuery('#fs-community-box').addClass('fs-inactive');
		}
	}
	jQuery('#fs-notifier2').click(function(){
		login_button_press();
		jQuery(this).html('');
		jQuery(this).hide();
		jQuery('#fs-notifier3').hide();
	});

 
//Show login form
jQuery('.fs-btn-login').click(function(){
	login_button_press();
}); 

jQuery('.fs-account-options').click(function(){
	//jQuery('.fs-options ul').slideToggle(250);
	var d = jQuery('.fs-options ul').css('display');
	if (d == 'none') {
		jQuery('.fs-options ul').show(100);
		jQuery('.fs-options ul').addClass('ie-show');
	}
	else {
		jQuery('.fs-options ul').hide(100);
	}
});
jQuery('.fs-signup').click(function(){
	jQuery('.fs-login-form').hide();
	jQuery('#fs-sign-up').show();
	jQuery('#messages').html('');
});

jQuery('#fs-signup-back').click(function(){
	jQuery('#fs-sign-up').hide();
	jQuery('.fs-login-form').show();
	jQuery('#messages').html('');
});

var gl_user = '';
var gl_pass = '';
var start; 
	function isURL(url){
		var regex = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/;
		return regex.test(url);
	}
	function IsEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	} 
	function alphaNumericDashUnderscore(name){
		var regex = /^[0-9a-zA-Z_-]*$/;
		return regex.test(name);
	}
	var start = jQuery.now();
			var user_id = jQuery('#rewardial_uid').val();
			var logged = jQuery('#rewardial_logged').val();
	

	// check rewards for reading
	// Create Base64 Object
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}


		// var storedAryY = JSON.parse(decodeURIComponent(Base64.decode(getCookie('rwd_notifier'))));		
		// console.log(storedAryY);		

	function isJson(str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}
	function htmlDecode( input ) {
		return String(input)
			.replace(/&amp;/g, '&')
			.replace(/&quot;/g, '"')
			.replace(/&lt;/g, '<')
			.replace(/&gt;/g, '>');
	}
	// if(getCookie('rewardial_anonymous')){
		// console.log(Base64.decode(getCookie('rewardial_anonymous')));
		// var cookie_anonymous = Base64.decode(getCookie('rewardial_anonymous'));
		// console.log(cookie_anonymous);
		// var json_cookie_anonymous = jQuery.parseJSON(cookie_anonymous);
	// }else{
		// var cookie_anonymous = '';
		// var json_cookie_anonymous = '';
	// }
	var check_anonymous_action = 0;
	var current_page = jQuery('#rewardial-page-link').val();
	var number_of_actions_rewarded = 1;
	
	// if(json_cookie_anonymous){
		// jQuery.each(json_cookie_anonymous,function(index, object){
				// var link_ok = 0;
				// var action_ok = 0;
			// jQuery.each(object,function(a,val){
				
				// if(a == 'link' && current_page == val){
					// link_ok = 1;
				// }
				// if(a == 'action' && val == 'reading'){
					// action_ok = 1;
				// }
			// });
			// if(link_ok == 1 && action_ok == 1){
				// check_anonymous_action = 1;
			// }
			// number_of_actions_rewarded++;
		// });
	// }
	if(getCookie('rewardial_anonymous')){
		jQuery.ajax({
			url:fs_ajax,
			data: { "action":'check_anonymous_reading',"link":current_page},
			type:"post",
			success:function(r){
				var resp = jQuery.parseJSON(r);
				number_of_actions_rewarded = resp.actions;
				if(resp.read_status){
					check_anonymous_action = 1;
				}
			}
		});
	}
	
	var focused = 0;
	var check_post = setInterval(function(){
		if(document.hasFocus()){
			focused++;
			if(check_anonymous_action!=1){
				check_rewardable_reading();
			}
		}else{
			focused = 0;
		}
	},1000);
				
	var check_reward_reading = 1;
	var scroll_down = 0;
	
	function check_rewardable_reading(){
		
		
			if(jQuery('body').height() < window.innerHeight){
				if(focused > 10 && check_reward_reading == 1){
					reward_reading();
					check_reward_reading = 0;
					clearInterval(check_post);
				}
			}else{
				jQuery(window).scroll( function() {
					if(jQuery(window).scrollTop() >= jQuery('body').height()/2){ // 50% scroll of the page
						scroll_down = 1;
						
					}
				
				});
				// console.log('aaa '+focused+' '+check_reward_reading+' '+scroll_down);
				if(focused > 10 && check_reward_reading == 1 && scroll_down == 1){
					reward_reading();
					check_reward_reading = 0;
					clearInterval(check_post);
				}
			}	
		// }
	}
	/*
	// when the content is changed without reloading the page we use this method
	window.onpopstate = function(event) {
	  alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
	};
	// if(currentHref!=window.location){
	jQuery('.button-x').click(function(){
		history.pushState("", "", "/2015/06/what-a-quest/");
	});
	jQuery('.button-y').click(function(){
		history.pushState("", "", "/2015/06/my-last-post/");
	});
	jQuery('.button-z').click(function(){
		history.pushState("", "", "/sample-page/");
	});
	// var numberOfEntries = window.history.length;
	// console.log(numberOfEntries);
	*/
	var i = 0;

(function(history){
    var pushState = history.pushState;
    history.pushState = function(state,desc,url) {
        if (typeof history.onpushstate == "function") {
            history.onpushstate({state: state,url:url});
        }
      
        return pushState.apply(history, arguments);
    }
})(window.history);

// check if the page is automatically uploaded through pushState and reward for reading if it's a new page
var check_post_page = '';
window.onpopstate = history.onpushstate = function(e) {
    i++;
	// console.log(JSON.stringify(e.url));
    jQuery('.rewardial-question-text').text(JSON.stringify(e.url));
	
	var root_url = jQuery('#rewardial-blog-url').val();
	if(isURL(e.url)){
		var current_page = e.url;
	}else{
		var current_page = root_url+e.url;
	}
	
	check_anonymous_action = 0;
	number_of_actions_rewarded = 1;
	focused = 0;
	check_reward_reading = 1;
	scroll_down = 0;
	
	// check if the page was already read
	jQuery.ajax({
		url:fs_ajax,
		data: { "action":'check_anonymous_reading',"link":current_page},
		type:"post",
		success:function(r){
			var resp = jQuery.parseJSON(r);
			number_of_actions_rewarded = resp.actions;
			if(resp.read_status){
				check_anonymous_action = 1;
			}
			// get the entire page and check if it's a post page
			jQuery.ajax({
				 type: "GET",
				 url:current_page,
				 cache: false,
				 dataType: 'html',
				 success: function(data){
					var page_type_input = jQuery(data).find('#rewardial-page-type');
					page_type = page_type_input.val();
					
				}
			});
			
		}
	});
	// console.log(page_type);
	// check every second if the page was read 
	var test_array = new Array();
	test_array[i] = setInterval(function(){
		if(document.hasFocus()){
			focused++;
			if(check_anonymous_action!=1){
				if(jQuery('body').height() < window.innerHeight){
					if(focused > 10 && check_reward_reading == 1){
						reward_reading();
						check_reward_reading = 0;
						clearInterval(test_array[i]);
					}
				}else{
					jQuery(window).scroll( function() {
						if(jQuery(window).scrollTop() >= jQuery('body').height()/2){ // 50% scroll of the page
							scroll_down = 1;
							
						}
					
					});
					if(focused > 10 && check_reward_reading == 1 && scroll_down == 1){
						reward_reading();
						check_reward_reading = 0;
						clearInterval(test_array[i]);
					}
				}
			}else{
				clearInterval(test_array[i]);
			}
		}else{
			focused = 0;
		}
	},1000);
	/*
	MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
	var observer = new MutationObserver(function(mutations, observer) {
		// fired when a mutation occurs
		// console.log(mutations, observer);

			
	});

	// define what element should be observed by the observer
	// and what types of mutations trigger the callback
	observer.observe(document, {
	  subtree: true,
	  attributes: true
	  //...
	});
	*/
	
};
	
	// */
	
	


	var LastPageVisited = getCookie('rewardial_LastPageVisited');
	// var LastPageVisited = jQuery('#rewardial_last_page').val();
	
	// get cookie
	function getCookie(cname){
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++)
		  {
		  var c = ca[i].trim();
		  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		  }
		return "";
	}
	
	// }
	// var location = 'ghjjjjjjj';
	//get the current credit by ajax to api
	if(user_id){
		jQuery.ajax({
			url: fs_api_base +'get_credits',
			data: { "uid":user_id,'code':my_code,'time':my_time,'link':my_link},
			type:"post",
			success:function(r){
				var response = jQuery.parseJSON(r);
				document.cookie = "rewardial_Credits ="+response.credits+ '; path=/';
				document.cookie = "rewardial_Premium_Currency ="+response.premium+ '; path=/';
				
				jQuery('.fs-credits-value').html(response.credits);
			}
		});
	}
	var credits = getCookie('rewardial_Credits');
	var username = getCookie('rewardial_Username');
	var newComment = getCookie('rewardial_NewComment');
	
	
	// if the user spent 10 seconds on the page, and the article or page is unvisited, and the page type is post, and the user has scrolled till the end of the post, reward the current user
	var page_type = jQuery('#rewardial-page-type').val();
	function reward_reading(){
		if(page_type == 'post'){
			if(logged == 'on'){
				var pathname = window.location.href;
				jQuery.ajax({
					url: fs_ajax,
					data: {'action':'save_reading_time','uid': user_id,'link': pathname,'code':my_code,'time': my_time},
					dataType: 'json',
					type: 'post',
					success:function(response){
						if(response.status == 'not read'){
							var level_up = '';
							var check_level_up = 0;
							var levels_up = 0;
							jQuery('.fs-level-attribute').each(function(){
								var name = jQuery(this).find('.fs-attribute-name').val();
								var value = jQuery('.fs-'+name+'-value').val();
								var level = jQuery('.fs-'+name+'-level').val();
								if(response.returned[name]){
									var total_points = parseInt(value)+parseInt(response.returned[name]);
									current_level = 25*level*level - 25*level;
									next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
									extra = parseInt(total_points) - current_level;
									points = next_level - current_level;
									percent_extra = extra*100/points;
									if(percent_extra > 99){
										percent_extra = percent_extra - 100;
										level = parseInt(level) + 1;
										level_up += '<div class="level-up-container"><div class="level-up-generic level-up-'+name+'"><div class="level-up-level">'+level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+level+' '+name+' </div></div>';
										check_level_up = 1;
										levels_up = levels_up +1;
									}
									jQuery('#'+name+'-progress').css("width",percent_extra+'%');
									jQuery('.fs-'+name+'-value').val(total_points);
									jQuery('.fs-'+name+'-level').val(level);
									jQuery('#fs-'+name+'-level-value').html(level);
								}
							});
							
							if(check_level_up == 1){
								// show notification for level up
								jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
								jQuery('.fs-collection-complete-close').click(function(){
									jQuery('.fs-collection-complete-rewards').html('');
									jQuery('.fs-collection-complete-rewards').hide();
								});
								jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
								jQuery('.fs-collection-complete-rewards').show();
								if(levels_up == 1){
									jQuery('.fs-collection-complete-rewards .level-up-container').css('width','100%');
								}
							}
							var credit_to_add = response.add;

							notify_bubble(0,credit_to_add,'reading'); // display the notification bubble
							
							var current_credit = jQuery('.fs-credits-value').html();
							jQuery('.fs-credits-value').html(parseInt(current_credit)+parseInt(credit_to_add));
							jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
							jQuery('.fs-notifications .fs-notifications-text').html('Reward - Article Read');
							jQuery.each(response.rewarded,function(index,value){
								if(value > 0){
									jQuery('#fs-reward-'+index).html('+'+value);
								}
							});
							
							// display the notifications for the 5th rewardable action
							if(response.display_5th_action){
								jQuery('#rwd-5-actions').show();
							}
							
						}
					}
				});
				
			}else{
				// if the user is not logged in into the rewardial plugin, save all actions that he visits and reward him at login
				// var pathname = getCookie('rewardial_LastPageVisited');
				var pathname = window.location.href;
				jQuery.ajax({
					url: fs_ajax,
					data: {'action':'anonymous_actions','type':'reading','link':pathname},
					type: 'post',
					success:function(resp){
						
						jQuery.ajax({
							url:fs_api_base+'get_action_credits',
							crossDomain:true,
							data:{'type':'reading','link':my_link},
							type:"post",
							success:function(resp){
								var response = jQuery.parseJSON(resp);
								var creceive = response.credits;
															
								notify_bubble(1,creceive,'reading');

							}
						});
						
						
					}
					
				});
			}
		}
	}
	
	
	// login function 
	
	localStorage.removeItem('rwd-comment-submitted');
	jQuery('.fs-submit').click(function(){
		var email = jQuery('.fs-inner-box #fs-login-username').val();
		var password = jQuery('.fs-inner-box #fs-login-password').val();

		if(!IsEmail(email)) jQuery("#messages").html('<div class="error">The email address is invalid!</div>');
		if(password.length == 0) jQuery("#messages").html('<div class="error">Please insert password!</div>'); 
		 else{
			
			 jQuery('#fs_login_button_').hide();
			jQuery.ajax({
				url: fs_api_base + 'api_login',
				crossDomain: true,
                data: {'email': email,'password':password,'code':my_code,'time':my_time,'link':my_link},
                type:"post",
				beforeSend: function(){
					jQuery('#rewardial-loader').show();
				},
                success:function(r){
					var response = jQuery.parseJSON(r);
                    if(response.response == 'success'){
						var uid = response.uid;
						var now = new Date();
						var time = now.getTime();
						var final_credits = response.credits;
						// if(jQuery('.fs-remember-me').is(':checked')) {
							// time += 365*24*3600*1000; // 1 year
						// }else{
							time += 9  * 3600 * 1000; // 9 hours to expire cookie
						//}
						now.setTime(time);
						
						document.cookie = 'rewardial_Logged = on; path=/';
						document.cookie = 'rewardial_Username = '+response.username+'; path=/'; 
						document.cookie = 'rewardial_Credits = '+response.credits+'; path=/'; 
						document.cookie = 'rewardial_Premium_Currency = '+response.premium+'; path=/';
						document.cookie = 'rewardial_Avatar = '+response.avatar+'; path=/';
						if(response.token){
							document.cookie = 'rewardial_Uid = '+response.token+'; path=/';
						}else{
							document.cookie = 'rewardial_Uid = '+response.uid+'; path=/';
						}
						
						jQuery('#fs-user-id').val(response.uid);
						document.cookie = 'rewardial_login = now;path=/';
						location.reload();
						
						
					}
                    else{ 
						jQuery("#messages").html('<div class="error">'+response.response+'</div>'); 
						
					}
                 },
				 complete:function(response){
					jQuery('#rewardial-loader').hide();
					jQuery('#fs_login_button_').show();
				 }
			});
		  }
		
	});

	
	// signing up function
jQuery('.fs-signup-submit').click(function(){
	
	firstName = jQuery('.fs-first-name').val();
	lastName = jQuery('.fs-last-name').val();
	userEmail = jQuery('.fs-user-email').val();
	password = jQuery('.fs-signup-password').val();
	if(!IsEmail(userEmail)) jQuery("#messages").html('<div class="error">The email address is invalid!</div>');
		 else if(password.length == 0) jQuery("#messages").html('<div class="error">Please insert password!</div>'); 
		 else if(password.length < 9) jQuery("#messages").html('<div class="error">Your password should have more than 8 characters!</div>');
		  else if(firstName.length == 0) jQuery("#messages").html('<div class="error">Please insert first name!</div>'); 
		  else if(lastName.length == 0) jQuery("#messages").html('<div class="error">Please insert last name!</div>'); 
		  else if(!alphaNumericDashUnderscore(firstName) || !alphaNumericDashUnderscore(lastName)) jQuery('#messages').html('<div class="error">Please insert alpha numeric characters');
		  else {
			if(!jQuery('.fs-signup-agreement').is(':checked')) {
				jQuery("#messages").html('<div class="error">Please agree with the terms and privacy policy!</div>'); 
			}else {
				//jQuery('.fs-signup-submit').off('click');
				jQuery.ajax({
					url: fs_api_base + 'signup',
					data:{'first_name':firstName,'last_name':lastName,'user_email':userEmail,'password':password,'link':my_link,'code':my_code,'time':my_time},
					type:'post',
					success:function(r){
						var resp = jQuery.parseJSON(r);
						if(resp.response == 'success'){
							
							jQuery.ajax({
								url: fs_api_base + 'api_login',
								crossDomain: true,
								data: {'email': userEmail,'password':password,'code':my_code,'time':my_time,'link':my_link},
								type:"post",
								success:function(r){
									var response = jQuery.parseJSON(r);
									if(response.response == 'success'){
										var uid = response.uid;
										var now = new Date();
										var time = now.getTime();
										var final_credits = response.credits;
										
										time += 9  * 3600 * 1000; // 9 hours to expire cookie
										now.setTime(time);
										
										document.cookie = 'rewardial_Logged = on; path=/';
										document.cookie = 'rewardial_Username = '+response.username+'; path=/';
										document.cookie = 'rewardial_Credits = '+response.credits+'; path=/';
										document.cookie = 'rewardial_Premium Currency = '+response.premium+'; path=/';
										document.cookie = 'rewardial_Avatar = '+response.avatar+'; path=/';
										if(response.token){
											document.cookie = 'rewardial_Uid = '+response.token+'; path=/';
										}else{
											document.cookie = 'rewardial_Uid = '+response.uid+'; path=/';
										}
										
										jQuery('#fs-user-id').val(response.uid);
										document.cookie = 'rewardial_login = now;path=/';
										location.reload();
									}
									else jQuery("#messages").html('<div class="error">'+response.response+'</div>');
								 }
							});
							
							
						}else if(resp.response == 'existent'){
							jQuery('#messages').html('<div class="error">Email Already registered!</div>');
						}else {
							jQuery('#messages').html('<div class="error">Please try again.</div>');
						}
					}
				});
			}
		}
});

	// check if the user has logged in for the first time today and display rewards
	
	var login = getCookie('rewardial_login');
	// var login = jQuery('#rewardial_login').val();
	if(login == 'now'){
		jQuery('.fs-login-ttip .fs-pop-account').show();
		jQuery.ajax({
			url: fs_ajax,
			data: {'action':'save_user','username': username,'uid':user_id,'code':my_code,'time': my_time},
			type: 'post',
			dataType: 'json',
			success:function(response){
				
				if(response.status == 'first_login'){
					var bonuses = '';
					var level_up = '';
					var levels_up = 0;
					if(response.rwd_rewards){
						new_credit = parseInt(credits)+parseInt(response.add)+parseInt(response.rwd_rewards['credits']);
					}else{
						new_credit = parseInt(credits)+parseInt(response.add);
					}
					jQuery('.fs-level-attribute').each(function(){
						var name = jQuery(this).find('.fs-attribute-name').val();
						var value = jQuery('.fs-'+name+'-value').val();
						var level = jQuery('.fs-'+name+'-level').val();
						if(response.returned[name]){
							var anonymous_reward_name = 0;
							if(response.rwd_rewards){
								var total_points = parseInt(value)+parseInt(response.returned[name])+parseInt(response.rwd_rewards[name]);
							}else{
								var total_points = parseInt(value)+parseInt(response.returned[name]);
							}
							
								current_level = 25*level*level - 25*level;
								next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
								extra = parseInt(total_points) - current_level;
								points = next_level - current_level;
								percent_extra = extra*100/points;
								if(percent_extra > 99){
									percent_extra = percent_extra - 100;
									level = parseInt(level) + 1;
									level_up += '<div class="level-up-container"><div class="level-up-generic level-up-'+name+'"><div class="level-up-level">'+level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+level+' '+name+' </div></div>';
									levels_up = levels_up + 1;
								}
								jQuery('#'+name+'-progress').css("width",percent_extra+'%');
								jQuery('.fs-'+name+'-value').val(total_points);
								jQuery('.fs-'+name+'-level').val(level);
								jQuery('#fs-'+name+'-level-value').html(level);
								
								if(response.rwd_rewards){
									if(parseInt(response.returned[name])+parseInt(response.rwd_rewards[name])){
										var temporary_sum = parseInt(response.returned[name])+parseInt(response.rwd_rewards[name]);
										bonuses += '<div class="fs-rewards-line fs-completion-reward-'+name+'" title="'+name+'">'+temporary_sum+'</div>';
									}
								}else{
									if(parseInt(response.returned[name])){
										bonuses += '<div class="fs-rewards-line fs-completion-reward-'+name+'" title="'+name+'">'+parseInt(response.returned[name])+'</div>';
									}
								}
						}
					});

					// show notification for first login rewards
					jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
					jQuery('.fs-collection-complete-close').click(function(){
						jQuery('.fs-collection-complete-rewards').html('');
						jQuery('.fs-collection-complete-rewards').hide();
						
						if(response.first_login_notify == 1){
							// display the first login popup 
							jQuery('#rwd-first-login').show();
						}
					});
					jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
					jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="fs-completion-rewards">Welcome. You just earned: <div class="fs-rewards-background"><span>'+bonuses+'</span></div><div>Continue reading, commenting and sharing for more credits.</div></div>');
					jQuery('.fs-collection-complete-rewards').show();
					if(levels_up == 1){
						jQuery('.fs-collection-complete-rewards .level-up-container').css('width','100%');
					}
					
					
					var credit_to_add = response.add;
					jQuery('.fs-credits-value').html(new_credit);
					jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
					jQuery('.fs-notifications .fs-notifications-text').html('Reward - First Login');
					jQuery.each(response.rewarded,function(index,value){
						if(value > 0){
							if(response.rwd_rewards){
								anonymous_credit = parseInt(value)+parseInt(response.rwd_rewards[index]);
							}else{
								anonymous_credit = parseInt(value);
							}
							jQuery('#fs-reward-'+index).html('+'+anonymous_credit);
						}
					});
					
					
					if(response.display_shop_reminder == 1){
						// display the shop reminder
						jQuery('#rwd-5-days-shop').show();
					}
					if(response.display_community_reminder == 1){
						// display the community reminder
						jQuery('#rwd-4-days-commmunity').show();
					}
					if(response.display_5th_action){
						// display the notifications for the 5th rewardable action
						jQuery('#rwd-5-actions').show();
					}
					
				}else{
					
					if(response.rwd_rewards){
						var bonuses = '';
						var level_up = '';
						var levels_up = 0;
							new_credit = parseInt(credits)+parseInt(response.rwd_rewards['credits']);
						
						
						jQuery('.fs-level-attribute').each(function(){
							var name = jQuery(this).find('.fs-attribute-name').val();
							var value = jQuery('.fs-'+name+'-value').val();
							var level = jQuery('.fs-'+name+'-level').val();

							var total_points = parseInt(value)+parseInt(response.rwd_rewards[name]);
					
						
							current_level = 25*level*level - 25*level;
							next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
							extra = parseInt(total_points) - current_level;
							points = next_level - current_level;
							percent_extra = extra*100/points;
							if(percent_extra > 99){
								percent_extra = percent_extra - 100;
								level = parseInt(level) + 1;
								level_up += '<div class="level-up-container"><div class="level-up-generic level-up-'+name+'"><div class="level-up-level">'+level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+level+' '+name+' </div></div>';
								levels_up = levels_up + 1;
							}
							jQuery('#'+name+'-progress').css("width",percent_extra+'%');
							jQuery('.fs-'+name+'-value').val(total_points);
							jQuery('.fs-'+name+'-level').val(level);
							jQuery('#fs-'+name+'-level-value').html(level);
							
							
							bonuses += '<div class="fs-rewards-line fs-completion-reward-'+name+'" title="'+name+'">'+parseInt(response.rwd_rewards[name])+'</div>';
									
						});

						// show notification for first login rewards
						jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
						jQuery('.fs-collection-complete-close').click(function(){
							jQuery('.fs-collection-complete-rewards').html('');
							jQuery('.fs-collection-complete-rewards').hide();
						});
						jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
						jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="fs-completion-rewards">Welcome. You just earned: <div class="fs-rewards-background"><span>'+bonuses+'</span></div>');
						jQuery('.fs-collection-complete-rewards').show();
						
						if(levels_up == 1){
							jQuery('.fs-collection-complete-rewards .level-up-container').css('width','100%');
						}
						
						var credit_to_add = response.rwd_rewards['credits'];
						jQuery('.fs-credits-value').html(new_credit);
						jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
						jQuery('.fs-notifications .fs-notifications-text').html('Reward - Anonymous Actions');
						jQuery.each(response.rwd_rewards,function(index,value){
							if(value > 0){
								jQuery('#fs-reward-'+index).html('+'+value);
							}
						});
					}
				}
			}
		});
	}
	// if there's a new comment, reward the current user
	if(newComment){
		jQuery.ajax({
			url:fs_api_base+'get_task_credits',
			crossDomain:true,
			data:{'type':'comment','user_id': user_id,'code':my_code,'time':my_time,'link':my_link},
			type:"post",
			success:function(resp){
				var response = jQuery.parseJSON(resp);
				var credit_to_add = response.credits;
			
						notify_bubble(0,credit_to_add,'comment');
							
					//jQuery('.fs-credits-value').html(credits);
					jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
					jQuery('.fs-notifications .fs-notifications-text').html('Reward - Comment');
					jQuery.each(response,function(index,value){
						if(value > 0){
							jQuery('#fs-reward-'+index).html('+'+value);
						}
					});
				document.cookie = "rewardial_NewComment =''; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
				
			}
		});
	}
	
	jQuery('.rewardial-gift-buy').each(function(){
		jQuery(this).click(function(){
			
			var gift_id = jQuery(this).attr('id').replace('rwd-buy-gift-','');
			//var gift_qty = jQuery('#rwd-gift-qty-'+gift_id+' input').val();
			
			// if(gift_qty){

				jQuery.ajax({
					url:fs_api_base+'buy_gift',
					data:{'user_id': user_id,'link':my_link,'gift_id':gift_id},
					type:"post",
					success:function(resp){
						var response = jQuery.parseJSON(resp);
						if(response.status == 200){
							jQuery('.rewardial-gift-message').html('');
							jQuery('#rwd-gift-message-'+gift_id).html('<span class="rwd-success">'+response.message+'</span>');
						}else{
							jQuery('.rewardial-gift-message').html('');
							jQuery('#rwd-gift-message-'+gift_id).html('<span class="rwd-error">'+response.message+'</span>');
						}
					}
				});
				
			// }else{
				// jQuery('.rewardial-gift-message').html('');
				// jQuery('#rwd-gift-message-'+gift_id).html('<span class="rwd-error">Please select quantity</span>');
			// }
		});
	
	});
	
	
	// if there is something new bought, reward the current user
	
	var bought = getCookie('rewardial_bought');
	if(bought == '1'){
		jQuery.ajax({
			url:fs_api_base+'get_task_credits',
			crossDomain:true,
			data:{'type':'buying','user_id': user_id,'code':my_code,'time':my_time,'link':my_link},
			type:"post",
			success:function(resp){
				var response = jQuery.parseJSON(resp);
				var credit_to_add = response.credits;
				
					notify_bubble(0,credit_to_add,'buying');
					
					//jQuery('.fs-credits-value').html(credits);
					jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
					jQuery('.fs-notifications .fs-notifications-text').html('Reward - Buying');
					jQuery.each(response,function(index,value){
						if(value > 0){
							jQuery('#fs-reward-'+index).html('+'+value);
						}
					});
				document.cookie = "rewardial_bought =''; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
				
			}
		});
	}
	
	jQuery('.fs-profile-link').click(function(){
		jQuery('.fs-login-ttip .fs-pop-account').css('display','block');
		
		jQuery('#fs-community-box').removeClass('fs-active');
		jQuery('#fs-shop-box').removeClass('fs-active');
		jQuery('.fs-quest').removeClass('active');
		jQuery('#fs-overlay').hide();
		
		jQuery('#fs-community-box').addClass('fs-inactive');
		jQuery('#fs-shop-box').addClass('fs-inactive');
		jQuery('.fs-quest').addClass('inactive');
	});
	
	if(jQuery('#rewardial_login').val() == 'now'){
		document.cookie = "rewardial_login = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
	}
	jQuery('.fs-community').click(function(){
		//alert('community');
		
			if(jQuery('#fs-community-box').hasClass('fs-active')) {
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery('#fs-shop-box').removeClass('fs-active');
				jQuery('#fs-shop-box').addClass('fs-inactive');
				jQuery('.fs-shop').removeClass('active');
				jQuery('.fs-community').removeClass('active');
				jQuery('.fs-quest').removeClass('active');
				jQuery("#fs-overlay").removeClass("fs-active");
				jQuery("#fs-overlay").addClass("fs-inactive");
				
				
			} else {
				jQuery("#fs-community-box").removeClass("fs-inactive");
				jQuery("#fs-community-box").addClass("fs-active");
				jQuery('#fs-shop-box').removeClass('fs-active');
				jQuery('#fs-shop-box').addClass('fs-inactive');
				jQuery('#fs-community').addClass('active');
				jQuery('.fs-quest').removeClass('active');
				jQuery('.fs-community').addClass('active');
				jQuery('.fs-shop').removeClass('active');
				jQuery("#fs-overlay").removeClass("fs-active");
				jQuery("#fs-overlay").addClass("fs-inactive");
				
				  jQuery('.fs-friends-flexslider').flexslider({
				    animation: "slide",
				    animationLoop: false,
					slideshowSpeed: 10000,
				    itemWidth: 130,
					nextText: " ",
					prevText: " ",
					minItems: 2,
					maxItems: 6,
					controlNav: false,
				    itemMargin: 10 
				  });
				
			}
			
			jQuery.ajax({
				url: fs_api_base+ 'click_community',
				data:{'link':my_link,'uid':user_id,'code':my_code,'time':my_time},
				type:"post",
				crossDomain: true,
				success: function(r){
					
				}
			
			});
		
	});
	// hide community with x
	jQuery('.fs-close-community').click(function(){
		if(jQuery('#fs-community-box').hasClass('fs-active')) {
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery(".fs-community").removeClass("active");
				
			} else {
				jQuery("#fs-community-box").removeClass("fs-inactive");
				jQuery("#fs-community-box").addClass("fs-active");
				jQuery(".fs-community").addClass("active");
				
			}
	});
	
	
	
	// trade popup for community
	jQuery('.fs-btn-trade').each(function(){
		jQuery(this).click(function(){
			jQuery(this).css('box-shadow','0 0 10px #aaaaaa inset');
			jQuery('.fs-community-content').hide();
			jQuery('.pop-up-trade').show();
			jQuery('.fs-trade-popup-header').show();
			jQuery('.fs-trade-popup-content').show();
			var friend_id = jQuery(this).attr('id').replace('fs-trade-friend-','');
			//var user_id = getCookie('rewardial_Uid');
			var user_id = jQuery('#rewardial_uid').val();
			// get friend avatar and name
			var friend_src = jQuery('#fs-friend-'+friend_id+' img').attr('src');
			var friend_name = jQuery('#fs-friend-'+friend_id+' .fs-avatar-nameofuser').html();
			jQuery('.fs-trade-popup-friend').html('<img class="fs-trade-avatar-img" src="'+friend_src+'"><div class="fs-trade-avatar-frame"></div><p class="fs-trade-user-name">'+friend_name+'</p>');
			
			jQuery('#fs-trade-button-fid').val(friend_id);
			jQuery.ajax({
				url: fs_api_base+ 'get_trade_info',
				data:{'uid':user_id,'fid':friend_id,'code':my_code,'time':my_time,'link':my_link},
				type:"post",
				crossDomain: true,
				success: function(r){
					var response = jQuery.parseJSON(r);
					if(response.status == 'ok'){
						var user_duplicates = response.user_stamps;
						var friend_duplicates = response.friend_stamps;
						var user_stamps = '';
						var friend_stamps = '';
						if(response.user_status == 'ok'){
							jQuery.each(user_duplicates,function(){
								user_stamps = user_stamps + '<li class="fs-stamp-holder-'+this.shape+' fs-stamp-need-'+this.need+'"><div class="fs-nav-stamp-name">'+this.name+'</div><span class="fs-nav-stamp-duplicates">'+this.duplicates+'</span><a id="fs-trade-offer-'+this.stamp_id+'" class="fs-trade-offer" href="javascript:void(0)">';
								if(this.need == 2){
									user_stamps += '<div class="rewardial-not-needed rewardial-tag-'+this.shape+'"></div>';
								}
								user_stamps += '<img class="fs-stamp-shape-'+this.shape+'" src="'+img_url+'/stamps/'+this.image+'"></a></li>';
							});
						}else{
							user_stamps = '<div class="no-duplicates">You have no available duplicates for this trade. Buy more envelopes.</div>';
						}
						jQuery('.pop-up-trade #fs-trade-stamp-original .nav').html(user_stamps);
						
						if(response.friend_status == 'ok'){
							jQuery.each(friend_duplicates,function(){
								friend_stamps = friend_stamps + '<li class="fs-stamp-holder-'+this.shape+' fs-stamp-need-'+this.need+'"><div class="fs-nav-stamp-name">'+this.name+'</div><span class="fs-nav-stamp-duplicates">'+this.duplicates+'</span><a id="fs-trade-offer-'+this.stamp_id+'" class="fs-trade-offer" href="javascript:void(0)">';
								if(this.need == 2){
									friend_stamps += '<div class="rewardial-not-needed rewardial-tag-'+this.shape+'"></div>';
								}
								friend_stamps += '<img class="fs-stamp-shape-'+this.shape+'" src="'+img_url+'/stamps/'+this.image+'"></a></li>';
							});
						}else{
							friend_stamps = '<div class="no-duplicates">Your friend has no duplicates for trade right now.</div>';
						}
						jQuery('.pop-up-trade #fs-trade-stamp-receive .nav').html(friend_stamps);
						
						jQuery('#fs-trade-stamp-original .nav .fs-trade-offer').each(function(){
							jQuery(this).click(function(){
								var stamp_id = jQuery(this).attr('id').replace('fs-trade-offer-','');
								jQuery.ajax({
									url: fs_api_base+ 'get_stamp_information',
									data:{'id':stamp_id},
									type:"post",
									crossDomain: true,
									success: function(r){
										var response = jQuery.parseJSON(r);
										if(response.status == 'ok'){
											jQuery('.fs-trade-offer-stamp').show();
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').show();
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-portrait');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-square');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-landscape');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').addClass('fs-stamp-holder-'+response.shape);
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-image').html('<img class="fs-stamp-area-image fs-stamp-shape-'+response.shape+'" src="'+img_url+'/stamps/'+response.image+'">');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-name').html('<span>'+response.name+'</span>');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-category').html('Category : <span>'+response.category+'</span>');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-level').html('Level : <span>'+response.level+'</span>');
											jQuery('.fs-trade-offer-stamp .fs-stamp-holder-rarity').html('Rarity : <span>'+response.rarity+'</span>');
											jQuery('#fs-trade-button-stamp-offer').val(stamp_id);
											
										}
									}
								});
							});
						});
						jQuery('#fs-trade-stamp-receive .nav .fs-trade-offer').each(function(){
							jQuery(this).click(function(){
								var stamp_fid = jQuery(this).attr('id').replace('fs-trade-offer-','');
								jQuery.ajax({
									url: fs_api_base+ 'get_stamp_information',
									data:{'id':stamp_fid},
									type:"post",
									crossDomain: true,
									success: function(r){
										var response = jQuery.parseJSON(r);
										if(response.status == 'ok'){
											jQuery('.fs-trade-receive-stamp').show();
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').show();
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-portrait');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-square');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').removeClass('fs-stamp-holder-landscape');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').addClass('fs-stamp-holder-'+response.shape);
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-image').html('<img class="fs-stamp-area-image fs-stamp-shape-'+response.shape+'" src="'+img_url+'/stamps/'+response.image+'">');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-name').html('<span>'+response.name+'</span>');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-category').html('Category : <span>'+response.category+'</span>');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-level').html('Level : <span>'+response.level+'</span>');
											jQuery('.fs-trade-receive-stamp .fs-stamp-holder-rarity').html('Rarity : <span>'+response.rarity+'</span>');
											jQuery('#fs-trade-button-stamp-receive').val(stamp_fid);
											
										}
									}
								});
							});
						});
					}
				}
			});
		});
	});
	
	// trade duplicate stamps
	jQuery('.fs-trade-button-middle').click(function(){
		var submit_button = jQuery(this);
		jQuery(this).css('box-shadow','0 0 10px #aaaaaa inset');
		setTimeout(function() {
			jQuery(this).css('box-shadow','none');
		},1000);
		var uid = jQuery('#fs-trade-button-uid').val();
		var fid = jQuery('#fs-trade-button-fid').val();
		var stamp_offer = jQuery('#fs-trade-button-stamp-offer').val();
		var stamp_receive = jQuery('#fs-trade-button-stamp-receive').val();
		var friend_name = jQuery('#fs-friend-'+fid+' .fs-avatar-nameofuser').html();
		if(uid && fid && stamp_offer & stamp_receive){
			jQuery.ajax({
				url: fs_api_base+ 'trade_stamps',
				data:{'uid':uid,'fid':fid,'stamp_uid':stamp_offer,'stamp_fid':stamp_receive,'code':my_code,'time':my_time,'link':my_link},
				type:"post",
				crossDomain: true,
				success: function(r){
					var response = jQuery.parseJSON(r);
					if(response.status == 'ok'){
						jQuery('.pop-up-trade').children().hide();
						jQuery('.pop-up-trade .fs-trade-message').html('<div class="fs-trade-success">Your offer has been sent to <span>'+friend_name+'</span>. Check your Trades tab regulary to see his response. </div><div class="fs-trade-back-button">Back</div>');
						jQuery('.pop-up-trade .fs-trade-message').show();
						jQuery('.fs-trade-back-button').click(function(){
							jQuery('.pop-up-trade .fs-trade-message').hide();
							jQuery('.fs-community-content').show();
							jQuery('.fs-trade-offer-stamp').hide();
							jQuery('.fs-trade-receive-stamp').hide();
							
						});
						submit_button.css('box-shadow','none');
					}
				}
			});
		}else{
				jQuery(this).css('box-shadow','none');
		}
	});
	// back button to community
	jQuery('.fs-trade-back-to-community').click(function(){
		jQuery('.pop-up-trade').hide();
		jQuery('.fs-community-content').show();
		jQuery('.fs-trade-offer-stamp').hide();
		jQuery('.fs-trade-receive-stamp').hide();
	});
	
	// hide shop with X
	jQuery('.fs-close-shop').click(function(){
		if(jQuery('#fs-shop-box').hasClass('fs-active')) {
				jQuery("#fs-shop-box").removeClass("fs-active");
				jQuery("#fs-shop-box").addClass("fs-inactive");
				jQuery(".fs-shop").removeClass("active");
			} else {
				jQuery("#fs-shop-box").removeClass("fs-inactive");
				jQuery("#fs-shop-box").addClass("fs-active");
				jQuery(".fs-shop").addClass("active");
				
			}
	});
	
	
	
	// hide logged in with X
	jQuery('.fs-close-logged').click(function(){
		jQuery('.fs-login-ttip .fs-pop-account').toggle(100);
		jQuery('#fs-notifier').hide();
	});
	function shop_tabs(){
		jQuery('.rewardial-shop-collectibles').click(function(){
			jQuery('.rewardial-shop-tabs span').removeClass('shop-tab-active');
			jQuery(this).addClass('shop-tab-active');
			jQuery('.rewardial-gifts-box').hide();
			if(screen.width < 490){
				jQuery('.responsive-shop').show();
			}else{
				jQuery('.fs-envelopes-flexslider').show();
			}
			get_blog_shop();
			
		});
		jQuery('.rewardial-shop-gifts').click(function(){
			jQuery('.rewardial-shop-tabs span').removeClass('shop-tab-active');
			jQuery(this).addClass('shop-tab-active');
			jQuery('.fs-envelopes-flexslider').hide();
			jQuery('.rewardial-gifts-box').show();
			if(screen.width < 490){
				jQuery('.responsive-shop').hide();
			}
		});
	}
	shop_tabs();
	
// display and hide the shop
	jQuery('.fs-shop').click(function(){
		if(jQuery('#fs-shop-box').hasClass('fs-active')) {
				jQuery("#fs-shop-box").removeClass("fs-active");
				jQuery("#fs-shop-box").addClass("fs-inactive");
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery('.fs-shop').removeClass('active');
				jQuery('.fs-quest').removeClass('active');
				jQuery("#fs-overlay").removeClass("fs-active");
				jQuery("#fs-overlay").addClass("fs-inactive");
				jQuery("#fs-overlay").addClass('fs-inactive');
				jQuery('.fs-envelopes-flexslider').flexslider({
					    animation: "slide",
					    animationLoop: false,
						slideshowSpeed: 10000,
					    itemWidth: 280,
					    nextText: " ",
						prevText: " ",
						controlNav: false,
					    itemMargin: 0 
					  });
				
		} else {
				jQuery("#fs-shop-box").removeClass("fs-inactive");
				jQuery("#fs-shop-box").addClass("fs-active");
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery('.fs-community').removeClass('active');
				jQuery('.fs-shop').addClass('active');
				jQuery('.fs-quest').removeClass('active');
				jQuery("#fs-overlay").removeClass("fs-active");
				jQuery("#fs-overlay").addClass("fs-inactive");
				jQuery("#fs-overlay").addClass('fs-inactive');
			
		
		}
			get_blog_shop();
			return false;
	});
	
	function get_blog_shop(){
		jQuery.ajax({
			url: fs_api_base+ 'get_featured_envelopes',
			data:{'link':my_link,'uid':user_id,'code':my_code,'time':my_time},
			type:"post",
			crossDomain: true,
			success: function(r){
				var response = jQuery.parseJSON(r);
				if(response.status == 'ok'){
					var envelopes = response.featured;
					var shop ='';
					// add the featured envelopes into the content
					jQuery.each(envelopes,function(){
						 shop = shop + '<li class="fs-stamp '+this.featured+'" id="envelope_'+this.id+'"><span class="fs-stamp-index" title="Level: '+this.level+'. This represents the level of the available envelope. It will slightly increase each time you buy an envelope. Each level gives access to new collections.">'+this.level+'</span><div class="fs-envelope-level-up" id="envelope-level-up-'+this.id+'"></div><div class="fs-envelope-level-container"><input type="hidden" class="fs-envelope-level-value-'+this.id+'" value="'+this.level_value+'"/><div class="fs-envelope-level-bar" style="width:'+this.progress+'%;"><div class="fs-envelope-level-status-'+this.id+'" style="display:none">'+this.level_status+'</div></div></div><div class="fs-inner-stamp"><h2 class="fs-stamp-title"><span class="fs-light-title">'+this.name+'</span></h2><img src="'+img_url+'/envelopes/'+this.image+'" title="'+this.name+'"/></div><img src="'+wp_url+'img/bg-credits-profile-stockbook.png" class="fs-shop-envelope-credits"/><p class="fs-shop-envelope-price">'+this.price+'</p><button class="fs-buy-now" id="buy_'+this.id+'"></button></li>';
						
					});
					jQuery('.fs-slider').html(shop);
					jQuery('.fs-stamps-carousel .responsive-shop').remove();
					jQuery('.fs-stamps-carousel').append('<ul class="responsive-shop">'+shop+'</ul>');
					
					if(getCookie('rwd_notifier')){
						if(isJson(decodeURIComponent(Base64.decode(getCookie('rwd_notifier'))))){
							var notifier = JSON.parse(decodeURIComponent(Base64.decode(getCookie('rwd_notifier'))));
							//console.log(notifier);
							if(notifier.check_shop_visit){
								jQuery('#rwd-first-shop').show();
								jQuery.ajax({
									url: fs_ajax,
									data: {'action':'notifications_permanently_expire','type':'rwd-first-shop'},
									type: 'post',
									success:function(resp){
										
									}
								});
							}
						}
					}
					
					var shop_full_content = jQuery('.fs-shop-content').html();
					//console.log(shop_full_content);
					jQuery('.fs-envelopes-flexslider').flexslider({
					    animation: "slide",
					    animationLoop: false,
						slideshowSpeed: 10000,
					    itemWidth: 280,
					    nextText: " ",
						prevText: " ",
						controlNav: false,
					    itemMargin: 0
					  });
					
					buy_envelope_shop(shop_full_content);
				}else{
					// console.log('No envelopes selected yet');
				}
			}
		});
	}
	
	function buy_envelope_shop(shop_full_content){
		// click on button buy_now and buy an envelope
		jQuery('.fs-buy-now').each(function(){
			
			jQuery(this).click(function(){
				jQuery(this).off('click');
				jQuery(this).css('box-shadow','0 0 10px #aaaaaa inset');
				var id = jQuery(this).attr('id').replace('buy_', '');
				var uid = jQuery('#fs-user-id').val();
				 jQuery.ajax({
					url: fs_api_base + "buy_envelope",
					data: {'envelope_id': id, 'uid': uid,'link':my_link},
					type:"post",
					success:function(r){
						var response = jQuery.parseJSON(r);
						if(response.response == 'success') {	
						
							
							// if(response.duplicates.length && response.old_duplicates.length){
								if(parseInt(response.duplicates)%10 < parseInt(response.old_duplicates)%10){ // check if the number of duplicates aquired have passed the 10th multiple 
									if(getCookie('rwd_notifier')){
										if(isJson(decodeURIComponent(Base64.decode(getCookie('rwd_notifier'))))){
											var storedAry = JSON.parse(decodeURIComponent(Base64.decode(getCookie('rwd_notifier'))));
											if(storedAry.duplicates_show){
												// show the duplicates notification window
												jQuery('#rwd-10-duplicates').show();

											}
										}
									}
								}
							// }
							jQuery('.fs-credits-value').html(parseInt(response.credits)+parseInt(response.bonusCredits));
							jQuery('#envelope_'+id).hide();
							
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
							jQuery('.fs-collection-complete-close').click(function(){
									jQuery('.fs-collection-complete-rewards').html('');
									jQuery('.fs-collection-complete-rewards').hide();
								});
							
							// show notifications when a collection is completed with a bought envelope
							
							if(response.completion_status == 1){
								finished = '';
								jQuery.each(response.completed_collections,function(){
									finished = finished + this.name + ', ';
								});
								jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<h3>Congratulations ! </h3><div class="fs-completion-rewards">You have just finished '+finished+' collection(s) and received: <div class="fs-rewards-background"><span><div class="fs-rewards-line fs-completion-reward-fame" title="fame">'+response.bonusFame+'</div><div class="fs-rewards-line fs-completion-reward-credits" title="credits">'+response.bonusCredits+'</div><div class="fs-rewards-line fs-completion-reward-collector" title="collector">'+response.bonusCollector+'</div><div class="fs-rewards-line fs-completion-reward-premium" title="gold">'+response.bonusPremium+'</div></span></div></div>');
								jQuery('.fs-collection-complete-rewards').show();
								
								var premi = jQuery('.fs-premium-value').html();
								jQuery('.fs-premium-value').html(parseInt(premi)+parseInt(response.bonusPremium));
								
							}
							
							// show notifications when there are first time stamps bought
							
							if(response.first_time == 'yes'){
								var first_stamps = '';
								jQuery.each(response.new_stamps,function(){
									first_stamps += this.name+', ';
								});
								
								jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="fs-completion-rewards">Great! You have discovered new stamps : <div class="fs-stamps-name">'+first_stamps+'</div> and received : <div class="fs-rewards-background"><span><div class="fs-rewards-line fs-completion-reward-fame" title="fame">'+response.first_fame+'</div><div class="fs-rewards-line fs-completion-reward-credits" title="credits">'+response.first_credits+'</div><div class="fs-rewards-line fs-completion-reward-collector" title="collector">'+response.first_collector+'</div></span></div></div>');
								jQuery('.fs-collection-complete-rewards').show();
							}
							
							// show notification when envelope levels up
							var envelope_level_value = jQuery('.fs-envelope-level-value-'+id).val();
							var env_level = Math.floor((1+Math.sqrt((4*parseInt(envelope_level_value) + 5)/5))/2);
							var env_level2 = Math.floor((1+Math.sqrt((4*(parseInt(envelope_level_value)+1) + 5)/5))/2);

							if(env_level2 > env_level){
								jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="envelope-level-up">Fantastic! Now the '+response.envelope_name+' envelope is level '+env_level2+'. New collections have been unlocked. ');
								jQuery('.fs-collection-complete-rewards').show();
							}
							
							
							
							var envelope_id = response.envelope_id;
							var stamps = response.stamps;
							var bought = '<div>';
							
							var first = 0;
							jQuery.each(stamps,function(){
								first = first + 1;
								bought = bought + '<div class="fs-bought-envelope-content order-'+first+'" id="content_'+this.id+'" style="display:none"><div class="fs-bought-big-image"><div class="fs-bought-big-frame-'+this.Shape+'"><img src="'+img_url+'/stamps/'+this.file+'" class="fs-bought-envelope-big-'+this.Shape+'"><img src="'+webroot_url+'/img_ws/new-tag.png" class="fs-bought-new-tag '+this.neww+'"></div></div><div class="fs-bought-description"><div class="fs-bought-stamp-name">'+this.name+'</div>'+this.Description+'</div><div class="fs-bought-envelope-sideinfo"><p class="fs-bought-envelope-category">CATEGORY: <b>'+this.Category+'</b></p><div class="fs-bought-envelope-stamp-value"><p class="fs-bought-stamp-value">STAMP VALUE</p><img src="'+webroot_url+'/img_ws/bg-credits-profile-stockbook.png'+'"/><p class="fs-bought-stamp-value-price">'+this.Price+'</p></div><div id="fs-btn-collect-one-'+this.id+'" class="fs-btn-collect-one"></div><div class="fs-btn-collect"></div><div class="fs-btn-sell-now" id="sell_'+this.id+'"></div></div><p class="fs-bought-stamp-collection">'+this.Collection+'</p></div>';
							});
							bought = bought + '</div>';
							bought = bought + '<div class="fs-bought-images-bottom">';
							jQuery.each(stamps,function(){
								bought = bought + '<div class="fs-bought-envelope-inner-image" title="Rarity: '+this.Rarity+'"><a href="#" id="stampp_'+this.id+'" class="fs-display-image-slide"><input type="hidden" value="'+this.id+'" id="stamp_'+this.id+'"><div class="fs-bought-frame-'+this.Shape+'"><img src="'+img_url+'/stamps/'+this.file+'" class="fs-bought-image-'+this.Shape+'" /><img src="'+webroot_url+'/img_ws/new-tag.png" class="fs-bought-new-tag '+this.neww+'"></div></a></div>';
							});
							bought = bought + '</div>';
							
							jQuery('.fs-shop-content').html(bought);
							jQuery('.fs-shop-box .order-1').css("display","block");
							jQuery('.fs-bought-envelope-inner-image a').each(function(){
								jQuery(this).click(function(){
									var idd = jQuery(this).attr('id').replace('stampp_','');
									jQuery('.fs-bought-envelope-content').hide();
									jQuery('#content_'+idd).show();
									return false;
								});
							});
							jQuery('.fs-btn-collect-one').each(function(){
								jQuery(this).click(function(){
									
									var bid = jQuery(this).attr('id').replace('fs-btn-collect-one-','');
									if(!jQuery('#content_'+bid).siblings().length){
										jQuery(".fs-shop-content").html(shop_full_content);
										jQuery('#envelope-level-up-'+id).html('+1');
										if(jQuery('.fs-envelope-level-status-'+id).html() == 1){
											jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div><div class="not-enough-money">We are sorry, but you have to grow the level of your fame so that the envelope would level up.</div></div>');
											jQuery('.fs-collection-complete-rewards').show();
											
											jQuery('.fs-collection-complete-close').click(function(){
												jQuery('.fs-collection-complete-rewards').html('');
												jQuery('.fs-collection-complete-rewards').hide();
											});
										}
										setTimeout(function() {
											jQuery(".fs-shop").trigger('click');
											jQuery(".fs-shop").trigger('click');
											shop_tabs();
										},2000);
										jQuery(".fs-shop-box").removeClass('fs-inactive');
										jQuery(".fs-shop-box").addClass('fs-active');
									}
									
									if(jQuery('#content_'+bid).next().length){
										jQuery('#content_'+bid).next().show();
									}else{
										jQuery('#content_'+bid).prev().show();
									}
									jQuery('#content_'+bid).remove();
									jQuery('#stampp_'+bid).parent().remove();
								});
							});
							jQuery(".fs-btn-sell-now").each(function(){
								jQuery(this).click(function(){
									var id = jQuery(this).attr('id').replace('sell_', '');
									//jQuery(this).remove();
									jQuery.ajax({
										url: fs_api_base + "sell_stamp/"+id,
										data: {'id': id, 'envelope_id': envelope_id,'user_id':user_id},
										type:"post",
										success:function(r){
											var response = jQuery.parseJSON(r);
											if(response.response == 'success') {
												jQuery('.fs-credits-value').html(response.credit);
												if(!jQuery('#content_'+id).siblings().length){
													jQuery(".fs-shop-content").html(shop_full_content);
													jQuery(".fs-shop").trigger('click');
													jQuery(".fs-shop-box").removeClass('fs-inactive');
													jQuery(".fs-shop-box").addClass('fs-active');
													shop_tabs();
												}
												jQuery('#content_'+id).next().show();
												jQuery('#content_'+id).remove();
												jQuery('#stampp_'+id).parent().remove();
											} else {
												// console.log("Can't sell.");
											}
										 }
								   }); 
								return false;
								
									});
							});
							jQuery(".fs-btn-collect").click(function(){
								jQuery(".fs-shop-content").html(shop_full_content);
								jQuery('#envelope-level-up-'+id).html('+1');
								if(jQuery('.fs-envelope-level-status-'+id).html() == 1){
										jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div><div class="not-enough-money">We are sorry, but you have to grow the level of your fame so that the envelope would level up.</div></div>');
										jQuery('.fs-collection-complete-rewards').show();
										
										jQuery('.fs-collection-complete-close').click(function(){
											jQuery('.fs-collection-complete-rewards').html('');
											jQuery('.fs-collection-complete-rewards').hide();
										});
									}
								setTimeout(function() {
									jQuery(".fs-shop").trigger('click');
									jQuery(".fs-shop").trigger('click');
									shop_tabs();
								},2000);
								jQuery(".fs-shop-box").removeClass('fs-inactive');
								jQuery(".fs-shop-box").addClass('fs-active');
								
								// setTimeout("window.location.reload(true);",100);
							});
						} else if(response.response == 'Invalid Envelope') {
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div><div class="not-enough-money">We are sorry, this envelope is not currently available for buying. Please wait for the administrator to add more stamps.</div></div>');
							jQuery('.fs-collection-complete-rewards').show();
							
							jQuery('.fs-collection-complete-close').click(function(){
								jQuery('.fs-collection-complete-rewards').html('');
								jQuery('.fs-collection-complete-rewards').hide();
								jQuery(".fs-shop").trigger('click');
								jQuery(".fs-shop").trigger('click');
								shop_tabs();
							});
						}else {
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div><div class="not-enough-money">We are sorry, but you do not have enough credits</div></div>');
							jQuery('.fs-collection-complete-rewards').show();
							
							jQuery('.fs-collection-complete-close').click(function(){
								jQuery('.fs-collection-complete-rewards').html('');
								jQuery('.fs-collection-complete-rewards').hide();
								jQuery(".fs-shop").trigger('click');
								jQuery(".fs-shop").trigger('click');
								shop_tabs();
							});
						}
					 }
			   }); 
			return false;
			
				});
		});
	}

	// logout 
	jQuery('.fs-inner-box-logout').click(function(){
		document.cookie = "rewardial_Logged = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		document.cookie = "rewardial_Username = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		document.cookie = "rewardial_Credits = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		document.cookie = "rewardial_Premium_Currency = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		document.cookie = "rewardial_Avatar = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		document.cookie = "rewardial_Uid = '';expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
		jQuery('#fs-shop-box').hide();
		jQuery('#fs-community-box').hide();
		jQuery('.fs-logged .fs-login-ttip').hide();
		
		setTimeout("window.location.reload(true);",500);
		
	});
	
	jQuery('.fs-close-after-click').click(function(){
		jQuery(this).parent().parent().hide();
	});

	// FACEBOOK SHARE BUTTON
	jQuery('.facebook-share-button').click(function(){
		var currentPage = window.location.href;
		if(logged == 'on'){
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'save_share','link':currentPage},
				type: 'post',
				success:function(r){
					var response = jQuery.parseJSON(r);
					if(response.status == 'unshared'){
						//var response = jQuery.parseJSON(resp);
						credits = jQuery('.fs-credits-value').html();
						new_credit = parseInt(credits)+parseInt(response.add);
						var level_up = '';
						var check_level_up = 0;
						var levels_up = 0;
						jQuery('.fs-level-attribute').each(function(){
							var name = jQuery(this).find('.fs-attribute-name').val();
							var value = jQuery('.fs-'+name+'-value').val();
							var level = jQuery('.fs-'+name+'-level').val();
							if(response.returned[name]){ 
								var total_points = parseInt(value)+parseInt(response.returned[name]);
								current_level = 25*level*level - 25*level;
								next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
								extra = parseInt(total_points) - current_level;
								points = next_level - current_level;
								percent_extra = extra*100/points;
								if(percent_extra > 99){
									percent_extra = percent_extra - 100;
									level = parseInt(level) + 1;
									level_up += '<div class="level-up-container"><div class="level-up-generic level-up-'+name+'"><div class="level-up-level">'+level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+level+' '+name+' </div></div>';
									check_level_up = 1;
									levels_up = levels_up + 1;
								}
								jQuery('#'+name+'-progress').css("width",percent_extra+'%');
								jQuery('.fs-'+name+'-value').val(total_points);
								jQuery('.fs-'+name+'-level').val(level);
								jQuery('#fs-'+name+'-level-value').html(level);
							}
						});
						
						// show notification when the user levels up
						if(check_level_up == 1){
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
							jQuery('.fs-collection-complete-close').click(function(){
								jQuery('.fs-collection-complete-rewards').html('');
								jQuery('.fs-collection-complete-rewards').hide();
							});
							jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
							jQuery('.fs-collection-complete-rewards').show();
							if(levels_up == 1){
								jQuery('.fs-collection-complete-rewards .level-up-container').css('width','100%');
							}
						}
						var credit_to_add = response.add;
						
						notify_bubble(0,credit_to_add,'sharing');
						
						jQuery('.fs-credits-value').html(new_credit);
						jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
						jQuery('.fs-notifications .fs-notifications-text').html('Reward - Share Link');
						jQuery.each(response.rewarded,function(index,value){
							if(value > 0){
								jQuery('#fs-reward-'+index).html('+'+value);
							}
						});
						
						// display the notifications for the 5th rewardable action
						if(response.display_5th_action){
							jQuery('#rwd-5-actions').show();
						}
						
					}
				}
			});
			
		}else{
			// save action into a cookie when there is no user logged in the plugin rewardial
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'anonymous_actions','type':'share','link':currentPage},
				type: 'post',
				success:function(resp){
					
					jQuery.ajax({
						url:fs_api_base+'get_action_credits',
						crossDomain:true,
						data:{'type':'share','link':my_link},
						type:"post",
						success:function(resp){
							var response = jQuery.parseJSON(resp);
							var creceive = response.credits;
							
							notify_bubble(1,creceive,'sharing');
							
						}
					});
					
				}
				
			});
			
		}
	});
	
	// mini quest 
	if(getCookie('rewardial_Logged') && getCookie('rewardial_Logged') == 'on'){
		get_answer(0);
		
		
	}else{
		var pathname = window.location.href;
		jQuery('.rewardial-question-answer').click(function(){
			var answer = jQuery(this).html();
			var quest_id = jQuery(this).attr('data-quest');
			var step_id = jQuery(this).attr('data-step');
			var question_id = jQuery(this).attr('data-question');
			var obj = jQuery(this);
			var data2 = {
				action: 'anonymous_actions',type:'mini-quest','link':pathname,answer:answer,question_id:question_id
			};
			jQuery.ajax({
				url:fs_ajax,
				type:'post',
				data:data2,
				success:function(response){
					var rs = jQuery.parseJSON(response);
					if(rs.status == 1){
						jQuery('.rewardial-question-content').append('<div class="rewardial-question-answered">Question already answered</div>');
						console.log('already answered this question');
					}else{
						jQuery.ajax({
							url:fs_api_base+'get_quest_answer',
							type:'post',
							data:{answer:answer,question_id:question_id,quest_id:quest_id,step_id:step_id},
							success:function(resp){
								
								var r = jQuery.parseJSON(resp);
								
								if(r.status == 200){
									obj.removeClass('active');
									obj.addClass('correct');
									obj.css('background','green');
									obj.css('color','#fff');
									var credit_to_add = r.credit;
									notify_bubble(0,credit_to_add,'answer');
									
									jQuery('.rewardial-question-popup').html('<span class="rwd-close-popup">X</span>You have just earned '+credit_to_add+' credits for answering correctly to the Quiz. <span class="rewardial-login-action">Login</span> to claim your reward.');
									jQuery('.rewardial-question-popup').show();
									jQuery('.rwd-close-popup').click(function(){
										jQuery(this).parent().hide();
									});
									
									jQuery('.rewardial-login-action').click(function(){
										login_button_press();
									});
									
								}else{
									obj.css('background','red');
									obj.css('color','#fff');
								}
								
							}
						});
					}
					
				}
			});
			jQuery('.quest-answer').unbind('click');
		
		});
	}
	
	
	/**********************/

	// for every comment add credit through ajax
	jQuery('#commentform [type=submit]').click(function(e){
		var submitted = localStorage['rwd-comment-submitted'];
		
		if (!submitted) {
			e.preventDefault();
			author = jQuery('#author').val();
			email = jQuery('#email').val();
			comment = jQuery('#comment').val();
			if(author && email && comment && comment.length > 25){
				
				if(logged == 'on'){
					var post_id = jQuery('#comment_post_ID').val();
					jQuery.ajax({
						url: fs_ajax,
						data: {'action':'save_comment','comment':comment,'link':window.location.href},
						type: 'post',
						beforeSend: function(){
							jQuery('#rewardial-loader').show();
						},
						success:function(resp){
							var response = jQuery.parseJSON(resp);
							credit_after = parseInt(credits)+parseInt(response.add);
							var credit_to_add = response.add;
							if(credit_to_add > 0 ){
								document.cookie = 'rewardial_Credits = '+credit_after+ '; path=/';
								document.cookie = 'rewardial_NewComment = new'+ '; path=/';
								
								jQuery('.fs-credits-value').html(credit_after);
							}
							localStorage['rwd-comment-submitted'] = "yes";
							jQuery('#commentform [type=submit]').trigger('click');
						},
						complete:function(response){
							jQuery('#rewardial-loader').hide();
						}
					});
				}else{
					jQuery.ajax({
						url: fs_ajax,
						data: {'action':'anonymous_actions','type':'comment','data':comment,'link':window.location.href},
						type: 'post',
						success:function(resp){
							
							document.cookie = 'rewardial_anonymous_comment = new'+ '; path=/';
							localStorage['rwd-comment-submitted'] = "yes";
							jQuery('#commentform [type=submit]').trigger('click');
						}
						
					});
					
				}
			}else{
					localStorage['rwd-comment-submitted'] = "yes";
					jQuery('#commentform [type=submit]').trigger('click');
			}
		}
	}); 

	var anonymous_comment = getCookie('rewardial_anonymous_comment');
	if(anonymous_comment == 'new'){
		jQuery.ajax({
			url:fs_api_base+'get_action_credits',
			crossDomain:true,
			data:{'type':'comment','link':my_link},
			type:"post",
			success:function(resp){
				var response = jQuery.parseJSON(resp);
				var creceive = response.credits;
			
				notify_bubble(1,creceive,'comment');
				
				document.cookie = "rewardial_anonymous_comment =''; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
			}
		});
	}

	jQuery('.fs-quest a').click(function(){
		if(jQuery('#fs-overlay').hasClass('fs-active')) {
				jQuery("#fs-overlay").removeClass("fs-active");
				jQuery("#fs-overlay").addClass("fs-inactive");
				jQuery('#fs-shop-box').removeClass('fs-active');
				jQuery('#fs-shop-box').addClass('fs-inactive');
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery('.fs-community').removeClass('active');
				jQuery('.fs-shop').removeClass('active');
				jQuery('.fs-quest').removeClass('active');
				
			} else {
				jQuery("#fs-overlay").removeClass("fs-inactive");
				jQuery("#fs-overlay").addClass("fs-active");
				jQuery('#fs-shop-box').removeClass('fs-active');
				jQuery('#fs-shop-box').addClass('fs-inactive');
				jQuery("#fs-community-box").removeClass("fs-active");
				jQuery("#fs-community-box").addClass("fs-inactive");
				jQuery('.fs-community').removeClass('active');
				jQuery('#fs-community').addClass('active');
				jQuery('.fs-quest').addClass('active');
				jQuery('.fs-shop').removeClass('active');
				
				
			}
	});
	
	// notifications in plugin
	
	jQuery('.rwd-notifications-close').each(function(){
		jQuery(this).click(function(){
			var parent =  jQuery(this).parent();
			var parent_id = jQuery(this).parent().attr('id');
			var check = jQuery('#'+parent_id+'-check').is(':checked');
			if(check){
				jQuery.ajax({
					url: fs_ajax,
					data: {'action':'notifications_permanently_expire','type':parent_id},
					type: 'post',
					success:function(resp){
						parent.hide();
					}
				});
			}else{
				parent.hide();
			}
			
		});
	});

	
	
	
	jQuery(document).keyup(function(e) {

	  if (e.keyCode == 27) { 
			if (jQuery('#fs-overlay').is(':visible'))
			{
                            if (quest_click){
				var data = {
                                action: 'sync_quests'
                                };
                                jQuery.ajax({
                                    url: fs_ajax,
                                    type: 'POST',
                                    data: data,
                                    success:function(response){
										jQuery('#fs-overlay').removeClass('fs-active');
                                        jQuery('#fs-overlay').addClass('fs-inactive');
                                        jQuery('html, body').css({
                                                'overflow': 'auto',
                                                'height': 'auto'
                                        });
                                    }
                                });
                            }
                            else
                            {
                                jQuery('#fs-overlay').removeClass('fs-active');
                                jQuery('#fs-overlay').addClass('fs-inactive');
                                jQuery('html, body').css({
                                        'overflow': 'auto',
                                        'height': 'auto'
                                });
                            }
			}
			if(jQuery('.class-for-escape').is(':visible')){
				jQuery('.class-for-escape').removeClass('fs-active');
				jQuery('.class-for-escape').addClass('fs-inactive');
			}
			if(jQuery('.class-for-escape1').is(':visible')){
				jQuery('.class-for-escape1').hide();
			}
	  }   // esc
	});
	function close_quest_button(){
		jQuery('.close-quest').click(function(event){
					if (quest_click){
						var data = {
						action: 'sync_quests'
						};
						jQuery.ajax({
							url: fs_ajax,
							type: 'POST',
							data: data,
							success:function(response){
								jQuery('#fs-overlay').removeClass('fs-active');
								jQuery('#fs-overlay').addClass('fs-inactive');
								jQuery('html, body').css({
										'overflow': 'auto',
										'height': 'auto'
								});
							}
						});
					}
					else
					{
						jQuery('#fs-overlay').removeClass('fs-active');
						jQuery('#fs-overlay').addClass('fs-inactive');
						jQuery('html, body').css({
								'overflow': 'auto',
								'height': 'auto'
						});
					}
		});
	}
	close_quest_button();
	quest_action();
	
	jQuery('.fs-quests-tab-new').click(function(){
		jQuery('.fs-accepted-quests-tab').addClass('invisible');
		jQuery('.fs-ended-quests-tab').addClass('invisible');
		jQuery('.fs-new-quests-tab').removeClass('invisible');
		jQuery('.fs-quests-tab').removeClass('q-active');
		jQuery('.fs-quests-tab-new').addClass('q-active');
	});
	jQuery('.fs-quests-tab-accepted').click(function(){
		jQuery('.fs-accepted-quests-tab').removeClass('invisible');
		jQuery('.fs-ended-quests-tab').addClass('invisible');
		jQuery('.fs-new-quests-tab').addClass('invisible');
		jQuery('.fs-quests-tab').removeClass('q-active');
		jQuery('.fs-quests-tab-accepted').addClass('q-active');
	});
	jQuery('.fs-quests-tab-finished').click(function(){
		jQuery('.fs-accepted-quests-tab').addClass('invisible');
		jQuery('.fs-ended-quests-tab').removeClass('invisible');
		jQuery('.fs-new-quests-tab').addClass('invisible');
		jQuery('.fs-quests-tab').removeClass('q-active');
		jQuery('.fs-quests-tab-finished').addClass('q-active');
	});
	
	// responsive actions
	
	jQuery('.responsive-my-profile').on('touchstart click',function(){
		
	});
	
	// get the file information for upload and put it into a variable
	var files;
	jQuery('#upload-avatar-form input[type=file]').on('change',function(event){
		files = event.target.files;
	});
	// on form submit send the file through ajax
	jQuery('#upload-avatar-form input[type=submit]').click(function(event){
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
		
		var data = new FormData();
		jQuery.each(files, function(key, value)
			{
				data.append(key, value);
			});
			data.append('uid',user_id);
		// console.log(data);
		jQuery.ajax({
			url:fs_api_base+'upload_avatar',
			data:data,
			cache: false,
			dataType: 'json',
			processData: false, 
			contentType: false,
			type:'post',
			success:function(r){
				if(r.status == 'ok'){
					jQuery('#fs-avatar .fs-avatar-img').attr('src',img_url+'/users/'+r.new_avatar);
					var now = new Date();
					var time = now.getTime();
					time += 9  * 3600 * 1000; // 9 hours to expire cookie
					now.setTime(time);
					document.cookie = 'rewardial_Avatar = '+r.new_avatar+'; path=/';
				}else if(r.status == 'size'){
					alert(r.message);
				}

			}
		});
	});
	jQuery('#fs-avatar').click(function(){
		jQuery('#upload-avatar-form input[type=file]').trigger('click');
		jQuery('#upload-avatar-form input[type=file]').on('change',function(){
			jQuery('#upload-avatar-form input[type=submit]').trigger('click');
		});
	});
	
	var quest_click = false;
	//var credits = getCookie('rewardial_Credits');
function save_retry_quest(quest_id){
	
					console.log('inside quest retry');
	jQuery.ajax({
		url: fs_api_base +'sync_retry_quests',
		data: { "user_id":user_id,'quest_id':quest_id},
		type:"post",
		success:function(r){
			jQuery('.quest-answer').unbind('click');
		}
	});
}
function quest_action(){
    jQuery('.quest-action').click(function(){
		jQuery(this).css('box-shadow','0 0 10px #aaaaaa inset');
		
					console.log('inside quest action');
		var quest_id = jQuery(this).attr('data-i');
		var step_id = jQuery(this).attr('data-step');
		
		// check if the clicked button is a retry quest button
		if(jQuery(this).hasClass('quest-retry')){
			var retry = 1;
			save_retry_quest(quest_id);
		}else{
			var retry = 0;
		}
		
		if (!step_id)
			step_id = 0;
		var question_id = jQuery(this).attr('data-question');
		if (!question_id)
			question_id = 0;
                jQuery("#quest-list").hide();
		jQuery('#quest-list-title').hide();
		jQuery('.fs-quests-tab').hide();
		jQuery('#ajax-loader').show();
		get_question(quest_id,step_id,question_id,retry);
                quest_click = true;
		
	});
}

function get_question(quest_id,step_id,question_id,retry){
	
					console.log('inside get question');
		data = {
			action:'quest_question',
			question_id: question_id,
			step_id: step_id,
			quest_id: quest_id,
			retry:retry
		};
		jQuery.ajax({
			type: 'POST',
			data: data,
			url: fs_ajax,
			beforeSend: function(){
				jQuery('#answer-loader').css('display','block');
			},
			complete: function(response){
				jQuery('#question-place').html(response.responseText);
				jQuery('#question-place').show();
				jQuery('#ajax-loader').hide();
				jQuery('#answer-loader').hide();
				jQuery('.quest-answer').unbind('click');
				get_answer(retry);
				back_button(retry);
			}
		});
}
var quest_rewards = {} ; // the variable where we store all the rewards received for the completed quest
quest_rewards['credits'] = 0;
quest_rewards['fame'] = 0;

function get_answer(retry){
	jQuery('.quest-answer').click(function(){
		jQuery(this).addClass('active');
		var answer = jQuery(this).html();
		var quest_id = jQuery(this).attr('data-quest');
		var step_id = jQuery(this).attr('data-step');
		var question_id = jQuery(this).attr('data-question');
		var obj = jQuery(this);
		
		var data = {
			action:'check_question_answer',
			answer:answer,
			question_id: question_id,
			step_id: step_id,
			quest_id: quest_id,
			retry: retry
		};
		jQuery.ajax({
			type:'POST',
			data:data,
			dataType: 'json',
			url: fs_ajax,
			beforeSend:function(){
				jQuery('#answer-loader').css('display','block');
			},
			complete:function(response){
				jQuery('#answer-loader').hide();
				if (response.responseJSON.status == '1')
				{
					// display the bonuses that you receive for each correct answer
					if(response.responseJSON.reward_value){
						quest_rewards['credits'] += response.responseJSON.reward_value;
						quest_rewards['fame'] += response.responseJSON.reward_value;
						if(quest_rewards[response.responseJSON.reward_name.toLowerCase()]){
							quest_rewards[response.responseJSON.reward_name.toLowerCase()] += response.responseJSON.reward_value;
						}else{
							quest_rewards[response.responseJSON.reward_name.toLowerCase()] = response.responseJSON.reward_value;
						}
						
						var credit_to_add = response.responseJSON.reward_value;
						
						notify_bubble(0,credit_to_add,'answer');
						
						jQuery('.fs-reward-box').each(function(){
							jQuery(this).html('');
						});
						
						var current_credit = jQuery('.fs-credits-value').html();
						jQuery('.fs-credits-value').html(parseInt(current_credit)+parseInt(credit_to_add));
						credits = parseInt(credits) + parseInt(credit_to_add);
						var level_up ='';
						var check_level_up = 0;
						var levels_up = 0;
							jQuery('#fs-reward-fame').html('+'+response.responseJSON.reward_value);
							jQuery('#fs-reward-fame').css('color','green');
							jQuery('#fs-reward-fame').css('text-decoration','none');
							var fame_value = jQuery('.fs-fame-value').val();
							var fame_level = jQuery('.fs-fame-level').val();	
							var total_points1 = parseInt(fame_value)+parseInt(response.responseJSON.reward_value);
								current_level1 = 25*fame_level*fame_level - 25*fame_level;
								next_level1 = 25*(parseInt(fame_level)+1)*(parseInt(fame_level)+1) - 25*(parseInt(fame_level)+1);
								extra = parseInt(total_points1) - current_level1;
								points1 = next_level1 - current_level1;
								percent_extra1 = extra*100/points1;
								if(percent_extra1 > 99){
									percent_extra1 = percent_extra1 - 100;
									fame_level = parseInt(fame_level) + 1;
									level_up += '<div class="level-up-container"><div class="level-up-generic level-up-fame"><div class="level-up-level">'+fame_level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+fame_level+' fame </div></div>';
									check_level_up = 1;
									levels_up = levels_up + 1;
								}
								jQuery('#fame-progress').css("width",percent_extra1+'%');
								jQuery('.fs-fame-value').val(total_points1);
								jQuery('.fs-fame-level').val(fame_level);
								jQuery('#fs-fame-level-value').html(fame_level);
							
						
							var reward_name = response.responseJSON.reward_name.toLowerCase();
							jQuery('#fs-reward-'+reward_name).html('+'+response.responseJSON.reward_value);
							jQuery('#fs-reward-'+reward_name).css('color','green');
							jQuery('#fs-reward-'+reward_name).css('text-decoration','none');
							var value = jQuery('.fs-'+reward_name+'-value').val();
							var level = jQuery('.fs-'+reward_name+'-level').val();
							var total_points = parseInt(value)+parseInt(response.responseJSON.reward_value);
								current_level = 25*level*level - 25*level;
								next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
								extra = parseInt(total_points) - current_level;
								points = next_level - current_level;
								percent_extra = extra*100/points;
								if(percent_extra > 99){
									percent_extra = percent_extra - 100;
									level = parseInt(level) + 1;
									level_up += '<div class="level-up-container"><div class="level-up-generic level-up-'+reward_name+'"><div class="level-up-level">'+level+'</div><div class="level-up-icon"></div></div><div class="level-up-message">You have achieved level '+level+' '+reward_name+' </div></div>';
									check_level_up = 1;
									levels_up = levels_up + 1;
								}
								jQuery('#'+reward_name+'-progress').css("width",percent_extra+'%');
								jQuery('.fs-'+reward_name+'-value').val(total_points);
								jQuery('.fs-'+reward_name+'-level').val(level);
								jQuery('#fs-'+reward_name+'-level-value').html(level);
						
						
						if(check_level_up == 1){
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
							jQuery('.fs-collection-complete-close').click(function(){
								jQuery('.fs-collection-complete-rewards').html('');
								jQuery('.fs-collection-complete-rewards').hide();
							});
							jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
							jQuery('.fs-collection-complete-rewards').show();
							if(levels_up == 1){
								jQuery('.fs-collection-complete-rewards .level-up-container').css('width','100%');
							}
						}
						
						jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
						jQuery('.fs-notifications .fs-notifications-text').html('Reward '+response.responseJSON.reward_name+' - Correct Answer');
						jQuery('.fs-notifications .fs-notifications-credit').css('color','green');
						jQuery('.fs-notifications .fs-notifications-text').css('color','green');
					}
					obj.removeClass('active');
					obj.addClass('correct');
					obj.css('background','green');
					obj.css('color','#fff');
					if (response.responseJSON.end != '1')
					{
						
						setTimeout(function (){
							get_question(response.responseJSON.quest_id,response.responseJSON.step_id,response.responseJSON.question_id,retry);
						}, 3000); // how long do you want the delay to be? 
						
					}
					else
					{
						
						setTimeout(function (){
							jQuery('#back-to-quest').click();
						},3000);
					}
				}
				else
				{
					// display the rewards that the user could have get with line-through
					jQuery('.fs-notifications').hide();
					obj.css('background','red');
					obj.css('color','#fff');
					
					jQuery('.fs-reward-box').each(function(){
						jQuery(this).html('');
					});
					
					jQuery('#fs-reward-fame').html('+'+response.responseJSON.reward_value);
					jQuery('#fs-reward-fame').css('text-decoration','line-through');
					jQuery('#fs-reward-fame').css('color','red');
					
					var reward_name = response.responseJSON.reward_name.toLowerCase();
					jQuery('#fs-reward-'+reward_name).html('+'+response.responseJSON.reward_value);
					jQuery('#fs-reward-'+reward_name).css('text-decoration','line-through');
					jQuery('#fs-reward-'+reward_name).css('color','red');
				
					
				
					jQuery('.fs-notifications .fs-notifications-text').html('Reward '+response.responseJSON.reward_name+' - Correct Answer');
					jQuery('.fs-notifications .fs-notifications-text').css('text-decoration','line-through');
					jQuery('.fs-notifications .fs-notifications-credit').css('color','green');
					jQuery('.fs-notifications .fs-notifications-text').css('color','green');
						
						
					jQuery('.quest-answer').each(function(){
							var html = jQuery(this).html();
							if (jQuery(this).html() == response.responseJSON.answer)
							{
									var obiect = jQuery(this);
									jQuery(this).addClass('correct');
									return false;
							}
					});
					setTimeout(function(){jQuery('#back-to-quest').click();},5000);
				}
				
				if(obj.hasClass('mini-quest-class')){
					quest_action();
					jQuery('.quest-action').trigger('click');
					jQuery('.quest-answer').unbind('click');
					console.log('inside quest answer');
				}
				jQuery('.quest-answer').unbind('click');
			}
		});
	});

}
function back_button(retry){
	var check_quest_rewards = 0;
    jQuery('#back-to-quest').click(function(){
		jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div><div class="fs-completion-rewards">Quest has been completed. Good job! During the quest you have earned:</div></div>');
		jQuery('.fs-collection-complete-close').click(function(){
			jQuery('.fs-collection-complete-rewards').html('');
			jQuery('.fs-collection-complete-rewards').hide();
		});
		
					console.log('inside quest back button');
		jQuery.each(quest_rewards,function(index,value){
			if(value > 0){
				check_quest_rewards = 1;
				jQuery('.fs-collection-complete-rewards .fs-completion-popup .fs-completion-rewards').append('<div class="fs-completion-reward-'+index+'">'+value+' '+index+'</div>');
			}
		});
		if(check_quest_rewards == 1){
			jQuery('.fs-collection-complete-rewards').show();
		}
            jQuery('#question-place').hide();
            jQuery('#ajax-loader').css('display','block');
            jQuery('#quest-list-title').show();
            data = {
                action: 'sync_quests',
				retry:retry
            }
            jQuery.ajax({
                url: fs_ajax,
				type: 'post',
                data: data,
                complete:function(response){
					jQuery.ajax({
						url: fs_ajax,
						type: 'post',
						data: {action: 'add_updated_quests'},
						complete:function(responser){
							//console.log(responser);
							jQuery('#fs-overlay').remove();
							jQuery('.fs-logged').append(responser.responseText);
							jQuery('#fs-overlay').removeClass('fs-inactive');
							jQuery('#fs-overlay').addClass('fs-active');
							jQuery('.quest-answer').unbind('click');
							// rewardial_actions();
							// jQuery('#quest-list').show();
							// jQuery('.fs-quests-tab').show();
							jQuery("#ajax-loader").hide();
							 quest_action();
							 close_quest_button();
							 jQuery('.fs-quests-tab-new').click(function(){
								jQuery('.fs-accepted-quests-tab').addClass('invisible');
								jQuery('.fs-ended-quests-tab').addClass('invisible');
								jQuery('.fs-new-quests-tab').removeClass('invisible');
								jQuery('.fs-quests-tab').removeClass('q-active');
								jQuery('.fs-quests-tab-new').addClass('q-active');
							});
							jQuery('.fs-quests-tab-accepted').click(function(){
								jQuery('.fs-accepted-quests-tab').removeClass('invisible');
								jQuery('.fs-ended-quests-tab').addClass('invisible');
								jQuery('.fs-new-quests-tab').addClass('invisible');
								jQuery('.fs-quests-tab').removeClass('q-active');
								jQuery('.fs-quests-tab-accepted').addClass('q-active');
							});
							jQuery('.fs-quests-tab-finished').click(function(){
								jQuery('.fs-accepted-quests-tab').addClass('invisible');
								jQuery('.fs-ended-quests-tab').removeClass('invisible');
								jQuery('.fs-new-quests-tab').addClass('invisible');
								jQuery('.fs-quests-tab').removeClass('q-active');
								jQuery('.fs-quests-tab-finished').addClass('q-active');
							});
						}
					});
                    // jQuery("#quest-list").load('/ #quest-list .fs-quest-q',function(response, status, xhr){
                        
                     // });
                }
            });
            
    });
} // end of back_button()
	
	
// facebook login errors
var fb_login_status = getCookie('rewardial_loginfb_status');
var fb_login_permissions = getCookie('rewardial_loginfb_perms');
if(fb_login_status == 'banned'){
	// alert('banned');
	jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
	jQuery('.fs-collection-complete-close').click(function(){
		jQuery('.fs-collection-complete-rewards').html('');
		jQuery('.fs-collection-complete-rewards').hide();
	});
	jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="facebook-login-status">Your account is banned. Please contact an administrator.</div>');
	jQuery('.fs-collection-complete-rewards').show();
	document.cookie = "rewardial_loginfb_status = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
}else if(fb_login_status == 'ok'){
	// alert('ok');
	document.cookie = "rewardial_loginfb_status = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
}else if(fb_login_status == 'new'){
	// alert('new');
	document.cookie = "rewardial_loginfb_status = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
}

if(fb_login_permissions == 'yes'){
	//alert('Permitted');
	document.cookie = "rewardial_loginfb_perms = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
}else if(fb_login_permissions == 'no'){
	//alert('Not Permitted');
	jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">close X</div></div>');
	jQuery('.fs-collection-complete-close').click(function(){
		jQuery('.fs-collection-complete-rewards').html('');
		jQuery('.fs-collection-complete-rewards').hide();
	});
	jQuery('.fs-collection-complete-rewards .fs-completion-popup').append('<div class="facebook-login-status">You have not accepted all the permissions from Facebook. Please accept them  and try again. </div>');
	jQuery('.fs-collection-complete-rewards').show();
	document.cookie = "rewardial_loginfb_perms = ''; expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
}
	// jQuery(window).load(function() {
		jQuery.fn.verticalMarquee = function(vertSpeed, horiSpeed, index, reward) {
		   
			// this.css('float', 'left');
			this.css('position','absolute');
			this.css('top',index);
			this.css('display','block');

			vertSpeed = vertSpeed || 1;
			horiSpeed = 1/horiSpeed || 1;

			var windowH = window.innerHeight,
				thisH = this.height(),
				parentW = (220 - this.width()) / 2,
				rand = Math.random() * (index * 1000),
				current = this;
				
				

			this.css('margin-top',  (15*windowH)/100);
			this.parent().css('overflow', 'hidden');

			var bubble_func = setInterval(function() {
				current.css({
					marginTop: function(n, v) {
						return parseFloat(v) + vertSpeed;
					},
					marginLeft: function(n, v) {
						return (Math.sin(new Date().getTime() / (horiSpeed * 1000) + rand) + 1) * parentW;
					}
				});

				if(parseInt(current.css('margin-top')) == windowH - 48 - thisH){
					clearInterval(bubble_func);
					current.hide();
					var old_credits = jQuery('#rewardial-logout-credits').html();
					if(old_credits){
						var new_credits = parseInt(old_credits)+parseInt(reward);
					}else{
						var new_credits = parseInt(reward);
					}
					
					jQuery('#rewardial-logout-credits').html(new_credits);
					
				}
				
			}, 15);
			
			

			setInterval(function() {
				if (parseFloat(current.css('margin-top')) > windowH) {
					current.css('margin-top', (15*windowH)/100);
				}
			}, 250);
		};
		
		function notify_bubble(anonymous,reward,action){
			var message = 1;
			if(anonymous == 1){
				
				var bubble_content = '<div class="rwd-bubble"><div class="rwd-bubble-content"><div class="rwd-bubble-prize"><span>+'+reward+'</span><img src="'+wp_url+'/img/credits-icon.png"></div><div class="rwd-bubble-prize-action">for '+action+'</div></div></div>';
				
				var bubble_content_login = '<div class="rwd-bubble rwd-bubble-login"><div class="rwd-bubble-content">Login to claim your rewards</div></div>';
				
				jQuery('#parent').append(bubble_content);
				jQuery('#parent').children().last().verticalMarquee(1, 1, message,reward);
				
				if(number_of_actions_rewarded%3 == 0){
					setTimeout(function(){
						jQuery('#parent').append(bubble_content_login);
						jQuery('#parent').children().last().verticalMarquee(0.8, 0.8, message,0);
						
						
						jQuery('.rwd-bubble-login').click(function(){
							login_button_press();
							jQuery(this).html('');
							jQuery(this).hide();
							jQuery('#fs-notifier3').hide();
						});
						
					},5000);
				}
				
				
			}else{
			
				var bubble_content = '<div class="rwd-bubble"><div class="rwd-bubble-content"><div class="rwd-bubble-prize"><span>+'+reward+'</span><img src="'+wp_url+'/img/fame-icon.png"></div><div class="rwd-bubble-prize-action">for '+action+'</div></div></div>';
				
				jQuery('#parent').append(bubble_content);
				jQuery('#parent').children().last().verticalMarquee(1, 1, message,reward);

			}
		}
		
	// });
} // end of function rewardial_actions

