<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	function doctype(){
	
		qa_html_theme_base::doctype();

		if($this->request == 'admin/permissions' && qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) {
			$permits[] = 'permit_view_best_users_page';
			foreach($permits as $optionname) {
				$value = qa_opt($optionname);
				$optionfield=array(
					'id' => $optionname,
					'label' => qa_lang_html('qa_best_users_lang/'.$optionname).':',
					'tags' => 'NAME="option_'.$optionname.'" ID="option_'.$optionname.'"',
					'error' => qa_html(@$errors[$optionname]),
				);
				
				$permitoptions=qa_admin_permit_options(QA_PERMIT_ALL, QA_PERMIT_ADMINS, (!QA_FINAL_EXTERNAL_USERS) && qa_opt('confirm_user_emails'));
				
				if (count($permitoptions)>1)
					qa_optionfield_make_select($optionfield, $permitoptions, $value,
						($value==QA_PERMIT_CONFIRMED) ? QA_PERMIT_USERS : min(array_keys($permitoptions)));
				$this->content['form']['fields'][$optionname]=$optionfield;

				$this->content['form']['fields'][$optionname.'_points']= array(
					'id' => $optionname.'_points',
					'tags' => 'NAME="option_'.$optionname.'_points" ID="option_'.$optionname.'_points"',
					'type'=>'number',
					'value'=>qa_opt($optionname.'_points'),
					'prefix'=>qa_lang_html('admin/users_must_have').'&nbsp;',
					'note'=>qa_lang_html('admin/points')
				);
				$checkboxtodisplay[$optionname.'_points']='(option_'.$optionname.'=='.qa_js(QA_PERMIT_POINTS).') ||(option_'.$optionname.'=='.qa_js(QA_PERMIT_POINTS_CONFIRMED).')';
			}
			qa_set_display_rules($this->content, $checkboxtodisplay);
		}

		global $qa_request;
		// adds subnavigation to pages bestusers and users
		if((bool)qa_opt('bupm_active') && ($qa_request == 'bestusers' || $qa_request == 'users')) {
			$this->content['navigation']['sub'] = array(
				'users' => array(
					'url' => qa_path_html('users'),
					'label' => qa_lang_html('main/highest_users'),
					'selected' => ($qa_request == 'users')
				),
				'bestusers' => array(
					'label' => qa_lang_html('qa_best_users_lang/subnav_title'),
					'url' => qa_path_html('bestusers'),
					'selected' => ($qa_request == 'bestusers')
				),
			);
		}
		// highlight selected
		/*if($qa_request == 'bestusers') {
			// not working
			$this->content['navigation']['sub']['bestusers']['selected'] = true;
		}*/
		
	}

}
