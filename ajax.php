<script type="text/javascript">
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
	var logged = jQuery('#rewardial-blog-logged').val();

	var user_id = jQuery('#rewardial_uid').val();
	var credits = jQuery('#rewardial_credits').val();
	var username = jQuery('#rewardial_username').val();
	var wp_url = jQuery('#fs-plugin-url').val()+'/';
	var my_link = jQuery('#rewardial-blog-url').val();
		
	if(!number_of_actions_rewarded){
		var number_of_actions_rewarded = 1;
	}
	var page_like_callback = function(url, html_element){
		/*console.log("page_like_callback");
		console.log(url);
		console.log(html_element);
		console.log(user_id);*/
		if(logged){
			var user_id = jQuery('#rewardial_uid').val();
			var credits = jQuery('#rewardial_credits').val();
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'save_like','username': username,'link': url},
				type: 'post',
				success:function(r){
					var response = jQuery.parseJSON(r);
					var level_up = '';
					var check_level_up = 0;
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
								level_up += '<div class="fs-completion-level-up">Congratulations! You are now a level '+level+' '+name+' </div>';
								check_level_up = 1;
							}
							jQuery('#'+name+'-progress').css("width",percent_extra+'%');
							jQuery('.fs-'+name+'-value').val(total_points);
							jQuery('.fs-'+name+'-level').val(level);
							jQuery('#fs-'+name+'-level-value').html(level);
						}
						if(response.display_5th_action){
							jQuery('#rwd-5-actions').show();
						}
					});
					
					if(check_level_up == 1){
							// show notification for level up
							jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">X</div></div>');
							jQuery('.fs-collection-complete-close').click(function(){
								jQuery('.fs-collection-complete-rewards').html('');
								jQuery('.fs-collection-complete-rewards').hide();
							});
							jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
							jQuery('.fs-collection-complete-rewards').show();
						}
					credits = jQuery('.fs-credits-value').html();
					new_credit = parseInt(credits)+parseInt(response.add);
					var credit_to_add = response.add;
					jQuery('.fs-credits-value').html(new_credit);
							document.cookie = "Credits = "+new_credit;
							// if(!jQuery('.fs-login-ttip span').is(":visible")){	
								// jQuery('#fs-notifier').html('+'+credit_to_add+' Reward - Like');
								// jQuery('#fs-notifier').show();
								// setTimeout(function() {
									// jQuery("#fs-notifier").fadeOut(1500);
								// },3000);
							// }
							
							// jQuery('.rwd-bubble .rwd-bubble-prize span').html('+'+credit_to_add);
							// jQuery('.rwd-bubble .rwd-bubble-prize-action').html('for like');
							notify_bubble(0,credit_to_add,'like');
							
							jQuery('.fs-credits-value').html(new_credit);
							jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
							jQuery('.fs-notifications .fs-notifications-text').html('Reward - Like');
							jQuery.each(response.rewarded,function(index,value){
								if(value > 0){
									jQuery('#fs-reward-'+index).html('+'+value);
								}
							});
				}
			});
		}else{
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'anonymous_actions','type':'like','link':url},
				type: 'post',
				success:function(resp){
					
					jQuery.ajax({
						url:fs_api_base+'get_action_credits',
						crossDomain:true,
						data:{'type':'like','link':my_link},
						type:"post",
						success:function(resp){
							var response = jQuery.parseJSON(resp);
							var creceive = response.credits;
							// jQuery('.rwd-bubble-credit .rwd-bubble-prize span').html('+'+creceive);
							// jQuery('.rwd-bubble-credit .rwd-bubble-prize-action').html('for like');
							notify_bubble(1,creceive,'like');
							
							// jQuery('#fs-notifier2').html('Login to claim your rewards');
							// jQuery('#fs-notifier2').show();
							// setTimeout(function() {
								// jQuery("#fs-notifier2").fadeOut(1500);
							// },3000);
							
						}
					});
				}
				
			});
		}
		
	}
	var page_unlike_callback = function(url, html_element){	
		/*console.log("page_unlike_callback");
		console.log(url);
		console.log(html_element);*/
		if(logged){
			var user_id = jQuery('#rewardial_uid').val();
			var credits = jQuery('#rewardial_credits').val();
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'save_unlike','username': username,'link': url},
				type: 'post',
				success:function(r){
					var response = jQuery.parseJSON(r);
					jQuery('.fs-level-attribute').each(function(){
						var name = jQuery(this).find('.fs-attribute-name').val();
						var value = jQuery('.fs-'+name+'-value').val();
						var level = jQuery('.fs-'+name+'-level').val();
						if(response.returned[name]){ 
							var total_points = parseInt(value)-parseInt(response.returned[name]);
							current_level = 25*level*level - 25*level;
							next_level = 25*(parseInt(level)+1)*(parseInt(level)+1) - 25*(parseInt(level)+1);
							extra = parseInt(total_points) - current_level;
							points = next_level - current_level;
							percent_extra = extra*100/points;
							if(percent_extra > 99){
								percent_extra = percent_extra - 100;
								level = parseInt(level) + 1;
							}
							jQuery('#'+name+'-progress').css("width",percent_extra+'%');
							jQuery('.fs-'+name+'-value').val(total_points);
							jQuery('.fs-'+name+'-level').val(level);
							jQuery('#fs-'+name+'-level-value').html(level);
						}
					});
					credits = jQuery('.fs-credits-value').html();
					new_credit = parseInt(credits)+parseInt(response.add);
					var credit_to_add = response.add;
					jQuery('.fs-credits-value').html(new_credit);
							document.cookie = "Credits = "+new_credit;
							// if(!jQuery('.fs-login-ttip span').is(":visible")){	
								// jQuery('#fs-notifier').html('+'+credit_to_add+' Reward - Unlike');
								// jQuery('#fs-notifier').show();
								// setTimeout(function() {
									// jQuery("#fs-notifier").fadeOut(1500);
								// },3000);
							// }
							
							// jQuery('.rwd-bubble .rwd-bubble-prize span').html(credit_to_add);
							// jQuery('.rwd-bubble .rwd-bubble-prize-action').html('for unlike');
							notify_bubble(0,credit_to_add,'unlike');
							
							jQuery('.fs-credits-value').html(new_credit);
							jQuery('.fs-notifications .fs-notifications-credit').html(credit_to_add);
							jQuery('.fs-notifications .fs-notifications-text').html('Reward - Unlike');
							jQuery.each(response.rewarded,function(index,value){
								if(value > 0){
									jQuery('#fs-reward-'+index).html('-'+value);
								}
							});
				}
			});
		}else{
			jQuery.ajax({
				url: fs_ajax,
				data: {'action':'anonymous_actions','type':'unlike','link':url},
				type: 'post',
				success:function(resp){
					
					// jQuery('#fs-notifier3').html('Login to claim -10 credits for unlike');
					// jQuery('#fs-notifier3').show();
					// setTimeout(function() {
						// jQuery("#fs-notifier3").fadeOut(1500);
					// },10000);
					// jQuery('#fs-notifier2').html('Login to claim your rewards');
					// jQuery('#fs-notifier2').show();
					// setTimeout(function() {
						// jQuery("#fs-notifier2").fadeOut(1500);
					// },10000);
				}
				
			});
		}
	}
	var page_comment_callback = function(object){
		// console.log(object);
		// console.log(object.commentID);
		// console.log(object.href);
		// console.log(object.message);
		// console.log(object.parentCommentID);
		
		var user_id = jQuery('#rewardial_uid').val();
		var credits = jQuery('#rewardial_credits').val();
		jQuery.ajax({
			url: fs_ajax,
			data: {'action':'save_comment','link': object.href,'comment':object.message},
			type: 'post',
			success:function(r){
				var response = jQuery.parseJSON(r);
				var level_up = '';
				var check_level_up = 0;
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
							level_up += '<div class="fs-completion-level-up">Congratulations! You are now a level '+level+' '+name+' </div>';
							check_level_up = 1;
						}
						jQuery('#'+name+'-progress').css("width",percent_extra+'%');
						jQuery('.fs-'+name+'-value').val(total_points);
						jQuery('.fs-'+name+'-level').val(level);
						jQuery('#fs-'+name+'-level-value').html(level);
					}
				});
				
				if(check_level_up == 1){
						// show notification for level up
						jQuery('.fs-collection-complete-rewards').html('<div class="fs-completion-popup"><div class="fs-collection-complete-close">X</div></div>');
						jQuery('.fs-collection-complete-close').click(function(){
							jQuery('.fs-collection-complete-rewards').html('');
							jQuery('.fs-collection-complete-rewards').hide();
						});
						jQuery('.fs-collection-complete-rewards .fs-completion-popup').append(level_up);
						jQuery('.fs-collection-complete-rewards').show();
					}
				credits = jQuery('.fs-credits-value').html();
				new_credit = parseInt(credits)+parseInt(response.add);
				var credit_to_add = response.add;
				jQuery('.fs-credits-value').html(new_credit);
						document.cookie = "Credits = "+new_credit;
						// if(!jQuery('.fs-login-ttip span').is(":visible")){	
							// jQuery('#fs-notifier').html('+'+credit_to_add);
							// jQuery('#fs-notifier').show();
							// setTimeout(function() {
								// jQuery("#fs-notifier").fadeOut(1500);
							// },3000);
						// }
						
							jQuery('.rwd-bubble .rwd-bubble-prize span').html('+'+credit_to_add);
							jQuery('.rwd-bubble .rwd-bubble-prize-action').html('for comment');
							notify_bubble(0,credit_to_add);
							
						jQuery('.fs-credits-value').html(new_credit);
						jQuery('.fs-notifications .fs-notifications-credit').html('+'+credit_to_add);
						jQuery('.fs-notifications .fs-notifications-text').html('Reward - Comment');
						jQuery.each(response.rewarded,function(index,value){
							if(value > 0){
								jQuery('#fs-reward-'+index).html('+'+value);
							}
						});
			}
		});
		
	}


if(typeof FB === 'undefined'){
	window.fbAsyncInit = function() {
	  FB.init({
		status : true, 
		cookie : true, 
		xfbml  : true  
	  });

	FB.Event.subscribe('edge.create',page_like_callback);
	FB.Event.subscribe('edge.remove', page_unlike_callback);
	FB.Event.subscribe('comment.create', page_comment_callback);
	}
}else{
	FB.Event.subscribe('edge.create', page_like_callback);
	FB.Event.subscribe('edge.remove', page_unlike_callback);
	FB.Event.subscribe('comment.create', page_comment_callback);
}

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
			
			

			// setInterval(function() {
				// if (parseFloat(current.css('margin-top')) > windowH) {
					// current.css('margin-top', (15*windowH)/100);
				// }
			// }, 250);
		};
		function notify_bubble(anonymous,reward,action){
			var message = 1;
			if(anonymous == 1){
				
				var bubble_content = '<div class="rwd-bubble"><div class="rwd-bubble-content"><div class="rwd-bubble-prize"><span>+'+reward+'</span><img src="'+wp_url+'/img/credits-icon.png"></div><div class="rwd-bubble-prize-action">for '+action+'</div></div></div>';
				
				var bubble_content_login = '<div class="rwd-bubble rwd-bubble-login"><div class="rwd-bubble-content">Login to claim your rewards</div></div>';
				
				jQuery('#parent').append(bubble_content);
				jQuery('#parent').children().last().verticalMarquee(1, 1, message,reward);
				
				if((number_of_actions_rewarded-1)%3 == 0){
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
</script>