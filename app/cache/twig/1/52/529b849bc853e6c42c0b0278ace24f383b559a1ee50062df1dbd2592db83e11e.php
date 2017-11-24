<?php

/* default//user_portal/classic_courses_with_category.tpl */
class __TwigTemplate_8ecd12fbb81f3e423c244a46355b3f8ae370b654522d57d1af859353f844e669 extends Twig_Template
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
        if ( !twig_test_empty((isset($context["categories"]) ? $context["categories"] : null))) {
            // line 2
            echo "    <div class=\"classic-courses\">
        ";
            // line 3
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["categories"]) ? $context["categories"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 4
                echo "            <div class=\"panel panel-default\">
                <div id=\"category-";
                // line 5
                echo $this->getAttribute($context["category"], "id_category", array());
                echo "\" class=\"panel-heading\">
                    ";
                // line 6
                echo $this->getAttribute($context["category"], "title_category", array());
                echo "
                </div>
                <div class=\"panel-body\">
                    ";
                // line 9
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["category"], "courses", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 10
                    echo "                        <div class=\"row\">
                            <div class=\"col-md-2\">
                                <a class=\"thumbnail\">
                                    ";
                    // line 13
                    if (($this->getAttribute($context["item"], "thumbnails", array()) != "")) {
                        // line 14
                        echo "                                        <img src=\"";
                        echo $this->getAttribute($context["item"], "thumbnails", array());
                        echo "\" title=\"";
                        echo $this->getAttribute($context["item"], "title", array());
                        echo "\"
                                             alt=\"";
                        // line 15
                        echo $this->getAttribute($context["item"], "title", array());
                        echo "\"/>
                                    ";
                    } else {
                        // line 17
                        echo "                                        ";
                        echo Template::get_image("blackboard.png", 48, $this->getAttribute($context["item"], "title", array()));
                        echo "
                                    ";
                    }
                    // line 19
                    echo "                                </a>
                            </div>
                            <div class=\"col-md-10\">
                                ";
                    // line 22
                    if (($this->getAttribute($context["item"], "edit_actions", array()) != "")) {
                        // line 23
                        echo "                                    <div class=\"pull-right\">
                                        ";
                        // line 24
                        if (($this->getAttribute($context["item"], "document", array()) == "")) {
                            // line 25
                            echo "                                            <a class=\"btn btn-default btn-sm\" href=\"";
                            echo $this->getAttribute($context["item"], "edit_actions", array());
                            echo "\">
                                                <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                            </a>
                                        ";
                        } else {
                            // line 29
                            echo "                                            <div class=\"btn-group\" role=\"group\">
                                                <a class=\"btn btn-default btn-sm\" href=\"";
                            // line 30
                            echo $this->getAttribute($context["item"], "edit_actions", array());
                            echo "\">
                                                    <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                                </a>
                                                ";
                            // line 33
                            echo $this->getAttribute($context["item"], "document", array());
                            echo "
                                            </div>
                                        ";
                        }
                        // line 36
                        echo "                                    </div>
                                ";
                    }
                    // line 38
                    echo "                                <h4 class=\"course-items-title\">
                                    ";
                    // line 39
                    if (($this->getAttribute($context["item"], "visibility", array()) == twig_constant("COURSE_VISIBILITY_CLOSED"))) {
                        // line 40
                        echo "                                        ";
                        echo $this->getAttribute($context["item"], "title", array());
                        echo " ";
                        echo $this->getAttribute($context["item"], "code_course", array());
                        echo "
                                    ";
                    } else {
                        // line 42
                        echo "                                        <a href=\"";
                        echo $this->getAttribute($context["item"], "link", array());
                        echo "\">
                                            ";
                        // line 43
                        echo $this->getAttribute($context["item"], "title", array());
                        echo " ";
                        echo $this->getAttribute($context["item"], "code_course", array());
                        echo "
                                        </a>
                                        ";
                        // line 45
                        echo $this->getAttribute($context["item"], "notifications", array());
                        echo "
                                    ";
                    }
                    // line 47
                    echo "                                </h4>
                                <div class=\"course-items-session\">
                                    <div class=\"list-teachers\">
                                        ";
                    // line 50
                    if ((twig_length_filter($this->env, $this->getAttribute($context["item"], "teachers", array())) > 0)) {
                        // line 51
                        echo "                                            <img src=\"";
                        echo Template::get_icon_path("teacher.png", 16);
                        echo "\" width=\"16\" height=\"16\">&nbsp;
                                            ";
                        // line 52
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["item"], "teachers", array()));
                        foreach ($context['_seq'] as $context["_key"] => $context["teacher"]) {
                            // line 53
                            echo "                                                ";
                            $context["counter"] = ((isset($context["counter"]) ? $context["counter"] : null) + 1);
                            // line 54
                            echo "                                                ";
                            if (((isset($context["counter"]) ? $context["counter"] : null) > 1)) {
                                echo " | ";
                            }
                            // line 55
                            echo "                                                <a href=\"";
                            echo $this->getAttribute($context["teacher"], "url", array());
                            echo "\" class=\"ajax\"
                                                   data-title=\"";
                            // line 56
                            echo $this->getAttribute($context["teacher"], "firstname", array());
                            echo " ";
                            echo $this->getAttribute($context["teacher"], "lastname", array());
                            echo "\">
                                                    ";
                            // line 57
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
                        // line 60
                        echo "                                        ";
                    }
                    // line 61
                    echo "                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 66
                echo "                </div>
            </div>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 69
            echo "    </div>
";
        }
        // line 71
        echo "
";
    }

    public function getTemplateName()
    {
        return "default//user_portal/classic_courses_with_category.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  210 => 71,  206 => 69,  198 => 66,  188 => 61,  185 => 60,  174 => 57,  168 => 56,  163 => 55,  158 => 54,  155 => 53,  151 => 52,  146 => 51,  144 => 50,  139 => 47,  134 => 45,  127 => 43,  122 => 42,  114 => 40,  112 => 39,  109 => 38,  105 => 36,  99 => 33,  93 => 30,  90 => 29,  82 => 25,  80 => 24,  77 => 23,  75 => 22,  70 => 19,  64 => 17,  59 => 15,  52 => 14,  50 => 13,  45 => 10,  41 => 9,  35 => 6,  31 => 5,  28 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
