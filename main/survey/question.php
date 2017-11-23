<?php
/* For licensing terms, see /license.txt */

/**
*	@package chamilo.survey
* 	@author unknown, the initial survey that did not make it in 1.8 because of bad code
* 	@author Patrick Cool <patrick.cool@UGent.be>, Ghent University: cleanup, refactoring and rewriting large parts of the code
* 	@version $Id: question.php 21734 2009-07-02 17:12:41Z cvargas1 $
*/

require_once '../inc/global.inc.php';

$htmlHeadXtra[] = '<script>
$(document).ready( function() {
    $("button").click(function() {
        $("#is_executable").attr("value",$(this).attr("name"));
    });
} ); </script>';

/** @todo this has to be moved to a more appropriate place (after the display_header of the code)*/
if (!api_is_allowed_to_edit(false, true)) {
	Display :: display_header();
	Display :: display_error_message(get_lang('NotAllowed'), false);
	Display :: display_footer();
	exit;
}

// Is valid request
$is_valid_request = isset($_REQUEST['is_executable']) ? $_REQUEST['is_executable'] : null;

// Database table definitions
$table_survey = Database:: get_course_table(TABLE_SURVEY);
$table_survey_question = Database:: get_course_table(TABLE_SURVEY_QUESTION);
$table_survey_question_option = Database:: get_course_table(TABLE_SURVEY_QUESTION_OPTION);

$table_course = Database:: get_main_table(TABLE_MAIN_COURSE);
$table_user = Database:: get_main_table(TABLE_MAIN_USER);

$course_id = api_get_course_int_id();

// Getting the survey information
$surveyData = SurveyManager::get_survey($_GET['survey_id']);

if (empty($surveyData)) {
	Display :: display_header(get_lang('ToolSurvey'));
	Display :: display_error_message(get_lang('InvallidSurvey'), false);
	Display :: display_footer();
	exit;
}

$urlname = api_substr(api_html_entity_decode($surveyData['title'], ENT_QUOTES), 0, 40);
if (api_strlen(strip_tags($surveyData['title'])) > 40) {
	$urlname .= '...';
}

if ($surveyData['survey_type'] == 1) {
	$sql = 'SELECT id FROM '.Database :: get_course_table(TABLE_SURVEY_QUESTION_GROUP).'
	        WHERE
                c_id = '.$course_id.' AND
                survey_id = '.(int)$_GET['survey_id'].' LIMIT 1';
	$rs = Database::query($sql);
	if (Database::num_rows($rs)===0) {
        Display::addFlash(
            Display::return_message(get_lang('YouNeedToCreateGroups'))
        );
		header('Location: '.api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.(int)$_GET['survey_id']);
		exit;
	}
}

// Breadcrumbs
$interbreadcrumb[] = array(
    'url' => api_get_path(WEB_CODE_PATH).'survey/survey_list.php',
    'name' => get_lang('SurveyList'),
);
$interbreadcrumb[] = array(
    'url' => api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.intval($_GET['survey_id']),
    'name' => strip_tags($urlname),
);

// Tool name
if ($_GET['action'] == 'add') {
	$tool_name = get_lang('AddQuestion');
}
if ($_GET['action'] == 'edit') {
	$tool_name = get_lang('EditQuestion');
}

// The possible question types
$possible_types = array(
    'personality',
    'yesno',
    'multiplechoice',
    'multipleresponse',
    'open',
    'dropdown',
    'comment',
    'pagebreak',
    'percentage',
    'score'
);

// Actions
$actions = '<div class="actions">';
$actions .= '<a href="'.api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.intval($_GET['survey_id']).'">'.
	Display::return_icon('back.png', get_lang('BackToSurvey'),'',ICON_SIZE_MEDIUM).'</a>';
$actions .= '</div>';
// Checking if it is a valid type
if (!in_array($_GET['type'], $possible_types)) {
	Display :: display_header($tool_name, 'Survey');
	echo $actions;
	Display :: display_error_message(get_lang('TypeDoesNotExist'), false);
	Display :: display_footer();
}

$error_message = '';

// Displaying the form for adding or editing the question

$ch_type = 'ch_'.$_GET['type'];
/** @var survey_question $surveyQuestion */
$surveyQuestion = new $ch_type;

// The defaults values for the form
$formData = array();
$formData['answers'] = array('', '');

if ($_GET['type'] == 'yesno') {
	$formData['answers'][0] = get_lang('Yes');
	$formData['answers'][1] = get_lang('No');
}

if ($_GET['type'] == 'personality') {
	$formData['answers'][0] = 1;
	$formData['answers'][1] = 2;
	$formData['answers'][2] = 3;
	$formData['answers'][3] = 4;
	$formData['answers'][4] = 5;

	$formData['values'][0] = 0;
	$formData['values'][1] = 0;
	$formData['values'][2] = 1;
	$formData['values'][3] = 2;
	$formData['values'][4] = 3;
}

// We are editing a question
if (isset($_GET['question_id']) && !empty($_GET['question_id'])) {
	$formData = SurveyManager::get_question($_GET['question_id']);
}

$formData = $surveyQuestion->preSave($formData);
$surveyQuestion->createForm($surveyData, $formData);
$surveyQuestion->getForm()->setDefaults($formData);
$surveyQuestion->renderForm();

if ($surveyQuestion->getForm()->validate()) {
	$values = $surveyQuestion->getForm()->getSubmitValues();
	$surveyQuestion->save($surveyData, $values);
}

Display::display_header($tool_name, 'Survey');

echo $surveyQuestion->getForm()->returnForm();

Display :: display_footer();
