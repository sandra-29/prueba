<?php
/* For licensing terms, see /license.txt */

/**
 *	This page allows the administrator to manage the system announcements.
 *	@package chamilo.admin.announcement
 */

// Resetting the course id.
$cidReset = true;

// Including the global initialization file.
require_once '../inc/global.inc.php';

// Setting the section (for the tabs).
$this_section = SECTION_PLATFORM_ADMIN;
$_SESSION['this_section']=$this_section;

$action = isset($_GET['action']) ? $_GET['action'] : null;
$action_todo = false;

// Access restrictions
api_protect_admin_script(true);

// Setting breadcrumbs.
$interbreadcrumb[] = array(
    "url" => 'index.php',
    "name" => get_lang('PlatformAdmin'),
);

$tool_name = null;

if (empty($_GET['lang'])) {
    $_GET['lang'] = isset($_SESSION['user_language_choice']) ? $_SESSION['user_language_choice'] : null;
}

if (!empty($action)) {
    $interbreadcrumb[] = array ("url" => "system_announcements.php", "name" => get_lang('SystemAnnouncements'));
    if ($action == 'add') {
        $interbreadcrumb[] = array ("url" => '#', "name" => get_lang('AddAnnouncement'));
    }
    if ($action == 'edit') {
        $interbreadcrumb[] = array ("url" => '#', "name" => get_lang('Edit'));
    }
} else {
    $tool_name = get_lang('SystemAnnouncements');
}

// Displaying the header.
Display :: display_header($tool_name);

if ($action != 'add' && $action != 'edit') {
    echo '<div class="actions">';
    echo '<a href="?action=add">'.Display::return_icon('add.png', get_lang('AddAnnouncement'), array(), 32).'</a>';
    echo '</div>';
}

/* MAIN CODE */

$show_announcement_list = true;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

// Form was posted?
if (isset($_POST['action'])) {
    $action_todo = true;
}

// Actions
switch($action) {
    case 'make_visible':
    case 'make_invisible':
        $status = false;
        if ($action == 'make_visible') {
            $status = true;
        }
        SystemAnnouncementManager :: set_visibility($_GET['id'], $_GET['person'], $status);
        break;
    case 'delete':
        // Delete an announcement.
        SystemAnnouncementManager :: delete_announcement($_GET['id']);
        Display :: display_confirmation_message(get_lang('AnnouncementDeleted'));
        break;
    case 'delete_selected':
        foreach ($_POST['id'] as $index => $id) {
            SystemAnnouncementManager :: delete_announcement($id);
        }
        Display :: display_confirmation_message(get_lang('AnnouncementDeleted'));
        $action_todo = false;
        break;
    case 'add':
        // Add an announcement.
        $values['action'] = 'add';
        // Set default time window: NOW -> NEXT WEEK
        $values['start'] = date('Y-m-d H:i:s', api_strtotime(api_get_local_time()));
        $values['end']   = date('Y-m-d H:i:s', api_strtotime(api_get_local_time()) + (7 * 24 * 60 * 60));

        $values['range'] =
            substr(api_get_local_time(time()), 0, 16).' / '.
            substr(api_get_local_time(time() + (7 * 24 * 60 * 60)), 0, 16);
        $action_todo = true;
        break;
    case 'edit':
        // Edit an announcement.
        $announcement = SystemAnnouncementManager:: get_announcement($_GET['id']);
        $values['id'] = $announcement->id;
        $values['title'] = $announcement->title;
        $values['content'] = $announcement->content;
        $values['start'] = api_get_local_time($announcement->date_start);
        $values['end'] = api_get_local_time($announcement->date_end);

        $values['range'] =
            substr(api_get_local_time($announcement->date_start), 0, 16).' / '.
            substr(api_get_local_time($announcement->date_end), 0, 16);

        $values['visible_teacher'] = $announcement->visible_teacher;
        $values['visible_student'] = $announcement->visible_student;
        $values['visible_guest'] = $announcement->visible_guest;
        $values['lang'] = $announcement->lang;
        $values['action'] = 'edit';
        $groups = SystemAnnouncementManager::get_announcement_groups($announcement->id);
        $values['group'] = isset($groups['group_id']) ? $groups['group_id'] : 0;
        $action_todo = true;
        break;
}

if ($action_todo) {
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add') {
        $form_title = get_lang('AddNews');
        $url = api_get_self();
    } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
        $form_title = get_lang('EditNews');
        $url = api_get_self().'?id='.intval($_GET['id']);
    }
    $form = new FormValidator('system_announcement', 'post', $url);
    $form->addElement('header', '', $form_title);
    $form->addText('title', get_lang('Title'), true);
    $language_list = api_get_languages();
    $language_list_with_keys = array();
    $language_list_with_keys['all'] = get_lang('All');
    for ($i = 0; $i < count($language_list['name']); $i++) {
        $language_list_with_keys[$language_list['folder'][$i]] = $language_list['name'][$i];
    }

    $form->addElement(
        'select',
        'lang',
        get_lang('Language'),
        $language_list_with_keys
    );
    $form->addHtmlEditor(
        'content',
        get_lang('Content'),
        true,
        false,
        array(
            'ToolbarSet' => 'PortalNews',
            'Width' => '100%',
            'Height' => '300',
        )
    );
    $form->addDateRangePicker('range', get_lang('StartTimeWindow'), true, array('id' => 'date_range'));

    $group = array();

    $group[]= $form->createElement('checkbox', 'visible_teacher', null, get_lang('Teacher'));
    $group[]= $form->createElement('checkbox', 'visible_student', null, get_lang('Student'));
    $group[]= $form->createElement('checkbox', 'visible_guest', null, get_lang('Guest'));

    $form->addGroup($group, null, get_lang('Visible'));

    $form->addElement('hidden', 'id');
    $userGroup = new UserGroup();
    $group_list = $userGroup->get_all();

    if (!empty($group_list)) {
        $group_list = array_column($group_list, 'name', 'id');
        $group_list[0] = get_lang('All');
        $form->addElement(
            'select',
            'group',
            get_lang('AnnouncementForGroup'),
            $group_list
        );
    }

    $values['group'] = isset($values['group']) ? $values['group'] : '0';

    $form->addElement('checkbox', 'send_mail', null, get_lang('SendMail'));

    if (isset($_REQUEST['action']) && $_REQUEST['action']=='add') {
        $form->addElement('checkbox', 'add_to_calendar', null, get_lang('AddToCalendar'));
        $text=get_lang('AddNews');
        $class='add';
        $form->addElement('hidden', 'action', 'add');

    } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
        $text=get_lang('EditNews');
        $class='save';
        $form->addElement('hidden', 'action', 'edit');
    }
    $form->addElement('checkbox', 'send_email_test', null, get_lang('SendOnlyAnEmailToMySelfToTest'));
    $form->addButtonSend($text, 'submit');
    $form->setDefaults($values);

    if ($form->validate()) {
        $values = $form->exportValues();
        if (!isset($values['visible_teacher'])) {
            $values['visible_teacher'] = false;
        }
        if (!isset($values['visible_student'])) {
            $values['visible_student'] = false;
        }
        if (!isset($values['visible_guest'])) {
            $values['visible_guest'] = false;
        }
        if ($values['lang'] == 'all') {
            $values['lang'] = null;
        }

        $sendMail = isset($values['send_mail']) ? $values['send_mail'] : null;

        switch ($values['action']) {
            case 'add':
                $announcement_id = SystemAnnouncementManager::add_announcement(
                    $values['title'],
                    $values['content'],
                    $values['range_start'],
                    $values['range_end'],
                    $values['visible_teacher'],
                    $values['visible_student'],
                    $values['visible_guest'],
                    $values['lang'],
                    $sendMail,
                    empty($values['add_to_calendar']) ? false : true,
                    empty($values['send_email_test']) ? false : true
                );

                if ($announcement_id !== false) {
                    if (isset($values['group'])) {
                        SystemAnnouncementManager::announcement_for_groups(
                            $announcement_id,
                            array($values['group'])
                        );
                        Display:: display_confirmation_message(
                            get_lang('AnnouncementAdded')
                        );
                    }
                } else {
                    $show_announcement_list = false;
                    $form->display();
                }
                break;
            case 'edit':
                $sendMailTest = isset($values['send_email_test']) ? $values['send_email_test'] : null;

                if (SystemAnnouncementManager::update_announcement(
                    $values['id'],
                    $values['title'],
                    $values['content'],
                    $values['range_start'],
                    $values['range_end'],
                    $values['visible_teacher'],
                    $values['visible_student'],
                    $values['visible_guest'],
                    $values['lang'],
                    $sendMail,
                    $sendMailTest
                )
                ) {
                    if (isset($values['group'])) {
                        SystemAnnouncementManager::announcement_for_groups(
                            $values['id'],
                            array($values['group'])
                        );
                        Display:: display_confirmation_message(
                            get_lang('AnnouncementUpdated')
                        );
                    }
                } else {
                    $show_announcement_list = false;
                    $form->display();
                }
                break;
            default:
                break;
        }
        $show_announcement_list = true;
    } else {
        $form->display();
        $show_announcement_list = false;
    }
}

if ($show_announcement_list) {
    $announcements = SystemAnnouncementManager :: get_all_announcements();
    $announcement_data = array ();
    foreach ($announcements as $index => $announcement) {
        $row = array();
        $row[] = $announcement->id;
        $row[] = Display::return_icon(($announcement->visible ? 'accept.png' : 'exclamation.png'), ($announcement->visible ? get_lang('AnnouncementAvailable') : get_lang('AnnouncementNotAvailable')));
        $row[] = $announcement->title;
        $row[] = api_convert_and_format_date($announcement->date_start);
        $row[] = api_convert_and_format_date($announcement->date_end);
        $row[] = "<a href=\"?id=".$announcement->id."&person=".SystemAnnouncementManager::VISIBLE_TEACHER."&action=". ($announcement->visible_teacher ? 'make_invisible' : 'make_visible')."\">".Display::return_icon(($announcement->visible_teacher  ? 'eyes.png' : 'eyes-close.png'), get_lang('ShowOrHide'))."</a>";
        $row[] = "<a href=\"?id=".$announcement->id."&person=".SystemAnnouncementManager::VISIBLE_STUDENT."&action=". ($announcement->visible_student  ? 'make_invisible' : 'make_visible')."\">".Display::return_icon(($announcement->visible_student  ? 'eyes.png' : 'eyes-close.png'), get_lang('ShowOrHide'))."</a>";
        $row[] = "<a href=\"?id=".$announcement->id."&person=".SystemAnnouncementManager::VISIBLE_GUEST."&action=". ($announcement->visible_guest ? 'make_invisible' : 'make_visible')."\">".Display::return_icon(($announcement->visible_guest  ? 'eyes.png' : 'eyes-close.png'), get_lang('ShowOrHide'))."</a>";

        $row[] = $announcement->lang;
        $row[] = "<a href=\"?action=edit&id=".$announcement->id."\">".Display::return_icon('edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL)."</a> <a href=\"?action=delete&id=".$announcement->id."\"  onclick=\"javascript:if(!confirm('".addslashes(api_htmlentities(get_lang("ConfirmYourChoice"), ENT_QUOTES))."')) return false;\">".Display::return_icon('delete.png', get_lang('Delete'), array(), ICON_SIZE_SMALL)."</a>";
        $announcement_data[] = $row;
    }
    $table = new SortableTableFromArray($announcement_data);
    $table->set_header(0, '', false);
    $table->set_header(1, get_lang('Active'));
    $table->set_header(2, get_lang('Title'));
    $table->set_header(3, get_lang('StartTimeWindow'));
    $table->set_header(4, get_lang('EndTimeWindow'));
    $table->set_header(5, get_lang('Teacher'));
    $table->set_header(6, get_lang('Student'));
    $table->set_header(7, get_lang('Guest'));

    $table->set_header(8, get_lang('Language'));
    $table->set_header(9, get_lang('Modify'), false, 'width="50px"');
    $form_actions = array();
    $form_actions['delete_selected'] = get_lang('Delete');
    $table->set_form_actions($form_actions);
    $table->display();
}

Display :: display_footer();
