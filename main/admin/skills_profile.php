<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 *  @package chamilo.admin
 */

$cidReset = true;
require_once '../inc/global.inc.php';
$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

if (api_get_setting('allow_skills_tool') != 'true') {
    api_not_allowed();
}

$interbreadcrumb[] = array(
    'url' => 'index.php',
    "name" => get_lang('PlatformAdmin'),
);

$skill           = new Skill();
$skill_profile   = new SkillProfile();
$skill_rel_user  = new SkillRelUser();

$url  = api_get_path(WEB_AJAX_PATH).'skill.ajax.php';

$tpl = new Template(get_lang('Skills'));

$form = new FormValidator('profile_search');

$form->addElement('header', get_lang('SearchSkills'));
$form->addElement('select', 'skills', null, null, array('id'=>'skills'));
$form->addButtonSearch(get_lang('Search'));

$profiles = $skill_profile->get_all();

$tpl->assign('profiles', $profiles);

$total_skills_to_search = array();

if ($form->validate()) {
    $values = $form->getSubmitValues();

    $skills = $values['skills'];
    if (!empty($skills)) {
        $hidden_skills = isset($values['hidden_skills']) ? $values['hidden_skills'] : array();
        $skills = array_merge($skills, $hidden_skills);
        $skills = array_filter($skills);
        $skills = array_unique($skills);
        Session::write('skills', $skills);

    } else {
        $skills = Session::read('skills', []);
    }
} else {
    $skills = Session::read('skills', []);
}

$user_list = array();
$count_skills = count($skills);

$users  = $skill_rel_user->get_user_by_skills($skills);

if (!empty($users)) {
    foreach ($users as $user) {
        $user_info = api_get_user_info($user['user_id']);
        $user_list[$user['user_id']]['user'] = $user_info;
        $my_user_skills = $skill_rel_user->get_user_skills($user['user_id']);
        $user_skills = array();
        $found_counts = 0 ;
        foreach ($my_user_skills as $my_skill) {
            $found = false;
            if (in_array($my_skill['skill_id'], $skills)) {
                $found = true;
                $found_counts++;
            }
            $user_skills[] = array(
                'skill_id' => $my_skill['skill_id'],
                'found' => $found,
            );
            $total_skills_to_search[$my_skill['skill_id']] = $my_skill['skill_id'];
        }
        $user_list[$user['user_id']]['skills'] = $user_skills;
        $user_list[$user['user_id']]['total_found_skills'] = $found_counts;
    }
    $ordered_user_list = array();
    foreach ($user_list as $user_id => $user_data) {
        $ordered_user_list[$user_data['total_found_skills']][] = $user_data;
    }
    if (!empty($ordered_user_list)) {
        asort($ordered_user_list);
    }
}

//$tpl->assign('user_list', $user_list);
$tpl->assign('order_user_list', $ordered_user_list);
$tpl->assign('total_search_skills', $count_skills);

if (!empty($skills)) {
    $counter = 0;
    foreach ($skills as $hidden_skill_id) {
        $form->addElement('hidden', 'hidden_skills[]', $hidden_skill_id);
        $counter++;
    }
}

if (!empty($skills)) {
    foreach ($skills as $my_skill) {
        $total_skills_to_search[$my_skill] = $my_skill;
    }
}

$total_skills_to_search = $skill->get_skills_info($total_skills_to_search);
$action = isset($_REQUEST['a']) ? $_REQUEST['a'] : null;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;

switch ($action) {
    case 'remove_skill':
        $new_skill = array();
        foreach ($skills as $skill_id) {
            if ($id != $skill_id) {
                $new_skill[] = $skill_id;
            }
        }
        $skills = $new_skill;
        Session::write('skills', $skills);
        break;
    case 'load_profile':
        $skill_profile = new SkillRelProfile();
        $skills = $skill_profile->get_skills_by_profile($id);
        $total_skills_to_search = $skill->get_skills_info($skills);
        break;
}

$skill_list = array();
foreach ($total_skills_to_search as $skill_info) {
    $skill_list[$skill_info['id']] = $skill_info;
}

$tpl->assign('skill_list', $skill_list);
$tpl->assign('search_skill_list', $skills);
$form_to_html = $form->returnForm();
$tpl->assign('form', $form_to_html);
$tpl->assign('url', $url);
$templateName = $tpl->get_template('skill/profile.tpl');
$content = $tpl->fetch($templateName);
$tpl->assign('content', $content);
$tpl->display_one_col_template();
