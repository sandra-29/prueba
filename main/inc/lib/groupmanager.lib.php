<?php
/* For licensing terms, see /license.txt */

use Chamilo\CourseBundle\Entity\CGroupRelUser;

/**
 * This library contains some functions for group-management.
 * @author Bart Mollet
 * @package chamilo.library
 * @todo Add $course_code parameter to all functions. So this GroupManager can
 * be used outside a session.
 */
class GroupManager
{
    /* VIRTUAL_COURSE_CATEGORY:
    in this category groups are created based on the virtual course of a course*/
    const VIRTUAL_COURSE_CATEGORY = 1;

    /* DEFAULT_GROUP_CATEGORY:
    When group categories aren't available (platform-setting),
    all groups are created in this 'dummy'-category*/
    const DEFAULT_GROUP_CATEGORY = 2;

    /**
     * infinite
     */
    const INFINITE = 99999;
    /**
     * No limit on the number of users in a group
     */
    const MEMBER_PER_GROUP_NO_LIMIT = 0;
    /**
     * No limit on the number of groups per user
     */
    const GROUP_PER_MEMBER_NO_LIMIT = 0;
    /**
     * The tools of a group can have 3 states
     * - not available
     * - public
     * - private
     */
    const TOOL_NOT_AVAILABLE = 0;
    const TOOL_PUBLIC = 1;
    const TOOL_PRIVATE = 2;
    /**
     * Constants for the available group tools
     */
    const GROUP_TOOL_FORUM = 0;
    const GROUP_TOOL_DOCUMENTS = 1;
    const GROUP_TOOL_CALENDAR = 2;
    const GROUP_TOOL_ANNOUNCEMENT = 3;
    const GROUP_TOOL_WORK = 4;
    const GROUP_TOOL_WIKI = 5;
    const GROUP_TOOL_CHAT = 6;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function get_groups($courseId = null)
    {
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $courseId = !empty($courseId) ? (int) $courseId : api_get_course_int_id();

        $sql = "SELECT * FROM $table_group WHERE c_id = $courseId  ";
        $result = Database::query($sql);
        return Database::store_result($result, 'ASSOC');
    }

    /**
     * Get list of groups for current course.
     * @param int $categoryId The id of the category from which the groups are
     * requested
     * @param string $course_code Default is current course
     * @return array An array with all information about the groups.
     */
    public static function get_group_list($categoryId = null, $course_code = null, $status = null)
    {
        $course_info = api_get_course_info($course_code);
        $session_id = api_get_session_id();

        $course_id = $course_info['real_id'];
        $table_group = Database :: get_course_table(TABLE_GROUP);

        $sql = "SELECT 
                    g.id,
                    g.iid,
                    g.name,
                    g.description,
                    g.category_id,
                    g.max_student maximum_number_of_members,
                    g.secret_directory,
                    g.self_registration_allowed,
                    g.self_unregistration_allowed,
                    g.session_id,
                    g.status
                FROM $table_group g
                WHERE 1 = 1 ";

        if (!is_null($categoryId)) {
            $sql .= " AND g.category_id = '".intval($categoryId)."' ";
            $session_condition = api_get_session_condition($session_id);
            if (!empty($session_condition)) {
                $sql .= $session_condition;
            }
        } else {
            $session_condition = api_get_session_condition($session_id, true);
        }

        if (!is_null($status)) {
            $sql .= "  AND  g.status = '".intval($status)."' ";
        }

        $sql .= " AND g.c_id = $course_id ";

        if (!empty($session_condition)) {
            $sql .= $session_condition;
        }
        $sql .= "ORDER BY UPPER(g.name)";

        $groupList = Database::query($sql);

        $groups = array();
        while ($thisGroup = Database::fetch_array($groupList)) {
            $thisGroup['number_of_members'] = count(self::get_subscribed_users($thisGroup['iid']));
            if ($thisGroup['session_id'] != 0) {
                $sql = 'SELECT name FROM '.Database::get_main_table(TABLE_MAIN_SESSION).'
                        WHERE id='.$thisGroup['session_id'];
                $rs_session = Database::query($sql);
                if (Database::num_rows($rs_session) > 0) {
                    $thisGroup['session_name'] = Database::result($rs_session, 0, 0);
                }
            }
            $groups[] = $thisGroup;
        }

        return $groups;
    }

    /**
     * Create a group
     * @param string $name The name for this group
     * @param int $category_id
     * @param int $tutor The user-id of the group's tutor
     * @param int $places How many people can subscribe to the new group
     */
    public static function create_group($name, $category_id, $tutor, $places)
    {
        $_course = api_get_course_info();
        $session_id = api_get_session_id();
        $course_id  = $_course['real_id'];
        $currentCourseRepository = $_course['path'];
        $category = self :: get_category($category_id);
        $places = intval($places);

        if ($category) {
            if ($places == 0) {
                //if the amount of users per group is not filled in, use the setting from the category
                $places = $category['max_student'];
            } else {
                if ($places > $category['max_student'] && $category['max_student'] != 0) {
                    $places = $category['max_student'];
                }
            }
            $docState = $category['doc_state'];
            $calendarState = $category['calendar_state'];
            $workState = $category['work_state'];
            $anonuncementState = $category['announcements_state'];
            $forumState = $category['forum_state'];
            $wikiState = $category['wiki_state'];
            $chatState = $category['chat_state'];
            $selfRegAllowed = $category['self_reg_allowed'];
            $selfUnregAllwoed = $category['self_unreg_allowed'];

        } else {
            $docState = self::TOOL_PRIVATE;
            $calendarState = self::TOOL_PRIVATE;
            $workState = self::TOOL_PRIVATE;
            $anonuncementState = self::TOOL_PRIVATE;
            $forumState = self::TOOL_PRIVATE;
            $wikiState = self::TOOL_PRIVATE;
            $chatState = self::TOOL_PRIVATE;
            $selfRegAllowed = 0;
            $selfUnregAllwoed = 0;
        }

        $table_group = Database :: get_course_table(TABLE_GROUP);
        $sql = "INSERT INTO $table_group SET
                c_id = $course_id,
                status = 1,
                category_id='".Database::escape_string($category_id)."',
                max_student = '".$places."',
                doc_state = '".$docState."',
                calendar_state = '".$calendarState."',
                work_state = '".$workState."',
                announcements_state = '".$anonuncementState."',
                forum_state = '".$forumState."',
                wiki_state = '".$wikiState."',
                chat_state = '".$chatState."',
                self_registration_allowed = '".$selfRegAllowed."',
                self_unregistration_allowed = '".$selfUnregAllwoed."',
                session_id='".intval($session_id)."'";

        Database::query($sql);
        $lastId = Database::insert_id();

        if ($lastId) {
            $sql = "UPDATE $table_group SET id = iid WHERE iid = $lastId";
            Database::query($sql);

            $desired_dir_name= '/'.api_replace_dangerous_char($name).'_groupdocs';
            $my_path = api_get_path(SYS_COURSE_PATH) . $currentCourseRepository . '/document';

            $newFolderData = create_unexisting_directory(
                $_course,
                api_get_user_id(),
                $session_id,
                $lastId,
                null,
                $my_path,
                $desired_dir_name,
                null,
                1
            );

            $unique_name = $newFolderData['path'];

            /* Stores the directory path into the group table */
            $sql = "UPDATE $table_group SET
                        name = '".Database::escape_string($name)."',
                        secret_directory = '".$unique_name."'
                    WHERE c_id = $course_id AND id ='".$lastId."'";

            Database::query($sql);

            // create a forum if needed
            if ($forumState >= 0) {
                require_once api_get_path(SYS_CODE_PATH).'forum/forumconfig.inc.php';
                require_once api_get_path(SYS_CODE_PATH).'forum/forumfunction.inc.php';

                $forum_categories = get_forum_categories();
                if (empty($forum_categories)) {
                    $categoryParam = array(
                        'forum_category_title' => get_lang('GroupForums'),
                    );
                    store_forumcategory($categoryParam);

                    $forum_categories = get_forum_categories();
                }

                $counter = 0;
                foreach ($forum_categories as $key => $value) {
                    if ($counter == 0) {
                        $forum_category_id = $key;
                    }
                    $counter++;
                }
                // A sanity check.
                if (empty($forum_category_id)) {
                    $forum_category_id = 0;
                }

                $values = array();
                $values['forum_title'] = $name;
                $values['group_id'] = $lastId;
                $values['forum_category'] = $forum_category_id;
                $values['allow_anonymous_group']['allow_anonymous'] = 0;
                $values['students_can_edit_group']['students_can_edit'] = 0;
                $values['approval_direct_group']['approval_direct'] = 0;
                $values['allow_attachments_group']['allow_attachments'] = 1;
                $values['allow_new_threads_group']['allow_new_threads'] = 1;
                $values['default_view_type_group']['default_view_type'] = api_get_setting('default_forum_view');
                $values['group_forum'] = $lastId;
                if ($forumState == '1') {
                    $values['public_private_group_forum_group']['public_private_group_forum']='public';
                } elseif ($forumState == '2') {
                    $values['public_private_group_forum_group']['public_private_group_forum']='private';
                } elseif ($forumState == '0') {
                    $values['public_private_group_forum_group']['public_private_group_forum']='unavailable';
                }
                store_forum($values);
            }
        }

        return $lastId;
    }

    /**
     * Create subgroups.
     * This function creates new groups based on an existing group. It will
     * create the specified number of groups and fill those groups with users
     * from the base group
     * @param int $group_id The group from which subgroups have to be created.
     * @param int $number_of_groups The number of groups that have to be created
     */
    public static function create_subgroups($group_id, $number_of_groups)
    {
        $course_id = api_get_course_int_id();
        $table_group = Database::get_course_table(TABLE_GROUP);
        $category_id = self::create_category(
            get_lang('Subgroups'),
            '',
            self::TOOL_PRIVATE,
            self::TOOL_PRIVATE,
            0,
            0,
            1,
            1
        );
        $users = self::get_users($group_id);
        $group_ids = array ();

        for ($group_nr = 1; $group_nr <= $number_of_groups; $group_nr ++) {
            $group_ids[] = self::create_group(
                get_lang('Subgroup').' '.$group_nr,
                $category_id,
                0,
                0
            );
        }

        $members = array();
        foreach ($users as $index => $user_id) {
            self::subscribe_users(
                $user_id,
                $group_ids[$index % $number_of_groups]
            );
            $members[$group_ids[$index % $number_of_groups]]++;
        }

        foreach ($members as $group_id => $places) {
            $sql = "UPDATE $table_group SET max_student = $places
                    WHERE c_id = $course_id  AND id = $group_id";
            Database::query($sql);
        }
    }

    /**
     * Create a group for every class subscribed to the current course
     * @param int $category_id The category in which the groups should be created
     * @return array
     */
    public static function create_class_groups($category_id)
    {
        $options['where'] = array(" usergroup.course_id = ? " => api_get_course_int_id());
        $obj = new UserGroup();
        $classes = $obj->getUserGroupInCourse($options);
        $group_ids = array();
        foreach ($classes as $class) {
            $users_ids = $obj->get_users_by_usergroup($class['id']);
            $group_id = self::create_group(
                $class['name'],
                $category_id,
                0,
                count($users_ids)
            );
            self::subscribe_users($users_ids, $group_id);
            $group_ids[] = $group_id;
        }
        return $group_ids;
    }

    /**
     * Deletes groups and their data.
     * @author Christophe Gesche <christophe.gesche@claroline.net>
     * @author Hugues Peeters <hugues.peeters@claroline.net>
     * @author Bart Mollet
     * @param  int  $groupId iid
     * @param string $course_code Default is current course
     * @return integer              - number of groups deleted.
     */
    public static function delete_groups($groupId, $course_code = null)
    {
        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];

        // Database table definitions
        $group_table = Database:: get_course_table(TABLE_GROUP);
        $forum_table = Database:: get_course_table(TABLE_FORUM);
        $groupId = (int) $groupId;

        $sql = "SELECT id, iid, secret_directory, session_id
                FROM $group_table
                WHERE c_id = $course_id AND iid = $groupId";
        $db_result = Database::query($sql);

        $group = Database::fetch_object($db_result);
        if ($group) {
            // Unsubscribe all users
            self::unsubscribe_all_users($groupId);
            self::unsubscribe_all_tutors($groupId);
            // move group-documents to garbage
            $source_directory = api_get_path(SYS_COURSE_PATH).$course_info['path']."/document".$group->secret_directory;
            // File to renamed
            $destination_dir = api_get_path(SYS_COURSE_PATH).$course_info['path']."/document".$group->secret_directory.'_DELETED_'.$group->id;

            if (!empty($group->secret_directory)) {
                //Deleting from document tool
                DocumentManager::delete_document($course_info, $group->secret_directory, $source_directory);

                if (file_exists($source_directory)) {
                    if (api_get_setting('permanently_remove_deleted_files') === 'true') {
                        // Delete
                        my_delete($source_directory);
                    } else {
                        // Rename
                        rename($source_directory, $destination_dir);
                    }
                }
            }

            $sql = "DELETE FROM $forum_table
                    WHERE c_id = $course_id AND forum_of_group = $groupId ";
            Database::query($sql);

            // Delete item properties of this group.
            $itemPropertyTable = Database::get_course_table(TABLE_ITEM_PROPERTY);
            $sql = "DELETE FROM $itemPropertyTable
                    WHERE c_id = $course_id AND to_group_id = $groupId ";
            Database::query($sql);

            // delete the groups
            $sql = "DELETE FROM $group_table
                    WHERE c_id = $course_id AND iid = $groupId ";
            Database::query($sql);
        }

        return true;
    }

    /**
     * Get group properties
     * @param int $group_id The group from which properties are requested.
     * @return array All properties. Array-keys are:
     * name, tutor_id, description, maximum_number_of_students,
     * directory and visibility of tools
     */
    public static function get_group_properties($group_id, $useIid = false)
    {
        $course_id = api_get_course_int_id();
        if (empty($group_id) || !is_integer(intval($group_id))) {
            return null;
        }

        $table_group = Database :: get_course_table(TABLE_GROUP);
        $sql = "SELECT * FROM $table_group
                WHERE c_id = $course_id AND id = ".intval($group_id);

        if ($useIid) {
            $sql = "SELECT * FROM $table_group
                    WHERE c_id = $course_id AND iid = ".intval($group_id);
        }
        $db_result = Database::query($sql);
        $db_object = Database::fetch_object($db_result);

        $result = array();

        if ($db_object) {
            $result['id'] = $db_object->id;
            $result['iid'] = $db_object->iid;
            $result['name'] = $db_object->name;
            $result['status'] = $db_object->status;
            $result['description'] = $db_object->description;
            $result['maximum_number_of_students'] = $db_object->max_student;
            $result['max_student'] = $db_object->max_student;
            $result['doc_state'] = $db_object->doc_state;
            $result['work_state'] = $db_object->work_state;
            $result['calendar_state'] = $db_object->calendar_state;
            $result['announcements_state'] = $db_object->announcements_state;
            $result['forum_state'] = $db_object->forum_state;
            $result['wiki_state'] = $db_object->wiki_state;
            $result['chat_state'] = $db_object->chat_state;
            $result['directory'] = $db_object->secret_directory;
            $result['self_registration_allowed'] = $db_object->self_registration_allowed;
            $result['self_unregistration_allowed'] = $db_object->self_unregistration_allowed;
            $result['count_users'] = count(
                self::get_subscribed_users($db_object->iid)
            );
            $result['count_tutor'] = count(
                self::get_subscribed_tutors($db_object->iid)
            );
            $result['count_all'] = $result['count_users'] + $result['count_tutor'];
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $courseCode
     * @return array
     */
    public static function getGroupByName($name, $courseCode = null)
    {
        $name = trim($name);

        if (empty($name)) {
            return array();
        }

        $course_info = api_get_course_info($courseCode);
        $course_id = $course_info['real_id'];
        $name = Database::escape_string($name);
        $table_group = Database::get_course_table(TABLE_GROUP);
        $sql = "SELECT * FROM $table_group
                WHERE c_id = $course_id AND name = '$name'
                LIMIT 1";
        $res = Database::query($sql);
        $group = array();
        if (Database::num_rows($res)) {
            $group = Database::fetch_array($res, 'ASSOC');
        }

        return $group;
    }

    /**
     * @param int $courseId
     * @param int $categoryId
     * @param string $name
     * @return array
     */
    public static function getGroupListFilterByName($name, $categoryId, $courseId)
    {
        $name = trim($name);
        if (empty($name)) {
            return array();
        }
        $name = Database::escape_string($name);
        $courseId = intval($courseId);
        $table_group = Database::get_course_table(TABLE_GROUP);
        $sql = "SELECT * FROM $table_group
                WHERE c_id = $courseId AND name LIKE '%$name%'";

        if (!empty($categoryId)) {
            $categoryId = intval($categoryId);
            $sql .= " AND category_id = $categoryId";
        }
        $sql .= " ORDER BY name";
        $result = Database::query($sql);

        return Database::store_result($result, 'ASSOC');
    }

    /**
     * Set group properties
     * Changes the group's properties.
     * @param int       Group Id
     * @param string    Group name
     * @param string    Group description
     * @param int       Max number of students in group
     * @param int       Document tool's visibility (0=none,1=private,2=public)
     * @param int       Work tool's visibility (0=none,1=private,2=public)
     * @param int       Calendar tool's visibility (0=none,1=private,2=public)
     * @param int       Announcement tool's visibility (0=none,1=private,2=public)
     * @param int       Forum tool's visibility (0=none,1=private,2=public)
     * @param int       Wiki tool's visibility (0=none,1=private,2=public)
     * @param int       Chat tool's visibility (0=none,1=private,2=public)
     * @param bool      Whether self registration is allowed or not
     * @param bool      Whether self unregistration is allowed or not
     * @param int       $categoryId
     * @return bool     TRUE if properties are successfully changed, false otherwise
     */
    public static function set_group_properties(
        $group_id,
        $name,
        $description,
        $maximum_number_of_students,
        $doc_state,
        $work_state,
        $calendar_state,
        $announcements_state,
        $forum_state,
        $wiki_state,
        $chat_state,
        $self_registration_allowed,
        $self_unregistration_allowed,
        $categoryId = null
    ) {
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $table_forum = Database :: get_course_table(TABLE_FORUM);
        $categoryId = intval($categoryId);
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();

        $sql = "UPDATE ".$table_group." SET
                    name='".Database::escape_string(trim($name))."',
                    doc_state = '".Database::escape_string($doc_state)."',
                    work_state = '".Database::escape_string($work_state)."',
                    calendar_state = '".Database::escape_string($calendar_state)."',
                    announcements_state = '".Database::escape_string($announcements_state)."',
                    forum_state = '".Database::escape_string($forum_state)."',
                    wiki_state = '".Database::escape_string($wiki_state)."',
                    chat_state = '".Database::escape_string($chat_state)."',
                    description ='".Database::escape_string(trim($description))."',
                    max_student = '".Database::escape_string($maximum_number_of_students)."',
                    self_registration_allowed = '".Database::escape_string($self_registration_allowed)."',
                    self_unregistration_allowed = '".Database::escape_string($self_unregistration_allowed)."',
                    category_id = ".intval($categoryId)."
                WHERE c_id = $course_id AND id=".$group_id;
        $result = Database::query($sql);

        /* Here we are updating a field in the table forum_forum that perhaps
        duplicates the table group_info.forum_state cvargas*/
        $forum_state = (int) $forum_state;
        $sql2 = "UPDATE ".$table_forum." SET ";
        if ($forum_state === 1) {
            $sql2 .= " forum_group_public_private='public' ";
        } elseif ($forum_state === 2) {
            $sql2 .= " forum_group_public_private='private' ";
        } elseif ($forum_state === 0) {
            $sql2 .= " forum_group_public_private='unavailable' ";
        }
        $sql2 .=" WHERE c_id = $course_id AND forum_of_group=".$group_id;
        Database::query($sql2);
        return $result;
    }

    /**
     * Get the total number of groups for the current course.
     * @return int The number of groups for the current course.
     */
    public static function get_number_of_groups()
    {
        $course_id = api_get_course_int_id();
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $sql = "SELECT COUNT(id) AS number_of_groups
                FROM $table_group
                WHERE c_id = $course_id ";
        $res = Database::query($sql);
        $obj = Database::fetch_object($res);

        return $obj->number_of_groups;
    }

    /**
     * Get all categories
     * @param string $course_code The course (default = current course)
     * @return array
     */
    public static function get_categories($course_code = null)
    {
        $course_info = api_get_course_info($course_code);
        $course_id     = $course_info['real_id'];
        $table_group_cat = Database :: get_course_table(TABLE_GROUP_CATEGORY);
        $sql = "SELECT * FROM $table_group_cat
                WHERE c_id = $course_id
                ORDER BY display_order";
        $res = Database::query($sql);
        $cats = array ();
        while ($cat = Database::fetch_array($res)) {
            $cats[] = $cat;
        }
        return $cats;
    }

    /**
     * Get a group category
     * @param int $id The category id
     * @param string $course_code The course (default = current course)
     * @return array
     */
    public static function get_category($id, $course_code = null)
    {
        if (empty($id)) {
            return array();
        }

        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];
        $id = intval($id);
        $table_group_cat = Database :: get_course_table(TABLE_GROUP_CATEGORY);
        $sql = "SELECT * FROM $table_group_cat
                WHERE c_id = $course_id AND id = $id
                LIMIT 1";
        $res = Database::query($sql);

        return Database::fetch_array($res);
    }

    /**
     * Get a group category
     * @param string $title
     * @param string $course_code The course (default = current course)
     * @return array
     */
    public static function getCategoryByTitle($title, $course_code = null)
    {
        $title = trim($title);

        if (empty($title)) {
            return array();
        }

        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];
        $title = Database::escape_string($title);
        $table_group_cat = Database::get_course_table(TABLE_GROUP_CATEGORY);
        $sql = "SELECT * FROM $table_group_cat
                WHERE c_id = $course_id AND title = '$title'
                LIMIT 1";
        $res = Database::query($sql);
        $category = array();
        if (Database::num_rows($res)) {
            $category = Database::fetch_array($res, 'ASSOC');
        }
        return $category;
    }

    /**
     * Get the unique category of a given group
     * @param int $group_id The iid of the group
     * @param string $course_code The course in which the group is (default =
     * current course)
     * @return array The category
     */
    public static function get_category_from_group($group_id, $course_code = null)
    {
        $table_group = Database:: get_course_table(TABLE_GROUP);
        $table_group_cat = Database:: get_course_table(TABLE_GROUP_CATEGORY);

        $group_id = intval($group_id);

        if (empty($group_id)) {
            return array();
        }

        $course_info = api_get_course_info($course_code);

        if (empty($course_info)) {
            return false;
        }

        $course_id = $course_info['real_id'];
        $sql = "SELECT gc.* FROM $table_group_cat gc, $table_group g
                WHERE
                    gc.c_id = $course_id AND
                    g.c_id = $course_id AND
                    gc.id = g.category_id AND 
                    g.iid = $group_id
                LIMIT 1";
        $res = Database::query($sql);
        $cat = array();
        if (Database::num_rows($res)) {
            $cat = Database::fetch_array($res);
        }
        return $cat;
    }

    /**
     * Delete a group category
     * @param int $cat_id The id of the category to delete
     * @param string $course_code The code in which the category should be
     * deleted (default = current course)
     */
    public static function delete_category($cat_id, $course_code = null)
    {
        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];

        $table_group = Database:: get_course_table(TABLE_GROUP);
        $table_group_cat = Database:: get_course_table(TABLE_GROUP_CATEGORY);
        $cat_id = intval($cat_id);
        $sql = "SELECT iid FROM $table_group
                WHERE c_id = $course_id AND category_id='".$cat_id."'";
        $res = Database::query($sql);
        if (Database::num_rows($res) > 0) {
            while ($group = Database::fetch_object($res)) {
                self::delete_groups($group->iid, $course_code);
            }
        }
        $sql = "DELETE FROM $table_group_cat
                WHERE c_id = $course_id  AND id='".$cat_id."'";
        Database::query($sql);
    }

    /**
     * Create group category
     * @param string $title The title of the new category
     * @param string $description The description of the new category
     * @param bool $self_registration_allowed
     * @param bool $self_unregistration_allowed
     * @param int $max_number_of_students
     * @param int $groups_per_user
     */
    public static function create_category(
        $title,
        $description,
        $doc_state,
        $work_state,
        $calendar_state,
        $announcements_state,
        $forum_state,
        $wiki_state,
        $chat_state = 1,
        $self_registration_allowed = 0,
        $self_unregistration_allowed = 0,
        $maximum_number_of_students = 8,
        $groups_per_user = 0
    ) {
        if (empty($title)) {
            return false;
        }
        $table_group_category = Database :: get_course_table(TABLE_GROUP_CATEGORY);
        $course_id = api_get_course_int_id();

        $sql = "SELECT MAX(display_order)+1 as new_order
                FROM $table_group_category
                WHERE c_id = $course_id ";
        $res = Database::query($sql);
        $obj = Database::fetch_object($res);
        if (!isset($obj->new_order)) {
            $obj->new_order = 1;
        }

        $params = [
            'c_id' => $course_id,
            'title' => $title,
            'display_order' => $obj->new_order,
            'description' => $description,
            'doc_state' => $doc_state,
            'work_state' => $work_state,
            'calendar_state' => $calendar_state,
            'announcements_state' => $announcements_state,
            'forum_state' => $forum_state,
            'wiki_state' => $wiki_state,
            'chat_state' => $chat_state,
            'groups_per_user' => $groups_per_user,
            'self_reg_allowed' => $self_registration_allowed,
            'self_unreg_allowed' => $self_unregistration_allowed,
            'max_student' => $maximum_number_of_students
        ];

        $categoryId = Database::insert($table_group_category, $params);
        if ($categoryId) {
            $sql = "UPDATE $table_group_category SET id = iid
                    WHERE iid = $categoryId";
            Database::query($sql);

            return $categoryId;
        }

        return false;
    }

    /**
     * Update group category
     *
     * @param int $id
     * @param string $title
     * @param string $description
     * @param $doc_state
     * @param $work_state
     * @param $calendar_state
     * @param $announcements_state
     * @param $forum_state
     * @param $wiki_state
     * @param $chat_state
     * @param $self_registration_allowed
     * @param $self_unregistration_allowed
     * @param $maximum_number_of_students
     * @param $groups_per_user
     */
    public static function update_category(
        $id,
        $title,
        $description,
        $doc_state,
        $work_state,
        $calendar_state,
        $announcements_state,
        $forum_state,
        $wiki_state,
        $chat_state,
        $self_registration_allowed,
        $self_unregistration_allowed,
        $maximum_number_of_students,
        $groups_per_user
    ) {
        $table_group_category = Database::get_course_table(TABLE_GROUP_CATEGORY);
        $id = intval($id);

        $course_id = api_get_course_int_id();

        $sql = "UPDATE ".$table_group_category." SET
                    title='".Database::escape_string($title)."',
                    description='".Database::escape_string($description)."',
                    doc_state = '".Database::escape_string($doc_state)."',
                    work_state = '".Database::escape_string($work_state)."',
                    calendar_state = '".Database::escape_string($calendar_state)."',
                    announcements_state = '".Database::escape_string($announcements_state)."',
                    forum_state = '".Database::escape_string($forum_state)."',
                    wiki_state = '".Database::escape_string($wiki_state)."',
                    chat_state = '".Database::escape_string($chat_state)."',
                    groups_per_user   = '".Database::escape_string($groups_per_user)."',
                    self_reg_allowed = '".Database::escape_string($self_registration_allowed)."',
                    self_unreg_allowed = '".Database::escape_string($self_unregistration_allowed)."',
                    max_student = ".intval($maximum_number_of_students)."
                WHERE c_id = $course_id AND id = $id";

        Database::query($sql);

        // Updating all groups inside this category
        $groups = self::get_group_list($id);

        if (!empty($groups)) {
            foreach ($groups as $group) {
                GroupManager::set_group_properties(
                    $group['id'],
                    $group['name'],
                    $group['description'],
                    $maximum_number_of_students,
                    $doc_state,
                    $work_state,
                    $calendar_state,
                    $announcements_state,
                    $forum_state,
                    $wiki_state,
                    $chat_state,
                    $self_registration_allowed,
                    $self_unregistration_allowed,
                    $id
                );
            }
        }
    }

    /**
     * Returns the number of groups of the user with the greatest number of
     * subscriptions in the given category
     */
    public static function get_current_max_groups_per_user($category_id = null, $course_code = null)
    {
        $course_info = api_get_course_info ($course_code);
        $group_table = Database :: get_course_table(TABLE_GROUP);
        $group_user_table = Database :: get_course_table(TABLE_GROUP_USER);
        $sql = 'SELECT COUNT(gu.group_id) AS current_max
                FROM '.$group_user_table.' gu, '.$group_table.' g
				WHERE g.c_id = '.$course_info['real_id'].'
				AND gu.c_id = g.c_id
				AND gu.group_id = g.iid ';
        if ($category_id != null) {
            $category_id = intval($category_id);
            $sql .= ' AND g.category_id = '.$category_id;
        }
        $sql .= ' GROUP BY gu.user_id ORDER BY current_max DESC LIMIT 1';
        $res = Database::query($sql);
        $obj = Database::fetch_object($res);

        return $obj->current_max;
    }

    /**
     * Swaps the display-order of two categories
     * @param int $id1 The id of the first category
     * @param int $id2 The id of the second category
     */
    public static function swap_category_order($id1, $id2)
    {
        $table_group_cat = Database :: get_course_table(TABLE_GROUP_CATEGORY);
        $id1 = intval($id1);
        $id2 = intval($id2);
        $course_id = api_get_course_int_id();

        $sql = "SELECT id, display_order FROM $table_group_cat
                WHERE id IN ($id1,$id2) AND c_id = $course_id ";
        $res = Database::query($sql);
        $cat1 = Database::fetch_object($res);
        $cat2 = Database::fetch_object($res);
        if ($cat1 && $cat2) {
            $sql = "UPDATE $table_group_cat SET display_order=$cat2->display_order
                WHERE id = $cat1->id AND c_id = $course_id ";
            Database::query($sql);

            $sql = "UPDATE $table_group_cat SET display_order=$cat1->display_order
                    WHERE id = $cat2->id AND c_id = $course_id ";
            Database::query($sql);
        }
    }

    /**
     * Get all users from a given group
     * @param int $group_id The group
     * @param bool $load_extra_info
     * @param int $start
     * @param int $limit
     * @param bool $getCount
     * @param int $courseId
     * @return array list of user id
     */
    public static function get_users(
        $group_id,
        $load_extra_info = false,
        $start = null,
        $limit = null,
        $getCount = false,
        $courseId = null,
        $column = null,
        $direction = null
    ) {
        $group_user_table = Database :: get_course_table(TABLE_GROUP_USER);
        $groupTable = Database :: get_course_table(TABLE_GROUP);
        $user_table = Database :: get_main_table(TABLE_MAIN_USER);

        $group_id = intval($group_id);

        if (empty($courseId)) {
            $courseId = api_get_course_int_id();
        } else {
            $courseId = intval($courseId);
        }

        $select = " SELECT u.id, firstname, lastname ";
        if ($getCount) {
            $select = " SELECT count(u.id) count";
        }
        $sql = "$select
                FROM $group_user_table gu
                INNER JOIN $groupTable g
                ON (gu.group_id = g.iid and g.c_id = gu.c_id)
                INNER JOIN $user_table u
                ON (u.id = gu.user_id)
                WHERE 
                    gu.c_id = $courseId AND 
                    g.id = $group_id";

        if (!empty($column) && !empty($direction)) {
            $column = Database::escape_string($column, null, false);
            $direction = ($direction == 'ASC' ? 'ASC' : 'DESC');
            $sql .= " ORDER BY $column $direction";
        }

        if (!empty($start) && !empty($limit)) {
            $start = intval($start);
            $limit = intval($limit);
            $sql .= " LIMIT $start, $limit";
        }

        $res = Database::query($sql);
        $users = array();
        while ($obj = Database::fetch_object($res)) {
            if ($getCount) {
                return $obj->count;
                break;
            }
            if ($load_extra_info) {
                $users[] = api_get_user_info($obj->user_id);
            } else {
                $users[] = $obj->user_id;
            }
        }

        return $users;
    }

    /**
     * @param int $group_id
     * @return array
     */
    public static function getStudentsAndTutors($group_id)
    {
        $group_user_table = Database :: get_course_table(TABLE_GROUP_USER);
        $tutor_user_table = Database :: get_course_table(TABLE_GROUP_TUTOR);
        $groupTable = Database :: get_course_table(TABLE_GROUP);

        $course_id = api_get_course_int_id();
        $group_id = intval($group_id);
        $sql = "SELECT user_id 
                FROM $group_user_table gu
                INNER JOIN $groupTable g
                ON (gu.group_id = g.iid and g.c_id = gu.c_id)
                WHERE gu.c_id = $course_id AND g.id = $group_id";
        $res = Database::query($sql);
        $users = array();

        while ($obj = Database::fetch_object($res)) {
            $users[] = api_get_user_info($obj->user_id);
        }

        $sql = "SELECT user_id 
                FROM $tutor_user_table gu
                INNER JOIN $groupTable g
                ON (gu.group_id = g.id and g.c_id = gu.c_id)
                WHERE gu.c_id = $course_id AND g.id = $group_id";
        $res = Database::query($sql);
        while ($obj = Database::fetch_object($res)) {
            $users[] = api_get_user_info($obj->user_id);
        }

        return $users;
    }

    /**
     * Get only tutors from a group
     * @param int $group_id iid
     * @return array
     */
    public static function getTutors($group_id)
    {
        $groupTable = Database :: get_course_table(TABLE_GROUP);
        $tutor_user_table = Database :: get_course_table(TABLE_GROUP_TUTOR);
        $course_id = api_get_course_int_id();
        $group_id = intval($group_id);

        $sql = "SELECT user_id 
                FROM $tutor_user_table gu
                INNER JOIN $groupTable g
                ON (gu.group_id = g.id and g.c_id = gu.c_id)
                WHERE gu.c_id = $course_id AND g.id = $group_id";
        $res = Database::query($sql);

        $users = array();
        while ($obj = Database::fetch_object($res)) {
            $users[] = api_get_user_info($obj->user_id);
        }

        return $users;
    }

    /**
     * Get only students from a group (not tutors)
     * @param int $group_id iid
     * @return array
     */
    public static function getStudents($group_id)
    {
        $em = Database::getManager();
        $subscriptions = $em
            ->createQuery('
                SELECT gu
                FROM ChamiloCourseBundle:CGroupRelUser gu
                INNER JOIN ChamiloCourseBundle:CGroupInfo g
                WITH gu.groupId = g.iid AND g.cId = gu.cId
                WHERE gu.cId = :course AND g.id = :group
            ')
            ->setParameters([
                'course' => api_get_course_int_id(),
                'group' => intval($group_id)
            ])
            ->getResult();

        $users = [];

        /** @var CGroupRelUser $subscription */
        foreach ($subscriptions as $subscription) {
            $users[] = api_get_user_info($subscription->getUserId());
        }

        return $users;
    }

    /**
     * Returns users belonging to any of the group
     *
     * @param array $groups list of group ids
     * @return array list of user ids
     */
    public static function get_groups_users($groups = array())
    {
        $result = array();
        $tbl_group_user = Database::get_course_table(TABLE_GROUP_USER);
        $course_id = api_get_course_int_id();

        $groups = array_map('intval', $groups);
        // protect individual elements with surrounding quotes
        $groups = implode(', ', $groups);
        $sql = "SELECT DISTINCT user_id
                FROM $tbl_group_user gu
                WHERE c_id = $course_id AND gu.group_id IN ($groups)";
        $rs = Database::query($sql);
        while ($row = Database::fetch_array($rs)) {
            $result[] = $row['user_id'];
        }

        return $result;
    }

    /**
     * Fill the groups with students.
     * The algorithm takes care to first fill the groups with the least # of users.
     * Analysis
     * There was a problem with the "ALL" setting.
     * When max # of groups is set to all, the value is sometimes NULL and sometimes ALL
     * and in both cased the query does not work as expected.
     * Stupid solution (currently implemented: set ALL to a big number (INFINITE) and things are solved :)
     * Better solution: that's up to you.
     *
     * Note
     * Throughout Dokeos there is some confusion about "course id" and "course code"
     * The code is e.g. TEST101, but sometimes a variable that is called courseID also contains a course code string.
     * However, there is also a integer course_id that uniquely identifies the course.
     * ywarnier:> Now the course_id has been removed (25/1/2005)
     * The databases are als very inconsistent in this.
     *
     * @param int $groupId iid
     * @author Chrisptophe Gesche <christophe.geshe@claroline.net>,
     *         Hugues Peeters     <hugues.peeters@claroline.net> - original version
     * @author Roan Embrechts - virtual course support, code cleaning
     * @author Bart Mollet - code cleaning, use other GroupManager-functions
     * @return void
     */
    public static function fill_groups($groupId)
    {
        $_course = api_get_course_info();
        $groupId = (int) $groupId;

        $category = self::get_category_from_group($groupId);
        $groups_per_user = isset($category['groups_per_user']) ? $category['groups_per_user'] : self::GROUP_PER_MEMBER_NO_LIMIT;
        $group_table = Database:: get_course_table(TABLE_GROUP);
        $group_user_table = Database:: get_course_table(TABLE_GROUP_USER);
        $session_id = api_get_session_id();

        $complete_user_list = CourseManager :: get_real_and_linked_user_list($_course['code'], true, $session_id);
        $number_groups_per_user = $groups_per_user == self::GROUP_PER_MEMBER_NO_LIMIT ? self::INFINITE : $groups_per_user;

        /*
         * Retrieve all the groups where enrollment is still allowed
         * (reverse) ordered by the number of place available
         */

        $course_id = api_get_course_int_id();
        $sql = "SELECT g.id gid, g.iid, g.max_student-count(ug.user_id) nbPlaces, g.max_student
                FROM $group_table g
                LEFT JOIN  $group_user_table ug ON
                    g.c_id = $course_id AND 
                    ug.c_id = $course_id AND 
                    g.iid = ug.group_id
                WHERE
                    g.iid = $groupId
                GROUP BY (g.iid)
                HAVING (nbPlaces > 0 OR g.max_student = ".self::MEMBER_PER_GROUP_NO_LIMIT.")
                ORDER BY nbPlaces DESC";
        $sql_result = Database::query($sql);
        $group_available_place = array();
        while ($group = Database::fetch_array($sql_result, 'ASSOC')) {
            $group_available_place[$group['iid']] = $group['nbPlaces'];
        }

        /*
         * Retrieve course users (reverse) ordered by the number
         * of group they are already enrolled
         */
        for ($i = 0; $i < count($complete_user_list); $i ++) {
            //find # of groups the user is enrolled in
            $number_of_groups = self :: user_in_number_of_groups(
                $complete_user_list[$i]["user_id"],
                isset($category['id']) ? $category['id'] : null
            );
            //add # of groups to user list
            $complete_user_list[$i]['number_groups_left'] = $number_groups_per_user - $number_of_groups;
        }


        //first sort by user_id to filter out duplicates
        $complete_user_list = TableSort :: sort_table($complete_user_list, 'user_id');
        $complete_user_list = self :: filter_duplicates($complete_user_list, 'user_id');
        $complete_user_list = self :: filter_only_students($complete_user_list);


        //now sort by # of group left
        $complete_user_list = TableSort :: sort_table($complete_user_list, 'number_groups_left', SORT_DESC);
        $userToken = array ();
        foreach ($complete_user_list as $this_user) {
            if ($this_user['number_groups_left'] > 0) {
                $userToken[$this_user['user_id']] = $this_user['number_groups_left'];
            }
        }

        $changed = true;
        while ($changed) {
            $changed = false;
            reset($group_available_place);
            arsort($group_available_place);
            reset($userToken);
            arsort($userToken);

            foreach ($group_available_place as $group_id => $place) {
                foreach ($userToken as $user_id => $places) {
                    if (self :: can_user_subscribe($user_id, $group_id)) {
                        self :: subscribe_users($user_id, $group_id);
                        $group_available_place[$group_id]--;
                        unset($userToken[$user_id]);
                        $changed = true;
                        break;
                    }
                }
                if ($changed) {
                    break;
                }
            }
        }
    }

    /**
     * Get the number of students in a group.
     * @param int $group_id iid
     * @return int Number of students in the given group.
     */
    public static function number_of_students($group_id, $course_id = null)
    {
        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        $group_id = intval($group_id);
        if (empty($course_id)) {
            $course_id = api_get_course_int_id();
        } else {
            $course_id = intval($course_id);
        }
        $sql = "SELECT  COUNT(*) AS number_of_students
                FROM $table_group_user
                WHERE c_id = $course_id AND group_id = $group_id";
        $result = Database::query($sql);
        $db_object = Database::fetch_object($result);

        return $db_object->number_of_students;
    }

    /**
     * Maximum number of students in a group
     * @param int $group_id iid
     * @return int Maximum number of students in the given group.
     */
    public static function maximum_number_of_students($group_id)
    {
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();
        $sql = "SELECT max_student FROM $table_group WHERE c_id = $course_id AND iid = $group_id";
        $db_result = Database::query($sql);
        $db_object = Database::fetch_object($db_result);
        if ($db_object->max_student == 0) {
            return self::INFINITE;
        }
        return $db_object->max_student;
    }

    /**
     * Number of groups of a user
     * @param int $user_id
     * @return int The number of groups the user is subscribed in.
     */
    public static function user_in_number_of_groups($user_id, $cat_id = null)
    {
        $table_group_user = Database:: get_course_table(TABLE_GROUP_USER);
        $table_group = Database:: get_course_table(TABLE_GROUP);
        $user_id = intval($user_id);
        $cat_id = intval($cat_id);

        $course_id = api_get_course_int_id();
        $cat_condition = '';
        if (!empty($cat_id)) {
            $cat_condition = " AND g.category_id =  $cat_id ";
        }

        $sql = "SELECT  COUNT(*) AS number_of_groups
                FROM $table_group_user gu, $table_group g
                WHERE
                    gu.c_id = $course_id AND
                    g.c_id = $course_id AND
                    gu.user_id = $user_id AND
                    g.iid = gu.group_id  
                    $cat_condition";
        $result = Database::query($sql);
        $db_object = Database::fetch_object($result);

        return $db_object->number_of_groups;
    }

    /**
     * Is sef-registration allowed?
     * @param int $user_id
     * @param int $group_id iid
     * @return bool TRUE if self-registration is allowed in the given group.
     */
    public static function is_self_registration_allowed($user_id, $group_id)
    {
        $course_id = api_get_course_int_id();
        if (!$user_id > 0) {
            return false;
        }
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $group_id = intval($group_id);
        if (isset($group_id)) {
            $sql = "SELECT status, self_registration_allowed
                    FROM $table_group
                    WHERE c_id = $course_id AND iid = $group_id";
            $result = Database::query($sql);
            $group = Database::fetch_object($result);

            if ($group->status == 0 || $group->self_registration_allowed != 1) {

                return false;
            }

            return self::can_user_subscribe($user_id, $group_id);
        } else {
            return false;
        }
    }

    /**
     * Is sef-unregistration allowed?
     * @param int $user_id
     * @param int $group_id iid
     * @return bool TRUE if self-unregistration is allowed in the given group.
     */
    public static function is_self_unregistration_allowed($user_id, $group_id)
    {
        if (!$user_id > 0) {
            return false;
        }
        $table_group = Database :: get_course_table(TABLE_GROUP);
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();

        $sql = "SELECT status, self_unregistration_allowed
                FROM $table_group
                WHERE c_id = $course_id AND iid = $group_id";
        $result = Database::query($sql);
        $group = Database::fetch_object($result);

        if ($group->status == 0 || $group->self_unregistration_allowed != 1) {

            return false;
        }

        return self::is_subscribed($user_id, $group_id);
    }

    /**
     * Is user subscribed in group?
     * @param int $user_id
     * @param int $group_id iid
     * @return bool TRUE if given user is subscribed in given group
     */
    public static function is_subscribed($user_id, $group_id)
    {
        $course_id = api_get_course_int_id();
        if (empty($user_id) || empty($group_id) || empty($course_id)) {
            return false;
        }
        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        $group_id = intval($group_id);
        $user_id = intval($user_id);

        $sql = "SELECT 1 FROM $table_group_user
                WHERE
                    c_id = $course_id AND
                    group_id = $group_id AND
                    user_id = $user_id
                ";
        $result = Database::query($sql);

        return Database::num_rows($result) > 0;
    }

    /**
     * Can a user subscribe to a specified group in a course
     * @param int $user_id
     * @param int $group_id iid
     * @param bool $checkMaxNumberStudents
     *
     * @return bool TRUE if given user  can be subscribed in given group
     */
    public static function can_user_subscribe($user_id, $group_id, $checkMaxNumberStudents = true)
    {
        if ($checkMaxNumberStudents) {
            $category = self:: get_category_from_group($group_id);
            if ($category) {
                if ($category['groups_per_user'] == self::GROUP_PER_MEMBER_NO_LIMIT) {
                    $category['groups_per_user'] = self::INFINITE;
                }
                $result = self:: user_in_number_of_groups($user_id, $category['id'] ) < $category['groups_per_user'];
                if ($result == false) {
                    return false;
                }
            }

            $result = self:: number_of_students($group_id) < self:: maximum_number_of_students($group_id);

            if ($result == false) {
                return false;
            }
        }

        $result = self::is_tutor_of_group($user_id, $group_id);

        if ($result) {
            return false;
        }

        $result = self::is_subscribed($user_id, $group_id);

        if ($result) {
            return false;
        }

        return true;
    }

    /**
     * Get all subscribed users (members) from a group
     * @param int $group_id (iid)
     * @return array An array with information of all users from the given group.
     *               (user_id, firstname, lastname, email)
     */
    public static function get_subscribed_users($group_id)
    {
        $table_user = Database :: get_main_table(TABLE_MAIN_USER);
        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        $order_clause = api_sort_by_first_name() ? ' ORDER BY u.firstname, u.lastname' : ' ORDER BY u.lastname, u.firstname';
        $orderListByOfficialCode = api_get_setting('order_user_list_by_official_code');
        if ($orderListByOfficialCode === 'true') {
            $order_clause = " ORDER BY u.official_code, u.firstname, u.lastname";
        }

        if (empty($group_id)) {
            return array();
        }
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();

        $sql = "SELECT ug.id, u.user_id, u.lastname, u.firstname, u.email, u.username
                FROM  $table_user u INNER JOIN $table_group_user ug
                ON (ug.user_id = u.user_id)
                WHERE ug.c_id = $course_id AND
                      ug.group_id = $group_id
                $order_clause";
        $db_result = Database::query($sql);
        $users = array();
        while ($user = Database::fetch_object($db_result)) {
            $users[$user->user_id] = array(
                'user_id'   => $user->user_id,
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'email'     => $user->email,
                'username'  => $user->username
            );
        }

        return $users;
    }

    /**
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * Get all subscribed tutors of a group
     * @param int $group_id (iid)
     * @return array An array with information of all users from the given group.
     *               (user_id, firstname, lastname, email)
     */
    public static function get_subscribed_tutors($group_id, $id_only = false)
    {
        $table_user = Database :: get_main_table(TABLE_MAIN_USER);
        $table_group_tutor = Database :: get_course_table(TABLE_GROUP_TUTOR);
        $order_clause = api_sort_by_first_name() ? ' ORDER BY u.firstname, u.lastname' : ' ORDER BY u.lastname, u.firstname';

        $orderListByOfficialCode = api_get_setting('order_user_list_by_official_code');
        if ($orderListByOfficialCode === 'true') {
            $order_clause = " ORDER BY u.official_code, u.firstname, u.lastname";
        }

        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();

        $sql = "SELECT tg.id, u.user_id, u.lastname, u.firstname, u.email
                FROM $table_user u, $table_group_tutor tg
                WHERE
                    tg.c_id = $course_id AND
                    tg.group_id = $group_id AND
                    tg.user_id = u.user_id 
                $order_clause
                ";
        $db_result = Database::query($sql);
        $users = array ();
        while ($user = Database::fetch_object($db_result)) {
            if (!$id_only) {
                $member['user_id'] = $user->user_id;
                $member['firstname'] = $user->firstname;
                $member['lastname'] = $user->lastname;
                $member['email'] = $user->email;
                $users[] = $member;
            } else {
                $users[] = $user->user_id;
            }
        }

        return $users;
    }

    /**
     * Subscribe user(s) to a specified group in current course (as a student)
     * @param mixed $user_ids Can be an array with user-id's or a single user-id
     * @param int $group_id iid
     * @param int $course_id
     * @return bool TRUE if successful
     */
    public static function subscribe_users($user_ids, $group_id, $course_id = null)
    {
        $user_ids = is_array($user_ids) ? $user_ids : array($user_ids);
        $course_id = isset($course_id) && !empty($course_id) ? intval($course_id) : api_get_course_int_id();
        $group_id = intval($group_id);

        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                if (self::can_user_subscribe($user_id, $group_id)) {
                    $user_id = intval($user_id);
                    $sql = "INSERT INTO ".$table_group_user." (c_id, user_id, group_id)
                            VALUES ('$course_id', '".$user_id."', '".$group_id."')";
                    Database::query($sql);
                }
            }
        }

        return true;
    }

    /**
     * Subscribe tutor(s) to a specified group in current course
     * @param mixed $user_ids Can be an array with user-id's or a single user-id
     * @param int $group_id iid
     * @param int $course_id
     *
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @see subscribe_users. This function is almost an exact copy of that function.
     * @return bool TRUE if successful
     */
    public static function subscribe_tutors($user_ids, $groupId, $course_id = null)
    {
        $user_ids = is_array($user_ids) ? $user_ids : array($user_ids);
        $result = true;
        $course_id = isset($course_id) && !empty($course_id) ? intval($course_id) : api_get_course_int_id();
        $table_group_tutor = Database :: get_course_table(TABLE_GROUP_TUTOR);
        $groupId = (int) $groupId;

        foreach ($user_ids as $user_id) {
            $user_id = intval($user_id);
            if (self::can_user_subscribe($user_id, $groupId, false)) {
                $sql = "INSERT INTO " . $table_group_tutor . " (c_id, user_id, group_id)
                        VALUES ('$course_id', '" . $user_id . "', '" . $groupId . "')";
                $result = Database::query($sql);
            }
        }

        return $result;
    }

    /**
     * Unsubscribe user(s) from a specified group in current course
     * @param mixed $user_ids Can be an array with user-id's or a single user-id
     * @param int $group_id iid
     * @return bool TRUE if successful
     */
    public static function unsubscribe_users($user_ids, $group_id)
    {
        $user_ids = is_array($user_ids) ? $user_ids : array ($user_ids);
        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();
        $sql = 'DELETE FROM '.$table_group_user.'
                WHERE
                    c_id = '.$course_id.' AND
                    group_id = '.$group_id.' AND
                    user_id IN ('.implode(',', $user_ids).')
                ';
        Database::query($sql);
    }

    /**
     * Unsubscribe all users from one or more groups
     * @param int $group_id iid
     * @return bool TRUE if successful
     */
    public static function unsubscribe_all_users($groupId)
    {
        $course_id = api_get_course_int_id();
        //api_is_element_in_the_session(TOOL_GROUP, $group_ids[$i])

        $groupId = (int) $groupId;
        if (empty($course_id) || empty($groupId)) {
            return false;
        }

        $table_group_user = Database :: get_course_table(TABLE_GROUP_USER);
        $sql = "DELETE FROM $table_group_user
                WHERE 
                    group_id = $groupId AND 
                    c_id = $course_id";
        $result = Database::query($sql);

        return $result;
    }

    /**
     * Unsubscribe all tutors from one or more groups
     * @param int $groupId iid
     * @see unsubscribe_all_users. This function is almost an exact copy of that function.
     * @return bool TRUE if successful
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function unsubscribe_all_tutors($groupId)
    {
        $courseId = api_get_course_int_id();
        $groupId = (int) $groupId;

        if (empty($courseId) || empty($groupId)) {
            return false;
        }

        if (!empty($groupId) > 0) {
            $table_group_tutor = Database :: get_course_table(TABLE_GROUP_TUTOR);
            $sql = "DELETE FROM $table_group_tutor
                    WHERE group_id = $groupId AND c_id = $courseId";
            $result = Database::query($sql);
            return $result;
        }
        return true;
    }

    /**
     * Is the user a tutor of this group?
     * @param int $user_id the id of the user
     * @param int $group_id the iid of the group
     * @return boolean true/false
     * @todo use the function user_has_access that includes this function
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function is_tutor_of_group($user_id, $group_id)
    {
        $table_group_tutor = Database :: get_course_table(TABLE_GROUP_TUTOR);
        $user_id = intval($user_id);
        $group_id = intval($group_id);
        $course_id = api_get_course_int_id();

        $sql = "SELECT * FROM $table_group_tutor
                WHERE 
                    c_id = $course_id AND 
                    user_id = $user_id AND 
                    group_id = $group_id";
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is the user part of this group? This can be a tutor or a normal member
     * you should use this function if the access to a tool or functionality is
     * restricted to the people who are actually in the group
     * before you had to check if the user was
     * 1. a member of the group OR
     * 2. a tutor of the group. This function combines both
     * @param int $user_id the id of the user
     * @param int $group_id the iid of the group
     * @return boolean true/false
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function is_user_in_group($user_id, $group_id)
    {
        $member = self :: is_subscribed($user_id, $group_id);
        $tutor = self :: is_tutor_of_group($user_id, $group_id);
        if ($member || $tutor) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all group's from a given course in which a given user is unsubscribed
     * @author  Patrick Cool
     * @param     int  course id
     * retrieve the groups for
     * @param integer $user_id: the ID of the user you want to know all its
     * group memberships
     */
    public static function get_group_ids($course_id, $user_id)
    {
        $groups = array();
        $tbl_group = Database::get_course_table(TABLE_GROUP_USER);
        $tbl_group_tutor = Database::get_course_table(TABLE_GROUP_TUTOR);
        $user_id = intval($user_id);
        $course_id = intval($course_id);

        $sql = "SELECT group_id FROM $tbl_group
                WHERE c_id = $course_id AND user_id = '$user_id'";
        $groupres = Database::query($sql);

        if ($groupres) {
            while ($myrow = Database::fetch_array($groupres)) {
                $groups[] = $myrow['group_id'];
            }
        }

        //Also loading if i'm the tutor
        $sql = "SELECT group_id FROM $tbl_group_tutor
                WHERE c_id = $course_id AND user_id = '$user_id'";
        $groupres = Database::query($sql);
        if ($groupres) {
            while ($myrow = Database::fetch_array($groupres)) {
                $groups[] = $myrow['group_id'];
            }
        }
        if (!empty($groups)) {
            array_filter($groups);
        }

        return $groups;
    }

    /**
     * Filter out duplicates in a multidimensional array
     * by comparing field $compare_field.
     *
     * @param $user_array_in list of users (must be sorted).
     * @param string $compare_field, the field to be compared
     */
    public static function filter_duplicates($user_array_in, $compare_field)
    {
        $total_number = count($user_array_in);
        $user_array_out[0] = $user_array_in[0];
        $count_out = 0;
        for ($count_in = 1; $count_in < $total_number; $count_in++) {
            if ($user_array_in[$count_in][$compare_field] != $user_array_out[$count_out][$compare_field]) {
                $count_out++;
                $user_array_out[$count_out] = $user_array_in[$count_in];
            }
        }
        return $user_array_out;
    }

    /**
     * Remove all users that are not students and all users who have tutor status
     * from  the list.
     */
    public static function filter_only_students($user_array_in)
    {
        $user_array_out = array();
        foreach ($user_array_in as $this_user) {
            if (api_get_session_id()) {
                if ($this_user['status_session'] == 0) {
                    $user_array_out[] = $this_user;
                }
            } else {
                if ($this_user['status_rel'] == STUDENT) {
                    $user_array_out[] = $this_user;
                }
            }
        }
        return $user_array_out;
    }

    /**
     * Check if a user has access to a certain group tool
     * @param int $user_id The user id
     * @param int $group_id The group iid
     * @param string $tool The tool to check the access rights. This should be
     * one of constants: GROUP_TOOL_DOCUMENTS
     * @return bool True if the given user has access to the given tool in the
     * given course.
     */
    public static function user_has_access($user_id, $group_id, $tool)
    {
        // Admin have access everywhere
        if (api_is_platform_admin()) {
            return true;
        }

        // Course admin also have access to everything
        if (api_is_allowed_to_edit()) {
            return true;
        }

        switch ($tool) {
            case self::GROUP_TOOL_FORUM:
                $key = 'forum_state';
                break;
            case self::GROUP_TOOL_DOCUMENTS:
                $key = 'doc_state';
                break;
            case self::GROUP_TOOL_CALENDAR:
                $key = 'calendar_state';
                break;
            case self::GROUP_TOOL_ANNOUNCEMENT:
                $key = 'announcements_state';
                break;
            case self::GROUP_TOOL_WORK:
                $key = 'work_state';
                break;
            case self::GROUP_TOOL_WIKI:
                $key = 'wiki_state';
                break;
            case self::GROUP_TOOL_CHAT:
                $key = 'chat_state';
                break;
            default:
                return false;
        }

        // Check group properties
        $groupInfo = self::get_group_properties($group_id, true);

        if (empty($groupInfo)) {
            return false;
        }

        if ($groupInfo['status'] == 0) {
            return false;
        }

        if (!isset($groupInfo[$key])) {
            return false;
        }

        if (api_is_allowed_to_edit(false, true)) {
            return true;
        }

        $status = $groupInfo[$key];

        switch ($status) {
            case self::TOOL_NOT_AVAILABLE:
                return false;
                break;
            case self::TOOL_PUBLIC:
                return true;
                break;
            case self::TOOL_PRIVATE:
                $userIsInGroup = self::is_user_in_group($user_id, $groupInfo['iid']);
                if ($userIsInGroup) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * @param int $userId
     * @param array $groupInfo
     * @param int $sessionId
     *
     * @return bool
     */
    public static function userHasAccessToBrowse($userId, $groupInfo, $sessionId = 0)
    {
        if (empty($groupInfo)) {
            return false;
        }

        if (api_is_platform_admin()) {
            return true;
        }

        if (api_is_allowed_to_edit(false, true)) {
            return true;
        }

        $groupId = $groupInfo['iid'];
        $tutors = self::get_subscribed_tutors($groupId, true);

        if (in_array($userId, $tutors)) {
            return true;
        }

        if ($groupInfo['status'] == 0) {
            return false;
        }

        if (
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_FORUM) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_DOCUMENTS) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_CALENDAR) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_ANNOUNCEMENT) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_WORK) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_WIKI) ||
            self::user_has_access($userId, $groupId, self::GROUP_TOOL_CHAT)
        ) {

            return true;
        }

        if (api_is_course_coach() && $groupInfo['session_id'] == $sessionId) {
            return true;
        }

        return false;
    }


    /**
     * Get all groups where a specific user is subscribed
     * @param int $user_id
     * @return array
     */
    public static function get_user_group_name($user_id)
    {
        $table_group_user = Database::get_course_table(TABLE_GROUP_USER);
        $table_group = Database::get_course_table(TABLE_GROUP);
        $user_id = intval($user_id);
        $course_id = api_get_course_int_id();
        $sql = "SELECT name
                FROM $table_group g 
                INNER JOIN $table_group_user gu
                ON (gu.group_id = g.iid)
                WHERE
                  gu.c_id= $course_id AND
                  g.c_id= $course_id AND
                  gu.user_id = $user_id";
        $res = Database::query($sql);
        $groups = array();
        while ($group = Database::fetch_array($res)) {
            $groups[] .= $group['name'];
        }
        return $groups;
    }

    /**
     * Get all groups where a specific user is subscribed
     * @param int $user_id
     * @return array
     */
    public static function getAllGroupPerUserSubscription($user_id)
    {
        $table_group_user = Database::get_course_table(TABLE_GROUP_USER);
        $table_tutor_user = Database::get_course_table(TABLE_GROUP_TUTOR);
        $table_group = Database::get_course_table(TABLE_GROUP);
        $user_id = intval($user_id);
        $course_id = api_get_course_int_id();
        $sql = "SELECT DISTINCT g.*
               FROM $table_group g
               LEFT JOIN $table_group_user gu
               ON (gu.group_id = g.iid AND g.c_id = gu.c_id)
               LEFT JOIN $table_tutor_user tu
               ON (tu.group_id = g.iid AND g.c_id = tu.c_id)
               WHERE
                  g.c_id = $course_id AND
                  (gu.user_id = $user_id OR tu.user_id = $user_id) ";
        $res = Database::query($sql);
        $groups = array();
        while ($group = Database::fetch_array($res, 'ASSOC')) {
            $groups[] = $group;
        }

        return $groups;
    }

    /**
     *
     * See : fill_groups
     *       Fill the groups with students.
     *
     * note : optimize fill_groups_list <--> fill_groups
     * @param int $groupId (group iid)
     * @return array|bool
     */
    public static function fill_groups_list($groupId)
    {
        $groupId = (int) $groupId;
        $_course = api_get_course_info();

        if (empty($_course) || empty($groupId)) {
            return [];
        }

        $category = self::get_category_from_group($groupId);
        $number_groups_per_user = self::GROUP_PER_MEMBER_NO_LIMIT;
        $categoryId = 0;
        if ($category) {
            $groups_per_user = $category['groups_per_user'];
            $number_groups_per_user = ($groups_per_user == self::GROUP_PER_MEMBER_NO_LIMIT ? self::INFINITE : $groups_per_user);
            $categoryId = $category['id'];
        }

        $group_table = Database::get_course_table(TABLE_GROUP);
        $group_user_table = Database::get_course_table(TABLE_GROUP_USER);
        $session_id = api_get_session_id();
        $complete_user_list = CourseManager::get_real_and_linked_user_list($_course['code'], true, $session_id);
        $course_id = api_get_course_int_id();

        /*
         * Retrieve all the groups where enrollment is still allowed
         * (reverse) ordered by the number of place available
         */
        $sql = "SELECT g.id gid, count(ug.user_id) count_users, g.max_student
                FROM $group_table g
                LEFT JOIN $group_user_table ug
                ON g.iid = ug.group_id
                WHERE   
                    g.c_id = $course_id AND
                    ug.c_id = $course_id AND
                    g.iid = $groupId
                GROUP BY (g.iid)";

        $sql_result = Database::query($sql);
        $group_available_place = array();
        while ($group = Database::fetch_array($sql_result, 'ASSOC')) {
            if (!empty($group['max_student'])) {
                $places = intval($group['max_student'] - $group['count_users']);
            } else {
                $places = self::MEMBER_PER_GROUP_NO_LIMIT;
            }
            $group_available_place[$group['gid']] = $places;
        }

        /*
         * Retrieve course users (reverse) ordered by the number
         * of group they are already enrolled
         */
        for ($i = 0; $i < count($complete_user_list); $i ++) {
            // find # of groups the user is enrolled in
            $number_of_groups = self:: user_in_number_of_groups(
                $complete_user_list[$i]["user_id"],
                $categoryId
            );
            // add # of groups to user list
            $complete_user_list[$i]['number_groups_left'] = $number_groups_per_user - $number_of_groups;
        }
        //first sort by user_id to filter out duplicates
        $complete_user_list = TableSort::sort_table($complete_user_list, 'user_id');
        $complete_user_list = self::filter_duplicates($complete_user_list, 'user_id');
        //now sort by # of group left
        $complete_user_list = TableSort::sort_table($complete_user_list, 'number_groups_left', SORT_DESC);

        return $complete_user_list;
    }

    /**
     * @param array $group_list
     * @param int $category_id
     *
     * @return string
     */
    public static function process_groups($group_list, $category_id = null)
    {
        global $charset;
        $category_id = intval($category_id);

        $totalRegistered = 0;
        $group_data = array();
        $user_info = api_get_user_info();
        $session_id = api_get_session_id();
        $user_id = $user_info['user_id'];
        $hideGroup = api_get_setting('hide_course_group_if_no_tools_available');

        foreach ($group_list as $this_group) {
            // Validation when belongs to a session
            $session_img = api_get_session_image($this_group['session_id'], $user_info['status']);

            // All the tutors of this group
            $tutorsids_of_group = self::get_subscribed_tutors($this_group['iid'], true);
            $isMember = self::is_subscribed($user_id, $this_group['iid']);

            // Create a new table-row
            $row = array();

            // Checkbox
            if (api_is_allowed_to_edit(false, true) && count($group_list) > 1) {
                $row[] = $this_group['id'];
            }

            if (GroupManager::userHasAccessToBrowse($user_id, $this_group, $session_id)) {
                // Group name
                $groupNameClass = null;
                if ($this_group['status'] == 0) {
                    $groupNameClass = 'muted';
                }

                $group_name = '<a class="'.$groupNameClass.'" href="group_space.php?'.api_get_cidreq(true, false).'&gidReq='.$this_group['id'].'">'.
                    Security::remove_XSS($this_group['name']).'</a> ';
                if (!empty($user_id) && !empty($this_group['id_tutor']) && $user_id == $this_group['id_tutor']) {
                    $group_name .= Display::label(get_lang('OneMyGroups'), 'success');
                } elseif ($isMember) {
                    $group_name .= Display::label(get_lang('MyGroup'), 'success');
                }

                if (api_is_allowed_to_edit() && !empty($this_group['session_name'])) {
                    $group_name .= ' ('.$this_group['session_name'].')';
                }
                $group_name .= $session_img;
                $row[] = $group_name.'<br />'.stripslashes(trim($this_group['description']));
            } else {
                if ($hideGroup === 'true') {
                    continue;
                }
                $row[] = $this_group['name'].'<br />'.stripslashes(trim($this_group['description']));
            }

            // Tutor name
            $tutor_info = null;

            if (count($tutorsids_of_group) > 0) {
                foreach ($tutorsids_of_group as $tutor_id) {
                    $tutor = api_get_user_info($tutor_id);
                    $username = api_htmlentities(sprintf(get_lang('LoginX'), $tutor['username']), ENT_QUOTES);
                    if (api_get_setting('show_email_addresses') === 'true') {
                        $tutor_info .= Display::tag(
                            'span',
                            Display::encrypted_mailto_link($tutor['mail'], api_get_person_name($tutor['firstName'], $tutor['lastName'])),
                            array('title'=>$username)
                        ).', ';
                    } else {
                        if (api_is_allowed_to_edit()) {
                            $tutor_info .= Display::tag(
                                'span',
                                Display::encrypted_mailto_link($tutor['mail'], api_get_person_name($tutor['firstName'], $tutor['lastName'])),
                                array('title'=>$username)
                            ).', ';
                        } else {
                            $tutor_info .= Display::tag(
                                'span',
                                api_get_person_name($tutor['firstName'], $tutor['lastName']),
                                array('title'=>$username)
                            ).', ';
                        }
                    }
                }
            }

            $tutor_info = api_substr($tutor_info, 0, api_strlen($tutor_info) - 2);
            $row[] = $tutor_info;

            // Max number of members in group
            $max_members = $this_group['maximum_number_of_members'] == self::MEMBER_PER_GROUP_NO_LIMIT ? ' ' : ' / '.$this_group['maximum_number_of_members'];

            // Number of members in group
            $row[] = $this_group['number_of_members'].$max_members;

            // Self-registration / unregistration
            if (!api_is_allowed_to_edit(false, true)) {
                if (self :: is_self_registration_allowed($user_id, $this_group['iid'])) {
                    $row[] = '<a class = "btn btn-default" href="group.php?'.api_get_cidreq().'&category='.$category_id.'&action=self_reg&group_id='.$this_group['id'].'" onclick="javascript:if(!confirm('."'".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES, $charset))."'".')) return false;">'.get_lang('GroupSelfRegInf').'</a>';
                } elseif (self :: is_self_unregistration_allowed($user_id, $this_group['iid'])) {
                    $row[] = '<a class = "btn btn-default" href="group.php?'.api_get_cidreq().'&category='.$category_id.'&action=self_unreg&group_id='.$this_group['id'].'" onclick="javascript:if(!confirm('."'".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES, $charset))."'".')) return false;">'.get_lang('GroupSelfUnRegInf').'</a>';
                } else {
                    $row[] = '-';
                }
            }

            $url = api_get_path(WEB_CODE_PATH).'group/';
            // Edit-links
            if (api_is_allowed_to_edit(false, true) &&
                !(api_is_course_coach() && intval($this_group['session_id']) != $session_id)
            ) {
                $edit_actions = '<a href="'.$url.'settings.php?'.api_get_cidreq(true, false).'&gidReq='.$this_group['id'].'"  title="'.get_lang('Edit').'">'.
                    Display::return_icon('edit.png', get_lang('EditGroup'),'',ICON_SIZE_SMALL).'</a>&nbsp;';

                if ($this_group['status'] == 1) {
                    $edit_actions .= '<a href="' . api_get_self() . '?' . api_get_cidreq(true,false) . '&category=' . $category_id . '&action=set_invisible&id=' . $this_group['id'] . '" title="' . get_lang('Hide') . '">' .
                        Display::return_icon('visible.png', get_lang('Hide'), '', ICON_SIZE_SMALL) . '</a>&nbsp;';
                } else {
                    $edit_actions .= '<a href="' . api_get_self() . '?' . api_get_cidreq(true, false) . '&category=' . $category_id . '&action=set_visible&id=' . $this_group['id'] . '" title="' . get_lang('Show') . '">' .
                        Display::return_icon('invisible.png', get_lang('Show'), '', ICON_SIZE_SMALL) . '</a>&nbsp;';
                }

                $edit_actions .= '<a href="'.$url.'member_settings.php?'.api_get_cidreq(true, false).'&gidReq='.$this_group['id'].'"  title="'.get_lang('GroupMembers').'">'.
                    Display::return_icon('user.png', get_lang('GroupMembers'), '', ICON_SIZE_SMALL).'</a>&nbsp;';

                $edit_actions .= '<a href="'.$url.'group_overview.php?action=export&type=xls&'.api_get_cidreq(true, false).'&id='.$this_group['id'].'"  title="'.get_lang('ExportUsers').'">'.
                    Display::return_icon('export_excel.png', get_lang('Export'), '', ICON_SIZE_SMALL).'</a>&nbsp;';

                $edit_actions .= '<a href="'.api_get_self().'?'.api_get_cidreq(true, false).'&category='.$category_id.'&action=fill_one&id='.$this_group['id'].'" onclick="javascript: if(!confirm('."'".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES))."'".')) return false;" title="'.get_lang('FillGroup').'">'.
                    Display::return_icon('fill.png',get_lang('FillGroup'),'',ICON_SIZE_SMALL).'</a>&nbsp;';

                $edit_actions .= '<a href="'.api_get_self().'?'.api_get_cidreq(true, false).'&category='.$category_id.'&action=delete_one&id='.$this_group['id'].'" onclick="javascript: if(!confirm('."'".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES))."'".')) return false;" title="'.get_lang('Delete').'">'.
                    Display::return_icon('delete.png', get_lang('Delete'),'',ICON_SIZE_SMALL).'</a>&nbsp;';

                $row[] = $edit_actions;
            }
            if (!empty($this_group['nbMember'])) {
                $totalRegistered = $totalRegistered + $this_group['nbMember'];
            }
            $group_data[] = $row;
        } // end loop

        // If no groups then don't show the table (only for students)
        if (!api_is_allowed_to_edit(true, false)) {
            if (empty($group_data)) {
                return '';
            }
        }

        $table = new SortableTableFromArrayConfig(
            $group_data,
            1,
            20,
            'group_category_'.$category_id
        );
        $table->set_additional_parameters(array('category' => $category_id));
        $column = 0;
        if (api_is_allowed_to_edit(false, true) and count($group_list) > 1) {
            $table->set_header($column++, '', false);
        }
        $table->set_header($column++, get_lang('Groups'));
        $table->set_header($column++, get_lang('GroupTutor'));
        $table->set_header($column++, get_lang('Registered'), false);

        if (!api_is_allowed_to_edit(false, true)) {
            // If self-registration allowed
            $table->set_header($column++, get_lang('GroupSelfRegistration'), false);
        }

        if (api_is_allowed_to_edit(false, true)) {
            // Only for course administrator
            $table->set_header($column++, get_lang('Modify'), false);
            $form_actions = array();
            $form_actions['fill_selected'] = get_lang('FillGroup');
            $form_actions['empty_selected'] = get_lang('EmptyGroup');
            $form_actions['delete_selected'] = get_lang('Delete');
            if (count($group_list) > 1) {
                $table->set_form_actions($form_actions, 'group');
            }
        }

        return $table->return_table();
    }

    /**
     * @param array $groupData
     * @param bool $deleteNotInArray
     * @return array
     */
    public static function importCategoriesAndGroupsFromArray($groupData, $deleteNotInArray = false)
    {
        $result = array();
        $elementsFound = array(
            'categories' => array(),
            'groups' => array()
        );

        $groupCategories = GroupManager::get_categories();

        if (empty($groupCategories)) {
            $result['error'][] = get_lang('CreateACategory');
            return $result;
        }

        foreach ($groupData as $data) {
            $isCategory = empty($data['group']) ? true : false;
            if ($isCategory) {
                $categoryInfo = self::getCategoryByTitle($data['category']);
                $categoryId = $categoryInfo['id'];

                if (!empty($categoryInfo)) {
                    // Update
                    self::update_category(
                        $categoryId,
                        $data['category'],
                        $data['description'],
                        $data['doc_state'],
                        $data['work_state'],
                        $data['calendar_state'],
                        $data['announcements_state'],
                        $data['forum_state'],
                        $data['wiki_state'],
                        $data['chat_state'],
                        $data['self_reg_allowed'],
                        $data['self_unreg_allowed'],
                        $data['max_student'],
                        $data['groups_per_user']
                    );
                    $data['category_id'] = $categoryId;
                    $result['updated']['category'][] = $data;
                } else {

                    // Add
                    $categoryId = self::create_category(
                        $data['category'],
                        $data['description'],
                        $data['doc_state'],
                        $data['work_state'],
                        $data['calendar_state'],
                        $data['announcements_state'],
                        $data['forum_state'],
                        $data['wiki_state'],
                        $data['chat_state'],
                        $data['self_reg_allowed'],
                        $data['self_unreg_allowed'],
                        $data['max_student'],
                        $data['groups_per_user']
                    );

                    if ($categoryId) {
                        $data['category_id'] = $categoryId;
                        $result['added']['category'][] = $data;
                    }
                }
                $elementsFound['categories'][] = $categoryId;
            } else {
                $groupInfo = self::getGroupByName($data['group']);
                $categoryInfo = self::getCategoryByTitle($data['category']);
                $categoryId = null;
                if (!empty($categoryInfo)) {
                    $categoryId = $categoryInfo['id'];
                } else {
                    if (!empty($groupCategories) && isset($groupCategories[0])) {
                        $defaultGroupCategory = $groupCategories[0];
                        $categoryId = $defaultGroupCategory['id'];
                    }
                }

                if (empty($groupInfo)) {

                    // Add
                    $groupId = self::create_group(
                        $data['group'],
                        $categoryId,
                        null,
                        $data['max_students']
                    );

                    if ($groupId) {
                        self::set_group_properties(
                            $groupId,
                            $data['group'],
                            $data['description'],
                            $data['max_students'],
                            $data['doc_state'],
                            $data['work_state'],
                            $data['calendar_state'],
                            $data['announcements_state'],
                            $data['forum_state'],
                            $data['wiki_state'],
                            $data['chat_state'],
                            $data['self_reg_allowed'],
                            $data['self_unreg_allowed'],
                            $categoryId
                        );
                        $data['group_id'] = $groupId;
                        $result['added']['group'][] = $data;
                    }
                } else {
                    // Update
                    $groupId = $groupInfo['id'];
                    self::set_group_properties(
                        $groupId,
                        $data['group'],
                        $data['description'],
                        $data['max_students'],
                        $data['doc_state'],
                        $data['work_state'],
                        $data['calendar_state'],
                        $data['announcements_state'],
                        $data['forum_state'],
                        $data['wiki_state'],
                        $data['chat_state'],
                        $data['self_reg_allowed'],
                        $data['self_unreg_allowed'],
                        $categoryId
                    );

                    $data['group_id'] = $groupId;
                    $result['updated']['group'][] = $data;
                }

                $students = isset($data['students']) ? explode(',', $data['students']) : null;
                if (!empty($students)) {
                    $studentUserIdList = array();
                    foreach ($students as $student) {
                        $userInfo = api_get_user_info_from_username($student);
                        $studentUserIdList[] = $userInfo['user_id'];
                    }
                    self::subscribe_users($studentUserIdList, $groupId);
                }

                $tutors = isset($data['tutors']) ? explode(',', $data['tutors']) : null;
                if (!empty($tutors)) {
                    $tutorIdList = array();
                    foreach ($tutors as $tutor) {
                        $userInfo = api_get_user_info_from_username($tutor);
                        $tutorIdList[] = $userInfo['user_id'];
                    }
                    self::subscribe_tutors($tutorIdList, $groupId);
                }

                $elementsFound['groups'][] = $groupId;
            }
        }

        if ($deleteNotInArray) {
            // Check categories
            $categories = GroupManager::get_categories();
            foreach ($categories as $category) {
                if (!in_array($category['id'], $elementsFound['categories'])) {
                    GroupManager::delete_category($category['id']);
                    $category['category'] = $category['title'];
                    $result['deleted']['category'][] = $category;
                }
            }

            $groups = GroupManager::get_groups();
            foreach ($groups as $group) {
                if (!in_array($group['iid'], $elementsFound['groups'])) {
                    GroupManager::delete_groups($group['iid']);
                    $group['group'] = $group['name'];
                    $result['deleted']['group'][] = $group;
                }
            }
        }

        return $result;
    }

    /**
     * Export all categories/group from a course to an array.
     * This function works only in a context of a course.
     * @param int $groupId
     * @param bool $loadUsers
     * @return array
     */
    public static function exportCategoriesAndGroupsToArray($groupId = null, $loadUsers = false)
    {
        $data = array();
        $data[] = array(
            'category',
            'group',
            'description',
            'announcements_state',
            'calendar_state',
            'chat_state',
            'doc_state',
            'forum_state',
            'work_state',
            'wiki_state',
            'max_student',
            'self_reg_allowed',
            'self_unreg_allowed',
            'groups_per_user'
        );

        $count = 1;

        if ($loadUsers) {
            $data[0][] = 'students';
            $data[0][] = 'tutors';
        }

        if ($loadUsers == false) {
            $categories = GroupManager::get_categories();

            foreach ($categories as $categoryInfo) {
                $data[$count] = array(
                    $categoryInfo['title'],
                    null,
                    $categoryInfo['description'],
                    $categoryInfo['announcements_state'],
                    $categoryInfo['calendar_state'],
                    $categoryInfo['chat_state'],
                    $categoryInfo['doc_state'],
                    $categoryInfo['forum_state'],
                    $categoryInfo['work_state'],
                    $categoryInfo['wiki_state'],
                    $categoryInfo['max_student'],
                    $categoryInfo['self_reg_allowed'],
                    $categoryInfo['self_unreg_allowed'],
                    $categoryInfo['groups_per_user']
                );
                $count++;
            }
        }

        $groups = GroupManager::get_group_list();

        foreach ($groups as $groupInfo) {
            $categoryTitle = null;
            $categoryInfo = GroupManager::get_category($groupInfo['category_id']);
            $groupSettings = GroupManager::get_group_properties($groupInfo['id']);
            if (!empty($categoryInfo)) {
                $categoryTitle = $categoryInfo['title'];
            }

            $users = GroupManager::getStudents($groupInfo['iid']);
            $userList = array();
            foreach ($users as $user) {
                $user = api_get_user_info($user['user_id']);
                $userList[] = $user['username'];
            }

            $tutors = GroupManager::getTutors($groupInfo['iid']);
            $tutorList = array();
            foreach ($tutors as $user) {
                $user = api_get_user_info($user['user_id']);
                $tutorList[] = $user['username'];
            }

            $userListToString = null;
            if (!empty($userList)) {
                $userListToString = implode(',', $userList);
            }

            $tutorListToString = null;
            if (!empty($tutorList)) {
                $tutorListToString = implode(',', $tutorList);
            }

            $data[$count] = array(
                $categoryTitle,
                $groupSettings['name'],
                $groupSettings['description'],
                $groupSettings['announcements_state'],
                $groupSettings['calendar_state'],
                $groupSettings['chat_state'],
                $groupSettings['doc_state'],
                $groupSettings['forum_state'],
                $groupSettings['work_state'],
                $groupSettings['wiki_state'],
                $groupSettings['maximum_number_of_students'],
                $groupSettings['self_registration_allowed'],
                $groupSettings['self_unregistration_allowed'],
                null
            );

            if ($loadUsers) {
                $data[$count][] = $userListToString;
                $data[$count][] = $tutorListToString;
            }

            if (!empty($groupId)) {
                if ($groupId == $groupInfo['id']) {
                    break;
                }
            }
            $count++;
        }

        return $data;
    }

    /**
     * @param string $default
     */
    static function getSettingBar($default)
    {
        $activeSettings = null;
        $activeTutor = null;
        $activeMember = null;

        switch($default) {
            case 'settings':
                $activeSettings = 'active';
                break;
            case'tutor':
                $activeTutor = 'active';
                break;
            case 'member':
                $activeMember = 'active';
                break;
        }

        $url = api_get_path(WEB_CODE_PATH).'group/%s?'.api_get_cidreq();

        echo '
            <ul class="toolbar-groups nav nav-tabs">
                <li class="'.$activeSettings.'">
                    <a href="'.sprintf($url, 'settings.php').'">
                    '.Display::return_icon('settings.png').' '.get_lang('Settings').'
                    </a>
                </li>
                <li class="'.$activeMember.'">
                    <a href="'.sprintf($url, 'member_settings.php').'">
                    '.Display::return_icon('user.png').' '.get_lang('GroupMembers').'
                    </a>
                </li>
                <li class="'.$activeTutor.'">
                    <a href="'.sprintf($url, 'tutor_settings.php').'">
                    '.Display::return_icon('teacher.png').' '.get_lang('GroupTutors').'
                    </a>
                </li>
            </ul>';
    }

    /**
     * @param int $courseId
     * @param string $keyword
     * @return string
     */
    public static function getOverview($courseId, $keyword = null)
    {
        $content = null;
        $categories = GroupManager::get_categories();
        if (!empty($categories)) {

            foreach ($categories as $category) {
                if (api_get_setting('allow_group_categories') == 'true') {
                    $content .= '<h2>'.$category['title'].'</h2>';
                }
                if (!empty($keyword)) {
                    $groups = GroupManager::getGroupListFilterByName($keyword, $category['id'], $courseId);
                } else {
                    $groups = GroupManager::get_group_list($category['id']);
                }

                if (empty($groups)) {
                    $groups = GroupManager::get_group_list();
                }

                $content .= '<ul>';
                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        $content .= '<li>';
                        $content .= Display::tag('h3', Security::remove_XSS($group['name']));
                        $users = GroupManager::getTutors($group['iid']);
                        if (!empty($users)) {
                            $content .= '<ul>';
                            $content .= "<li>".Display::tag('h4', get_lang('Tutors'))."</li><ul>";
                            foreach ($users as $user) {
                                $user_info = api_get_user_info($user['user_id']);
                                $content .= '<li title="'.$user_info['username'].'">'.
                                    $user_info['complete_name_with_username'].
                                '</li>';
                            }
                            $content .= '</ul>';
                            $content .= '</ul>';
                        }

                        $users = GroupManager::getStudents($group['iid']);
                        if (!empty($users)) {
                            $content .= '<ul>';
                            $content .= "<li>".Display::tag('h4', get_lang('Students'))."</li><ul>";
                            foreach ($users as $user) {
                                $user_info = api_get_user_info($user['user_id']);
                                $content .= '<li title="'.$user_info['username'].'">'.
                                    $user_info['complete_name_with_username'].
                                    '</li>';
                            }
                            $content .= '</ul>';
                            $content .= '</ul>';
                        }
                        $content .= '</li>';
                    }
                }
                $content .= '</ul>';
            }
        }

        return $content;
    }

    /**
     * Returns the search form
     * @return string
     */
    public static function getSearchForm()
    {
        $url = api_get_path(WEB_CODE_PATH).'group/group_overview.php?'.api_get_cidreq();
        $form = new FormValidator(
            'search_groups',
            'get',
            $url,
            null,
            array('class' => 'form-search'),
            FormValidator::LAYOUT_INLINE
        );
        $form->addElement('text', 'keyword');
        $form->addButtonSearch();
        return $form->toHtml();
    }

    /**
     * @param int $groupId
     * @param int $status
     */
    public static function setStatus($groupId, $status)
    {
        $groupInfo = self::get_group_properties($groupId);

        $courseId = api_get_course_int_id();
        if (!empty($groupInfo)) {
            $table = Database::get_course_table(TABLE_GROUP);
            $params = array(
                'status' => intval($status)
            );
            Database::update(
                $table,
                $params,
                array('c_id = ? AND id = ?' => array($courseId, $groupId))
            );
        }
    }

    /**
     * @param int $groupId
     */
    public static function setVisible($groupId)
    {
        self::setStatus($groupId, 1);
    }

    /**
     * @param int $groupId
     */
    public static function setInvisible($groupId)
    {
        self::setStatus($groupId, 0);
    }
}
