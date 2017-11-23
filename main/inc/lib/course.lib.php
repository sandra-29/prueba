<?php
/* For licensing terms, see /license.txt*/

use Chamilo\CoreBundle\Entity\ExtraField as EntityExtraField;
use ChamiloSession as Session;
use Chamilo\CourseBundle\Component\CourseCopy\CourseBuilder;
use Chamilo\CourseBundle\Component\CourseCopy\CourseRestorer;

/**
 * Class CourseManager
 *
 * This is the course library for Chamilo.
 *
 * All main course functions should be placed here.
 *
 * Many functions of this library deal with providing support for
 * virtual/linked/combined courses (this was already used in several universities
 * but not available in standard Chamilo).
 *
 * There are probably some places left with the wrong code.
 *
 * @package chamilo.library
 */
class CourseManager
{
    const MAX_COURSE_LENGTH_CODE = 40;
    /** This constant is used to show separate user names in the course
     * list (userportal), footer, etc */
    const USER_SEPARATOR = ' |';
    const COURSE_FIELD_TYPE_CHECKBOX = 10;
    public $columns = array();

    /**
     * Creates a course
     * @param   array $params columns in the main.course table
     *
     * @return  mixed  false if the course was not created, array with the course info
     */
    public static function create_course($params, $authorId = 0)
    {
        global $_configuration;
        // Check portal limits
        $access_url_id = 1;
        if (api_get_multiple_access_url()) {
            $access_url_id = api_get_current_access_url_id();
        }

        $authorId = empty($authorId) ? api_get_user_id() : (int) $authorId;

        if (isset($_configuration[$access_url_id]) && is_array($_configuration[$access_url_id])) {
            $return = self::checkCreateCourseAccessUrlParam(
                $_configuration,
                $access_url_id,
                'hosting_limit_courses',
                'PortalCoursesLimitReached'
            );
            if ($return != false) {
                return $return;
            }
            $return = self::checkCreateCourseAccessUrlParam(
                $_configuration,
                $access_url_id,
                'hosting_limit_active_courses',
                'PortalActiveCoursesLimitReached'
            );
            if ($return != false) {
                return $return;
            }
        }

        if (empty($params['title'])) {
            return false;
        }

        if (empty($params['wanted_code'])) {
            $params['wanted_code'] = $params['title'];
            // Check whether the requested course code has already been occupied.
            $substring = api_substr($params['title'], 0, self::MAX_COURSE_LENGTH_CODE);
            if ($substring === false || empty($substring)) {
                return false;
            } else {
                $params['wanted_code'] = CourseManager::generate_course_code($substring);
            }
        }

        // Create the course keys
        $keys = AddCourse::define_course_keys($params['wanted_code']);

        $params['exemplary_content'] = isset($params['exemplary_content']) ? $params['exemplary_content'] : false;

        if (count($keys)) {
            $params['code'] = $keys['currentCourseCode'];
            $params['visual_code'] = $keys['currentCourseId'];
            $params['directory'] = $keys['currentCourseRepository'];

            $course_info = api_get_course_info($params['code']);

            if (empty($course_info)) {
                $course_id = AddCourse::register_course($params);
                $course_info = api_get_course_info_by_id($course_id);

                if (!empty($course_info)) {
                    self::fillCourse($course_info, $params, $authorId);

                    return $course_info;
                }
            }
        }

        return false;
    }

    /**
     * Returns all the information of a given course code
     * @param string $course_code , the course code
     * @return array with all the fields of the course table
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @assert ('') === false
     */
    public static function get_course_information($course_code)
    {
        return Database::fetch_array(
            Database::query(
                "SELECT *, id as real_id FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code='" . Database::escape_string($course_code) . "'"), 'ASSOC'
        );
    }

    /**
     * Returns a list of courses. Should work with quickform syntax
     * @param    integer $from Offset (from the 7th = '6'). Optional.
     * @param    integer $howmany Number of results we want. Optional.
     * @param    int $orderby The column we want to order it by. Optional, defaults to first column.
     * @param    string $orderdirection The direction of the order (ASC or DESC). Optional, defaults to ASC.
     * @param    int $visibility The visibility of the course, or all by default.
     * @param    string $startwith If defined, only return results for which the course *title* begins with this string
     * @param    string $urlId The Access URL ID, if using multiple URLs
     * @param    bool $alsoSearchCode An extension option to indicate that we also want to search for course codes (not *only* titles)
     * @param    array $conditionsLike
     * @return array
     */
    public static function get_courses_list(
        $from = 0,
        $howmany = 0,
        $orderby = 1,
        $orderdirection = 'ASC',
        $visibility = -1,
        $startwith = '',
        $urlId = null,
        $alsoSearchCode = false,
        $conditionsLike = array()
    ) {
        $sql = "SELECT course.* FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course ";

        if (!empty($urlId)) {
            $table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
            $sql .= " INNER JOIN $table url ON (url.c_id = course.id) ";
        }

        if (!empty($startwith)) {
            $sql .= "WHERE (title LIKE '" . Database::escape_string($startwith) . "%' ";
            if ($alsoSearchCode) {
                $sql .= "OR code LIKE '" . Database::escape_string($startwith) . "%' ";
            }
            $sql .= ') ';
            if ($visibility !== -1 && $visibility == strval(intval($visibility))) {
                $sql .= " AND visibility = $visibility ";
            }
        } else {
            $sql .= "WHERE 1 ";
            if ($visibility !== -1 && $visibility == strval(intval($visibility))) {
                $sql .= " AND visibility = $visibility ";
            }
        }

        if (!empty($urlId)) {
            $urlId = intval($urlId);
            $sql .= " AND access_url_id= $urlId";
        }

        $allowedFields = array(
            'title',
            'code'
        );

        if (count($conditionsLike) > 0) {
            $sql .= ' AND ';
            $temp_conditions = array();
            foreach ($conditionsLike as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    continue;
                }
                $field = Database::escape_string($field);
                $value = Database::escape_string($value);
                $simple_like = false;
                if ($simple_like) {
                    $temp_conditions[] = $field . " LIKE '$value%'";
                } else {
                    $temp_conditions[] = $field . ' LIKE \'%' . $value . '%\'';
                }
            }
            $condition = ' AND ';
            if (!empty($temp_conditions)) {
                $sql .= implode(' ' . $condition . ' ', $temp_conditions);
            }
        }

        if (!empty($orderby)) {
            $sql .= " ORDER BY " . Database::escape_string($orderby) . " ";
        } else {
            $sql .= " ORDER BY 1 ";
        }

        if (!in_array($orderdirection, array('ASC', 'DESC'))) {
            $sql .= 'ASC';
        } else {
            $sql .= ($orderdirection == 'ASC' ? 'ASC' : 'DESC');
        }

        if (!empty($howmany) && is_int($howmany) and $howmany > 0) {
            $sql .= ' LIMIT ' . Database::escape_string($howmany);
        } else {
            $sql .= ' LIMIT 1000000'; //virtually no limit
        }
        if (!empty($from)) {
            $from = intval($from);
            $sql .= ' OFFSET ' . intval($from);
        } else {
            $sql .= ' OFFSET 0';
        }

        $data = [];
        $res = Database::query($sql);
        if (Database::num_rows($res) > 0) {
            while ($row = Database::fetch_array($res, 'ASSOC')) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Returns the access settings of the course:
     * which visibility;
     * whether subscribing is allowed;
     * whether unsubscribing is allowed.
     *
     * @param string $course_code , the course code
     * @todo for more consistency: use course_info call from database API
     * @return array with int fields "visibility", "subscribe", "unsubscribe"
     * @assert ('') === false
     */
    public static function get_access_settings($course_code)
    {
        return Database::fetch_array(
            Database::query(
                "SELECT visibility, subscribe, unsubscribe
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code = '" . Database::escape_string($course_code) . "'"
            )
        );
    }

    /**
     * Returns the status of a user in a course, which is COURSEMANAGER or STUDENT.
     * @param   int $user_id
     * @param   string $course_code
     *
     * @return int|bool the status of the user in that course (or false if the user is not in that course)
     */
    public static function get_user_in_course_status($user_id, $course_code)
    {
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];
        if (empty($courseId)) {
            return false;
        }
        $result = Database::fetch_array(
            Database::query(
                "SELECT status FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE
                    c_id  = $courseId AND
                    user_id = " . intval($user_id)
            )
        );

        return $result['status'];
    }

    /**
     * @param int $userId
     * @param int $courseId
     *
     * @return mixed
     */
    public static function getUserCourseInfo($userId, $courseId)
    {

        $result = Database::fetch_array(
            Database::query("
                SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE
                    c_id  = '" . intval($courseId). "' AND
                    user_id = " . intval($userId)
            )
        );

        return $result;
    }

    /**
     * @param int  $userId
     * @param int  $courseId
     * @param bool $isTutor
     *
     * @return bool
     */
    public static function updateUserCourseTutor($userId, $courseId, $isTutor)
    {
        $table = Database::escape_string(TABLE_MAIN_COURSE_USER);

        $courseId = intval($courseId);
        $isTutor = intval($isTutor);

        $sql = "UPDATE $table SET is_tutor = '".$isTutor."'
			    WHERE
				    user_id = '".$userId."' AND
				    c_id = '".$courseId."'";

        $result = Database::query($sql);

        if (Database::affected_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $user_id
     * @param string $course_code
     *
     * @return mixed
     */
    public static function get_tutor_in_course_status($user_id, $course_code)
    {
        $result = Database::fetch_array(
            Database::query("
                SELECT is_tutor
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE
                    course_code = '" . Database::escape_string($course_code) . "' AND
                    user_id = " . intval($user_id)
            )
        );

        return $result['is_tutor'];
    }

    /**
     * Unsubscribe one or more users from a course
     *
     * @param   mixed   user_id or an array with user ids
     * @param   string  course code
     * @param   int     session id
     * @assert ('', '') === false
     *
     */
    public static function unsubscribe_user($user_id, $course_code, $session_id = 0)
    {
        if (!is_array($user_id)) {
            $user_id = array($user_id);
        }

        if (count($user_id) == 0) {
            return;
        }

        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        $user_list = array();

        // Cleaning the $user_id variable
        if (is_array($user_id)) {
            $new_user_id_list = array();
            foreach ($user_id as $my_user_id) {
                $new_user_id_list[] = intval($my_user_id);
            }
            $new_user_id_list = array_filter($new_user_id_list);
            $user_list = $new_user_id_list;
            $user_ids = implode(',', $new_user_id_list);
        } else {
            $user_ids = intval($user_id);
            $user_list[] = $user_id;
        }

        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];

        // Unsubscribe user from all groups in the course.
        $sql = "DELETE FROM " . Database::get_course_table(TABLE_GROUP_USER) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);
        $sql = "DELETE FROM " . Database::get_course_table(TABLE_GROUP_TUTOR) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        // Erase user student publications (works) in the course - by André Boivin

        if (!empty($user_list)) {
            require_once api_get_path(SYS_CODE_PATH) . 'work/work.lib.php';
            foreach ($user_list as $userId) {
                // Getting all work from user
                $workList = getWorkPerUser($userId);
                if (!empty($workList)) {
                    foreach ($workList as $work) {
                        $work = $work['work'];
                        // Getting user results
                        if (!empty($work->user_results)) {
                            foreach ($work->user_results as $workSent) {
                                deleteWorkItem($workSent['id'], $course_info);
                            }
                        }
                    }
                }
            }
        }

        // Unsubscribe user from all blogs in the course.
        Database::query("DELETE FROM " . Database::get_course_table(TABLE_BLOGS_REL_USER) . " WHERE c_id = $course_id AND  user_id IN (" . $user_ids . ")");
        Database::query("DELETE FROM " . Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER) . " WHERE c_id = $course_id AND  user_id IN (" . $user_ids . ")");

        // Deleting users in forum_notification and mailqueue course tables
        $sql = "DELETE FROM  " . Database::get_course_table(TABLE_FORUM_NOTIFICATION) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        $sql = "DELETE FROM " . Database::get_course_table(TABLE_FORUM_MAIL_QUEUE) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        // Unsubscribe user from the course.
        if (!empty($session_id)) {

            // Delete in table session_rel_course_rel_user
            $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE
                        session_id ='" . $session_id . "' AND
                        c_id = '" . $course_id . "' AND
                        user_id IN ($user_ids)";
            Database::query($sql);

            foreach ($user_list as $uid) {
                // check if a user is register in the session with other course
                $sql = "SELECT user_id FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                        WHERE session_id='$session_id' AND user_id='$uid'";
                $rs = Database::query($sql);

                if (Database::num_rows($rs) == 0) {
                    // Delete in table session_rel_user
                    $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                            WHERE
                                session_id ='" . $session_id . "' AND
                                user_id = '$uid' AND
                                relation_type<>" . SESSION_RELATION_TYPE_RRHH . "";
                    Database::query($sql);
                }
            }

            // Update the table session
            $sql = "SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                    WHERE session_id = '" . $session_id . "' AND relation_type <> " . SESSION_RELATION_TYPE_RRHH;
            $row = Database::fetch_array(Database::query($sql));
            $count = $row[0];
            // number of users by session
            $sql = "UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION) . " SET nbr_users = '$count'
                    WHERE id = '" . $session_id . "'";
            Database::query($sql);

            // Update the table session_rel_course
            $sql = "SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE session_id = '$session_id' AND c_id = '$course_id' AND status<>2";
            $row = Database::fetch_array(@Database::query($sql));
            $count = $row[0];

            // number of users by session and course
            $sql = "UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE) . "
                    SET nbr_users = '$count'
                    WHERE session_id = '$session_id' AND c_id = '$course_id'";
            Database::query($sql);

        } else {
            $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                    WHERE
                        user_id IN (" . $user_ids . ") AND
                        relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND
                        c_id = '" . $course_id . "'";
            Database::query($sql);

            // add event to system log
            $user_id = api_get_user_id();

            Event::addEvent(
                LOG_UNSUBSCRIBE_USER_FROM_COURSE,
                LOG_COURSE_CODE,
                $course_code,
                api_get_utc_datetime(),
                $user_id
            );

            foreach ($user_list as $userId) {
                $userInfo = api_get_user_info($userId);
                Event::addEvent(
                    LOG_UNSUBSCRIBE_USER_FROM_COURSE,
                    LOG_USER_OBJECT,
                    $userInfo,
                    api_get_utc_datetime(),
                    $user_id
                );
            }
        }
    }

    /**
     * Subscribe a user to a course. No checks are performed here to see if
     * course subscription is allowed.
     * @param   int     User ID
     * @param   string  Course code
     * @param   int     Status (STUDENT, COURSEMANAGER, COURSE_ADMIN, NORMAL_COURSE_MEMBER)
     * @return  bool    True on success, false on failure
     * @see add_user_to_course
     * @assert ('', '') === false
     */
    public static function subscribe_user(
        $user_id,
        $course_code,
        $status = STUDENT,
        $session_id = 0,
        $userCourseCategoryId = 0
    ) {
        if ($user_id != strval(intval($user_id))) {
            return false; //detected possible SQL injection
        }

        $course_code = Database::escape_string($course_code);
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];
        $courseCode = $courseInfo['code'];

        $userCourseCategoryId = intval($userCourseCategoryId);

        if (empty($user_id) || empty ($course_code)) {
            return false;
        }

        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        $status = ($status == STUDENT || $status == COURSEMANAGER) ? $status : STUDENT;
        //$role_id = ($status == COURSEMANAGER) ? COURSE_ADMIN : NORMAL_COURSE_MEMBER;

        // A preliminary check whether the user has bben already registered on the platform.
        if (Database::num_rows(Database::query(
                "SELECT status FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                WHERE user_id = '$user_id' ")) == 0
        ) {
            return false; // The user has not been registered to the platform.
        }

        // Check whether the user has not been already subscribed to the course.

        if (empty($session_id)) {
            if (Database::num_rows(Database::query("
                    SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                    WHERE user_id = '$user_id' AND relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND c_id = '$courseId'")) > 0
            ) {
                // The user has been already subscribed to the course.
                return false;
            }
        }

        if (!empty($session_id)) {
            SessionManager::subscribe_users_to_session_course(array($user_id), $session_id, $courseCode);
        } else {
            CourseManager::add_user_to_course($user_id, $courseCode, $status);

            // Add event to the system log
            Event::addEvent(
                LOG_SUBSCRIBE_USER_TO_COURSE,
                LOG_COURSE_CODE,
                $course_code,
                api_get_utc_datetime(),
                api_get_user_id()
            );

            $user_info = api_get_user_info($user_id);
            Event::addEvent(
                LOG_SUBSCRIBE_USER_TO_COURSE,
                LOG_USER_OBJECT,
                $user_info,
                api_get_utc_datetime(),
                api_get_user_id()
            );
        }

        return true;
    }

    /**
     * Get the course id based on the original id and field name in the
     * extra fields. Returns 0 if course was not found
     *
     * @param string $original_course_id_value
     * @param string $original_course_id_name
     * @return int Course id
     *
     * @assert ('', '') === false
     */
    public static function get_course_code_from_original_id($original_course_id_value, $original_course_id_name)
    {
        $t_cfv = Database::get_main_table(TABLE_EXTRA_FIELD_VALUES);
        $table_field = Database::get_main_table(TABLE_EXTRA_FIELD);
        $extraFieldType = EntityExtraField::COURSE_FIELD_TYPE;

        $original_course_id_value = Database::escape_string($original_course_id_value);
        $original_course_id_name = Database::escape_string($original_course_id_name);

        $sql = "SELECT item_id
                FROM $table_field cf
                INNER JOIN $t_cfv cfv
                ON cfv.field_id=cf.id
                WHERE
                    variable = '$original_course_id_name' AND
                    value = '$original_course_id_value' AND
                    cf.extra_field_type = $extraFieldType
                ";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if ($row) {
            return $row->item_id;
        } else {
            return 0;
        }
    }

    /**
     * Gets the course code from the course id. Returns null if course id was not found
     *
     * @param int $id Course id
     * @return string Course code
     * @assert ('') === false
     */
    public static function get_course_code_from_course_id($id)
    {
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $id = intval($id);
        $sql = "SELECT code FROM $table WHERE id = '$id' ";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if ($row) {
            return $row->code;
        } else {
            return null;
        }
    }

    /**
     * Subscribe a user $user_id to a course defined by $courseCode.
     * @author Hugues Peeters
     * @author Roan Embrechts
     *
     * @param  int $user_id the id of the user
     * @param  string $courseCode the course code
     * @param  int $status (optional) The user's status in the course
     * @param  int The user category in which this subscription will be classified
     *
     * @return false|string true if subscription succeeds, boolean false otherwise.
     * @assert ('', '') === false
     */
    public static function add_user_to_course($user_id, $courseCode, $status = STUDENT, $userCourseCategoryId = 0)
    {
        $debug = false;
        $user_table = Database::get_main_table(TABLE_MAIN_USER);
        $course_table = Database::get_main_table(TABLE_MAIN_COURSE);
        $course_user_table = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $status = ($status == STUDENT || $status == COURSEMANAGER) ? $status : STUDENT;
        if (empty($user_id) || empty($courseCode) || ($user_id != strval(intval($user_id)))) {
            return false;
        }

        $courseCode = Database::escape_string($courseCode);
        $courseInfo = api_get_course_info($courseCode);
        $courseId = $courseInfo['real_id'];

        // Check in advance whether the user has already been registered on the platform.
        $sql = "SELECT status FROM " . $user_table . " WHERE user_id = $user_id ";
        if (Database::num_rows(Database::query($sql)) == 0) {
            if ($debug) {
                error_log('The user has not been registered to the platform');
            }
            return false; // The user has not been registered to the platform.
        }

        // Check whether the user has already been subscribed to this course.
        $sql = "SELECT * FROM $course_user_table
                WHERE
                    user_id = $user_id AND
                    relation_type <> " . COURSE_RELATION_TYPE_RRHH . " AND
                    c_id = $courseId";
        if (Database::num_rows(Database::query($sql)) > 0) {
            if ($debug) {
                error_log('The user has been already subscribed to the course');
            }
            return false; // The user has been subscribed to the course.
        }

        if (!api_is_course_admin()) {
            // Check in advance whether subscription is allowed or not for this course.
            $sql = "SELECT code, visibility FROM $course_table
                    WHERE id = $courseId AND subscribe = '" . SUBSCRIBE_NOT_ALLOWED . "'";
            if (Database::num_rows(Database::query($sql)) > 0) {
                if ($debug) {
                    error_log('Subscription is not allowed for this course');
                }
                return false; // Subscription is not allowed for this course.
            }
        }

        // Ok, subscribe the user.
        $max_sort = api_max_sort_value('0', $user_id);
        $params = [
            'c_id' => $courseId,
            'user_id' => $user_id,
            'status' => $status,
            'sort' => $max_sort + 1,
            'relation_type' => 0,
            'user_course_cat' => $userCourseCategoryId
        ];
        $insertId = Database::insert($course_user_table, $params);

        return $insertId;
    }

    /**
     * Add the user $userId visibility to the course $courseCode in the catalogue.
     * @author David Nos (https://github.com/dnos)
     *
     * @param  int $userId the id of the user
     * @param  string $courseCode the course code
     * @param  int $visible (optional) The course visibility in the catalogue to the user (1=visible, 0=invisible)
     *
     * @return boolean true if added succesfully, false otherwise.
     */
    public static function addUserVisibilityToCourseInCatalogue($userId, $courseCode, $visible = 1)
    {
        $debug = false;
        $userTable = Database::get_main_table(TABLE_MAIN_USER);
        $courseUserTable = Database::get_main_table(TABLE_MAIN_COURSE_CATALOGUE_USER);

        if (empty($userId) || empty($courseCode) || ($userId != strval(intval($userId)))) {
            return false;
        }

        $courseCode = Database::escape_string($courseCode);
        $courseInfo = api_get_course_info($courseCode);
        $courseId = $courseInfo['real_id'];

        // Check in advance whether the user has already been registered on the platform.
        $sql = "SELECT status FROM " . $userTable . " WHERE user_id = $userId ";
        if (Database::num_rows(Database::query($sql)) == 0) {
            if ($debug) {
                error_log('The user has not been registered to the platform');
            }
            return false; // The user has not been registered to the platform.
        }

        // Check whether the user has already been registered to the course visibility in the catalogue.
        $sql = "SELECT * FROM $courseUserTable
                WHERE
                    user_id = $userId AND
                    visible = " . $visible . " AND
                    c_id = $courseId";
        if (Database::num_rows(Database::query($sql)) > 0) {
            if ($debug) {
                error_log('The user has been already registered to the course visibility in the catalogue');
            }
            return true; // The visibility of the user to the course in the catalogue does already exist.
        }

        // Register the user visibility to course in catalogue.
        $params = [
            'user_id' => $userId,
            'c_id' => $courseId,
            'visible' => $visible
        ];
        $insertId = Database::insert($courseUserTable, $params);

        return $insertId;
    }


    /**
     * Remove the user $userId visibility to the course $courseCode in the catalogue.
     * @author David Nos (https://github.com/dnos)
     *
     * @param  int $userId the id of the user
     * @param  string $courseCode the course code
     * @param  int $visible (optional) The course visibility in the catalogue to the user (1=visible, 0=invisible)
     *
     * @return boolean true if removed succesfully or register not found, false otherwise.
     */
    public static function removeUserVisibilityToCourseInCatalogue($userId, $courseCode, $visible = 1)
    {
        $courseUserTable = Database::get_main_table(TABLE_MAIN_COURSE_CATALOGUE_USER);

        if (empty($userId) || empty($courseCode) || ($userId != strval(intval($userId)))) {
            return false;
        }

        $courseCode = Database::escape_string($courseCode);
        $courseInfo = api_get_course_info($courseCode);
        $courseId = $courseInfo['real_id'];

        // Check whether the user has already been registered to the course visibility in the catalogue.
        $sql = "SELECT * FROM $courseUserTable
                WHERE
                    user_id = $userId AND
                    visible = " . $visible . " AND
                    c_id = $courseId";
        if (Database::num_rows(Database::query($sql)) > 0) {
            $cond = array(
                'user_id = ? AND c_id = ? AND visible = ? ' => array(
                    $userId,
                    $courseId,
                    $visible
                )
            );
            return Database::delete($courseUserTable, $cond);
        } else {
            return true; // Register does not exist
        }
    }


    /**
     *    Checks wether a parameter exists.
     *    If it doesn't, the function displays an error message.
     *
     * @return boolean if parameter is set and not empty, false otherwise
     * @todo move function to better place, main_api ?
     */
    public static function check_parameter($parameter, $error_message)
    {
        if (empty($parameter)) {
            Display::display_normal_message($error_message);
            return false;
        }
        return true;
    }

    /**
     *    Lets the script die when a parameter check fails.
     * @todo move function to better place, main_api ?
     */
    public static function check_parameter_or_fail($parameter, $error_message)
    {
        if (!self::check_parameter($parameter, $error_message)) {
            die();
        }
    }

    /**
     * @return boolean if there already are one or more courses
     *  with the same code OR visual_code (visualcode), false otherwise
     */
    public static function course_code_exists($wanted_course_code)
    {
        $wanted_course_code = Database::escape_string($wanted_course_code);
        $sql = "SELECT COUNT(*) as number
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code = '$wanted_course_code' OR visual_code = '$wanted_course_code'";
        $result = Database::fetch_array(Database::query($sql));

        return $result['number'] > 0;
    }

    /**
     * Get course list as coach
     *
     * @param int $user_id
     * @param bool $include_courses_in_sessions
     * @return array Course list
     *
     **/
    public static function get_course_list_as_coach($user_id, $include_courses_in_sessions = false)
    {
        // 1. Getting courses as teacher (No session)
        $courses_temp = CourseManager::get_course_list_of_user_as_course_admin($user_id);
        $courseList = array();

        if (!empty($courses_temp)) {
            foreach ($courses_temp as $course_item) {
                $courseList[0][$course_item['code']] = $course_item['code'];
            }
        }

        //2. Include courses in sessions
        if ($include_courses_in_sessions) {
            $sessions = Tracking::get_sessions_coached_by_user($user_id);

            if (!empty($sessions)) {
                foreach ($sessions as $session_item) {
                    $courses = Tracking:: get_courses_followed_by_coach($user_id, $session_item['id']);
                    if (is_array($courses)) {
                        foreach ($courses as $course_item) {
                            $courseList[$session_item['id']][$course_item] = $course_item;
                        }
                    }
                }
            }
        }

        return $courseList;
    }

    /**
     * @param int $user_id
     * @param bool $include_sessions
     * @return array
     */
    public static function get_user_list_from_courses_as_coach($user_id, $include_sessions = true)
    {
        $students_in_courses = array();
        $sessions = CourseManager::get_course_list_as_coach($user_id, true);

        if (!empty($sessions)) {
            foreach ($sessions as $session_id => $courses) {
                if (!$include_sessions) {
                    if (!empty($session_id)) {
                        continue;
                    }
                }
                if (empty($session_id)) {
                    foreach ($courses as $course_code) {
                        $students_in_course = CourseManager::get_user_list_from_course_code($course_code);

                        foreach ($students_in_course as $user_item) {
                            //Only students
                            if ($user_item['status_rel'] == STUDENT) {
                                $students_in_courses[$user_item['user_id']] = $user_item['user_id'];
                            }
                        }
                    }
                } else {
                    $students_in_course = SessionManager::get_users_by_session($session_id, '0');
                    if (is_array($students_in_course)) {
                        foreach ($students_in_course as $user_item) {
                            $students_in_courses[$user_item['user_id']] = $user_item['user_id'];
                        }
                    }
                }
            }
        }

        $students = Tracking:: get_student_followed_by_coach($user_id);
        if (!empty($students_in_courses)) {
            if (!empty($students)) {
                $students = array_merge($students, $students_in_courses);
            } else {
                $students = $students_in_courses;
            }
        }

        if (!empty($students)) {
            $students = array_unique($students);
        }
        return $students;
    }

    /**
     * @param int $user_id
     * @param string $startsWith Optional
     * @return array An array with the course info of all the courses (real and virtual)
     * of which the current user is course admin.
     */
    public static function get_course_list_of_user_as_course_admin($user_id, $startsWith = null)
    {
        if ($user_id != strval(intval($user_id))) {
            return array();
        }

        // Definitions database tables and variables
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $user_id = intval($user_id);
        $data = array();

        $sql = "SELECT
                    course.code,
                    course.title,
                    course.id,
                    course.id as real_id,
                    course.category_code
                FROM $tbl_course_user as course_rel_user
                INNER JOIN $tbl_course as course
                ON course.id = course_rel_user.c_id
                WHERE
                    course_rel_user.user_id='$user_id' AND
                    course_rel_user.status='1'
        ";

        if (api_get_multiple_access_url()) {
            $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
            $access_url_id = api_get_current_access_url_id();
            if ($access_url_id != -1) {
                $sql = "
                    SELECT
                        course.code,
                        course.title,
                        course.id,
                        course.id as real_id
                    FROM $tbl_course_user as course_rel_user
                    INNER JOIN $tbl_course as course
                    ON course.id = course_rel_user.c_id
                    INNER JOIN $tbl_course_rel_access_url course_rel_url
                    ON (course_rel_url.c_id = course.id)
                    WHERE
                        access_url_id = $access_url_id  AND
                        course_rel_user.user_id = '$user_id' AND
                        course_rel_user.status = '1'
                ";
            }
        }

        if (!empty($startsWith)) {
            $startsWith = Database::escape_string($startsWith);

            $sql .= " AND (course.title LIKE '$startsWith%' OR course.code LIKE '$startsWith%')";
        }

        $sql .= ' ORDER BY course.title';

        $result_nb_cours = Database::query($sql);
        if (Database::num_rows($result_nb_cours) > 0) {
            while ($row = Database::fetch_array($result_nb_cours, 'ASSOC')) {
                $data[$row['id']] = $row;
            }
        }

        return $data;
    }

    /**
     * @param int $userId
     * @param array $courseInfo
     * @return boolean|null
     */
    public static function isUserSubscribedInCourseAsDrh($userId, $courseInfo)
    {
        $userId = intval($userId);

        if (!api_is_drh()) {
            return false;
        }

        if (empty($courseInfo) || empty($userId)) {
            return false;
        }

        $courseId = intval($courseInfo['real_id']);
        $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $sql = "SELECT * FROM $table
                WHERE
                    user_id = $userId AND
                    relation_type = " . COURSE_RELATION_TYPE_RRHH . " AND
                    c_id = $courseId";

        $result = Database::fetch_array(Database::query($sql));

        if (!empty($result)) {
            // The user has been registered in this course.
            return true;
        }
    }

    /**
     * Check if user is subscribed inside a course
     * @param  int $user_id
     * @param  string $course_code , if this parameter is null, it'll check for all courses
     * @param  bool $in_a_session True for checking inside sessions too, by default is not checked
     * @return bool   $session_id true if the user is registered in the course, false otherwise
     */
    public static function is_user_subscribed_in_course(
        $user_id,
        $course_code = null,
        $in_a_session = false,
        $session_id = null
    ) {
        $user_id = intval($user_id);

        if (empty($session_id)) {
            $session_id = api_get_session_id();
        } else {
            $session_id = intval($session_id);
        }

        $condition_course = '';
        if (isset($course_code)) {
            $courseInfo = api_get_course_info($course_code);
            if (empty($courseInfo)) {
                return false;
            }
            $courseId = $courseInfo['real_id'];
            $condition_course = ' AND c_id = ' . $courseId;
        }

        $sql = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE
                    user_id = $user_id AND
                    relation_type<>" . COURSE_RELATION_TYPE_RRHH . "
                    $condition_course ";

        $result = Database::fetch_array(Database::query($sql));

        if (!empty($result)) {
            // The user has been registered in this course.
            return true;
        }

        if (!$in_a_session) {
            // The user has not been registered in this course.
            return false;
        }

        $tableSessionCourseUser = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $sql = 'SELECT 1 FROM ' . $tableSessionCourseUser .
            ' WHERE user_id = ' . $user_id . ' ' . $condition_course;
        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        $sql = 'SELECT 1 FROM ' . $tableSessionCourseUser .
            ' WHERE user_id = ' . $user_id . ' AND status=2 ' . $condition_course;
        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        $sql = 'SELECT 1 FROM ' . Database::get_main_table(TABLE_MAIN_SESSION) .
            ' WHERE id = ' . $session_id . ' AND id_coach=' . $user_id;

        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        return false;
    }

    /**
     *    Is the user a teacher in the given course?
     *
     * @param integer $user_id , the id (int) of the user
     * @param $course_code , the course code
     *
     * @return boolean if the user is a teacher in the course, false otherwise
     */
    public static function is_course_teacher($user_id, $course_code)
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        $result = Database::query(
            'SELECT status FROM ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) .
            ' WHERE c_id = ' . $courseId . ' AND user_id = ' . $user_id . ''
        );

        if (Database::num_rows($result) > 0) {
            return Database::result($result, 0, 'status') == 1;
        }

        return false;
    }

    /**
     *    Is the user subscribed in the real course or linked courses?
     *
     * @param int the id of the user
     * @param int $courseId
     * @deprecated linked_courses definition doesn't exists
     * @return boolean if the user is registered in the real course or linked courses, false otherwise
     */
    public static function is_user_subscribed_in_real_or_linked_course($user_id, $courseId, $session_id = '')
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $courseId = intval($courseId);

        if ($session_id == '') {
            $result = Database::fetch_array(
                Database::query(
                    "SELECT *
                    FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course
                    LEFT JOIN " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " course_user
                    ON course.id = course_user.c_id
                    WHERE
                        course_user.user_id = '$user_id' AND
                        course_user.relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND
                        ( course.id = '$courseId')"
                )
            );
            return !empty($result);
        }

        $session_id = intval($session_id);

        // From here we trust session id.
        // Is he/she subscribed to the session's course?

        // A user?
        if (Database::num_rows(Database::query("SELECT user_id
                FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                WHERE session_id='" . $session_id . "'
                AND user_id ='$user_id'"))
        ) {
            return true;
        }

        // A course coach?
        if (Database::num_rows(Database::query("SELECT user_id
                FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                WHERE session_id='" . $session_id . "'
                AND user_id = '$user_id' AND status = 2
                AND c_id ='$courseId'"))
        ) {
            return true;
        }

        // A session coach?
        if (Database::num_rows(Database::query("SELECT id_coach
                FROM " . Database::get_main_table(TABLE_MAIN_SESSION) . " AS session
                WHERE session.id='" . $session_id . "'
                AND id_coach='$user_id'"))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return user info array of all users registered in a course
     * This only returns the users that are registered in this actual course, not linked courses.
     * @param string $course_code
     * @param int $session_id
     * @param string $limit
     * @param string $order_by the field to order the users by.
     * Valid values are 'lastname', 'firstname', 'username', 'email', 'official_code' OR a part of a SQL statement
     * that starts with ORDER BY ...
     * @param integer|null $filter_by_status if using the session_id: 0 or 2 (student, coach),
     * if using session_id = 0 STUDENT or COURSEMANAGER
     * @param boolean|null $return_count
     * @param bool $add_reports
     * @param bool $resumed_report
     * @param array $extra_field
     * @param array $courseCodeList
     * @param array $userIdList
     * @param string $filterByActive
     * @param array $sessionIdList
     * @return array|int
     */
    public static function get_user_list_from_course_code(
        $course_code = null,
        $session_id = 0,
        $limit = null,
        $order_by = null,
        $filter_by_status = null,
        $return_count = null,
        $add_reports = false,
        $resumed_report = false,
        $extra_field = array(),
        $courseCodeList = array(),
        $userIdList = array(),
        $filterByActive = null,
        $sessionIdList = array()
    ) {
        $course_table = Database::get_main_table(TABLE_MAIN_COURSE);
        $sessionTable = Database::get_main_table(TABLE_MAIN_SESSION);

        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);
        $courseInfo = api_get_course_info($course_code);
        $courseId = 0;
        if (!empty($courseInfo)) {
            $courseId = $courseInfo['real_id'];
        }

        $where = array();
        if (empty($order_by)) {
            $order_by = 'user.lastname, user.firstname';
            if (api_is_western_name_order()) {
                $order_by = 'user.firstname, user.lastname';
            }
        }

        // if the $order_by does not contain 'ORDER BY'
        // we have to check if it is a valid field that can be sorted on
        if (!strstr($order_by, 'ORDER BY')) {
            if (!empty($order_by)) {
                $order_by = 'ORDER BY ' . $order_by;
            } else {
                $order_by = '';
            }
        }

        $filter_by_status_condition = null;

        if (!empty($session_id) || !empty($sessionIdList)) {
            $sql = 'SELECT DISTINCT
                        user.user_id,
                        user.email,
                        session_course_user.status as status_session,
                        session_id,
                        user.*,
                        course.*,
                        session.name as session_name
                    ';
            if ($return_count) {
                $sql = " SELECT COUNT(user.user_id) as count";
            }

            $sessionCondition = " session_course_user.session_id = $session_id";
            if (!empty($sessionIdList)) {
                $sessionIdListTostring = implode("','", array_map('intval', $sessionIdList));
                $sessionCondition = " session_course_user.session_id IN ('$sessionIdListTostring') ";
            }

            $courseCondition = " course.id = $courseId";
            if (!empty($courseCodeList)) {
                $courseCodeListForSession = array_map(array('Database', 'escape_string'), $courseCodeList);
                $courseCodeListForSession = implode('","', $courseCodeListForSession);
                $courseCondition = ' course.code IN ("' . $courseCodeListForSession . '")  ';
            }

            $sql .= ' FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user ';
            $sql .= " LEFT JOIN ".Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " as session_course_user
                      ON
                        user.id = session_course_user.user_id AND
                        $sessionCondition
                        INNER JOIN $course_table course 
                        ON session_course_user.c_id = course.id AND
                        $courseCondition
                        INNER JOIN $sessionTable session 
                        ON session_course_user.session_id = session.id
                   ";
            $where[] = ' session_course_user.c_id IS NOT NULL ';

            // 2 = coach
            // 0 = student
            if (isset($filter_by_status)) {
                $filter_by_status = intval($filter_by_status);
                $filter_by_status_condition = " session_course_user.status = $filter_by_status AND ";
            }
        } else {
            if ($return_count) {
                $sql = " SELECT COUNT(*) as count";
            } else {
                if (empty($course_code)) {
                    $sql = 'SELECT DISTINCT
                                course.title,
                                course.code,
                                course_rel_user.status as status_rel,
                                user.id as user_id,
                                user.email,
                                course_rel_user.is_tutor,
                                user.*  ';
                } else {
                    $sql = 'SELECT DISTINCT
                                course_rel_user.status as status_rel,
                                user.id as user_id,
                                user.email,
                                course_rel_user.is_tutor,
                                user.*  ';
                }
            }

            $sql .= ' FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user '
                  . ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) . ' as course_rel_user
                      ON 
                        user.id = course_rel_user.user_id AND
                        course_rel_user.relation_type <> ' . COURSE_RELATION_TYPE_RRHH . '  '
                  . " INNER JOIN $course_table course ON course_rel_user.c_id = course.id ";

            if (!empty($course_code)) {
                $sql .= ' AND course_rel_user.c_id = "' . $courseId . '"';
            }
            $where[] = ' course_rel_user.c_id IS NOT NULL ';

            if (isset($filter_by_status) && is_numeric($filter_by_status)) {
                $filter_by_status = intval($filter_by_status);
                $filter_by_status_condition = " course_rel_user.status = $filter_by_status AND ";
            }
        }

        $multiple_access_url = api_get_multiple_access_url();
        if ($multiple_access_url) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER) . ' au
                      ON (au.user_id = user.id) ';
        }

        $extraFieldWasAdded = false;
        if ($return_count && $resumed_report) {
            foreach ($extra_field as $extraField) {
                $extraFieldInfo = UserManager::get_extra_field_information_by_name($extraField);
                if (!empty($extraFieldInfo)) {
                    $fieldValuesTable = Database::get_main_table(TABLE_EXTRA_FIELD_VALUES);
                    $sql .= ' LEFT JOIN '.$fieldValuesTable.' as ufv
                            ON (
                                user.id = ufv.item_id AND
                                (field_id = '.$extraFieldInfo['id'].' OR field_id IS NULL)
                            )';
                    $extraFieldWasAdded = true;
                }
            }
        }

        $sql .= ' WHERE ' . $filter_by_status_condition . ' ' . implode(' OR ', $where);

        if ($multiple_access_url) {
            $current_access_url_id = api_get_current_access_url_id();
            $sql .= " AND (access_url_id =  $current_access_url_id ) ";
        }

        if ($return_count && $resumed_report && $extraFieldWasAdded) {
            $sql .= ' AND field_id IS NOT NULL GROUP BY value ';
        }

        if (!empty($courseCodeList)) {
            $courseCodeList = array_map(array('Database', 'escape_string'), $courseCodeList);
            $courseCodeList = implode('","', $courseCodeList);
            if (empty($sessionIdList)) {
                $sql .= ' AND course.code IN ("'.$courseCodeList.'")';
            }
        }

        if (!empty($userIdList)) {
            $userIdList = array_map('intval', $userIdList);
            $userIdList = implode('","', $userIdList);
            $sql .= ' AND user.id IN ("' . $userIdList . '")';
        }

        if (isset($filterByActive)) {
            $filterByActive = intval($filterByActive);
            $sql .= ' AND user.active = ' . $filterByActive;
        }

        $sql .= ' ' . $order_by . ' ' . $limit;

        $rs = Database::query($sql);
        $users = array();

        $extra_fields = UserManager::get_extra_fields(0, 100, null, null, true, true);

        $counter = 1;
        $count_rows = Database::num_rows($rs);

        if ($return_count && $resumed_report) {
            return $count_rows;
        }

        $table_user_field_value = Database::get_main_table(TABLE_EXTRA_FIELD_VALUES);
        $tableExtraField = Database::get_main_table(TABLE_EXTRA_FIELD);
        if ($count_rows) {
            while ($user = Database::fetch_array($rs)) {
                if ($return_count) {
                    return $user['count'];
                }
                $report_info = array();

                $user_info = $user;
                $user_info['status'] = $user['status'];

                if (isset($user['is_tutor'])) {
                    $user_info['is_tutor'] = $user['is_tutor'];
                }

                if (!empty($session_id)) {
                    $user_info['status_session'] = $user['status_session'];
                }

                $sessionId = isset($user['session_id']) ? $user['session_id'] : 0;
                $course_code = isset($user['code']) ? $user['code'] : null;

                if ($add_reports) {
                    if ($resumed_report) {
                        $extra = array();

                        if (!empty($extra_fields)) {
                            foreach ($extra_fields as $extra) {
                                if (in_array($extra['1'], $extra_field)) {
                                    $user_data = UserManager::get_extra_user_data_by_field(
                                        $user['user_id'],
                                        $extra['1']
                                    );
                                    break;
                                }
                            }
                        }

                        $row_key = '-1';
                        $name = '-';

                        if (!empty($extra)) {
                            if (!empty($user_data[$extra['1']])) {
                                $row_key = $user_data[$extra['1']];
                                $name = $user_data[$extra['1']];
                                $users[$row_key]['extra_'.$extra['1']] = $name;
                            }
                        }

                        $users[$row_key]['training_hours'] += Tracking::get_time_spent_on_the_course(
                            $user['user_id'],
                            $courseId,
                            $sessionId
                        );

                        $users[$row_key]['count_users'] += $counter;

                        $registered_users_with_extra_field = CourseManager::getCountRegisteredUsersWithCourseExtraField(
                            $name,
                            $tableExtraField,
                            $table_user_field_value
                        );

                        $users[$row_key]['count_users_registered'] = $registered_users_with_extra_field;
                        $users[$row_key]['average_hours_per_user'] = $users[$row_key]['training_hours'] / $users[$row_key]['count_users'];

                        $category = Category:: load(
                            null,
                            null,
                            $course_code,
                            null,
                            null,
                            $sessionId
                        );

                        if (!isset($users[$row_key]['count_certificates'])) {
                            $users[$row_key]['count_certificates'] = 0;
                        }

                        if (isset($category[0]) && $category[0]->is_certificate_available($user['user_id'])) {
                            $users[$row_key]['count_certificates']++;
                        }

                        foreach ($extra_fields as $extra) {
                            if ($extra['1'] == 'ruc') {
                                continue;
                            }

                            if (!isset($users[$row_key][$extra['1']])) {
                                $user_data = UserManager::get_extra_user_data_by_field($user['user_id'], $extra['1']);
                                if (!empty($user_data[$extra['1']])) {
                                    $users[$row_key][$extra['1']] = $user_data[$extra['1']];
                                }
                            }
                        }
                    } else {
                        $sessionName = !empty($sessionId) ? ' - '.$user['session_name'] : '';
                        $report_info['course'] = $user['title'].$sessionName;
                        $report_info['user'] = api_get_person_name($user['firstname'], $user['lastname']);
                        $report_info['email'] = $user['email'];
                        $report_info['time'] = api_time_to_hms(
                            Tracking::get_time_spent_on_the_course(
                                $user['user_id'],
                                $courseId,
                                $sessionId
                            )
                        );

                        $category = Category:: load(
                            null,
                            null,
                            $course_code,
                            null,
                            null,
                            $sessionId
                        );

                        $report_info['certificate'] = Display::label(get_lang('No'));
                        if (isset($category[0]) && $category[0]->is_certificate_available($user['user_id'])) {
                            $report_info['certificate'] = Display::label(get_lang('Yes'), 'success');
                        }

                        $progress = intval(
                            Tracking::get_avg_student_progress(
                                $user['user_id'],
                                $course_code,
                                array(),
                                $sessionId
                            )
                        );
                        $report_info['progress_100'] = $progress == 100 ? Display::label(get_lang('Yes'), 'success') : Display::label(get_lang('No'));
                        $report_info['progress'] = $progress . "%";

                        foreach ($extra_fields as $extra) {
                            $user_data = UserManager::get_extra_user_data_by_field($user['user_id'], $extra['1']);
                            $report_info[$extra['1']] = $user_data[$extra['1']];
                        }
                        $report_info['user_id'] = $user['user_id'];
                        $users[] = $report_info;
                    }
                } else {
                    $users[$user['user_id']] = $user_info;
                }
            }
        }

        return $users;
    }

    /**
     * @param bool $resumed_report
     * @param array $extra_field
     * @param array $courseCodeList
     * @param array $userIdList
     * @param array $sessionIdList
     * @return array|int
     */
    public static function get_count_user_list_from_course_code(
        $resumed_report = false,
        $extra_field = array(),
        $courseCodeList = array(),
        $userIdList = array(),
        $sessionIdList = array()
    ) {
        return self::get_user_list_from_course_code(
            null,
            0,
            null,
            null,
            null,
            true,
            false,
            $resumed_report,
            $extra_field,
            $courseCodeList,
            $userIdList,
            null,
            $sessionIdList
        );
    }

    /**
     * Gets subscribed users in a course or in a course/session
     *
     * @param   string $course_code
     * @param   int $session_id
     * @return  int
     */
    public static function get_users_count_in_course(
        $course_code,
        $session_id = 0,
        $status = null
    ) {
        // variable initialisation
        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);

        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        $sql = 'SELECT DISTINCT count(*) as count  FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user ';
        $where = array();
        if (!empty($session_id)) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . ' as session_course_user
                      ON
                        user.user_id = session_course_user.user_id AND
                        session_course_user.c_id = "' . $courseId . '" AND
                        session_course_user.session_id  = ' . $session_id;

            $where[] = ' session_course_user.c_id IS NOT NULL ';
        } else {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) . ' as course_rel_user
                        ON
                            user.user_id = course_rel_user.user_id AND
                            course_rel_user.relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND
                            course_rel_user.c_id = ' . $courseId ;
            $where[] = ' course_rel_user.c_id IS NOT NULL ';
        }

        $multiple_access_url = api_get_multiple_access_url();
        if ($multiple_access_url) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER) . '  au
                      ON (au.user_id = user.user_id) ';
        }

        $sql .= ' WHERE ' . implode(' OR ', $where);

        if ($multiple_access_url) {
            $current_access_url_id = api_get_current_access_url_id();
            $sql .= " AND (access_url_id =  $current_access_url_id ) ";
        }
        $rs = Database::query($sql);
        $count = 0;
        if (Database::num_rows($rs)) {
            $user = Database::fetch_array($rs);
            $count = $user['count'];
        }

        return $count;
    }

    /**
     * Get a list of coaches of a course and a session
     * @param   string  Course code
     * @param   int     Session ID
     * @return  array   List of users
     */
    public static function get_coach_list_from_course_code($course_code, $session_id)
    {
        if (empty($course_code) || empty($session_id)) {
            return array();
        }

        $course_code = Database::escape_string($course_code);
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        $session_id = intval($session_id);
        $users = array();

        // We get the coach for the given course in a given session.
        $sql = 'SELECT user_id FROM ' . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) .
               ' WHERE session_id ="' . $session_id . '" AND c_id="' . $courseId . '" AND status = 2';
        $rs = Database::query($sql);
        while ($user = Database::fetch_array($rs)) {
            $user_info = api_get_user_info($user['user_id']);
            $user_info['status'] = $user['status'];
            //$user_info['tutor_id'] = $user['tutor_id'];
            $user_info['email'] = $user['email'];
            $users[$user['user_id']] = $user_info;
        }

        $table = Database::get_main_table(TABLE_MAIN_SESSION);
        // We get the session coach.
        $sql = 'SELECT id_coach FROM ' . $table . ' WHERE id=' . $session_id;
        $rs = Database::query($sql);
        $session_id_coach = Database::result($rs, 0, 'id_coach');
        $user_info = api_get_user_info($session_id_coach);
        $user_info['status'] = $user['status'];
        //$user_info['tutor_id'] = $user['tutor_id'];
        $user_info['email'] = $user['email'];
        $users[$session_id_coach] = $user_info;

        return $users;
    }

    /**
     *  Return user info array of all users registered in a course
     *  This only returns the users that are registered in this actual course, not linked courses.
     *
     * @param string $course_code
     * @param boolean $with_session
     * @param integer $session_id
     * @param string $date_from
     * @param string $date_to
     * @param boolean $includeInvitedUsers Whether include the invited users
     * @param int $groupId
     * @return array with user id
     */
    public static function get_student_list_from_course_code(
        $course_code,
        $with_session = false,
        $session_id = 0,
        $date_from = null,
        $date_to = null,
        $includeInvitedUsers = true,
        $groupId = 0
    ) {
        $userTable = Database::get_main_table(TABLE_MAIN_USER);
        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];
        $students = array();

        if ($session_id == 0) {
            if (empty($groupId)) {
                // students directly subscribed to the course
                $sql = "SELECT *
                        FROM ".Database::get_main_table(TABLE_MAIN_COURSE_USER)." cu
                        INNER JOIN $userTable u
                        ON cu.user_id = u.user_id
                        WHERE c_id = '$courseId' AND cu.status = ".STUDENT;

                if (!$includeInvitedUsers) {
                    $sql .= " AND u.status != ".INVITEE;
                }
                $rs = Database::query($sql);
                while ($student = Database::fetch_array($rs)) {
                    $students[$student['user_id']] = $student;
                }
            } else {
                $students = GroupManager::get_users(
                    $groupId,
                    false,
                    null,
                    null,
                    false,
                    $courseInfo['real_id']
                );
                $students = array_flip($students);
            }
        }

        // students subscribed to the course through a session
        if ($with_session) {

            $joinSession = "";
            //Session creation date
            if (!empty($date_from) && !empty($date_to)) {
                $joinSession = "INNER JOIN " . Database::get_main_table(TABLE_MAIN_SESSION) . " s";
            }

            $sql_query = "SELECT *
                          FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " scu
                          $joinSession
                          INNER JOIN $userTable u ON scu.user_id = u.user_id
                          WHERE scu.c_id = '$courseId' AND scu.status <> 2";

            if (!empty($date_from) && !empty($date_to)) {
                $date_from = Database::escape_string($date_from);
                $date_to = Database::escape_string($date_to);
                $sql_query .= " AND s.access_start_date >= '$date_from' AND s.access_end_date <= '$date_to'";
            }

            if ($session_id != 0) {
                $sql_query .= ' AND scu.session_id = ' . $session_id;
            }

            if (!$includeInvitedUsers) {
                $sql_query .= " AND u.status != " . INVITEE;
            }

            $rs = Database::query($sql_query);
            while ($student = Database::fetch_array($rs)) {
                $students[$student['user_id']] = $student;
            }
        }

        return $students;
    }

    /**
     * Return user info array of all teacher-users registered in a course
     * This only returns the users that are registered in this actual course, not linked courses.
     *
     * @param string $course_code
     * @return array with user id
     */
    public static function get_teacher_list_from_course_code($course_code)
    {
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];
        if (empty($courseId)) {
            return false;
        }

        $sql = "SELECT DISTINCT
                    u.id as user_id,
                    u.lastname,
                    u.firstname,
                    u.email,
                    u.username,
                    u.status
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " cu
                INNER JOIN " . Database::get_main_table(TABLE_MAIN_USER) . " u
                ON (cu.user_id = u.id)
                WHERE
                    cu.c_id = $courseId AND
                    cu.status = 1 ";
        $rs = Database::query($sql);
        $teachers = array();
        while ($teacher = Database::fetch_array($rs)) {
            $teachers[$teacher['user_id']] = $teacher;
        }

        return $teachers;
    }


    /**
     * Return user info array of all teacher-users registered in a course
     * This only returns the users that are registered in this actual course, not linked courses.
     *
     * @param string $course_code
     * @return array with user id
     */
    public static function getTeachersFromCourseByCode($course_code)
    {
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];
        if (empty($courseId)) {
            return false;
        }

        $sql = "SELECT DISTINCT
                    u.id as user_id,
                    u.lastname,
                    u.firstname,
                    u.email,
                    u.username,
                    u.status
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " cu
                INNER JOIN " . Database::get_main_table(TABLE_MAIN_USER) . " u
                ON (cu.user_id = u.id)
                WHERE
                    cu.c_id = $courseId AND
                    cu.status = 1 ";
        $rs = Database::query($sql);
        $listTeachers = array();
        $teachers = array();
        while ($teacher = Database::fetch_array($rs)) {
            $userPicture = UserManager::getUserPicture($teacher['user_id'], USER_IMAGE_SIZE_SMALL);
            $teachers['id'] = $teacher['user_id'];
            $teachers['lastname'] = $teacher['lastname'];
            $teachers['firstname'] = $teacher['firstname'];
            $teachers['email'] = $teacher['email'];
            $teachers['username'] = $teacher['username'];
            $teachers['status'] = $teacher['status'];
            $teachers['avatar'] = $userPicture;
            $teachers['url'] = api_get_path(WEB_AJAX_PATH) . 'user_manager.ajax.php?a=get_user_popup&user_id=' . $teacher['user_id'];
            $listTeachers[]=$teachers;
        }
        return $listTeachers;
    }

    /**
     * Returns a string list of teachers assigned to the given course
     * @param string $course_code
     * @param string $separator between teachers names
     * @param bool $add_link_to_profile Whether to add a link to the teacher's profile
     * @return string List of teachers teaching the course
     */
    public static function get_teacher_list_from_course_code_to_string(
        $course_code,
        $separator = self::USER_SEPARATOR,
        $add_link_to_profile = false,
        $orderList = false
    ) {
        $teacher_list = self::get_teacher_list_from_course_code($course_code);
        $html = '';
        $list = array();
        if (!empty($teacher_list)) {
            foreach ($teacher_list as $teacher) {
                $teacher_name = api_get_person_name(
                    $teacher['firstname'],
                    $teacher['lastname']
                );
                if ($add_link_to_profile) {
                    $url = api_get_path(WEB_AJAX_PATH) . 'user_manager.ajax.php?a=get_user_popup&user_id=' . $teacher['user_id'];
                    $teacher_name = Display::url(
                        $teacher_name,
                        $url,
                        [
                            'class' => 'ajax',
                            'data-title' => $teacher_name
                        ]
                    );
                }
                $list[] = $teacher_name;
            }

            if (!empty($list)) {
                if ($orderList === true){
                    $html .= '<ul class="user-teacher">';
                    foreach ($list as $teacher){
                        $html .= Display::tag('li', Display::return_icon('teacher.png', $teacher, null, ICON_SIZE_TINY) . ' ' . $teacher);
                    }
                    $html .= '</ul>';
                } else {
                    $html .= array_to_string($list, $separator);
                }
            }
        }

        return $html;
    }

    /**
     * This function returns information about coachs from a course in session
     * @param int $session_id
     * @param int $courseId
     *
     * @return array    - array containing user_id, lastname, firstname, username
     *
     */
    public static function get_coachs_from_course($session_id = 0, $courseId = '')
    {
        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        if (!empty($courseId)) {
            $courseId = intval($courseId);
        } else {
            $courseId = api_get_course_int_id();
        }

        $tbl_user = Database:: get_main_table(TABLE_MAIN_USER);
        $tbl_session_course_user = Database:: get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $coaches = array();

        $sql = "SELECT DISTINCT u.user_id,u.lastname,u.firstname,u.username
                FROM $tbl_user u, $tbl_session_course_user scu
                WHERE
                    u.user_id = scu.user_id AND
                    scu.session_id = '$session_id' AND
                    scu.c_id = '$courseId' AND
                    scu.status = 2";
        $rs = Database::query($sql);

        if (Database::num_rows($rs) > 0) {
            while ($row = Database::fetch_array($rs)) {
                $completeName = api_get_person_name($row['firstname'], $row['lastname']);

                $coaches[] = $row + ['full_name' => $completeName];
            }

            return $coaches;
        } else {

            return false;
        }
    }

    /**
     * @param int $session_id
     * @param int $courseId
     * @param string $separator
     * @param bool $add_link_to_profile
     * @return string
     */
    public static function get_coachs_from_course_to_string(
        $session_id = 0,
        $courseId = null,
        $separator = self::USER_SEPARATOR,
        $add_link_to_profile = false,
        $orderList = false
    ) {
        $coachs_course = self::get_coachs_from_course($session_id, $courseId);
        $course_coachs = array();
        $html = '';
        if (is_array($coachs_course)) {
            foreach ($coachs_course as $coach_course) {
                $coach_name = api_get_person_name($coach_course['firstname'], $coach_course['lastname']);
                if ($add_link_to_profile) {
                    $url = api_get_path(WEB_AJAX_PATH) . 'user_manager.ajax.php?a=get_user_popup&user_id=' . $coach_course['user_id'];
                    $coach_name = Display::url(
                        $coach_name,
                        $url,
                        [
                            'class' => 'ajax',
                            'data-title' => $coach_name
                        ]
                    );
                }
                $course_coachs[] = $coach_name;
            }
        }
        $coaches_to_string = null;

        if (!empty($course_coachs)) {
            if ($orderList === true){
                $html .= '<ul class="user-coachs">';
                    foreach ($course_coachs as $coachs){
                        $html .= Display::tag('li', Display::return_icon('teacher.png', $coachs, null, ICON_SIZE_TINY) . ' ' . $coachs);
                    }
                $html .= '</ul>';
            } else {
                $coaches_to_string = array_to_string($course_coachs, $separator);
            }

        }

        return $html;
    }

    /**
     * Return user info array of all users registered in the specified course
     * this includes the users of the course itself and the users of all linked courses.
     *
     * @param string $course_code
     * @param bool $with_sessions
     * @param int $session_id
     * @return array with user info
     */
    public static function get_real_and_linked_user_list($course_code, $with_sessions = true, $session_id = 0)
    {
        $complete_user_list = array();

        //get users from real course
        $user_list = self::get_user_list_from_course_code($course_code, $session_id);
        foreach ($user_list as $this_user) {
            $complete_user_list[] = $this_user;
        }

        return $complete_user_list;
    }

    /**
     * Get the list of groups from the course
     * @param   string $course_code
     * @param   int $session_id Session ID (optional)
     * @param   integer $in_get_empty_group get empty groups (optional)
     * @return  array   List of groups info
     */
    public static function get_group_list_of_course($course_code, $session_id = 0, $in_get_empty_group = 0)
    {
        $course_info = api_get_course_info($course_code);

        if (empty($course_info)) {
            return array();
        }
        $course_id = $course_info['real_id'];

        if (empty($course_id)) {
            return array();
        }

        $group_list = array();
        $session_id != 0 ? $session_condition = ' WHERE g.session_id IN(1,' . intval($session_id) . ')' : $session_condition = ' WHERE g.session_id = 0';

        if ($in_get_empty_group == 0) {
            // get only groups that are not empty
            $sql = "SELECT DISTINCT g.id, g.iid, g.name
                    FROM " . Database::get_course_table(TABLE_GROUP) . " AS g
                    INNER JOIN " . Database::get_course_table(TABLE_GROUP_USER) . " gu
                    ON (g.id = gu.group_id AND g.c_id = $course_id AND gu.c_id = $course_id)
                    $session_condition
                    ORDER BY g.name";
        } else {
            // get all groups even if they are empty
            $sql = "SELECT g.id, g.name, g.iid 
                    FROM " . Database::get_course_table(TABLE_GROUP) . " AS g
                    $session_condition
                    AND c_id = $course_id";
        }
        $result = Database::query($sql);

        while ($group_data = Database::fetch_array($result)) {
            $group_data['userNb'] = GroupManager::number_of_students($group_data['iid'], $course_id);
            $group_list[$group_data['id']] = $group_data;
        }
        return $group_list;
    }

    /**
     * Delete a course
     * This function deletes a whole course-area from the platform. When the
     * given course is a virtual course, the database and directory will not be
     * deleted.
     * When the given course is a real course, also all virtual courses refering
     * to the given course will be deleted.
     * Considering the fact that we remove all traces of the course in the main
     * database, it makes sense to remove all tracking as well (if stats databases exist)
     * so that a new course created with this code would not use the remains of an older
     * course.
     *
     * @param string The code of the course to delete
     * @param string $code
     * @todo When deleting a virtual course: unsubscribe users from that virtual
     * course from the groups in the real course if they are not subscribed in
     * that real course.
     * @todo Remove globals
     */
    public static function delete_course($code)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $table_session_course = Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
        $table_session_course_user = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $table_course_survey = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY);
        $table_course_survey_question = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY_QUESTION);
        $table_course_survey_question_option = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY_QUESTION_OPTION);
        $table_course_rel_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        $table_stats_hotpots = Database::get_main_table(TABLE_STATISTIC_TRACK_E_HOTPOTATOES);
        $table_stats_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $table_stats_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);
        $table_stats_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ACCESS);
        $table_stats_lastaccess = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LASTACCESS);
        $table_stats_course_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $table_stats_online = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ONLINE);
        $table_stats_default = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DEFAULT);
        $table_stats_downloads = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DOWNLOADS);
        $table_stats_links = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LINKS);
        $table_stats_uploads = Database::get_main_table(TABLE_STATISTIC_TRACK_E_UPLOADS);

        $codeFiltered = Database::escape_string($code);
        $sql = "SELECT * FROM $table_course WHERE code='" . $codeFiltered . "'";
        $res = Database::query($sql);

        if (Database::num_rows($res) == 0) {
            return;
        }

        $sql = "SELECT * FROM $table_course
                WHERE code = '" . $codeFiltered . "'";
        $res = Database::query($sql);
        $course = Database::fetch_array($res);
        $courseId = $course['id'];

        $count = 0;
        if (api_is_multiple_url_enabled()) {
            $url_id = 1;
            if (api_get_current_access_url_id() != -1) {
                $url_id = api_get_current_access_url_id();
            }
            UrlManager::delete_url_rel_course($courseId, $url_id);
            $count = UrlManager::getCountUrlRelCourse($courseId);
        }

        if ($count == 0) {
            self::create_database_dump($code);

            $course_tables = AddCourse::get_course_tables();

            // Cleaning group categories
            $groupCategories = GroupManager::get_categories($course['code']);

            if (!empty($groupCategories)) {
                foreach ($groupCategories as $category) {
                    GroupManager::delete_category($category['id'], $course['code']);
                }
            }

            // Cleaning groups
            $groups = GroupManager::get_groups($courseId);
            if (!empty($groups)) {
                $groupList = array_column($groups, 'iid');
                foreach ($groupList as $groupId) {
                    GroupManager::delete_groups($groupId, $course['code']);
                }

            }

            // Cleaning c_x tables
            if (!empty($courseId)) {
                foreach ($course_tables as $table) {
                    $table = Database::get_course_table($table);
                    $sql = "DELETE FROM $table WHERE c_id = $courseId ";
                    Database::query($sql);
                }
            }

            $course_dir = api_get_path(SYS_COURSE_PATH) . $course['directory'];
            $archive_dir = api_get_path(SYS_ARCHIVE_PATH) . $course['directory'] . '_' . time();
            if (is_dir($course_dir)) {
                rename($course_dir, $archive_dir);
            }

            // Unsubscribe all users from the course
            $sql = "DELETE FROM $table_course_user WHERE c_id='" . $courseId . "'";
            Database::query($sql);
            // Delete the course from the sessions tables
            $sql = "DELETE FROM $table_session_course WHERE c_id='" . $courseId . "'";
            Database::query($sql);
            $sql = "DELETE FROM $table_session_course_user WHERE c_id='" . $courseId . "'";
            Database::query($sql);

            // Delete from Course - URL
            $sql = "DELETE FROM $table_course_rel_url WHERE c_id = '" . $courseId. "'";
            Database::query($sql);

            $sql = 'SELECT survey_id FROM ' . $table_course_survey . ' WHERE course_code="' . $codeFiltered . '"';
            $result_surveys = Database::query($sql);
            while ($surveys = Database::fetch_array($result_surveys)) {
                $survey_id = $surveys[0];
                $sql = 'DELETE FROM ' . $table_course_survey_question . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
                $sql = 'DELETE FROM ' . $table_course_survey_question_option . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
                $sql = 'DELETE FROM ' . $table_course_survey . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
            }

            // Delete the course from the stats tables

            $sql = "DELETE FROM $table_stats_hotpots WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_attempt WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_exercises WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_access WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_lastaccess WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_course_access WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_online WHERE c_id = $courseId";
            Database::query($sql);
            // Do not delete rows from track_e_default as these include course
            // creation and other important things that do not take much space
            // but give information on the course history
            //$sql = "DELETE FROM $table_stats_default WHERE c_id = $courseId";
            //Database::query($sql);
            $sql = "DELETE FROM $table_stats_downloads WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_links WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_uploads WHERE c_id = $courseId";
            Database::query($sql);

            // Delete the course from the database
            $sql = "DELETE FROM $table_course WHERE code = '" . $codeFiltered . "'";
            Database::query($sql);

            // delete extra course fields
            $extraFieldValues = new ExtraFieldValue('course');
            $extraFieldValues->deleteValuesByItem($courseId);

            // Add event to system log
            $user_id = api_get_user_id();
            Event::addEvent(
                LOG_COURSE_DELETE,
                LOG_COURSE_CODE,
                $code,
                api_get_utc_datetime(),
                $user_id,
                $courseId
            );
        }
    }

    /**
     * Creates a file called mysql_dump.sql in the course folder
     * @param $course_code The code of the course
     * @todo Implementation for single database
     */
    public static function create_database_dump($course_code)
    {
        $sql_dump = '';
        $course_code = Database::escape_string($course_code);
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "SELECT * FROM $table_course WHERE code = '$course_code'";
        $res = Database::query($sql);
        $course = Database::fetch_array($res);

        $course_tables = AddCourse::get_course_tables();

        if (!empty($course['id'])) {
            //Cleaning c_x tables
            foreach ($course_tables as $table) {
                $table = Database::get_course_table($table);
                $sql = "SELECT * FROM $table WHERE c_id = {$course['id']} ";
                $res_table = Database::query($sql);

                while ($row = Database::fetch_array($res_table, 'ASSOC')) {
                    $row_to_save = array();
                    foreach ($row as $key => $value) {
                        $row_to_save[$key] = $key . "='" . Database::escape_string($row[$key]) . "'";
                    }
                    $sql_dump .= "\nINSERT INTO $table SET " . implode(', ', $row_to_save) . ';';
                }
            }
        }

        if (is_dir(api_get_path(SYS_COURSE_PATH) . $course['directory'])) {
            $file_name = api_get_path(SYS_COURSE_PATH) . $course['directory'] . '/mysql_dump.sql';
            $handle = fopen($file_name, 'a+');
            if ($handle !== false) {
                fwrite($handle, $sql_dump);
                fclose($handle);
            } else {
                //TODO trigger exception in a try-catch
            }
        }
    }

    /**
     * Sort courses for a specific user ??
     * @param   int     User ID
     * @param   string  Course code
     * @param integer $user_id
     * @return  int     Minimum course order
     * @todo Review documentation
     */
    public static function userCourseSort($user_id, $course_code)
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $course_code = Database::escape_string($course_code);
        $TABLECOURSE = Database::get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $course_title = Database::result(Database::query('SELECT title FROM ' . $TABLECOURSE . ' WHERE code="' . $course_code . '"'),
            0, 0);

        $sql = 'SELECT course.code as code, course.title as title, cu.sort as sort
                FROM ' . $TABLECOURSUSER . ' as cu, ' . $TABLECOURSE . ' as course
                WHERE   course.id = cu.c_id AND user_id = "' . $user_id . '" AND
                        cu.relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND
                        user_course_cat = 0
                ORDER BY cu.sort';
        $result = Database::query($sql);

        $course_title_precedent = '';
        $counter = 0;
        $course_found = false;
        $course_sort = 1;

        if (Database::num_rows($result) > 0) {
            while ($courses = Database::fetch_array($result)) {
                if ($course_title_precedent == '') {
                    $course_title_precedent = $courses['title'];
                }
                if (api_strcasecmp($course_title_precedent, $course_title) < 0) {
                    $course_found = true;
                    $course_sort = $courses['sort'];
                    if ($counter == 0) {
                        $sql = 'UPDATE ' . $TABLECOURSUSER . '
                                SET sort = sort+1
                                WHERE
                                    user_id= "' . $user_id . '" AND
                                    relation_type<>' . COURSE_RELATION_TYPE_RRHH . '
                                    AND user_course_cat="0"
                                    AND sort > "' . $course_sort . '"';
                        $course_sort++;
                    } else {
                        $sql = 'UPDATE ' . $TABLECOURSUSER . ' SET sort = sort+1
                                WHERE
                                    user_id= "' . $user_id . '" AND
                                    relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND
                                    user_course_cat="0" AND
                                    sort >= "' . $course_sort . '"';
                    }
                    Database::query($sql);
                    break;

                } else {
                    $course_title_precedent = $courses['title'];
                }
                $counter++;
            }

            // We must register the course in the beginning of the list
            if (!$course_found) {
                $course_sort = Database::result(Database::query('SELECT min(sort) as min_sort FROM ' . $TABLECOURSUSER . ' WHERE user_id="' . $user_id . '" AND user_course_cat="0"'),
                    0, 0);
                Database::query('UPDATE ' . $TABLECOURSUSER . ' SET sort = sort+1 WHERE user_id= "' . $user_id . '" AND user_course_cat="0"');
            }
        }
        return $course_sort;
    }

    /**
     * check if course exists
     * @param string $course_code
     * @return integer if exists, false else
     */
    public static function course_exists($course_code)
    {
        $sql = 'SELECT 1 FROM ' . Database::get_main_table(TABLE_MAIN_COURSE) . '
                WHERE code="' . Database::escape_string($course_code) . '"';

        return Database::num_rows(Database::query($sql));
    }

    /**
     * Send an email to tutor after the auth-suscription of a student in your course
     * @author Carlos Vargas <carlos.vargas@dokeos.com>, Dokeos Latino
     * @param  int $user_id the id of the user
     * @param  string $courseId the course code
     * @param  bool $send_to_tutor_also
     * @return false|null we return the message that is displayed when the action is successful
     */
    public static function email_to_tutor($user_id, $courseId, $send_to_tutor_also = false)
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }
        $courseId = intval($courseId);
        $information = api_get_course_info_by_id($courseId);
        $course_code = $information['code'];

        $student = api_get_user_info($user_id);

        $name_course = $information['title'];
        $sql = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " 
                WHERE c_id ='" . $courseId . "'";

        // TODO: Ivan: This is a mistake, please, have a look at it. Intention here is diffcult to be guessed.
        //if ($send_to_tutor_also = true)
        // Proposed change:
        if ($send_to_tutor_also) {
            $sql .= " AND is_tutor=1";
        } else {
            $sql .= " AND status=1";
        }

        $result = Database::query($sql);
        while ($row = Database::fetch_array($result)) {
            $tutor = api_get_user_info($row['user_id']);
            $emailto = $tutor['email'];
            $emailsubject = get_lang('NewUserInTheCourse') . ': ' . $name_course;
            $emailbody = get_lang('Dear') . ': ' . api_get_person_name($tutor['firstname'], $tutor['lastname']) . "\n";
            $emailbody .= get_lang('MessageNewUserInTheCourse') . ': ' . $name_course . "\n";
            $emailbody .= get_lang('UserName') . ': ' . $student['username'] . "\n";
            if (api_is_western_name_order()) {
                $emailbody .= get_lang('FirstName') . ': ' . $student['firstname'] . "\n";
                $emailbody .= get_lang('LastName') . ': ' . $student['lastname'] . "\n";
            } else {
                $emailbody .= get_lang('LastName') . ': ' . $student['lastname'] . "\n";
                $emailbody .= get_lang('FirstName') . ': ' . $student['firstname'] . "\n";
            }
            $emailbody .= get_lang('Email') . ': <a href="mailto:' . $student['email'] . '">' . $student['email'] ."</a>\n\n";
            $recipient_name = api_get_person_name($tutor['firstname'], $tutor['lastname'], null,
                PERSON_NAME_EMAIL_ADDRESS);
            $sender_name = api_get_person_name(api_get_setting('administratorName'),
                api_get_setting('administratorSurname'), null, PERSON_NAME_EMAIL_ADDRESS);
            $email_admin = api_get_setting('emailAdministrator');

            $additionalParameters = array(
                'smsType' => SmsPlugin::NEW_USER_SUBSCRIBED_COURSE,
                'userId' => $tutor['user_id'],
                'userUsername' => $student['username'],
                'courseCode' => $course_code
            );
            api_mail_html(
                $recipient_name,
                $emailto,
                $emailsubject,
                $emailbody,
                $sender_name,
                $email_admin,
                null,
                null,
                null,
                $additionalParameters
            );
        }
    }

    /**
     * @return array
     */
    public static function get_special_course_list()
    {
        $courseTable = Database:: get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_field = Database:: get_main_table(TABLE_EXTRA_FIELD);
        $tbl_course_field_value = Database:: get_main_table(TABLE_EXTRA_FIELD_VALUES);

        //we filter the courses from the URL
        $join_access_url = $where_access_url = '';
        if (api_get_multiple_access_url()) {
            $access_url_id = api_get_current_access_url_id();
            if ($access_url_id != -1) {
                $tbl_url_course = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
                $join_access_url = "LEFT JOIN $tbl_url_course url_rel_course
                                    ON url_rel_course.c_id = tcfv.item_id ";
                $where_access_url = " AND access_url_id = $access_url_id ";
            }
        }

        $extraFieldType = EntityExtraField::COURSE_FIELD_TYPE;

        // get course list auto-register
        $sql = "SELECT DISTINCT(c.code)
                FROM $tbl_course_field_value tcfv
                INNER JOIN $tbl_course_field tcf
                ON tcfv.field_id =  tcf.id $join_access_url
                INNER JOIN $courseTable c
                ON (c.id = tcfv.item_id)
                WHERE
                    tcf.extra_field_type = $extraFieldType AND
                    tcf.variable = 'special_course' AND
                    tcfv.value = 1  $where_access_url";

        $result = Database::query($sql);
        $courseList = array();

        if (Database::num_rows($result) > 0) {
            while ($result_row = Database::fetch_array($result)) {
                $courseList[] = $result_row['code'];
            }
        }

        return $courseList;
    }

    /**
     * Get the course codes that have been restricted in the catalogue, and if byUserId is set
     * then the courses that the user is allowed or not to see in catalogue
     *
     * @param boolean allowed Either if the courses have some users that are or are not allowed to see in catalogue
     * @param boolean byUserId if the courses are or are not allowed to see to the user
     * @return array Course codes allowed or not to see in catalogue by some user or the user
     */
    public static function getCatalogueCourseList($allowed = true, $byUserId = -1)
    {
        $courseTable = Database:: get_main_table(TABLE_MAIN_COURSE);
        $tblCourseRelUserCatalogue = Database:: get_main_table(TABLE_MAIN_COURSE_CATALOGUE_USER);
        $visibility = ($allowed?1:0);

        // Restriction by user id
        $currentUserRestriction = "";
        if ($byUserId > 0) {
            $currentUserRestriction = " AND tcruc.user_id = $byUserId ";
        }

        //we filter the courses from the URL
        $joinAccessUrl = '';
        $whereAccessUrl = '';
        if (api_get_multiple_access_url()) {
            $accessUrlId = api_get_current_access_url_id();
            if ($accessUrlId != -1) {
                $tblUrlCourse = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
                $joinAccessUrl = "LEFT JOIN $tblUrlCourse url_rel_course
                                    ON url_rel_course.c_id = c.id ";
                $whereAccessUrl = " AND access_url_id = $accessUrlId ";
            }
        }

        // get course list auto-register
        $sql = "SELECT DISTINCT(c.code)
                FROM $tblCourseRelUserCatalogue tcruc
                INNER JOIN $courseTable c
                ON (c.id = tcruc.c_id) $joinAccessUrl
                WHERE tcruc.visible = $visibility $currentUserRestriction $whereAccessUrl";

        $result = Database::query($sql);
        $courseList = array();

        if (Database::num_rows($result) > 0) {
            while ($resultRow = Database::fetch_array($result)) {
                $courseList[] = $resultRow['code'];
            }
        }

        return $courseList;
    }

    /**
     * Get list of courses for a given user
     * @param int $user_id
     * @param boolean $include_sessions Whether to include courses from session or not
     * @param boolean $adminGetsAllCourses If the user is platform admin,
     * whether he gets all the courses or just his. Note: This does *not* include all sessions
     * @return array    List of codes and db name
     * @author isaac flores paz
     */
    public static function get_courses_list_by_user_id($user_id, $include_sessions = false, $adminGetsAllCourses = false)
    {
        $user_id = intval($user_id);
        $course_list = array();
        $codes = array();
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_user_course_category = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $special_course_list = self::get_special_course_list();

        if ($adminGetsAllCourses && UserManager::is_admin($user_id)) {
            // get the whole courses list
            $sql = "SELECT DISTINCT(course.code), course.id as real_id
                    FROM $tbl_course course";
        } else {

            $with_special_courses = $without_special_courses = '';
            if (!empty($special_course_list)) {
                $sc_string = '"' . implode('","', $special_course_list) . '"';
                $with_special_courses = ' course.code IN (' . $sc_string . ')';
                $without_special_courses = ' AND course.code NOT IN (' . $sc_string . ')';
            }

            if (!empty($with_special_courses)) {
                $sql = "SELECT DISTINCT(course.code), course.id as real_id
                        FROM $tbl_course_user  course_rel_user
                        LEFT JOIN $tbl_course  course
                        ON course.id = course_rel_user.c_id
                        LEFT JOIN $tbl_user_course_category user_course_category
                        ON course_rel_user.user_course_cat = user_course_category.id
                        WHERE  $with_special_courses
                        GROUP BY course.code
                        ORDER BY user_course_category.sort, course.title, course_rel_user.sort ASC

                    ";
                //
                $rs_special_course = Database::query($sql);
                if (Database::num_rows($rs_special_course) > 0) {
                    while ($result_row = Database::fetch_array($rs_special_course)) {
                        $result_row['special_course'] = 1;
                        $course_list[] = $result_row;
                        $codes[] = $result_row['real_id'];
                    }
                }
            }

            // get course list not auto-register. Use Distinct to avoid multiple
            // entries when a course is assigned to a HRD (DRH) as watcher
            $sql = "SELECT DISTINCT(course.code), course.id as real_id
                    FROM $tbl_course course
                    INNER JOIN $tbl_course_user cru ON course.id = cru.c_id
                    WHERE cru.user_id='$user_id' $without_special_courses";
        }
        $result = Database::query($sql);

        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                $course_list[] = $row;
                $codes[] = $row['real_id'];
            }
        }

        if ($include_sessions === true) {
            $sql = "SELECT DISTINCT(c.code), c.id as real_id
                    FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " s,
                    " . Database::get_main_table(TABLE_MAIN_COURSE) . " c
                    WHERE user_id = $user_id AND s.c_id = c.id";
            $r = Database::query($sql);
            while ($row = Database::fetch_array($r, 'ASSOC')) {
                if (!in_array($row['real_id'], $codes)) {
                    $course_list[] = $row;
                }
            }
        }

        return $course_list;
    }

    /**
     * Get course ID from a given course directory name
     * @param   string  Course directory (without any slash)
     * @return  string  Course code, or false if not found
     */
    public static function get_course_id_from_path($path)
    {
        $path = Database::escape_string(str_replace('.', '', str_replace('/', '', $path)));
        $res = Database::query("SELECT code FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE directory LIKE BINARY '$path'");
        if ($res === false) {
            return false;
        }
        if (Database::num_rows($res) != 1) {
            return false;
        }
        $row = Database::fetch_array($res);

        return $row['code'];
    }

    /**
     * Get course code(s) from visual code
     * @deprecated
     * @param   string  Visual code
     * @return  array   List of codes for the given visual code
     */
    public static function get_courses_info_from_visual_code($code)
    {
        $result = array();
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE visual_code = '" . Database::escape_string($code) . "'");
        while ($virtual_course = Database::fetch_array($sql_result)) {
            $result[] = $virtual_course;
        }
        return $result;
    }

    /**
     * Get emails of tutors to course
     * @param string Visual code
     * @param integer $courseId
     * @return array List of emails of tutors to course
     * @author @author Carlos Vargas <carlos.vargas@dokeos.com>, Dokeos Latino
     * */
    public static function get_emails_of_tutors_to_course($courseId)
    {
        $list = array();
        $res = Database::query("SELECT user_id FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE c_id ='" . intval($courseId) . "' AND status=1");
        while ($list_users = Database::fetch_array($res)) {
            $result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                    WHERE user_id=" . $list_users['user_id']);
            while ($row_user = Database::fetch_array($result)) {
                $name_teacher = api_get_person_name($row_user['firstname'], $row_user['lastname']);
                $list[] = array($row_user['email'] => $name_teacher);
            }
        }
        return $list;
    }

    /**
     * Get coaches emails by session
     * @param int session id
     * @param int $courseId
     * @param integer $session_id
     * @return array  array(email => name_tutor)  by coach
     * @author Carlos Vargas <carlos.vargas@dokeos.com>
     */
    public static function get_email_of_tutor_to_session($session_id, $courseId)
    {
        $tbl_session_course_user = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $tbl_user = Database::get_main_table(TABLE_MAIN_USER);
        $coachs_emails = array();

        $courseId = intval($courseId);
        $session_id = intval($session_id);

        $sql = "SELECT user_id
                FROM $tbl_session_course_user
                WHERE
                    session_id = '$session_id' AND
                    c_id = '$courseId' AND
                    status = 2
                ";
        $rs = Database::query($sql);

        if (Database::num_rows($rs) > 0) {

            $user_ids = array();
            while ($row = Database::fetch_array($rs)) {
                $user_ids[] = $row['user_id'];
            }

            $sql = "SELECT firstname, lastname, email FROM $tbl_user
                    WHERE user_id IN (" . implode(",", $user_ids) . ")";
            $rs_user = Database::query($sql);

            while ($row_emails = Database::fetch_array($rs_user)) {
                $mail_tutor = array(
                    'email' => $row_emails['email'],
                    'complete_name' => api_get_person_name($row_emails['firstname'], $row_emails['lastname'])
                );
                $coachs_emails[] = $mail_tutor;
            }
        }
        return $coachs_emails;
    }

    /**
     * Creates a new extra field for a given course
     * @param    string    Field's internal variable name
     * @param    int        Field's type
     * @param    string    Field's language var name
     * @param integer $fieldType
     * @param string $default
     * @return boolean     new extra field id
     */
    public static function create_course_extra_field($variable, $fieldType, $displayText, $default)
    {
        $extraField = new ExtraField('course');
        $params = [
            'variable' => $variable,
            'field_type' => $fieldType,
            'display_text' => $displayText,
            'default_value' => $default
        ];

        return $extraField->save($params);
    }

    /**
     * Updates course attribute. Note that you need to check that your
     * attribute is valid before you use this function
     *
     * @param int Course id
     * @param string Attribute name
     * @param string Attribute value
     * @return Doctrine\DBAL\Driver\Statement|null True if attribute was successfully updated,
     * false if course was not found or attribute name is invalid
     */
    public static function update_attribute($id, $name, $value)
    {
        $id = (int)$id;
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "UPDATE $table SET $name = '" . Database::escape_string($value) . "'
                WHERE id = '$id'";

        return Database::query($sql);
    }

    /**
     * Update course attributes. Will only update attributes with a non-empty value.
     * Note that you NEED to check that your attributes are valid before using this function
     *
     * @param int Course id
     * @param array Associative array with field names as keys and field values as values
     * @return Doctrine\DBAL\Driver\Statement|null True if update was successful, false otherwise
     */
    public static function update_attributes($id, $attributes)
    {
        $id = (int)$id;
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "UPDATE $table SET ";
        $i = 0;
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                if ($i > 0) {
                    $sql .= ", ";
                }
                $sql .= " $name = '" . Database::escape_string($value) . "'";
                $i++;
            }
        }
        $sql .= " WHERE id = '$id'";

        return Database::query($sql);
    }

    /**
     * Update an extra field value for a given course
     * @param    integer    Course ID
     * @param    string    Field variable name
     * @param    string    Field value
     * @return    boolean|null    true if field updated, false otherwise
     */
    public static function update_course_extra_field_value($course_code, $variable, $value = '')
    {
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        $extraFieldValues = new ExtraFieldValue('course');
        $params = [
            'item_id' => $courseId,
            'variable' => $variable,
            'value' => $value
        ];

        return $extraFieldValues->save($params);
    }

    /**
     * @param int $session_id
     * @return mixed
     */
    public static function get_session_category_id_by_session_id($session_id)
    {
        return Database::result(
            Database::query('SELECT  sc.id session_category
                FROM ' . Database::get_main_table(TABLE_MAIN_SESSION_CATEGORY) . ' sc
                INNER JOIN ' . Database::get_main_table(TABLE_MAIN_SESSION) . ' s
                ON sc.id=s.session_category_id WHERE s.id="' . Database::escape_string($session_id) . '"'),
            0,
            'session_category'
        );
    }

    /**
     * Gets the value of a course extra field. Returns null if it was not found
     *
     * @param string Name of the extra field
     * @param string Course code
     *
     * @return string Value
     */
    public static function get_course_extra_field_value($variable, $code)
    {
        $courseInfo = api_get_course_info($code);
        $courseId = $courseInfo['real_id'];

        $extraFieldValues = new ExtraFieldValue('course');
        $result = $extraFieldValues->get_values_by_handler_and_field_variable($courseId, $variable);
        if (!empty($result['value'])) {
            return $result['value'];
        }

        return null;
    }

    /**
     * Lists details of the course description
     * @param array        The course description
     * @param string    The encoding
     * @param bool        If true is displayed if false is hidden
     * @return string     The course description in html
     */
    public static function get_details_course_description_html($descriptions, $charset, $action_show = true)
    {
        $data = null;
        if (isset($descriptions) && count($descriptions) > 0) {
            foreach ($descriptions as $description) {
                $data .= '<div class="sectiontitle">';
                if (api_is_allowed_to_edit() && $action_show) {
                    //delete
                    $data .= '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&action=delete&description_id=' . $description->id . '" onclick="javascript:if(!confirm(\'' . addslashes(api_htmlentities(get_lang('ConfirmYourChoice'),
                                ENT_QUOTES, $charset)) . '\')) return false;">';
                    $data .= Display::return_icon('delete.gif', get_lang('Delete'),
                        array('style' => 'vertical-align:middle;float:right;'));
                    $data .= '</a> ';
                    //edit
                    $data .= '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&description_id=' . $description->id . '">';
                    $data .= Display::return_icon('edit.png', get_lang('Edit'),
                        array('style' => 'vertical-align:middle;float:right; padding-right:4px;'), ICON_SIZE_SMALL);
                    $data .= '</a> ';
                }
                $data .= $description->title;
                $data .= '</div>';
                $data .= '<div class="sectioncomment">';
                $data .= $description->content;
                $data .= '</div>';
            }
        } else {
            $data .= '<em>' . get_lang('ThisCourseDescriptionIsEmpty') . '</em>';
        }

        return $data;
    }

    /**
     * Returns the details of a course category
     *
     * @param string Category code
     * @return array Course category
     */
    public static function get_course_category($code)
    {
        $table_categories = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $sql = "SELECT * FROM $table_categories WHERE code = '$code';";
        return Database::fetch_array(Database::query($sql));
    }

    /**
     * Returns the details of a course category
     *
     * @param string Category code
     * @return array Course category
     */
    public static function getCategoriesList()
    {
        $table_categories = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $sql = "SELECT * FROM $table_categories";
        $result = Database::query($sql);
        $category = array();
        while ($row = Database::fetch_array($result, 'ASSOC')) {
            $category[$row['code']] = $row['name'];
        }
        return $category;
    }

    /**
     *  Get count rows of a table inside a course database
     * @param  string $table   The table of which the rows should be counted
     * @param  int $session_id       optionally count rows by session id
     * @return int $course_id    The number of rows in the given table.
     * @deprecated
     */
    public static function count_rows_course_table($table, $session_id = '', $course_id = null)
    {
        $condition_session = '';
        if ($session_id !== '') {
            $session_id = intval($session_id);
            $condition_session = " AND session_id = '$session_id' ";
        }
        if (!empty($course_id)) {
            $course_id = intval($course_id);
        } else {
            $course_id = api_get_course_int_id();
        }
        $condition_session .= " AND c_id = '$course_id' ";

        $sql = "SELECT COUNT(*) AS n FROM $table WHERE 1=1 $condition_session ";
        $rs = Database::query($sql);
        $row = Database::fetch_row($rs);
        return $row[0];
    }

    /**
     * Subscribes courses to human resource manager (Dashboard feature)
     * @param    int   $hr_manager_id      Human Resource Manager id
     * @param    array $courses_list       Courses code
     * @return int
     **/
    public static function subscribeCoursesToDrhManager($hr_manager_id, $courses_list)
    {
        $tbl_course_rel_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        $hr_manager_id = intval($hr_manager_id);
        $affected_rows = 0;

        //Deleting assigned courses to hrm_id
        if (api_is_multiple_url_enabled()) {
            $sql = "SELECT s.c_id FROM $tbl_course_rel_user s
                    INNER JOIN $tbl_course_rel_access_url a
                    ON (a.c_id = s.c_id)
                    WHERE
                        user_id = $hr_manager_id AND
                        relation_type=" . COURSE_RELATION_TYPE_RRHH . " AND
                        access_url_id = " . api_get_current_access_url_id() . "";
        } else {
            $sql = "SELECT c_id FROM $tbl_course_rel_user
                    WHERE user_id = $hr_manager_id AND relation_type=" . COURSE_RELATION_TYPE_RRHH . " ";
        }
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            while ($row = Database::fetch_array($result)) {
                $sql = "DELETE FROM $tbl_course_rel_user
                        WHERE
                            c_id = '{$row['c_id']}' AND
                            user_id = $hr_manager_id AND
                            relation_type=" . COURSE_RELATION_TYPE_RRHH . " ";
                Database::query($sql);
            }
        }

        // inserting new courses list
        if (is_array($courses_list)) {
            foreach ($courses_list as $course_code) {
                $courseInfo = api_get_course_info($course_code);
                $courseId = $courseInfo['real_id'];
                $sql = "INSERT IGNORE INTO $tbl_course_rel_user(c_id, user_id, status, relation_type)
                        VALUES($courseId, $hr_manager_id, '" . DRH . "', '" . COURSE_RELATION_TYPE_RRHH . "')";
                $result = Database::query($sql);
                if (Database::affected_rows($result)) {
                    $affected_rows++;
                }
            }
        }

        return $affected_rows;
    }

    /**
     * get courses followed by human resources manager
     * @param int $user_id
     * @param int $from
     * @param int $limit
     * @param string $column
     * @param string $direction
     * @return array    courses
     */
    public static function get_courses_followed_by_drh(
        $user_id,
        $status = DRH,
        $from = null,
        $limit = null,
        $column = null,
        $direction = null,
        $getCount = false
    ) {
        return self::getCoursesFollowedByUser(
            $user_id,
            $status,
            $from,
            $limit,
            $column,
            $direction,
            $getCount
        );
    }

    /**
     * get courses followed by user
     * @param   int $user_id
     * @param   int $status
     * @param   int $from
     * @param   int $limit
     * @param   string $column
     * @param   string $direction
     * @param   boolean $getCount
     * @param   string $keyword
     * @param   int $sessionId
     * @param   boolean $showAllAssignedCourses
     * @return  array   courses
     */
    public static function getCoursesFollowedByUser(
        $user_id,
        $status = null,
        $from = null,
        $limit = null,
        $column = null,
        $direction = null,
        $getCount = false,
        $keyword = null,
        $sessionId = null,
        $showAllAssignedCourses = false
    ) {
        // Database Table Definitions
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_rel_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sessionId = intval($sessionId);
        $user_id = intval($user_id);
        $select = "SELECT DISTINCT *, c.id as real_id ";

        if ($getCount) {
            $select = "SELECT COUNT(DISTINCT c.id) as count";
        }

        $whereConditions = null;
        switch ($status) {
            case COURSEMANAGER:
                $whereConditions .= " AND cru.user_id = '$user_id'";
                if (!$showAllAssignedCourses) {
                    $whereConditions .= " AND status = " . COURSEMANAGER;
                } else {
                    $whereConditions .= " AND relation_type = " . COURSE_RELATION_TYPE_COURSE_MANAGER;
                }
                break;
            case DRH:
                $whereConditions .= " AND
                    cru.user_id = '$user_id' AND
                    status = " . DRH . " AND
                    relation_type = '" . COURSE_RELATION_TYPE_RRHH . "'
                ";
                break;
        }

        $keywordCondition = null;
        if (!empty($keyword)) {
            $keyword = Database::escape_string($keyword);
            $keywordCondition = " AND (c.code LIKE '%$keyword%' OR c.title LIKE '%$keyword%' ) ";
        }

        $orderBy = null;
        $extraInnerJoin = null;

        if (!empty($sessionId)) {
            if (!empty($sessionId)) {
                $courseList = SessionManager::get_course_list_by_session_id(
                    $sessionId
                );
                if (!empty($courseList)) {
                    $courseListToString = implode("','", array_keys($courseList));
                    $whereConditions .= " AND c.id IN ('" . $courseListToString . "')";
                }
                $tableSessionRelCourse = Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
                $orderBy = ' ORDER BY position';
                $extraInnerJoin = " INNER JOIN $tableSessionRelCourse src
                                    ON (c.id = src.c_id AND session_id = $sessionId) ";
            }
        }

        $whereConditions .= $keywordCondition;
        $sql = "$select
                FROM $tbl_course c
                    INNER JOIN $tbl_course_rel_user cru ON (cru.c_id = c.id)
                    INNER JOIN $tbl_course_rel_access_url a ON (a.c_id = c.id)
                    $extraInnerJoin
                WHERE
                    access_url_id = " . api_get_current_access_url_id() . "
                    $whereConditions
                $orderBy
                ";
        if (isset($from) && isset($limit)) {
            $from = intval($from);
            $limit = intval($limit);
            $sql .= " LIMIT $from, $limit";
        }

        $result = Database::query($sql);

        if ($getCount) {
            $row = Database::fetch_array($result);
            return $row['count'];
        }

        $courses = array();
        if (Database::num_rows($result) > 0) {
            while ($row = Database::fetch_array($result)) {
                $courses[$row['code']] = $row;
            }
        }
        return $courses;
    }

    /**
     * check if a course is special (autoregister)
     * @param int $courseId
     * @return bool
     */
    public static function isSpecialCourse($courseId)
    {
        $extraFieldValue = new ExtraFieldValue('course');
        $result = $extraFieldValue->get_values_by_handler_and_field_variable(
            $courseId,
            'special_course'
        );

        if (!empty($result)) {
            if ($result['value'] == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update course picture
     * @param   string  Course code
     * @param   string  File name
     * @param   string  The full system name of the image from which course picture will be created.
     * @param   string $cropParameters Optional string that contents "x,y,width,height" of a cropped image format
     * @return  bool    Returns the resulting. In case of internal error or negative validation returns FALSE.
     */
    public static function update_course_picture($course_code, $filename, $source_file = null, $cropParameters = null)
    {
        $course_info = api_get_course_info($course_code);
        // course path
        $store_path = api_get_path(SYS_COURSE_PATH) . $course_info['path'];
        // image name for courses
        $course_image = $store_path . '/course-pic.png';
        $course_medium_image = $store_path . '/course-pic85x85.png';

        if (file_exists($course_image)) {
            unlink($course_image);
        }
        if (file_exists($course_medium_image)) {
            unlink($course_medium_image);
        }

        //Crop the image to adjust 4:3 ratio
        $image = new Image($source_file);
        $image->crop($cropParameters);

        //Resize the images in two formats
        $medium = new Image($source_file);
        $medium->resize(85);
        $medium->send_image($course_medium_image, -1, 'png');
        $normal = new Image($source_file);
        $normal->resize(400);
        $normal->send_image($course_image, -1, 'png');

        $result = $medium && $normal;

        return $result ? $result : false;
    }

    /**
     * Deletes the course picture
     * @param string $courseCode
     */
    public static function deleteCoursePicture($courseCode)
    {
        $course_info = api_get_course_info($courseCode);
        // course path
        $storePath = api_get_path(SYS_COURSE_PATH) . $course_info['path'];
        // image name for courses
        $courseImage = $storePath . '/course-pic.png';
        $courseMediumImage = $storePath . '/course-pic85x85.png';
        $courseSmallImage = $storePath . '/course-pic32.png';

        if (file_exists($courseImage)) {
            unlink($courseImage);
        }
        if (file_exists($courseMediumImage)) {
            unlink($courseMediumImage);
        }
        if (file_exists($courseSmallImage)) {
            unlink($courseSmallImage);
        }
    }

    /**
     * Builds the course block in user_portal.php
     * @todo use Twig
     *
     * @param array $params
     * @return string
     */
    public static function course_item_html_no_icon($params)
    {
        $html = '<div class="course_item">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-7">';

        $notifications = isset($params['notifications']) ? $params['notifications'] : null;

        $html .= '<h3>' . $params['title'] . $notifications . '</h3> ';

        if (isset($params['description'])) {
            $html .= '<p>' . $params['description'] . '</p>';
        }
        if (!empty($params['subtitle'])) {
            $html .= '<small>' . $params['subtitle'] . '</small>';
        }
        if (!empty($params['teachers'])) {
            $html .= '<h5 class="teacher">' . Display::return_icon('teacher.png', get_lang('Teacher'), array(),
                    ICON_SIZE_TINY) . $params['teachers'] . '</h5>';
        }
        if (!empty($params['coaches'])) {
            $html .= '<h5 class="teacher">' . Display::return_icon('teacher.png', get_lang('Coach'), array(),
                    ICON_SIZE_TINY) . $params['coaches'] . '</h5>';
        }

        $html .= '</div>';
        $params['right_actions'] = isset($params['right_actions']) ? $params['right_actions'] : null;
        $html .= '<div class="pull-right course-box-actions">' . $params['right_actions'] . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * @param $params
     * @param bool|false $is_sub_content
     * @return string
     */
    public static function session_items_html($params, $is_sub_content = false)
    {
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-2">';
        if (!empty($params['link'])){
            $html .= '<a class="thumbnail" href="'.$params['link'].'">';
            $html .= $params['icon'];
            $html .= '</a>';
        }else{
            $html .= $params['icon'];
        }
        $html .= '</div>';
        $html .= '<div class="col-md-10">';
        $html .= $params['title'];
        $html .= $params['coaches'];
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Display special courses (and only these) as several HTML divs of class userportal-course-item
     *
     * Special courses are courses that stick on top of the list and are "auto-registerable"
     * in the sense that any user clicking them is registered as a student
     * @param int       User id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function returnSpecialCourses($user_id, $load_dirs = false)
    {
        $user_id = intval($user_id);
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $special_course_list = self::get_special_course_list();

        $with_special_courses = '';
        if (!empty($special_course_list)) {
            $with_special_courses = ' course.code IN ("' . implode('","', $special_course_list) . '")';
        }

        $courseList = [];
        if (!empty($with_special_courses)) {
            $sql = "SELECT
                        course.id,
                        course.code,
                        course.subscribe subscr,
                        course.unsubscribe unsubscr,
                        course_rel_user.status status,
                        course_rel_user.sort sort,
                        course_rel_user.user_course_cat user_course_cat,
                        course_rel_user.user_id
                    FROM $tbl_course course
                    LEFT JOIN $tbl_course_user course_rel_user
                    ON course.id = course_rel_user.c_id AND course_rel_user.user_id = '$user_id'
                    WHERE $with_special_courses group by course.code";

            $rs_special_course = Database::query($sql);
            $number_of_courses = Database::num_rows($rs_special_course);
            $showCustomIcon = api_get_setting('course_images_in_courses_list');

            if ($number_of_courses > 0) {
                while ($course = Database::fetch_array($rs_special_course)) {
                    $course_info = api_get_course_info($course['code']);
                    if ($course_info['visibility'] == COURSE_VISIBILITY_HIDDEN) {
                        continue;
                    }

                    $params = [];
                    // Get notifications.
                    $course_info['id_session'] = null;
                    $course_info['status'] = $course['status'];
                    $show_notification = Display::show_notification($course_info);

                    if (empty($course['user_id'])) {
                        $course['status'] = STUDENT;
                    }

                    $params['edit_actions'] = '';
                    $params['document'] = '';
                    if (api_is_platform_admin()) {
                        $params['edit_actions'] .= api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'];
                        if ($load_dirs) {
                            $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                               . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                            $params['document'] .= Display::div('', ['id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container']);
                        }
                    }else{
                        if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED && $load_dirs) {
                            $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                               . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                            $params['document'] .= Display::div('', ['id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container']);
                        }
                    }

                    $params['visibility'] = $course_info['visibility'];
                    $params['status'] = $course_info['status'];
                    $params['category'] = $course_info['categoryName'];
                    $params['icon'] = Display::return_icon('drawing-pin.png',null, null, ICON_SIZE_LARGE, null);

                    if (api_get_setting('display_coursecode_in_courselist') == 'true') {
                        $params['code_course']  = '(' . $course_info['visual_code'] . ')';
                    }

                    $params['title'] = $course_info['title'];
                    $params['link'] = $course_info['course_public_url'].'?id_session=0&autoreg=1';
                    if (api_get_setting('display_teacher_in_courselist') === 'true') {
                        $params['teachers'] = CourseManager::getTeachersFromCourseByCode($course['code']);
                    }

                    if ($showCustomIcon === 'true') {
                        $params['thumbnails'] = $course_info['course_image'];
                        $params['image'] = $course_info['course_image_large'];
                    }

                    if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                        $params['notifications'] = $show_notification;
                    }

                    $params['is_special_course'] = true;

                    $courseList[] = $params;

                }
            }
        }

    return $courseList;
}

    /**
     * Display courses (without special courses) as several HTML divs
     * of course categories, as class userportal-catalog-item.
     * @uses displayCoursesInCategory() to display the courses themselves
     * @param int        user id
     * @param bool      Whether to show the document quick-loader or not
     * @param integer $user_id
     * @return string
     */
    public static function returnCourses($user_id, $load_dirs = false)
    {
        $user_id = intval($user_id);
        if (empty($user_id)) {
            $user_id = api_get_user_id();
        }
        // Step 1: We get all the categories of the user
        $table = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $sql = "SELECT id, title FROM $table
                WHERE user_id = '" . $user_id . "'
                ORDER BY sort ASC";
        $result = Database::query($sql);
        $listItems = [
            'in_category' => [],
            'not_category' => []
        ];

        while ($row = Database::fetch_array($result)) {
            // We simply display the title of the category.
            $courseInCategory = self:: returnCoursesCategories(
                $row['id'],
                $load_dirs
            );

            $params = [
                'id_category' => $row ['id'],
                'title_category' => $row['title'],
                'courses' => $courseInCategory
            ];
            $listItems['in_category'][] = $params;
        }

        // Step 2: We display the course without a user category.
        $coursesNotCategory = self::returnCoursesWithoutCategories(0, $load_dirs);
        if ($coursesNotCategory) {
            $listItems['not_category'] = $coursesNotCategory;
        }

        return $listItems;
    }

    /**
     *  Display courses inside a category (without special courses) as HTML dics of
     *  class userportal-course-item.
     * @param int      User category id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function returnCoursesCategories($user_category_id, $load_dirs = false)
    {
        $user_id = api_get_user_id();
        // Table definitions
        $TABLECOURS = Database:: get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database:: get_main_table(TABLE_MAIN_COURSE_USER);
        $TABLE_ACCESS_URL_REL_COURSE = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $current_url_id = api_get_current_access_url_id();

        // Get course list auto-register
        $special_course_list = self::get_special_course_list();

        $without_special_courses = '';
        if (!empty($special_course_list)) {
            $without_special_courses = ' AND course.code NOT IN ("' . implode('","', $special_course_list) . '")';
        }

        //AND course_rel_user.relation_type<>".COURSE_RELATION_TYPE_RRHH."
        $sql = "SELECT
                course.id,
                course.title,
                course.code,
                course.subscribe subscr,
                course.unsubscribe unsubscr,
                course_rel_user.status status,
                course_rel_user.sort sort,
                course_rel_user.user_course_cat user_course_cat
                FROM $TABLECOURS course,
                     $TABLECOURSUSER course_rel_user,
                     $TABLE_ACCESS_URL_REL_COURSE url
                WHERE
                    course.id = course_rel_user.c_id AND
                    url.c_id = course.id AND
                    course_rel_user.user_id = '" . $user_id . "' AND
                    course_rel_user.user_course_cat = '" . $user_category_id . "'
                    $without_special_courses ";

        // If multiple URL access mode is enabled, only fetch courses
        // corresponding to the current URL.
        if (api_get_multiple_access_url() && $current_url_id != -1) {
            $sql .= " AND url.c_id = course.id AND access_url_id='" . $current_url_id . "'";
        }
        // Use user's classification for courses (if any).
        $sql .= " ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC";

        $result = Database::query($sql);

        $courseList = array();
        $showCustomIcon = api_get_setting('course_images_in_courses_list');
        // Browse through all courses.
        while ($course = Database::fetch_array($result)) {
            $course_info = api_get_course_info($course['code']);
            if (isset($course_info['visibility']) &&
                $course_info['visibility'] == COURSE_VISIBILITY_HIDDEN
            ) {
                continue;
            }
            $course_info['id_session'] = null;
            $course_info['status'] = $course['status'];

            // For each course, get if there is any notification icon to show
            // (something that would have changed since the user's last visit).
            $showNotification = Display::show_notification($course_info);
            $iconName = basename($course_info['course_image']);

            if ($showCustomIcon === 'true' && $iconName != 'course.png') {
                $params['thumbnails'] = $course_info['course_image'];
                $params['image'] = $course_info['course_image_large'];
            }

            $params = array();

            $thumbnails = null;
            $image = null;

            if ($showCustomIcon === 'true' && $iconName != 'course.png') {
                $thumbnails = $course_info['course_image'];
                $image = $course_info['course_image_large'];
            }else{
                $image = Display::return_icon('session_default.png', null, null, null,null, true);
            }

            $params['course_id'] = $course['id'];
            $params['edit_actions'] = '';
            $params['document'] = '';
            if (api_is_platform_admin()) {
                $params['edit_actions'] .= api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'];
                if($load_dirs){
                    $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                               . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                    $params['document'] .= Display::div('', array('id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container'));
                }
            }
            if ($load_dirs) {
                $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                    . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                $params['document'] .= Display::div('', array('id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container'));
            }

            $courseUrl = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php?id_session=0';

            if (api_get_setting('display_teacher_in_courselist') === 'true') {
                $teachers = CourseManager::getTeachersFromCourseByCode($course['code']);
            }

            $params['status'] = $course['status'];

            if (api_get_setting('display_coursecode_in_courselist') == 'true') {
                $params['code_course'] = '(' . $course_info['visual_code'] . ') ';
            }

            $params['visibility'] = $course_info['visibility'];
            $params['link'] = $courseUrl;
            $params['thumbnails'] = $thumbnails;
            $params['image'] = $image;
            $params['title'] = $course_info['title'];
            $params['category'] = $course_info['categoryName'];
            $params['teachers'] = $teachers;

            if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                $params['notifications'] = $showNotification;
            }

            $courseList[] = $params;
        }

        return $courseList;
    }

    /**
     *  Display courses inside a category (without special courses) as HTML dics of
     *  class userportal-course-item.
     * @param int      User category id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function returnCoursesWithoutCategories($user_category_id, $load_dirs = false)
    {
        $user_id = api_get_user_id();
        // Table definitions
        $TABLECOURS = Database:: get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database:: get_main_table(TABLE_MAIN_COURSE_USER);
        $TABLE_ACCESS_URL_REL_COURSE = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $current_url_id = api_get_current_access_url_id();
        $courseList = [];

        // Get course list auto-register
        $special_course_list = self::get_special_course_list();

        $without_special_courses = '';
        if (!empty($special_course_list)) {
            $without_special_courses = ' AND course.code NOT IN ("' . implode('","', $special_course_list) . '")';
        }

        //AND course_rel_user.relation_type<>".COURSE_RELATION_TYPE_RRHH."
        $sql = "SELECT
                course.id,
                course.title,
                course.code,
                course.subscribe subscr,
                course.unsubscribe unsubscr,
                course_rel_user.status status,
                course_rel_user.sort sort,
                course_rel_user.user_course_cat user_course_cat
                FROM $TABLECOURS course,
                     $TABLECOURSUSER course_rel_user,
                     $TABLE_ACCESS_URL_REL_COURSE url
                WHERE
                    course.id = course_rel_user.c_id AND
                    url.c_id = course.id AND
                    course_rel_user.user_id = '" . $user_id . "' AND
                    course_rel_user.user_course_cat = '" . $user_category_id . "'
                    $without_special_courses ";

        // If multiple URL access mode is enabled, only fetch courses
        // corresponding to the current URL.
        if (api_get_multiple_access_url() && $current_url_id != -1) {
            $sql .= " AND url.c_id = course.id AND access_url_id='" . $current_url_id . "'";
        }
        // Use user's classification for courses (if any).
        $sql .= " ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC";
        $result = Database::query($sql);

        $showCustomIcon = api_get_setting('course_images_in_courses_list');
        // Browse through all courses.
        while ($course = Database::fetch_array($result)) {
            $course_info = api_get_course_info($course['code']);
            if (isset($course_info['visibility']) &&
                $course_info['visibility'] == COURSE_VISIBILITY_HIDDEN
            ) {
                continue;
            }
            $course_info['id_session'] = null;
            $course_info['status'] = $course['status'];

            // For each course, get if there is any notification icon to show
            // (something that would have changed since the user's last visit).
            $showNotification = Display::show_notification($course_info);

            $thumbnails = null;
            $image = null;

            $iconName = basename($course_info['course_image']);
            if ($showCustomIcon === 'true' && $iconName != 'course.png') {
                $thumbnails = $course_info['course_image'];
                $image = $course_info['course_image_large'];
            }else{
                $image = Display::return_icon('session_default.png', null, null, null,null, true);
            }

            $params = array();
            $params['edit_actions'] = '';
            $params['document'] = '';
            if (api_is_platform_admin()) {
                $params['edit_actions'] .= api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'];
                if($load_dirs){
                    $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                               . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                    $params['document'] .= Display::div('', array('id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container'));
                }
            }
            if ($load_dirs) {
                $params['document'] = '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">'
                    . Display::returnFontAwesomeIcon('folder-open') . '</a>';
                $params['document'] .= Display::div('', array('id' => 'document_result_' . $course_info['real_id'] . '_0', 'class' => 'document_preview_container'));
            }

            $course_title_url = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php?id_session=0';

            $teachers = '';

            if (api_get_setting('display_teacher_in_courselist') === 'true') {
                $teachers = CourseManager::getTeachersFromCourseByCode($course['code']);
            }
            $params['status'] = $course['status'];

            if (api_get_setting('display_coursecode_in_courselist') == 'true') {
                $params['code_course'] = '(' . $course_info['visual_code'] . ') ';
            }

            $params['visibility'] = $course_info['visibility'];
            $params['link'] = $course_title_url;
            $params['thumbnails'] = $thumbnails;
            $params['image'] = $image;
            $params['title'] = $course_info['title'];
            $params['category'] = $course_info['categoryName'];
            $params['teachers'] = $teachers;

            if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                $params['notifications'] = $showNotification;
            }

            $courseList[] = $params;

        }

        return $courseList;
    }

    /**
     * Retrieves the user defined course categories
     * @param string $userId
     * @return array containing all the titles of the user defined courses with the id as key of the array
     */
    public static function get_user_course_categories($userId = '')
    {
        if ($userId == '') {
            $realUserId = api_get_user_id();
        } else {
            $realUserId = $userId;
        }

        $output = array();
        $table_category = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $sql = "SELECT * FROM $table_category WHERE user_id = '".intval($realUserId)."'";
        $result = Database::query($sql);
        while ($row = Database::fetch_array($result)) {
            $output[$row['id']] = $row['title'];
        }
        return $output;
    }

    /**
     * Return an array the user_category id and title for the course $courseId for user $userId
     * @param $userId
     * @param $courseId
     * @return array
     */
    public static function getUserCourseCategoryForCourse($userId, $courseId)
    {
        $tblCourseRelUser = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tblUserCategory = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $courseId = intval($courseId);
        $userId = intval($userId);

        $sql = "SELECT user_course_cat, title
                FROM $tblCourseRelUser cru
                LEFT JOIN $tblUserCategory ucc
                ON cru.user_course_cat = ucc.id
                WHERE
                    cru.user_id = $userId AND c_id= $courseId ";

        $res = Database::query($sql);

        $result = array();
        if (Database::num_rows($res) > 0) {
            $data = Database::fetch_assoc($res);
            $result[] = $data['user_course_cat'];
            $result[] = $data['title'];
        }
        return $result;
    }

    /**
     * Get the course id based on the original id and field name in the extra fields.
     * Returns 0 if course was not found
     *
     * @param string $value Original course code
     * @param string $variable Original field name
     * @return int Course id
     */
    public static function getCourseInfoFromOriginalId($value, $variable)
    {
        $extraFieldValue = new ExtraFieldValue('course');
        $result = $extraFieldValue->get_item_id_from_field_variable_and_field_value(
            $variable,
            $value
        );

        if (!empty($result)) {
            $courseInfo = api_get_course_info_by_id($result['item_id']);
            return $courseInfo;
        }

        return 0;
    }

    /**
     * Display code for one specific course a logged in user is subscribed to.
     * Shows a link to the course, what's new icons...
     *
     * $my_course['d'] - course directory
     * $my_course['i'] - course title
     * $my_course['c'] - visual course code
     * $my_course['k']  - system course code
     *
     * @param   array       Course details
     * @param   integer     Session ID
     * @param   string      CSS class to apply to course entry
     * @param   boolean     Whether the session is supposedly accessible now
     * (not in the case it has passed and is in invisible/unaccessible mode)
     * @param bool      Whether to show the document quick-loader or not
     * @return  string      The HTML to be printed for the course entry
     *
     * @version 1.0.3
     * @todo refactor into different functions for database calls | logic | display
     * @todo replace single-character $my_course['d'] indices
     * @todo move code for what's new icons to a separate function to clear things up
     * @todo add a parameter user_id so that it is possible to show the
     * courselist of other users (=generalisation).
     * This will prevent having to write a new function for this.
     */
    public static function get_logged_user_course_html(
        $course,
        $session_id = 0,
        $class = 'courses',
        $session_accessible = true,
        $load_dirs = false
    ) {
        $entityManager = Database::getManager();
        $user_id = api_get_user_id();
        $course_info = api_get_course_info_by_id($course['real_id']);
        $status_course = CourseManager::get_user_in_course_status($user_id, $course_info['code']);
        $course_info['status'] = empty($session_id) ? $status_course : STUDENT;
        $course_info['id_session'] = $session_id;
        $objUser = $entityManager->find('ChamiloUserBundle:User', $user_id);
        $objCourse = $entityManager->find('ChamiloCoreBundle:Course', $course['real_id']);
        $objSession = $entityManager->find('ChamiloCoreBundle:Session', $session_id);
        $now = date('Y-m-d h:i:s');

        // Table definitions
        $main_user_table = Database:: get_main_table(TABLE_MAIN_USER);
        $tbl_session = Database:: get_main_table(TABLE_MAIN_SESSION);
        $tbl_session_category = Database:: get_main_table(TABLE_MAIN_SESSION_CATEGORY);

        $course_access_settings = CourseManager::get_access_settings($course_info['code']);
        $course_visibility = $course_access_settings['visibility'];

        if ($course_visibility == COURSE_VISIBILITY_HIDDEN) {
            return '';
        }

        $user_in_course_status = CourseManager::get_user_in_course_status(
            api_get_user_id(),
            $course_info['code']
        );

        $is_coach = api_is_coach($course_info['id_session'], $course_info['real_id']);

        // Display course entry.
        // Show a hyperlink to the course, unless the course is closed and user is not course admin.
        $session_url = '';

        $params = array();
        $params['icon'] = Display::return_icon(
            'blackboard_blue.png',
            null,
            array(),
            ICON_SIZE_LARGE,
            null,
            true
        );

        // Display the "what's new" icons
        $notifications = '';
        if ($course_visibility != COURSE_VISIBILITY_CLOSED && $course_visibility != COURSE_VISIBILITY_HIDDEN) {
            $notifications .= Display:: show_notification($course_info);
        }

        if ($session_accessible) {
            if ($course_visibility != COURSE_VISIBILITY_CLOSED ||
                $user_in_course_status == COURSEMANAGER
            ) {
                if (empty($course_info['id_session'])) {
                    $course_info['id_session'] = 0;
                }

                $sessionCourseAvailable = false;
                $sessionCourseStatus = api_get_session_visibility($session_id, $course_info['real_id']);

                if (in_array($sessionCourseStatus,
                    array(SESSION_VISIBLE_READ_ONLY, SESSION_VISIBLE, SESSION_AVAILABLE))) {
                    $sessionCourseAvailable = true;
                }

                if ($user_in_course_status == COURSEMANAGER || $sessionCourseAvailable) {
                    $session_url = $course_info['course_public_url'] . '?id_session=' . $course_info['id_session'];
                    $session_title = '<a href="' . $session_url. '">'. $course_info['name'] . '</a>'.$notifications;
                } else {
                    $session_title = $course_info['name'];
                }

            } else {
                $session_title =
                    $course_info['name'] . ' ' .
                    Display::tag('span', get_lang('CourseClosed'), array('class' => 'item_closed'));
            }
        } else {
            $session_title = $course_info['name'];
        }

        $thumbnails = null;
        $image = null;
        $showCustomIcon = api_get_setting('course_images_in_courses_list');
        $iconName = basename($course_info['course_image']);

        if ($showCustomIcon === 'true' && $iconName != 'course.png') {
            $thumbnails = $course_info['course_image'];
            $image = $course_info['course_image_large'];
        }else{
            $image = Display::return_icon('session_default.png', null, null, null,null, true);
        }
        $params['thumbnails'] = $thumbnails;
        $params['image'] = $image;
        $params['link'] = $session_url;
        $params['title'] = $session_title;
        $params['edit_actions'] = '';
        $params['document'] = '';

        if ($course_visibility != COURSE_VISIBILITY_CLOSED &&
            $course_visibility != COURSE_VISIBILITY_HIDDEN
        ) {
            if (api_is_platform_admin()) {
                $params['edit_actions'] .= api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course_info['code'];
            if ($load_dirs) {
                $params['document'] .= '<a id="document_preview_' . $course_info['real_id'] . '_' . $course_info['id_session'] . '" class="document_preview btn btn-default btn-sm" href="javascript:void(0);">' .
                    Display::returnFontAwesomeIcon('folder-open') . '</a>';
                $params['document'] .= Display::div('', array(
                    'id' => 'document_result_' . $course_info['real_id'] . '_' . $course_info['id_session'],
                    'class' => 'document_preview_container'
                ));
            }
        }
        }

        if (api_get_setting('display_coursecode_in_courselist') === 'true') {
            $session_title .= ' (' . $course_info['visual_code'] . ') ';
        }

        if (api_get_setting('display_teacher_in_courselist') === 'true') {
            $teacher_list = CourseManager::getTeachersFromCourseByCode(
                $course_info['code']
            );

            $course_coachs = self::get_coachs_from_course(
                $course_info['id_session'],
                $course_info['real_id']
            );
            $params['teachers'] = $teacher_list;

            if (($course_info['status'] == STUDENT && !empty($course_info['id_session'])) ||
                ($is_coach && $course_info['status'] != COURSEMANAGER)
            ) {
                $params['coaches'] = $course_coachs;
            }
        }

        $session_title .= isset($course['special_course']) ? ' ' .
                          Display::return_icon('klipper.png', get_lang('CourseAutoRegister')) : '';

        $params['title'] = $session_title;
        $params['extra'] = '';

        $html = $params;

        $session_category_id = null;
        if (1) {
            $session = '';
            $active = false;
            if (!empty($course_info['id_session'])) {

                // Request for the name of the general coach
                $sql = 'SELECT lastname, firstname,sc.name
                        FROM ' . $tbl_session . ' ts
                        LEFT JOIN ' . $main_user_table . ' tu
                        ON ts.id_coach = tu.user_id
                        INNER JOIN ' . $tbl_session_category . ' sc
                        ON ts.session_category_id = sc.id
                        WHERE ts.id=' . (int)$course_info['id_session'] . '
                        LIMIT 1';

                $rs = Database::query($sql);
                $sessioncoach = Database::store_result($rs);
                $sessioncoach = $sessioncoach ? $sessioncoach[0] : null;

                $session = api_get_session_info($course_info['id_session']);
                $session_category_id = CourseManager::get_session_category_id_by_session_id($course_info['id_session']);
                $session['category'] = $sessioncoach['name'];
                if (
                    $session['access_start_date'] == '0000-00-00 00:00:00' || empty($session['access_start_date']) ||
                    $session['access_start_date'] == '0000-00-00'
                ) {
                    //$session['dates'] = get_lang('WithoutTimeLimits');
                    $session['dates'] = '';
                    if (api_get_setting('show_session_coach') === 'true') {
                        $session['coach'] = get_lang('GeneralCoach') . ': ' . api_get_person_name($sessioncoach['firstname'],
                                $sessioncoach['lastname']);
                    }
                    $active = true;
                } else {
                    $session ['dates'] = ' - ' . get_lang('From') . ' ' . $session['access_start_date'] . ' ' . get_lang('To') . ' ' . $session['access_end_date'];
                    if (api_get_setting('show_session_coach') === 'true') {
                        $session['coach'] = get_lang('GeneralCoach') . ': ' . api_get_person_name($sessioncoach['firstname'],
                                $sessioncoach['lastname']);
                    }
                    $date_start = $session['access_start_date'];
                    $date_end = $session['access_end_date'];

                    $active = !$date_end ? ($date_start <= $now) : ($date_start <= $now && $date_end >= $now);
                }
            }
            $user_course_category = '';
            if (isset($course_info['user_course_cat'])) {
                $user_course_category = $course_info['user_course_cat'];
            }
            $output = array(
                $user_course_category,
                $html,
                $course_info['id_session'],
                $session,
                'active' => $active,
                'session_category_id' => $session_category_id
            );

            if (api_get_setting('allow_skills_tool') === 'true') {
                $skill = $entityManager
                    ->getRepository('ChamiloCoreBundle:Skill')
                    ->getLastByUser($objUser, $objCourse, $objSession);

                $output['skill'] = null;

                if ($skill) {
                    $output['skill']['name'] = $skill->getName();
                    $output['skill']['icon'] = $skill->getIcon();
                }
            }
        } else {
            $output = array($course_info['user_course_cat'], $html);
        }
        return $output;
    }

    /**
     *
     * @param    string    source course code
     * @param     int        source session id
     * @param    string    destination course code
     * @param     int        destination session id
     * @param integer $source_session_id
     * @param integer $destination_session_id
     * @return  bool
     */
    public static function copy_course(
        $source_course_code,
        $source_session_id,
        $destination_course_code,
        $destination_session_id,
        $params = array()
    ) {
        $course_info = api_get_course_info($source_course_code);

        if (!empty($course_info)) {
            $cb = new CourseBuilder('', $course_info);
            $course = $cb->build($source_session_id, $source_course_code, true);
            $course_restorer = new CourseRestorer($course);
            $course_restorer->skip_content = $params;
            $course_restorer->restore($destination_course_code, $destination_session_id, true, true);
            return true;
        }
        return false;
    }

    /**
     * A simpler version of the copy_course, the function creates an empty course with an autogenerated course code
     *
     * @param    string    new course title
     * @param    string    source course code
     * @param     int        source session id
     * @param     int        destination session id
     * @param    bool    new copied tools (Exercises and LPs)will be set to invisible by default?
     * @param string $new_title
     *
     * @return     array
     */
    public static function copy_course_simple(
        $new_title,
        $source_course_code,
        $source_session_id = 0,
        $destination_session_id = 0,
        $params = array()
    ) {
        $source_course_info = api_get_course_info($source_course_code);
        if (!empty($source_course_info)) {
            $new_course_code = self::generate_nice_next_course_code($source_course_code);
            if ($new_course_code) {
                $new_course_info = self::create_course($new_title, $new_course_code, false);
                if (!empty($new_course_info['code'])) {
                    $result = self::copy_course($source_course_code, $source_session_id, $new_course_info['code'],
                        $destination_session_id, $params);
                    if ($result) {
                        return $new_course_info;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Creates a new course code based in a given code
     *
     * @param string    wanted code
     * <code>    $wanted_code = 'curse' if there are in the DB codes like curse1 curse2 the function will return: course3</code>
     * if the course code doest not exist in the DB the same course code will be returned
     * @return string    wanted unused code
     */
    public static function generate_nice_next_course_code($wanted_code)
    {
        $course_code_ok = !self::course_code_exists($wanted_code);
        if (!$course_code_ok) {
            $wanted_code = CourseManager::generate_course_code($wanted_code);
            $table = Database::get_main_table(TABLE_MAIN_COURSE);
            $wanted_code = Database::escape_string($wanted_code);
            $sql = "SELECT count(*) as count
                    FROM $table
                    WHERE code LIKE '$wanted_code%'";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $row = Database::fetch_array($result);
                $count = $row['count'] + 1;
                $wanted_code = $wanted_code . '_' . $count;
                $result = api_get_course_info($wanted_code);
                if (empty($result)) {
                    return $wanted_code;
                }
            }

            return false;
        }

        return $wanted_code;
    }

    /**
     * Gets the status of the users agreement in a course course-session
     *
     * @param int $user_id
     * @param string $course_code
     * @param int $session_id
     * @return boolean
     */
    public static function is_user_accepted_legal($user_id, $course_code, $session_id = null)
    {
        $user_id = intval($user_id);
        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);

        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        // Course legal
        $enabled = api_get_plugin_setting('courselegal', 'tool_enable');

        if ($enabled == 'true') {
            require_once api_get_path(SYS_PLUGIN_PATH) . 'courselegal/config.php';
            $plugin = CourseLegalPlugin::create();
            return $plugin->isUserAcceptedLegal($user_id, $course_code, $session_id);
        }

        if (empty($session_id)) {
            $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
            $sql = "SELECT legal_agreement FROM $table
                    WHERE user_id = $user_id AND c_id = $courseId ";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $result = Database::fetch_array($result);
                if ($result['legal_agreement'] == 1) {
                    return true;
                }
            }
            return false;
        } else {
            $table = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
            $sql = "SELECT legal_agreement FROM $table
                    WHERE user_id = $user_id AND c_id = $courseId AND session_id = $session_id";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $result = Database::fetch_array($result);
                if ($result['legal_agreement'] == 1) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Saves the user-course legal agreement
     * @param   int user id
     * @param   string course code
     * @param   int session id
     * @return mixed
     */
    public static function save_user_legal($user_id, $course_code, $session_id = null)
    {
        // Course plugin legal
        $enabled = api_get_plugin_setting('courselegal', 'tool_enable');

        if ($enabled == 'true') {
            require_once api_get_path(SYS_PLUGIN_PATH) . 'courselegal/config.php';
            $plugin = CourseLegalPlugin::create();
            return $plugin->saveUserLegal($user_id, $course_code, $session_id);
        }

        $user_id = intval($user_id);
        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);

        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        if (empty($session_id)) {
            $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
            $sql = "UPDATE $table SET legal_agreement = '1'
                    WHERE user_id = $user_id AND c_id  = $courseId ";
            Database::query($sql);
        } else {
            $table = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
            $sql = "UPDATE  $table SET legal_agreement = '1'
                    WHERE user_id = $user_id AND c_id = $courseId AND session_id = $session_id";
            Database::query($sql);
        }
    }

    /**
     * @param int $user_id
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     * @return bool
     */
    public static function get_user_course_vote($user_id, $course_id, $session_id = null, $url_id = null)
    {
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $user_id = intval($user_id);

        if (empty($user_id)) {
            return false;
        }

        $params = array(
            'user_id' => $user_id,
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id
        );

        $result = Database::select(
            'vote',
            $table_user_course_vote,
            array(
                'where' => array(
                    'user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params
                )
            ),
            'first'
        );
        if (!empty($result)) {
            return $result['vote'];
        }
        return false;
    }

    /**
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     * @return array
     */
    public static function get_course_ranking($course_id, $session_id = null, $url_id = null)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $now = api_get_utc_datetime();

        $params = array(
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'creation_date' => $now,
        );

        $result = Database::select(
            'c_id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        $point_average_in_percentage = 0;
        $point_average_in_star = 0;
        $users_who_voted = 0;

        if (!empty($result['users'])) {
            $users_who_voted = $result['users'];
            $point_average_in_percentage = round($result['total_score'] / $result['users'] * 100 / 5, 2);
            $point_average_in_star = round($result['total_score'] / $result['users'], 1);
        }

        $result['user_vote'] = false;

        if (!api_is_anonymous()) {
            $result['user_vote'] = self::get_user_course_vote(api_get_user_id(), $course_id, $session_id, $url_id);
        }

        $result['point_average'] = $point_average_in_percentage;
        $result['point_average_star'] = $point_average_in_star;
        $result['users_who_voted'] = $users_who_voted;

        return $result;
    }

    /**
     *
     * Updates the course ranking
     * @param int   course id
     * @param int   session id
     * @param id    url id
     * @param integer $session_id
     * @return array
     **/
    public static function update_course_ranking(
        $course_id = null,
        $session_id = null,
        $url_id = null,
        $points_to_add = null,
        $add_access = true,
        $add_user = true
    ) {
        // Course catalog stats modifications see #4191
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);

        $now = api_get_utc_datetime();

        $course_id = empty($course_id) ? api_get_course_int_id() : intval($course_id);
        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);

        $params = array(
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'creation_date' => $now,
            'total_score' => 0,
            'users' => 0
        );

        $result = Database::select(
            'id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        // Problem here every time we load the courses/XXXX/index.php course home page we update the access

        if (empty($result)) {
            if ($add_access) {
                $params['accesses'] = 1;
            }
            //The votes and users are empty
            if (isset($points_to_add) && !empty($points_to_add)) {
                $params['total_score'] = intval($points_to_add);
            }
            if ($add_user) {
                $params['users'] = 1;
            }
            $result = Database::insert($table_course_ranking, $params);
        } else {
            $my_params = array();

            if ($add_access) {
                $my_params['accesses'] = intval($result['accesses']) + 1;
            }
            if (isset($points_to_add) && !empty($points_to_add)) {
                $my_params['total_score'] = $result['total_score'] + $points_to_add;
            }
            if ($add_user) {
                $my_params['users'] = $result['users'] + 1;
            }

            if (!empty($my_params)) {
                $result = Database::update(
                    $table_course_ranking,
                    $my_params,
                    array('c_id = ? AND session_id = ? AND url_id = ?' => $params)
                );
            }
        }

        return $result;
    }

    /**
     * Add user vote to a course
     *
     * @param   int user id
     * @param   int vote [1..5]
     * @param   int course id
     * @param   int session id
     * @param   int url id (access_url_id)
     * @return    false|string 'added', 'updated' or 'nothing'
     */
    public static function add_course_vote($user_id, $vote, $course_id, $session_id = null, $url_id = null)
    {
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);
        $course_id = empty($course_id) ? api_get_course_int_id() : intval($course_id);

        if (empty($course_id) || empty($user_id)) {
            return false;
        }

        if (!in_array($vote, array(1, 2, 3, 4, 5))) {
            return false;
        }

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $vote = intval($vote);

        $params = array(
            'user_id' => intval($user_id),
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'vote' => $vote
        );

        $action_done = 'nothing';

        $result = Database::select(
            'id, vote',
            $table_user_course_vote,
            array('where' => array('user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        if (empty($result)) {
            Database::insert($table_user_course_vote, $params);
            $points_to_add = $vote;
            $add_user = true;
            $action_done = 'added';
        } else {
            $my_params = array('vote' => $vote);
            $points_to_add = $vote - $result['vote'];
            $add_user = false;

            Database::update(
                $table_user_course_vote,
                $my_params,
                array('user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params)
            );
            $action_done = 'updated';
        }

        // Current points
        if (!empty($points_to_add)) {
            self::update_course_ranking(
                $course_id,
                $session_id,
                $url_id,
                $points_to_add,
                false,
                $add_user
            );
        }
        return $action_done;
    }

    /**
     * Remove course ranking + user votes
     *
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     *
     */
    public static function remove_course_ranking($course_id, $session_id, $url_id = null)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);

        if (!empty($course_id) && isset($session_id)) {
            $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
            $params = array(
                'c_id' => $course_id,
                'session_id' => $session_id,
                'url_id' => $url_id,
            );
            Database::delete($table_course_ranking, array('c_id = ? AND session_id = ? AND url_id = ?' => $params));
            Database::delete($table_user_course_vote, array('c_id = ? AND session_id = ? AND url_id = ?' => $params));
        }
    }

    /**
     * Returns an array with the hottest courses
     * @param   int $days number of days
     * @param   int $limit number of hottest courses
     * @return array
     */
    public static function return_hot_courses($days = 30, $limit = 6)
    {
        if (api_is_invitee()) {
            return array();
        }

        $limit = intval($limit);

        // Getting my courses
        $my_course_list = CourseManager::get_courses_list_by_user_id(api_get_user_id());

        $my_course_code_list = array();
        foreach ($my_course_list as $course) {
            $my_course_code_list[$course['real_id']] = $course['real_id'];
        }

        if (api_is_drh()) {
            $courses = CourseManager::get_courses_followed_by_drh(api_get_user_id());
            foreach ($courses as $course) {
                $my_course_code_list[$course['real_id']] = $course['real_id'];
            }
        }

        $table_course_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        //$table_course_access table uses the now() and interval ...
        $now = api_get_utc_datetime(time());
        $sql = "SELECT COUNT(course_access_id) course_count, a.c_id, visibility
                FROM $table_course c
                INNER JOIN $table_course_access a
                ON (c.id = a.c_id)
                INNER JOIN $table_course_url u
                ON u.c_id = c.id
                WHERE
                    u.access_url_id = " . api_get_current_access_url_id() . " AND
                    login_course_date <= '$now' AND
                    login_course_date > DATE_SUB('$now', INTERVAL $days DAY) AND
                    visibility <> '" . COURSE_VISIBILITY_CLOSED . "' AND visibility <> '" . COURSE_VISIBILITY_HIDDEN . "'
                GROUP BY a.c_id
                ORDER BY course_count DESC
                LIMIT $limit
            ";

        $result = Database::query($sql);
        $courses = array();

        if (Database::num_rows($result)) {
            $courses = Database::store_result($result, 'ASSOC');
            $courses = self::process_hot_course_item($courses, $my_course_code_list);
        }

        return $courses;
    }

    /**
     * @param array $courses
     * @param array $my_course_code_list
     * @return mixed
     */
    public static function process_hot_course_item($courses, $my_course_code_list = array())
    {
        $hotCourses = [];

        $ajax_url = api_get_path(WEB_AJAX_PATH) . 'course.ajax.php?a=add_course_vote';

        $stok = Security::get_existing_token();

        $user_id = api_get_user_id();

        foreach ($courses as $courseId) {
            $course_info = api_get_course_info_by_id($courseId['c_id']);
            $courseCode = $course_info['code'];
            $categoryCode = !empty($course_info['categoryCode']) ? $course_info['categoryCode'] : "";
            $my_course = $course_info;
            $my_course['go_to_course_button'] = '';
            $my_course['register_button'] = '';

            $access_link = self::get_access_link_by_user(
                api_get_user_id(),
                $course_info,
                $my_course_code_list
            );

            $user_registerd_in_course = CourseManager::is_user_subscribed_in_course($user_id, $course_info['code']);
            $user_registerd_in_course_as_teacher = CourseManager::is_course_teacher($user_id, $course_info['code']);
            $user_registerd_in_course_as_student = ($user_registerd_in_course && !$user_registerd_in_course_as_teacher);

            // if user registered as student
            if ($user_registerd_in_course_as_student) {
                $icon = '<em class="fa fa-graduation-cap"></em>';
                $title = get_lang("AlreadyRegisteredToCourse");
                $my_course['already_register_as'] = Display::tag(
                    'button',
                    $icon,
                    array('id' => 'register', 'class' => 'btn btn-default btn-sm', 'title' => $title)
                );
            } elseif ($user_registerd_in_course_as_teacher) {
                // if user registered as teacher
                $icon = '<em class="fa fa-suitcase"></em>';
                $title = get_lang("YouAreATeacherOfThisCourse");
                $my_course['already_register_as'] = Display::tag(
                    'button',
                    $icon,
                    array('id' => 'register', 'class' => 'btn btn-default btn-sm', 'title' => $title)
                );
            }

            //Course visibility
            if ($access_link && in_array('register', $access_link)) {
                $my_course['register_button'] = Display::url(
                    Display::returnFontAwesomeIcon('sign-in'),
                    api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php?action=subscribe&sec_token=' . $stok,
                    array('class' => 'btn btn-success btn-sm', 'title' => get_lang('Subscribe')));
            }

            if ($access_link && in_array('enter',
                    $access_link) || $course_info['visibility'] == COURSE_VISIBILITY_OPEN_WORLD
            ) {
                $my_course['go_to_course_button'] = Display::url(
                    Display::returnFontAwesomeIcon('share'),
                    api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php',
                    array('class' => 'btn btn-default btn-sm', 'title' => get_lang('GoToCourse')));
            }

            if ($access_link && in_array('unsubscribe', $access_link)) {
                $my_course['unsubscribe_button'] = Display::url(
                    Display::returnFontAwesomeIcon('sign-out'),
                    api_get_path(WEB_CODE_PATH) . 'auth/courses.php?action=unsubscribe&unsubscribe=' . $courseCode . '&sec_token=' . $stok . '&category_code=' . $categoryCode,
                    array('class' => 'btn btn-danger btn-sm', 'title' => get_lang('Unreg')));
            }

            // start buycourse validation
            // display the course price and buy button if the buycourses plugin is enabled and this course is configured
            $plugin = BuyCoursesPlugin::create();
            $isThisCourseInSale = $plugin->buyCoursesForGridCatalogVerificator($course_info['real_id'], BuyCoursesPlugin::PRODUCT_TYPE_COURSE);
            if ($isThisCourseInSale) {
                // set the price label
                $my_course['price'] = $isThisCourseInSale['html'];
                // set the Buy button instead register.
                if ($isThisCourseInSale['verificator'] && !empty($my_course['register_button'])) {
                    $my_course['register_button'] = $plugin->returnBuyCourseButton($course_info['real_id'], BuyCoursesPlugin::PRODUCT_TYPE_COURSE);
                }
            }
            // end buycourse validation

            //Description
            $my_course['description_button'] = '';
            /* if ($course_info['visibility'] == COURSE_VISIBILITY_OPEN_WORLD || in_array($course_info['real_id'],
                    $my_course_code_list)
            ) { */
                $my_course['description_button'] = Display::url(
                    Display::returnFontAwesomeIcon('info-circle'),
                    api_get_path(WEB_AJAX_PATH) . 'course_home.ajax.php?a=show_course_information&code=' . $course_info['code'],
                    [
                        'class' => 'btn btn-default btn-sm ajax',
                        'data-title' => get_lang('Description'),
                        'title' => get_lang('Description')
                    ]
                );
            //}
            /* get_lang('Description') */
            $my_course['teachers'] = CourseManager::getTeachersFromCourseByCode($course_info['code']);
            $point_info = self::get_course_ranking($course_info['real_id'], 0);
            $my_course['rating_html'] = Display::return_rating_system('star_' . $course_info['real_id'],
                $ajax_url . '&course_id=' . $course_info['real_id'], $point_info);

            $hotCourses[] = $my_course;
        }
        return $hotCourses;
    }

    /**
     * @param int $limit
     * @return array
     */
    public static function return_most_accessed_courses($limit = 5)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);
        $params['url_id'] = api_get_current_access_url_id();

        $result = Database::select(
            'c_id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('url_id = ?' => $params), 'order' => 'accesses DESC', 'limit' => $limit),
            'all',
            true
        );
        return $result;
    }

    /**
     * Get courses count
     * @param int Access URL ID (optional)
     * @param int $visibility
     * @param integer $access_url_id
     *
     * @return int Number of courses
     */
    public static function count_courses($access_url_id = null, $visibility = null)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql = "SELECT count(c.id) FROM $table_course c";
        if (!empty($access_url_id) && $access_url_id == intval($access_url_id)) {
            $sql .= ", $table_course_rel_access_url u
                    WHERE c.id = u.c_id AND u.access_url_id = $access_url_id";
            if (!empty($visibility)) {
                $visibility = intval($visibility);
                $sql .= " AND visibility = $visibility ";
            }
        } else {
            if (!empty($visibility)) {
                $visibility = intval($visibility);
                $sql .= " WHERE visibility = $visibility ";
            }
        }

        $res = Database::query($sql);
        $row = Database::fetch_row($res);

        return $row[0];
    }

    /**
     * Get active courses count.
     * Active = all courses except the ones with hidden visibility.
     *
     * @param int $urlId Access URL ID (optional)
     * @return int Number of courses
     */
    public static function countActiveCourses($urlId = null)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql = "SELECT count(id) FROM $table_course c";
        if (!empty($urlId) && $urlId == intval($urlId)) {
            $sql .= ", $table_course_rel_access_url u
                    WHERE
                        c.id = u.c_id AND
                        u.access_url_id = $urlId AND
                        visibility <> " . COURSE_VISIBILITY_HIDDEN;
        } else {
            $sql .= " WHERE visibility <> " . COURSE_VISIBILITY_HIDDEN;
        }
        $res = Database::query($sql);
        $row = Database::fetch_row($res);
        return $row[0];
    }

    /**
     * Returns the SQL conditions to filter course only visible by the user in the catalogue
     *
     * @param $courseTableAlias Alias of the course table
     * @return string SQL conditions
     */
    public static function getCourseVisibilitySQLCondition($courseTableAlias) {
        $visibilityCondition = '';
        $hidePrivate = api_get_setting('course_catalog_hide_private');
        if ($hidePrivate === 'true') {
            $visibilityCondition = ' AND '.$courseTableAlias.'.visibility <> 1';
        }

        // Check if course have users allowed to see it in the catalogue, then show only if current user is allowed to see it
        $currentUserId = api_get_user_id();
        $restrictedCourses = self::getCatalogueCourseList(true);
        $allowedCoursesToCurrentUser = self::getCatalogueCourseList(true, $currentUserId);
        if (!empty($restrictedCourses)) {
            $visibilityCondition .= ' AND ('.$courseTableAlias.'.code NOT IN ("' . implode('","', $restrictedCourses) . '")';
            $visibilityCondition .= ' OR '.$courseTableAlias.'.code IN ("' . implode('","', $allowedCoursesToCurrentUser) . '"))';
        }

        // Check if course have users denied to see it in the catalogue, then show only if current user is not denied to see it
        $restrictedCourses = self::getCatalogueCourseList(false);
        $notAllowedCoursesToCurrentUser = self::getCatalogueCourseList(false, $currentUserId);
        if (!empty($restrictedCourses)) {
            $visibilityCondition .= ' AND ('.$courseTableAlias.'.code NOT IN ("' . implode('","', $restrictedCourses) . '")';
            $visibilityCondition .= ' OR '.$courseTableAlias.'.code NOT IN ("' . implode('","', $notAllowedCoursesToCurrentUser) . '"))';
        }

        return $visibilityCondition;
    }

    /**
     * Get available le courses count
     * @param int Access URL ID (optional)
     * @param integer $accessUrlId
     * @return int Number of courses
     */
    public static function countAvailableCourses($accessUrlId = null)
    {
        $tableCourse = Database::get_main_table(TABLE_MAIN_COURSE);
        $tableCourseRelAccessUrl = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $specialCourseList = self::get_special_course_list();

        $withoutSpecialCourses = '';
        if (!empty($specialCourseList)) {
            $withoutSpecialCourses = ' AND c.code NOT IN ("' . implode('","', $specialCourseList) . '")';
        }

        $visibilityCondition = self::getCourseVisibilitySQLCondition('c');

        if (!empty($accessUrlId) && $accessUrlId == intval($accessUrlId)) {
            $sql = "SELECT count(c.id) FROM $tableCourse c, $tableCourseRelAccessUrl u
                    WHERE
                        c.id = u.c_id AND
                        u.access_url_id = $accessUrlId AND
                        c.visibility != 0 AND
                        c.visibility != 4
                        $withoutSpecialCourses
                        $visibilityCondition
                    ";
        }
        $res = Database::query($sql);
        $row = Database::fetch_row($res);

        return $row[0];
    }

    /**
     * Return a link to go to the course, validating the visibility of the
     * course and the user status
     * @param int User ID
     * @param array Course details array
     * @param array  List of courses to which the user is subscribed (if not provided, will be generated)
     * @param integer $uid
     * @return mixed 'enter' for a link to go to the course or 'register' for a link to subscribe, or false if no access
     */
    static function get_access_link_by_user($uid, $course, $user_courses = array())
    {
        if (empty($uid) or empty($course)) {
            return false;
        }

        if (empty($user_courses)) {
            // get the array of courses to which the user is subscribed
            $user_courses = CourseManager::get_courses_list_by_user_id($uid);
            foreach ($user_courses as $k => $v) {
                $user_courses[$k] = $v['real_id'];
            }
        }

        if (!isset($course['real_id']) && empty($course['real_id'])) {
            $course = api_get_course_info($course['code']);
        }

        if ($course['visibility'] == COURSE_VISIBILITY_HIDDEN) {
            return array();
        }

        $is_admin = api_is_platform_admin_by_id($uid);
        $options = array();
        // Register button
        if (!api_is_anonymous($uid) &&
            (
            ($course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD || $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM)
                //$course['visibility'] == COURSE_VISIBILITY_REGISTERED && $course['subscribe'] == SUBSCRIBE_ALLOWED
            ) &&
            $course['subscribe'] == SUBSCRIBE_ALLOWED &&
            (!in_array($course['real_id'], $user_courses) || empty($user_courses))
        ) {
            $options[] = 'register';
        }

        // Go To Course button (only if admin, if course public or if student already subscribed)
        if ($is_admin ||
            $course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD && empty($course['registration_code']) ||
            (api_user_is_login($uid) && $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM && empty($course['registration_code'])) ||
            (in_array($course['real_id'], $user_courses) && $course['visibility'] != COURSE_VISIBILITY_CLOSED)
        ) {
            $options[] = 'enter';
        }

        if ($is_admin ||
            $course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD && empty($course['registration_code']) ||
            (api_user_is_login($uid) && $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM && empty($course['registration_code'])) ||
            (in_array($course['real_id'], $user_courses) && $course['visibility'] != COURSE_VISIBILITY_CLOSED)
        ) {
            $options[] = 'enter';
        }

        if ($course['visibility'] != COURSE_VISIBILITY_HIDDEN && empty($course['registration_code']) && $course['unsubscribe'] == UNSUBSCRIBE_ALLOWED && api_user_is_login($uid) && (in_array($course['real_id'],
                $user_courses))
        ) {
            $options[] = 'unsubscribe';
        }

        return $options;
    }

    /**
     * @param array $courseInfo
     * @param array $teachers
     * @param bool $deleteTeachersNotInList
     * @param bool $editTeacherInSessions
     * @param bool $deleteSessionTeacherNotInList
     * @return false|null
     */
    public static function updateTeachers(
        $courseInfo,
        $teachers,
        $deleteTeachersNotInList = true,
        $editTeacherInSessions = false,
        $deleteSessionTeacherNotInList = false,
        $teacherBackup = array()
    ) {
        if (empty($teachers)) {
            return false;
        }

        if (!is_array($teachers)) {
            $teachers = array($teachers);
        }

        if (empty($courseInfo) || !isset($courseInfo['real_id'])) {
            return false;
        }

        $courseId = $courseInfo['real_id'];
        $course_code = $courseInfo['code'];

        $course_user_table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $alreadyAddedTeachers = CourseManager::get_teacher_list_from_course_code($course_code);

        if ($deleteTeachersNotInList) {

            // Delete only teacher relations that doesn't match the selected teachers
            $cond = null;
            if (count($teachers) > 0) {
                foreach ($teachers as $key) {
                    $key = Database::escape_string($key);
                    $cond .= " AND user_id <> '" . $key . "'";
                }
            }

            $sql = 'DELETE FROM ' . $course_user_table . '
                    WHERE c_id ="' . $courseId . '" AND status="1" AND relation_type = 0 ' . $cond;
            Database::query($sql);
        }

        if (count($teachers) > 0) {
            foreach ($teachers as $userId) {
                $userId = intval($userId);
                // We check if the teacher is already subscribed in this course
                $sql = 'SELECT 1 FROM ' . $course_user_table . '
                        WHERE user_id = "' . $userId . '" AND c_id = "' . $courseId . '" ';
                $result = Database::query($sql);
                if (Database::num_rows($result)) {
                    $sql = 'UPDATE ' . $course_user_table . ' SET status = "1"
                            WHERE c_id = "' . $courseId . '" AND user_id = "' . $userId . '"  ';
                } else {
                    $userCourseCategory = '0';
                    if (isset($teacherBackup[$userId]) &&
                        isset($teacherBackup[$userId][$course_code])
                    ) {
                        $courseUserData = $teacherBackup[$userId][$course_code];
                        $userCourseCategory = $courseUserData['user_course_cat'];
                    }

                    $sql = "INSERT INTO " . $course_user_table . " SET
                            c_id = " . $courseId . ",
                            user_id = " . $userId . ",
                            status = '1',
                            is_tutor = '0',
                            sort = '0',
                            relation_type = '0',
                            user_course_cat = '$userCourseCategory'
                    ";
                }
                Database::query($sql);
            }
        }

        if ($editTeacherInSessions) {
            $sessions = SessionManager::get_session_by_course($courseId);

            if (!empty($sessions)) {
                foreach ($sessions as $session) {
                    // Remove old and add new
                    if ($deleteSessionTeacherNotInList) {
                        foreach ($teachers as $userId) {
                            SessionManager::set_coach_to_course_session(
                                $userId,
                                $session['id'],
                                $courseId
                            );
                        }

                        $teachersToDelete = array();
                        if (!empty($alreadyAddedTeachers)) {
                            $teachersToDelete = array_diff(array_keys($alreadyAddedTeachers), $teachers);
                        }

                        if (!empty($teachersToDelete)) {
                            foreach ($teachersToDelete as $userId) {
                                SessionManager::set_coach_to_course_session(
                                    $userId,
                                    $session['id'],
                                    $courseId,
                                    true
                                );
                            }
                        }
                    } else {
                        // Add new teachers only
                        foreach ($teachers as $userId) {
                            SessionManager::set_coach_to_course_session(
                                $userId,
                                $session['id'],
                                $courseId
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Course available settings variables see c_course_setting table
     * @param AppPlugin $appPlugin
     * @return array
     */
    public static function getCourseSettingVariables(AppPlugin $appPlugin)
    {
        $pluginCourseSettings = $appPlugin->getAllPluginCourseSettings();
        $courseSettings = array(
            // Get allow_learning_path_theme from table
            'allow_learning_path_theme',
            // Get allow_open_chat_window from table
            'allow_open_chat_window',
            'allow_public_certificates',
            // Get allow_user_edit_agenda from table
            'allow_user_edit_agenda',
            // Get allow_user_edit_announcement from table
            'allow_user_edit_announcement',
            // Get allow_user_image_forum from table
            'allow_user_image_forum',
            //Get allow show user list
            'allow_user_view_user_list',
            // Get course_theme from table
            'course_theme',
            //Get allow show user list
            'display_info_advance_inside_homecourse',
            'documents_default_visibility',
            // Get send_mail_setting (work)from table
            'email_alert_manager_on_new_doc',
            // Get send_mail_setting (work)from table
            'email_alert_manager_on_new_quiz',
            // Get send_mail_setting (dropbox) from table
            'email_alert_on_new_doc_dropbox',
            'email_alert_students_on_new_homework',
            // Get send_mail_setting (auth)from table
            'email_alert_to_teacher_on_new_user_in_course',
            'enable_lp_auto_launch',
            'pdf_export_watermark_text',
            'show_system_folders',
            'exercise_invisible_in_session',
            'enable_forum_auto_launch',
            'show_course_in_user_language'
        );

        $allowLPReturnLink = api_get_setting('allow_lp_return_link');
        if ($allowLPReturnLink === 'true') {
            $courseSettings[] = 'lp_return_link';
        }

        if (!empty($pluginCourseSettings)) {
            $courseSettings = array_merge(
                $courseSettings,
                $pluginCourseSettings
            );
        }

        return $courseSettings;
    }

    /**
     * @param AppPlugin $appPlugin
     * @param string $variable
     * @param string $value
     * @param int $courseId
     * @return bool
     */
    public static function saveCourseConfigurationSetting(AppPlugin $appPlugin, $variable, $value, $courseId)
    {
        $settingList = self::getCourseSettingVariables($appPlugin);

        if (!in_array($variable, $settingList)) {

            return false;
        }

        $courseSettingTable = Database::get_course_table(TABLE_COURSE_SETTING);
        if (self::hasCourseSetting($variable, $courseId)) {
            // Update
            Database::update(
                $courseSettingTable,
                array('value' => $value),
                array('variable = ? AND c_id = ?' => array($variable, $courseId))
            );
        } else {
            // Create
            Database::insert(
                $courseSettingTable,
                ['title' => $variable, 'value' => $value, 'c_id' => $courseId, 'variable' => $variable]
            );
        }
        return true;
    }

    /**
     * Check if course setting exists
     * @param string $variable
     * @param int $courseId
     * @return bool
     */
    public static function hasCourseSetting($variable, $courseId)
    {
        $courseSetting = Database::get_course_table(TABLE_COURSE_SETTING);
        $courseId = intval($courseId);
        $variable = Database::escape_string($variable);
        $sql = "SELECT variable FROM $courseSetting
                WHERE c_id = $courseId AND variable = '$variable'";
        $result = Database::query($sql);

        return Database::num_rows($result) > 0;
    }

    /**
     * Get information from the track_e_course_access table
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public static function getCourseAccessPerSessionAndUser($sessionId, $userId, $limit = null)
    {
        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);

        $sessionId = intval($sessionId);
        $userId = intval($userId);

        $sql = "SELECT * FROM $table
                WHERE session_id = $sessionId AND user_id = $userId";

        if (!empty($limit)) {
            $limit = intval($limit);
            $sql .= " LIMIT $limit";
        }
        $result = Database::query($sql);

        return Database::store_result($result);
    }

    /**
     * Get information from the track_e_course_access table
     * @param int $courseId
     * @param int $sessionId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getCourseAccessPerCourseAndSession(
        $courseId,
        $sessionId,
        $startDate,
        $endDate
    ) {
        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $courseId = intval($courseId);
        $sessionId = intval($sessionId);
        $startDate = Database::escape_string($startDate);
        $endDate = Database::escape_string($endDate);

        $sql = "SELECT * FROM $table
                WHERE
                    c_id = $courseId AND
                    session_id = $sessionId AND
                    login_course_date BETWEEN '$startDate' AND '$endDate'
                ";

        $result = Database::query($sql);

        return Database::store_result($result);
    }

    /**
     * Get login information from the track_e_course_access table, for any
     * course in the given session
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public static function getFirstCourseAccessPerSessionAndUser($sessionId, $userId)
    {
        $sessionId = intval($sessionId);
        $userId = intval($userId);

        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $sql = "SELECT * FROM $table
                WHERE session_id = $sessionId AND user_id = $userId
                ORDER BY login_course_date ASC
                LIMIT 1";

        $result = Database::query($sql);
        $courseAccess = array();
        if (Database::num_rows($result)) {
            $courseAccess = Database::fetch_array($result, 'ASSOC');
        }
        return $courseAccess;
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     * @param bool $getAllSessions
     * @return mixed
     */
    public static function getCountForum(
        $courseId,
        $sessionId = 0,
        $getAllSessions = false
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        if ($getAllSessions) {
            $sql = "SELECT count(*) as count
                    FROM $forum f
                    WHERE f.c_id = %s";
        } else {
            $sql = "SELECT count(*) as count
                    FROM $forum f
                    WHERE f.c_id = %s and f.session_id = %s";
        }

        $sql = sprintf($sql, intval($courseId), intval($sessionId));
        $result = Database::query($sql);
        $row = Database::fetch_array($result);

        return $row['count'];
    }

    /**
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     * @return mixed
     */
    public static function getCountPostInForumPerUser(
        $userId,
        $courseId,
        $sessionId = 0
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        $forum_post = Database::get_course_table(TABLE_FORUM_POST);

        $sql = "SELECT count(distinct post_id) as count
                FROM $forum_post p
                INNER JOIN $forum f
                ON f.forum_id = p.forum_id AND f.c_id = p.c_id
                WHERE p.poster_id = %s and f.session_id = %s and p.c_id = %s";

        $sql = sprintf(
            $sql,
            intval($userId),
            intval($sessionId),
            intval($courseId)
        );

        $result = Database::query($sql);
        $row = Database::fetch_array($result);
        return $row['count'];
    }

    /**
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     * @return mixed
     */
    public static function getCountForumPerUser(
        $userId,
        $courseId,
        $sessionId = 0
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        $forum_post = Database::get_course_table(TABLE_FORUM_POST);

        $sql = "SELECT count(distinct f.forum_id) as count
                FROM $forum_post p
                INNER JOIN $forum f
                ON f.forum_id = p.forum_id AND f.c_id = p.c_id
                WHERE p.poster_id = %s and f.session_id = %s and p.c_id = %s";

        $sql = sprintf(
            $sql,
            intval($userId),
            intval($sessionId),
            intval($courseId)
        );

        $result = Database::query($sql);
        $row = Database::fetch_array($result);
        return $row['count'];
    }

    /**
     * Returns the course name from a given code
     * @param string $code
     */
    public static function getCourseNameFromCode($code)
    {
        $tbl_main_categories = Database:: get_main_table(TABLE_MAIN_COURSE);
        $sql = 'SELECT title
                FROM ' . $tbl_main_categories . '
                WHERE code = "' . Database::escape_string($code) . '"';
        $result = Database::query($sql);
        if ($col = Database::fetch_array($result)) {
            return $col['title'];
        }
    }

    /**
     * Generates a course code from a course title
     * @todo Such a function might be useful in other places too. It might be moved in the CourseManager class.
     * @todo the function might be upgraded for avoiding code duplications (currently, it might suggest a code that is already in use)
     * @param string $title A course title
     * @return string A proposed course code
     * +
     * @assert (null,null) === false
     * @assert ('ABC_DEF', null) === 'ABCDEF'
     * @assert ('ABC09*^[%A', null) === 'ABC09A'
     */
    public static function generate_course_code($title)
    {
        return substr(
            preg_replace('/[^A-Z0-9]/', '', strtoupper(api_replace_dangerous_char($title))),
            0,
            CourseManager::MAX_COURSE_LENGTH_CODE
        );
    }

    /**
     * @param $courseId
     * @return array
     */
    public static function getCourseSettings($courseId)
    {
        $settingTable = Database::get_course_table(TABLE_COURSE_SETTING);
        $courseId = intval($courseId);
        $sql = "SELECT * FROM $settingTable WHERE c_id = $courseId";
        $result = Database::query($sql);
        $settings = array();
        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                $settings[$row['variable']] = $row;
            }
        }
        return $settings;
    }

    /**
     * this function gets all the users of the course,
     * including users from linked courses
     */
    public static function getCourseUsers()
    {
        //this would return only the users from real courses:
        $session_id = api_get_session_id();
        if ($session_id != 0) {
            $user_list = self::get_real_and_linked_user_list(api_get_course_id(), true, $session_id);
        } else {
            $user_list = self::get_real_and_linked_user_list(api_get_course_id(), false, 0);
        }

        return $user_list;
    }

    /**
     * this function gets all the groups of the course,
     * not including linked courses
     */
    public static function getCourseGroups()
    {
        $session_id = api_get_session_id();
        if ($session_id != 0) {
            $new_group_list = self::get_group_list_of_course(api_get_course_id(), $session_id, 1);
        } else {
            $new_group_list = self::get_group_list_of_course(api_get_course_id(), 0, 1);
        }

        return $new_group_list;
    }

    /**
     * @param FormValidator $form
     * @param array $to_already_selected
     *
     * @return HTML_QuickForm_element
     */
    public static function addUserGroupMultiSelect(&$form, $to_already_selected)
    {
        $user_list = self::getCourseUsers();
        $group_list = self::getCourseGroups();
        $array = self::buildSelectOptions($group_list, $user_list, $to_already_selected);

        $result = array();
        foreach ($array as $content) {
            $result[$content['value']] = $content['content'];
        }

        return $form->addElement(
            'advmultiselect',
            'users',
            get_lang('Users'),
            $result,
            array('select_all_checkbox' => true)
        );
    }

    /**
     * This function separates the users from the groups
     * users have a value USER:XXX (with XXX the groups id have a value
     *  GROUP:YYY (with YYY the group id)
     * @param  array $to Array of strings that define the type and id of each destination
     * @return array Array of groups and users (each an array of IDs)
     */
    public static function separateUsersGroups($to)
    {
        $grouplist = array();
        $userlist = array();

        foreach ($to as $to_item) {
            if (!empty($to_item)) {
                $parts = explode(':', $to_item);
                $type = isset($parts[0]) ? $parts[0] : '';
                $id = isset($parts[1]) ? $parts[1] : '';

                switch ($type) {
                    case 'GROUP':
                        $grouplist[] = intval($id);
                        break;
                    case 'USER':
                        $userlist[] = intval($id);
                        break;
                }
            }
        }

        $send_to['groups'] = $grouplist;
        $send_to['users'] = $userlist;

        return $send_to;
    }

    /**
     * Shows the form for sending a message to a specific group or user.
     * @param FormValidator $form
     * @param int $group_id iid
     * @param array $to
     */
    public static function addGroupMultiSelect($form, $group_id, $to = array())
    {
        $group_users = GroupManager::get_subscribed_users($group_id);
        $array = self::buildSelectOptions(null, $group_users, $to);

        $result = array();
        foreach ($array as $content) {
            $result[$content['value']] = $content['content'];
        }

        return $form->addElement('advmultiselect', 'users', get_lang('Users'), $result);
    }

    /**
     * this function shows the form for sending a message to a specific group or user.
     * @param array $group_list
     * @param array $user_list
     * @param array $to_already_selected
     * @return array
     */
    public static function buildSelectOptions(
        $group_list = array(),
        $user_list = array(),
        $to_already_selected = array()
    ) {
        if (empty($to_already_selected)) {
            $to_already_selected = array();
        }

        $result = array();
        // adding the groups to the select form
        if ($group_list) {
            foreach ($group_list as $this_group) {
                if (is_array($to_already_selected)) {
                    if (!in_array(
                        "GROUP:" . $this_group['id'],
                        $to_already_selected
                    )
                    ) { // $to_already_selected is the array containing the groups (and users) that are already selected
                        $user_label = ($this_group['userNb'] > 0) ? get_lang('Users') : get_lang('LowerCaseUser');
                        $user_disabled = ($this_group['userNb'] > 0) ? "" : "disabled=disabled";
                        $result[] = array(
                            'disabled' => $user_disabled,
                            'value' => "GROUP:" . $this_group['id'],
                            'content' => "G: " . $this_group['name'] . " - " . $this_group['userNb'] . " " . $user_label
                        );
                    }
                }
            }
        }

        // adding the individual users to the select form
        if ($user_list) {
            foreach ($user_list as $user) {
                if (is_array($to_already_selected)) {
                    if (!in_array(
                        "USER:" . $user['user_id'],
                        $to_already_selected
                    )
                    ) { // $to_already_selected is the array containing the users (and groups) that are already selected

                        $result[] = array(
                            'value' => "USER:" . $user['user_id'],
                            'content' => api_get_person_name($user['firstname'], $user['lastname'])
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array a list (array) of all courses.
     */
    public static function get_course_list()
    {
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        return Database::store_result(Database::query("SELECT *, id as real_id FROM $table"));
    }

    /**
     * Returns course code from a given gradebook category's id
     * @param int  Category ID
     * @return string  Course code
     */
    public static function get_course_by_category($category_id)
    {
        $category_id = intval($category_id);
        $info = Database::fetch_array(
            Database::query('SELECT course_code FROM ' . Database::get_main_table(TABLE_MAIN_GRADEBOOK_CATEGORY) . '
            WHERE id=' . $category_id), 'ASSOC'
        );
        return $info ? $info['course_code'] : false;
    }

    /**
     * This function gets all the courses that are not in a session
     * @param date Start date
     * @param date End date
     * @param   bool    $includeClosed Whether to include closed and hidden courses
     * @return array Not-in-session courses
     */
    public static function getCoursesWithoutSession($startDate = null, $endDate = null, $includeClosed = false)
    {
        $dateConditional = ($startDate && $endDate) ?
            " WHERE session_id IN (SELECT id FROM " . Database::get_main_table(TABLE_MAIN_SESSION) .
            " WHERE access_start_date = '$startDate' AND access_end_date = '$endDate')" :
            null;
        $visibility = ($includeClosed ? '' : 'visibility NOT IN (0, 4) AND ');

        $query = "SELECT id, code, title
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE). "
                WHERE $visibility code NOT IN (
                    SELECT DISTINCT course_code FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE) . $dateConditional . ")
                ORDER BY id";

        $result = Database::query($query);
        $courses = array();
        while ($row = Database::fetch_array($result)) {
            $courses[] = $row;
        }
        return $courses;
    }

    /**
     * Get list of courses based on users of a group for a group admin
     * @param int $userId The user id
     * @return array
     */
    public static function getCoursesFollowedByGroupAdmin($userId)
    {
        $coursesList = [];

        $courseTable = Database::get_main_table(TABLE_MAIN_COURSE);
        $courseUserTable = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $userGroup = new UserGroup();
        $userIdList = $userGroup->getGroupUsersByUser($userId);

        if (empty($userIdList)) {
            return [];
        }

        $sql = "SELECT DISTINCT(c.id), c.title
                FROM $courseTable c
                INNER JOIN $courseUserTable cru ON c.id = cru.c_id
                WHERE (
                    cru.user_id IN (" . implode(', ', $userIdList) . ")
                    AND cru.relation_type = 0
                )";

        if (api_is_multiple_url_enabled()) {
            $courseAccessUrlTable = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
            $accessUrlId = api_get_current_access_url_id();

            if ($accessUrlId != -1) {
                $sql = "SELECT DISTINCT(c.id), c.title
                        FROM $courseTable c
                        INNER JOIN $courseUserTable cru ON c.id = cru.c_id
                        INNER JOIN $courseAccessUrlTable crau ON c.id = crau.c_id
                        WHERE crau.access_url_id = $accessUrlId
                            AND (
                            cru.id_user IN (" . implode(', ', $userIdList) . ") AND
                            cru.relation_type = 0
                        )";
            }
        }

        $result = Database::query($sql);

        while ($row = Database::fetch_assoc($result)) {
            $coursesList[] = $row;
        }

        return $coursesList;
    }

    /**
     * Direct course link see #5299
     *
     * You can send to your students an URL like this
     * http://chamilodev.beeznest.com/main/auth/inscription.php?c=ABC&e=3
     * Where "c" is the course code and "e" is the exercise Id, after a successful
     * registration the user will be sent to the course or exercise
     *
     */
    public static function redirectToCourse($form_data)
    {
        $course_code_redirect = Session::read('course_redirect');
        $_user = api_get_user_info();
        $user_id = api_get_user_id();

        if (!empty($course_code_redirect)) {
            $course_info = api_get_course_info($course_code_redirect);
            if (!empty($course_info)) {
                if (in_array($course_info['visibility'],
                    array(COURSE_VISIBILITY_OPEN_PLATFORM, COURSE_VISIBILITY_OPEN_WORLD))
                ) {
                    if (CourseManager::is_user_subscribed_in_course($user_id, $course_info['code'])) {

                        $form_data['action'] = $course_info['course_public_url'];
                        $form_data['message'] = sprintf(get_lang('YouHaveBeenRegisteredToCourseX'), $course_info['title']);
                        $form_data['button'] = Display::button(
                            'next',
                            get_lang('GoToCourse', null, $_user['language']),
                            array('class' => 'btn btn-primary btn-large')
                        );

                        $exercise_redirect = intval(Session::read('exercise_redirect'));
                        // Specify the course id as the current context does not
                        // hold a global $_course array
                        $objExercise = new Exercise($course_info['real_id']);
                        $result = $objExercise->read($exercise_redirect);

                        if (!empty($exercise_redirect) && !empty($result)) {
                            $form_data['action'] = api_get_path(WEB_CODE_PATH) . 'exercise/overview.php?exerciseId='.$exercise_redirect.'&cidReq='.$course_info['code'];
                            $form_data['message'] .= '<br />'.get_lang('YouCanAccessTheExercise');
                            $form_data['button'] = Display::button(
                                'next',
                                get_lang('Go', null, $_user['language']),
                                array('class' => 'btn btn-primary btn-large')
                            );
                        }

                        if (!empty($form_data['action'])) {
                            header('Location: '.$form_data['action']);
                            exit;
                        }
                    }
                }
            }
        }

        return $form_data;
    }

    /**
     * return html code for displaying a course title in the standard view (not the Session view)
     * @param $courseId
     * @param bool $loadDirs
     * @return string
     */
    public static function displayCourseHtml($courseId, $loadDirs = false)
    {
        $params = self::getCourseParamsForDisplay($courseId, $loadDirs);
        $html = self::course_item_html($params, false);

        return $html;
    }

    /**
     * Return tab of params to display a course title in the My Courses tab
     * Check visibility, right, and notification icons, and load_dirs option
     * @param $courseId
     * @param bool $loadDirs
     * @return array
     */
    public static function getCourseParamsForDisplay($courseId, $loadDirs = false)
    {
        $user_id = api_get_user_id();
        // Table definitions
        $TABLECOURS = Database :: get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database :: get_main_table(TABLE_MAIN_COURSE_USER);
        $TABLE_ACCESS_URL_REL_COURSE = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $current_url_id = api_get_current_access_url_id();

        // Get course list auto-register
        $special_course_list = self::get_special_course_list();

        $without_special_courses = '';
        if (!empty($special_course_list)) {
            $without_special_courses = ' AND course.code NOT IN ("'.implode('","',$special_course_list).'")';
        }

        //AND course_rel_user.relation_type<>".COURSE_RELATION_TYPE_RRHH."
        $sql = "SELECT 
                    course.id, 
                    course.title, 
                    course.code, 
                    course.subscribe subscr, 
                    course.unsubscribe unsubscr, 
                    course_rel_user.status status,
                    course_rel_user.sort sort, 
                    course_rel_user.user_course_cat user_course_cat
                FROM
                $TABLECOURS course,
                $TABLECOURSUSER course_rel_user, 
                $TABLE_ACCESS_URL_REL_COURSE url
                WHERE
                    course.id=".intval($courseId)." AND
                    course.id = course_rel_user.c_id AND
                    url.c_id = course.id AND
                    course_rel_user.user_id = ".intval($user_id)."
                    $without_special_courses
                ";

        // If multiple URL access mode is enabled, only fetch courses
        // corresponding to the current URL.
        if (api_get_multiple_access_url() && $current_url_id != -1) {
            $sql .= " AND url.course_code=course.code AND access_url_id=".intval($current_url_id);
        }
        // Use user's classification for courses (if any).
        $sql .= " ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC";

        $result = Database::query($sql);

        // Browse through all courses. We can only have one course because
        // of the  course.id=".intval($courseId) in sql query
        $course = Database::fetch_array($result);
        $course_info = api_get_course_info_by_id($courseId);
        if (empty($course_info)) {
            return '';
        }

        //$course['id_session'] = null;
        $course_info['id_session'] = null;
        $course_info['status'] = $course['status'];

        // For each course, get if there is any notification icon to show
        // (something that would have changed since the user's last visit).
        $show_notification = Display::show_notification($course_info);

        // New code displaying the user's status in respect to this course.
        $status_icon = Display::return_icon(
            'blackboard.png',
            $course_info['title'],
            array(),
            ICON_SIZE_LARGE
        );

        $params = array();
        $params['right_actions'] = '';

        if (api_is_platform_admin()) {
            if ($loadDirs) {
                $params['right_actions'] .= '<a id="document_preview_'.$course_info['real_id'].'_0" class="document_preview" href="javascript:void(0);">'.Display::return_icon('folder.png', get_lang('Documents'), array('align' => 'absmiddle'),ICON_SIZE_SMALL).'</a>';
                $params['right_actions'] .= '<a href="'.api_get_path(WEB_CODE_PATH).'course_info/infocours.php?cidReq='.$course['code'].'">'.Display::return_icon('edit.png', get_lang('Edit'), array('align' => 'absmiddle'),ICON_SIZE_SMALL).'</a>';
                $params['right_actions'] .= Display::div('', array('id' => 'document_result_'.$course_info['real_id'].'_0', 'class'=>'document_preview_container'));
            } else {
                $params['right_actions'].= '<a class="btn btn-default btn-sm" title="'.get_lang('Edit').'" href="'.api_get_path(WEB_CODE_PATH).'course_info/infocours.php?cidReq='.$course['code'].'">'.Display::returnFontAwesomeIcon('pencil').'</a>';
            }

            if ($course_info['status'] == COURSEMANAGER) {
                //echo Display::return_icon('teachers.gif', get_lang('Status').': '.get_lang('Teacher'), array('style'=>'width: 11px; height: 11px;'));
            }
        } else {
            if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                if ($loadDirs) {
                    $params['right_actions'] .= '<a id="document_preview_'.$course_info['real_id'].'_0" class="document_preview" href="javascript:void(0);">'.Display::return_icon('folder.png', get_lang('Documents'), array('align' => 'absmiddle'),ICON_SIZE_SMALL).'</a>';
                    $params['right_actions'] .= Display::div('', array('id' => 'document_result_'.$course_info['real_id'].'_0', 'class'=>'document_preview_container'));
                } else {
                    if ($course_info['status'] == COURSEMANAGER) {
                        $params['right_actions'].= '<a class="btn btn-default btn-sm" title="'.get_lang('Edit').'" href="'.api_get_path(WEB_CODE_PATH).'course_info/infocours.php?cidReq='.$course['code'].'">'.Display::returnFontAwesomeIcon('pencil').'</a>';
                    }
                }
            }
        }

        $course_title_url = '';
        if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED || $course['status'] == COURSEMANAGER) {
            $course_title_url = api_get_path(WEB_COURSE_PATH).$course_info['path'].'/?id_session=0';
            $course_title = Display::url($course_info['title'], $course_title_url);
        } else {
            $course_title = $course_info['title'].' '.Display::tag('span',get_lang('CourseClosed'), array('class'=>'item_closed'));
        }

        // Start displaying the course block itself
        if (api_get_setting('display_coursecode_in_courselist') === 'true') {
            $course_title .= ' ('.$course_info['visual_code'].') ';
        }
        $teachers = '';
        if (api_get_setting('display_teacher_in_courselist') === 'true') {
            $teachers = CourseManager::get_teacher_list_from_course_code_to_string($course['code'], self::USER_SEPARATOR, true);
        }
        $params['link'] = $course_title_url;
        $params['icon'] = $status_icon;
        $params['title'] = $course_title;
        $params['teachers'] = $teachers;
        if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
            $params['notifications'] = $show_notification;
        }

        return $params;
    }

    /**
     * Get the course id based on the original id and field name in the extra fields.
     * Returns 0 if course was not found
     *
     * @param string $original_course_id_value Original course id
     * @param string $original_course_id_name Original field name
     * @return int Course id
     */
    public static function get_course_id_from_original_id($original_course_id_value, $original_course_id_name)
    {
        $extraFieldValue = new ExtraFieldValue('course');
        $value = $extraFieldValue->get_item_id_from_field_variable_and_field_value(
            $original_course_id_name,
            $original_course_id_value
        );

        if ($value) {
            return $value['item_id'];
        }
        return 0;
    }

    /**
     * Helper function to create a default gradebook (if necessary) upon course creation
     * @param   int     $modelId    The gradebook model ID
     * @param   string  $courseCode Course code
     * @return  void
     */
    public static function createDefaultGradebook($modelId, $courseCode)
    {
        if (api_get_setting('gradebook_enable_grade_model') === 'true') {
            //Create gradebook_category for the new course and add
            // a gradebook model for the course
            if (isset($modelId) &&
                !empty($modelId) &&
                $modelId != '-1'
            ) {
                GradebookUtils::create_default_course_gradebook(
                    $courseCode,
                    $modelId
                );
            }
        }
    }

    /**
     * Helper function to check if there is a course template and, if so, to
     * copy the template as basis for the new course
     * @param   string  $courseCode   Course code
     * @param   int     $courseTemplate 0 if no course template is defined
     */
    public static function useTemplateAsBasisIfRequired($courseCode, $courseTemplate)
    {
        $template = api_get_setting('course_creation_use_template');
        $teacherCanSelectCourseTemplate = api_get_setting('teacher_can_select_course_template') === 'true';
        $courseTemplate = isset($courseTemplate) ? intval($courseTemplate) : 0;

        $useTemplate = false;

        if ($teacherCanSelectCourseTemplate && $courseTemplate) {
            $useTemplate = true;
            $originCourse = api_get_course_info_by_id($courseTemplate);
        } elseif (!empty($template)) {
            $useTemplate = true;
            $originCourse = api_get_course_info_by_id($template);
        }

        if ($useTemplate) {
            // Include the necessary libraries to generate a course copy
            // Call the course copy object
            $originCourse['official_code'] = $originCourse['code'];
            $cb = new CourseBuilder(null, $originCourse);
            $course = $cb->build(null, $originCourse['code']);
            $cr = new CourseRestorer($course);
            $cr->set_file_option();
            $cr->restore($courseCode);
        }
    }

    /**
     * Helper method to get the number of users defined with a specific course extra field
     * @param   string  $name   Field title
     * @param   string  $tableExtraFields The extra fields table name
     * @param   string  $tableUserFieldValues The user extra field value table name
     * @return  int     The number of users with this extra field with a specific value
     */
    public static function getCountRegisteredUsersWithCourseExtraField($name, $tableExtraFields = '', $tableUserFieldValues = '')
    {
        if (empty($tableExtraFields)) {
            $tableExtraFields = Database::get_main_table(TABLE_EXTRA_FIELD);
        }
        if (empty($tableUserFieldValues)) {
            $tableUserFieldValues = Database::get_main_table(TABLE_EXTRA_FIELD_VALUES);
        }

        $registered_users_with_extra_field = 0;

        if (!empty($name) && $name != '-') {
            $extraFieldType = EntityExtraField::COURSE_FIELD_TYPE;
            $name = Database::escape_string($name);
            $sql = "SELECT count(v.item_id) as count
                    FROM $tableUserFieldValues v 
                    INNER JOIN $tableExtraFields f
                    ON (f.id = v.field_id)
                    WHERE value = '$name' AND extra_field_type = $extraFieldType";
            $result_count = Database::query($sql);
            if (Database::num_rows($result_count)) {
                $row_count = Database::fetch_array($result_count);
                $registered_users_with_extra_field = $row_count['count'];
            }
        }

        return $registered_users_with_extra_field;
    }

    /**
     * Check if a specific access-url-related setting is a problem or not
     * @param array $_configuration The $_configuration array
     * @param int $accessUrlId The access URL ID
     * @param string $param
     * @param string $msgLabel
     * @return bool|string
     */
    private static function checkCreateCourseAccessUrlParam($_configuration, $accessUrlId, $param, $msgLabel)
    {
        if (isset($_configuration[$accessUrlId][$param]) && $_configuration[$accessUrlId][$param] > 0) {
            $num = self::count_courses($accessUrlId);
            if ($num >= $_configuration[$accessUrlId][$param]) {
                api_warn_hosting_contact($param);

                Display::addFlash(
                    Display::return_message($msgLabel)
                );
            }
        }
        return false;
    }
    /**
     * Fill course with all necessary items
     * @param array $courseInfo Course info array
     * @param array $params Parameters from the course creation form
     * @param int $authorId
     * @return void
     */
    private static function fillCourse($courseInfo, $params, $authorId = 0)
    {
        $authorId = empty($authorId) ? api_get_user_id() : (int) $authorId;

        AddCourse::prepare_course_repository($courseInfo['directory'], $courseInfo['code']);
        AddCourse::fill_db_course(
            $courseInfo['real_id'],
            $courseInfo['directory'],
            $courseInfo['course_language'],
            $params['exemplary_content'],
            $authorId
        );

        if (isset($params['gradebook_model_id'])) {
            CourseManager::createDefaultGradebook($params['gradebook_model_id'], $courseInfo['code']);
        }
        // If parameter defined, copy the contents from a specific
        // template course into this new course
        if (isset($params['course_template'])) {
            CourseManager::useTemplateAsBasisIfRequired($courseInfo['id'], $params['course_template']);
        }
        $params['course_code'] = $courseInfo['code'];
        $params['item_id'] = $courseInfo['real_id'];

        $courseFieldValue = new ExtraFieldValue('course');
        $courseFieldValue->saveFieldValues($params);
    }
}
