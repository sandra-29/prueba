<?php
/* For licensing terms, see /license.txt */

/**
 *  @package chamilo.admin
 */
$cidReset = true;

require_once '../inc/global.inc.php';

$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

if (api_get_setting('allow_skills_tool') != 'true') {
    api_not_allowed();
}

//Adds the JS needed to use the jqgrid
$htmlHeadXtra[] = api_get_js('jquery.jsPlumb.all.js');
$htmlHeadXtra[] = api_get_js('jqueryui-touch-punch/jquery.ui.touch-punch.min.js');
$htmlHeadXtra[] = api_get_js('skills.js');

$skill = new Skill();
$type = 'edit'; //edit
$tree = $skill->get_skills_tree(null, null, true);
$skill_visualizer = new SkillVisualizer($tree, $type);

$html = $skill_visualizer->return_html();
$url = api_get_path(WEB_AJAX_PATH).'skill.ajax.php?1=1';

$tpl = new Template(null, false, false);

$tpl->assign('url', $url);
$tpl->assign('html', $html);
$tpl->assign('skill_visualizer', $skill_visualizer);
$tpl->assign('js', $skill_visualizer->return_js());
$templateName = $tpl->get_template('skill/skill_tree.tpl');
$content = $tpl->fetch($templateName);
$tpl->assign('content', $content);
$tpl->display_no_layout_template();
