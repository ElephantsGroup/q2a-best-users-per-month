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
		'enabled_external_users' => 'Enable external users',
		'external_users_table' => 'External users table name',
		'external_users_table_key' => 'External users table key name',
		'external_users_table_handle' => 'External users table handle field name',
		'page_users_count' => 'Number of displayed best users in page',
		'widget_users_count' => 'Number of displayed best users in widget',
		'enabled_excluded_users' => 'Enable excluded users',
		'enabled_excluded_users_note' => 'Do you want to exclude some users (e.g. admin, moderators, ...) from best users list?',
		'excluded_users' => 'Excluded users ids',
		'excluded_users_note' => 'Enter comma separated list of excluded users ids (e.g. 1,2,3).',
		'add_reward_button' => 'Add a reward',
		'reward_n' => 'Rewads for user ^:',
		'delete_reward' => 'Delete Reward',
		'incorrect_entry' => 'Incorrect Entries',
		'award_level' => 'Award Level',
		'award_level_note' => 'Maximum level that include in awards.',

		// widget + page
		'best_users' => 'Best Users',			// your language string for 'best users'
		'points' => 'points',						// your language string for 'points'
		'rewardline_widget' => 'Rewards', 	// tell your users about monthly rewards/premiums
		'reward_title' => 'Rewards for this month best users!', // the mousetip when mouse is over reward field: <p class="rewardlist" title="x">...</p>
		'best_users_per_month' => 'Best Users per Month',
		'best_users_per_month_page' => 'Best Users per Month Page',
		'plugin_is_not_activated' => 'This page is deactivated.',
		
		// on page only
		'page_title' => 'Best users per month (^ first places)', // best users of each month (top 20)
		'choose_month' => 'choose month:', 
		'rewardline_onpage' => 'Rewards: 1. Place: ... | 2. Place: ...', // tell your users about monthly rewards/premiums
		
		// subnavigation on all users page
		'subnav_title' => 'Best Users', // best users of the month
		'permit_view_best_users_page' => 'best users page view',
		'permission_error' => 'Sorry, you do not have permissions to view best users records.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/