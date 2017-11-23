<?php
/* For licensing terms, see /license.txt */

/**
 * Class CourseBlockPlugin
 */
class CourseBlockPlugin extends Plugin
{
    public $isCoursePlugin = true;

    // When creating a new course this settings are added to the course
    public $course_settings = array(
         array(
            'name' => 'course_block_pre_footer',
            'type' => 'textarea'
        ),
        array(
            'name' => 'course_block_footer_left',
            'type' => 'textarea'
        ),
        array(
            'name' => 'course_block_footer_center',
            'type' => 'textarea'
        ),
        array(
            'name' => 'course_block_footer_right',
            'type' => 'textarea'
        )
    );

    /**
     * @return CourseBlockPlugin
     */
    public static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    /**
     *
     */
    protected function __construct()
    {
        parent::__construct(
            '0.1',
            'Julio Montoya',
            array(
                'tool_enable' => 'boolean'
            )
        );
    }

    ///public function

    public function install()
    {
        // Installing course settings
        $this->install_course_fields_in_all_courses(false);
    }

    public function uninstall()
    {
        // Deleting course settings
        $this->uninstall_course_fields_in_all_courses();
    }

        /**
     * @param string $region
     * @return string
     */
    public function renderRegion($region)
    {
        $content = '';
        switch ($region) {
            case 'footer_left':
                $content = api_get_course_setting('course_block_footer_left');
                $content = $content === -1 ? '' : $content;
                break;
            case 'footer_center':
                $content = api_get_course_setting('course_block_footer_center');
                $content = $content === -1 ? '' : $content;
                break;
            case 'footer_right':
                $content = api_get_course_setting('course_block_footer_right');
                $content = $content === -1 ? '' : $content;
                break;
            case 'pre_footer':
                $content = api_get_course_setting('course_block_pre_footer');
                $content = $content === -1 ? '' : $content;
                break;
        }
        return $content;
    }
}
