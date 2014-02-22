<?php

require_once QA_INCLUDE_DIR.'qa-app-users.php';

class qa_best_users_per_month_admin
{
	private $optactive = 'bupm_active';
	private $date_type = 'bupm_date_type';
	/*private $ninja_edit_time = 'best_users_NET';
	private $view_permission = 'best_users_view_permission';
	private $enabled_external_users = 'best_users_EEU';
	private $external_users_table = 'best_users_EUT';
	private $external_users_table_key = 'best_users_EUTK';
	private $external_users_table_handle = 'best_users_EUTH';*/

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
			/*qa_opt( $this->ninja_edit_time, (int)qa_post_text('ninja_edit_time') );
			qa_opt( $this->view_permission, (int)qa_post_text('view_permission') );
			if ( qa_post_text('enabled_external_users') ) qa_opt( $this->enabled_external_users, '1' );
			else qa_opt( $this->enabled_external_users, '0' );
			qa_opt( $this->external_users_table, qa_post_text('external_users_table') );
			qa_opt( $this->external_users_table_key, qa_post_text('external_users_table_key') );
			qa_opt( $this->external_users_table_handle, qa_post_text('external_users_table_handle') );*/
		}

		$bupm_active = qa_opt($this->optactive);
		$date_type = qa_opt($this->date_type);
		/*$ninja_edit_time = qa_opt($this->ninja_edit_time);
		$view_permission = qa_opt($this->view_permission);
		$enabled_external_users = qa_opt($this->enabled_external_users);
		$external_users_table = qa_opt($this->external_users_table);
		$external_users_table_key = qa_opt($this->external_users_table_key);
		$external_users_table_handle = qa_opt($this->external_users_table_handle);*/

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
				/*array(
					'type' => 'number',
					'label' => qa_lang_html('edithistory/ninja_edit_time'),
					'suffix' => qa_lang_html('edithistory/seconds'),
					'tags' => 'NAME="ninja_edit_time"',
					'value' => $ninja_edit_time,
					'note' => qa_lang_html('edithistory/ninja_edit_time_note'),
				),
				array(
					'type' => 'select',
					'label' => qa_lang_html('edithistory/view_permission'),
					'tags' => 'NAME="view_permission"',
					'value' =>  @$permitoptions[$view_permission],
					'options' => $permitoptions,
					'note' => qa_lang_html('edithistory/view_permission_note'),
				),
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('edithistory/enabled_external_users'),
					'tags' => 'NAME="enabled_external_users"',
					'value' => $enabled_external_users === '1',
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('edithistory/external_users_table'),
					'tags' => 'NAME="external_users_table"',
					'value' => $external_users_table,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('edithistory/external_users_table_key'),
					'tags' => 'NAME="external_users_table_key"',
					'value' => $external_users_table_key,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('edithistory/external_users_table_handle'),
					'tags' => 'NAME="external_users_table_handle"',
					'value' => $external_users_table_handle,
				),*/
			),

			'buttons' => array(
				array(
					'label' => qa_lang_html('admin/save_options_button'),
					'tags' => 'name="bupm_save"',
				),
				array(
					'label' => qa_lang_html('qa_best_users_lang/cronjob_button'),
					'tags' => 'name="bupm_cronjob"',
				),
			),

		);

		if ( $error !== null )
			$form['fields'][] = $error;

		return $form;
	}

}
