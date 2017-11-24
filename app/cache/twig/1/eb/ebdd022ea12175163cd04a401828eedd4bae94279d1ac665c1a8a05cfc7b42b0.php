<?php

/* default/layout/welcome_to_course.tpl */
class __TwigTemplate_bb6c5244c76b604de7960a8693e0f7aba14b360bb4fc0d6e7caddc892db462a6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"normal-message\">
  ";
        // line 2
        echo sprintf(get_lang("HelloXAsYouCanSeeYourCourseListIsEmpty"), $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "complete_name", array()));
        echo "

";
        // line 4
        if (((isset($context["count_courses"]) ? $context["count_courses"] : null) == 0)) {
            // line 5
            echo "    ";
            echo get_lang("PleaseAllowUsALittleTimeToSubscribeYouToOneOfOurCourses");
            echo "
";
        } else {
            // line 7
            echo "
    ";
            // line 8
            if ((api_get_setting("allow_students_to_browse_courses") == "true")) {
                // line 9
                echo "        ";
                echo sprintf(get_lang("GoAheadAndBrowseOurCourseCatalogXOnceRegisteredYouWillSeeTheCourseHereX"), (isset($context["course_catalog_link"]) ? $context["course_catalog_link"] : null), (isset($context["course_list_link"]) ? $context["course_list_link"] : null));
                echo "
        <a class=\"btn btn-primary btn-large\" href=\"";
                // line 10
                echo (isset($context["course_catalog_url"]) ? $context["course_catalog_url"] : null);
                echo "\">
            ";
                // line 11
                echo get_lang("CourseCatalog");
                echo "
        </a>
    ";
            } else {
                // line 14
                echo "        ";
                echo get_lang("PleaseAllowUsALittleTimeToSubscribeYouToOneOfOurCourses");
                echo "
    ";
            }
        }
        // line 17
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "default/layout/welcome_to_course.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 17,  55 => 14,  49 => 11,  45 => 10,  40 => 9,  38 => 8,  35 => 7,  29 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
