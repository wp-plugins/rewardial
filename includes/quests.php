<?php

// add_action ('init','fs_cron_status');
// function fs_cron_status(){ 
	// $fs_options = get_option('fs_options');
	// if ((time() - $fs_options['last_update']) > $fs_options['update_interval'])
	// {
		// fs_update_status();
		// fs_update_settings();
		// $fs_options['last_update'] = time();
		// update_option('fs_options',$fs_options);
	// }
// }

function fs_update_status(){

	global $wpdb;
	$fs_secret = get_option('focusedstamps_secret_key');
	$post = array(
		'name' => get_site_url(),
		'secret' => $fs_secret
	);
	$status = curl_posting($post, get_fs_api_url('/status'));
	$status = json_decode($status,true);
	if ($status):
		//Quests - first delete all quests information in order to keep the database updated
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_quests');
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_quest_steps');
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_quest_questions');
		$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'focusedstamps_quest_alerts');
		if (is_array($status['quests'])){
			foreach($status['quests'] as $quest):
			
				//INSERT quest
				$wpdb->insert(
					$wpdb->prefix.'focusedstamps_quests',
					$quest['quest']
				);
				//INSERT quest steps
				foreach($quest['steps'] as $step):
				
					$wpdb->insert(
						$wpdb->prefix.'focusedstamps_quest_steps',
						$step['step']
					);
					
					//INSERT step questions
					foreach($step['questions'] as $question):
					
						$wpdb->insert(
							$wpdb->prefix.'focusedstamps_quest_questions',
							$question
						);
					
					endforeach;
				
				endforeach;
				
				//Save Quest alerts
				foreach ($quest['quest_alerts'] as $alert){
					$wpdb->insert(
						$wpdb->prefix.'focusedstamps_quest_alerts',
						$alert['Quest_alert']
					);
				}
				
			endforeach;
		}
	endif;
	
}

function fs_quests(){

	global $wpdb;
	$ret = false;
	$quests = $wpdb->get_results(
		'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quests ORDER BY id'
	);
	if ($quests):
		$ret = array();
		$i = 0;
		foreach($quests as $key => $quest):
				$ret[$i] = array('quest' => $quest,'steps' => array());
				$sql = $wpdb->prepare(
					'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE quest_id = %s',
					$quest->id
				);
				$steps = $wpdb->get_results($sql);
				$j = 0;
				foreach($steps as $key => $step):
				
					$ret[$i]['steps'][$j] = array('step' => $step,'questions' => array());
					$sql = $wpdb->prepare(
						'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE step_id = %s',
						$step->id
					);
					$questions = $wpdb->get_results($sql);
					foreach($questions as $key => $question):
					
						$ret[$i]['steps'][$j]['questions'][] = $question;
					
					endforeach;
					$j++;
				
				endforeach;
				$i++;
			
		endforeach;
	endif;
	if($ret){
		$ret = array_reverse($ret);
	}
	return $ret;
}
function rwd_get_user_id($uid){
	
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
function quest_area($quests){
	global $wpdb;
	$uid = rwd_get_user_id($_COOKIE['rewardial_Uid']);
?>
	<div id="fs-overlay" class="fs-inactive class-for-escape">
			
		<div id="fs-quests">
		<div class="fs-headerwrap">
			<div class="fs-logo responsive-logo">
				<a href="http://www.rewardial.com/" target="_blank">
				<img class="rewardial-logo" src="<?php echo plugins_url('rewardial'); ?>/img/logo-new.png" title="Rewardial"/>
				</a>
			</div><!-- .fs-logo -->
			
		 <div class="fs-account-links-quest">
			<div class="responsive-my-profile">
					<div class="fs-profile-link"><a href="javascript:void(0);">Profile</a></div>
			 </div>
			<div class="fs-account-credits responsive-credits" title="You have <?php echo $_COOKIE['rewardial_Credits']; ?> credits. You can use these to purchase envelopes from the shop">
				<span class="fs-details">Credits</span><br />
				<div class="fs-credits-value"><?php echo $_COOKIE['rewardial_Credits'];?></div>
			</div><!-- .fs-account-credits -->

			<div class="fs-account-premium " title="You have <?php echo $_COOKIE['rewardial_Premium_Currency'];?> gold. You can use these to purchase various special items on www.rewardial.com.">
				<span class="fs-details">Gold</span><br />
				<div class="fs-premium-value"><?php echo $_COOKIE['rewardial_Premium_Currency'];?></div>
			</div><!-- .fs-account-premium -->
		 
			
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
				<div class="clear"></div>
			</div>	
				<div class="fs-account-menu responsive-account-menu">
						<ul>
							<li class="fs-shop" id="fs-shop"><a href="javascript:void(0);">SHOP </a> </li>
							<li class="fs-community"><a href="javascript:void(0);"> <?php esc_html_e('COMMUNITY');?></a> </li>
							<li class="fs-quest <?php echo 'active';?>"><a href="javascript:void(0);"> QUEST</a> </li>
						</ul>
					</div>
				
			<a href="javascript:void(0);" class="close-quest"><Span>Close</span> X</a>
				<div class="clear"></div>
				</div>
			<div id="question-place">
					
				
			</div>
			<h2 id="quest-list-title"><?php esc_html_e('Available Quests');?></h2>
			<div class="fs-quests-tabs-container">
				<div class="fs-quests-tab fs-quests-tab-new q-active">New</div>
				<div class="fs-quests-tab fs-quests-tab-accepted">Accepted</div>
				<div class="fs-quests-tab fs-quests-tab-finished">Finished</div>
			</div>
			<img src="<?php echo plugins_url('rewardial');?>/img/ajax-loader-1.gif" id="ajax-loader"/>
			<div id="quest-list">
			<script>
				function countdown(date, quest_id) {
					var now = new Date();
					now.setHours(now.getHours() + (offset-5));
					var seconds = Math.floor((date.getTime() - now.getTime()) * 0.001);
					var sec = Math.floor(seconds % 60);
					seconds = (seconds - sec) / 60;
					var minutes = Math.floor(seconds%60);
					seconds = (seconds - minutes) / 60;
					var hours = Math.floor(seconds  % 60);
					seconds = (seconds - hours) / 24;
					var days = Math.floor(seconds % 24);
					
					jQuery('#days-'+quest_id).html(days);
					jQuery('#hours-'+quest_id).html(hours);
					jQuery('#minutes-'+quest_id).html(minutes);
					jQuery('#seconds-'+quest_id).html(sec);
					
					setInterval(function () { countdown(date, quest_id); }, 1000);
				}

				function get_time_zone_offset() {
					 var current_date = new Date( );
					 var gmt_offset = current_date.getTimezoneOffset( ) / 60;
					 return gmt_offset;
				}
				var offset = get_time_zone_offset();
			</script>
			<?php 
				$new_quests = array();
				$completed_quests = array();
				$finished_quests = array();
				$accepted_quests = array();
			?>
			<?php foreach($quests as $quest):
				
				$sql = $wpdb->prepare(
					'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE status = 1 AND quest_id = %s',
					$quest['quest']->id
				);
				$quests_completed = $wpdb->get_var($sql);
				
				$sql = $wpdb->prepare(
					'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s',
					$quest['quest']->id
				);
				$quests_accepted = $wpdb->get_var($sql);
				
				
				
				$sql = $wpdb->prepare(
					'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s LIMIT 1',
					$uid,$quest['quest']->id
				);
				$user_status = $wpdb->get_row($sql);
				
				if(!$user_status){
					$new_quests[] = $quest;
				}elseif($user_status->status == 1){
					$completed_quests[] = $quest;
				}elseif($user_status->status == 2){
					$finished_quests[] = $quest;
				}else{
					$accepted_quests[] = $quest;
				}
				endforeach;

				$quests_ended = array_merge($completed_quests,$finished_quests);
				
				
			?>
				
			<div class="fs-new-quests-tab fs-quest-q">
				<?php if($new_quests){ ?>
				<?php foreach($new_quests as $quest):
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE status = 1 AND quest_id = %s',
							$quest['quest']->id
						);
						$quests_completed = $wpdb->get_var($sql);
						
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s',
							$quest['quest']->id
						);
						$quests_accepted = $wpdb->get_var($sql);
						
					
						
						$sql = $wpdb->prepare(
							'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s LIMIT 1',
							$uid,$quest['quest']->id
						);
						$user_status = $wpdb->get_row($sql);
				?>
					<div class="row quest">
					<div class="quest-title">
						<h3> <?php echo $quest['quest']->title;?> </h3>

						<?php if (!$user_status):?>
								<a class="quest-action quest-accept" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Accept Quest');?></a>
							<?php elseif($user_status->status == 1): ?>
															<span class="quest-completed">Quest Completed</span>
							<?php elseif($user_status->status == 2): ?>
															<span class="quest-finished">Quest Failed</span>
															<span class="quest-retry quest-action" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>">Retry Quest</span>
													<?php else: ?>
								<a class="quest-action quest-continue" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Continue Quest');?></a>
						<?php endif;?>
							<div class="clear"></div>
					</div>	

						<table class="fs-desktopquest"> 
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description'; ?></p> </td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>  </td>
						</tr>
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>  </td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>  </td>
						</tr>
						<tr>
						<td><?php if ($quest['quest']->custom_prize):?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
							<?php endif;?>  </td>
						<td><?php if ($quest['quest']->custom_prize):?>
								<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
							<?php endif;?> </td>
						</tr>
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p></td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p> </td>
						</tr>
						<!--<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>  </td>
						<td> <p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p> </td>
						</tr>-->
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>  </td>
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
														
														if($seconds < 0 ){
															$seconds = 0;
															$minutes = 0;
															$hours = 0;
															$days = 0;
														}
														
														
													?>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6">
								<?php if($seconds > 0){ ?>
								<span id="days-<?php echo $quest['quest']->id;?>">
									<?php echo $days;?>
								</span> Days 
								<span id="hours-<?php echo $quest['quest']->id;?>">
									<?php echo $hours;?>
								</span> Hours  
								<?php }else { ?>
									<span id="no-time-limit">No time limit</span>
								<?php } ?>
							</p>
							  </td>
						</tr>
						
						 
						</table>
						<script> 
								<?php 
								
	//								$date = str_replace('/','-',$quest['quest']->deadline);
	//								echo 'date = new Date("'.date('c',strtotime($date)).'");'."\n";
	//								echo 'countdown(date,'.$quest['quest']->id.');'."\n";
								
								?>
						</script> 
						<div class="fs-mobilequest"> 
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description';?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>
							<!--<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p>-->
							  
							<?php if ($quest['quest']->custom_prize):?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
							<?php endif;?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><span id="days-<?php echo $quest['quest']->id;?>"><?php echo $days;?></span> Days <span id="hours-<?php echo $quest['quest']->id;?>"><?php echo $hours;?></span> Hours  </p>
						 
						</div>
						
					</div>
				
				<?php endforeach;?>
				<?php }else{ ?>
					<div class="fs-no-quests-available">
						There are no Quests here.
					</div>
				<?php } ?>
			</div>
			<div class="fs-accepted-quests-tab invisible fs-quest-q">
				<?php if($accepted_quests){ ?>
				<?php foreach($accepted_quests as $quest):
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE status = 1 AND quest_id = %s',
							$quest['quest']->id
						);
						$quests_completed = $wpdb->get_var($sql);
						
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s',
							$quest['quest']->id
						);
						$quests_accepted = $wpdb->get_var($sql);
						
						
						
						$sql = $wpdb->prepare(
							'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s LIMIT 1',
							$uid,$quest['quest']->id
						);
						$user_status = $wpdb->get_row($sql);
				?>
					<div class="row quest">
					<div class="quest-title">
						<h3> <?php echo $quest['quest']->title;?> </h3>

						<?php if (!$user_status):?>
								<a class="quest-action quest-accept" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Accept Quest');?></a>
							<?php elseif($user_status->status == 1): ?>
															<span class="quest-completed">Quest Completed</span>
							<?php elseif($user_status->status == 2): ?>
															<span class="quest-finished">Quest Failed</span>
															<span class="quest-retry quest-action" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>">Retry Quest</span>
													<?php else: ?>
								<a class="quest-action quest-continue" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Continue Quest');?></a>
						<?php endif;?>
							<div class="clear"></div>
					</div>	

						<table class="fs-desktopquest"> 
							<tr>
								<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description';?></p> </td>
								<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>  </td>
							</tr>
							<tr>
								<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>  </td>
								<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>  </td>
							</tr>
							<tr>
								<td><?php if ($quest['quest']->custom_prize):?>
									<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
									<?php endif;?>  </td>
								<td><?php if ($quest['quest']->custom_prize):?>
										<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
									<?php endif;?> </td>
							</tr>
							<tr>
								<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p></td>
								<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p> </td>
							</tr>
							<!--<tr>
							<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>  </td>
							<td> <p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p> </td>
							</tr>-->
							<tr>
								<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>  </td>
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
															
															if($seconds < 0 ){
																$seconds = 0;
																$minutes = 0;
																$hours = 0;
																$days = 0;
															}
															
															
														?>
								<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6">
									<?php if($seconds > 0){ ?>
									<span id="days-<?php echo $quest['quest']->id;?>">
										<?php echo $days;?>
									</span> Days 
									<span id="hours-<?php echo $quest['quest']->id;?>">
										<?php echo $hours;?>
									</span> Hours  
									<?php }else { ?>
										<span id="no-time-limit">No time limit</span>
									<?php } ?>
								</p>
								  </td>
							</tr>
						
						 
						</table>
						<script> 
								<?php 
								
	//								$date = str_replace('/','-',$quest['quest']->deadline);
	//								echo 'date = new Date("'.date('c',strtotime($date)).'");'."\n";
	//								echo 'countdown(date,'.$quest['quest']->id.');'."\n";
								
								?>
						</script> 
						<div class="fs-mobilequest"> 
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description';?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>
							<!--<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p>-->
							  
							<?php if ($quest['quest']->custom_prize):?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
							<?php endif;?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><span id="days-<?php echo $quest['quest']->id;?>"><?php echo $days;?></span> Days <span id="hours-<?php echo $quest['quest']->id;?>"><?php echo $hours;?></span> Hours  </p>
						 
						</div>
						
					</div>
				
				<?php endforeach;?>
				<?php }else{ ?>
					<div class="fs-no-quests-available">
						There are no Quests here.
					</div>
				<?php } ?>
			</div>
			<div class="fs-ended-quests-tab invisible fs-quest-q">
				<?php if($quests_ended){ ?>
				<?php foreach($quests_ended as $quest):
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE status = 1 AND quest_id = %s',
							$quest['quest']->id
						);
						$quests_completed = $wpdb->get_var($sql);
						
						$sql = $wpdb->prepare(
							'SELECT COUNT(*) FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s',
							$quest['quest']->id
						);
						$quests_accepted = $wpdb->get_var($sql);
						
						
						
						$sql = $wpdb->prepare(
							'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s LIMIT 1',
							$uid,$quest['quest']->id
						);
						$user_status = $wpdb->get_row($sql);
				?>
					<div class="row quest">
					<div class="quest-title">
						<h3> <?php echo $quest['quest']->title;?> </h3>

						<?php if (!$user_status):?>
								<a class="quest-action quest-accept" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Accept Quest');?></a>
							<?php elseif($user_status->status == 1): ?>
															<span class="quest-completed">Quest Completed</span>
							<?php elseif($user_status->status == 2): ?>
															<span class="quest-finished">Quest Failed</span>
															<span class="quest-retry quest-action" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>">Retry Quest</span>
													<?php else: ?>
								<a class="quest-action quest-continue" data-step="<?php echo $user_status->step_id;?>" data-question="<?php echo $user_status->question_id;?>" data-i="<?php echo $quest['quest']->id;?>"><?php esc_html_e('Continue Quest');?></a>
						<?php endif;?>
							<div class="clear"></div>
					</div>	

						<table class="fs-desktopquest"> 
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description';?></p> </td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>  </td>
						</tr>
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>  </td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>  </td>
						</tr>
						<tr>
						<td><?php if ($quest['quest']->custom_prize):?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
							<?php endif;?>  </td>
						<td><?php if ($quest['quest']->custom_prize):?>
								<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
							<?php endif;?> </td>
						</tr>
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p></td>
						<td><p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p> </td>
						</tr>
						<!--<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>  </td>
						<td> <p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p> </td>
						</tr>-->
						<tr>
						<td><p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>  </td>
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
														
														if($seconds < 0 ){
															$seconds = 0;
															$minutes = 0;
															$hours = 0;
															$days = 0;
														}
														
														
													?>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6">
								<?php if($seconds > 0){ ?>
								<span id="days-<?php echo $quest['quest']->id;?>">
									<?php echo $days;?>
								</span> Days 
								<span id="hours-<?php echo $quest['quest']->id;?>">
									<?php echo $hours;?>
								</span> Hours  
								<?php }else { ?>
									<span id="no-time-limit">No time limit</span>
								<?php } ?>
							</p>
							  </td>
						</tr>
						
						 
						</table>
						<script> 
								<?php 
								
	//								$date = str_replace('/','-',$quest['quest']->deadline);
	//								echo 'date = new Date("'.date('c',strtotime($date)).'");'."\n";
	//								echo 'countdown(date,'.$quest['quest']->id.');'."\n";
								
								?>
						</script> 
						<div class="fs-mobilequest"> 
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo 'Description';?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="1"><?php echo $quest['quest']->description;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php esc_html_e('User Limit');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="2"><?php echo $quest['quest']->user_limit;?></p>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php esc_html_e('No. of steps');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="3"><?php echo count($quest['steps']);?></p>
							<!--<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php esc_html_e('Completion Rate');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="4"><?php if($quests_accepted) echo number_format( $quests_completed * 100 / $quests_accepted,2); else echo 0.00;?>%</p>-->
							  
							<?php if ($quest['quest']->custom_prize):?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php esc_html_e('Custom Prize');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="5"><?php echo $quest['quest']->custom_prize;?></p>
							<?php endif;?>
							<p class="quest-label" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><?php esc_html_e('Time Left');?></p>
							<p class="quest-content" data-quest="<?php echo $quest['quest']->id;?>" data-type="6"><span id="days-<?php echo $quest['quest']->id;?>"><?php echo $days;?></span> Days <span id="hours-<?php echo $quest['quest']->id;?>"><?php echo $hours;?></span> Hours  </p>
						 
						</div>
						
					</div>
				
				<?php endforeach;?>
				<?php }else{ ?>
					<div class="fs-no-quests-available">
						There are no Quests here.
					</div>
				<?php } ?>
			</div>
			<script>
				jQuery('.fs-quest a').click(function(){
					setTimeout(function(){
						jQuery('.quest-content').each(function(){
							var type = jQuery(this).attr('data-type');
							var quest = jQuery(this).attr('data-quest');
							var height = jQuery(this).height();
							jQuery('.quest-label[data-type="'+type+'"][data-quest="'+quest+'"]').height(height);
						});
					},1);
				});
			</script>
			</div>
		</div>
	
	</div>

<?php

}

add_action( 'wp_ajax_nopriv_quest_question', 'quest_question_ajax_callback' );
add_action( 'wp_ajax_quest_question', 'quest_question_ajax_callback');
function quest_question_ajax_callback(){
	global $wpdb;
	if(isset($_POST['retry'])){
		$retry = $_POST['retry'];
	}else{
		$retry = 0;
	}
	if ($_POST['question_id'])
	{
		$question_id = intval($_POST['question_id']);
		$sql = $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE id = %s',
			$question_id
		);
		$question = $wpdb->get_row($sql);
		
		$sql = $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE id = %s',
			intval($_POST['step_id'])
		);
		$step = $wpdb->get_row($sql);
	}
	else
	{
		$sql = $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE quest_id = %s ORDER BY id ASC LIMIT 1',
			intval($_POST['quest_id'])
		);
		$step = $wpdb->get_row($sql);
		
		$sql = $wpdb->prepare(
			'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE step_id = %s ORDER BY id ASC LIMIT 1',
			$step->id
		);
		$question = $wpdb->get_row($sql);
	}
	$quest = $wpdb->prepare(
		'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quests WHERE id = %s',
		intval($_POST['quest_id'])
	);
	$quest = $wpdb->get_row($quest);
	
	$quest_steps = $wpdb->prepare(
		'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE quest_id = %s',
		intval($_POST['quest_id'])
	);
	$quest_steps = $wpdb->get_results($quest_steps);

	$answers = array($question->answer,$question->wrong_1,$question->wrong_2,$question->wrong_3);
	shuffle($answers);
	?>
		
		<header class="fs-shop-header-steps">
	
			<div class="fs-text-header">
				<h1 class="fs-section-title-steps"><?php echo $quest->title;?></h1>
				<span class="fs-large"><?php echo $quest->description; ?></span>
			</div>
			<hr class="fs-divisor">
		</header>
		<section class="fs-shop-content">
			<div class="fs-steps-boxes">
				<div class="fs-box1">
					<div class="fs-box1-topo-gray" > </div>
					<div class="fs-box1-topo-yellow"<?php if ($step->step_nr == 1):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box1-topo-green"<?php if ($step->step_nr > 1):?> style="display:block;"<?php endif;?>> </div>
				</div>
				<?php if(count($quest_steps) > 1) { ?>
				<div class="fs-box2">
					<div class="fs-box2-topo-gray"<?php if ($step->step_nr < 2):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box2-topo-yellow"<?php if ($step->step_nr == 2):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box2-topo-green"<?php if ($step->step_nr >2):?> style="display:block;"<?php endif;?>> </div>
					<span style="display: block;"> </span>
				</div>
				<?php } ?>
				<?php if(count($quest_steps) > 2) { ?>
				<div class="fs-box3">
					<div class="fs-box3-topo-gray"<?php if ($step->step_nr < 3):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box3-topo-yellow"<?php if ($step->step_nr == 3):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box3-topo-green"<?php if ($step->step_nr > 3):?> style="display:block;"<?php endif;?>> </div>
				</div>
				<?php } ?>
				<?php if(count($quest_steps) > 3) { ?>
				<div class="fs-box4">
					<div class="fs-box4-topo-gray"<?php if ($step->step_nr < 4):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box4-topo-yellow"<?php if ($step->step_nr == 4):?> style="display:block;"<?php endif;?>> </div>
					<div class="fs-box4-topo-green"> </div>
				</div>
				<?php } ?>
				<hr class="fs-divisor">
			</div>
		</section>
		<div class="row">
			<h2 class="question-text"><?php echo $step->title.'. '.$question->question;?></h2>
			<a class="col-1-2 quest-answer" data-quest="<?php echo $quest->id;?>" data-step="<?php echo $step->id;?>" data-question="<?php echo $question->id;?>"><?php echo $answers[0];?></a>
			<a class="col-1-2 quest-answer" data-quest="<?php echo $quest->id;?>" data-step="<?php echo $step->id;?>" data-question="<?php echo $question->id;?>"><?php echo $answers[1];?></a>
			<a class="col-1-2 quest-answer" data-quest="<?php echo $quest->id;?>" data-step="<?php echo $step->id;?>" data-question="<?php echo $question->id;?>"><?php echo $answers[2];?></a>
			<a class="col-1-2 quest-answer" data-quest="<?php echo $quest->id;?>" data-step="<?php echo $step->id;?>" data-question="<?php echo $question->id;?>"><?php echo $answers[3];?></a>
                        <a id="back-to-quest" href="javascript:void(0);">Back</a>
						<div class="quest-rewards">
							<span>Completion reward : </span><span class="icon-bg-<?php if($step->currency == 'Experience') echo 'Fame'; else echo $step->currency; ?>"><?php if($retry == 1) echo ($step->number - $step->number*(20/100)); else echo $step->number; ?> <?php if($step->currency == 'Experience') echo 'Fame'; else echo $step->currency; ?></span>
						</div>
        </div>
		<img src="<?php echo plugins_url('rewardial');?>/img/ajax-loader-1.gif" id="answer-loader"/>
                
	<?php
	
	die();
}

add_action( 'wp_ajax_nopriv_check_question_answer', 'check_question_answer_callback' );
add_action( 'wp_ajax_check_question_answer', 'check_question_answer_callback');

function check_question_answer_callback(){
	global $wpdb;
	
	$uid = rwd_get_user_id($_COOKIE['rewardial_Uid']);
	$sql = $wpdb->prepare(
		'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE id = %s',
		intval($_POST['question_id'])
	);
	
	$res = $wpdb->get_row($sql);
	$response = array();
	if ($res->answer == $_POST['answer']){
		//The answer is correct
		$response['status'] = 1;
		if($_POST['retry'] == 1){
			$retry = 80/100; // 20% penalty for the quest retry
		}else{
			$retry = 1;
		}
		
		//Check for next question
		$sql = $wpdb->prepare(
						'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE step_id = %s AND id > %s LIMIT 1',
						intval($_POST['step_id']),intval($_POST['question_id'])
						);
		$question = $wpdb->get_row($sql);
		if(!$question){
				//The step has no more questions add rewards for the finished step
				$sql = $wpdb->prepare(
								'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE id = %s LIMIT 1',
								intval($_POST['step_id'])
								);
				$step_passed = $wpdb->get_row($sql);
				$response['reward_name'] = $step_passed->currency;
				$response['reward_value'] = $retry*$step_passed->number;
				if (!in_array($step_passed->currency,array('Experience','Credits','Premium Currency'))){
							//We have custom attributes and we reward the user
							$sql = $wpdb->prepare(
											'SELECT * FROM '.$wpdb->prefix.'focusedstamps_attributes WHERE name = %s',
											lcfirst($step_passed->currency)
											);
							$attribute = $wpdb->get_row($sql);
							
							$sql = $wpdb->prepare(
											'SELECT * FROM '.$wpdb->prefix.'focusedstamps_attributes_users WHERE user_id = %s AND attribute_id = %s',
											$uid,$attribute->id
											);
							$user_attribute = $wpdb->get_row($sql);
							if (!$user_attribute){
									//Insert attribute value for user
									$wpdb->insert(
													$wpdb->prefix.'focusedstamps_attributes_users',
													array(
															'attribute_id' => $attribute->id,
															'user_id' => $uid,
															'value' => $retry*$step_passed->number
													)
													);
							}
							else{
									//Add attribute value for user
									
									$wpdb->update(
													$wpdb->prefix.'focusedstamps_attributes_users',
													array(
															'value' => $user_attribute->value + $retry*$step_passed->number
													),
													array(
															'id' => $user_attribute->id 
													)
													);
							}
				}
				
				//Check if there are more steps
				$sql = $wpdb->prepare(
								'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE id > %s AND quest_id = %s LIMIT 1',
								intval($_POST['step_id']),intval($_POST['quest_id'])
								);
				$step = $wpdb->get_row($sql);
				if ($step){
						//There is a new step, lets get its first question
						$sql = $wpdb->prepare(
										'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_questions WHERE step_id = %s ORDER BY id ASC LIMIT 1',
										$step->id
										);
						$question = $wpdb->get_row($sql);
						$response['quest_id'] = intval($_POST['quest_id']);
						$response['step_id'] = $step->id;
						$response['question_id'] = $question->id;
						
						$sql = $wpdb->prepare(
							'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE quest_id = %s AND uid = %s',
							intval($_POST['quest_id']),$uid
						);
						$user_quest = $wpdb->get_row($sql);
						if (!$user_quest)
						{
								//The quest has just been started
								$wpdb->insert(
												$wpdb->prefix.'focusedstamps_quest_user',
												array(
														'quest_id' => intval($_POST['quest_id']),
														'step_id' => $step->id,
														'question_id' => $question->id,
														'uid' => $uid
												)
								);
						}
						else{
								//Set new position of the user
								$wpdb->update(
												$wpdb->prefix.'focusedstamps_quest_user',
												array(
														'step_id' => $step->id,
														'question_id' => $question->id,
														'uid' => $uid
												),
												array(
														'uid' => $uid,
														'quest_id' => intval($_POST['quest_id'])
												)
												);
						}
				}
				else{
						//The user has finished the quest
						$response['end'] = 1;
						//check if there is a user entry for this quest
						$sql = $wpdb->prepare(
							'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s',
							$uid,intval($_POST['quest_id'])
						);
						$res = $wpdb->get_row($sql);
						if (!$res){
							$wpdb->insert(
								$wpdb->prefix.'focusedstamps_quest_user',
								array(
									'uid' => $uid,
									'quest_id' => intval($_POST['quest_id']),
									'step_id' => intval($_POST['step_id']),
									'question_id' => intval($_POST['question_id']),
									'status' => 1
								)
							);
						}
						else{
							$wpdb->update(
											$wpdb->prefix.'focusedstamps_quest_user',
											array(
													'status' => 1
											),
											array(
													'uid' => $uid,
													'quest_id' => intval($_POST['quest_id'])
											)
							);
						}
				}
			
		}
		else{
				// The quest has at least one more question
				$response['quest_id'] = intval($_POST['quest_id']);
				$response['step_id'] = intval($_POST['step_id']);
				$response['question_id'] = $question->id;
				$sql = $wpdb->prepare(
						'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s',
						$uid,intval($_POST['quest_id'])
						);
				 
				$user_quest = $wpdb->get_row($sql);
				if (!$user_quest)
				{
						//The user has just started the quest
						$wpdb->insert(
										$wpdb->prefix.'focusedstamps_quest_user',
										array(
												'quest_id' => intval($_POST['quest_id']),
												'step_id' => intval($_POST['step_id']),
												'question_id' => intval($_POST['question_id']),
												'uid' => $uid
										)
						);
				}
				else
				{
						//Set new position in the quest
						$wpdb->update(
										$wpdb->prefix.'focusedstamps_quest_user',
										array(
												'step_id' => intval($_POST['step_id']),
												'question_id' => intval($_POST['question_id']),
												'uid' => $uid
										),
										array(
												'uid' => $uid,
												'quest_id' => intval($_POST['quest_id'])
										)
										);
				}
		}
	}
	else{
	
		$response['status'] = 0;
		$response['answer'] = $res->answer;
		$response['reward_name'] = '';
		$response['reward_value'] = 0;
		
		$sql = $wpdb->prepare(
						'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_steps WHERE id = %s LIMIT 1',
						intval($_POST['step_id'])
						);
		$step_passed = $wpdb->get_row($sql);
		$response['reward_name'] = $step_passed->currency;
		$response['reward_value'] = $step_passed->number;
		
                $sql = $wpdb->prepare(
                    'SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user WHERE uid = %s AND quest_id = %s',
                    $uid,intval($_POST['quest_id'])
                    );

                $user_quest = $wpdb->get_row($sql);
                if (!$user_quest)
                    $wpdb->insert(
                            $wpdb->prefix.'focusedstamps_quest_user',
                            array(
                                'quest_id' => intval($_POST['quest_id']),
                                'step_id' => intval($_POST['step_id']),
                                'question_id' => intval($_POST['question_id']),
                                'uid' => $uid,
                                'status' => 2
                            )
                    );
                else
                    $wpdb->update(
                            $wpdb->prefix.'focusedstamps_quest_user',
                            array(
                                'status' => 2
                            ),
                            array(
                                'uid' => $uid,
                                'quest_id' => intval($_POST['quest_id'])
                            )
                            );
	
	}

	echo json_encode($response);
	
	die();
}

function fs_sync_quest_users($retry){
    global $wpdb;
    $ret = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'focusedstamps_quest_user',ARRAY_A );
    $fs_secret = get_option('focusedstamps_secret_key');
    $post = array(
            'name' => get_site_url(),
            'secret' => $fs_secret,
            'users' => json_encode($ret),
			'retry' => $retry
    );
    $status = curl_posting($post, get_fs_api_url('/sync_quest_users'));
    echo $status;
}


add_action( 'wp_ajax_nopriv_sync_quests', 'sync_quests_callback' );
add_action( 'wp_ajax_sync_quests', 'sync_quests_callback');

function sync_quests_callback(){
    fs_sync_quest_users($_POST['retry']);
    die();
}
?>