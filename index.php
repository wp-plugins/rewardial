<?php
/* 
Plugin Name: Rewardial
Version: 0.9.9.4
Author: Puga Software
Description: Gamified engagement solution for bloggers.
*/

global $wpdb;



	function curl_posting($fields,$url)
	{
		
		if(in_array('curl',get_loaded_extensions())){
			$fields_string = '';
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			//execute post
			$result = curl_exec($ch);
			//close connection
			curl_close($ch);
			return $result;
		}else{
			$args = array('method'=>'POST','body'=>$fields);
			$response = wp_remote_post($url,$args);
			return $response['body'];
		}
	}
	function fs_after_activate_plugin(){

		
		global $api_url;   
		global $wpdb;
		$fs_options = array(
			'last_update' => time(),
			'update_interval' => 24 * 60 * 60 // 24 hours, 60 minutes, 60 seconds
			);
			
			
		// $api_url = 'http://rwd.andreil.complimentmedia.net/api';
		$api_url = 'http://www.rewardial.com/api';
		
		$test = curl_posting(array('link'=>get_site_url(),'active'=>1),$api_url.'/activate_blog'); // save active plugin on the main platform
		
		$api_url_log = get_fs_api_url('/save_blog_logs');
		$data2 = array('action'=>'activate','link'=>get_site_url(),'version'=>'0.9902');
		curl_posting($data2,$api_url_log);
		
		if (!get_option('fs_api_base'))
			add_option('fs_api_base',$api_url);
		else
			update_option('fs_api_base',$api_url);
		
		if (!get_option('fs_options'))
			add_option('fs_options', $fs_options);
		else
			update_option('fs_options',$fs_options);
		
		if(get_option('fs_activated')){
			update_option('fs_activated',' ');
		}else{
			add_option('fs_activated',' ');
		}
		
		if (get_option('focusedstamps_secret_key'))
			update_option('fs_redirect',admin_url('/admin.php?page=fs-settings&onboarding=1'));
		else
			add_option('fs_redirect',admin_url('/admin.php?page=focused-stamps'));
			
		
	}
	
	add_action('admin_init','fs_redirect');
	function fs_redirect(){
		$redirect = get_option('fs_redirect');
		delete_option('fs_redirect');
		if ($redirect)
			header('Location:'.$redirect);
	}
	
	register_deactivation_hook(__FILE__,'fs_deactivate');
	
	function fs_deactivate(){
		$api_url = get_fs_api_url('/wordpress_links');
		$blogname = get_option('blogname');
		$data = array('active' => 0, 'link'=>get_site_url());
		curl_posting($data,$api_url);
		
		$api_url_log = get_fs_api_url('/save_blog_logs');
		$data2 = array('action'=>'deactivate','link'=>get_site_url(),'version'=>'0.9902');
		curl_posting($data2,$api_url_log);
	}
	register_activation_hook(__FILE__,'fs_after_activate_plugin');
	function fs_update_all(){
		//fs_update_status();
		fs_update_settings();
		$fs_options['last_update'] = time();
		update_option('fs_options',$fs_options);
	}
	
/***** Create table into the database *****/

	global $jal_db_version;
	$jal_db_version = "1.0";

function fs_install() {
	// adding tables to the database
	global $wpdb;
	global $jal_db_version;
	
	
	
	$table_name1 = $wpdb->prefix . "focusedstamps_users";
	  
	$sql1 = "CREATE TABLE $table_name1 (
	id int(11) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	created varchar(255) NOT NULL,
	uid int(11) NOT NULL,
	last_login varchar(255) NOT NULL,
	fame int(11) NOT NULL,
	credits int(11) NOT NULL,
	premium_currency int(11) NOT NULL,
	level int(11) NOT NULL,
	PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";


	$table_name3 = $wpdb->prefix . "focusedstamps_comments";

	$sql3 = "CREATE TABLE $table_name3 (
		id int(11) NOT NULL AUTO_INCREMENT,
		comment_id int(11) NOT NULL,
		comment_post_id int(11) NOT NULL,
		comment_author varchar(255) NOT NULL,
		comment_author_email varchar(255) NOT NULL,
		comment_content text NOT NULL,
		comment_date datetime NOT NULL,
		comment_approved varchar(20) NOT NULL,
		user_id int(11) NOT NULL,
		username varchar(255) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql1);
	// dbDelta( $sql2);
	dbDelta( $sql3);
	
	$attributes_table_name = $wpdb->prefix . "focusedstamps_attributes";
	$attributes_table = "CREATE TABLE $attributes_table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		is_active varchar(255) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta( $attributes_table);
	
	$attributes_users_table_name = $wpdb->prefix . "focusedstamps_attributes_users";
	$attributes_users_table = "CREATE TABLE $attributes_users_table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		attribute_id int(11) NOT NULL,
		user_id int(11) NOT NULL,
		value int(11) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta( $attributes_users_table);

	/*** Quest tables ***/
	$quest_table_name = $wpdb->prefix . "focusedstamps_quests";
	$quest_table = "CREATE TABLE $quest_table_name (
		id int(11) NOT NULL,
		title varchar(300) NOT NULL,
		description text NOT NULL,
		user_limit int(20) NOT NULL,
		deadline varchar(100) NOT NULL,
		custom_prize varchar(300) NULL DEFAULT NULL,
		cloned int(20) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta( $quest_table );

	$quest_steps_table_name = $wpdb->prefix . "focusedstamps_quest_steps";
	$quest_steps_table = "CREATE TABLE $quest_steps_table_name (
		id int(20) NOT NULL,
		quest_id int(20) NOT NULL,
		step_nr int(20) NOT NULL,
		currency varchar(100) NOT NULL,
		number int(20) NOT NULL,
		title varchar(100) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta( $quest_steps_table );
	
	$quest_questions_table_name = $wpdb->prefix . "focusedstamps_quest_questions";
	$quest_questions_table = "CREATE TABLE $quest_questions_table_name (
		id int(20) NOT NULL,
		step_id int(20) NOT NULL,
		question varchar(300) NOT NULL,
		answer varchar(144) NOT NULL,
		wrong_1 varchar(144) NOT NULL,
		wrong_2 varchar(144) NOT NULL,
		wrong_3 varchar(144) NOT NULL,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	
	dbDelta( $quest_questions_table );
	
	$quest_user_table_name = $wpdb->prefix . "focusedstamps_quest_user";
	$quest_user_table = "CREATE TABLE $quest_user_table_name (
		id int(20) NOT NULL AUTO_INCREMENT,
		quest_id int(20) NOT NULL,
		step_id int(20) NOT NULL,
		question_id int(20) NOT NULL,
		uid int(20) NOT NULL,
		status int(2) NOT NULL DEFAULT 0,
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	
	dbDelta( $quest_user_table );	
	
	$quest_alerts_table_name = $wpdb->prefix.'focusedstamps_quest_alerts';
	$quest_alerts_table = "CREATE TABLE $quest_alerts_table_name (
		id int(20) NOT NULL,
		quest_id int(20) NOT NULL,
		text varchar(255),
		users varchar(255),
		type int(3),
		PRIMARY KEY id (id)
	)CHARACTER SET utf8 COLLATE utf8_general_ci;";
	
	dbDelta( $quest_alerts_table );
	
	
}

function get_fs_api_url($addr = '/'){
	// get the full link of the api method
	$fs_base = get_option('fs_api_base');
	return $fs_base.$addr;
}
function get_user_ip(){
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

	function fs_install_data() {
	   global $wpdb;
	   $welcome_name = "Mr. WordPress";
	   $welcome_text = "Congratulations, you just completed the installation!";
	   $table_name = $wpdb->prefix . "focusedstamps_users";
	   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
	}
	register_activation_hook( __FILE__, 'fs_install' );
	register_activation_hook( __FILE__, 'fs_install_data' );
	
	
	
	function focused_stamps_menus(){
		// add menus in the admin area
		$appName = 'Rewardial';
		$appID = 'rewardial';
		if (!get_option('focusedstamps_secret_key'))
			add_menu_page($appName,$appName, 'manage_options', 'focused-stamps', 'fs_user_info');
		else{
			add_menu_page($appName,$appName, 'manage_options', 'fs-overview', 'fs_overview');
			add_submenu_page( 'fs-overview', 'Overview', 'Overview', 'manage_options', 'fs-overview', 'fs_overview');
			//add_submenu_page( 'fs-overview', 'Quests', 'Quests', 'manage_options', 'fs-quests', 'fs_quests_page');
			add_submenu_page( 'fs-overview', 'Settings', 'Settings', 'manage_options', 'fs-settings', 'fs_settings_page');
			add_submenu_page( 'fs-overview', 'Gifts', 'Gifts', 'manage_options', 'fs-gifts', 'fs_gifts_page');
			add_submenu_page( 'fs-overview', 'Gift orders', 'Gift orders', 'manage_options', 'fs-gift-orders', 'fs_gift_orders_page');
			$api_url = get_fs_api_url('/check_rstk_option');
			$data = array('link'=>get_site_url());
			$result = curl_posting($data,$api_url);
			if($result){
				$result = json_decode($result,true);
				if($result['status'] == 200){
					add_submenu_page( 'fs-overview', 'Reset', 'Reset', 'manage_options', 'fs-reset', 'rewardial_reset_blog');
				}
			}
			
		}
	}
	add_action('admin_menu', 'focused_stamps_menus');
	function rewardial_reset_blog(){
		ob_start();?>
		<div class="fs-reset-options">
			<div class="rewardial-reset-button">
				<input type="hidden" value="<?php echo get_site_url(); ?>" class="rewardial-blog-link">
				<div id="rewardial-reset-blog">Reset Options</div>
			</div>
			<div class="rewardial-reset-button-info">
				Push this button in case the plugin didn't install correctly and try again.
			</div>
		</div>
		<?php $content = ob_get_clean();
		
		echo $content;
		
	}
	add_action( 'wp_ajax_change_order_status', 'rew_ajax_change_order_status' );
	add_action( 'wp_ajax_nopriv_change_order_status', 'rew_ajax_change_order_status' );
	function rew_ajax_change_order_status(){
		
		$order_id = intval($_POST['order_id']);
		$value_selected = intval($_POST['status']);
		
		$key = get_option('focusedstamps_secret_key');
		$timer = time();
		$string = get_site_url().$timer;
		$secret_key = hash_hmac('sha1',$string,$key);
		
		$api_url = get_fs_api_url('/order_status');
		
		$data = array('link'=>get_site_url(),'time'=>$timer,'code'=>$secret_key,'order_id'=>$order_id,'status'=>$value_selected);
		$order_status = curl_posting($data,$api_url);
		if($order_status){
			$status = json_decode($order_status,true);
			
			echo json_encode(array('status'=>$status['status'],'message'=>$status['message'])); die();
		}else{
			echo json_encode(array('status'=>'error','message'=>'Invalid request')); die();
		}
		//add_option('testttttttttttttttttttttttttttt',$order_id.'testttttttt'.$value_selected);
		
	}
	function fs_gift_orders_page(){
		global $wpdb;
		
		$key = get_option('focusedstamps_secret_key');
		$timer = time();
		$string = get_site_url().$timer;
		$secret_key = hash_hmac('sha1',$string,$key);
		
		$api_url = get_fs_api_url('/get_gift_orders');
		
		$data = array('link'=>get_site_url(),'time'=>$timer,'code'=>$secret_key);
		$gifts_response = curl_posting($data,$api_url);
		if($gifts_response){
			$gift_orders = json_decode($gifts_response,true);
		}else{
			$gift_orders = array();
		}
		
		$main_app_link = str_replace('/api','',get_fs_api_url());
		
		
		ob_start(); ?>
		
		<div class="rwd-gifts-container">
			<h3>Gift Orders</h3>
				<table class="rwd-orders-table">
					<tr>
						<th>Gift title</th>
						<th>Date created</th>
						<th>User email</th>
						<th>Status</th>
						<th>Message</th>
					</tr>
				<?php if($gift_orders){ ?>
					<?php foreach($gift_orders as $g_order){ ?>
						
						<?php foreach($g_order['orders'] as $order){ ?>
							
							<tr class="rwd-order-content">
								<td class="rwd-order-gift-name">
									<?php echo $g_order['gift']['BlogGift']['title']; ?>
								</td>
								<td class="rwd-order-gift-date">
									<?php echo date('d-m-y h:m:s',$order['order']['BlogOrder']['created']); ?>
								</td>
								<td class="rwd-order-gift-user">
									<?php echo $order['user']['user_email']; ?>
								</td>
								
								<td class="rwd-order-status-change">
									<select id="rwd-order-status-select-<?php echo $order['order']['BlogOrder']['id']; ?>">
										<option value="0" <?php if($order['order']['BlogOrder']['status'] == 0) echo 'selected="selected"'; ?>>Confirmed</option>
										<option value="1" <?php if($order['order']['BlogOrder']['status'] == 1) echo 'selected="selected"'; ?>>Shipped</option>
										<option value="2" <?php if($order['order']['BlogOrder']['status'] == 2) echo 'selected="selected"'; ?>>Closed</option>
									</select>
									<button class="rwd-order-status-button" id="rwd-order-status-<?php echo $order['order']['BlogOrder']['id']; ?>">Save</button>
								</td>
								<td class="rwd-order-status-message" id="rwd-order-status-message-<?php echo $order['order']['BlogOrder']['id']; ?>">
									
								</td>
								
							</tr>
							
						<?php } ?>
						
					<?php } ?>	
				<?php } ?>
			</table>
		
		
		</div>
		
		<?php $content = ob_get_clean();
		
		echo $content;
	}
	function fs_gifts_page(){
		
		global $wpdb;
		
			$app_link1 = str_replace('/api','/admin',get_fs_api_url());
			$code_s1 = get_option('focusedstamps_secret_key');
			$my_string1 = time();
			$final_encode1 = hash_hmac('sha1',$my_string1,$code_s1);
		
		$key = get_option('focusedstamps_secret_key');
		$timer = time();
		$string = get_site_url().$timer;
		$secret_key = hash_hmac('sha1',$string,$key);
		
		$api_url = get_fs_api_url('/get_gifts');
		
		$data = array('link'=>get_site_url(),'time'=>$timer,'code'=>$secret_key);
		$gifts_response = curl_posting($data,$api_url);
		if($gifts_response){
			$gifts = json_decode($gifts_response,true);
		}else{
			$gifts = array();
		}
		
		$main_app_link = str_replace('/api','',get_fs_api_url());
		//var_dump($gifts_response);
		
		ob_start(); ?>
		
		<div class="rwd-gifts-container">
			<h3>Gifts</h3>
			
				<?php if($gifts){ ?>
					<?php foreach($gifts as $gift){ ?>
						<div class="rwd-gifts-content">
							<div class="rwd-gift-image">
								<img src="<?php echo $main_app_link.'files/blogs/'.$gift['BlogGift']['blog_id'].'/'.$gift['BlogGift']['image']; ?>">
							</div>
							<div class="rwd-gift-title">
								<?php echo $gift['BlogGift']['title']; ?>
							</div>
							<div class="rwd-gift-description">
								<?php echo $gift['BlogGift']['description']; ?>
							</div>
							<div class="rwd-gift-price">
								<?php echo $gift['BlogGift']['price']; ?>
							</div>
							<div class="rwd-gift-min-level">
								<?php echo $gift['BlogGift']['min_level']; ?>
							</div>
							<div class="rwd-gift-collection">
								<?php echo $gift['collection_name']; ?>
							</div>
						</div>
						
						
					<?php } ?>	
					<div class="fs-redirect-button">
						<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=gifts'; ?>">Edit</a>
					</div>
				<?php } ?>
			
		
		
		</div>
		
		<?php $content = ob_get_clean();
		
		echo $content;
	
	
	}
	function fs_user_info(){
		// add content before the plugin is loaded

	
		
		global $wpdb;
		$key = get_option('focusedstamps_secret_key');
		$siteurl = get_option('siteurl');
		$secret_key = hash_hmac('sha1',$siteurl,$key);
		
		if(isset($_GET['sync'])){
			fs_update_all();
		}
		
		ob_start();?>
		
		<div id="fs-admin-page" style="margin-top:40px;">
			<input type="hidden" value="<?php echo get_option('fs_api_base'); ?>" id="fs-api-base"/>
			<input type="hidden" value="<?php echo $secret_key; ?>" class="fs-secret-key"/>
			<input type="hidden" value="<?php echo $siteurl; ?>" class="fs-siteurl"/>
			<input type="hidden" value="<?php echo $city; ?>" class="rwd-city"/>
			
			<input type="hidden" value="" class="rwd-selected-city"/>
			<input type="hidden" value="" class="rwd-selected-country"/>
			
			<div class="fs-register-block">
				<h2>Register for Rewardial</h2>
					<input type="text" name="first_name" class="fs-input-first-name" placeholder="First Name"/>
					<input type="text" name="last_name" class="fs-input-last-name" placeholder="Last name"/>
					<input type="email" name="email" class="fs-input-email" placeholder="Email"/>
					<input type="password" name="password" class="fs-input-password" placeholder="Password"/>
					
					
					
					<input type="submit" value="Submit" id="fs-register-admin-submit">
				<div class="fs-register-messages"></div>
			</div>
			
			<div class="fs-add-account">
				<h2> Add your account as an admin </h2>
				<input type="email" name="add-email" class="fs-add-email" placeholder="Email"/>
				<input type="password" name="add-password" class="fs-add-password" placeholder="Password"/>
					
				<input type="submit" value="Add account" id="fs-add-admin-submit"/>
				<div class="fs-add-account-messages"></div>
			</div>
			
			<div class="fs-accept-communication">
				<input type="checkbox" class="rewardial-accept-communication" id="rewardial-accept-claim"/>
				<label for="rewardial-accept-claim">I agree to the <a href="http://www.rewardial.com/page/index/terms-of-service" target="_blank">Terms and Conditions</a> for using the Rewardial plugin</label>
			</div>
			
			<hr/>
			<div class="fs-reset-options">
				<div class="rewardial-reset-button">
					<input type="hidden" value="<?php echo get_site_url(); ?>" class="rewardial-blog-link">
					<div id="rewardial-reset-blog">Reset Options</div>
				</div>
				<div class="rewardial-reset-button-info">
					Push this button in case the plugin didn't install correctly and try again.
				</div>
			</div>
			
		</div>
		<?php 
		$focused = ob_get_clean();
		echo $focused;
	}
	
	function fs_settings_page(){
		// admin menu settings page
		$plugin_data = get_plugin_data( __FILE__);
		if(get_option('rewardial_info')){
			update_option('rewardial_info',json_encode($plugin_data));
		}else{
			add_option('rewardial_info',json_encode($plugin_data));
		}
		global $wpdb;
		$data = array('link'=>get_site_url());
		$all_settings = curl_posting($data,get_fs_api_url('/settings_page'));
		$all_settings = json_decode($all_settings,true);
		
		$user_admin = $all_settings['user_admin'];
		
		$logo = $all_settings['logo'];
		
		$options = $all_settings['options'];
		
		$attributes = $all_settings['attributes'];
		
		$tasks = $all_settings['tasks'];
		
		$envelopes = $all_settings['envelopes'];
		
		$app_link = str_replace('/api','',get_fs_api_url()); 
		
		$deadline = '';
		$licensed = '';
		$profile_type = '';
		
			$app_link1 = str_replace('/api','/admin',get_fs_api_url());
			$code_s1 = get_option('focusedstamps_secret_key');
			$my_string1 = time();
			$final_encode1 = hash_hmac('sha1',$my_string1,$code_s1);

		ob_start(); ?>
		<div class="fs-settings-page-content">
			<div class="fs-settings-page-website-type fs-settings-page-box">
				<h4> Website </h4>
				<hr>
				<div class="fs-website-info">
					<table>
						<tr>
							<th>Type:</th>
							<?php if($options){ ?>
								<td><?php foreach($options as $option){
									if($option['WordpressOption']['option_name'] == 'profile_type'){
										$profile_type =  $option['WordpressOption']['option_value'];
										echo $profile_type;
									}
								}?></td>
							<?php } ?>
						</tr>
						<tr>
							<th>Name:</th>
							<td><?php echo get_option('blogname'); ?></td>
						</tr>
						<tr>
							<th>Admin:</th>
							<td><?php echo $user_admin['first_name'].' '.$user_admin['last_name']; ?></td>
						</tr>
						<tr>
							<th>Email:</th>
							<td><?php echo $user_admin['user_email']; ?></td>
						</tr>
					</table>
					
				</div>
				<div class="fs-website-logo">
					<img src="<?php echo $app_link; ?>/img/uploads/websites/<?php echo $logo; ?>">
				</div>
				<hr>
				<div class="fs-website-support">
					<p>For any questions or suggestions, please report back to us at  <a href="http://support.rewardial.com/" target="_blank">Support Rewardial</a></p>
				</div>
				
				

					
					<div class="fs-redirect-button">
						<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=settings&section=#website-setup'; ?>">Edit</a>
					</div>
			</div>
			
			<div class="fs-settings-page-attributes fs-settings-page-box">
				<h4> User Profile </h4>
				
				<hr>
				<div class="fs-attributes-list">
					<table>
						<tr>
							<th>Profile type</th>
							<td><?php echo $profile_type; ?></td>
						</tr>
						<tr>
							<th>Attributes</th>
							<?php if($attributes){ ?>
								<td>
								<?php foreach($attributes as $attr){ ?>
									<div><?php echo $attr['WordpressAttribute']['name']; ?></div>
								<?php } ?>
								</td>
							<?php } ?>
						</tr>
					</table>
				</div>
				<div class="fs-redirect-button">
					<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=settings&section=#user-profile'; ?>">Edit</a>
				</div>
			</div>
			
			<div class="fs-settings-page-tasks fs-settings-page-box">
				<h4> Actions </h4>
				<hr>
				<div class="fs-actions-list">
					<table>
						<tr>
							<th>Action</th>
							<th>Priority</th>
							<th>Rewards</th>
						</tr>
							<?php if($tasks){ ?>
							<?php foreach($tasks as $act){ ?>
								<tr>
									<td> <?php 
											if($act['WordpressTask']['name'] == 'first_login') echo 'First Login'; else if($act['WordpressTask']['name'] == 'like') echo 'FB Like'; else if($act['WordpressTask']['name'] == 'share') echo 'FB Share'; else echo $act['WordpressTask']['name']; 
										?>
									</td>
									<td> <?php 
										switch($act['WordpressTask']['priority']){
											case 1: $prior = 'low'; break;
											case 2: $prior = 'medium'; break;
											case 3: $prior = 'high'; break;
											case 4: $prior = 'critical'; break;
											default: $prior = 'Not selected';
										}
										echo $prior; ?>
									</td>
									<td> 
										<?php $rewards = json_decode($act['WordpressTask']['rewards'],true); 
											foreach($rewards as $key=>$reward){
												if($reward){
													echo '<span>'.$key.' : '.$reward.' '.'</span>';
												}
											}
										?>
									</td>
								</tr>
							<?php } ?>
							<?php } ?>
					</table>
				</div>
				<div class="fs-redirect-button">
					<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=settings&section=#actions-setup'; ?>">Edit</a>
				</div>
			</div>
			
			<div class="fs-settings-page-envelopes fs-settings-page-box">
				<h4> Envelopes </h4>
				<hr>
				<?php if($envelopes){ ?>
					<div class="fs-envelopes-list">
						<table>
						<tr>
							<th>Envelope</th>
							<th>Category</th>
							<th>Deadline</th>
						</tr>
						
						
							<?php $i = 1; 
								foreach($envelopes as $envelope){ ?>
									<tr>
										<td> Envelope <?php echo $i; ?></td>
										<td><?php echo $envelope['WordpressEnvelope']['category']; ?></td>
										<td><?php echo date('d-m-y H:i:s',$envelope['WordpressEnvelope']['updated']); ?></td>
									</tr>
							<?php $i++; } ?>
						
						</table>
					</div>
					
				<?php }else { ?>
					<div class="fs-no-envelopes-selected">
						None Selected.
					</div>
				<?php } ?>
				<div class="fs-redirect-button">
					<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=settings&section=#envelopes-setup'; ?>">Edit</a>
				</div>
			</div>
			<?php if(isset($_GET['onboarding']) and $_GET['onboarding'] == 1){ ?>
			<div class="fs-settings-welcome">
				<div class="welcome-text">
					<div class="welcome-first">Thank you for installing the Rewardial plugin and welcome into the Rewardial community.</div>
					<div class="welcome-second">This page will allow you to customize the plugin. You will be able to change the settings at any time.</div>
				</div>
				<div class="rwd-welcome-image">
					<img src="<?php echo plugins_url('rewardial'); ?>/img/rewardial-plugin-blog1.png">
				</div>
				<blockquote class="continue-blockquote">
					Clicking on Continue will open your account settings on the Rewardial platform for you to edit.
				</blockquote>
				
				<div class="welcome-skip">
					<a href="javascript:void(0);">Skip</a>
				</div>
				
				<div class="welcome-continue">
					<a target="_blank" href="<?php echo $app_link1.'?time='.$my_string1.'&code='.$final_encode1.'&link='.get_site_url().'&page=settings&section=#website-setup'; ?>">Continue</a>
				</div>
				
			</div>
			<div class="settings-overlay">
				
			</div>
			<?php } ?>
		</div>
		<?php $settings = ob_get_clean();
		echo $settings;
	}
	function fs_overview(){
		// admin menu overview page
	
		$plugin_data = get_plugin_data( __FILE__);
		if(get_option('rewardial_info')){
			update_option('rewardial_info',json_encode($plugin_data));
		}else{
			add_option('rewardial_info',json_encode($plugin_data));
		}
		global $wpdb;
		

		if(isset($_GET['sync'])){
			fs_update_all();
		}
		$api_url = get_fs_api_url('/overview_graphs');
		$data = array('link'=>get_site_url(),'option1'=>'license_deadline','option2'=>'licensed');
		$metrics = curl_posting($data,$api_url);
		$metrics = json_decode($metrics,true);
		
		
		$users = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."focusedstamps_users",ARRAY_A);
		
		$url = plugins_url('rewardial');
		
		$daily_logins = array();
		$months_logins = array();
		$daily_users = array();
		$monthly_users = array();
		$m_labels = '';
		$tips = array();
		if($metrics){
			$daily_logins = $metrics['daily'];
			$months_logins = $metrics['monthly'];
			$daily_users = $metrics['daily_users'];
			$monthly_users = $metrics['monthly_users'];
			$m_labels = $metrics['m_labels'];
			$tips = $metrics['tips'];
			$opt_date = $metrics['license_deadline'];
			$opt_licensed = $metrics['licensed'];
		}
				
		ob_start();?>
		
		<?php if(get_option('rewardial_server') and get_option('rewardial_server') == 'no'){ ?>
			<div class="rewardial-admin-server-inactive">
				<div class="rewardial-admin-server-message">
					Server is currently inactive. We are sorry for the inconvenience ..
				</div>
			</div>
		<?php } ?>
		<div class="wp-overview-content">
			<div class="wp-overview-description-content">
				<h3> Description </h3>
				<div class="wp-description-about">
					
				<p>RewarDial is an online customer loyalty platform for publishing and e-commerce websites. It functions on gamification principles, which means applying game-like mechanics in real life processes. Within RewarDial, the online readers and shoppers will play a collectibles game where:</p>
				<p>- They will feel rewarded for their time spent online via credits earned for meaningful actions;</p>
				<p>- They will enjoy the thrill of discovery when spending their credits to buy „surprise” collectibles;</p>
				<p>- They will feel socially engaged while connecting with other website users to exchange duplicate collectibles.</p>
				<p>Besides the „collectibles game” for the end users, RewarDial also contains a powerful tool for the website owners – the Quests module. Through quests, the website owner may „direct” user activity towards specific content (either your own or a brand’s you want to advertise).</p>
				<p>Have a look at the Settings options, unleash your creativity in using them and keep an eye on the website metrics! We trust you will be pleased with the results!</p>

				</div>
				<?php if($tips){ ?>
				<div class="wp-description-tips flexslider tips-flexslider">
					<ul class="slides">
						<?php $mainweb = get_option('fs_api_base');
							$mainwebsite = explode('api',$mainweb);
							$weburl = $mainwebsite[0];
							foreach($tips as $tip){
						?>
						<li><img src="<?php echo $weburl; ?>/img/tips/<?php echo $tip['Tips']['img']; ?>"/></li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
				<div class="fs-redirect-button">
					<div class="wp-sync-all"><a href="<?php echo admin_url('/admin.php?page=fs-overview&sync=1');?>">Sync all details</a></div>
				</div>
			</div>
			
			<div class="wp-overview-metrics-content">
				<h3>Metrics</h3>
				<div class="dau-container">
					<h2> Daily Active Users (DAU): <?php echo count($daily_users); ?></h2>
					<div id="local-dau-graph" style="width: 100%">
						<canvas id="local-metrics-dau" height="450" width="600"></canvas>
					</div>
					<?php $today = time(); $_30_days = time() - 29*24*60*60; $labels = '';?>
					<?php foreach($daily_logins as $log) { $labels = $labels.'"'.date('d-m-y',$_30_days).'" ,'; $_30_days = $_30_days + 24*60*60;} ?>
					<script>
						var data_dau = {
							labels : [<?php echo $labels; ?>],
							datasets : [
								{
									fillColor : "rgba(151,187,205,0.5)",
									strokeColor : "rgba(151,187,205,0.8)",
									highlightFill : "rgba(151,187,205,0.75)",
									highlightStroke : "rgba(151,187,205,1)",
									data : [<?php foreach($daily_logins as $day){ echo $day.','; } ?>]
								}
							]
						}
					</script>
				</div>
				<div class="mau-container">
					<h2> Monthly Active Users (MAU) : <?php echo count($monthly_users); ?> </h2>
					<div id="local-mau-graph" style="width: 100%">
						<canvas id="local-metrics-mau" height="450" width="600"></canvas>
					</div>
					<script>
						var data_mau = {
							labels : [<?php echo $m_labels; ?>],
							datasets : [
								{
									fillColor : "rgba(151,187,205,0.5)",
									strokeColor : "rgba(151,187,205,0.8)",
									highlightFill : "rgba(151,187,205,0.75)",
									highlightStroke : "rgba(151,187,205,1)",
									data : [<?php foreach($months_logins as $month){ echo $month.','; }?>]
								}
							]

						}
					</script>	
				</div>
				<script>
					window.onload = function(){
						var ctx1 = document.getElementById("local-metrics-dau").getContext("2d");
						var dau_char  = new Chart(ctx1).Bar(data_dau, {
							responsive : true
						});
						
						var ctx2 = document.getElementById("local-metrics-mau").getContext("2d");
						var mau_chart  = new Chart(ctx2).Bar(data_mau, {
							responsive : true
						});
					}
				</script>
			</div>
		</div>
		
		<?php $overview = ob_get_clean();
		echo $overview;
	}

	function fs_custom_admin_head(){
		$url = plugins_url('rewardial');
		echo '<link rel="stylesheet" type="text/css" href="'.$url.'/css/admin-style.css">';
		echo '<link rel="stylesheet" type="text/css" href="'.$url.'/css/flexslider.css">';
		echo '<script type="text/javascript" src="'.$url.'/js/jquery.flexslider.js"></script>';
		echo '<script type="text/javascript" src="'.$url.'/js/Chart.js"></script>';
		echo '<script type="text/javascript" src="'.$url.'/js/admin-js.js"></script>';
	}
	add_action('admin_head', 'fs_custom_admin_head');
	
	add_action( 'wp_enqueue_script', 'load_jquery' );
	function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}
	
	function fs_add_js($content){
		// add the javascript files
		$page = '';
		if(is_single()) $page = 'post';
		$url = plugins_url('rewardial');
		ob_start();
	?>
		<div class="rewardial_links">
			<input type="hidden" id="fs-plugin-url" value="<?php echo plugins_url('rewardial'); ?>"/>
			<input type="hidden" id="rewardial-blog-url" value="<?php echo get_site_url(); ?>"/>
			<input type="hidden" id="rewardial-blog-logged" value="<?php if(isset($_COOKIE['rewardial_Logged'])) echo $_COOKIE['rewardial_Logged']; ?>"/>
			<input type="hidden" id="rewardial-page-type" value="<?php echo $page; ?>"/>
			<input type="hidden" id="rewardial-page-link" value="<?php the_permalink() ?>"/>
			
		</div>
		<div id="rewardial-plugin-loader">
			<img class="rewardial-first-loader" src="<?php echo $url; ?>/img/ajax-loader-1.gif" style="position:fixed; right:50px; bottom:0; display:none; width:100px; z-index:99999;">
		</div>
		<script>var fs_ajax = "<?php echo admin_url('admin-ajax.php');?>";</script>
		<script>var fs_api_base = "<?php echo get_fs_api_url();?>";</script>
		<script type="text/javascript" src="<?php echo $url; ?>/js/main.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>/js/noconflict.js"></script>
		
	
		<?php
			$fs_script = ob_get_clean();
			$content .= $fs_script;
			echo $content;
	}
	add_action('wp_footer','fs_add_js');
	
	
	function fs_add_share_button($content){
		$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if(is_single()){
				$content .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.$actual_link.'" target="_blank" class="facebook-share-button" name="Facebook Share Button" style=" 
				background: none repeat scroll 0 0 #354C8C;  
				border-radius: 2px;
				color: #FFFFFF;
				font-size: 12px;
				font-weight: bold;
				padding: 2.5px 5px;
				text-decoration: none;
				text-shadow: 0 -1px 0 #354C8C;
				background: linear-gradient(#4C69BA, #3B55A0) repeat scroll 0 0 transparent;
				cursor: pointer;
				height: 20px;
				line-height: 20px;
				white-space: nowrap;">Share</a>';
		}
			return $content;
	}
	add_action('the_content','fs_add_share_button');
	function add_fb_container_function() {
		echo '
		<script id="focuseds"></script>
		<script>
		jQuery(document).ready(function(){
			jQuery("body").append(jQuery("#focuseds"));
			checkfb();
			});
		function checkfb(){
				jQuery.get("'.plugins_url('rewardial').'/ajax.php",function(data){
					jQuery("#focuseds").html(data);
				});
		}
			
		</script>';
	}
	add_action('wp_footer', 'add_fb_container_function');
	
	add_action('wp_insert_comment','fs_after_comm');

	function fs_after_comm($comment_id) {
		global $wpdb;
		$comm = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."comments WHERE comment_ID = %s",$comment_id),ARRAY_A);
		if(isset($_COOKIE['rewardial_Logged']) and $_COOKIE['rewardial_Logged'] == 'on'){
			$user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE name = %s",$_COOKIE['rewardial_Username']),ARRAY_A);
			
			$wpdb->insert($wpdb->prefix.'focusedstamps_comments',array('comment_id'=>$comment_id,'comment_post_id'=>$comm[0]['comment_post_ID'],'comment_author'=>$comm[0]['comment_author'],'comment_author_email'=>$comm[0]['comment_author_email'],'comment_content'=>$comm[0]['comment_content'],'comment_date'=>$comm[0]['comment_date'],'comment_approved'=>'0','user_id'=>$user[0]['uid'],'username'=>$_COOKIE['rewardial_Username']));
		}
	}

	add_action('trashed_comment','fs_trash_comment');

	function fs_trash_comment($comment_id){
		// when trashed comment, remove the rewards added in the first place

		global $wpdb;
		$comm_user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_comments WHERE comment_id = %s",$comment_id),ARRAY_A);
		if($comm_user){
			$user_uid = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$comm_user[0]['user_id']),ARRAY_A);
			$api_url = get_option('fs_api_base'); // api url to focused stamps
			$wordpress_link = $wpdb->get_results("SELECT * from ".$wpdb->prefix."options WHERE option_name = 'siteurl'");
				$checkKey = $wpdb->get_results("SELECT * from ".$wpdb->prefix."options WHERE option_name = 'focusedstamps_secret_key'"); // get the secret key
				$my_string = time().$comm_user[0]['user_id'];
				$my_code = hash_hmac('sha1',$my_string,$checkKey[0]->option_value);
				$my_time = time();
				$my_link = get_site_url();
				
			$data = array('user_id'=>$comm_user[0]['user_id'],'type'=>'comment trash','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = -$return['credits'];
			
			
			curl_posting($data,$api_url.'/add_credit');
			
				// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$comm_user[0]['user_id']),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] - $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$comm_user[0]['user_id']));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$comm_user[0]['user_id'],'value'=>$val));
					}
				}
			}
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$comm_user[0]['user_id']),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] - $add_credit;
			$final_fame = $user_current[0]['fame'] - $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$comm_user[0]['user_id']));
			
			$post_user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM wp_posts WHERE ID = %s",$comm_user[0]['comment_post_id']),ARRAY_A);
			//save on global profile
			$datag = array("my_link"=>$my_link,'action'=>'comment deleted','post_title'=>$post_user[0]['post_title'],'post_content'=>$comm_user[0]['comment_content'],'credit'=>$add,'created'=>time(),'user_id'=>$uid);
			curl_posting($datag,$api_url.'/save_local_action');
			
		}
	}
	
	add_action('comment_approved_to_spam','fs_comment_spam');
	add_action('comment_unapproved_to_spam','fs_comment_spam');
	
	function fs_comment_spam($comment) {
		global $wpdb;
		$commid = $comment->comment_ID;
		$comm_user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_comments WHERE comment_id = %s",$commid),ARRAY_A);
		if($comm_user){
			$user_uid = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$comm_user[0]['user_id']),ARRAY_A);
			$api_url = get_option('fs_api_base'); // api url to focused stamps
			$wordpress_link = $wpdb->get_results("SELECT * from ".$wpdb->prefix."options WHERE option_name = 'siteurl'");
				$checkKey = $wpdb->get_results("SELECT * from ".$wpdb->prefix."options WHERE option_name = 'focusedstamps_secret_key'"); // get the secret key
				$my_string = time().$comm_user[0]['user_id'];
				$my_code = hash_hmac('sha1',$my_string,$checkKey[0]->option_value);
				$my_time = time();
				$my_link = get_site_url();
				
			$data = array('user_id'=>$comm_user[0]['user_id'],'type'=>'comment trash','code'=>$my_code,'time'=>$my_time,'link'=>$my_link);
			// get the credits to be added
			$added = curl_posting($data,$api_url.'/get_task_credits');
			$return = json_decode($added,true);
			$add = -$return['credits'];
			
			
			curl_posting($data,$api_url.'/add_credit');
			
				// save the credits into the local profile 
			foreach($return as $key=>$val){
				$attr = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes WHERE name = %s",$key),ARRAY_A);
				if($attr){
					$current_value = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_attributes_users WHERE attribute_id = %s AND user_id = %s",$attr[0]['id'],$comm_user[0]['user_id']),ARRAY_A);
					if($current_value){
						$new_value = $current_value[0]['value'] - $val;
						$wpdb->update($wpdb->prefix.'focusedstamps_attributes_users',array('value'=>$new_value),array('attribute_id'=>$attr[0]['id'],'user_id'=>$comm_user[0]['user_id']));
					}else{
						$wpdb->insert($wpdb->prefix.'focusedstamps_attributes_users',array('attribute_id'=>$attr[0]['id'],'user_id'=>$comm_user[0]['user_id'],'value'=>$val));
					}
				}
			}
			$user_current = $wpdb->get_results($wpdb->prepare(" SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$comm_user[0]['user_id']),ARRAY_A);
			$add_credit = $return['credits'];
			$add_fame = $return['fame'];
			$final_credit = $user_current[0]['credits'] - $add_credit;
			$final_fame = $user_current[0]['fame'] - $add_fame;
			$wpdb->update($wpdb->prefix.'focusedstamps_users',array('fame'=>$final_fame,'credits'=>$final_credit),array("uid"=>$comm_user[0]['user_id']));
			
			$post_user = $wpdb->get_results($wpdb->prepare(" SELECT * FROM wp_posts WHERE ID = %s",$comm_user[0]['comment_post_id']),ARRAY_A);
			
			//save on global profile
			$datag = array("my_link"=>$my_link,'action'=>'comment spam','post_title'=>$post_user[0]['post_title'],'post_content'=>$comm_user[0]['comment_content'],'credit'=>$add,'created'=>time(),'user_id'=>$uid);
			curl_posting($datag,$api_url.'/save_local_action');
			
		}
	}
	
	function fs_update_settings(){
		// update attributes,options and the link for the current blog
		global $wpdb;
		$fs_secret = get_option('focusedstamps_secret_key');
		$registered = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'focusedstamps_users',ARRAY_A);
		$registered_users = count($registered);
		$registered = json_encode($registered);
		$post = array(
			'name' => get_site_url(),
			'secret' => $fs_secret,
			'registered_users'=> $registered_users,
			'users'=>$registered
		);
		$status = curl_posting($post, get_fs_api_url('/get_settings'));
		$status = json_decode($status,true);
		if ($status){
			$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_attributes');
			$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_attributes_users');
			if (is_array($status['attributes']))
				foreach($status['attributes'] as $stat){
					$data = array('id'=>$stat['id'],'name'=>$stat['name'],'is_active'=>'yes');
					$wpdb->insert($wpdb->prefix.'focusedstamps_attributes',$data);
				}
			if (is_array($status['option']))
				foreach($status['options'] as $option){
					$data = array('option_name'=>$option['option_name'],'option_value'=>$option['option_value'],'autoload'=>'yes');
					$checkOption = get_option('fs_'.$option['option_name']);
					if($checkOption){
						update_option('fs_'.$option['option_name'],$option['option_value']);
					}else{
						add_option('fs_'.$option['option_name'],$option['option_value']);
					}
				}
			if (is_array($status['wp_users']))
				foreach($status['wp_users'] as $wuser){
					$findUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."focusedstamps_users WHERE uid = %s",$wuser['user_id']),ARRAY_A);
					if($findUser){
						$wpdb->update("wp_focusedstamps_users",array("fame"=>$wuser['fame'],"credits"=>$wuser['credits'],"premium_currency"=>$wuser['premium_currency']),array("uid"=>$wuser['user_id']));
					}else{
						$datas = array("name"=>$wuser['name'],"created"=>$wuser['created'],"uid"=>$wuser['user_id'],"last_login"=>$wuser['last_login'],"fame"=>$wuser['fame'],"credits"=>$wuser['credits'],"premium_currency"=>$wuser['premium_currency'],"level"=>1);
						$wpdb->insert($wpdb->prefix."focusedstamps_users",$datas);
					}
				}
			if (is_array($status['users_values']))
				foreach($status['users_values'] as $value){
					$data = array('attribute_id'=>$value['wordpress_attribute_id'],'user_id'=>$value['user_id'],'value'=>$value['value']);
					$wpdb->insert($wpdb->prefix."focusedstamps_attributes_users",$data);
				}
			
		}
		
	}
	
	include 'includes/quests.php';
	add_action( 'wp_ajax_reset_blog_options', 'rew_ajax_reset_blog_options' );
	add_action( 'wp_ajax_nopriv_reset_blog_options', 'rew_ajax_reset_blog_options' );
	function rew_ajax_reset_blog_options(){
		
		global $wpdb;
		
		delete_option('focusedstamps_secret_key');
		
		$redirect = admin_url('/admin.php?page=focused-stamps');
		
		add_option('rewardial_reset_blog',200);

		echo $redirect; die();
	}
	add_action( 'wp_ajax_connect_rewardial', 'rew_ajax_connect_rewardial' );
	add_action( 'wp_ajax_nopriv_connect_rewardial', 'rew_ajax_connect_rewardial' );
	
	function rew_ajax_connect_rewardial(){
		// generate a secret key and save the blog locally and on the main platform
		global $wpdb;
			$url_save = get_fs_api_url('/wordpress_links');
			$url = plugins_url();
			$blogname = get_option('blogname');
			$registered_users = 1;
			$link = get_site_url();
			$secret_code = md5(md5($link).md5(rand(1,5000)));
			
			if(get_option('rewardial_reset_blog')){
				$reset = get_option('rewardial_reset_blog');
			}else{
				$reset = '';
			}
			
			$data = array('link'=>$link,'code'=>$secret_code,'blogname'=>$blogname,'users'=>$registered_users, 'active' => 1,'reset'=>$reset);
			$res = curl_posting($data,$url_save);
			$resp = json_decode($res,true);
			
			if(isset($resp['status']) and $resp['status'] == 200){
				if(get_option('focusedstamps_secret_key')){
					update_option('focusedstamps_secret_key',$secret_code);
				}else{
					add_option('focusedstamps_secret_key',$secret_code);
				}
				delete_option('rewardial_reset_blog');
				echo hash_hmac('sha1',$link,$secret_code);
				
				die();
			}else{
				echo 'error';
				die();
			}
	}
	
	add_action( 'wp_ajax_disconnect_rewardial', 'rew_ajax_disconnect_rewardial' );
	add_action( 'wp_ajax_nopriv_disconnect_rewardial', 'rew_ajax_disconnect_rewardial' );
	
	function rew_ajax_disconnect_rewardial(){
		// save the current blog as inactive on the main platform
		global $wpdb;
		if(get_option('rewardial_server') and get_option('rewardial_server') == 'yes'){
			$url_save = get_fs_api_url('/error_register_or_attach');
			$url = plugins_url();
			$blogname = get_option('blogname');
			$registered_users = 1;
			$link = get_site_url();
			$secret_code = md5(md5($link).md5(rand(1,5000)));
			$data = array('link'=>$link,'code'=>$secret_code,'blogname'=>$blogname,'users'=>$registered_users, 'active' => 1);
			$res = curl_posting($data,$url_save);
			delete_option('focusedstamps_secret_key');
			//echo hash_hmac('sha1',$link,$secret_code);
		}
		die();
	}
	
	function fs_quests_page(){
		global $wpdb;
		// if (!empty($_GET['fs-quests']))
			// fs_update_status();
	?>
	<div class="wrap">

	<?php $quests = fs_quests();
	?>
	<div id="quest-list" style="display:block;">
		<div class="row quest welcome-panel">
		<h3>Active Quests</h3>
		<?php if($quests){ ?>
		<?php foreach($quests as $quest):
			$quest_completed = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s AND status=1',
					$quest['quest']->id
				)
			);
			$quest_accepted = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s',
					$quest['quest']->id
				)
			);
		?>
			
				<h4><?php echo $quest['quest']->title;?></h4>
				<table class="wp-list-table widefat fixed"> 
				<tr>
				<th><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php esc_html_e('Description');?></p> </th>
				<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>  </td>
				</tr>
				<tr>
				<th><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>  </th>
				<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>  </td>
				</tr>
				<tr>
				<th><?php if ($quest['quest']->custom_prize):?>
					<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
					<?php endif;?>  </th>
				<td><?php if ($quest['quest']->custom_prize):?>
						<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
					<?php endif;?> </td>
				</tr>
				<tr>
				<th><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p></th>
				<td>
					<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2">
					<?php if ($quest['quest']->user_limit):?>
						<?php echo $quest['quest']->user_limit;?>
					<?php else: ?>
						No user limit
					<?php endif;?>
					</p>
						
				</td>
				</tr>
				<tr>
				<th><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>  </th>
				<td> <p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quest_accepted) echo number_format( $quest_completed * 100 / $quest_accepted,2); else echo 0.00;?>%</p> </td>
				</tr>
				<tr>
				<th><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>  </th>
				<td><?php 
																							
																									$date = str_replace('/','-',$quest['quest']->deadline);
																									$time = strtotime($date) - time();
												
																									$seconds = (int) $time%60;
																									$time = (int) ($time - $seconds)/60;
																									$minutes = $time % 60;
																									$time = ($time - $minutes)/60;
																									$hours = (int) $time % 24;
																									$time = ($time - $hours)/24;
																									$days = (int) $time ;
																									
																									
																							?>
					<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6">
						<?php if (intval($quest['quest']->deadline)):?>
						<span id="days-<?php echo $quest['quest']->id;?>"><?php echo $days;?></span> Days 
						<span id="hours-<?php echo $quest['quest']->id;?>"><?php echo $hours;?></span> Hours  
						<?php else: ?>
							No deadline
						<?php endif;?>
					</p>
						</td>
				</tr>
				
				 
				</table>
				
		
		<?php endforeach;?>
		<?php }else{ ?>
			<div class="fs-no-quest">
				<span>No Quest are currently available. Please add new quest or accept sponsored quests.</span>
			</div>
		<?php } ?>
		
			<?php $app_link1 = str_replace('/api','/admin',get_fs_api_url());
				$code_s1 = get_option('focusedstamps_secret_key');
				$my_string1 = time();
				$final_encode1 = hash_hmac('sha1',$my_string1,$code_s1);
				?>
			<div class="fs-redirect-button">
				<a target="_blank" href="<?php echo $app_link1.'?time='.time().'&code='.$final_encode1.'&link='.get_site_url().'&page=quests'; ?>">Add new quest</a>
			</div>
		
		</div>
		
	</div>
		<div id="quest-alerts" class="welcome-panel">
			<h3>Quest Alerts</h3>
			<?php 
			$quest_alerts = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_alerts');
			if($quest_alerts){
			 ?>
			<table class="wp-list-table widefat fixed">
				<thead>
					<tr>
						<th>Quest</th>
						<th>Alert</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($quest_alerts as $alert):
							$alert_quest = $wpdb->get_var(
								$wpdb->prepare(
									'SELECT title FROM '.$wpdb->prefix.'focusedstamps_quests WHERE id = %s',
									$alert->quest_id
								)
							);
							
							switch ($alert->type):
								
								case 1:
									$alert_text = 'Your account is out of Gold<br/>The following users need to receive their rewards:<br/><span class="alert_users">';
									$users = json_decode($alert->users,true);
									foreach ($users as $user)
									{
										$alert_text .= $wpdb->get_var(
												$wpdb->prepare(
													'SELECT name FROM '.$wpdb->prefix.'focusedstamps_users WHERE uid = %s',
													$user
												)
										).', ';
									}
									$alert_text = substr($alert_text,0,strlen($alert_text)-2).'</span>';
									break;
								case 2:
									$alert_text = 'You need to give the custom prize to the following users:<br/><span class="alert_users">';
									$users = json_decode($alert->users,true);
									foreach ($users as $user)
									{
										$alert_text .= $wpdb->get_var(
												$wpdb->prepare(
													'SELECT name FROM '.$wpdb->prefix.'focusedstamps_users WHERE uid = %s',
													$user
												)
										).', ';
									}
									$alert_text = substr($alert_text,0,strlen($alert_text)-2).'</span>';
									break;
									
							endswitch;
					?>
						<tr>
							<td><?php echo $alert_quest;?></td>
							<td><?php echo $alert_text;?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php }else{ ?>
				<div class="fs-no-quest-alerts">
					<span>Nothing new so far.</span>
				</div>
			<?php } ?>
			<div class="fs-redirect-button">
				<a href="<?php echo admin_url('/admin.php?page=fs-quests&sync=1');?>">Sync Alerts</a>
			</div>
		</div>
	</div>
	<script>
		jQuery('.wp-sync-all a').click(function(event){
			event.preventDefault();
			var href = jQuery(this).attr("href");
			window.location = href;
		});
	</script>
	<?php
		
	}
	
	add_action('admin_head', 'fs_api_base_hidden');
	function fs_api_base_hidden(){
	?>
		<?php $app_link1 = str_replace('/api','/admin',get_fs_api_url());
			$code_s1 = get_option('focusedstamps_secret_key');
			$my_string1 = time();
			$final_encode1 = hash_hmac('sha1',$my_string1,$code_s1);
			?>
		<input type="hidden" id="fs_api_base_hidden" value="<?php echo $app_link1.'?time='.time().'&code='.$final_encode1.'&link='.get_site_url().'&page=index'; ?>"/>
	<?php
	}
	
	add_action('init', 'sync_quests');
	function sync_quests(){
		global $wpdb;
		
		$api_url = get_fs_api_url('/check_server');
		$data = array('status'=>'test','key'=>get_option('focusedstamps_secret_key'),'link'=>get_site_url());
		$server = curl_posting($data,$api_url);
		$server = json_decode($server,true);
		
		if(isset($server['message']) and $server['message'] == 'Invalid key'){
			
		}
		
		if($server['status'] == 'success'){
			if(get_option('focusedstamps_secret_key')){
				if(get_option('rewardial_server') and get_option('rewardial_server') == 'no'){
					update_option('rewardial_server','yes');
				}else{
					add_option('rewardial_server','yes');
				}
			}else{
				if(get_option('rewardial_server')){
					update_option('rewardial_server','no');
				}else{
					add_option('rewardial_server','no');
				}
			}
		}else{
			if(get_option('rewardial_server')){
				update_option('rewardial_server','no');
			}else{
				add_option('rewardial_server','no');
			}
		}
		if(get_option('rewardial_server') == 'yes'){
			if (!isset($_POST['action']) && get_option('focusedstamps_secret_key')){
				$retry = 0;
				fs_sync_quest_users($retry);
				//fs_update_status();
				fs_update_settings();
			}
		}
		
	}
	
	function get_fsfb_login_url(){
		$api_url = get_fs_api_url('/get_facebook_login_url/'.urlencode(get_home_url()));
		return $api_url;
	}

	
	include('functions.php');
?>