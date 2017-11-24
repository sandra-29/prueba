<?php

/* default//user_portal/classic_courses_without_category.tpl */
class __TwigTemplate_424a5e8f417cbf6d6470f21ed40db8622e71fd30dc8536a7e81f4f0519f2b0c1 extends Twig_Template
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
        if ( !twig_test_empty((isset($context["courses"]) ? $context["courses"] : null))) {
            // line 2
            echo "    <div class=\"classic-courses\">
    ";
            // line 3
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["courses"]) ? $context["courses"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 4
                echo "        <div class=\"panel panel-default\">
            <div class=\"panel-body\">
                <div class=\"row\">
                    <div class=\"col-md-2\">
                        <a class=\"thumbnail\">
                            ";
                // line 9
                if (($this->getAttribute($context["item"], "thumbnails", array()) != "")) {
                    // line 10
                    echo "                                <img src=\"";
                    echo $this->getAttribute($context["item"], "thumbnails", array());
                    echo "\" title=\"";
                    echo $this->getAttribute($context["item"], "title", array());
                    echo "\" alt=\"";
                    echo $this->getAttribute($context["item"], "title", array());
                    echo "\"/>
                            ";
                } else {
                    // line 12
                    echo "                                ";
                    echo Template::get_image("blackboard.png", 48, $this->getAttribute($context["item"], "title", array()));
                    echo "
                            ";
                }
                // line 14
                echo "                        </a>
                    </div>
                    <div class=\"col-md-10\">
                        ";
                // line 17
                if (($this->getAttribute($context["item"], "edit_actions", array()) != "")) {
                    // line 18
                    echo "                            <div class=\"pull-right\">
                                ";
                    // line 19
                    if (($this->getAttribute($context["item"], "document", array()) == "")) {
                        // line 20
                        echo "                                    <a class=\"btn btn-default btn-sm\" href=\"";
                        echo $this->getAttribute($context["item"], "edit_actions", array());
                        echo "\">
                                        <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                    </a>
                                ";
                    } else {
                        // line 24
                        echo "                                    <div class=\"btn-group\" role=\"group\">
                                        <a class=\"btn btn-default btn-sm\" href=\"";
                        // line 25
                        echo $this->getAttribute($context["item"], "edit_actions", array());
                        echo "\">
                                            <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                        </a>
                                        ";
                        // line 28
                        echo $this->getAttribute($context["item"], "document", array());
                        echo "
                                    </div>
                                ";
                    }
                    // line 31
                    echo "                            </div>
                        ";
                }
                // line 33
                echo "                        <h4 class=\"course-items-title\">
                            ";
                // line 34
                if (($this->getAttribute($context["item"], "visibility", array()) == twig_constant("COURSE_VISIBILITY_CLOSED"))) {
                    // line 35
                    echo "                                ";
                    echo $this->getAttribute($context["item"], "title", array());
                    echo " ";
                    echo $this->getAttribute($context["item"], "code_course", array());
                    echo "
                            ";
                } else {
                    // line 37
                    echo "                                <a href=\"";
                    echo $this->getAttribute($context["item"], "link", array());
                    echo "\">
                                    ";
                    // line 38
                    echo $this->getAttribute($context["item"], "title", array());
                    echo " ";
                    echo $this->getAttribute($context["item"], "code_course", array());
                    echo "
                                </a>
                                ";
                    // line 40
                    echo $this->getAttribute($context["item"], "notifications", array());
                    echo "
                                ";
                    // line 41
                    if ($this->getAttribute($context["item"], "is_special_course", array())) {
                        // line 42
                        echo "                                    ";
                        echo Template::get_image("klipper.png", 22, get_lang("CourseAutoRegister"));
                        echo "
                                ";
                    }
                    // line 44
                    echo "                            ";
                }
                // line 45
                echo "                        </h4>
                        <div class=\"course-items-session\">
                            <div class=\"list-teachers\">
                                ";
                // line 48
                echo Template::get_image("teacher.png", 16, get_lang("Professor"));
                echo "
                                ";
                // line 49
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["item"], "teachers", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["teacher"]) {
                    // line 50
                    echo "                                    ";
                    $context["counter"] = ((isset($context["counter"]) ? $context["counter"] : null) + 1);
                    // line 51
                    echo "                                    ";
                    if (((isset($context["counter"]) ? $context["counter"] : null) > 1)) {
                        echo " | ";
                    }
                    // line 52
                    echo "                                    <a href=\"";
                    echo $this->getAttribute($context["teacher"], "url", array());
                    echo "\" class=\"ajax\"
                                       data-title=\"";
                    // line 53
                    echo $this->getAttribute($context["teacher"], "firstname", array());
                    echo " ";
                    echo $this->getAttribute($context["teacher"], "lastname", array());
                    echo "\">
                                        ";
                    // line 54
                    echo $this->getAttribute($context["teacher"], "firstname", array());
                    echo " ";
                    echo $this->getAttribute($context["teacher"], "lastname", array());
                    echo "
                                    </a>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['teacher'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 57
                echo "                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 64
            echo "    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "default//user_portal/classic_courses_without_category.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  187 => 64,  175 => 57,  164 => 54,  158 => 53,  153 => 52,  148 => 51,  145 => 50,  141 => 49,  137 => 48,  132 => 45,  129 => 44,  123 => 42,  121 => 41,  117 => 40,  110 => 38,  105 => 37,  97 => 35,  95 => 34,  92 => 33,  88 => 31,  82 => 28,  76 => 25,  73 => 24,  65 => 20,  63 => 19,  60 => 18,  58 => 17,  53 => 14,  47 => 12,  37 => 10,  35 => 9,  28 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
