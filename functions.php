<?php 

add_action( 'wp_ajax_nopriv_rewardial_facebook_login', 'rewardial_facebook_login_callback' );
add_action( 'wp_ajax_rewardial_facebook_login', 'rewardial_facebook_login_callback');
function rewardial_facebook_login_callback(){
	
	$userid = sanitize_text_field($_POST['fs_fb_returning']);
	$permissions = sanitize_text_field($_POST['perms']);
	$status = sanitize_text_field($_POST['check_fb_login']);
	
	$api_url = get_fs_api_url('/get_user_data');		
	$url = plugins_url('rewardial');
	$data = array('uid'=>$_POST['fs_fb_returning']);
	
	setcookie("rewardial_loginfb_status",$status,0,'/');
	if($permissions){
		$perms = 'no';
	}else{
		$perms = 'yes';
	}
	setcookie("rewardial_loginfb_perms",$perms,0,'/');
			
	if($userid and !$permissions and $status !='banned'){
	
		$my_time = sanitize_text_field($_POST['time']);
		$encode = sanitize_text_field($_POST['code']);
		
		$my_string = $my_time.$userid;
		$secret_key = get_option('focusedstamps_secret_key');
		$final_encode = hash_hmac('sha1',$my_string,$secret_key);
		
		if($final_encode == $encode){
			$response = curl_posting($data,$api_url);
		
			$resp = json_decode($response);
			$wp_url = admin_url('admin-ajax.php');
			$data = array('action'=>'save_user','username'=>$resp->username,'uid'=>$resp->uid);
			$response = curl_posting($data,$wp_url);
			if($response){
				
				setcookie("rewardial_Logged","on",0,'/');
				setcookie("rewardial_Username",$resp->username,0,'/');
				setcookie("rewardial_Credits",$resp->credits,0,'/');
				setcookie("rewardial_Premium_Currency",$resp->premium,0,'/');
				setcookie("rewardial_Avatar",$resp->avatar,0,'/');
				setcookie("rewardial_Uid",$resp->uid,0,'/');
				
				echo json_encode(array('status'=>200,'message'=>'login success')); die();
			}else{
				echo json_encode(array('status'=>300,'message'=>'login error')); die();
			}
			
		}else{
			echo json_encode(array('status'=>400,'message'=>'login error')); die();
		}
		
	}else{
		echo json_encode(array('status'=>500,'message'=>'login error')); die();
	}

}


add_action( 'wp_ajax_nopriv_anonymous_actions', 'rwd_anonymous_actions_callback' );
add_action( 'wp_ajax_anonymous_actions', 'rwd_anonymous_actions_callback');
function rwd_anonymous_actions_callback(){
// save all actions made when not logged in 

	global $wpdb;
	$_10years = 60*60*24*30*12*10;
	$after_10years = time() + $_10years;
	$link = sanitize_text_field($_POST['link']);
	$check_link = explode('#',$link);
	$final_link = $check_link[0];
	
	
	if(isset($_COOKIE['rewardial_anonymous']) and $_COOKIE['rewardial_anonymous']){
		// var_dump($_COOKIE['rewardial_anonymous']);
		$cookie = html_entity_decode($_COOKIE['rewardial_anonymous']);
		$content = json_decode($cookie,true);
	}else{
		$content = array();
	}
	// var_dump('content '.$content);
	switch($_POST['type']){
		case 'comment':
			$comment = sanitize_text_field($_POST['data']);

				$data = $content;
				$data[] = array("action"=>"comment","link"=>$final_link,"data"=>$comment);
				$final_data = htmlentities(json_encode($data,JSON_UNESCAPED_SLASHES));
				setcookie('rewardial_anonymous',$final_data,$after_10years,'/');
			
			break;
		case 'like':
			$ok = 0;
			
			foreach($content as $con){
				if($con['link'] == $final_link and $con['action'] == 'like'){
					$ok = 1;
				}
			}
			if($ok == 0){
				$data = $content;
				$data[] = array("action"=>"like","link"=>$final_link);
				$final_data = htmlentities(json_encode($data,JSON_UNESCAPED_SLASHES));
				setcookie('rewardial_anonymous',$final_data,$after_10years,'/');
			}	
			break;
		case 'unlike':
			$ok = 0;
			
			foreach($content as $con){
				if($con['link'] == $final_link and $con['action'] == 'unlike'){
					$ok = 1;
				}
			}
			if($ok == 0){
				$data = $content;
				$data[] = array("action"=>"unlike","link"=>$final_link);
				$final_data = htmlentities(json_encode($data,JSON_UNESCAPED_SLASHES));
				setcookie('rewardial_anonymous',$final_data,$after_10years,'/');
			}
			break;
		case 'share':
			$ok = 0;
			
			foreach($content as $con){
				if($con['link'] == $final_link and $con['action'] == 'share'){
					$ok = 1;
				}
			}
			if($ok == 0){
				$data = $content;
				$data[] = array("action"=>"share","link"=>$final_link);
				$final_data = htmlentities(json_encode($data,JSON_UNESCAPED_SLASHES));
				setcookie('rewardial_anonymous',$final_data,$after_10years,'/');
			}
			break;
		case 'reading':
			$ok = 0;
			foreach($content as $con){
				if($con['link'] == $final_link and $con['action'] == 'reading'){
					$ok = 1;
				}
			}
			if($ok == 0){
				$data = $content;
				$data[] = array("action"=>"reading","link"=>$final_link);
				$final_data = htmlentities(json_encode($data,JSON_UNESCAPED_SLASHES));
				setcookie('rewardial_anonymous',$final_data,$after_10years,'/');
			}
			break;
	}
	//var_dump($content);
	die();
}
function rwd_get_uid($uid){
	
	$api_url = get_option('fs_api_base');
	$data = array('uid'=>$uid);
	$url = $api_url.'/get_user_id';
	$get_uid = curl_posting($data,$url);
	if($get_uid){
	
		$get_uid = json_decode($get_uid,true);
		
		if(isset($get_uid['uid']) and $get_uid['uid']){
			$user_id = $get_uid['uid'];
		}else{
			$user_id = '';
		}
	}else{
		$user_id = '';
	}
	
	return $user_id;
	
}
function get_ip(){
	$ip = '';
	if (isset($_SERVER)) {

		$ip =  $_SERVER["REMOTE_ADDR"];
		
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            $ip = $_SERVER["HTTP_CLIENT_IP"];

    }
	return $ip;
}

add_action( 'wp_ajax_nopriv_unique_visitor', 'rwd_unique_visitor_callback' );
add_action( 'wp_ajax_add_unique_visitor', 'rwd_unique_visitor_callback');
function rwd_unique_visitor_callback(){

	if (isset($_SERVER)) {

		$ip =  $_SERVER["REMOTE_ADDR"];
		
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            $ip = $_SERVER["HTTP_CLIENT_IP"];

    }
	
	
	$api_url = get_option('fs_api_base');
	
	$cook = '';
	$cook = html_entity_decode($_COOKIE['rewardial_anonymous']);
	
	$data = array('ip'=>$ip,'link'=>get_site_url(),'time_spent'=>$_POST['time_spent'],'actions'=>$cook);
	$save_daily_visitor = curl_posting($data,$api_url.'/unique_visitors');
	
	die();

}
add_action( 'wp_ajax_nopriv_get_blog_info', 'rwd_get_blog_info_callback' );
add_action( 'wp_ajax_add_get_blog_info', 'rwd_get_blog_info_callback');
function rwd_get_blog_info_callback(){
	$plugin_info = get_option('rewardial_info');
	echo $plugin_info; die();
}

add_action( 'wp_ajax_nopriv_save_user', 'rwd_save_user_callback' );
add_action( 'wp_ajax_add_save_user', 'rwd_save_user_callback');
function rwd_save_user_callback(){
	global $wpdb;
	// $id_user = intval($_POST['uid']);
	$id_user = rwd_get_uid($_POST['uid']);
	if(!$id_user){
		die();
	}
	$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$id_user),ARRAY_A);
	$rwd_rewards = '';
	$addd_credit = 0;
	$addd_fame = 0;
	if(isset($_COOKIE['rewardial_anonymous']) and $_COOKIE['rewardial_anonymous']){
		//var_dump($_COOKIE['rewardial_anonymous']);
		$cook = html_entity_decode($_COOKIE['rewardial_anonymous']);
		$api_url = get_option('fs_api_base');
		$rwd_key = get_option('focusedstamps_secret_key');
		$rwd_time = time();
		$rwd_string = $rwd_time.$id_user;
		$rwd_code = hash_hmac('sha1',$rwd_string,$rwd_key);
		$rwd_link = get_site_url();
		
		$rwd_userdata = array('user_id'=>$id_user,'time'=>$rwd_time,'code'=>$rwd_code,'link'=>$rwd_link,'actions'=>$cook,'ip'=>get_ip());
		$rwd_data = curl_posting($rwd_userdata,$api_url.'/save_anonymous_actions');
		
		if($rwd_data){
			$rwd_rewards = json_decode($rwd_data,true);
			if($rwd_rewards){
				// save the credits into the local profile 
				foreach($rwd_rewards as $key=>$val){
					$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
					if($attr){
						$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$id_user),ARRAY_A);
						if($current_value){
							$new_value = $current_value[0]['value'] + $val;
							$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$id_user));
						}else{
							$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$id_user,'value'=>$val));
						}
					}
				}
				$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$id_user),ARRAY_A);
				$addd_credit = $rwd_rewards['credits'];
				$addd_fame = $rwd_rewards['fame'];
				$final_credit = $user_current[0]['credits'] + $addd_credit;
				$final_fame = $user_current[0]['fame'] + $addd_fame;
				$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$id_user));
			}
		}
		// var_dump($rwd_data);
		// expire the cookie 
		setcookie('rewardial_anonymous','',time() - 3600,'/');
		
		
		// add the rewards to the display when logging in :  $rwd_rewards
	}
	
	// echo json_encode(array('rwd_rewards'=>$rwd_rewards)); die();
	// echo 'test';
	// die();
	if(empty($user)){
		
		$username = sanitize_text_field($_POST['username']);
		$created = time();
		$uid = rwd_get_uid($_POST['uid']);
		$last_login = time();
		$wpdb->insert($wpdb->prefix.'focusedstamps_users',array('name'=>$username,'created'=>$created,'uid'=>$uid,'last_login'=>$last_login,'level'=>1,'credits'=>0,'premium_currency'=>0,'fame'=>0));
		$lastid = $wpdb->insert_id;
		
		$api_url = get_option('fs_api_base'); // api url to focused stamps
		
		
		
				$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
				$my_string = time().$_COOKIE['rewardial_Uid'];
				$my_code = hash_hmac('sha1',$my_string,$checkKey);
				$my_time = time();
				$my_link = get_site_url();
			
				// save the new user in the main application
			$userdata = array('user_id'=>$uid,'name'=>$username,'created'=>$created,'last_login'=>$last_login,'link'=>$my_link);
			curl_posting($userdata,$api_url.'/save_local_user');
			
			$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'first_login','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = $return['credits'];
			// save the credits into the global profile
			curl_posting($data,$api_url.'/add_credit');
			
			
			//save on global profile
			$datag = array("my_link"=>$my_link,"action"=>'first login','link'=>'','post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
			curl_posting($datag,$api_url.'/save_local_action');
	
			// save the activity in the local database
			//$uid = $_POST['uid'];
			$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'first login','credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
			
			// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] + $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
					}
				}
			}
			
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] + $add_credit;
			$final_fame = $user_current[0]['fame'] + $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
		
		
		// create the cookie for the notifications and for the action counting
			$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']++;
				$first_login_notify = $notif['first_login_active'];
				$shop_reminder = $notif['shop_reminder'];
				$community_reminder = $notif['community_reminder'];
				
				$reminder_data = array('link'=>get_site_url(),'uid'=>$uid);
				$reminder_info = curl_posting($reminder_data,$api_url.'/reminder_info');
				$remind = json_decode($reminder_info,true);
				
				
				if($remind['shop'] == 1){
					$display_shop_reminder = 1;
					$notif['shop_reminder'] = time();
				}
				
				if($remind['community'] == 1){
					$display_community_reminder = 1;
					$notif['community_reminder'] = time();
				}
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
			
			$add = $add + $addd_credit;
			
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			
		echo json_encode(array('status'=>'first_login','add'=>$add,'rewarded'=>$return,'returned'=>$return,'rwd_rewards'=>$rwd_rewards,'first_login_notify'=>$first_login_notify,'display_shop_reminder'=>$display_shop_reminder,'display_community_reminder'=>$display_community_reminder,'display_5th_action'=>$display_5th_action)); die();
	}else{
		// check if $user[0]['last_login'] 
		if($user[0]['last_login'] < mktime(0,0,0)){
			
			$api_url = get_option('fs_api_base'); // api url to focused stamps
				$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
				$my_string = time().$_COOKIE['rewardial_Uid'];
				$my_code = hash_hmac('sha1',$my_string,$checkKey);
				$my_time = time();
				$my_link = get_site_url();
			
			$username = sanitize_text_field($_POST['username']);
			$uid = rwd_get_uid($_POST['uid']);
			$last_login = time();
			// save the new user in the main application
			$userdata = array('user_id'=>$uid,'last_login'=>$last_login,'link'=>$my_link);
			curl_posting($userdata,$api_url.'/save_local_user');
			
			$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'first_login','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = $return['credits'];
			// save the credits into the global profile
			curl_posting($data,$api_url.'/add_credit');
			
			//save on global profile
			$datag = array("my_link"=>$my_link,"action"=>'first login','link'=>'','post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
			curl_posting($datag,$api_url.'/save_local_action');
	
	
			// save the activity in the local database
			$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'first login','credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
			
			// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] + $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
					}
				}
			}
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] + $add_credit;
			$final_fame = $user_current[0]['fame'] + $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
			
			
			// create the cookie for the notifications and for the action counting
			$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
			
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);

				$notif['actions']++;
				
				$shop_reminder = $notif['shop_reminder'];
				$community_reminder = $notif['community_reminder'];
				
				$reminder_data = array('link'=>get_site_url(),'uid'=>$uid);
				$reminder_info = curl_posting($reminder_data,$api_url.'/reminder_info');
				$remind = json_decode($reminder_info,true);
				
				
				if($remind['shop'] == 1){
					$display_shop_reminder = 1;
					$notif['shop_reminder'] = time();
				}
				
				if($remind['community'] == 1){
					$display_community_reminder = 1;
					$notif['community_reminder'] = time();
				}
				
				$first_login_notify = $notif['first_login_active'];
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
			
			
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			
			$add = $add + $addd_credit;
			
			echo json_encode(array('status'=>'first_login','add'=>$add,'returned'=>$return,'rewarded'=>$return,'rwd_rewards'=>$rwd_rewards,'first_login_notify'=>$first_login_notify,'display_shop_reminder'=>$display_shop_reminder,'display_community_reminder'=>$display_community_reminder,'display_5th_action'=>$display_5th_action)); 
		}else{
			echo json_encode(array('status'=>'multiple_login','rwd_rewards'=>$rwd_rewards));
		}
		$wpdb->update($wpdb->prefix.'focusedstamps_users',array('last_login'=>time()),array('uid'=>$id_user));
		die();
	}
}

add_action( 'wp_ajax_nopriv_save_comment', 'rwd_save_comment_callback' );
add_action( 'wp_ajax_add_save_comment', 'rwd_save_comment_callback');
function rwd_save_comment_callback(){
	global $wpdb;
	$uid = rwd_get_uid($_COOKIE['rewardial_Uid']);
	$api_url = get_option('fs_api_base'); // api url to focused stamps
		$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
		$my_string = time().$_COOKIE['rewardial_Uid'];
		$my_code = hash_hmac('sha1',$my_string,$checkKey);
		$my_time = time();
		$my_link = get_site_url();
		
	$link = sanitize_text_field($_POST['link']);
	$comment = sanitize_text_field($_POST['comment']);
	
	$check_existing_comment = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."focusedstamps_comments WHERE comment_content = %s",$comment),ARRAY_A);
	if($check_existing_comment){
		$add = 0;
		$return = '';
	}else{
		$today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		$date_today = date('Y-m-d h:i:s',$today);
		$date_tomorrow = date('Y-m-d h:i:s',$tomorrow);
		
		
		$find_daily_comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."focusedstamps_comments WHERE user_id = %s and comment_date >= %s and comment_date <= %s",$uid,$date_today,$date_tomorrow),ARRAY_A);
		if(count($find_daily_comments) > 24 ){
			$add = 0;
			$return = '';
		}else{
			$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'comment','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = $return['credits'];
			
			
			
			// save the credits into the global profile
			curl_posting($data,$api_url.'/add_credit');
			
			// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] + $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
					}
				}
			}
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] + $add_credit;
			$final_fame = $user_current[0]['fame'] + $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
			
			
			$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			
			//save on global profile
			$datag = array("my_link"=>$my_link,"action"=>'comment','link'=>$link,'post_title'=>'','post_content'=>$comment,'credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
			curl_posting($datag,$api_url.'/save_local_action');
			
			// create the cookie for the notifications and for the action counting
			$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']++;
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
			
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			
			// save on local profile
			// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'comment','link'=>$link,'post_content'=>$comment,'credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
		}
	}
	echo json_encode(array("add"=>$add,'rewarded'=>$return,'returned'=>$return,'display_5th_action'=>$display_5th_action)); die();
}
add_action( 'wp_ajax_nopriv_save_reading_time', 'rwd_save_reading_time_callback' );
add_action( 'wp_ajax_add_save_reading_time', 'rwd_save_reading_time_callback');
function rwd_save_reading_time_callback(){
	global $wpdb;
	$uid = rwd_get_uid($_COOKIE['rewardial_Uid']);
	$api_url = get_option('fs_api_base'); // api url to focused stamps
		$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
		$my_string = time().$_COOKIE['rewardial_Uid'];
		$my_code = hash_hmac('sha1',$my_string,$checkKey);
		$my_time = time();
		$my_link = get_site_url();
	
	$link = sanitize_text_field($_POST['link']);
	$new_link = explode('#',$link);
	$unique_link = $new_link[0];
	$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
	
	$dataction = array('my_link'=>$my_link,'action'=>'article read','link'=>$unique_link,'user_id'=>$uid);
	$pageCheck = curl_posting($dataction,$api_url.'/check_local_action');
	$pageCheck = json_decode($pageCheck,true);

	if($pageCheck['status']	== 'no'){
		$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'reading','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
		// get the credits to be added
		$added = curl_posting($data,$api_url.'/get_task_credits');
		$return = json_decode($added,true);
		$add = $return['credits'];
		// save the credits into the global profile
		curl_posting($data,$api_url.'/add_credit');
		
		//save on global profile
		$datag = array("my_link"=>$my_link,"action"=>'article read','link'=>$unique_link,'post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
		curl_posting($datag,$api_url.'/save_local_action');
	
		// save the activity in the local database
		// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'article read','link'=>$unique_link,'credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
		
		// save the credits into the local profile
		foreach($return as $key=>$val){
			$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
			if($attr){
				$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
				if($current_value){
					$new_value = $current_value[0]['value'] + $val;
					$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
				}else{
					$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
				}
			}
		}
		$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
		$add_credit = $return['credits'];
		$add_fame = $return['fame'];
		$final_credit = $user_current[0]['credits'] + $add_credit;
		$final_fame = $user_current[0]['fame'] + $add_fame;
		$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
		
		// create the cookie for the notifications and for the action counting
		$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']++;
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		
		echo json_encode(array("status"=>"not read","add"=>$add,'returned'=>$return,'rewarded'=>$return,'display_5th_action'=>$display_5th_action)); die();
	}else{
		echo json_encode(array("status"=>"read")); die();
	}
}
add_action( 'wp_ajax_nopriv_save_like', 'rwd_save_like_callback' );
add_action( 'wp_ajax_add_save_like', 'rwd_save_like_callback');
function rwd_save_like_callback(){
	global $wpdb;
	$username = sanitize_text_field($_POST['username']);
	$link = sanitize_text_field($_POST['link']);
	$uid = rwd_get_uid($_COOKIE['rewardial_Uid']);
	if($uid){
		$user = $wpdb->get_results($wpdb->prepare(" SELECT id FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
		$api_url = get_option('fs_api_base'); // api url to focused stamps
			$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
			$my_string = time().$_COOKIE['rewardial_Uid'];
			$my_code = hash_hmac('sha1',$my_string,$checkKey);
			$my_time = time();
			$my_link = get_site_url();
			
		$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'like','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
		// get the credits to be added
		$added = curl_posting($data,$api_url.'/get_task_credits');
		$return = json_decode($added,true);
		$add = $return['credits'];
		
		
		// save the credits into the global profile
		curl_posting($data,$api_url.'/add_credit');
		
		// save the credits into the local profile 
		foreach($return as $key=>$val){
			$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
			if($attr){
				$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
				if($current_value){
					$new_value = $current_value[0]['value'] + $val;
					$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
				}else{
					$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
				}
			}
		}
		$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
		$add_credit = $return['credits'];
		$add_fame = $return['fame'];
		$final_credit = $user_current[0]['credits'] + $add_credit;
		$final_fame = $user_current[0]['fame'] + $add_fame;
		$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
		
		//save on global profile
		$datag = array("my_link"=>$my_link,"action"=>'like','link'=>$link,'post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
		curl_posting($datag,$api_url.'/save_local_action');
		
		
		// create the cookie for the notifications and for the action counting
		$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']++;
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		
		
		// save the activity into the local database
		// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'like','link'=>$link,'credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
	}else{
		$add = '';
		$return = '';
	}
	echo json_encode(array('add'=>$add,'return'=>$return,'returned'=>$return,'rewarded'=>$return,'display_5th_action'=>$display_5th_action)); die();
}
add_action( 'wp_ajax_nopriv_save_unlike', 'rwd_save_unlike_callback' );
add_action( 'wp_ajax_add_save_unlike', 'rwd_save_unlike_callback');
function rwd_save_unlike_callback(){
	global $wpdb;
	$username = sanitize_text_field($_POST['username']);
	$link = sanitize_text_field($_POST['link']);
	//$add = -10;
	$uid = rwd_get_uid($_COOKIE['rewardial_Uid']);
	if($uid){
		$user = $wpdb->get_results($wpdb->prepare(" SELECT id FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
		$api_url = get_option('fs_api_base'); // api url to focused stamps	
			$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
			$my_string = time().$_COOKIE['rewardial_Uid'];
			$my_code = hash_hmac('sha1',$my_string,$checkKey);
			$my_time = time();
			$my_link = get_site_url();
			
		$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'like','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
		// get the credits to be added
		$added = curl_posting($data,$api_url.'/get_task_credits');
		$return = json_decode($added,true);
		$add = -$return['credits'];
		// save the credits into the global profile
		$data1 = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'unlike','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
		curl_posting($data1,$api_url.'/add_credit');
		
		// save the credits into the local profile 
		foreach($return as $key=>$val){
			$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
			if($attr){
				$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
				if($current_value){
					$new_value = $current_value[0]['value'] - $val;
					$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
				}else{
					$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
				}
			}
		}
		$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
		$add_credit = $return['credits'];
		$add_fame = $return['fame'];
		$final_credit = $user_current[0]['credits'] - $add_credit;
		$final_fame = $user_current[0]['fame'] - $add_fame;
		$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
		
		//save on global profile
		$datag = array("my_link"=>$my_link,"action"=>'unlike','link'=>$link,'post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
		curl_posting($datag,$api_url.'/save_local_action');
		
		// create the cookie for the notifications and for the action counting
		$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']--;
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		/*******************************************************************************/
		
		
		// save the activity on the local database
		// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'unlike','link'=>$link,'credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
	}else{
		$add = '';
		$return = '';
	}
	echo json_encode(array('add'=>$add,'return'=>$return,'returned'=>$return,'rewarded'=>$return,'display_5th_action'=>$display_5th_action)); die();
}

add_action( 'wp_ajax_nopriv_save_share', 'rwd_save_share_callback' );
add_action( 'wp_ajax_add_save_share', 'rwd_save_share_callback');
function rwd_save_share_callback(){
	global $wpdb;
	$uid = rwd_get_uid($_COOKIE['rewardial_Uid']);
	$link = sanitize_text_field($_POST['link']);
	$user = $wpdb->get_results($wpdb->prepare(" SELECT id FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
	$my_link = get_site_url();
	$api_url = get_option('fs_api_base'); // api url to focused stamps
	
	$dataction = array('my_link'=>$my_link,'action'=>'share','link'=>$link,'user_id'=>$uid);
	$pageCheck = curl_posting($dataction,$api_url.'/check_local_action');
	$pageCheck = json_decode($pageCheck,true);
	
	if($pageCheck['status'] == 'no'){
		if($pageCheck['likes'] < 11 ){
			
				$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
				$my_string = time().$_COOKIE['rewardial_Uid'];
				$my_code = hash_hmac('sha1',$my_string,$checkKey);
				$my_time = time();
				//$my_link = get_site_url();
				
			$data = array('user_id'=>$_COOKIE['rewardial_Uid'],'type'=>'share','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = $return['credits'];
			// save the credits into the global profile
			curl_posting($data,$api_url.'/add_credit');
			
			// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$uid),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] + $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$uid,'value'=>$val));
					}
				}
			}
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$uid),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] + $add_credit;
			$final_fame = $user_current[0]['fame'] + $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$uid));
			
			//save on global profile
			$datag = array("my_link"=>$my_link,"action"=>'share','link'=>$link,'post_title'=>'','post_content'=>'','credit'=>$add,'created'=>time(),'user_id'=>$uid,'ip'=>get_ip());
			curl_posting($datag,$api_url.'/save_local_action');
					
			// create the cookie for the notifications and for the action counting
			$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
			if(isset($_COOKIE['rwd_notifier']) and $_COOKIE['rwd_notifier']){
				
				$cookie_expire = time()+86400*30*12*50; // 50 years
				$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
				$notif['actions']++;
				
				if($notif['actions'] % 5 == 0){
					if($notif['actions_show'] == 1){
						$display_5th_action = 1;
					}else{
						$display_5th_action = 0;
					}
				}
				
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
				
			}else{
				setcookie('rwd_notifier',json_encode($notifier),$cookie_expire,"/"); // 50 years
				$first_login_notify = 1;
				$display_shop_reminder = 0;
				$display_community_reminder = 0;
				$display_5th_action = 0;
			}
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/
			/*******************************************************************************/		
					
			// save the activity into the local database
			// $wpdb->insert($wpdb->prefix.'focusedstamps_actions',array('action'=>'share','link'=>$link,'credit'=>$add,'created'=>time(),'user_id'=>$user[0]['id']));
			echo json_encode(array('status'=>'unshared','add'=>$add,'returned'=>$return,'rewarded'=>$return,'display_5th_action'=>$display_5th_action)); die();
		}else{
			echo json_encode(array('status'=>'shared_many')); die();
		}
	}else{
		echo json_encode(array('status'=>'shared')); die();
	}
}
add_action( 'wp_ajax_nopriv_notifications_permanently_expire', 'rwd_notifications_permanently_expire_callback' );
add_action( 'wp_ajax_add_notifications_permanently_expire', 'rwd_notifications_permanently_expire_callback');
function rwd_notifications_permanently_expire_callback(){

	$type = sanitize_text_field($_POST['type']);
	
	$notifier = array('actions'=>1,'actions_show'=>1,'duplicates'=>0,'duplicates_show'=>1,'shop_reminder'=>time(),'community_reminder'=>time(),'first_login_active'=>1,'first_shop_visit'=>time(),'check_shop_visit'=>1);
		
	$cookie_expire = time()+86400*30*12*50; // 50 years
	$notif = json_decode(stripslashes($_COOKIE['rwd_notifier']),true);
				
	if($type){
	
		switch($type){
		
			case 'rwd-5-actions':
				$notif['actions_show'] = 0;
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
			break;
			
			case 'rwd-10-duplicates':
				$notif['duplicates_show'] = 0;
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
			break;
			
			case 'rwd-first-login':
				$notif['first_login_active'] = 0;
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
			break;
			
			case 'rwd-first-shop':
				$notif['check_shop_visit'] = 0;
				setcookie('rwd_notifier',json_encode($notif),$cookie_expire,"/"); // 50 years
			break;
		}
	
	}

}

add_action( 'wp_ajax_nopriv_add_rewardial_plugin', 'add_rewardial_plugin_callback' );
add_action( 'wp_ajax_add_rewardial_plugin', 'add_rewardial_plugin_callback');
function add_rewardial_plugin_callback(){
		$url = plugins_url('rewardial');
		global $wpdb;
		$content = '';

		
		$checkKey = get_option('focusedstamps_secret_key'); // get the secret key
		$current_timer = time();
		
			$my_string = $current_timer.$_COOKIE['rewardial_Uid'];
		
		
		$my_key = hash_hmac('sha1',$my_string,$checkKey);
		ob_start(); ?>
		
		<?php 
			 $datac = array('uid'=>$_COOKIE['rewardial_Uid'],'link'=>get_site_url(),'key'=>$my_key,'time'=>$current_timer);
			$new_trades = curl_posting($datac,get_fs_api_url('/get_new_trade_notification'));
			$new_trades = json_decode($new_trades,true);
			
			
			
			$cookie_anonymous = html_entity_decode($_COOKIE['rewardial_anonymous']);
			
			$datas = array('anonymous'=>base64_encode($cookie_anonymous),'link'=>get_site_url());
			$anonymous = curl_posting($datas,get_fs_api_url('/get_anonymous_credits'));
			
			
		?>
			<div id="rewardial-plugin">
		<?php $containing_div_start = ob_get_clean(); 
			
		
			/********** the included files ( css ) ***********/
			ob_start(); ?>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" type="text/css" href="<?php echo $url; ?>/css/style2.css" />
			<link rel="stylesheet" type="text/css" href="<?php echo $url; ?>/css/flexslider.css" />
			
			<input type="hidden" id="check_logged_in" value="<?php if(isset($_COOKIE['rewardial_Logged'])) echo $_COOKIE['rewardial_Logged']; else echo $_COOKIE['rewardial_Logged']; ?>">
			<input type="hidden" id="ajaxurl-link" value="<?php echo admin_url('admin-ajax.php'); ?>">
			<?php $fs_includes = ob_get_clean();
			
			/********** the login box  ***********/
			ob_start(); ?>
				<div id="test-div-javascript" style="display:none"></div>
				<div class="rwd-bubbles-hidden" style="display:none">
					<div class="rwd-bubble rwd-bubble-fame">
						<div class="rwd-bubble-content">
							<div class="rwd-bubble-prize"><span>+5</span> <img src="<?php echo $url; ?>/img/fame-icon.png"></div>
							<div class="rwd-bubble-prize-action">for reading</div>
						</div>
					</div>
					<div class="rwd-bubble rwd-bubble-credit">
						<div class="rwd-bubble-content">
							<div class="rwd-bubble-prize"><span>+5</span> <img src="<?php echo $url; ?>/img/credits-icon.png"></div>
							<div class="rwd-bubble-prize-action">for reading</div>
						</div>
					</div>
				</div>
				<div id="parent">
					
					
					
				</div>
					<div id="fs-notifier2"></div>
					<div id="fs-notifier3"></div>
					<div id="rwd-notifications">
						<div class="rwd-central-notifications" id="rwd-5-actions">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								<b>You stacked some credits.</b> It’s time to go shopping! <div class="fs-shop fs-close-after-click">Go to Shop</div>
							</div>
							<div class="rwd-notification-remember">
								<input type="checkbox" id="rwd-5-actions-check" name="rwd-5">
									<label for="rwd-5-actions-check">Don't show this again</label>
							</div>
						</div>
						<div class="rwd-central-notifications" id="rwd-10-duplicates">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								You have plenty of <b>duplicate collectibles</b>. You can trade  them for new collectibles from other community members. <div class="fs-community fs-close-after-click">Go to community</div>
							</div>
							<div class="rwd-notification-remember">
								<input type="checkbox" id="rwd-10-duplicates-check" name="rwd-10">
									<label for="rwd-10-duplicates-check">Don't show this again</label>
							</div>
						</div>
						<div class="rwd-central-notifications" id="rwd-5-days-shop">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								You haven’t shopped in a while. There are lots of fun collectibles awaiting you here.
							</div>
							<div class="rwd-notification-remember">
							
							</div>
						</div>
						<div class="rwd-central-notifications" id="rwd-4-days-community">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								Your friends are waiting for you to exchange collectibles.
							</div>
							<div class="rwd-notification-remember">
							
							</div>
						</div>
						<div class="rwd-central-notifications" id="rwd-first-login">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								This is your <b>Rewardial profile</b>. You will earn credits and fame as you read, comment or share articles on this website. Enjoy your reading while we stow your credits for you!
							</div>
							<div class="rwd-notification-remember">
								<input type="checkbox" name="rwd-first-login" id="rwd-first-login-check">
									<label for="rwd-first-login-check">Don't show this again</label>
							</div>
						</div>
						<div class="rwd-central-notifications" id="rwd-first-shop">
							<div class="rwd-notifications-close">
								close x
							</div>
							<div class="rwd-notification-message">
								Here you can <b>buy virtual collectibles</b>. Each package includes 6 random items from the category you choose. Start collecting and enjoy.
							</div>
							<div class="rwd-notification-remember">
								
							</div>
						</div>
						
					</div>
				<div class="fs-login">
					
					<div class="fs-login-ttip">
						<div class="fs-credits" id="rewardial-logout-credits">
						<?php if(isset($_COOKIE['rewardial_anonymous']) and $_COOKIE['rewardial_anonymous']){
							$credits = json_decode($anonymous,true);
							echo $credits['credits']? $credits['credits']:0;
						}else{
							echo 0;
						}?>
						</div>
						<div class="fs-btn-login" title="Login"/>
						<div class="fs-pop-account">
							<div id="messages">
							</div>
							<div class="fs-login-form">
								<div class="fs-inner-box">
										<input type="text" class="fs-user" id="fs-login-username" placeholder="Email" value="<?php //echo $_COOKIE['rewardial_remember_me']; ?>" />

										<div class="fs-join-fields">
											<input type="password" class="fs-pass" id="fs-login-password"  placeholder="Password"  />
											<a  href="javascript:void(0);" id="fs_login_button_" class="fs-submit"></a>
										</div>
										
									<button class="fs-signup" title="Signup"></button>

									<p class="fs-noaccess"><a href="http://www.rewardial.com/users/forgot_password" target="_blank" title="Can't access your account?">Can't access</a> your account?</p>
									<!--<a href="<?php //echo get_fsfb_login_url();?>"><button class="fs-btn-fb" title="Connect with your Facebook account"></button></a>-->
									
								</div>
							</div>
							<div id="fs-sign-up" style="display:none;">
								<div class="fs-inner-box">
									<input type="text" name="first_name" class="fs-first-name" placeholder="First Name"/>
									<input type="text" name="last_name" class="fs-last-name" placeholder="Last Name"/>
									<input type="text" name="user_email" class="fs-user-email" placeholder="Email"/>
									<input type="password" name="password" class="fs-signup-password" placeholder="Password"/>
									<label><input type="checkbox" name="agreement" class="fs-signup-agreement" />I agree to the <a href="http://www.rewardial.com/page/index/terms-of-service" target="_blank">Terms of Service</a> and <a href="http://www.rewardial.com/page/index/privacy-policy" target="_blank">Privacy Policy.</a> </label>
									<a href="javascript:void(0);" class="fs-signup-submit"></a>
									<a href="javascript:void(0);" id="fs-signup-back">Back</a>
								</div>
							</div>
						</div>
						<div id="rewardial-loader-out">
							<img src="<?php echo plugins_url('rewardial'); ?>/img/ajax-loader-1.gif">
						</div>
					</div>
				</div>
			<?php $fs_login = ob_get_clean(); 

			
			/********** the logged in box  ***********/
			
			ob_start(); ?>
			
			<div class="fs-logged">
			
				
				
				<div id="fs-notifier" style="display:none"></div>
				<input type="hidden" value="<?php echo $_COOKIE['rewardial_Uid']; ?>" id="fs-user-id"/>
				<input type="hidden" value="<?php echo $current_timer; ?>" id="fs-time"/>
				<input type="hidden" value="<?php echo $my_key;?>" id="fs-code"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_login']; ?>" id="rewardial_login"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_Logged']; ?>" id="rewardial_logged"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_Uid']; ?>" id="rewardial_uid"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_Credits']; ?>" id="rewardial_credits"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_Username']; ?>" id="rewardial_username"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_TimeSpent']; ?>" id="rewardial_time_spent"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_NewComment']; ?>" id="rewardial_new_comment"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_LastPageVisited']; ?>" id="rewardial_last_page"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_login']; ?>" id="rewardial_login"/>
				<input type="hidden" value="<?php  echo $_COOKIE['rewardial_bought']; ?>" id="rewardial_bought"/>
				
				
				<div class="fs-btn-login" title="Login"/>
				<div class="fs-login-ttip">
					<div class="fs-pop-account class-for-escape1">
						<div class="fs-inner-box">
							<p class="fs-close-logged"><a href="javascript:void(0);">X</a></p>
							<p class="fs-inner-box-logout"> <a href="javascript:void(0);" title="Logout">Logout</a></p>
							<div class="fs-account-logado">
								<div class="fs-avatar-container" id="fs-avatar">
									<?php $mainweb = get_option('fs_api_base');
										$mainwebsite = explode('api',$mainweb);
										$weburl = $mainwebsite[0];
										$avatar = explode('://',$_COOKIE['rewardial_Avatar']);
										if($avatar[0] == 'https'){ 
									?>
										<img title="John Mayer" class="fs-avatar-img" src="<?php if(isset($_COOKIE['rewardial_Avatar'])) echo $_COOKIE['rewardial_Avatar'];?>">
									<? } else { ?>
									<img title="John Mayer" class="fs-avatar-img" 
										src="
										<?php if(isset($_COOKIE['rewardial_Avatar'])) { ?>
											<?php echo $weburl; ?>img/uploads/users/<?php echo $_COOKIE['rewardial_Avatar']; } ?>
										"> 
										<?php } ?>
									<div class="fs-avatar-frame"></div>
									
									
								</div>
								<div class="server-test">
									<form id="upload-avatar-form" name="upload-avatar" action="<?php echo $weburl; ?>users/upload_avatar" method="post" enctype="multipart/form-data">
										<input type="file" name="avatar">
										<input type="hidden" name="uid" value="<?php echo rwd_get_uid($_COOKIE['rewardial_Uid']); ?>">
										<input type="submit" name="submit" value="Submit">
									</form>
								</div>
								<label class="fs-username" for="fs-avatar">
									<?php echo $_COOKIE['rewardial_Username'];?>
									<p class="fs-user-role"><?php echo get_option('fs_profile_type'); ?></p>
								</label>
							</div>
						
							<div class="responsive-my-profile">
							
							</div>
							<div class="fs-account-credits-logado" title="You have <?php echo $_COOKIE['rewardial_Credits']; ?> credits. You can use these to purchase envelopes from the shop">
								<!--<p class="fs-details">Credits</p>-->
								<div class="fs-credits-value"><?php echo $_COOKIE['rewardial_Credits'];?></div>
							</div>
							<div class="fs-account-premium responsive-premium" title="You have <?php echo $_COOKIE['rewardial_Premium_Currency'];?> gold. You can use these to purchase various special items on www.rewardial.com.">
								<div class="fs-premium-value"><?php echo $_COOKIE['rewardial_Premium_Currency'];?></div>
							</div><!-- .fs-account-premium -->
							
							
							<div class="fs-account-levels">
								<?php if($_COOKIE['rewardial_Uid']): ?>
									<?php $current_user = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'focusedstamps_users WHERE uid = "%d"',rwd_get_uid($_COOKIE['rewardial_Uid'])),ARRAY_A);
									$fame = $current_user[0]['fame'];
									//$credits = $current_user[0]['credits'];
									$attributes = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'focusedstamps_attributes',ARRAY_A);
									//var_dump($attributes);
									?>
									<div class="fs-level-attribute" id="fs-level-attribute-fame">
										<?php $levelF = intval(floor(25 + sqrt(625+100*$fame))/50); //current level  ?>
										<div class="fs-fame-information fs-attribute-information" title="Fame: <?php echo $levelF; ?>. This is your overall fame on this website, a marker of your status within the community. The more activities you perform on the website, the faster it will raise.">
											<div class="fs-level-title">Fame </div>
											<div class="fs-attribute-level-value" id="fs-fame-level-value">
												<?php 
													
													if($levelF == 0 or $levelF < 0 ){ $levelF = 1; }
													echo $levelF;
													$fameCurrentLevel = 25*$levelF*$levelF - 25*$levelF; // points for current level
													$fameNextLevel = 25*($levelF+1)*($levelF+1) - 25*($levelF+1); // points for the next level
													$extraFame = $fame-$fameCurrentLevel; // the extra points between current points and points for current level
													$pointsFame = $fameNextLevel - $fameCurrentLevel; // total of points for this current level 
													if($pointsFame) $percentExtraFame = $extraFame*100/$pointsFame;
														else $percentExtraFame = 0;// percentage of extra points related to total points for this level
														if($percentExtraFame < 0) $percentExtraFame = 0;
												?>
											</div>
											<div id="fs-reward-fame" class="fs-reward-box"></div>
											<input type="hidden" class="fs-fame-value" value="<?php echo $fame;?>"/>
											<input type="hidden" class="fs-fame-level" value="<?php echo $levelF;?>"/>
											<input type="hidden" class="fs-attribute-name" value="<?php echo 'fame';?>"/>
											<div class="attribute-progress-container">
												<div id="fame-progress" style="background-color:#009933; height:10px; width:<?php echo $percentExtraFame; ?>%;"></div>
											</div>
										</div>
									</div>
									<?php if($attributes){ ?>
									<?php foreach($attributes as $attr): ?>
										<?php $value = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'focusedstamps_attributes_users WHERE attribute_id = "%s" AND user_id = "%d"',$attr['id'],rwd_get_uid($_COOKIE['rewardial_Uid'])),ARRAY_A); ?>
										<div class="fs-level-attribute" id="fs-level-attribute-<?php echo $attr['name']; ?>" style="background:url('<?php echo plugins_url('rewardial');?>/img/<?php if(in_array($attr['name'],array('knowledge','persuasion','social','spender'))) echo $attr['name']; else echo 'generic-user'; ?>-icon.png');">
											<?php $levelF = intval(floor(25 + sqrt(625+100*$value[0]['value']))/50); //current level ?>
											<div class="fs-attribute-information" title="<?php echo $attr['name'];?> : <?php echo $levelF; ?>. This is an indicator of your overall performance on this website which you may increase by performing various rewardable actions.">
												<div class="fs-level-title"><?php echo $attr['name'];?> </div>
												 <div class="fs-attribute-level-value" id="fs-<?php echo $attr['name'];?>-level-value">
													<?php 
														
														if($levelF == 0 or $levelF < 0){ $levelF = 1; }
														echo $levelF;
														$fameCurrentLevel = 25*$levelF*$levelF - 25*$levelF; // points for current level
														$fameNextLevel = 25*($levelF+1)*($levelF+1) - 25*($levelF+1); // points for the next level
														$extraFame = $value[0]['value']-$fameCurrentLevel; // the extra points between current points and points for current level
														$pointsFame = $fameNextLevel - $fameCurrentLevel; // total of points for this current level 
														if($pointsFame) $percentExtraFame = $extraFame*100/$pointsFame;
														else $percentExtraFame = 0;// percentage of extra points related to total points for this level
														if($percentExtraFame < 0) $percentExtraFame = 0;
													?>
												</div>
												<div id="fs-reward-<?php echo $attr['name']; ?>" class="fs-reward-box"></div>
												<input type="hidden" class="fs-<?php echo $attr['name'];?>-value" value="<?php echo $value[0]['value'];?>"/>
												<input type="hidden" class="fs-<?php echo $attr['name'];?>-level" value="<?php echo $levelF;?>"/>
												<input type="hidden" class="fs-attribute-name" value="<?php echo $attr['name'];?>"/>
												<div class="attribute-progress-container">
													<div id="<?php echo $attr['name'];?>-progress" style="background-color:#129bdb; height:5px; width:<?php echo $percentExtraFame; ?>%;"></div>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
									<?php } ?>
								<?php endif; ?>
							</div>
							
						</div>
						<div class="fs-support">
							<a href="http://support.rewardial.com/" target="_blank">Support Team</a>
						</div>
						<div class="fs-notifications">
							<div class="fs-notifications-credit">
							
							</div>
							<div class="fs-notifications-text">
								
							</div>
						</div>
						<?php 
						
							// $quests = fs_quests();
							// if ($quests)
								// $fs_class =" active";
							// else
								// $fs_class = '';
						
						?>
						
						<div class="fs-account-menu">
							<ul>
								<li class="fs-shop" id="fs-shop"><a href="javascript:void(0);">SHOP </a> </li>
								<li class="fs-community">
									<?php if(isset($new_trades) and $new_trades and $new_trades['trades']){ ?>
										<div class="fs-new-trades"><?php echo $new_trades['trades']; ?></div>
									<?php } ?>
									<a href="javascript:void(0);"> <?php esc_html_e('COMMUNITY');?></a> 
								</li>
								<!--<li class="fs-quest<?php echo $fs_class;?>">
									<?php if(isset($new_trades) and $new_trades){ ?>
										<div class="fs-new-quests"><?php echo $new_trades['quests']; ?></div>
									<?php } ?>
									<a href="javascript:void(0);"> QUEST</a> 
								</li>-->
								
							</ul>
						</div>
					</div>
				</div>
				<div id="rewardial-loader">
					<img src="<?php echo plugins_url('rewardial'); ?>/img/ajax-loader-1.gif">
				</div>
			</div>
			<?php 
			// if ($quests)
				// quest_area($quests);
			?>
			
			<?php $fs_logged = ob_get_clean(); 
			
			/********** the scripts  ***********/
			
				ob_start(); ?>
	<input type="hidden" value="<?php echo get_site_url(); ?>" id="fs-wordpress-link"/>
	<script>var fs_ajax = "<?php echo admin_url('admin-ajax.php');?>";</script>
	<script>var fs_api_base = "<?php echo get_fs_api_url();?>";</script>
	<script type="text/javascript" src="<?php echo $url; ?>/js/jquery.flexslider.js"></script>
	<script type="text/javascript">
			if (window.location.hash && window.location.hash == '#_=_') {
				window.location.hash = '';
				 history.pushState('', document.title, window.location.pathname); // nice and clean
				event.preventDefault(); // no page reload
			}
		</script>
	
<!--<script type="text/javascript">
  // var _gaq = _gaq || [];
  // _gaq.push(['_setAccount', 'UA-2485345-8']);
  // _gaq.push(['_trackPageview']);
  // (function() {
    // var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    // ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    // var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  // })();
</script>-->

	
		<?php
			$fs_script = ob_get_clean();
			
			/********** the shop  ***********/
			
			ob_start(); ?>
			 <?php $app_link = explode('/api',get_fs_api_url());
								$app_link = $app_link[0];
								$time = time();
								$code_s = get_option('focusedstamps_secret_key');
								$my_string = time().rwd_get_uid($_COOKIE['rewardial_Uid']);
								$final_encode = hash_hmac('sha1',$my_string,$code_s);
								?>
			<div class="fs-shop-box fs-inactive class-for-escape" id="fs-shop-box">
				<div class="fs-shop-box-inner">
					<header class="fs-shop-header">
						<div class="fs-account-info">
							<div class="fs-logo">
								<a href="http://www.rewardial.com/" target="_blank">
								<img class="rewardial-logo" src="<?php echo $url; ?>/img/logo-new.png" title="Rewardial"/>
								</a>
							</div><!-- .fs-logo -->
						 <div class="fs-account-links">
							<div class="responsive-my-profile">
							<div class="fs-profile-link"><a href="javascript:void(0);">Profile</a></div>
							</div>
							<div class="fs-account-credits" title="You have <?php echo $_COOKIE['rewardial_Credits']; ?> credits. You can use these to purchase envelopes from the shop">
								<span class="fs-details">Credits</span><br />
								<div class="fs-credits-value"><?php echo $_COOKIE['rewardial_Credits'];?></div>
							</div><!-- .fs-account-credits -->
							
							 <a href="<?php echo $app_link.'?uid='.rwd_get_uid($_COOKIE['rewardial_Uid']).'&time='.$time.'&code='.$final_encode.'&link='.get_site_url().'&page=products'; ?>" target="_blank" class="rwd-redirect-main">
								<div class="fs-account-premium" title="You have <?php echo $_COOKIE['rewardial_Premium_Currency'];?> gold. You can use these to purchase various special items on www.rewardial.com.">
									<span class="fs-details">Gold</span><br />
									<div class="fs-premium-value"><?php echo $_COOKIE['rewardial_Premium_Currency'];?></div>
								</div><!-- .fs-account-premium -->
							</a>
							<div class="fs-account-options">
							<div id="fs-avatar" class="fs-avatar-container">
								<?php $mainweb = get_option('fs_api_base');
									$mainwebsite = explode('api',$mainweb);
									$weburl = $mainwebsite[0];
									$avatar = explode('://',$_COOKIE['rewardial_Avatar']);
									if($avatar[0] == 'https'){ 
								?>
									<img title="John Mayer" class="fs-avatar-img" src="<?php if(isset($_COOKIE['rewardial_Avatar'])) echo $_COOKIE['rewardial_Avatar'];?>">
											<? } else { ?>
											<img title="John Mayer" class="fs-avatar-img" 
												src="
												<?php if(isset($_COOKIE['rewardial_Avatar'])) { ?>
													<?php echo $weburl; ?>img/uploads/users/<?php echo $_COOKIE['rewardial_Avatar']; } ?>
												"> 
												<?php } ?>
								<div class="fs-avatar-frame"></div>
								<span class="fs-level"></span>
							</div><!-- .fs-avatar -->
							<label for="fs-avatar" class="fs-username">
								<?php echo $_COOKIE['rewardial_Username'];?><br />
								<span class="fs-user-role"><?php echo get_option('fs_profile_type');?></span>
							</label>
							
						</div><!-- .fs-account-options -->
						
						</div>
							
							
						</div><!-- .fs-account-info -->

						<div class="fs-account-menu responsive-account-menu">
								<ul>
									<li class="fs-shop" id="fs-shop"><a href="javascript:void(0);">SHOP </a> </li>
									<li class="fs-community"><a href="javascript:void(0);"> <?php esc_html_e('COMMUNITY');?></a> </li>
									<!--<li class="fs-quest <?php echo 'active';?>"><a href="javascript:void(0);"> QUEST</a> </li>-->
									
								</ul>
							</div>
						<div class="fs-shop-close-button"><a href="javascript:void(0);" class="fs-close-shop"><Span>Close</span> X</a></div>
						<div class="clear"></div>
					</header><!-- .fs-shop-header -->
					
					<section class="fs-shop-content">
				
						<div class="fs-stamps-carousel">
						  
						  <div class="rewardial-shop-tabs">
							<span class="rewardial-shop-collectibles shop-tab-active">Collectibles</span>
							<span class="rewardial-shop-gifts">Gifts</span>
						  </div>
						   <a href="<?php echo $app_link.'?uid='.rwd_get_uid($_COOKIE['rewardial_Uid']).'&time='.$time.'&code='.$final_encode.'&link='.get_site_url().'&page=stockbook'; ?>" target="_blank" class="rewardial-my-stockbook"><div class="fs-my-stockbook" id="fs-username-<?php echo rwd_get_uid($_COOKIE['rewardial_Uid']); ?>">My Stockbook</div></a>
							<div class="fs-envelopes-flexslider">
							  <ul class="fs-slider slides">
								
							  </ul>
							</div>
							<?php 
								$key3 = get_option('focusedstamps_secret_key');
								$timer3 = time();
								$string3 = get_site_url().$timer3;
								$secret_key3 = hash_hmac('sha1',$string3,$key3);
								
								$api_url3 = get_fs_api_url('/get_gifts');
								
								$data3 = array('link'=>get_site_url(),'time'=>$timer3,'code'=>$secret_key3);
								$gifts_response3 = curl_posting($data3,$api_url3);
								
								if($gifts_response3){
									$gifts3 = json_decode($gifts_response3,true);
								}else{
									$gifts3 = array();
								}
								
								$main_app_link3 = str_replace('/api','',get_fs_api_url());
							?>
							<div class="rewardial-gifts-box">
								<?php if($gifts3){ ?>
									<?php foreach($gifts3 as $gift){ ?>
										<div class="rewardial-gift-content">
											<div class="rewardial-gift-title">
												<?php echo $gift['BlogGift']['title']; ?>
											</div>
											<div class="rewardial-gift-image" title="<?php echo $gift['BlogGift']['description']; ?>">
												<img src="<?php echo $main_app_link3.'files/blogs/'.$gift['BlogGift']['blog_id'].'/'.$gift['BlogGift']['image']; ?>">
											</div>
											<div class="rewardial-gift-price">
												<?php
													switch($gift['BlogGift']['payment_type']){
														case 0:
															echo $gift['BlogGift']['price'].' credits';
														break;
														
														case 1:
															echo $gift['collection_name'];
														break;
													
													}
												?> 
											</div>
											<div class="rewardial-gift-buy" id="rwd-buy-gift-<?php echo $gift['BlogGift']['id']; ?>">
												
											</div>
											
											<div class="rewardial-gift-message" id="rwd-gift-message-<?php echo $gift['BlogGift']['id']; ?>"></div>
										</div>
										
									<?php } ?>
								<?php } ?>
							</div>
						 </div><!-- .stamps-carousel -->
					</section>
				</div><!-- .shop-box inner-->
			</div><!-- .shop-box -->
		<div class="fs-collection-complete-rewards"></div>
		<?php
			$fs_shop = ob_get_clean();
			ob_start(); ?>
			<div id="fs-community-box" class="fs-inactive class-for-escape">
			<div class="fs-shop-box-inner">
				<header class="fs-community-header">
					<div class="fs-account-info">
						<div class="fs-logo">
							<a href="http://www.rewardial.com/" target="_blank">
							<img class="rewardial-logo" src="<?php echo $url; ?>/img/logo-new.png" title="Rewardial"/>
							</a>
						</div>
						
					<div class="fs-account-links">
						<div class="responsive-my-profile">
					<div class="fs-profile-link"><a href="javascript:void(0);">Profile</a></div>
					</div>
						
						<div class="fs-account-credits" title="You have <?php echo $_COOKIE['rewardial_Credits']; ?> credits. You can use these to purchase envelopes from the shop">
							<span class="fs-details">Credits</span><br />
							<div class="fs-credits-value"><?php echo $_COOKIE['rewardial_Credits'];?></div>
						</div>
						
						 <a href="<?php echo $app_link.'?uid='.rwd_get_uid($_COOKIE['rewardial_Uid']).'&time='.$time.'&code='.$final_encode.'&link='.get_site_url().'&page=products'; ?>" target="_blank" class="rwd-redirect-main">
							<div class="fs-account-premium"  title="You have <?php echo $_COOKIE['rewardial_Premium_Currency'];?> gold. You can use these to purchase various special items on www.rewardial.com.">
								<span class="fs-details">Gold</span><br />
								<div class="fs-premium-value"><?php echo $_COOKIE['rewardial_Premium_Currency'];?></div>
							</div>
						</a>
						<div class="fs-account-options">
						<div id="fs-avatar" class="fs-avatar-container">
							<?php $mainweb = get_option('fs_api_base');
							$mainwebsite = explode('api',$mainweb);
							$weburl = $mainwebsite[0];
							$avatar = explode('://',$_COOKIE['rewardial_Avatar']);
							if($avatar[0] == 'https'){ 
						?>
							<img title="John Mayer" class="fs-avatar-img" src="<?php if(isset($_COOKIE['rewardial_Avatar'])) echo $_COOKIE['rewardial_Avatar'];?>">
									<? } else { ?>
									<img title="John Mayer" class="fs-avatar-img" 
										src="
										<?php if(isset($_COOKIE['rewardial_Avatar'])) { ?>
											<?php echo $weburl; ?>img/uploads/users/<?php echo $_COOKIE['rewardial_Avatar']; } ?>
										"> 
										<?php } ?>
							<div class="fs-avatar-frame"></div>
							<span class="fs-level"></span>
						</div>
						<label for="fs-avatar" class="fs-username">
							<?php echo $_COOKIE['rewardial_Username'];?><br />
							<span class="fs-user-role"><?php echo get_option('fs_profile_type');?></span>
						</label>
						
					</div>
						<div class="clear"></div>
					</div>
					</div>

					
					<div class="fs-account-menu responsive-account-menu">
						<ul>
							<li class="fs-shop" id="fs-shop"><a href="javascript:void(0);">SHOP </a> </li>
							<li class="fs-community"><a href="javascript:void(0);"> <?php esc_html_e('COMMUNITY');?></a> </li>
							<!--<li class="fs-quest <?php echo 'active';?>"><a href="javascript:void(0);"> QUEST</a> </li>-->
							
						</ul>
					</div>
					<div class="fs-community-close-button"><a href="javascript:void(0);" class="fs-close-community"><span>CLOSE </span>X</a></div>
					
					<div class="clear"></div>
				</header>
				<section class="fs-community-content">
					 
					<div class="fs-friends-carousel">
					  <h1 class="fs-section-title">Community:</h1>
					 
					  <a href="<?php echo $app_link.'?uid='.rwd_get_uid($_COOKIE['rewardial_Uid']).'&time='.$time.'&code='.$final_encode.'&link='.get_site_url().'&page=stockbook'; ?>" target="_blank"><span class="fs-my-stockbook" id="fs-username-<?php echo rwd_get_uid($_COOKIE['rewardial_Uid']); ?>">My Stockbook</span></a>
					  <span class="fs-large">Your friends also on <?php echo get_option('blogname'); ?></span>
					 <hr class="fs-divisor" />
					 <hr class="fs-divisor-lighter" />
					 <div class="fs-friends-flexslider">
						  <ul class="fs-friends-slider slides">
							<?php 
								// get the local users from this blog
								$data = array('uid'=>$_COOKIE['rewardial_Uid'],'link'=>get_site_url(),'key'=>$my_key,'time'=>time());
								$locals = curl_posting($data,get_fs_api_url('/get_local_users'));
								$locals = json_decode($locals,true);
								
								$mainweb = get_option('fs_api_base');
								$mainwebsite = explode('api',$mainweb);
								$weburl = $mainwebsite[0];
								//var_dump($locals);
								if($locals['locals']){ 
								foreach($locals['locals'] as $local){
									$avatar = explode('://',$local['image']);
									if($avatar[0] == 'https'){
										$avatar_img = $local['image'];
									}else{
										$avatar_img = $weburl.'img/uploads/users/'.$local['image'];
									}
									$levelLocal = intval(floor(25 + sqrt(625+100*$local['level']))/50); //current level 
									if($levelLocal == 0 or $levelLocal < 0){ $levelLocal = 1; }
							?>
									<li class="fs-friend" id="fs-friend-<?php echo $local['id']; ?>">
										<div class="fs-friend-wrapper">
											<span class="fs-stamp-index-friends" title="Level: <?php echo $levelLocal; ?>. This represents the fame level of the user. The more active the user, the higher the level. More active users might also have more duplicates or unique stamps to trade."><?php echo $levelLocal; ?></span>
											<div class="fs-inner-stamp-friends">
												<div class="fs-avatar-frame-community <?php if($local['friend'] == 1) echo 'fs-avatar-frame-friend'; ?>">
													<img src="<?php echo $avatar_img; ?>" class="fs-avatar-img-friend" title="John Mayer"/>
												</div>
											</div><!-- .fs-inner-stamp -->
											<div class="fs-avatar-nameofuser">
												<?php echo $local['name']; ?>
											</div> 
											
												<div class="fs-local-user-duplicates" title="Duplicates: <?php echo $local['missing']; ?>. This is the number of duplicates that the user has and you might need."><?php echo $local['missing']; ?></div>
											
											<div class="fs-btn-trade" id="fs-trade-friend-<?php echo $local['id']; ?>"></div>
										</div>
									</li>
							<?php } ?>
							<?php } ?>
						  </ul>
					  </div>
					  <?php // check here if the user has new trade requests or not ?>
					  <?php if($new_trades['status'] == '1'){ ?>
						<a href="<?php echo $app_link.'?uid='.rwd_get_uid($_COOKIE['rewardial_Uid']).'&time='.$time.'&code='.$final_encode.'&link='.get_site_url().'&page=trades'; ?>" target="_blank"><div class="fs-trades-requests-button" id="fs-username-<?php echo rwd_get_uid($_COOKIE['rewardial_Uid']); ?>">You have trade requests!</div></a>
					  <?php } ?>
					  
						<hr class="fs-divisor" />  
					 </div><!-- .stamps-carousel -->
				</section>			
				<div class="pop-up-trade container">
					<div class="fs-trade-popup-header">
						
						<h2>Trade duplicates stamps</h2>
						<div class="fs-trade-popup-user">
							<?php $mainweb = get_option('fs_api_base');
							$mainwebsite = explode('api',$mainweb);
							$weburl = $mainwebsite[0];
							$avatar = explode('://',$_COOKIE['rewardial_Avatar']);
							if($avatar[0] == 'https'){ 
							?>
								<img title="John Mayer" class="fs-trade-avatar-img" src="<?php echo $_COOKIE['rewardial_Avatar'];?>">
							<? } else { ?>
							<img title="John Mayer" class="fs-trade-avatar-img" src="<?php if($_COOKIE['rewardial_Avatar']) { echo $weburl; ?>img/uploads/users/<?php echo $_COOKIE['rewardial_Avatar']; } ?>"> <?php } ?>
							<div class="fs-trade-avatar-frame"></div>
							<p class="fs-trade-user-name"><?php echo $_COOKIE['rewardial_Username']; ?></p>
						</div>
						<div class="fs-trade-popup-friend">
							
						</div>
					</div>
					<div class="fs-trade-popup-content">
						<div class="fs-trade-popup-user mobile">
							<?php $mainweb = get_option('fs_api_base');
							$mainwebsite = explode('api',$mainweb);
							$weburl = $mainwebsite[0];
							$avatar = explode('://',$_COOKIE['rewardial_Avatar']);
							if($avatar[0] == 'https'){ 
							?>
								<img title="John Mayer" class="fs-avatar-img" src="<?php if(isset($_COOKIE['rewardial_Avatar'])) echo $_COOKIE['rewardial_Avatar'];?>">
									<? } else { ?>
									<img title="John Mayer" class="fs-avatar-img" 
										src="
										<?php if(isset($_COOKIE['rewardial_Avatar'])) { ?>
											<?php echo $weburl; ?>img/uploads/users/<?php echo $_COOKIE['rewardial_Avatar']; } ?>
										"> 
										<?php } ?>
							<div class="fs-trade-avatar-frame"></div>
							<p class="fs-trade-user-name"><?php echo $_COOKIE['rewardial_Username']; ?></p>
						</div>
						
						<div id="fs-trade-stamp-original" class="scroll-pane">
							<div class="nav">
							
							</div>
						</div>
						<div class="fs-trade-stamp-area">
							<div class="fs-trade-stamp-left">
								<div class="you-offer">You Offer: </div>
								<div class="fs-trade-offer-stamp">
									<div class="fs-stamp-holder-image">
										
									</div>
									<div class="fs-stamp-holder-name">
									
									</div>
									<div class="fs-stamp-holder-category">
									
									</div>
									<div class="fs-stamp-holder-level">
									
									</div>
									<div class="fs-stamp-holder-rarity">
									
									</div>
								</div>
							</div>
							<div class="fs-trade-stamp-right">
								<div class="you-receive">You Receive: </div>
								<div class="fs-trade-receive-stamp">
									<div class="fs-stamp-holder-image">
										
									</div>
									<div class="fs-stamp-holder-name">
									
									</div>
									<div class="fs-stamp-holder-category">
									
									</div>
									<div class="fs-stamp-holder-level">
									
									</div>
									<div class="fs-stamp-holder-rarity">
									
									</div>
								</div>
							</div>
							<Div class="clear"></div>
							<div class="fs-trade-btns">
							<div class="fs-trade-button-middle">
								<input type="hidden" id="fs-trade-button-uid" value="<?php echo rwd_get_uid($_COOKIE['rewardial_Uid']); ?>"/>
								<input type="hidden" id="fs-trade-button-fid"/>
								<input type="hidden" id="fs-trade-button-stamp-offer"/>
								<input type="hidden" id="fs-trade-button-stamp-receive"/>
							</div>
							<div class="fs-trade-back-to-community"></div>
							<div class="clear"></div>
							 </div>
						</div>
						<div id="fs-trade-stamp-receive" class="scroll-pane">
						<div class="fs-trade-popup-friend mobile">
							
						</div>
							<div class="nav">
							
							</div>
						</div>
						
					</div>
					<div class="fs-trade-message"></div>
				</div>
			</div>
			</div>
		<?php $fs_community = ob_get_clean();
			ob_start(); ?>
			</div>
		<?php $containing_div_end = ob_get_clean(); 
		ob_start();
		?>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" type="text/css" href="<?php echo $url; ?>/css/style2.css" />
			<div id="rewardial-plugin">
				<div class="rewardial-server-inactive">
					<div class="rewardial-server-message">Server Inactive</div>
				</div>
			</div>
		<?php $server_inactive = ob_get_clean();
		if(get_option('focusedstamps_secret_key')){
			if(get_option('rewardial_server') == 'yes'){
				$content .= $containing_div_start;
				$content .= $fs_includes;
				$content .= $fs_logged;
				$content .= $fs_login;
				$content .= $fs_shop;
				$content .= $fs_community;
				$content .= $fs_script;
				$content .= $containing_div_end;
			} else if(get_option('rewardial_server') == 'no'){
				$content .=$server_inactive;
			}
			echo $content; die();
		}else{
			die();
		}
	}
	
	// add_action( 'wp_ajax_nopriv_add_updated_quests', 'fs_add_quests' );
	// add_action( 'wp_ajax_add_updated_quests', 'fs_add_quests');
	// function fs_add_quests(){
			// $quests = fs_quests();		
			// if ($quests)
				// $all_quests = quest_area($quests);
				
			// echo $all_quests; die();
	// }
?>