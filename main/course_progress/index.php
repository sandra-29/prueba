<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 * Template (front controller in MVC pattern) used for distpaching to the controllers depend on the current action
 * @author Christian Fasanando <christian1827@gmail.com>
 * @author Julio Montoya <gugli100@gmail.com> Bugfixes session support
 * @package chamilo.course_progress
 */

// including files
require_once '../inc/global.inc.php';
require_once 'thematic_controller.php';

// current section
$this_section = SECTION_COURSES;

$current_course_tool = TOOL_COURSE_PROGRESS;

// protect a course script
api_protect_course_script(true);

// get actions
$actions = array(
    'thematic_details',
    'thematic_list',
    'thematic_add',
    'thematic_edit',
    'thematic_copy',
    'thematic_delete',
    'moveup',
    'movedown',
    'thematic_import_select',
    'thematic_import',
    'thematic_export',
    'thematic_export_pdf',
    'thematic_plan_list',
    'thematic_plan_add',
    'thematic_plan_edit',
    'thematic_plan_delete',
    'thematic_advance_list',
    'thematic_advance_add',
    'thematic_advance_edit',
    'thematic_advance_delete'
);

$action  = 'thematic_details';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], $actions)) {
    $action = $_REQUEST['action'];
}

if (isset($_POST['action']) && $_POST['action'] == 'thematic_delete_select') {
    $action = 'thematic_delete_select';
}

if (isset($_GET['isStudentView']) && $_GET['isStudentView'] == 'true') {
    $action = 'thematic_details';
}

if ($action == 'thematic_details' || $action == 'thematic_list') {
    Session::write('thematic_control', $action);
}

// get thematic id
$thematic_id = isset($_GET['thematic_id']) ? (int) $_GET['thematic_id'] : 0;

// get thematic plan description type
$description_type = isset($_GET['description_type']) ? (int) $_GET['description_type'] : 0;

// instance thematic object for using like library here
$thematic = new Thematic();

// thematic controller object
$thematic_controller = new ThematicController();
$thematic_data = [];
if (!empty($thematic_id)) {
    // thematic data by id
    $thematic_data = $thematic->get_thematic_list($thematic_id);
}

// get default thematic plan title
$default_thematic_plan_title = $thematic->get_default_thematic_plan_title();

// Only when I see the 3 columns. Avoids double or triple click binding for onclick event

$htmlHeadXtra[] = '<script>
$(document).ready(function() {
    $(".thematic_advance_actions, .thematic_tools ").hide();
	$(".thematic_content").mouseover(function() {
		var id = parseInt(this.id.split("_")[3]);
		$("#thematic_id_content_"+id ).show();
	});

	$(".thematic_content").mouseleave(function() {
		var id = parseInt(this.id.split("_")[3]);
		$("#thematic_id_content_"+id ).hide();
	});

	$(".thematic_advance_content").mouseover(function() {
		var id = parseInt(this.id.split("_")[4]);
		$("#thematic_advance_tools_"+id ).show();
	});

	$(".thematic_advance_content").mouseleave(function() {
		var id = parseInt(this.id.split("_")[4]);
		$("#thematic_advance_tools_"+id ).hide();
	});
});
</script>';

$htmlHeadXtra[] = '<script>

function datetime_by_attendance(attendance_id, thematic_advance_id) {
	$.ajax({
		contentType: "application/x-www-form-urlencoded",
		beforeSend: function(objeto) {},
		type: "GET",
		url: "'.api_get_path(WEB_AJAX_PATH).'thematic.ajax.php?a=get_datetime_by_attendance",
		data: "attendance_id="+attendance_id+"&thematic_advance_id="+thematic_advance_id,
		success: function(data) {
			$("#div_datetime_attendance").html(data);
            if (thematic_advance_id == 0) {
                $("#start_date_select_calendar").val($("#start_date_select_calendar option:first").val());
            }
		}
	});
}

function update_done_thematic_advance(selected_value) {
	$.ajax({
		contentType: "application/x-www-form-urlencoded",
		beforeSend: function(objeto) {},
		type: "GET",
		url: "'.api_get_path(WEB_AJAX_PATH).'thematic.ajax.php?a=update_done_thematic_advance",
		data: "thematic_advance_id="+selected_value,
		success: function(data) {
			$("#div_result").html(data);
		}
	});

	// clean all radios
	for (var i=0; i< $(".done_thematic").length;i++) {
		var id_radio_thematic = $(".done_thematic").get(i).id;
		$("#td_"+id_radio_thematic).css({"background-color":"#FFF"});
	}

	// set background to previous radios
	for (var i=0; i < $(".done_thematic").length;i++) {
		var id_radio_thematic = $(".done_thematic").get(i).id;
		$("#td_"+id_radio_thematic).css({"background-color":"#E5EDF9"});
		if ($(".done_thematic").get(i).value == selected_value) {
			break;
		}
	}
}

function check_per_attendance(obj) {
	if (obj.checked) {
        $("#div_datetime_by_attendance").show();
        $("#div_custom_datetime").hide();
	} else {
        $("#div_datetime_by_attendance").hide();
        $("#div_custom_datetime").show();
	}
}

function check_per_custom_date(obj) {
	if (obj.checked) {
        $("#div_custom_datetime").show();
        $("#div_datetime_by_attendance").hide();
	} else {
        $("#div_custom_datetime").hide();
        $("#div_datetime_by_attendance").show();
	}
}
</script>';

$thematicControl = Session::read('thematic_control');

if ($action == 'thematic_list') {
    $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('ThematicControl'));
}
if ($action == 'thematic_add') {
    $interbreadcrumb[] = array(
        'url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl,
        'name' => get_lang('ThematicControl')
    );
    $interbreadcrumb[] = array('url' => '#', 'name' => get_lang('NewThematicSection'));
}
if ($action == 'thematic_edit') {
    $interbreadcrumb[] = array(
        'url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl,
        'name' => get_lang('ThematicControl')
    );
    $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('EditThematicSection'));
}
if ($action == 'thematic_details') {
    $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('ThematicControl'));
}
if ($action == 'thematic_plan_list' || $action == 'thematic_plan_delete') {
    $interbreadcrumb[] = array(
        'url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl,
        'name' => get_lang('ThematicControl')
    );
    if (!empty($thematic_data)) {
        $interbreadcrumb[] = array(
            'url' => '#',
            'name' => get_lang('ThematicPlan').' ('.$thematic_data['title'].') '
        );
    }
}
if ($action == 'thematic_plan_add' || $action == 'thematic_plan_edit') {
    $interbreadcrumb[] = array ('url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl, 'name' => get_lang('ThematicControl'));
    $interbreadcrumb[] = array ('url' => 'index.php?'.api_get_cidreq().'&action=thematic_plan_list&thematic_id='.$thematic_id, 'name' => get_lang('ThematicPlan').' ('.$thematic_data['title'].')');
    if ($description_type >= ADD_THEMATIC_PLAN) {
        $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('NewBloc'));
    } else {
        $interbreadcrumb[] = array ('url' => '#', 'name' => $default_thematic_plan_title[$description_type]);
    }
}
if ($action == 'thematic_advance_list' || $action == 'thematic_advance_delete') {
    $interbreadcrumb[] = array ('url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl, 'name' => get_lang('ThematicControl'));
    $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('ThematicAdvance').' ('.$thematic_data['title'].')');
}
if ($action == 'thematic_advance_add' || $action == 'thematic_advance_edit') {
    $interbreadcrumb[] = array ('url' => 'index.php?'.api_get_cidreq().'&action='.$thematicControl, 'name' => get_lang('ThematicControl'));
    $interbreadcrumb[] = array ('url' => 'index.php?'.api_get_cidreq().'&action=thematic_advance_list&thematic_id='.$thematic_id, 'name' => get_lang('ThematicAdvance').' ('.$thematic_data['title'].')');
    $interbreadcrumb[] = array ('url' => '#', 'name' => get_lang('NewThematicAdvance'));
}

// Distpacher actions to controller
switch ($action) {
    case 'thematic_add':
    case 'thematic_edit':
    case 'thematic_delete':
    case 'thematic_delete_select':
    case 'thematic_copy':
    case 'thematic_import_select':
    case 'thematic_import':
    case 'moveup':
    case 'movedown':
        if (!api_is_allowed_to_edit(null, true)) {
            api_not_allowed();
        }
    case 'thematic_list':
    case 'thematic_export':
    case 'thematic_export_pdf':
    case 'thematic_details':
        $thematic_controller->thematic($action);
        break;
    case 'thematic_plan_add':
    case 'thematic_plan_edit':
    case 'thematic_plan_delete':
        if (!api_is_allowed_to_edit(null, true)) {
            api_not_allowed();
        }
    case 'thematic_plan_list':
        $thematic_controller->thematic_plan($action);
        break;
    case 'thematic_advance_add':
    case 'thematic_advance_edit':
    case 'thematic_advance_delete':
        if (!api_is_allowed_to_edit(null, true)) {
            api_not_allowed();
        }
    case 'thematic_advance_list':
        $thematic_controller->thematic_advance($action);
        break;
}
