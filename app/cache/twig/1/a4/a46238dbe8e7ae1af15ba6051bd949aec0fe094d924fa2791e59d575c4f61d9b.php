<?php

/* default/layout/topbar.tpl */
class __TwigTemplate_e11d2bead32490dd5d14543715a9e125d31e6e6443c2aa60cca88dc75513c2bb extends Twig_Template
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
        echo "<!-- Topbar -->
";
        // line 2
        if (((isset($context["show_toolbar"]) ? $context["show_toolbar"] : null) == 1)) {
            // line 3
            echo "    <nav id=\"toolbar-admin\" class=\"navbar navbar-inverse\">
        <div class=\"container-fluid\">
            <div class=\"navbar-header\">
                <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#toolbar\">
                    <span class=\"sr-only\">Toggle navigation</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </button>
                <a class=\"navbar-brand\" href=\"";
            // line 12
            echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
            echo "\">
                    <img src=\"";
            // line 13
            echo Template::get_icon_path("icon-chamilo.png", 22);
            echo "\" title=\"";
            echo api_get_setting("siteName");
            echo "\">
                </a>
            </div>
            ";
            // line 16
            if ($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "logged", array())) {
                // line 17
                echo "                <div class=\"collapse navbar-collapse\" id=\"toolbar\">
                    <ul class=\"nav navbar-nav\">
                        <li class=\"active\"><a href=\"";
                // line 19
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
                echo "user_portal.php\"> ";
                echo get_lang("MyCourses");
                echo "</a></li>
                        <li class=\"dropdown\">
                            <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
                // line 21
                echo get_lang("Tracking");
                echo "<b class=\"caret\"></b></a>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"";
                // line 23
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                echo "mySpace/\">";
                echo get_lang("CoursesReporting");
                echo "</a></li>
                                <li><a href=\"";
                // line 24
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                echo "mySpace/index.php?view=admin\">";
                echo get_lang("AdminReports");
                echo "</a></li>
                                <li><a href=\"";
                // line 25
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                echo "tracking/exams.php\">";
                echo get_lang("ExamsReporting");
                echo "</a></li>
                                <li class=\"divider\"></li>
                                <li><a href=\"";
                // line 27
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                echo "dashboard/\">";
                echo get_lang("Dashboard");
                echo "</a></li>
                            </ul>
                        </li>
                        ";
                // line 30
                if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array()) == 1)) {
                    // line 31
                    echo "                        <li class=\"dropdown\">
                            <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
                    // line 32
                    echo get_lang("Administration");
                    echo "<b class=\"caret\"></b></a>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"";
                    // line 34
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/\">";
                    echo get_lang("Home");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 35
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/user_list.php\">";
                    echo get_lang("UserList");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 36
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/course_list.php\">";
                    echo get_lang("CourseList");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 37
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "session/session_list.php\">";
                    echo get_lang("SessionList");
                    echo "</a></li>
                                <li class=\"divider\"></li>
                                <li><a href=\"";
                    // line 39
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/settings.php\">";
                    echo get_lang("Settings");
                    echo "</a></li>
                                <li class=\"divider\"></li>
                                <li><a href=\"";
                    // line 41
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/settings.php?category=Plugins\">";
                    echo get_lang("Plugins");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 42
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/settings.php?category=Regions\">";
                    echo get_lang("Regions");
                    echo "</a></li>
                            </ul>
                        </li>

                        <li class=\"dropdown\">
                            <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
                    // line 47
                    echo get_lang("Add");
                    echo "<b class=\"caret\"></b></a>
                            <ul class=\"dropdown-menu\">
                                <li><a href=\"";
                    // line 49
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/user_add.php\">";
                    echo get_lang("User");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 50
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/course_add.php\">";
                    echo get_lang("Course");
                    echo "</a></li>
                                <li><a href=\"";
                    // line 51
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "session/session_add.php\">";
                    echo get_lang("Session");
                    echo "</a></li>
                            </ul>
                        </li>
                        ";
                }
                // line 55
                echo "                    </ul>

                    ";
                // line 57
                if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array()) == 1)) {
                    // line 58
                    echo "                    <form class=\"navbar-form navbar-left\" role=\"search\" action=\"";
                    echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                    echo "admin/user_list.php\" method=\"get\">
                        <input type=\"text\" class=\"form-control\" placeholder=\"";
                    // line 59
                    echo get_lang("SearchUsers");
                    echo "\" name=\"keyword\">
                    </form>
                    ";
                }
                // line 62
                echo "
                    <ul class=\"nav navbar-nav navbar-right\">
                        <li><a href=\"";
                // line 64
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
                echo "index.php?logout=logout&uid=";
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "user_id", array());
                echo "\">";
                echo get_lang("Logout");
                echo "</a></li>
                    </ul>
                </div> <!-- /nav collapse -->
            ";
            }
            // line 68
            echo "        </div> <!-- /container-->
    </nav><!-- /topbar -->
";
        }
    }

    public function getTemplateName()
    {
        return "default/layout/topbar.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  208 => 68,  197 => 64,  193 => 62,  187 => 59,  182 => 58,  180 => 57,  176 => 55,  167 => 51,  161 => 50,  155 => 49,  150 => 47,  140 => 42,  134 => 41,  127 => 39,  120 => 37,  114 => 36,  108 => 35,  102 => 34,  97 => 32,  94 => 31,  92 => 30,  84 => 27,  77 => 25,  71 => 24,  65 => 23,  60 => 21,  53 => 19,  49 => 17,  47 => 16,  39 => 13,  35 => 12,  24 => 3,  22 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
