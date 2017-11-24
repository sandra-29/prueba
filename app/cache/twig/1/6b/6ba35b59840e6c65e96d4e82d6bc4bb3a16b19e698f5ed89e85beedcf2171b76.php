<?php

/* default/user_portal/classic_session.tpl */
class __TwigTemplate_42d3bc129681b764d589b14ae70fd44953ff9d9c45d530eaa4056a2a31b91dcd extends Twig_Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["session"]) ? $context["session"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 2
            echo "    <div class=\"panel panel-default\">
        ";
            // line 3
            if ( !$this->getAttribute($context["row"], "show_simple_session_info", array())) {
                // line 4
                echo "            ";
                $context["collapsable"] = "";
                // line 5
                echo "            <div class=\"panel-heading\">
                ";
                // line 6
                if (($this->getAttribute($context["row"], "course_list_session_style", array()) == 1)) {
                    // line 7
                    echo "                    ";
                    // line 8
                    echo "                    <a href=\"";
                    echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "session/index.php?session_id=") . $this->getAttribute($context["row"], "id", array()));
                    echo "\">
                        <img id=\"session_img_";
                    // line 9
                    echo $this->getAttribute($context["row"], "id", array());
                    echo "\" src=\"";
                    echo Template::get_icon_path("window_list.png", 32);
                    echo "\" width=\"32\" height=\"32\"
                             alt=\"";
                    // line 10
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\" title=\"";
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\"/>
                        ";
                    // line 11
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "
                    </a>
                ";
                } elseif (($this->getAttribute(                // line 13
$context["row"], "course_list_session_style", array()) == 2)) {
                    // line 14
                    echo "                    ";
                    // line 15
                    echo "                    <img id=\"session_img_";
                    echo $this->getAttribute($context["row"], "id", array());
                    echo "\" src=\"";
                    echo Template::get_icon_path("window_list.png", 32);
                    echo "\" width=\"32\" height=\"32\"
                         alt=\"";
                    // line 16
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\" title=\"";
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\"/>
                    ";
                    // line 17
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "
                ";
                } elseif (($this->getAttribute(                // line 18
$context["row"], "course_list_session_style", array()) == 3)) {
                    // line 19
                    echo "                    ";
                    // line 20
                    echo "                    <a role=\"button\" data-toggle=\"collapse\" data-parent=\"#page-content\" href=\"#collapse_";
                    echo $this->getAttribute($context["row"], "id", array());
                    echo "\"
                       aria-expanded=\"false\">
                        <img id=\"session_img_";
                    // line 22
                    echo $this->getAttribute($context["row"], "id", array());
                    echo "\" src=\"";
                    echo Template::get_icon_path("window_list.png", 32);
                    echo "\" width=\"32\" height=\"32\"
                             alt=\"";
                    // line 23
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\" title=\"";
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "\"/>
                        ";
                    // line 24
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "
                    </a>
                    ";
                    // line 26
                    $context["collapsable"] = "collapse";
                    // line 27
                    echo "                ";
                }
                // line 28
                echo "                ";
                if ($this->getAttribute($context["row"], "show_actions", array())) {
                    // line 29
                    echo "                    <div class=\"pull-right\">
                        <a href=\"";
                    // line 30
                    echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "session/resume_session.php?id_session=") . $this->getAttribute($context["row"], "id", array()));
                    echo "\">
                            <img src=\"";
                    // line 31
                    echo Template::get_icon_path("edit.png", 22);
                    echo "\" width=\"22\" height=\"22\" alt=\"";
                    echo get_lang("Edit");
                    echo "\"
                                 title=\"";
                    // line 32
                    echo get_lang("Edit");
                    echo "\"/>
                        </a>
                    </div>
                ";
                }
                // line 36
                echo "            </div>
            <div class=\"session panel-body ";
                // line 37
                echo (isset($context["collapsable"]) ? $context["collapsable"] : null);
                echo "\" id=\"collapse_";
                echo $this->getAttribute($context["row"], "id", array());
                echo "\">
                <div class=\"row\">
                    <div class=\"col-md-12\">
                        ";
                // line 40
                if (($this->getAttribute($context["row"], "description", array()) != "")) {
                    // line 41
                    echo "                            ";
                    echo $this->getAttribute($context["row"], "description", array());
                    echo "
                        ";
                }
                // line 43
                echo "                        <div class=\"info-session\">
                            ";
                // line 44
                if (($this->getAttribute($context["row"], "coach_name", array()) != "")) {
                    // line 45
                    echo "                                <span><i class=\"fa fa-user\" aria-hidden=\"true\"></i>
                                    ";
                    // line 46
                    echo $this->getAttribute($context["row"], "coach_name", array());
                    echo "
                                </span>
                            ";
                }
                // line 49
                echo "                            <span>
                                <i class=\"fa fa-calendar\" aria-hidden=\"true\"></i>
                                ";
                // line 51
                echo $this->getAttribute($context["row"], "date", array());
                echo "
                            </span>
                        </div>
                        <div class=\"sessions-items\">
                        ";
                // line 55
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["row"], "courses", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 56
                    echo "                            <div class=\"courses\">
                                <div class=\"row\">
                                    <div class=\"col-md-2\">
                                        ";
                    // line 59
                    if ($this->getAttribute($context["item"], "link", array())) {
                        // line 60
                        echo "                                            <a href=\"";
                        echo $this->getAttribute($context["item"], "link", array());
                        echo "\" class=\"thumbnail\">
                                                <img class=\"img-responsive\" src=\"";
                        // line 61
                        echo $this->getAttribute($context["item"], "icon", array());
                        echo "\">
                                            </a>
                                        ";
                    } else {
                        // line 64
                        echo "                                            ";
                        echo Template::get_image("blackboard.png", 48, $this->getAttribute($context["item"], "title", array()));
                        echo "
                                        ";
                    }
                    // line 66
                    echo "                                    </div>
                                    <div class=\"col-md-10\">
                                        <h4>";
                    // line 68
                    echo $this->getAttribute($context["item"], "title", array());
                    echo "</h4>
                                        <div class=\"list-teachers\">
                                            ";
                    // line 70
                    if ((twig_length_filter($this->env, $this->getAttribute($context["item"], "coaches", array())) > 0)) {
                        // line 71
                        echo "                                                <img src=\"";
                        echo Template::get_icon_path("teacher.png", 16);
                        echo "\" width=\"16\" height=\"16\">&nbsp;
                                                ";
                        // line 72
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["item"], "coaches", array()));
                        $context['loop'] = array(
                          'parent' => $context['_parent'],
                          'index0' => 0,
                          'index'  => 1,
                          'first'  => true,
                        );
                        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                            $length = count($context['_seq']);
                            $context['loop']['revindex0'] = $length - 1;
                            $context['loop']['revindex'] = $length;
                            $context['loop']['length'] = $length;
                            $context['loop']['last'] = 1 === $length;
                        }
                        foreach ($context['_seq'] as $context["_key"] => $context["coach"]) {
                            // line 73
                            echo "                                                    ";
                            echo ((($this->getAttribute($context["loop"], "index", array()) > 1)) ? (" | ") : (""));
                            echo "
                                                    <a href=\"";
                            // line 74
                            echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_ajax", array()) . "user_manager.ajax.php?") . twig_urlencode_filter(array("a" => "get_user_popup", "user_id" => $this->getAttribute($context["coach"], "user_id", array()))));
                            echo "\"
                                                       data-title=\"";
                            // line 75
                            echo $this->getAttribute($context["coach"], "full_name", array());
                            echo "\" class=\"ajax\">
                                                        ";
                            // line 76
                            echo $this->getAttribute($context["coach"], "firstname", array());
                            echo ",
                                                        ";
                            // line 77
                            echo $this->getAttribute($context["coach"], "lastname", array());
                            echo "
                                                    </a>
                                                ";
                            ++$context['loop']['index0'];
                            ++$context['loop']['index'];
                            $context['loop']['first'] = false;
                            if (isset($context['loop']['length'])) {
                                --$context['loop']['revindex0'];
                                --$context['loop']['revindex'];
                                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                            }
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['coach'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 80
                        echo "                                            ";
                    }
                    // line 81
                    echo "                                        </div>
                                    </div>
                                </div>
                            </div>
                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 86
                echo "                        </div>
                    </div>
                </div>
            </div>
        ";
            } else {
                // line 91
                echo "        <div class=\"panel-heading\">
            <a href=\"";
                // line 92
                echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "session/index.php?session_id=") . $this->getAttribute($context["row"], "id", array()));
                echo "\">
                <img id=\"session_img_";
                // line 93
                echo $this->getAttribute($context["row"], "id", array());
                echo "\" src=\"";
                echo Template::get_icon_path("window_list.png", 32);
                echo "\" alt=\"";
                echo $this->getAttribute($context["row"], "title", array());
                echo "\" title=\"";
                echo $this->getAttribute($context["row"], "title", array());
                echo "\"/>
                ";
                // line 94
                echo $this->getAttribute($context["row"], "title", array());
                echo "
            </a>
        </div>
        <!-- view simple info -->
        <div class=\"panel-body\">
            <div class=\"row\">
                <div class=\"col-md-2\">
                    <a class=\"thumbnail\" href=\"";
                // line 101
                echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "session/index.php?session_id=") . $this->getAttribute($context["row"], "id", array()));
                echo "\">
                        <img class=\"img-responsive\" src=\"";
                // line 102
                echo (($this->getAttribute($context["row"], "image", array())) ? (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_upload", array()) . $this->getAttribute($context["row"], "image", array()))) : (Template::get_icon_path("session_default.png")));
                echo "\" alt=\"";
                echo $this->getAttribute($context["row"], "title", array());
                echo "\" title=\"";
                echo $this->getAttribute($context["row"], "title", array());
                echo "\"/>
                    </a>
                </div>
                <div class=\"col-md-10\">
                    <div class=\"info-session\">
                        <div class=\"date\">
                            <i class=\"fa fa-calendar\" aria-hidden=\"true\"></i>
                            ";
                // line 109
                echo $this->getAttribute($context["row"], "date", array());
                echo "
                            ";
                // line 110
                if ($this->getAttribute($context["row"], "coach_name", array())) {
                    // line 111
                    echo "                                <h5 class=\"teacher-name\">";
                    echo Template::get_image("teacher.png", 16);
                    echo " <a class=\"ajax\" href=\"";
                    echo $this->getAttribute($context["row"], "coach_url", array());
                    echo "\" alt=\"";
                    echo $this->getAttribute($context["row"], "coach_name", array());
                    echo "\">";
                    echo $this->getAttribute($context["row"], "coach_name", array());
                    echo "</a></h5>
                            ";
                }
                // line 113
                echo "                        </div>
                        ";
                // line 114
                if (($this->getAttribute($context["row"], "description", array()) != "")) {
                    // line 115
                    echo "                            <div class=\"description\">
                                ";
                    // line 116
                    echo $this->getAttribute($context["row"], "description", array());
                    echo "
                            </div>                 
                        ";
                }
                // line 119
                echo "                    </div>
                </div>
            </div>
        </div>
        <!-- end view simple info -->
    ";
            }
            // line 125
            echo "    </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "default/user_portal/classic_session.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  380 => 125,  372 => 119,  366 => 116,  363 => 115,  361 => 114,  358 => 113,  346 => 111,  344 => 110,  340 => 109,  326 => 102,  322 => 101,  312 => 94,  302 => 93,  298 => 92,  295 => 91,  288 => 86,  278 => 81,  275 => 80,  258 => 77,  254 => 76,  250 => 75,  246 => 74,  241 => 73,  224 => 72,  219 => 71,  217 => 70,  212 => 68,  208 => 66,  202 => 64,  196 => 61,  191 => 60,  189 => 59,  184 => 56,  180 => 55,  173 => 51,  169 => 49,  163 => 46,  160 => 45,  158 => 44,  155 => 43,  149 => 41,  147 => 40,  139 => 37,  136 => 36,  129 => 32,  123 => 31,  119 => 30,  116 => 29,  113 => 28,  110 => 27,  108 => 26,  103 => 24,  97 => 23,  91 => 22,  85 => 20,  83 => 19,  81 => 18,  77 => 17,  71 => 16,  64 => 15,  62 => 14,  60 => 13,  55 => 11,  49 => 10,  43 => 9,  38 => 8,  36 => 7,  34 => 6,  31 => 5,  28 => 4,  26 => 3,  23 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
