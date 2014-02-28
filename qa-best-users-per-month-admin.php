<?php

require_once QA_INCLUDE_DIR.'qa-app-users.php';

class qa_best_users_per_month_admin
{
	private $optactive = 'bupm_active';
	private $date_type = 'bupm_date_type';
	private $page_users_count = 'bupm_page_users_count';
	private $widget_users_count = 'bupm_widget_users_count';
	/*private $view_permission = 'best_users_view_permission';*/
	private $enabled_external_users = 'best_users_EEU';
	private $external_users_table = 'best_users_EUT';
	private $external_users_table_key = 'best_users_EUTK';
	private $external_users_table_handle = 'best_users_EUTH';
	private $enabled_excluded_users = 'best_users_EExU';
	private $excluded_users = 'best_users_ExU';
	private $rewards = 'best_users_rewards';

	// initialize db-table 'userscores' if it does not exist yet
	function init_queries($tableslc)
	{
		$tablename=qa_db_add_table_prefix('userscores');
		
		if(!in_array($tablename, $tableslc)) {
			return "CREATE TABLE IF NOT EXISTS `".$tablename."` (
			  `date` date NOT NULL,
			  `userid` int(10) unsigned NOT NULL,
			  `points` int(11) NOT NULL DEFAULT '0',
			  KEY `userid` (`userid`),
			  KEY `date` (`date`)
			)
			";
		}
	}

	function admin_form( &$qa_content )
	{
		$saved_msg = '';
		$error = null;
		$date_types = array( 1 => qa_lang_html('qa_best_users_lang/admin_georgian'), 2 => qa_lang_html('qa_best_users_lang/admin_jalali'));
		//$permitoptions = qa_admin_permit_options(QA_PERMIT_ALL, QA_PERMIT_SUPERS, false, false);
		$rewards = explode(',', qa_opt($this->rewards));
		$post = @$_POST['rewards'];
		$deleted = @$_POST['deleted_rewards'];

		if ( qa_clicked('bupm_add_reward') )
		{
			$rewards = $this->save_rewards( $post, $deleted );
			$rewards[] = '';
		}
		if ( qa_clicked('bupm_cronjob') )
		{
			// get current month from today
			$date = date('Y-m-d');
			
			// MONTH 
			// to avoid double entries, check if cronjob was not already run for THIS MONTH
			$sql = "SELECT `date` FROM `^userscores` 
						WHERE YEAR(`date`) = YEAR('".$date."') 
						AND MONTH(`date`) = MONTH('".$date."');";
			$result = qa_db_query_sub($sql);
			$rows = qa_db_read_all_assoc($result);
			if (count($rows) > 0) { 
				$saved_msg = qa_lang_html('qa_best_users_lang/cronjob_exists');
			}
			else {
				try {
					$sql = "INSERT INTO `^userscores` (userid, points, date) SELECT userid, points, '".$date."' AS date from `^userpoints` ORDER BY userid ASC;";
					qa_db_query_sub($sql);
					$saved_msg = qa_lang_html('qa_best_users_lang/cronjob_success');
				}
				catch(exception $extp) {
					$saved_msg = qa_lang_html('qa_best_users_lang/cronjob_failed');
				}
			}
		}
		if ( qa_clicked('bupm_save') )
		{
			if( !$this->validate_data() )
			{
				$saved_msg = qa_lang_html('qa_best_users_lang/incorrect_entry');
			}
			else
			{
				if ( qa_post_text('bupm_active') )
				{
					$sql = 'SHOW TABLES LIKE "^userscores"';
					$result = qa_db_query_sub($sql);
					$rows = qa_db_read_all_assoc($result);
					if ( count($rows) > 0 )
					{
						qa_opt( $this->optactive, '1' );
					}
					else
					{
						$error = array(
							'type' => 'custom',
							'error' => qa_lang_html('qa_best_users_lang/admin_notable') . '<a href="' . qa_path('install') . '">' . qa_lang_html('qa_best_users_lang/admin_create_table') . '</a>',
						);
					}

					$saved_msg = qa_lang_html('admin/options_saved');
				}
				else
					qa_opt( $this->optactive, '0' );
				qa_opt( $this->date_type, (int)qa_post_text('date_type') );
				qa_opt( $this->page_users_count, (int)qa_post_text('page_users_count') );
				qa_opt( $this->widget_users_count, (int)qa_post_text('widget_users_count') );
				/*qa_opt( $this->ninja_edit_time, (int)qa_post_text('ninja_edit_time') );
				qa_opt( $this->view_permission, (int)qa_post_text('view_permission') );*/
				if ( qa_post_text('enabled_external_users') ) qa_opt( $this->enabled_external_users, '1' );
				else qa_opt( $this->enabled_external_users, '0' );
				qa_opt( $this->external_users_table, qa_post_text('external_users_table') );
				qa_opt( $this->external_users_table_key, qa_post_text('external_users_table_key') );
				qa_opt( $this->external_users_table_handle, qa_post_text('external_users_table_handle') );
				if ( qa_post_text('enabled_excluded_users') ) qa_opt( $this->enabled_excluded_users, '1' );
				else qa_opt( $this->enabled_excluded_users, '0' );
				qa_opt( $this->excluded_users, qa_post_text('excluded_users') );
				$rewards = $this->save_rewards( $post, $deleted );
			}
		}

		$bupm_active = qa_opt($this->optactive);
		$date_type = qa_opt($this->date_type);
		$page_users_count = qa_opt($this->page_users_count);
		$widget_users_count = qa_opt($this->widget_users_count);
		/*$ninja_edit_time = qa_opt($this->ninja_edit_time);
		$view_permission = qa_opt($this->view_permission);*/
		$enabled_external_users = qa_opt($this->enabled_external_users);
		$external_users_table = qa_opt($this->external_users_table);
		$external_users_table_key = qa_opt($this->external_users_table_key);
		$external_users_table_handle = qa_opt($this->external_users_table_handle);
		$enabled_excluded_users = qa_opt($this->enabled_excluded_users);
		$excluded_users = qa_opt($this->excluded_users);

		$form = array(
			'ok' => $saved_msg,

			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('qa_best_users_lang/admin_active'),
					'tags' => 'NAME="bupm_active"',
					'value' => $bupm_active === '1',
					'note' => qa_lang_html('qa_best_users_lang/admin_active_note'),
				),
				array(
					'type' => 'select',
					'label' => qa_lang_html('qa_best_users_lang/admin_date_type'),
					'tags' => 'NAME="date_type"',
					'value' =>  @$date_types[$date_type],
					'options' => $date_types,
				),
				array(
					'type' => 'select',
					'label' => qa_lang_html('qa_best_users_lang/page_users_count'),
					'tags' => 'NAME="page_users_count"',
					'value' =>  $page_users_count,
					'options' => array(5=>5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20),
				),
				array(
					'type' => 'select',
					'label' => qa_lang_html('qa_best_users_lang/widget_users_count'),
					'tags' => 'NAME="widget_users_count"',
					'value' =>  $widget_users_count,
					'options' => array(2=>2, 3, 4, 5, 6, 7),
				),
				/*array(
					'type' => 'select',
					'label' => qa_lang_html('edithistory/view_permission'),
					'tags' => 'NAME="view_permission"',
					'value' =>  @$permitoptions[$view_permission],
					'options' => $permitoptions,
					'note' => qa_lang_html('edithistory/view_permission_note'),
				),*/
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('qa_best_users_lang/enabled_external_users'),
					'tags' => 'NAME="enabled_external_users"',
					'value' => $enabled_external_users === '1',
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_best_users_lang/external_users_table'),
					'tags' => 'NAME="external_users_table"',
					'value' => $external_users_table,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_best_users_lang/external_users_table_key'),
					'tags' => 'NAME="external_users_table_key"',
					'value' => $external_users_table_key,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_best_users_lang/external_users_table_handle'),
					'tags' => 'NAME="external_users_table_handle"',
					'value' => $external_users_table_handle,
				),
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('qa_best_users_lang/enabled_excluded_users'),
					'tags' => 'NAME="enabled_excluded_users"',
					'value' => $enabled_excluded_users === '1',
					'note' => qa_lang_html('qa_best_users_lang/enabled_excluded_users_note'),
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_best_users_lang/excluded_users'),
					'tags' => 'NAME="excluded_users"',
					'value' => $excluded_users,
					'note' => qa_lang_html('qa_best_users_lang/excluded_users_note'),
				),
			),

			'buttons' => array(
				array(
					'label' => qa_lang_html('admin/save_options_button'),
					'tags' => 'name="bupm_save"',
				),
				array(
					'label' => qa_lang_html('qa_best_users_lang/add_reward_button'),
					'tags' => 'name="bupm_add_reward"',
				),
				array(
					'label' => qa_lang_html('qa_best_users_lang/cronjob_button'),
					'tags' => 'name="bupm_cronjob"',
				),
			),

		);
		
		for ( $i = 0, $len = count($rewards); $i < $len; $i++ )
		{
			$form['fields'][] = array(
					'label' => qa_lang_html_sub('qa_best_users_lang/reward_n', ($i+1)),
					'tags' => 'name="rewards['.$i.']"',
					'value' => qa_html($rewards[$i]),
					'note' => '<label style="white-space:nowrap"><input type="checkbox" name="deleted_rewards['.$i.']"> ' .
						qa_lang_html('qa_best_users_lang/delete_reward') . '</label>',
				);
			$form['fields'][] = array(
				'type' => 'blank',
			);
		}

		if ( $error !== null )
			$form['fields'][] = $error;

		return $form;
	}

	private function save_rewards( $data, $deleted )
	{
		$rewards = array();
		foreach ( $data as $i=>$reward )
		{
			if ( !isset( $deleted[$i] ) )
			{
				$rewards[] = $reward;
			}
		}

		qa_opt( $this->rewards, implode(',', $rewards) );

		return $rewards;
	}
	
	private function validate_data()
	{
		$ret = true;
		
		$table = $_POST['external_users_table'];
		$table_key = $_POST['external_users_table_key'];
		$table_handle = $_POST['external_users_table_handle'];
	
		// check if table exists
		$sql = "SHOW TABLES LIKE '$table'";
		$result = qa_db_query_sub($sql);
		$rows = qa_db_read_all_assoc($result);
		if (count($rows) == 0)
			$ret = false;
			
		// check if id column exists
		$sql = "SHOW COLUMNS FROM `$table` LIKE '$table_key'";
		$result = qa_db_query_sub($sql);
		$rows = qa_db_read_all_assoc($result);
		if (count($rows) == 0)
			$ret = false;
			
		// check if id column exists
		$sql = "SHOW COLUMNS FROM `$table` LIKE '$table_handle'";
		$result = qa_db_query_sub($sql);
		$rows = qa_db_read_all_assoc($result);
		if (count($rows) == 0)
			$ret = false;
			
			
		// check excluded users
		$userids = explode(',', $_POST['excluded_users']);
		foreach($userids as $id)
			if($id and !is_numeric(trim($id)))
			{
				$ret = false;
				break;
			}
		return $ret;
	}
}
