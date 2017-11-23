<?php
/* For licensing terms, see /license.txt */

/**
 *	@package chamilo.admin
 */
$cidReset = true;
require_once '../inc/global.inc.php';
$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

$tool_name = get_lang('AddCourse');
$interbreadcrumb[] = array('url' => 'index.php', 'name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => 'course_list.php', 'name' => get_lang('CourseList'));


// Get all possible teachers.
$order_clause = api_sort_by_first_name() ? ' ORDER BY firstname, lastname' : ' ORDER BY lastname, firstname';
$table_user = Database :: get_main_table(TABLE_MAIN_USER);
$sql = "SELECT user_id,lastname,firstname
        FROM $table_user
        WHERE status=1".$order_clause;
// Filtering teachers when creating a course.
if (api_is_multiple_url_enabled()) {
    $access_url_rel_user_table= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
    $sql = "SELECT u.user_id,lastname,firstname
            FROM $table_user as u
            INNER JOIN $access_url_rel_user_table url_rel_user
            ON (u.user_id=url_rel_user.user_id)
            WHERE url_rel_user.access_url_id=".api_get_current_access_url_id()." AND status=1".$order_clause;
}

$res = Database::query($sql);
$teachers = array();
while ($obj = Database::fetch_object($res)) {
    $teachers[$obj->user_id] = api_get_person_name($obj->firstname, $obj->lastname);
}

// Build the form.
$form = new FormValidator('update_course');
$form->addElement('header', $tool_name);

// Title
$form->addText('title', get_lang('Title'), true);
$form->applyFilter('title', 'html_filter');
$form->applyFilter('title', 'trim');

// Code
$form->addText(
    'visual_code',
    array(
        get_lang('CourseCode'),
        get_lang('OnlyLettersAndNumbers')
    ),
    false,
    [
        'maxlength' => CourseManager::MAX_COURSE_LENGTH_CODE,
        'pattern' => '[a-zA-Z0-9]+',
        'title' => get_lang('OnlyLettersAndNumbers')
    ]
);

$form->applyFilter('visual_code', 'api_strtoupper');
$form->applyFilter('visual_code', 'html_filter');
$form->addRule('visual_code', get_lang('Max'), 'maxlength', CourseManager::MAX_COURSE_LENGTH_CODE);

$form->addElement(
    'select',
    'course_teachers',
    get_lang('CourseTeachers'),
    $teachers,
    [
        'id' => 'course_teachers',
        'multiple' => 'multiple'
    ]
);
$form->applyFilter('course_teachers', 'html_filter');

// Category code
$url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=search_category';

$form->addElement(
    'select_ajax',
    'category_code',
    get_lang('CourseFaculty'),
    null,
    array(
        'url' => $url
    //    'formatResult' => 'function(item) { return item.name + "'" +item.code; }'
    )
);

// Course department
$form->addText('department_name', get_lang('CourseDepartment'), false, array ('size' => '60'));
$form->applyFilter('department_name', 'html_filter');
$form->applyFilter('department_name', 'trim');

// Department URL
$form->addText('department_url', get_lang('CourseDepartmentURL'), false, array ('size' => '60'));
$form->applyFilter('department_url', 'html_filter');

// Course language.
$languages = api_get_languages();
if (count($languages['name']) === 1) {
    // If there's only one language available, there's no point in asking
    $form->addElement('hidden', 'course_language', $languages['folder'][0]);
} else {
    $form->addElement(
        'select_language',
        'course_language',
        get_lang('Ln'),
        array(),
        array('style' => 'width:150px')
    );
    $form->applyFilter('select_language', 'html_filter');
}

if (api_get_setting('teacher_can_select_course_template') === 'true') {
    $form->addElement(
        'select_ajax',
        'course_template',
        [
            get_lang('CourseTemplate'),
            get_lang('PickACourseAsATemplateForThisNewCourse'),
        ],
        null,
        ['url' => api_get_path(WEB_AJAX_PATH) . 'course.ajax.php?a=search_course']
    );
}

$form->addElement('checkbox', 'exemplary_content', '', get_lang('FillWithExemplaryContent'));

$group = array();
$group[]= $form->createElement('radio', 'visibility', get_lang('CourseAccess'), get_lang('OpenToTheWorld'), COURSE_VISIBILITY_OPEN_WORLD);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('OpenToThePlatform'), COURSE_VISIBILITY_OPEN_PLATFORM);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('Private'), COURSE_VISIBILITY_REGISTERED);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('CourseVisibilityClosed'), COURSE_VISIBILITY_CLOSED);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('CourseVisibilityHidden'), COURSE_VISIBILITY_HIDDEN);

$form->addGroup($group,'', get_lang('CourseAccess'));

$group = array();
$group[]= $form->createElement('radio', 'subscribe', get_lang('Subscription'), get_lang('Allowed'), 1);
$group[]= $form->createElement('radio', 'subscribe', null, get_lang('Denied'), 0);
$form->addGroup($group,'', get_lang('Subscription'));

$group = array();
$group[]= $form->createElement('radio', 'unsubscribe', get_lang('Unsubscription'), get_lang('AllowedToUnsubscribe'), 1);
$group[]= $form->createElement('radio', 'unsubscribe', null, get_lang('NotAllowedToUnsubscribe'), 0);
$form->addGroup($group, '', get_lang('Unsubscription'));

$form->addElement('text','disk_quota',array(get_lang('CourseQuota'), null, get_lang('MB')));
$form->addRule('disk_quota', get_lang('ThisFieldShouldBeNumeric'), 'numeric');

$obj = new GradeModel();
$obj->fill_grade_model_select_in_form($form);

//Extra fields
$extra_field = new ExtraField('course');
$extra = $extra_field->addElements($form);

$htmlHeadXtra[] ='
<script>

$(function() {
    '.$extra['jquery_ready_content'].'
});
</script>';

$form->addProgress();
$form->addButtonCreate(get_lang('CreateCourse'));

// Set some default values.
$values['course_language'] = api_get_setting('platformLanguage');
$values['disk_quota'] = round(api_get_setting('default_document_quotum')/1024/1024, 1);

$default_course_visibility = api_get_setting('courses_default_creation_visibility');

if (isset($default_course_visibility)) {
    $values['visibility'] = api_get_setting('courses_default_creation_visibility');
} else {
    $values['visibility'] = COURSE_VISIBILITY_OPEN_PLATFORM;
}
$values['subscribe'] = 1;
$values['unsubscribe'] = 0;
$values['course_teachers'] = array(api_get_user_id());

$form->setDefaults($values);

// Validate the form
if ($form->validate()) {
    $course = $form->exportValues();

    $course_teachers = isset($course['course_teachers']) ? $course['course_teachers'] : null;
    $course['disk_quota'] = $course['disk_quota']*1024*1024;
    $course['exemplary_content'] = empty($course['exemplary_content']) ? false : true;
    $course['teachers'] = $course_teachers;
    $course['wanted_code'] = $course['visual_code'];
    $course['gradebook_model_id']   = isset($course['gradebook_model_id']) ? $course['gradebook_model_id'] : null;
    // Fixing category code
    $course['course_category'] = isset($course['category_code']) ? $course['category_code'] :  '';

    include_once api_get_path(SYS_CODE_PATH) . 'lang/english/trad4all.inc.php';
    $file_to_include = api_get_path(SYS_CODE_PATH) . 'lang/' . $course['course_language'] . '/trad4all.inc.php';

    if (file_exists($file_to_include)) {
        include $file_to_include;
    }

    $course_info = CourseManager::create_course($course);

    header('Location: course_list.php');
    exit;
}

// Display the form.
$content = $form->returnForm();

$tpl = new Template($tool_name);
$tpl->assign('content', $content);
$tpl->display_one_col_template();
