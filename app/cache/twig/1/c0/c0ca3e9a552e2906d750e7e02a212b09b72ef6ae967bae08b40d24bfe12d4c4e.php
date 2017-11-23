<?php

/* default/layout/layout_2_col.tpl */
class __TwigTemplate_aea05e3e2264b1d12428f1537937d3bbb32581496ab18e3c9489a2a85d611ace extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'page_body' => array($this, 'block_page_body'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page.tpl"), "default/layout/layout_2_col.tpl", 1);
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        if ((isset($context["plugin_main_top"]) ? $context["plugin_main_top"] : null)) {
            // line 5
            echo "    <div id=\"plugin_main_top\" class=\"col-md-12\">
        ";
            // line 6
            echo (isset($context["plugin_main_top"]) ? $context["plugin_main_top"] : null);
            echo "
    </div>
    ";
        }
        // line 9
        echo "\t<div class=\"col-md-3 menu-column\">
    ";
        // line 10
        if ((isset($context["plugin_menu_top"]) ? $context["plugin_menu_top"] : null)) {
            // line 11
            echo "        <div id=\"plugin_menu_top\">
            ";
            // line 12
            echo (isset($context["plugin_menu_top"]) ? $context["plugin_menu_top"] : null);
            echo "
        </div>
    ";
        }
        // line 15
        echo "\t
    ";
        // line 16
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/login_form.tpl"), "default/layout/layout_2_col.tpl", 16)->display($context);
        // line 17
        echo "        
    ";
        // line 18
        if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "logged", array()) == 1)) {
            // line 19
            echo "        ";
            echo (isset($context["user_image_block"]) ? $context["user_image_block"] : null);
            echo "
    ";
        }
        // line 21
        echo "        
    ";
        // line 22
        echo (isset($context["profile_block"]) ? $context["profile_block"] : null);
        echo "
\t";
        // line 23
        echo (isset($context["course_block"]) ? $context["course_block"] : null);
        echo "
\t";
        // line 24
        echo (isset($context["teacher_block"]) ? $context["teacher_block"] : null);
        echo "
    ";
        // line 25
        echo (isset($context["skills_block"]) ? $context["skills_block"] : null);
        echo "
    ";
        // line 26
        echo (isset($context["certificates_search_block"]) ? $context["certificates_search_block"] : null);
        echo "
\t";
        // line 27
        echo (isset($context["notice_block"]) ? $context["notice_block"] : null);
        echo "
\t";
        // line 28
        echo (isset($context["help_block"]) ? $context["help_block"] : null);
        echo "
\t";
        // line 29
        echo (isset($context["navigation_course_links"]) ? $context["navigation_course_links"] : null);
        echo "
\t";
        // line 30
        echo (isset($context["search_block"]) ? $context["search_block"] : null);
        echo "
\t";
        // line 31
        echo (isset($context["classes_block"]) ? $context["classes_block"] : null);
        echo "
        
    ";
        // line 33
        if ((isset($context["plugin_menu_bottom"]) ? $context["plugin_menu_bottom"] : null)) {
            // line 34
            echo "        <div id=\"plugin_menu_bottom\">
            ";
            // line 35
            echo (isset($context["plugin_menu_bottom"]) ? $context["plugin_menu_bottom"] : null);
            echo "
        </div>
    ";
        }
        // line 38
        echo "\t</div>
\t<div class=\"col-md-9\">
        ";
        // line 40
        if ((isset($context["plugin_content_top"]) ? $context["plugin_content_top"] : null)) {
            // line 41
            echo "            <div id=\"plugin_content_top\">
                ";
            // line 42
            echo (isset($context["plugin_content_top"]) ? $context["plugin_content_top"] : null);
            echo "
            </div>
        ";
        }
        // line 45
        echo "        ";
        if ((isset($context["home_page_block"]) ? $context["home_page_block"] : null)) {
            // line 46
            echo "            <section id=\"homepage-home\">
                ";
            // line 47
            echo (isset($context["home_page_block"]) ? $context["home_page_block"] : null);
            echo "
            </section>
        ";
        }
        // line 50
        echo "        
        ";
        // line 51
        echo (isset($context["sniff_notification"]) ? $context["sniff_notification"] : null);
        echo "
        
        ";
        // line 53
        $this->displayBlock('page_body', $context, $blocks);
        // line 56
        echo "        
        ";
        // line 57
        if ((isset($context["welcome_to_course_block"]) ? $context["welcome_to_course_block"] : null)) {
            // line 58
            echo "            <section id=\"homepage-course\">
            ";
            // line 59
            echo (isset($context["welcome_to_course_block"]) ? $context["welcome_to_course_block"] : null);
            echo "
            </section>
        ";
        }
        // line 62
        echo "
        ";
        // line 63
        $this->displayBlock('content', $context, $blocks);
        // line 70
        echo "
        ";
        // line 71
        if ((isset($context["announcements_block"]) ? $context["announcements_block"] : null)) {
            // line 72
            echo "            <section id=\"homepage-announcements\">
            ";
            // line 73
            echo (isset($context["announcements_block"]) ? $context["announcements_block"] : null);
            echo "
            </section>
        ";
        }
        // line 76
        echo "
        ";
        // line 77
        if ((isset($context["course_category_block"]) ? $context["course_category_block"] : null)) {
            // line 78
            echo "            <section id=\"homepage-course-category\">
                ";
            // line 79
            echo (isset($context["course_category_block"]) ? $context["course_category_block"] : null);
            echo "
            </section>
        ";
        }
        // line 82
        echo "
\t";
        // line 83
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/hot_courses.tpl"), "default/layout/layout_2_col.tpl", 83)->display($context);
        // line 84
        echo "
    ";
        // line 85
        if ((isset($context["plugin_content_bottom"]) ? $context["plugin_content_bottom"] : null)) {
            // line 86
            echo "        <div id=\"plugin_content_bottom\">
            ";
            // line 87
            echo (isset($context["plugin_content_bottom"]) ? $context["plugin_content_bottom"] : null);
            echo "
        </div>
    ";
        }
        // line 90
        echo "</div>
";
        // line 91
        if ((isset($context["plugin_main_bottom"]) ? $context["plugin_main_bottom"] : null)) {
            // line 92
            echo "    <div id=\"plugin_main_bottom\" class=\"col-md-12\">
        ";
            // line 93
            echo (isset($context["plugin_main_bottom"]) ? $context["plugin_main_bottom"] : null);
            echo "
    </div>
";
        }
    }

    // line 53
    public function block_page_body($context, array $blocks = array())
    {
        // line 54
        echo "            ";
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_body.tpl"), "default/layout/layout_2_col.tpl", 54)->display($context);
        // line 55
        echo "        ";
    }

    // line 63
    public function block_content($context, array $blocks = array())
    {
        // line 64
        echo "        ";
        if ( !(null === (isset($context["content"]) ? $context["content"] : null))) {
            // line 65
            echo "            <section id=\"page-content\" class=\"";
            echo (isset($context["course_history_page"]) ? $context["course_history_page"] : null);
            echo "\">
                ";
            // line 66
            echo (isset($context["content"]) ? $context["content"] : null);
            echo "
            </section>
        ";
        }
        // line 69
        echo "        ";
    }

    public function getTemplateName()
    {
        return "default/layout/layout_2_col.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  274 => 69,  268 => 66,  263 => 65,  260 => 64,  257 => 63,  253 => 55,  250 => 54,  247 => 53,  239 => 93,  236 => 92,  234 => 91,  231 => 90,  225 => 87,  222 => 86,  220 => 85,  217 => 84,  215 => 83,  212 => 82,  206 => 79,  203 => 78,  201 => 77,  198 => 76,  192 => 73,  189 => 72,  187 => 71,  184 => 70,  182 => 63,  179 => 62,  173 => 59,  170 => 58,  168 => 57,  165 => 56,  163 => 53,  158 => 51,  155 => 50,  149 => 47,  146 => 46,  143 => 45,  137 => 42,  134 => 41,  132 => 40,  128 => 38,  122 => 35,  119 => 34,  117 => 33,  112 => 31,  108 => 30,  104 => 29,  100 => 28,  96 => 27,  92 => 26,  88 => 25,  84 => 24,  80 => 23,  76 => 22,  73 => 21,  67 => 19,  65 => 18,  62 => 17,  60 => 16,  57 => 15,  51 => 12,  48 => 11,  46 => 10,  43 => 9,  37 => 6,  34 => 5,  32 => 4,  29 => 3,  20 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
