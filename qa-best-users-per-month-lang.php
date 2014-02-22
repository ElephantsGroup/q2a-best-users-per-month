<?php
	
/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/example-page/qa-example-lang-default.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: US English language phrases for example plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

	/* this file is UTF-8 encoded, you can use your language characters without worries */
	
	return array(
		// admin
		'admin_notable' => 'Database table is not set up yet.',
		'admin_create_table' => 'Create table',
		'admin_active' => 'activate best user per month',
		'admin_active_note' => 'Untick to deactivate.',
		'admin_date_type' => 'date type',
		'admin_georgian' => 'Georgian',
		'admin_jalali' => 'Jalali',
		'cronjob_button' => 'cronjob',
		'cronjob_success' => 'cronjob executed successfully.',
		'cronjob_failed' => 'cronjob failed to run.',
		'cronjob_exists' => 'cronjob could not run for this month.',

		// widget + page
		'best_users' => 'Best Users',			// your language string for 'best users'
		'points' => 'points',						// your language string for 'points'
		'rewardline_widget' => 'Rewards', 	// tell your users about monthly rewards/premiums
		'reward_1' => 'First place: ...', 			// this is for the 1st winner
		'reward_2' => 'Second place: ...',			// this is for the 2nd winner
		'reward_title' => 'Rewards for this month best users!', // the mousetip when mouse is over reward field: <p class="rewardlist" title="x">...</p>
		
		// on page only
		'page_title' => 'Best users per month (20 first places)', // best users of each month (top 20)
		'choose_month' => 'choose month:', 
		'rewardline_onpage' => 'Rewards: 1. Place: ... | 2. Place: ...', // tell your users about monthly rewards/premiums
		
		// subnavigation on all users page
		'subnav_title' => 'Best Users', // best users of the month
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/