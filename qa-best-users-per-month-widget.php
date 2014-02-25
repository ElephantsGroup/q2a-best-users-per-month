<?php

class qa_best_users_per_month_widget {
	
	function allow_template($template)
	{
		$allow=false;
		
		switch ($template)
		{
			case 'activity':
			case 'qa':
			case 'questions':
			case 'hot':
			case 'ask':
			case 'categories':
			case 'question':
			case 'tag':
			case 'tags':
			case 'unanswered':
			case 'user':
			case 'users':
			case 'search':
			case 'admin':
			case 'custom':
				$allow=true;
				break;
		}
		
		return $allow;
	}
	
	function allow_region($region)
	{
		$allow=false;
		
		switch ($region)
		{
			case 'main':
			case 'side':
				$allow=true;
				break;
			case 'full':					
				break;
		}
		
		return $allow;
	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		if(!(bool)qa_opt('bupm_active'))
			return;
		require_once 'jdf.php';
		/* Settings */
		$maxusers = (int)qa_opt('bupm_widget_users_count');			// max users to display in widget
		$showReward = false; 	// false to hide rewards
		$excluded_users = array(0);

		if((bool)qa_opt('best_users_EExU'))
		{
			foreach(explode(',', qa_opt('best_users_ExU')) as $id)
				$excluded_users[] = $id;
			
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

		$rewards = explode(',', qa_opt('best_users_rewards'));
		$langActUsers = qa_lang_html('qa_best_users_lang/best_users');		// language string for 'best users'
		$pointsLang = qa_lang_html('qa_best_users_lang/points'); 			// language string for 'points'
		$rewardHtml = '<p class="rewardlist" title="'.qa_lang_html('qa_best_users_lang/reward_title').'"><b>'.qa_lang_html('qa_best_users_lang/rewardline_widget').'</b><br />';
		foreach($rewards as $i=>$reward)
			if($reward)
			{
				$rewardHtml .= qa_lang_html_sub('qa_best_users_lang/reward_n', ($i+1)) . ' ' . $reward . '<br />';
				$showReward = true;
			}
		$rewardHtml .= '</p>';
		
		
		/* 	CSS: 
		
			you can style the best-users-box by css: .bestusers
			define height and width of images using: .bestusers img
			
			For instance, for my template I used the following css (add these lines to qa-styles.css): 
			.bestusers { width: 184px;padding:10px 0 10px 8px;margin-bottom:20px; border:2px solid #CFdd00;-moz-border-radius:14px 3px 3px 14px;border-radius:14px 3px 3px 14px; }
			.bestusers ol { margin:0; padding-left:20px; }
			.bestusers li { position:relative; clear:both; height:40px; }
			.bestusers li .qa-avatar-link { display:inline-block; border:1px solid #CCCCCC; margin-right:4px; vertical-align:top; }
			.uscore { position:absolute; top:15px; left:40px; font-size:11px; color:#545454; }
			.rewardlist { clear:both; width:120px; padding:2px 7px; background: rgba(50,50,50,0.3); font-size:11px; color:#454545; margin:10px 0 0 50px; cursor:default; border:1px solid #C0CC50; }
		*/

		
		// compare userscores from last month to userpoints now (this query is considering new users that do not exist in qa_userscores) 
		// as we order by mpoints the query returns best users first, and we do not need to sort by php: arsort($scores)
		$queryRecentScores = qa_db_query_sub("SELECT UP.userid, UP.points-COALESCE(TT.points, 0) AS mpoints
								FROM ^userpoints AS UP LEFT JOIN
								(SELECT US.userid, US.points FROM ^userscores AS US INNER JOIN
								(SELECT userid, MAX(date) AS mdate FROM ^userscores GROUP BY userid) T
								ON US.userid=T.userid AND US.date=T.mdate)
								TT ON UP.userid=TT.userid
								WHERE UP.userid NOT IN (".implode(',', $excluded_users).")
								ORDER BY mpoints DESC, UP.userid DESC;");

		
		// save all userscores in array $scores
		$scores = array();
		while ( ($row = qa_db_read_one_assoc($queryRecentScores,true)) !== null ) {
			$scores[$row['userid']] = $row['mpoints'];
		}

		// save userids in array $userids that we need to get their usernames by qa_userids_to_handles()
		$userids = array();
		$cnt = 0;
		foreach ($scores as $userId => $val) {
			$userids[++$cnt] = $userId;
			// max users to store in array, had to be commented out as blocked users came into play (see below) 
			// if($cnt >= $maxusers) break;
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
					$bestusers .= "<li><a href=\"" . qa_path('user/'.$currentUser) . "\">" . $currentUser . "</a><p class=\"uscore\">" . $val . " " . $pointsLang . "</p></li>";
					if(++$nrUsers >= $maxusers) break;
				}
				else
				{
					$user = qa_db_select_with_pending( qa_db_user_account_selectspec($currentUser, false) );
					// check if user is blocked
					if (! (QA_USER_FLAGS_USER_BLOCKED & $user['flags'])) {
						// points below user name, check CSS descriptions for .bestusers
						$bestusers .= "<li>" . qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'], $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), false) . " " . qa_get_one_user_html($usernames[$userId], false).' <p class="uscore">'.$val.' '.$pointsLang.'</p></li>
						';

						// max users to display 
						if(++$nrUsers >= $maxusers) break;
					}
				}
			}
		}
		$bestusers .= "</ol>";

		// output into theme
		$themeobject->output('<div class="bestusers">');
		
		// if you want the month displayed in your language uncomment the following block, 
		// and comment out the line: $monthName = date('m/Y'); 
		// you have to define your language code as well, e.g. en_US, fr_FR, de_DE
		/* 
		$localcode = "de_DE"; 
		setlocale (LC_TIME, $localcode); 
		$monthName = strftime("%B %G", strtotime( date('F')) ); // %B for full month name, %b for abbreviation
		*/
		if(qa_opt('bupm_date_type') == 1)
			$monthName = date('m/Y'); // 2 digit month and 4 digit year
		else if(qa_opt('bupm_date_type') == 2)
			$monthName = jgetdate()['month'] . ' ' . jgetdate()['year'];

		$themeobject->output('<div style="font-size:14px;margin-bottom:18px;"><a style="font-weight:bold;" href="'.qa_opt('site_url').'bestusers">'.$langActUsers.'</a> <span style="font-size:12px;">'.$monthName.'</span></div>'); 
		$themeobject->output( $bestusers );
		
		// display reward info
		if($showReward) {
			$themeobject->output( $rewardHtml );
		}
		$themeobject->output('</div>');
	}

}

/*
	Omit PHP closing tag to help avoid accidental output
*/