<?php

	class qa_best_users_per_month_page {

		var $directory;
		var $urltoroot;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}
		
		// for display in admin interface under admin/pages
		function suggest_requests() 
		{	
			return array(
				array(
					'title' => qa_lang_html('qa_best_users_lang/best_users_per_month_page'), // title of page
					'request' => 'bestusers', // request name
					'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}
		
		// for url query
		function match_request($request)
		{
			if ($request=='bestusers') {
				return true;
			}

			return false;
		}

		function process_request($request)
		{
			$lang_page_title = qa_lang_html_sub('qa_best_users_lang/page_title', qa_opt('bupm_page_users_count'));

			if(!(bool)qa_opt('bupm_active'))
			{
				/* start */
				$qa_content=qa_content_prepare();

				// add sub navigation (remove for plugin release)
				// $qa_content['navigation']['sub']=qa_users_sub_navigation();
				
				$qa_content['title'] = $lang_page_title; // list title
				$qa_content['error']=qa_lang_html('qa_best_users_lang/plugin_is_not_activated');
				return $qa_content;
			}
			if(qa_opt('bupm_date_type') == "2") require_once 'jdf.php';
			/* SETTINGS */
			$maxusers = (int)qa_opt('bupm_page_users_count'); 			// max users to display 
			$showReward = true; 		// false to hide rewards
			$creditDeveloper = true;	// leave this true if you like this plugin, it sets one hidden link to my q2a-forum from the best-user-page only
			$excluded_users = array(0);

			if((bool)qa_opt('best_users_EExU'))
			{
				foreach(explode(',', qa_opt('best_users_ExU')) as $id)
					if($id) $excluded_users[] = intval(trim($id));
				
				if(QA_FINAL_EXTERNAL_USERS && (bool)qa_opt('best_users_EEU'))
				{
				}
				else
				{
					$excluded_users_query = qa_db_query_sub("SELECT userid FROM `^users` WHERE level=" . QA_USER_LEVEL_SUPER); 
					$rows = qa_db_read_all_assoc($excluded_users_query);
					foreach($rows as $row)
						$excluded_users[] = $row['userid'];
				}
			}
						
			/* TRANSFER LANGUAGE STRINGS */
			$lang_choose_month = qa_lang_html('qa_best_users_lang/choose_month');
			$lang_best_users = qa_lang_html('qa_best_users_lang/best_users');
			$lang_points = qa_lang_html('qa_best_users_lang/points');
			
			$rewards = explode(',', qa_opt('best_users_rewards'));
			foreach($rewards as $i=>$reward)
				if($reward)
					$rewards_text[] = qa_lang_html_sub('qa_best_users_lang/reward_n', ($i+1)) . ' ' . $reward;

			$showRewardOnTop = '<p style="font-size:14px;width:650px;margin-left:2px;line-height:140%;">';
				if(@$rewards_text) $showRewardOnTop .= implode(' | ', $rewards_text);
			$showRewardOnTop .= '</p>';
			
			
			/* start */
			$qa_content=qa_content_prepare();

			// add sub navigation (remove for plugin release)
			// $qa_content['navigation']['sub']=qa_users_sub_navigation();
			
			$qa_content['title'] = $lang_page_title; // list title

			// counter for custom html output
			$c = 2;
			
			
			// get first month to show in dropdown list (e.g. 10/2012)
			$firstListDate = '2012-10-01'; // default
			$queryFirstDate = qa_db_query_sub("SELECT `date` FROM `^userscores` ORDER BY `date` ASC LIMIT 1;"); 
			while ( ($row = qa_db_read_one_assoc($queryFirstDate,true)) !== null ) {
				$firstListDate = $row['date'];
				break;
			}
			
			
			// last entry of dropdown list
			// -1 month, to also show the "first point interval" from all 0 userscores to all first saved userscores
			$firstListDate = date("Y-m-01", strtotime($firstListDate."-1 month") );
			
			// first entry of dropdown list
			$lastListDate = date("Y-m-01");
			// if you want last month as default use
			// $lastListDate = date("Y-m-01", strtotime("last month") );
			
			// this month as default
			$chosenMonth = date("Y-m-01");
			// if you want last month as default use
			// $chosenMonth = date("Y-m", strtotime("last month") ); 
			
			// we received post data, user has chosen a month
			if( qa_post_text('request') ) {
				$chosenMonth = qa_post_text('request');
				// sanitize string, keep only 0-9 and - (maybe I am to suspicious?)
				$chosenMonth = preg_replace("/[^0-9\-]/i", '', $chosenMonth);
			}

			// get interval start from chosen month
			$intervalStart = date("Y-m-01", strtotime($chosenMonth) ); // 05/2012 becomes 2012-05-01
			$intervalEnd = date("Y-m-01", strtotime($chosenMonth."+1 month") ); // 05/2012 becomes 2012-06-01
			
			
			$dropdown_options=array();
			
			if(qa_opt('bupm_date_type') == 1)
			{
				foreach(get_year_months_en($firstListDate, $lastListDate) as $value)
					$dropdown_options[$value] = date("m/Y", strtotime($value) );
				arsort($dropdown_options);
			}
			else if(qa_opt('bupm_date_type') == 2)
				foreach(get_year_months_fa($firstListDate, $lastListDate) as $key => $value)
					$dropdown_options[$value] = $key;			

			// output reward on top
			if($showReward) {
				$qa_content['custom'.++$c] = $showRewardOnTop;
			}
			
			// output dropdown form for choosing the months
			$qa_content['form']=array(
				'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
				
				'style' => 'wide', // options: light, wide, tall, basic
				
				// 'ok' => qa_post_text('buttonOK') ? 'Chosen Month: '.qa_post_text('request') : null,

				// 'title' => 'Form title',
				'fields' => array(
					'request' => array(
						'type' => 'select',
						'label' => $lang_choose_month, 
						'tags' => 'NAME="request" onchange="this.form.submit()" id="dropdown_select"',
						'id' => 'dropdown', 
						'value' => @$dropdown_options[qa_post_text('request')],
						'options' => $dropdown_options,
						//'error' => qa_html('Another error'),
					),
				),
				
				// if you want to display a button, uncomment: 
				/*
				'buttons' => array(
					'ok' => array(
						'tags' => 'NAME="buttonOK"',
						'label' => 'Zeigen',
						'value' => '1',
					),
				),
				*/
				
			);

			// we need to do another query to get the userscores of the recent month
			if($chosenMonth == date("Y-m-01") ) {
				// calculate userscores from recent month
			$queryRecentScores = qa_db_query_sub("SELECT UP.userid, UP.points-COALESCE(TT.points, 0) AS mpoints
									FROM ^userpoints AS UP LEFT JOIN
									(SELECT US.userid, US.points FROM ^userscores AS US INNER JOIN
									(SELECT userid, MAX(date) AS mdate FROM ^userscores GROUP BY userid) T
									ON US.userid=T.userid AND US.date=T.mdate)
									TT ON UP.userid=TT.userid
									WHERE UP.userid NOT IN (".implode(',', $excluded_users).")
									ORDER BY mpoints DESC, UP.userid DESC;");
				// thanks srini.venigalla for helping me with advanced mysql
				// http://stackoverflow.com/questions/11085202/calculate-monthly-userscores-between-two-tables-using-mysql
			}
			else {
				if(qa_opt('bupm_date_type') == 1)
					$queryRecentScores = qa_db_query_sub("
											SELECT ul.userid, 
													ul.points - COALESCE(uf.points, 0) AS mpoints 
											FROM `^userscores` ul 
											LEFT JOIN (SELECT userid, points FROM `^userscores` WHERE `date` = '".$intervalStart."') AS uf
											ON uf.userid = ul.userid
											WHERE ul.date = '".$intervalEnd."'
											AND ul.userid NOT IN (".implode(',', $excluded_users).")
											ORDER BY mpoints DESC;"
										);
				else if(qa_opt('bupm_date_type') == 2)
					$queryRecentScores = qa_db_query_sub("
						SELECT END.userid, end_points-begin_points AS mpoints FROM
							(
								SELECT UP.userid, COALESCE(TTT1.points, UP.points) AS end_points FROM ^userpoints AS UP
									LEFT JOIN
									(
										SELECT TT1.userid, points FROM ^userscores AS TT1
											LEFT JOIN (
												SELECT userid, MIN(date) AS end_date FROM ^userscores WHERE date>='{$chosenMonth}' GROUP BY userid
											) AS T1
											ON T1.userid = TT1.userid WHERE TT1.date=end_date
									) TTT1
									ON UP.userid=TTT1.userid
							) END
							INNER JOIN
							(
								SELECT TT2.userid, COALESCE(points, 0) AS begin_points FROM ^userscores AS TT2
									LEFT JOIN
									(
										SELECT userid, MAX(date) AS begin_date FROM ^userscores WHERE date<'{$chosenMonth}' GROUP BY userid
									) AS T2
									ON T2.userid = TT2.userid WHERE TT2.date=begin_date
							) BEGIN
							ON END.userid=BEGIN.userid
						");
			}


			// save all userscores in array
			$scores = array();
			while ( ($row = qa_db_read_one_assoc($queryRecentScores,true)) !== null ) {
				$scores[$row['userid']] = $row['mpoints'];
			}

			// save userids in array that we need for qa_userids_to_handles()
			$userids = array();
			$cnt = 0;
			foreach ($scores as $userId => $val) {
				$userids[++$cnt] = $userId;
			}
			
			// get handles (i.e. usernames) in array usernames
			$usernames = qa_userids_to_handles($userids);

			// initiate output string
			$bestusers = "<ol>";
			$nrUsers = 0;
			foreach ($scores as $userId => $val) {
				// no users with 0 points, and no blocked users!
				if($val>0) {
					$currentUser = $usernames[$userId];
					if(QA_FINAL_EXTERNAL_USERS && (bool)qa_opt('best_users_EEU'))
					{
						$bestusers .= "<li><a href=\"" . qa_path('user/'.$currentUser) . "\">" . $currentUser . "</a><p class=\"uscore\">" . $val . " " . $lang_points . "</p></li>";
						if(++$nrUsers >= $maxusers) break;
					}
					else
					{
						$user = qa_db_select_with_pending( qa_db_user_account_selectspec($currentUser, false) );
						// check if user is blocked, do not list them
						if (! (QA_USER_FLAGS_USER_BLOCKED & $user['flags'])) {
							// points below user name, check CSS descriptions for .bestusers
							$bestusers .= "<li>" . qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'], $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), false) . " " . qa_get_one_user_html($usernames[$userId], false).' <p class="uscore">'.$val.' '.$lang_points.'</p></li>'; 
							
							// max users to display 
							if(++$nrUsers >= $maxusers) break;
						}
					}
				}
			}
			$bestusers .= "</ol>";

			
			/* output into theme */
			$qa_content['custom'.++$c]='<div class="bestusers" style="border-radius:0; padding:35px 48px; margin-top:-2px;">';
			
			if(qa_opt('bupm_date_type') == 1)
				$monthName = date("m/Y", strtotime($chosenMonth) );
			else if(qa_opt('bupm_date_type') == 2)
				$monthName = jgetdate(strtotime($chosenMonth))['month'] . ' ' . jgetdate(strtotime($chosenMonth))['year'];
			
			$qa_content['custom'.++$c]='<div style="font-size:16px;margin-bottom:18px;"><b>'.$lang_best_users.' '.$monthName.'</b></div>'; 
			$qa_content['custom'.++$c]= $bestusers;
			
			$qa_content['custom'.++$c]='</div>';
			
			// make bestusers list bigger on page and style the dropdown
			$qa_content['custom'.++$c] = '<style type="text/css">#dropdown .qa-form-wide-label { width:120px; text-align:center; } #dropdown .qa-form-wide-data { width:120px; text-align:center; }</style>'; 
			
			// jquery workaround (or call it hack) to select the current month in dropdown
			//$qa_content['custom'.++$c] = ' <script type="text/javascript">$(document).ready(function(){  $("select#dropdown_select").val(\''.$intervalStart.'\') }); </script>';
			
			// as I said, this is one chance to say thank you
			if($creditDeveloper) {
				$qa_content['custom'.++$c] = "<a style='display:none' href='http://www.gute-mathe-fragen.de/'>Gute Mathe-Fragen! * Dein bestes Mathe-Forum</a>";
			}
			

			// WIDGET CALL: we want the best-user-widget also to be displayed on this page
			$widget['title'] = 'Best Users per Month';
			$module=qa_load_module('widget', $widget['title']);
			$region = "side";
			$place = "high";
			$qa_content['widgets'][$region][$place][] = $module;
			

			return $qa_content;
		}
		
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/