<?php
/* For licensing terms, see /license.txt */

use Chamilo\CourseBundle\Entity\CLink;

/**
 * Function library for the links tool.
 *
 * This is a complete remake of the original link tool.
 * New features:
 * - Organize links into categories;
 * - favorites/bookmarks interface;
 * - move links up/down within a category;
 * - move categories up/down;
 * - expand/collapse all categories;
 * - add link to 'root' category => category-less link is always visible.
 *
 * @author Patrick Cool, complete remake (December 2003 - January 2004)
 * @author René Haentjens, CSV file import (October 2004)
 * @package chamilo.link
 */
class Link extends Model
{
    public $table;
    public $is_course_model = true;
    public $columns = array(
        'id',
        'c_id',
        'url',
        'title',
        'description',
        'category_id',
        'display_order',
        'on_homepage',
        'target',
        'session_id'
    );
    public $required = array('url', 'title');
    private $course;

    /**
     *
     */
    public function __construct()
    {
        $this->table = Database::get_course_table(TABLE_LINK);
    }

    /**
     * @param array $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @return array
     */
    public function getCourse()
    {
        return !empty($this->course) ? $this->course : api_get_course_info();
    }

    /**
     * Organize the saving of a link, using the parent's save method and
     * updating the item_property table
     * @param array $params
     * @param boolean $show_query Whether to show the query in logs when
     * calling parent's save method
     *
     * @return bool True if link could be saved, false otherwise
     */
    public function save($params, $show_query = null)
    {
        $course_info = $this->getCourse();
        $courseId = $course_info['real_id'];

        $params['session_id'] = api_get_session_id();
        $params['category_id'] = isset($params['category_id']) ? $params['category_id'] : 0;

        $sql =  "SELECT MAX(display_order)
                FROM  ".$this->table."
                WHERE
                    c_id = $courseId AND
                    category_id = '" . intval($params['category_id'])."'";
        $result = Database:: query($sql);
        list ($orderMax) = Database:: fetch_row($result);
        $order = $orderMax + 1;
        $params['display_order'] = $order;

        $id = parent::save($params, $show_query);

        if (!empty($id)) {
            // iid
            $sql = "UPDATE ".$this->table." SET id = iid WHERE iid = $id";
            Database:: query($sql);

            api_set_default_visibility($id, TOOL_LINK);

            api_item_property_update(
                $course_info,
                TOOL_LINK,
                $id,
                'LinkAdded',
                api_get_user_id()
            );
        }

        return $id;
    }

    /**
     * Update a link in the database
     * @param int $linkId The ID of the link to update
     * @param string $linkUrl The new URL to be saved
     * @param int   Course ID
     * @param int   Session ID
     * @return bool
     */
    public function updateLink(
        $linkId,
        $linkUrl,
        $courseId = null,
        $sessionId = null
    ) {
        $tblLink = Database:: get_course_table(TABLE_LINK);
        $linkUrl = Database::escape_string($linkUrl);
        $linkId = intval($linkId);
        if (is_null($courseId)) {
            $courseId = api_get_course_int_id();
        }
        $courseId = intval($courseId);
        if (is_null($sessionId)) {
            $sessionId = api_get_session_id();
        }
        $sessionId = intval($sessionId);
        if ($linkUrl != '') {
            $sql = "UPDATE $tblLink SET url = '$linkUrl'
                    WHERE id = $linkId AND c_id = $courseId AND session_id = $sessionId";
            $resLink = Database::query($sql);

            return $resLink;
        }

        return false;
    }

    /**
     * Used to add a link or a category
     * @param string $type , "link" or "category"
     * @todo replace strings by constants
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @return bool True on success, false on failure
     */
    public static function addlinkcategory($type)
    {
        global $catlinkstatus;
        global $msgErr;

        $ok = true;
        $_course = api_get_course_info();
        $course_id = $_course['real_id'];
        $session_id = api_get_session_id();

        if ($type == 'link') {
            $title = Security::remove_XSS(stripslashes($_POST['title']));
            $urllink = Security::remove_XSS($_POST['url']);
            $description = Security::remove_XSS($_POST['description']);
            $selectcategory = Security::remove_XSS($_POST['category_id']);

            if (!isset($_POST['on_homepage'])) {
                $onhomepage = 0;
            } else {
                $onhomepage = Security::remove_XSS($_POST['on_homepage']);
            }

            if (empty($_POST['target'])) {
                $target = '_self'; // Default target.
            } else {
                $target = Security::remove_XSS($_POST['target']);
            }

            $urllink = trim($urllink);
            $title = trim($title);
            $description = trim($description);

            // We ensure URL to be absolute.
            if (strpos($urllink, '://') === false) {
                $urllink = 'http://' . $urllink;
            }

            // If the title is empty, we use the URL as title.
            if ($title == '') {
                $title = $urllink;
            }

            // If the URL is invalid, an error occurs.
            if (!api_valid_url($urllink, true)) {
                // A check against an absolute URL
                Display::addFlash(Display::return_message(get_lang('GiveURL'), 'error'));

                return false;
            } else {
                // Looking for the largest order number for this category.

                $link = new Link();
                $params = [
                    'c_id' => $course_id,
                    'url' => $urllink,
                    'title' => $title,
                    'description' => $description ,
                    'category_id' => $selectcategory,
                    'on_homepage' => $onhomepage,
                    'target' => $target,
                    'session_id' => $session_id,
                ];
                $link_id = $link->save($params);

                $catlinkstatus = get_lang('LinkAdded');

                if ((api_get_setting('search_enabled') == 'true') &&
                    $link_id && extension_loaded('xapian')
                ) {
                    require_once api_get_path(LIBRARY_PATH) . 'search/ChamiloIndexer.class.php';
                    require_once api_get_path(LIBRARY_PATH) . 'search/IndexableChunk.class.php';
                    require_once api_get_path(LIBRARY_PATH) . 'specific_fields_manager.lib.php';

                    $course_int_id = $_course['real_id'];
                    $courseCode = $_course['code'];
                    $specific_fields = get_specific_field_list();
                    $ic_slide = new IndexableChunk();

                    // Add all terms to db.
                    $all_specific_terms = '';
                    foreach ($specific_fields as $specific_field) {
                        if (isset($_REQUEST[$specific_field['code']])) {
                            $sterms = trim($_REQUEST[$specific_field['code']]);
                            if (!empty($sterms)) {
                                $all_specific_terms .= ' ' . $sterms;
                                $sterms = explode(',', $sterms);
                                foreach ($sterms as $sterm) {
                                    $ic_slide->addTerm(
                                        trim($sterm),
                                        $specific_field['code']
                                    );
                                    add_specific_field_value(
                                        $specific_field['id'],
                                        $courseCode,
                                        TOOL_LINK,
                                        $link_id,
                                        $sterm
                                    );
                                }
                            }
                        }
                    }

                    // Build the chunk to index.
                    $ic_slide->addValue('title', $title);
                    $ic_slide->addCourseId($courseCode);
                    $ic_slide->addToolId(TOOL_LINK);
                    $xapian_data = array(
                        SE_COURSE_ID => $courseCode,
                        SE_TOOL_ID => TOOL_LINK,
                        SE_DATA => array(
                            'link_id' => (int)$link_id
                        ),
                        SE_USER => (int)api_get_user_id(),
                    );
                    $ic_slide->xapian_data = serialize($xapian_data);
                    $description = $all_specific_terms . ' ' . $description;
                    $ic_slide->addValue('content', $description);

                    // Add category name if set.
                    if (isset($selectcategory) && $selectcategory > 0) {
                        $table_link_category = Database:: get_course_table(
                            TABLE_LINK_CATEGORY
                        );
                        $sql_cat = 'SELECT * FROM %s WHERE id=%d AND c_id = %d LIMIT 1';
                        $sql_cat = sprintf(
                            $sql_cat,
                            $table_link_category,
                            (int)$selectcategory,
                            $course_int_id
                        );
                        $result = Database:: query($sql_cat);
                        if (Database:: num_rows($result) == 1) {
                            $row = Database:: fetch_array($result);
                            $ic_slide->addValue(
                                'category',
                                $row['category_title']
                            );
                        }
                    }

                    $di = new ChamiloIndexer();
                    isset($_POST['language']) ? $lang = Database:: escape_string(
                        $_POST['language']
                    ) : $lang = 'english';
                    $di->connectDb(null, null, $lang);
                    $di->addChunk($ic_slide);

                    // Index and return search engine document id.
                    $did = $di->index();
                    if ($did) {
                        // Save it to db.
                        $tbl_se_ref = Database:: get_main_table(
                            TABLE_MAIN_SEARCH_ENGINE_REF
                        );
                        $sql = 'INSERT INTO %s (id, course_code, tool_id, ref_id_high_level, search_did)
                                VALUES (NULL , \'%s\', \'%s\', %s, %s)';
                        $sql = sprintf(
                            $sql,
                            $tbl_se_ref,
                            $course_int_id,
                            $courseCode,
                            TOOL_LINK,
                            $link_id,
                            $did
                        );
                        Database:: query($sql);
                    }
                }
                Display::addFlash(Display::return_message(get_lang('LinkAdded')));
            }
        } elseif ($type == 'category') {
            $tbl_categories = Database:: get_course_table(TABLE_LINK_CATEGORY);

            $category_title = trim($_POST['category_title']);
            $description = trim($_POST['description']);

            if (empty($category_title)) {
                Display:: display_error_message(get_lang('GiveCategoryName'));
                $ok = false;
            } else {
                // Looking for the largest order number for this category.
                $result = Database:: query(
                    "SELECT MAX(display_order) FROM  $tbl_categories
                    WHERE c_id = $course_id "
                );
                list ($orderMax) = Database:: fetch_row($result);
                $order = $orderMax + 1;
                $order = intval($order);
                $session_id = api_get_session_id();

                $params = [
                    'c_id' => $course_id,
                    'category_title' => $category_title,
                    'description' => $description,
                    'display_order' => $order,
                    'session_id' => $session_id
                ];
                $linkId = Database::insert($tbl_categories, $params);

                if ($linkId) {
                    // iid
                    $sql = "UPDATE $tbl_categories SET id = iid WHERE iid = $linkId";
                    Database:: query($sql);

                    // add link_category visibility
                    // course ID is taken from context in api_set_default_visibility
                    api_set_default_visibility($linkId, TOOL_LINK_CATEGORY);
                }

                Display::addFlash(Display::return_message(get_lang('CategoryAdded')));
            }
        }

        return $ok;
    }

    /**
     * Used to delete a link or a category
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @param int $id
     * @param string $type The type of item to delete
     * @return bool
     */
    public static function deletelinkcategory($id, $type)
    {
        $courseInfo = api_get_course_info();
        $tbl_link = Database:: get_course_table(TABLE_LINK);
        $tbl_categories = Database:: get_course_table(TABLE_LINK_CATEGORY);

        $course_id = $courseInfo['real_id'];
        $id = intval($id);

        if (empty($id)) {
            return false;
        }

        $result = false;

        switch ($type) {
            case 'link':
                // -> Items are no longer physically deleted,
                // but the visibility is set to 2 (in item_property).
                // This will make a restore function possible for the platform administrator.

                $sql = "UPDATE $tbl_link SET on_homepage='0'
                        WHERE c_id = $course_id AND id='" . $id . "'";
                Database:: query($sql);

                api_item_property_update(
                    $courseInfo,
                    TOOL_LINK,
                    $id,
                    'delete',
                    api_get_user_id()
                );
                self::delete_link_from_search_engine(api_get_course_id(), $id);
                Display:: display_confirmation_message(get_lang('LinkDeleted'));
                $result = true;
                break;
            case 'category':
                // First we delete the category itself and afterwards all the links of this category.
                $sql = "DELETE FROM " . $tbl_categories . "
                        WHERE c_id = $course_id AND id='" . $id . "'";
                Database:: query($sql);

                $sql = "DELETE FROM " . $tbl_link . "
                        WHERE c_id = $course_id AND category_id='" . $id . "'";
                Database:: query($sql);

                api_item_property_update(
                    $courseInfo,
                    TOOL_LINK_CATEGORY,
                    $id,
                    'delete',
                    api_get_user_id()
                );

                Display::addFlash(Display::return_message(get_lang('CategoryDeleted')));
                $result = true;
                break;
        }

        return $result;
    }

    /**
     * Removes a link from search engine database
     * @param string $course_id Course code
     * @param int $link_id Document id to delete
     * @return void
     */
    public static function delete_link_from_search_engine($course_id, $link_id)
    {
        // Remove from search engine if enabled.
        if (api_get_setting('search_enabled') === 'true') {
            $tbl_se_ref = Database:: get_main_table(
                TABLE_MAIN_SEARCH_ENGINE_REF
            );
            $sql = 'SELECT * FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s LIMIT 1';
            $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_LINK, $link_id);
            $res = Database:: query($sql);
            if (Database:: num_rows($res) > 0) {
                $row = Database:: fetch_array($res);
                require_once api_get_path(LIBRARY_PATH) . 'search/ChamiloIndexer.class.php';
                $di = new ChamiloIndexer();
                $di->remove_document((int)$row['search_did']);
            }
            $sql = 'DELETE FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s LIMIT 1';
            $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_LINK, $link_id);
            Database:: query($sql);

            // Remove terms from db.
            require_once api_get_path(LIBRARY_PATH) . 'specific_fields_manager.lib.php';
            delete_all_values_for_item($course_id, TOOL_DOCUMENT, $link_id);
        }
    }

    /**
     *
     * Get link info
     * @param int link id
     * @return array link info
     *
     **/
    public static function get_link_info($id)
    {
        $tbl_link = Database:: get_course_table(TABLE_LINK);
        $course_id = api_get_course_int_id();
        $sql = "SELECT * FROM " . $tbl_link . "
                WHERE c_id = $course_id AND id='" . intval($id) . "' ";
        $result = Database::query($sql);
        $data = array();
        if (Database::num_rows($result)) {
            $data = Database::fetch_array($result);
        }
        return $data;
    }

    /**
     * @param int $id
     * @param array $values
     */
    public static function editLink($id, $values = array())
    {
        $tbl_link = Database:: get_course_table(TABLE_LINK);
        $_course = api_get_course_info();
        $course_id = $_course['real_id'];

        $values['url'] = trim($values['url']);
        $values['title'] = trim($values['title']);
        $values['description'] = trim($values['description']);
        $values['target'] = empty($values['target']) ? '_self' : $values['target'];
        $values['on_homepage'] = isset($values['on_homepage']) ? $values['on_homepage'] : '';

        $categoryId = intval($values['category_id']);

        // We ensure URL to be absolute.
        if (strpos($values['url'], '://') === false) {
            $values['url'] = 'http://' . $_POST['url'];
        }

        // If the title is empty, we use the URL as title.
        if ($values['title'] == '') {
            $values['title'] = $values['url'];
        }

        // If the URL is invalid, an error occurs.
        if (!api_valid_url($values['url'], true)) {
            Display::addFlash(
                Display::return_message(get_lang('GiveURL'), 'error')
            );

            return false;
        }

        // Finding the old category_id.
        $sql = "SELECT * FROM " . $tbl_link . "
                WHERE c_id = $course_id AND id='" . $id . "'";
        $result = Database:: query($sql);
        $row = Database:: fetch_array($result);
        $category_id = $row['category_id'];

        if ($category_id != $values['category_id']) {
            $sql = "SELECT MAX(display_order)
                    FROM " . $tbl_link . "
                    WHERE
                        c_id = $course_id AND
                        category_id='" . intval($values['category_id']) . "'";
            $result = Database:: query($sql);
            list ($max_display_order) = Database:: fetch_row($result);
            $max_display_order++;
        } else {
            $max_display_order = $row['display_order'];
        }
        $params = [
            'url' => $values['url'],
            'title' => $values['title'],
            'description' => $values['description'],
            'category_id' => $values['category_id'],
            'display_order' => $max_display_order,
            'on_homepage' => $values['on_homepage'],
            'target' => $values['target'],
            'category_id' => $values['category_id']
        ];
        Database::update($tbl_link, $params, ['c_id = ? AND id = ?' => [$course_id, $id] ]);

        // Update search enchine and its values table if enabled.
        if (api_get_setting('search_enabled') == 'true') {
            $course_int_id = api_get_course_int_id();
            $course_id = api_get_course_id();
            $link_title = Database:: escape_string($values['title']);
            $link_description = Database:: escape_string($values['description']);

            // Actually, it consists on delete terms from db,
            // insert new ones, create a new search engine document, and remove the old one.
            // Get search_did.
            $tbl_se_ref = Database:: get_main_table(
                TABLE_MAIN_SEARCH_ENGINE_REF
            );
            $sql = 'SELECT * FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s LIMIT 1';
            $sql = sprintf(
                $sql,
                $tbl_se_ref,
                $course_id,
                TOOL_LINK,
                $id
            );
            $res = Database:: query($sql);

            if (Database:: num_rows($res) > 0) {
                require_once api_get_path(LIBRARY_PATH).'search/ChamiloIndexer.class.php';
                require_once api_get_path(LIBRARY_PATH).'search/IndexableChunk.class.php';
                require_once api_get_path(LIBRARY_PATH).'specific_fields_manager.lib.php';

                $se_ref = Database:: fetch_array($res);
                $specific_fields = get_specific_field_list();
                $ic_slide = new IndexableChunk();

                $all_specific_terms = '';
                foreach ($specific_fields as $specific_field) {
                    delete_all_specific_field_value(
                        $course_id,
                        $specific_field['id'],
                        TOOL_LINK,
                        $id
                    );
                    if (isset($_REQUEST[$specific_field['code']])) {
                        $sterms = trim(
                            $_REQUEST[$specific_field['code']]
                        );
                        if (!empty($sterms)) {
                            $all_specific_terms .= ' ' . $sterms;
                            $sterms = explode(',', $sterms);
                            foreach ($sterms as $sterm) {
                                $ic_slide->addTerm(
                                    trim($sterm),
                                    $specific_field['code']
                                );
                                add_specific_field_value(
                                    $specific_field['id'],
                                    $course_id,
                                    TOOL_LINK,
                                    $id,
                                    $sterm
                                );
                            }
                        }
                    }
                }

                // Build the chunk to index.
                $ic_slide->addValue("title", $link_title);
                $ic_slide->addCourseId($course_id);
                $ic_slide->addToolId(TOOL_LINK);
                $xapian_data = array(
                    SE_COURSE_ID => $course_id,
                    SE_TOOL_ID => TOOL_LINK,
                    SE_DATA => array(
                        'link_id' => (int)$id
                    ),
                    SE_USER => (int)api_get_user_id(),

                );
                $ic_slide->xapian_data = serialize($xapian_data);
                $link_description = $all_specific_terms . ' ' . $link_description;
                $ic_slide->addValue('content', $link_description);

                // Add category name if set.
                if (isset($categoryId) && $categoryId > 0) {
                    $table_link_category = Database:: get_course_table(
                        TABLE_LINK_CATEGORY
                    );
                    $sql_cat = 'SELECT * FROM %s WHERE id=%d and c_id = %d LIMIT 1';
                    $sql_cat = sprintf(
                        $sql_cat,
                        $table_link_category,
                        $categoryId,
                        $course_int_id
                    );
                    $result = Database:: query($sql_cat);
                    if (Database:: num_rows($result) == 1) {
                        $row = Database:: fetch_array($result);
                        $ic_slide->addValue(
                            'category',
                            $row['category_title']
                        );
                    }
                }

                $di = new ChamiloIndexer();
                isset ($_POST['language']) ? $lang = Database:: escape_string($_POST['language']) : $lang = 'english';
                $di->connectDb(null, null, $lang);
                $di->remove_document((int)$se_ref['search_did']);
                $di->addChunk($ic_slide);

                // Index and return search engine document id.
                $did = $di->index();
                if ($did) {
                    // Save it to db.
                    $sql = 'DELETE FROM %s
                            WHERE course_code=\'%s\'
                            AND tool_id=\'%s\'
                            AND ref_id_high_level=\'%s\'';
                    $sql = sprintf(
                        $sql,
                        $tbl_se_ref,
                        $course_id,
                        TOOL_LINK,
                        $id
                    );
                    Database:: query($sql);
                    $sql = 'INSERT INTO %s (c_id, id, course_code, tool_id, ref_id_high_level, search_did)
                            VALUES (NULL , \'%s\', \'%s\', %s, %s)';
                    $sql = sprintf(
                        $sql,
                        $tbl_se_ref,
                        $course_int_id,
                        $course_id,
                        TOOL_LINK,
                        $id,
                        $did
                    );
                    Database:: query($sql);
                }
            }
        }

        // "WHAT'S NEW" notification: update table last_toolEdit.
        api_item_property_update(
            $_course,
            TOOL_LINK,
            $id,
            'LinkUpdated',
            api_get_user_id()
        );
        Display::addFlash(Display::return_message(get_lang('LinkModded')));
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function editCategory($id, $values)
    {
        $tbl_categories = Database:: get_course_table(TABLE_LINK_CATEGORY);
        $course_id = api_get_course_int_id();
        $id = intval($id);

        // This is used to put the modified info of the category-form into the database.
        $params = [
            'category_title' => $values['category_title'],
            'description' => $values['description']
        ];
        Database::update($tbl_categories, $params, ['c_id = ? AND id = ?' => [$course_id, $id]]);
        Display::addFlash(Display::return_message(get_lang('CategoryModded')));

        return true;
    }

    /**
     * Changes the visibility of a link
     * @todo add the changing of the visibility of a course
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function change_visibility_link($id, $scope)
    {
        $_course = api_get_course_info();
        $_user = api_get_user_info();
        if ($scope == TOOL_LINK) {
            api_item_property_update(
                $_course,
                TOOL_LINK,
                $id,
                $_GET['action'],
                $_user['user_id']
            );
            Display::addFlash(Display::return_message(get_lang('VisibilityChanged')));

        } elseif ($scope == TOOL_LINK_CATEGORY) {
            api_item_property_update(
                $_course,
                TOOL_LINK_CATEGORY,
                $id,
                $_GET['action'],
                $_user['user_id']
            );
            Display::addFlash(Display::return_message(get_lang('VisibilityChanged')));
        }
    }

    /**
     * Generate SQL to select all the links categories in the current course and
     * session
     * @param   int $courseId
     * @param   int $sessionId
     * @return array
     */
    public static function getLinkCategories($courseId, $sessionId)
    {
        $tblLinkCategory = Database:: get_course_table(TABLE_LINK_CATEGORY);
        $tblItemProperty = Database:: get_course_table(TABLE_ITEM_PROPERTY);
        $courseId = intval($courseId);
        // Condition for the session.
        $sessionCondition = api_get_session_condition($sessionId, true, true, 'linkcat.session_id');

        // Getting links
        $sql = "SELECT *, linkcat.id
                FROM $tblLinkCategory linkcat
                WHERE
                    linkcat.c_id = " . $courseId . "
                    $sessionCondition
                ORDER BY linkcat.display_order DESC";

        $result = Database::query($sql);
        $categories = Database::store_result($result);

        $sql = "SELECT *, linkcat.id
                FROM $tblLinkCategory linkcat
                INNER JOIN $tblItemProperty itemproperties
                ON (linkcat.id = itemproperties.ref AND linkcat.c_id = itemproperties.c_id)
                WHERE
                    itemproperties.tool = '" . TOOL_LINK_CATEGORY . "' AND
                    (itemproperties.visibility = '0' OR itemproperties.visibility = '1')
                    $sessionCondition AND
                    linkcat.c_id = " . $courseId . "
                ORDER BY linkcat.display_order DESC";

        $result = Database::query($sql);

        $categoryInItemProperty = array();
        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                $categoryInItemProperty[$row['id']] = $row;
            }
        }

        foreach ($categories as & $category) {
            if (!isset($categoryInItemProperty[$category['id']])) {
                api_set_default_visibility($category['id'], TOOL_LINK_CATEGORY);
            }
        }

        $sql = "SELECT DISTINCT linkcat.*, visibility
                FROM $tblLinkCategory linkcat
                INNER JOIN $tblItemProperty itemproperties
                ON (linkcat.id = itemproperties.ref AND linkcat.c_id = itemproperties.c_id)
                WHERE
                    itemproperties.tool = '" . TOOL_LINK_CATEGORY . "' AND
                    (itemproperties.visibility = '0' OR itemproperties.visibility = '1')
                    $sessionCondition AND
                    linkcat.c_id = " . $courseId . "
                ORDER BY linkcat.display_order DESC
                ";
        $result = Database::query($sql);

        return Database::store_result($result, 'ASSOC');
    }

    /**
     * Displays all the links of a given category.
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function showlinksofcategory($catid)
    {
        global $token;
        $_user = api_get_user_info();
        $course_id = api_get_course_int_id();
        $session_id = api_get_session_id();
        $catid = intval($catid);

        $tbl_link = Database:: get_course_table(TABLE_LINK);
        $TABLE_ITEM_PROPERTY = Database:: get_course_table(TABLE_ITEM_PROPERTY);

        // Condition for the session.
        $condition_session = api_get_session_condition($session_id, true, true, 'link.session_id');

        $content = '';

        $sql = "SELECT *, link.id FROM $tbl_link link
                INNER JOIN $TABLE_ITEM_PROPERTY itemproperties
                ON (link.id=itemproperties.ref AND link.c_id = itemproperties.c_id)
                WHERE
                    itemproperties.tool='" . TOOL_LINK . "' AND
                    link.category_id='" . $catid . "' AND
                    (itemproperties.visibility='0' OR itemproperties.visibility='1')
                    $condition_session AND
                    link.c_id = " . $course_id . " AND
                    itemproperties.c_id = " . $course_id . "
                ORDER BY link.display_order ASC";
        $result = Database:: query($sql);
        $numberoflinks = Database:: num_rows($result);
        if ($numberoflinks > 0) {
            $content .= '<div class="link list-group">';
            $i = 1;
            while ($myrow = Database:: fetch_array($result)) {
                // Validation when belongs to a session.
                $session_img = api_get_session_image(
                    $myrow['session_id'],
                    $_user['status']
                );

                $toolbar = '';
                $link_validator = '';
                if (api_is_allowed_to_edit(null, true)) {
                    $toolbar .= Display::toolbarButton(
                        '',
                        '#',
                        'check-circle-o',
                        'default btn-sm',
                        array(
                            'onclick' => "check_url('" . $myrow['id'] . "', '" . addslashes($myrow['url']) . "');",
                            'title' => get_lang('CheckURL')
                        )
                    );
                    $link_validator .= Display::span(
                        '',
                        array(
                        'id' => 'url_id_' . $myrow['id'],
                        'class' => 'check-link'
                        )
                    );

                    if ($session_id == $myrow['session_id']) {
                        $url = api_get_self() . '?' . api_get_cidreq() .
                            '&action=editlink&category=' . (!empty ($category) ? $category : '') .
                            '&id=' . $myrow['id'] .
                            '&category_id=' . $myrow['id'];
                        $title = get_lang('Edit');
                        $toolbar .= Display::toolbarButton(
                            '',
                            $url,
                            'pencil',
                            'default btn-sm',
                            array(
                                'title' => $title
                            )
                        );

                        if ($myrow['visibility'] == '1') {
                            $url .= 'link.php?' . api_get_cidreq() .
                                '&sec_token=' . $token .
                                '&action=invisible&id=' . $myrow['id'] .
                                '&scope=link&category_id=' . $myrow['category_id'];
                            $title = get_lang('MakeInvisible');
                            $toolbar .= Display::toolbarButton(
                                '',
                                $url,
                                'eye',
                                'default btn-sm',
                                array(
                                    'title' => $title
                                )
                            );
                        }

                        if ($myrow['visibility'] == '0') {
                            $url .= 'link.php?' . api_get_cidreq() .'&sec_token=' . $token .'&action=visible&id=' . $myrow['id'] .'&scope=link&category_id=' . $myrow['category_id'];
                            $title = get_lang('MakeVisible');
                            $toolbar .= Display::toolbarButton(
                                '',
                                $url,
                                'eye-slash',
                                'primary btn-sm',
                                array(
                                    'title' => $title
                                )
                            );
                        }

                        $moveLinkParams = [
                            'id' => $myrow['id'],
                            'scope' => 'category',
                            'category_id' => $myrow['category_id'],
                            'action' => 'move_link_up'
                        ];
                        $toolbar .= Display::toolbarButton(
                            get_lang('MoveUp'),
                            'link.php?' . api_get_cidreq() . '&' . http_build_query($moveLinkParams),
                            'level-up',
                            'default',
                            ['class' => 'btn-sm ' . ($i === 1 ? 'disabled' : '')],
                            false
                        );
                        $moveLinkParams['action'] = 'move_link_down';
                        $toolbar .= Display::toolbarButton(
                            get_lang('MoveDown'),
                            'link.php?' . api_get_cidreq() . '&' . http_build_query($moveLinkParams),
                            'level-down',
                            'default',
                            ['class' => 'btn-sm ' . ($i === $numberoflinks ? 'disabled' : '')],
                            false
                        );

                        $url .= api_get_self() . '?' . api_get_cidreq() .'&sec_token=' . $token .'&action=deletelink&id=' . $myrow['id'] .'&category_id=' . $myrow['category_id'];
                        $event = "javascript: if(!confirm('" . get_lang('LinkDelconfirm') . "'))return false;";
                        $title = get_lang('Delete');

                        $toolbar .= Display::toolbarButton(
                            '',
                            $url,
                            'trash',
                            'default btn-sm',
                            array(
                                'onclick' => $event,
                                'title' => $title
                            )
                        );
                    } else {
                        $title = get_lang('EditionNotAvailableFromSession');
                        $toolbar .= Display::toolbarButton(
                            '',
                            '#',
                            'trash-o',
                            'default btn-sm',
                            array(
                                'title' => $title
                            )
                        );
                    }
                }
                $iconLink = Display::return_icon(
                    'url.png',
                    get_lang('Link'),
                    null,
                    ICON_SIZE_SMALL
                );

                if ($myrow['visibility'] == '1') {
                    $content .= '<div class="list-group-item">';
                    $content .= '<div class="pull-right"><div class="btn-group">'.$toolbar.'</div></div>';
                    $content .= '<h4 class="list-group-item-heading">';
                    $content .= $iconLink;
                    $url =  'link_goto.php?' . api_get_cidreq() .'&link_id=' . $myrow['id'] .'&link_url=' . urlencode($myrow['url']);
                    $content .= Display::tag(
                        'a',
                        Security:: remove_XSS($myrow['title']),
                        array(
                            'href' => $url,
                            'target' => $myrow['target']
                        )
                    );
                    $content .= $link_validator;
                    $content .= $session_img;
                    $content .= '</h4>';

                    $content .= '<p class="list-group-item-text">' . $myrow['description'] . '</p>';
                    $content .= '</div>';
                } else {
                    if (api_is_allowed_to_edit(null, true)) {
                        $content .= '<div class="list-group-item">';
                        $content .= '<div class="pull-right"><div class="btn-group">'.$toolbar.'</div></div>';
                        $content .= '<h4 class="list-group-item-heading">';
                        $content .= $iconLink;
                        $url = 'link_goto.php?' . api_get_cidreq() .'&link_id=' . $myrow['id'] . "&link_url=" . urlencode($myrow['url']);
                        $content .= Display::tag(
                            'a',
                            Security:: remove_XSS($myrow['title']),
                            array(
                                'href' => $url,
                                'target' => '_blank',
                                'class' => 'text-muted'
                            )
                        );
                        $content .= $link_validator;
                        $content .= $session_img;
                        $content .= '</h4>';
                        $content .= '<p class="list-group-item-text">' . $myrow['description'] . '</p>';
                        $content .= '</div>';
                    }
                }
                $i++;
            }
            $content .= '</div>';
        }

        return $content;
    }

    /**
     * Displays the edit, delete and move icons
     * @param int   Category ID
     * @return void
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function showCategoryAdminTools($category, $currentCategory, $countCategories)
    {
        $categoryId = $category['id'];
        $token = null;
        $tools = '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&sec_token=' . $token . '&action=editcategory&id=' . $categoryId . '&category_id=' . $categoryId . '" title=' . get_lang('Modify') . '">' .
            Display:: return_icon(
                'edit.png',
                get_lang('Modify'),
                array(),
                ICON_SIZE_SMALL
            ) . '</a>';

        // DISPLAY MOVE UP COMMAND only if it is not the top link.
        if ($currentCategory != 0) {
            $tools .= '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&sec_token=' . $token . '&action=up&up='.$categoryId.'&category_id='.$categoryId.'" title="'.get_lang('Up').'">'.
                Display:: return_icon(
                    'up.png',
                    get_lang('Up'),
                    array(),
                    ICON_SIZE_SMALL
                ) . '</a>';
        } else {
            $tools .= Display:: return_icon(
                'up_na.png',
                get_lang('Up'),
                array(),
                ICON_SIZE_SMALL
            ) . '</a>';
        }

        // DISPLAY MOVE DOWN COMMAND only if it is not the bottom link.
        if ($currentCategory < $countCategories-1) {
            $tools .= '<a href="' . api_get_self() . '?' . api_get_cidreq() .'&sec_token=' . $token .'&action=down&down=' . $categoryId .'&category_id=' . $categoryId . '">'.
                Display:: return_icon(
                    'down.png',
                    get_lang('Down'),
                    array(),
                    ICON_SIZE_SMALL
                ) . '</a>';
        } else {
            $tools .= Display:: return_icon(
                    'down_na.png',
                    get_lang('Down'),
                    array(),
                    ICON_SIZE_SMALL
                ) . '</a>';
        }

        $tools .= '<a href="' . api_get_self() . '?' . api_get_cidreq() .'&sec_token=' . $token .'&action=deletecategory&id='.$categoryId.            "&category_id=$categoryId\"
            onclick=\"javascript: if(!confirm('" . get_lang('CategoryDelconfirm') . "')) return false;\">".
            Display:: return_icon(
                'delete.png',
                get_lang('Delete'),
                array(),
                ICON_SIZE_SMALL
            ) . '</a>';

        return $tools;
    }

    /**
     * move a link or a linkcategory up or down
     * @param   int Category ID
     * @param   int Course ID
     * @param   int Session ID
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @todo support sessions
     */
    public static function movecatlink($action, $catlinkid, $courseId = null, $sessionId = null)
    {
        $tbl_categories = Database:: get_course_table(TABLE_LINK_CATEGORY);

        if (is_null($courseId)) {
            $courseId = api_get_course_int_id();
        }
        $courseId = intval($courseId);
        if (is_null($sessionId)) {
            $sessionId = api_get_session_id();
        }
        $sessionId = intval($sessionId);
        $thiscatlinkId = intval($catlinkid);

        if ($action == 'down') {
            $sortDirection = 'DESC';
        }

        if ($action == 'up') {
            $sortDirection = 'ASC';
        }

        $movetable = $tbl_categories;

        if (!empty($sortDirection)) {
            if (!in_array(trim(strtoupper($sortDirection)), array('ASC', 'DESC'))) {
                $sortDirection = 'ASC';
            }

            $sql = "SELECT id, display_order FROM $movetable
                    WHERE c_id = $courseId
                    ORDER BY display_order $sortDirection";
            $linkresult = Database:: query($sql);
            $thislinkOrder = 1;
            while ($sortrow = Database:: fetch_array($linkresult)) {
                // STEP 2 : FOUND THE NEXT LINK ID AND ORDER, COMMIT SWAP
                // This part seems unlogic, but it isn't . We first look for the current link with the querystring ID
                // and we know the next iteration of the while loop is the next one. These should be swapped.
                if (isset($thislinkFound) && $thislinkFound) {
                    $nextlinkId = $sortrow['id'];
                    $nextlinkOrder = $sortrow['display_order'];

                    Database:: query(
                        "UPDATE " . $movetable . "
                        SET display_order = '$nextlinkOrder'
                        WHERE c_id = $courseId  AND id =  '$thiscatlinkId'"
                    );
                    Database:: query(
                        "UPDATE " . $movetable . "
                        SET display_order = '$thislinkOrder'
                        WHERE c_id = $courseId  AND id =  '$nextlinkId'"
                    );

                    break;
                }
                if ($sortrow['id'] == $thiscatlinkId) {
                    $thislinkOrder = $sortrow['display_order'];
                    $thislinkFound = true;
                }
            }
        }

        Display::addFlash(Display::return_message(get_lang('LinkMoved')));
    }

    /**
     * CSV file import functions
     * @author René Haentjens , Ghent University
     */
    public static function get_cat($catname)
    {
        // Get category id (existing or make new).
        $tbl_categories = Database:: get_course_table(TABLE_LINK_CATEGORY);
        $course_id = api_get_course_int_id();

        $result = Database:: query(
            "SELECT id FROM " . $tbl_categories . "
            WHERE c_id = $course_id AND category_title='" . Database::escape_string(
                $catname
            ) . "'"
        );
        if (Database:: num_rows($result) >= 1 && ($row = Database:: fetch_array(
                $result
            ))
        ) {
            return $row['id']; // Several categories with same name: take the first.
        }

        $result = Database:: query(
            "SELECT MAX(display_order) FROM " . $tbl_categories . " WHERE c_id = $course_id "
        );
        list ($max_order) = Database:: fetch_row($result);

        $params = [
            'c_id' => $course_id,
            'category_title' => $catname,
            'description' => '',
            'display_order' => $max_order + 1
        ];
        $id = Database::insert($tbl_categories, $params);

        return $id;
    }

    /**
     * CSV file import functions
     * @author René Haentjens , Ghent University
     */
    public static function put_link($url, $cat, $title, $description, $on_homepage, $hidden)
    {
        $_course = api_get_course_info();
        $_user = api_get_user_info();

        $tbl_link = Database:: get_course_table(TABLE_LINK);
        $course_id = api_get_course_int_id();

        $urleq = "url='" . Database:: escape_string($url) . "'";
        $cateq = "category_id=" . intval($cat);

        $result = Database:: query("
            SELECT id FROM $tbl_link
            WHERE c_id = $course_id AND " . $urleq . ' AND ' . $cateq
        );

        if (Database:: num_rows($result) >= 1 && ($row = Database:: fetch_array($result))) {
            $sql = "UPDATE $tbl_link SET 
                        title = '" . Database:: escape_string($title) . "', 
                        description = '" . Database:: escape_string($description) . "'
                    WHERE c_id = $course_id AND  id='" . Database:: escape_string($row['id']) . "'";
            Database:: query($sql);

            $ipu = 'LinkUpdated';
            $rv = 1; // 1 = upd
        } else {
            // Add new link
            $result = Database:: query(
                "SELECT MAX(display_order) FROM  $tbl_link
                WHERE c_id = $course_id AND category_id='" . intval($cat) . "'"
            );
            list ($max_order) = Database:: fetch_row($result);

            Database:: query(
                "INSERT INTO $tbl_link (c_id, url, title, description, category_id, display_order, on_homepage)
                VALUES (" . api_get_course_int_id() . ",
                '" . Database:: escape_string($url) . "',
                '" . Database:: escape_string($title) . "',
                '" . Database:: escape_string($description) . "',
                '" . intval($cat) . "','" . (intval($max_order) + 1) . "',
                '" . intval($on_homepage) .
                "')"
            );

            $id = Database:: insert_id();
            $ipu = 'LinkAdded';
            $rv = 2; // 2 = new
        }

        api_item_property_update(
            $_course,
            TOOL_LINK,
            $id,
            $ipu,
            $_user['user_id']
        );

        if ($hidden && $ipu == 'LinkAdded') {
            api_item_property_update(
                $_course,
                TOOL_LINK,
                $id,
                'invisible',
                $_user['user_id']
            );
        }

        return $rv;
    }

    /**
     * CSV file import functions
     * @author René Haentjens , Ghent University
     */
    public static function import_link($linkdata)
    {
        // url, category_id, title, description, ...

        // Field names used in the uploaded file
        $known_fields = array(
            'url',
            'category',
            'title',
            'description',
            'on_homepage',
            'hidden'
        );

        $hide_fields = array(
            'kw',
            'kwd',
            'kwds',
            'keyword',
            'keywords'
        );

        // All other fields are added to description, as "name:value".

        // Only one hide_field is assumed to be present, <> is removed from value.

        if (!($url = trim($linkdata['url'])) || !($title = trim(
                $linkdata['title']
            ))
        ) {
            return 0; // 0 = fail
        }

        $cat = ($catname = trim($linkdata['category'])) ? Link::get_cat($catname) : 0;

        $regs = array(); // Will be passed to ereg()
        $d = '';
        foreach ($linkdata as $key => $value) {
            if (!in_array($key, $known_fields)) {
                if (in_array($key, $hide_fields) && ereg(
                        '^<?([^>]*)>?$',
                        $value,
                        $regs
                    )
                ) { // possibly in <...>
                    if (($kwlist = trim($regs[1])) != '') {
                        $kw = '<i kw="' . htmlspecialchars($kwlist) . '">';
                    } else {
                        $kw = '';
                    }
                    // i.e. assume only one of the $hide_fields will be present
                    // and if found, hide the value as expando property of an <i> tag
                } elseif (trim($value)) {
                    $d .= ', ' . $key . ':' . $value;
                }
            }
        }
        if (!empty($d)) {
            $d = substr($d, 2) . ' - ';
        }

        return Link::put_link(
            $url,
            $cat,
            $title,
            $kw . ereg_replace(
                '\[((/?(b|big|i|small|sub|sup|u))|br/)\]',
                '<\\1>',
                htmlspecialchars($d . $linkdata['description'])
            ) . ($kw ? '</i>' : ''),
            $linkdata['on_homepage'] ? '1' : '0',
            $linkdata['hidden'] ? '1' : '0'
        );
        // i.e. allow some BBcode tags, e.g. [b]...[/b]
    }

    /**
     * This function checks if the url is a vimeo link
     * @author Julio Montoya
     * @version 1.0
     */
    public static function isVimeoLink($url)
    {
        $isLink = strrpos($url, "vimeo.com");

        return $isLink;
    }

    /**
     * Get vimeo id from URL
     * @param string $url
     * @return bool|mixed
     */
    public static function getVimeoLinkId($url)
    {
        $possibleUrls = array(
            'http://www.vimeo.com/',
            'http://vimeo.com/',
            'https://www.vimeo.com/',
            'https://vimeo.com/'
        );
        $url = str_replace($possibleUrls, '', $url);

        if (is_numeric($url)) {
            return $url;
        }
        return false;
    }

    /**
     * This function checks if the url is a youtube link
     * @author Jorge Frisancho
     * @author Julio Montoya - Fixing code
     * @version 1.0
     */
    public static function is_youtube_link($url)
    {
        $is_youtube_link = strrpos($url, "youtube") || strrpos(
            $url,
            "youtu.be"
        );
        return $is_youtube_link;
    }

    /**
     * Get youtube id from an URL
     * @param string $url
     * @return string
     */
    public static function get_youtube_video_id($url)
    {
        // This is the length of YouTube's video IDs
        $len = 11;

        // The ID string starts after "v=", which is usually right after
        // "youtube.com/watch?" in the URL
        $pos = strpos($url, "v=");

        $id = '';

        //If false try other options
        if ($pos === false) {
            $url_parsed = parse_url($url);

            //Youtube shortener
            //http://youtu.be/ID
            $pos = strpos($url, "youtu.be");

            if ($pos == false) {
                $id = '';
            } else {
                return substr($url_parsed['path'], 1);
            }

            //if empty try the youtube.com/embed/ID
            if (empty($id)) {
                $pos = strpos($url, "embed");
                if ($pos === false) {
                    return '';
                } else {
                    return substr($url_parsed['path'], 7);
                }
            }
        } else {
            // Offset the start location to match the beginning of the ID string
            $pos += 2;
            // Get the ID string and return it
            $id = substr($url, $pos, $len);
            return $id;
        }
    }

    /**
     * @param int $course_id
     * @param int $session_id
     * @param int $categoryId
     * @param string $show
     * @param null $token
     */
    public static function listLinksAndCategories($course_id, $session_id, $categoryId, $show = 'none', $token = null)
    {
        $tbl_link = Database::get_course_table(TABLE_LINK);

        $_user = api_get_user_info();
        $categoryId = intval($categoryId);

        /*	Action Links */
        echo '<div class="actions">';
        if (api_is_allowed_to_edit(null, true)) {
            echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=addlink&category_id='.$categoryId.'">'.
                Display::return_icon('new_link.png', get_lang('LinkAdd'),'',ICON_SIZE_MEDIUM).'</a>';
            echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=addcategory&category_id='.$categoryId.'">'.
                Display::return_icon('new_folder.png', get_lang('CategoryAdd'),'',ICON_SIZE_MEDIUM).'</a>';
        }

        $categories = Link::getLinkCategories($course_id, $session_id);
        $count = count($categories);
        if (!empty($count)) {
            echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=list&show=none">';
            echo Display::return_icon('forum_listview.png', get_lang('FlatView'), '', ICON_SIZE_MEDIUM) . ' </a>';

            echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=list&show=all">';
            echo Display::return_icon('forum_nestedview.png', get_lang('NestedView'), '', ICON_SIZE_MEDIUM) . '</a>';
        }
        echo '</div>';

        // Displaying the links which have no category (thus category = 0 or NULL),
        // if none present this will not be displayed
        $sql = "SELECT * FROM $tbl_link
                WHERE c_id = $course_id AND (category_id=0 OR category_id IS NULL)";
        $result = Database::query($sql);
        $count = Database::num_rows($result);

        if ($count !== 0) {
            echo Display::panel(Link::showlinksofcategory(0), get_lang('General'));
        }

        $counter = 0;
        foreach ($categories as $myrow) {
            // Student don't see invisible categories.
            if (!api_is_allowed_to_edit(null, true)) {
                if ($myrow['visibility'] == 0) {
                    continue;
                }
            }

            // Validation when belongs to a session
            $showChildren = $categoryId == $myrow['id'] || $show == 'all';
            $session_img = api_get_session_image($myrow['session_id'], $_user['status']);
            $myrow['description'] = $myrow['description'];

            $strVisibility = '';
            $visibilityClass = null;
            if ($myrow['visibility'] == '1') {
                $strVisibility =  '<a href="link.php?' . api_get_cidreq() .  '&sec_token='.$token.'&action=invisible&id=' . $myrow['id'] . '&scope=' . TOOL_LINK_CATEGORY . '" title="' . get_lang('Hide') . '">' .
                    Display :: return_icon('visible.png', get_lang('Hide'), array (), ICON_SIZE_SMALL) . '</a>';
            } elseif ($myrow['visibility'] == '0') {
                $visibilityClass = 'text-muted';
                $strVisibility =  ' <a href="link.php?' . api_get_cidreq() .  '&sec_token='.$token.'&action=visible&id=' . $myrow['id'] . '&scope=' . TOOL_LINK_CATEGORY . '" title="' . get_lang('Show') . '">' .
                    Display :: return_icon('invisible.png', get_lang('Show'), array (), ICON_SIZE_SMALL) . '</a>';
            }

            $header = '';
            if ($showChildren) {
                $header .= '<a class="'.$visibilityClass.'" href="'.api_get_self().'?'.api_get_cidreq().'&category_id=">';
                $header .= Display::return_icon('forum_nestedview.png');
            } else {
                $header .= '<a class="'.$visibilityClass.'" href="'.api_get_self().'?'.api_get_cidreq().'&category_id='.$myrow['id'].'">';
                $header .= Display::return_icon('forum_listview.png');
            }

            $header .= Security::remove_XSS($myrow['category_title']).'</a>';
            $header .= '<div class="pull-right">';
            if (api_is_allowed_to_edit(null, true)) {
                if ($session_id == $myrow['session_id']) {
                    $header .= $strVisibility;
                    $header .= Link::showCategoryAdminTools($myrow, $counter, count($categories));
                } else {
                    $header .= get_lang('EditionNotAvailableFromSession');
                }
            }

            $childrenContent = '';
            if ($showChildren) {
                $childrenContent = Link::showlinksofcategory($myrow['id']);
            }

            echo Display::panel($myrow['description'].$childrenContent, $header);

            $counter++;
        }
    }

    /**
     * @param int $linkId
     * @param $action
     * @param null $token
     *
     * @return FormValidator
     */
    public static function getLinkForm($linkId, $action, $token = null)
    {
        $course_id = api_get_course_int_id();
        $session_id = api_get_session_id();
        $linkInfo = Link::get_link_info($linkId);
        $categoryId = isset($linkInfo['category_id']) ? $linkInfo['category_id'] : '';
        $lpId = isset($_GET['lp_id']) ? Security::remove_XSS($_GET['lp_id']) : null;

        $form = new FormValidator(
            'link',
            'post',
            api_get_self().'?action='.$action.
            '&category_id='.$categoryId.
            '&'.api_get_cidreq().
            '&id='.$linkId.
            '&sec_token='.$token
        );

        if ($action == 'addlink') {
            $form->addHeader(get_lang('LinkAdd'));
        } else {
            $form->addHeader(get_lang('LinkMod'));
        }

        $target_link = "_blank";
        $title = '';
        $category = '';
        $onhomepage = '';
        $description = '';

        if (!empty($linkInfo)) {
            $urllink = $linkInfo['url'];
            $title = $linkInfo['title'];
            $description = $linkInfo['description'];
            $category = $linkInfo['category_id'];
            if ($linkInfo['on_homepage'] != 0) {
                $onhomepage = 1;
            }
            $target_link = $linkInfo['target'];
        }

        $form->addHidden('id', $linkId);
        $form->addText('url', 'URL');
        $form->addRule('url', get_lang('GiveURL'), 'url');
        $form->addText('title', get_lang('LinkName'));
        $form->addTextarea('description', get_lang('Description'));

        $resultcategories = Link::getLinkCategories($course_id, $session_id);
        $options = ['0' => '--'];
        if (!empty($resultcategories)) {
            foreach ($resultcategories as $myrow) {
                $options[$myrow['id']] = $myrow['category_title'];
            }
        }

        $form->addSelect('category_id', get_lang('Category'), $options);
        $form->addCheckBox('on_homepage', null, get_lang('OnHomepage'));

        $targets = array(
            '_self' => get_lang('LinkOpenSelf'),
            '_blank' => get_lang('LinkOpenBlank'),
            '_parent' => get_lang('LinkOpenParent'),
            '_top' => get_lang('LinkOpenTop')
        );

        $form->addSelect(
            'target',
            array(
                get_lang('LinkTarget'),
                get_lang('AddTargetOfLinkOnHomepage')
            ),
            $targets
        );

        $defaults = array(
            'url' => empty($urllink) ? 'http://' : Security::remove_XSS($urllink),
            'title' => Security::remove_XSS($title),
            'category_id' => $category,
            'on_homepage' => $onhomepage,
            'description' => $description,
            'target' => $target_link
        );

        if (api_get_setting('search_enabled') == 'true') {
            require_once api_get_path(LIBRARY_PATH).'specific_fields_manager.lib.php';
            $specific_fields = get_specific_field_list();
            $form->addCheckBox('index_document', get_lang('SearchFeatureDoIndexLink'), get_lang('Yes'));

            foreach ($specific_fields as $specific_field) {
                $default_values = '';
                if ($action == 'editlink') {
                    $filter = array(
                        'field_id' => $specific_field['id'],
                        'ref_id' => intval($_GET['id']),
                        'tool_id' => '\''.TOOL_LINK.'\''
                    );
                    $values = get_specific_field_values_list($filter, array('value'));
                    if (!empty($values)) {
                        $arr_str_values = array();
                        foreach ($values as $value) {
                            $arr_str_values[] = $value['value'];
                        }
                        $default_values = implode(', ', $arr_str_values);
                    }
                }
                $form->addText($specific_field['name'], $specific_field['code']);
                $defaults[$specific_field['name']] = $default_values;
            }
        }

        $form->addHidden('lp_id', $lpId);
        $form->addButtonSave(get_lang('SaveLink'), 'submitLink');
        $form->setDefaults($defaults);

        return $form;
    }

    /**
     * @param int $id
     * @param string $action
     *
     * @return FormValidator
     */
    public static function getCategoryForm($id, $action)
    {
        $form = new FormValidator(
            'category',
            'post',
            api_get_self().'?action='.$action.'&'.api_get_cidreq()
        );

        $defaults = [];
        if ($action == 'addcategory') {
            $form->addHeader(get_lang('CategoryAdd'));
            $my_cat_title = get_lang('CategoryAdd');
        } else {
            $form->addHeader(get_lang('CategoryMod'));
            $my_cat_title = get_lang('CategoryMod');
            $defaults = self::getCategory($id);
        }
        $form->addHidden('id', $id);
        $form->addText('category_title', get_lang('CategoryName'));
        $form->addTextarea('description', get_lang('Description'));
        $form->addButtonSave($my_cat_title, 'submitCategory');
        $form->setDefaults($defaults);

        return $form;
    }

    /**
     * @param int $id
     * @return array
     */
    public static function getCategory($id)
    {
        $table = Database::get_course_table(TABLE_LINK_CATEGORY);
        $id = intval($id);
        $courseId = api_get_course_int_id();
        $sql = "SELECT * FROM $table WHERE id = $id AND c_id = $courseId";
        $result = Database::query($sql);
        $category = Database::fetch_array($result, 'ASSOC');

        return $category;
    }

    /**
     * Move a link inside its category (display_order field)
     * @param int $id The link ID
     * @param string $direction The direction to sort the links
     * @return bool
     */
    private static function moveLinkDisplayOrder($id, $direction)
    {
        $em = Database::getManager();
        /** @var CLink $link */
        $link = $em->find('ChamiloCourseBundle:CLink', $id);

        if (!$link) {
            return false;
        }

        $compareLinks = $em
            ->getRepository('ChamiloCourseBundle:CLink')
            ->findBy(
                ['cId' => $link->getCId(), 'categoryId' => $link->getCategoryId()],
                ['displayOrder' => $direction]
            );
        /** @var CLink $prevLink */
        $prevLink = null;

        /** @var CLink $compareLink */
        foreach ($compareLinks as $compareLink) {
            if ($compareLink->getId() !== $link->getId()) {
                $prevLink = $compareLink;

                continue;
            }

            if (!$prevLink) {
                return false;
            }

            $newPrevLinkDisplayOrder = $link->getDisplayOrder();
            $newLinkDisplayOrder = $prevLink->getDisplayOrder();

            $link->setDisplayOrder($newLinkDisplayOrder);
            $prevLink->setDisplayOrder($newPrevLinkDisplayOrder);

            $em->merge($prevLink);
            $em->merge($link);
            break;
        }

        $em->flush();
        return true;
    }

    /**
     * Move a link up in its category
     * @param int $id
     * @return bool
     */
    public static function moveLinkUp($id)
    {
        return self::moveLinkDisplayOrder($id, 'ASC');
    }

    /**
     * Move a link down in its category
     * @param int $id
     * @return bool
     */
    public static function moveLinkDown($id)
    {
        return self::moveLinkDisplayOrder($id, 'DESC');
    }
}
