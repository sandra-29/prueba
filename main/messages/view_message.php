<?php
/* For licensing terms, see /license.txt */
/**
*	@package chamilo.messages
*/
$cidReset = true;
require_once '../inc/global.inc.php';
api_block_anonymous_users();
if (api_get_setting('allow_message_tool')!='true') {
	api_not_allowed();
}

if (isset($_REQUEST['f']) && $_REQUEST['f'] == 'social') {
	$this_section = SECTION_SOCIAL;
	$interbreadcrumb[]= array ('url' => api_get_path(WEB_PATH).'main/social/home.php','name' => get_lang('Social'));
	$interbreadcrumb[]= array ('url' => 'inbox.php?f=social','name' => get_lang('Inbox'));
} else {
	$this_section = SECTION_MYPROFILE;
	$interbreadcrumb[]= array ('url' => api_get_path(WEB_PATH).'main/auth/profile.php','name' => get_lang('Profile'));
}

$social_right_content = '';

if (isset($_GET['f']) && $_GET['f']=='social') {
	$social_parameter = '?f=social';
} else {
	if (api_get_setting('extended_profile') == 'true') {
		$social_right_content .= '<div class="actions">';

		if (api_get_setting('allow_social_tool') === 'true' && api_get_setting('allow_message_tool') === 'true') {
			$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php">'.
                Display::return_icon('shared_profile.png', get_lang('ViewSharedProfile')).'</a>';
		}
		if (api_get_setting('allow_message_tool') === 'true') {
		    $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php">'.
                Display::return_icon('message_new.png',get_lang('ComposeMessage')).'</a>';
            $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php">'.
                Display::return_icon('inbox.png',get_lang('Inbox')).'</a>';
            $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/outbox.php">'.
                Display::return_icon('outbox.png',get_lang('Outbox')).'</a>';
		}
		$social_right_content .= '</div>';
	}
}

if (empty($_GET['id'])) {
    $id_message = $_GET['id_send'];
    $source = 'outbox';
    $show_menu = 'messages_outbox';
} else {
    $id_message = $_GET['id'];
    $source = 'inbox';
    $show_menu = 'messages_inbox';
}

$message  = '';

// LEFT COLUMN
if (api_get_setting('allow_social_tool') === 'true') {
    // Block Social Menu
    $social_menu_block = SocialManager::show_social_menu($show_menu);
}
// MAIN CONTENT
$message .= MessageManager::show_message_box($id_message, $source);

if (!empty($message)) {
    $social_right_content .= $message;
} else {
    api_not_allowed();
}
$tpl = new Template(get_lang('View'));
// Block Social Avatar
SocialManager::setSocialUserBlock($tpl, $user_id, $show_menu);

if (api_get_setting('allow_social_tool') === 'true') {
    $tpl->assign('social_menu_block', $social_menu_block);
    $tpl->assign('social_right_content', $social_right_content);
    $social_layout = $tpl->get_template('social/inbox.tpl');
    $tpl->display($social_layout);
} else {
    $content = $social_right_content;

    $tpl->assign('content', $content);
    $tpl->display_one_col_template();
}
