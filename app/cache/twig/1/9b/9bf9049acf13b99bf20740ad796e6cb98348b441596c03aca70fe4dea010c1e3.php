<?php

/* default/layout/page_header.tpl */
class __TwigTemplate_baead6811057f345324abab88788d8a60f229cbddef3c0f351beeb527bed4438 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'topbar' => array($this, 'block_topbar'),
            'menu' => array($this, 'block_menu'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div id=\"navigation\" class=\"notification-panel\">
    ";
        // line 2
        echo (isset($context["help_content"]) ? $context["help_content"] : null);
        echo "
    ";
        // line 3
        echo (isset($context["bug_notification"]) ? $context["bug_notification"] : null);
        echo "
</div>
";
        // line 5
        $this->displayBlock('topbar', $context, $blocks);
        // line 8
        echo "<div class=\"extra-header\">";
        echo (isset($context["header_extra_content"]) ? $context["header_extra_content"] : null);
        echo "</div>
<header id=\"header-section\">
<section>
    <div class=\"container\">
\t<div class=\"row\">
\t    <div class=\"col-md-3\">
\t    \t<div class=\"logo\">
                ";
        // line 15
        echo (isset($context["logo"]) ? $context["logo"] : null);
        echo "
            </div>
\t    </div>
            <div class=\"col-md-9\">
                <div class=\"col-sm-4\">
                    ";
        // line 20
        if ( !(null === (isset($context["plugin_header_left"]) ? $context["plugin_header_left"] : null))) {
            // line 21
            echo "                    <div id=\"plugin_header_left\">
                        ";
            // line 22
            echo (isset($context["plugin_header_left"]) ? $context["plugin_header_left"] : null);
            echo "
                    </div>
                    ";
        }
        // line 25
        echo "                </div>
                <div class=\"col-sm-4\">
                    ";
        // line 27
        if ( !(null === (isset($context["plugin_header_center"]) ? $context["plugin_header_center"] : null))) {
            // line 28
            echo "                    <div id=\"plugin_header_center\">
                        ";
            // line 29
            echo (isset($context["plugin_header_center"]) ? $context["plugin_header_center"] : null);
            echo "
                    </div>
                    ";
        }
        // line 32
        echo "                </div>
                <div class=\"col-sm-4\">
                    ";
        // line 34
        if ( !(null === (isset($context["plugin_header_right"]) ? $context["plugin_header_right"] : null))) {
            // line 35
            echo "                    <div id=\"plugin_header_right\">
                        ";
            // line 36
            echo (isset($context["plugin_header_right"]) ? $context["plugin_header_right"] : null);
            echo "
                    </div>
                    ";
        }
        // line 39
        echo "                    <div class=\"section-notifications\">
                        <ul id=\"notifications\" class=\"nav nav-pills pull-right\">
                            ";
        // line 41
        echo (isset($context["notification_menu"]) ? $context["notification_menu"] : null);
        echo "
                        </ul>
                    </div>
                    ";
        // line 44
        echo (isset($context["accessibility"]) ? $context["accessibility"] : null);
        echo "
                </div>
            </div>
        </div>
    </div>
</section>
";
        // line 50
        $this->displayBlock('menu', $context, $blocks);
        // line 53
        echo "</header>
";
        // line 54
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/course_navigation.tpl"), "default/layout/page_header.tpl", 54)->display($context);
    }

    // line 5
    public function block_topbar($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/topbar.tpl"), "default/layout/page_header.tpl", 6)->display($context);
    }

    // line 50
    public function block_menu($context, array $blocks = array())
    {
        // line 51
        echo "    ";
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/menu.tpl"), "default/layout/page_header.tpl", 51)->display($context);
    }

    public function getTemplateName()
    {
        return "default/layout/page_header.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 51,  131 => 50,  126 => 6,  123 => 5,  119 => 54,  116 => 53,  114 => 50,  105 => 44,  99 => 41,  95 => 39,  89 => 36,  86 => 35,  84 => 34,  80 => 32,  74 => 29,  71 => 28,  69 => 27,  65 => 25,  59 => 22,  56 => 21,  54 => 20,  46 => 15,  35 => 8,  33 => 5,  28 => 3,  24 => 2,  21 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
