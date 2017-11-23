<?php
/* For licensing terms, see /license.txt */

/**
 *
 * @package chamilo.plugin.ticket
 */
require_once __DIR__.'/../inc/global.inc.php';

api_block_anonymous_users();

$user_id = api_get_user_id();
$isAdmin = api_is_platform_admin();
$interbreadcrumb[] = array(
    'url' => api_get_path(WEB_CODE_PATH).'ticket/tickets.php',
    'name' => get_lang('MyTickets')
);
$interbreadcrumb[] = array('url' => '#', 'name' => get_lang('TicketDetail'));

$disableReponseButtons = '';
$htmlHeadXtra[] = '<script>
$(document).ready(function() {
	$("#dialog-form").dialog({
		autoOpen: false,
		height: 450,
		width: 600,
		modal: true,
		buttons: {
            ' . get_lang('Accept') . ': function(){
                $("#frmResponsable").submit()
            },
            ' . ucfirst(get_lang('Close')) . ': function() {
                $(this).dialog("close");
            }
            }
        });

        $("a#assign").click(function () {
            $( "#dialog-form" ).dialog( "open" );
        });

        $(".responseyes").click(function () {
            if(!confirm("' . get_lang('AreYouSure') . ' : ' . strtoupper(get_lang('Yes')) . '. ' . get_lang('IfYouAreSureTheTicketWillBeClosed') . '")){
                return false;
            }
        });

        $("input#responseno").click(function () {
            if(!confirm("' . get_lang('AreYouSure') . ' : ' . strtoupper(get_lang('No')) . '")){
                return false;
            }
        });

        $("#unassign").click(function () {
            if (!confirm("' . get_lang('AreYouSureYouWantToUnassignTheTicket') . '")) {
                return false;
            }
        });

        $("#close").click(function () {
            if (!confirm("' . get_lang('AreYouSureYouWantToCloseTheTicket') . '")) {
                return false;
            }
        });
        '.$disableReponseButtons.'
});

function validate() {
    fckEditor1val = CKEDITOR.instances["content"].getData();
    document.getElementById("content").value= fckEditor1val;
    if (fckEditor1val == ""){
        alert("' . get_lang('Filled') . '");
        return false;
    }
}

var counter_image = 1;

function remove_image_form(element_id) {
    $("#" + element_id).remove();
    counter_image = counter_image - 1;
    $("#link-more-attach").css("display", "block");
}

function add_image_form() {
    // Multiple filepaths for image form
    var filepaths = $("#filepaths");
    var new_elem, input_file, link_remove, img_remove, new_filepath_id;

    if ($("#filepath_"+counter_image)) {
        counter_image = counter_image + 1;
    }  else {
        counter_image = counter_image;
    }

    new_elem = "filepath_"+counter_image;

    $("<div/>", {
        id: new_elem,
        class: "controls"
    }).appendTo(filepaths);

    input_file = $("<input/>", {
        type: "file",
        name: "attach_" + counter_image,
        size: 20
    });

    link_remove = $("<a/>", {
        onclick: "remove_image_form(\'" + new_elem + "\')",
        style: "cursor: pointer"
    });

    img_remove = $("<img/>", {
        src: "' . Display::returnIconPath('delete.png').'"
    });

    new_filepath_id = $("#filepath_" + counter_image);
    new_filepath_id.append(input_file, link_remove.append(img_remove));

    if (counter_image === 6) {
        var link_attach = $("#link-more-attach");
        if (link_attach) {
            $(link_attach).css("display", "none");
        }
    }
}
</script>';

$htmlHeadXtra[] = '<style>
div.row div.label2 {
	float:left;
	text-align: right;
	width:22%;
}
div.row div.formw2 {
    width:50%;
	margin-left: 2%;
	margin-right: 16%;
	float:left;
}
.messageuser, .messagesupport {
    border: 1px solid;
    margin: 10px 0px;
    padding:15px 10px 15px 50px;
    background-repeat: no-repeat;
    background-position: 10px center;
    width:50%;
	behavior: url(/pie/PIE.htc);
}
.messageuser {
    color: #00529B;
    -moz-border-radius: 15px 15px 15px 15px;
    -webkit-border-radius: 15px 15px 15px 15px;
    background-color: #BDE5F8;
    margin-left:20%;
    border-radius:15px;
    float: left;
}
.messagesupport {
    color: #4F8A10;
    -moz-border-radius: 15px 15px 15px 15px;
    -webkit-border-radius: 15px 15px 15px 15px;
    background-color: #DFF2BF;
    margin-right: 20%;
    float: right;
    border-radius:15px;
}
.attachment-link {
    margin: 12px;
}
#link-more-attach {
    color: white;
    cursor: pointer;
    width: 120px;
}
</style>';

$ticket_id = $_GET['ticket_id'];
$ticket = TicketManager::get_ticket_detail_by_id($ticket_id);
if (!isset($ticket['ticket'])) {
    api_not_allowed();
}
if (!isset($_GET['ticket_id'])) {
    header('Location: '.api_get_path(WEB_CODE_PATH).'ticket/tickets.php');
    exit;
}

if (isset($_POST['response'])) {
    if ($user_id == $ticket['ticket']['assigned_last_user'] || api_is_platform_admin()) {
        $response = $_POST['response'] === '1' ? true : false;
        $newStatus = TicketManager::STATUS_PENDING;
        if ($response) {
            $newStatus = TicketManager::STATUS_CLOSE;
        }
        TicketManager::update_ticket_status(
            TicketManager::getStatusIdFromCode($newStatus),
            $ticket_id,
            $user_id
        );
        Display::addFlash(Display::return_message(get_lang('Updated')));
        header("Location:" . api_get_self() . "?ticket_id=" . $ticket_id);
        exit;

    }
}
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    switch ($action) {
        case 'assign':
            if (api_is_platform_admin() && isset($_GET['ticket_id'])) {
                TicketManager::assign_ticket_user($_GET['ticket_id'], $_POST['admins']);
            }
            Display::addFlash(Display::return_message(get_lang('Updated')));
            header("Location:" . api_get_self() . "?ticket_id=" . $ticket_id);
            exit;
            break;
        case 'unassign':
            if (api_is_platform_admin() && isset($_GET['ticket_id'])) {
                TicketManager::assign_ticket_user($_GET['ticket_id'], 0);
            }
            Display::addFlash(Display::return_message(get_lang('Updated')));
            header("Location:" . api_get_self() . "?ticket_id=" . $ticket_id);
            exit;
            break;
        default:
            break;
    }
}

$title = 'Ticket #' . $ticket['ticket']['code'];

if (!isset($_POST['compose'])) {
    if (isset($_REQUEST['close'])) {
        TicketManager::close_ticket($_REQUEST['ticket_id'], $user_id);
        $ticket['ticket']['status_id'] = TicketManager::STATUS_CLOSE;
        $ticket['ticket']['status'] = get_lang('Closed');
    }

    Display::display_header();
    $form_close_ticket = '';
        if ($ticket['ticket']['status_id'] != TicketManager::STATUS_FORWARDED &&
            $ticket['ticket']['status_id'] != TicketManager::STATUS_CLOSE &&
            $isAdmin
        ) {
        /*if (intval($ticket['ticket']['assigned_last_user']) == $user_id) {
            if ($ticket['ticket']['status_id'] != TicketManager::STATUS_CLOSE) {
                $form_close_ticket.= '<a href="' . api_get_self() . '?close=1&ticket_id=' . $ticket['ticket']['id'] . '" id="close" class="btn btn-danger" >';
                $form_close_ticket.= get_lang('Close') . '</a>';
            }
        }*/
    }

    $img_assing = '';
    if (empty($ticket['ticket']['assigned_last_user'])) {
        if ($isAdmin) {
            $img_assing = '<a href="#" id="assign" class="btn btn-success">'.get_lang('Assign').'</a>';
        }
    } else {
        if ($isAdmin) {
            $img_assing = '<a class="btn btn-warning" href="#" id="assign">
                   '.get_lang('ChangeAssign').'
                   </a>';
        }
    }
    $bold = '';

    if ($ticket['ticket']['status_id'] == TicketManager::STATUS_CLOSE) {
        $bold = 'style = "font-weight: bold;"';
        echo "<style>
                #confirmticket {
                    display: none;
                }
              </style>";
    }
    if ($isAdmin) {
        $senderData = get_lang('AddedBy'). ' '.$ticket['ticket']['user_url'].' (' . $ticket['usuario']['username'] . ').';
    } else {
        $senderData = get_lang('AddedBy'). ' '.$ticket['usuario']['complete_name'].' (' . $ticket['usuario']['username']. ').';
    }

    echo '<table width="100%" >
            <tr>
              <td colspan="3">
              <h1>'.$title.' '.$form_close_ticket.'</h1>
              <h2>'.$ticket['ticket']['subject'].'</h2>
              <p>
                '.$senderData.' ' .
                get_lang('Created') . ' '.
                Display::url(
                    date_to_str_ago($ticket['ticket']['start_date_from_db']),
                    '#',
                    ['title' => $ticket['ticket']['start_date'], 'class' => 'boot-tooltip']
                ).'. '.
                get_lang('TicketUpdated').' '.
                Display::url(
                    date_to_str_ago($ticket['ticket']['sys_lastedit_datetime_from_db']),
                    '#',
                    ['title' => $ticket['ticket']['sys_lastedit_datetime'], 'class' => 'boot-tooltip']
                ).'
              </p>
              </td>
            </tr>
            <tr>
               <td><p><b>' . get_lang('Category') . ': </b>' . $ticket['ticket']['name'] . '</p></td>
            </tr>
            <tr>
               <td><p ' . $bold . '><b>' . get_lang('Status') . ':</b> ' . $ticket['ticket']['status'] . '</p></td>
            </tr>
            <tr>
                <td><p><b>' . get_lang('Priority') . ': </b>' . $ticket['ticket']['priority'] . '<p></td>
            </tr>';

    if (!empty($ticket['ticket']['assigned_last_user'])) {
        $assignedUser = api_get_user_info($ticket['ticket']['assigned_last_user']);
        echo '<tr>
                <td><p><b>' . get_lang('AssignedTo') . ': </b>' . $assignedUser['complete_name'] . '<p></td>
            </tr>';
    } else {
        echo '<tr>
                <td><p><b>' . get_lang('AssignedTo') . ': </b>-<p></td>
            </tr>';
    }
    if ($ticket['ticket']['course_url'] != null) {
        if (!empty($ticket['ticket']['session_id'])) {
            $sessionInfo = api_get_session_info($ticket['ticket']['session_id']);
            echo '<tr>
				<td><b>' . get_lang('Session') . ':</b> ' . $sessionInfo['name'] . ' </td>
			    <td></td>
	            <td colspan="2"></td>
	          </tr>';
        }

        echo '<tr>
				<td><b>' . get_lang('Course') . ':</b> ' . $ticket['ticket']['course_url'] . ' </td>
			    <td></td>
	            <td colspan="2"></td>
	          </tr>';
    }
    echo '<tr>
            <td>
            <hr />
            <b>' . get_lang('Description') . ':</b> <br />
            '.$ticket['ticket']['message'].'
            <hr />
            </td>            
         </tr>
        ';
    echo '</table>';
    $messages = $ticket['messages'];

    $logs = TicketManager::get_assign_log($ticket_id);
    $counter = 1;
    foreach ($messages as $message) {
        $date = Display::url(
            date_to_str_ago($message['sys_insert_datetime']),
            '#',
            ['title' => api_get_local_time($message['sys_insert_datetime']), 'class' => 'boot-tooltip']
        );

        $receivedMessage = '';
        if (!empty($message['subject'])) {
            $receivedMessage = '<b>'.get_lang('Subject') . ': </b> '.$message['subject'].'<br/>';
        }
        $receivedMessage = '<b>'.get_lang('Message') . ':</b><br/>'.$message['message'].'<br/>';

        $attachmentLinks = '';
        if (isset($message['attachments'])) {
            $attributeClass = array(
                'class' => 'attachment-link'
            );
            foreach ($message['attachments'] as $attach) {
                $attachmentLinks .= Display::tag('div', $attach['attachment_link'], $attributeClass);
            }
        }

        $entireMessage = $receivedMessage . $attachmentLinks;
        $counterLink = Display::url('#'.$counter, api_get_self().'?ticket_id='.$ticket_id.'#note-'.$counter);
        echo '<a id="note-'.$counter.'"> </a><h4>' . sprintf(get_lang('UpdatedByX'), $message['user_created']).' '.$date.
            ' <span class="pull-right">'.$counterLink.'</span></h4>';
        echo Display::div(
            $entireMessage,
            ['class' => 'well']
        );
        $counter++;
    }

    $subject = get_lang('ReplyShort') .': '.$ticket['ticket']['subject'];

    if ($ticket['ticket']['status_id'] != TicketManager::STATUS_FORWARDED &&
        $ticket['ticket']['status_id'] != TicketManager::STATUS_CLOSE
    ) {
        if (!$isAdmin && $ticket['ticket']['status_id'] != TicketManager::STATUS_UNCONFIRMED) {
            show_form_send_message($ticket['ticket']);
        } else {
            if (
                $ticket['ticket']['assigned_last_user'] == $user_id ||
                $ticket['ticket']['sys_insert_user_id'] == $user_id ||
                $isAdmin
            ) {
                show_form_send_message($ticket['ticket']);
            }
        }
    }

    Display::display_footer();
} else {
    $ticket_id = $_POST['ticket_id'];
    $content = $_POST['content'];
    $subject = $_POST['subject'];
    $message = isset($_POST['confirmation']) ? true : false;
    $file_attachments = $_FILES;
    $user_id = api_get_user_id();

    TicketManager::insert_message(
        $ticket_id,
        $subject,
        $content,
        $file_attachments,
        $user_id,
        'NOL',
        $message
    );

    if ($isAdmin) {
        TicketManager::updateTicket(
            [
                'priority_id' => $_POST['priority_id'],
                'status_id' => $_POST['status_id']
            ],
            $ticket_id,
            api_get_user_id()
        );

        if (isset($_POST['assigned_last_user']) && !empty($_POST['assigned_last_user'])) {
            TicketManager::assign_ticket_user($ticket_id, $_POST['assigned_last_user']);
        }
    }
    Display::addFlash(Display::return_message(get_lang('Saved')));
    header("Location:" . api_get_self() . "?ticket_id=" . $ticket_id);
    exit;
}

/**
 * @param array $ticket
 */
function show_form_send_message($ticket)
{
    global $isAdmin;
    global $subject;

    $form = new FormValidator(
        'send_ticket',
        'POST',
        api_get_self() . '?ticket_id=' . $ticket['id'],
        '',
        array(
            'enctype' => 'multipart/form-data',
            'onsubmit' => 'return validate()',
            'class' => 'form-horizontal'
        )
    );

    if ($isAdmin) {
        $statusList = TicketManager::getStatusList();
        $form->addElement(
            'select',
            'status_id',
            get_lang('Status'),
            $statusList
        );

        $priorityList = TicketManager::getPriorityList();
        $form->addElement(
            'select',
            'priority_id',
            get_lang('Priority'),
            $priorityList,
            array(
                'id' => 'priority_id',
                'for' => 'priority_id'
            )
        );

        $admins = UserManager::get_user_list_like(
            array('status' => COURSEMANAGER), array('username'),
            true
        );

        $adminList = ['' => get_lang('Select')];
        foreach ($admins as $admin) {
            $adminList[$admin['user_id']] = $admin['complete_name'];
        }

        $form->addElement(
            'select',
            'assigned_last_user',
            get_lang('Assign'),
            $adminList,
            array(
            'id' => 'assigned_last_user',
            'for' => 'assigned_last_user'
            )
        );

        $form->setDefaults(
            [
                'priority_id' =>  $ticket['priority_id'],
                'status_id' =>  $ticket['status_id'],
                'assigned_last_user' => $ticket['assigned_last_user']
            ]
        );
    }

    $form->addElement(
        'text',
        'subject',
        get_lang('Subject'),
        array(
            'for' => 'subject',
            'value' => $subject,
            'style' => 'width: 540px;'
        )
    );

    $form->addElement('hidden', 'ticket_id', $ticket['id']);

    $form->addHtmlEditor(
        'content',
        get_lang('Message'),
        false,
        false,
        array(
            'ToolbarSet' => 'Profile',
            'Width' => '550',
            'Height' => '250'
        )
    );

    if ($isAdmin) {
        $form->addElement(
            'checkbox',
            'confirmation',
             null,
            get_lang('RequestConfirmation')
        );
    }

    $form->addElement('file', 'attach_1', get_lang('FilesAttachment'));
    $form->addLabel('', '<span id="filepaths"><div id="filepath_1"></div></span>');
    $form->addLabel('',
        '<span id="link-more-attach">
         <span class="btn btn-success" onclick="return add_image_form()">' . get_lang('AddOneMoreFile') . '</span>
         </span>
         ('.sprintf(get_lang('MaximunFileSizeX'), format_file_size(api_get_setting('message_max_upload_filesize'))).')
    ');

    $form->addElement('html', '<br/>');
    $form->addElement(
        'button',
        'compose',
        get_lang('SendMessage'),
        null,
        null,
        null,
        'btn btn-primary'
    );

    $form->display();
}
