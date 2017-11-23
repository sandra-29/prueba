<?php

/* default/layout/course_navigation.tpl */
class __TwigTemplate_8a58a2c8f69fc38004c64b9887ecbba4b98c60bb927460c3604cd1e0b5e254ca extends Twig_Template
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
        // line 2
        if (((isset($context["show_header"]) ? $context["show_header"] : null) == true)) {
            // line 3
            echo "    ";
            if ( !(null === (isset($context["show_course_navigation_menu"]) ? $context["show_course_navigation_menu"] : null))) {
                // line 4
                echo "        <div class=\"nav-tools\">
            ";
                // line 5
                echo (isset($context["show_course_navigation_menu"]) ? $context["show_course_navigation_menu"] : null);
                echo "
        </div>
    ";
            }
        }
    }

    public function getTemplateName()
    {
        return "default/layout/course_navigation.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  27 => 5,  24 => 4,  21 => 3,  19 => 2,);
    }

    public function getSource()
    {
        return "";
    }
}
