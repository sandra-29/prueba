<?php
/* For licensing terms, see /license.txt */

/**
* Who is online list
*/
if (!isset($_GET['cidReq'])) {
    $cidReset = true;
}

// including necessary files
require_once './main/inc/global.inc.php';

if (isset($_GET['cidReq']) && strlen($_GET['cidReq']) > 0) {
    api_protect_course_script(true);
}

$this_section = SECTION_SOCIAL;
// table definitions

$social_right_content = null;
$whoisonline_list = null;
$social_search = '';

// This if statement prevents users accessing the who's online feature when it has been disabled.
if ((api_get_setting('showonline', 'world') == 'true' && !$_user['user_id']) ||
    ((api_get_setting('showonline', 'users') == 'true' || api_get_setting('showonline', 'course') == 'true') && $_user['user_id'])
) {

    if (isset($_GET['cidReq']) && strlen($_GET['cidReq']) > 0) {
        $user_list = who_is_online_in_this_course(0, MAX_ONLINE_USERS, api_get_user_id(), api_get_setting('time_limit_whosonline'), $_GET['cidReq']);
    } else {
        $user_list = who_is_online(0, MAX_ONLINE_USERS);
    }

    if ($user_list) {
        if (!isset($_GET['id'])) {
            if (api_get_setting('allow_social_tool') == 'true') {
                if (!api_is_anonymous()) {
                    $query = isset($_GET['q']) ? $_GET['q']: null;
                    $social_search = UserManager::get_search_form($query);
                }
            }
            $social_right_content .= SocialManager::display_user_list($user_list);
        }
    }

    $whoisonline_list .= SocialManager::display_user_list($user_list);


    if (isset($_GET['id'])) {
        if (api_get_setting('allow_social_tool') == 'true' && api_user_is_login()) {
            header("Location: ".api_get_path(WEB_CODE_PATH)."social/profile.php?u=".intval($_GET['id']));
            exit;
        } else {
            $social_right_content .= SocialManager::display_individual_user($_GET['id']);
        }
    }
} else {
    api_not_allowed();
    exit;
}

$tpl = new Template(get_lang('UsersOnLineList'));

if (api_get_setting('allow_social_tool') == 'true' && !api_is_anonymous()) {
    $tpl->assign('whoisonline', $whoisonline_list);
    $tpl->assign('social_search', $social_search);
    $social_layout = $tpl->get_template('social/whoisonline.tpl');
    $tpl->display($social_layout);
} else {
    $content = $social_right_content;
    $tpl->assign('header', get_lang('UsersOnLineList'));
    $tpl->assign('content', $content);
    $tpl->display_one_col_template();
}
