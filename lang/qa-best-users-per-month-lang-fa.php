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
		'admin_notable' => 'جدول پایگاه داده هنوز نصب نشده است.',
		'admin_create_table' => 'ساخت جدول',
		'admin_active' => 'فعال‌‌سازی بهترین کاربران ماه',
		'admin_active_note' => 'تیک را بردارید تا از ادامه‌ی بررسی بهترین کاربران انصراف دهید.',
		'admin_date_type' => 'نوع تاریخ',
		'admin_georgian' => 'میلادی',
		'admin_jalali' => 'هجری شمسی',
		'cronjob_button' => 'cronjob',
		'cronjob_success' => 'cronjob با موفقیت اجرا شد.',
		'cronjob_failed' => 'cronjob با شکست مواجه شد.',
		'cronjob_exists' => 'cronjob برای ماه جاری قابل اجرا نیست.',
		'enabled_external_users' => 'فعال‌سازی کاربران خارجی',
		'external_users_table' => 'نام جدول کاربران خارجی',
		'external_users_table_key' => 'کلید جدول کاربران خارجی',
		'external_users_table_handle' => 'نام فیلد هندل (نام کاربری) جدول کاربران خارجی',
		'page_users_count' => 'تعداد کاربران برتر در صفحه‌ی افزونه',
		'widget_users_count' => 'تعداد کاربران برتر در ابزارک',
		'enabled_excluded_users' => 'فعال‌سازی حذف کاربران',
		'enabled_excluded_users_note' => 'آیا می‌خواهید تعدادی از کاربران (مانند ادمین یا مدیران) را از فهرست بهترین کاربران حذف کنید؟',
		'excluded_users' => 'شناسه‌ی کاربران حذف شده',
		'excluded_users_note' => 'شناسه‌ی کاربران مورد نظر را با کاما جدا کنید. (مثلاً 1, 2, 3)',
		'add_reward_button' => 'افزودن یک پاداش',
		'reward_n' => 'پاداش نفر ^:',
		'delete_reward' => 'حذف پاداش',

		// widget + page
		'best_users' => 'بهترین کاربران',			// your language string for 'best users'
		'points' => 'امتیاز',						// your language string for 'points'
		'rewardline_widget' => 'پاداش‌ها', 	// tell your users about monthly rewards/premiums
		'reward_title' => 'پاداش بهترین کاربر این ماه ...!', // the mousetip when mouse is over reward field: <p class="rewardlist" title="x">...</p>
		'best_users_per_month' => 'بهترین کاربران ماه',
		'best_users_per_month_page' => 'صفحه‌ی بهترین کاربران ماه',
		'plugin_is_not_activated' => 'این صفحه غیرفعال شده است.',
		
		// on page only
		'page_title' => 'بهترین کاربران ماه (۲۰ نفر اول)', // best users of each month (top 20)
		'choose_month' => 'انتخاب ماه:', 
		'rewardline_onpage' => 'پاداش‌ها: نفر اول: ... | نفر دوم : ...', // tell your users about monthly rewards/premiums
		
		// subnavigation on all users page
		'subnav_title' => 'بهترین کاربران', // best users of the month
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/