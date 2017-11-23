<?php
/* For licensing terms, see /license.txt */

/**
 *	This file is responsible for  passing requested documents to the browser.
 *	Html files are parsed to fix a few problems with URLs,
 *	but this code will hopefully be replaced soon by an Apache URL
 *	rewrite mechanism.
 *
 *	@package chamilo.work
 */
require_once '../inc/global.inc.php';
require_once 'work.lib.php';

$current_course_tool  = TOOL_STUDENTPUBLICATION;
$this_section = SECTION_COURSES;

// Course protection
api_protect_course_script(true);

$id = intval($_GET['id']);

$course_info = api_get_course_info();

if (empty($course_info)) {
    api_not_allowed(true);
}

$result = getFile($id, $course_info, false);

if ($result == false) {
    api_not_allowed();
}
