<?php
/* For licensing terms, see /license.txt */
/**
 * Global events controller class
 * @package chamilo.admin
 */
$cidReset = true;

require_once '../inc/global.inc.php';

if (api_get_setting('activate_email_template') != 'true') {
    api_not_allowed();
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'show':
        break;
    case 'add':
        break;
    case 'new':
        break;
    case 'delete':
        $event_email_template = new EventEmailTemplate();
        $event_email_template->delete($_GET['id']);
        $content = $event_email_template->display();
        break;
    default:
    case 'listing':
        $event_email_template = new EventEmailTemplate();
        $content = $event_email_template->display();
        break;
}

//jqgrid will use this URL to do the selects
$url = api_get_path(WEB_AJAX_PATH).'model.ajax.php?a=get_event_email_template';

//The order is important you need to check the the $column variable in the model.ajax.php file
$columns = array(
    get_lang('Subject'),
    get_lang('EventTypeName'),
    get_lang('Language'),
    get_lang('Status'),
    get_lang('Actions'),
);

//Column config
$column_model = array(
    array('name' => 'subject', 'index' => 'subject', 'width' => '80', 'align' => 'left'),
//                        array('name'=>'message',        'index'=>'message', 'width'=>'500',  'align'=>'left','sortable'=>'false'),
    array('name' => 'event_type_name', 'index' => 'event_type_name', 'width' => '80', 'align' => 'left'),
    array('name' => 'language_id', 'index' => 'language_id', 'width' => '80', 'align' => 'left'),
    array('name' => 'activated', 'index' => 'activated', 'width' => '80', 'align' => 'left'),
    array('name' => 'actions', 'index' => 'actions', 'width' => '100'),
);
//Autowidth
$extra_params['autowidth'] = 'true';
//height auto
$extra_params['height'] = 'auto';

$htmlHeadXtra[] = api_get_jqgrid_js();
$htmlHeadXtra[] = '<script>
$(function() {
    '.Display::grid_js('event_email_template',  $url,$columns,$column_model,$extra_params, array(), $action_links,true).'
});
</script>';

$interbreadcrumb[] = array('url' => 'index.php', 'name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => '#', 'name' => get_lang('Events'));

$tpl = new Template($tool_name);
$tpl->assign('actions', $actions);
$tpl->assign('message', $message);
$tpl->assign('content', $content);
$tpl->display_one_col_template();
