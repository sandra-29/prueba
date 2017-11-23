<?php
/* For licensing terms, see /license.txt */

$cidReset = true;

require_once '../../../main/inc/global.inc.php';

$interbreadcrumb[] = array('url' => 'manage.php', 'name' => get_lang('VChamilo'));

// Security
api_protect_admin_script();
Virtual::checkSettings();

$plugin = VChamiloPlugin::create();
$form = new FormValidator('import', 'post', api_get_self());

// Database host.
$form->addHeader(get_lang('From'));

$form->addText('root_web', [$plugin->get_lang('rootweb'), 'Example: http://www.chamilo.org/']);
$form->addText('db_host', $plugin->get_lang('dbhost'));
$form->applyFilter('db_host', 'trim');

// Database login.
$form->addText('db_user', $plugin->get_lang('dbuser'));
$form->applyFilter('db_user', 'trim');

// Database password.
$form->addElement(
    'password',
    'db_password',
    $plugin->get_lang('dbpassword'),
    array('id' => 'id_vdbpassword')
);

// Database name.
$form->addText('main_database', [$plugin->get_lang('maindatabase')]);

$form->addText(
    'course_path',
    [
        $plugin->get_lang('CoursePath'),
        get_lang('Example').': /var/www/site/virtual/var/courses'
    ],
    true
);

$form->addText(
    'home_path',
    [
        $plugin->get_lang('HomePath'),
        get_lang('Example').': /var/www/site/virtual/var/home'
    ],
    true
);


$form->addHeader(get_lang('To'));

$form->addText('to_db_host', $plugin->get_lang('dbhost'));
$form->applyFilter('to_db_host', 'trim');

// Database login.
$form->addText('to_db_user', $plugin->get_lang('dbuser'));
$form->applyFilter('to_db_user', 'trim');

// Database password.
$form->addElement(
    'password',
    'to_db_password',
    $plugin->get_lang('dbpassword'),
    array('id' => 'id_vdbpassword')
);

// Database name.
$form->addText('to_main_database', [$plugin->get_lang('maindatabase'), $plugin->get_lang('DatabaseDescription')]);

$form->addButtonSave($plugin->get_lang('savechanges'), 'submitbutton');
$content = $form->returnForm();

if ($form->validate()) {
    $values = $form->getSubmitValues();

    $coursePath = $values['course_path'];
    $homePath = $values['home_path'];

    if (is_dir($coursePath) && is_dir($homePath)) {
        $currentHost = api_get_configuration_value('db_host');
        $currentDatabase = api_get_configuration_value('main_database');
        $currentUser = api_get_configuration_value('db_user');
        $currentPassword = api_get_configuration_value('db_password');

        if ($values['to_main_database'] !== $currentDatabase &&
            $values['to_db_user'] !== $currentUser &&
            $values['to_db_password'] !== $currentPassword
        ) {

        } else {
            Display::addFlash(
                Display::return_message(
                    $plugin->get_lang('DatabaseAccessShouldBeDifferentThanMasterChamilo')
                )
            );
        }

        $vchamilo = new stdClass();
        $vchamilo->main_database = $values['main_database'];
        $vchamilo->db_user = $values['db_user'];
        $vchamilo->db_password = $values['db_password'];
        $vchamilo->db_host = $values['db_host'];
        $vchamilo->root_web = $values['root_web'];

        $vchamilo->import_to_main_database = $values['to_main_database'];
        $vchamilo->import_to_db_user = $values['to_db_user'];
        $vchamilo->import_to_db_password = $values['to_db_password'];
        $vchamilo->import_to_db_host = $values['to_db_host'];

        $vchamilo->course_path = $values['course_path'];
        $vchamilo->home_path = $values['home_path'];

        Virtual::importInstance($vchamilo);
    }
}

$tpl = new Template(get_lang('Import'), true, true, false, true, false);
$tpl->assign('content', $content);
$tpl->display_one_col_template();
