<?php
/* For licensing terms, see /license.txt */

/**
 * BLOG HOMEPAGE
 * This file takes care of all blog navigation and displaying.
 * @package chamilo.blogs
 */

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_BLOGS;

$this_section = SECTION_COURSES;

$blog_table_attachment = Database::get_course_table(TABLE_BLOGS_ATTACHMENT);

/* 		ACCESS RIGHTS	 */
// notice for unauthorized people.
api_protect_course_script(true);

//	 ONLY USERS REGISTERED IN THE COURSE
if ((!$is_allowed_in_course || !$is_courseMember) && !api_is_allowed_to_edit()) {
    api_not_allowed(true);//print headers/footers
}

if (api_is_allowed_to_edit()) {
    $nameTools = get_lang("blog_management");

    // showing the header if we are not in the learning path, if we are in
    // the learning path, we do not include the banner so we have to explicitly
    // include the stylesheet, which is normally done in the header
    if (empty($_GET['origin']) || $_GET['origin'] != 'learnpath') {
        $interbreadcrumb[]= array ('url' => 'blog_admin.php?','name' => $nameTools);
        $my_url='';
        if (isset($_GET['action']) && $_GET['action']=='add') {
            $current_section = get_lang('AddBlog');
            $my_url='action=add';
        } elseif (isset($_GET['action']) && $_GET['action']=='edit') {
            $current_section = get_lang('EditBlog');
            $my_url='action=edit&amp;blog_id='.Security::remove_XSS($_GET['blog_id']);
        }
        Display::display_header('');
    }
    echo '<div class="actions">';
    echo "<a href='".api_get_self()."?".api_get_cidreq()."&action=add'>",
        Display::return_icon('new_blog.png', get_lang('AddBlog'),'',ICON_SIZE_MEDIUM)."</a>";
    echo '</div>';

    if (!empty($_POST['new_blog_submit']) && !empty($_POST['blog_name'])) {
        if (isset($_POST['blog_name']))  {
            Blog::create_blog($_POST['blog_name'], $_POST['blog_subtitle']);
            Display::display_confirmation_message(get_lang('BlogStored'));
        }
    }
    if (!empty($_POST['edit_blog_submit']) && !empty($_POST['blog_name'])) {
        if (strlen(trim($_POST['blog_name']))>0) {
            Blog::edit_blog($_POST['blog_id'], $_POST['blog_name'], $_POST['blog_subtitle']);
            Display::display_confirmation_message(get_lang('BlogEdited'));
        }
    }
    if (isset($_GET['action']) && $_GET['action'] == 'visibility') {
        Blog::change_blog_visibility(intval($_GET['blog_id']));
        Display::display_confirmation_message(get_lang('VisibilityChanged'));
    }
    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        Blog::delete_blog(intval($_GET['blog_id']));
        Display::display_confirmation_message(get_lang('BlogDeleted'));
    }

    if (isset($_GET['action']) && $_GET['action'] == 'add') {
        // we show the form if
        // 1. no post data
        // 2. there is post data and one of the required form elements is empty
        if (!$_POST || (!empty($_POST) && (empty($_POST['new_blog_submit']) || empty($_POST['blog_name'])))) {
            Blog::display_new_blog_form();
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'edit') {
        // we show the form if
        // 1. no post data
        // 2. there is post data and one of the three form elements is empty
        if (!$_POST || (!empty($_POST) && (empty($_POST['edit_blog_submit']) || empty($_POST['blog_name']) ))) {
            // if there is post data there is certainly an error in the form
            if ($_POST) {
                Display::display_error_message(get_lang('FormHasErrorsPleaseComplete'));
            }
            Blog::display_edit_blog_form(intval($_GET['blog_id']));
        }
    }
    Blog::display_blog_list();
} else {
    api_not_allowed(true);
}

// Display the footer
Display::display_footer();
