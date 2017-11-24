<?php

/* default/learnpath/list.tpl */
class __TwigTemplate_f23672c4ab7a300bf89dbaae081c18b6ea6fde3bb8d22ea6999fb1f16a6d064b extends Twig_Template
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
        echo "<script>
    function confirmation(name) {
        if (confirm(\"";
        // line 3
        echo get_lang("AreYouSureToDeleteJS");
        echo " \\\"\" + name + \"\\\" ?\")) {
            return true;
        } else {
            return false;
        }
    }
</script>

";
        // line 11
        echo (isset($context["introduction_section"]) ? $context["introduction_section"] : null);
        echo "

";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["data"]) ? $context["data"] : null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["lp_data"]) {
            // line 14
            echo "    <h3 class=\"page-header\">
        ";
            // line 15
            if ((isset($context["is_allowed_to_edit"]) ? $context["is_allowed_to_edit"] : null)) {
                // line 16
                echo "            ";
                if ((twig_length_filter($this->env, (isset($context["categories"]) ? $context["categories"] : null)) > 1)) {
                    // line 17
                    echo "                ";
                    echo $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getName", array(), "method");
                    echo "
            ";
                }
                // line 19
                echo "        ";
            } else {
                // line 20
                echo "            ";
                if ((twig_length_filter($this->env, (isset($context["categories"]) ? $context["categories"] : null)) > 1)) {
                    // line 21
                    echo "                ";
                    if (( !twig_test_empty($this->getAttribute($context["lp_data"], "lp_list", array())) && ($this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method") != 0))) {
                        // line 22
                        echo "                    ";
                        echo $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getName", array(), "method");
                        echo "
                ";
                    } elseif (( !twig_test_empty($this->getAttribute(                    // line 23
$context["lp_data"], "lp_list", array())) && ($this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method") == 0))) {
                        // line 24
                        echo "                    ";
                        echo $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getName", array(), "method");
                        echo "
                ";
                    } elseif (( !twig_test_empty($this->getAttribute(                    // line 25
$context["lp_data"], "lp_list", array())) && ($this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method") != 0))) {
                        // line 26
                        echo "                    ";
                        echo $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getName", array(), "method");
                        echo "
                ";
                    }
                    // line 28
                    echo "            ";
                }
                // line 29
                echo "        ";
            }
            // line 30
            echo "
        ";
            // line 31
            if ((($this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method") > 0) && (isset($context["is_allowed_to_edit"]) ? $context["is_allowed_to_edit"] : null))) {
                // line 32
                echo "            <a href=\"";
                echo ((("lp_controller.php?" . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=add_lp_category&id=") . $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method"));
                echo "\" title=\"";
                echo get_lang("Edit");
                echo "\">
                <img src=\"";
                // line 33
                echo Template::get_icon_path("edit.png");
                echo "\" alt=\"";
                echo get_lang("Edit");
                echo "\">
            </a>

            <a href=\"";
                // line 36
                echo ((("lp_controller.php?" . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=add_users_to_category&id=") . $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method"));
                echo "\" title=\"";
                echo get_lang("AddUser");
                echo "\">
                <img src=\"";
                // line 37
                echo Template::get_icon_path("user.png");
                echo "\" alt=\"";
                echo get_lang("AddUser");
                echo "\">
            </a>

            ";
                // line 40
                if (($this->getAttribute($context["loop"], "index0", array()) == 1)) {
                    // line 41
                    echo "                <a href=\"#\">
                    <img src=\"";
                    // line 42
                    echo Template::get_icon_path("up_na.png");
                    echo "\" alt=\"";
                    echo get_lang("Move");
                    echo "\">
                </a>
            ";
                } else {
                    // line 45
                    echo "                <a href=\"";
                    echo ((("lp_controller.php?" . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=move_up_category&id=") . $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method"));
                    echo "\" title=\"";
                    echo get_lang("Move");
                    echo "\">
                    <img src=\"";
                    // line 46
                    echo Template::get_icon_path("up.png");
                    echo "\" alt=\"";
                    echo get_lang("Move");
                    echo "\">
                </a>
            ";
                }
                // line 49
                echo "
            ";
                // line 50
                if (((twig_length_filter($this->env, (isset($context["data"]) ? $context["data"] : null)) - 1) == $this->getAttribute($context["loop"], "index0", array()))) {
                    // line 51
                    echo "                <a href=\"#\">
                    <img src=\"";
                    // line 52
                    echo Template::get_icon_path("down_na.png");
                    echo "\" alt=\"";
                    echo get_lang("Move");
                    echo "\">
                </a>
            ";
                } else {
                    // line 55
                    echo "                <a href=\"";
                    echo ((("lp_controller.php?" . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=move_down_category&id=") . $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method"));
                    echo "\" title=\"";
                    echo get_lang("Move");
                    echo "\">
                    <img src=\"";
                    // line 56
                    echo Template::get_icon_path("down.png");
                    echo "\" alt=\"";
                    echo get_lang("Move");
                    echo "\">
                </a>
            ";
                }
                // line 59
                echo "
            <a href=\"";
                // line 60
                echo ((("lp_controller.php?" . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=delete_lp_category&id=") . $this->getAttribute($this->getAttribute($context["lp_data"], "category", array()), "getId", array(), "method"));
                echo "\" title=\"";
                echo get_lang("Delete");
                echo "\">
                <img src=\"";
                // line 61
                echo Template::get_icon_path("delete.png");
                echo "\" alt=\"";
                echo get_lang("Delete");
                echo "\">
            </a>
        ";
            }
            // line 64
            echo "    </h3>

    ";
            // line 66
            if ($this->getAttribute($context["lp_data"], "lp_list", array())) {
                // line 67
                echo "        <div class=\"table-responsive\">
            <table class=\"table table-hover table-striped\">
                <thead>
                    <tr>
                        <th>";
                // line 71
                echo get_lang("Title");
                echo "</th>
                        ";
                // line 72
                if ((isset($context["is_allowed_to_edit"]) ? $context["is_allowed_to_edit"] : null)) {
                    // line 73
                    echo "                            <th>";
                    echo get_lang("PublicationDate");
                    echo "</th>
                            <th>";
                    // line 74
                    echo get_lang("ExpirationDate");
                    echo "</th>
                            <th>";
                    // line 75
                    echo get_lang("Progress");
                    echo "</th>
                            <th>";
                    // line 76
                    echo get_lang("AuthoringOptions");
                    echo "</th>
                        ";
                } else {
                    // line 78
                    echo "                            ";
                    if ( !(isset($context["is_invitee"]) ? $context["is_invitee"] : null)) {
                        // line 79
                        echo "                                <th>";
                        echo get_lang("Progress");
                        echo "</th>
                            ";
                    }
                    // line 81
                    echo "
                            <th>";
                    // line 82
                    echo get_lang("Actions");
                    echo "</th>
                        ";
                }
                // line 84
                echo "                    </tr>
                </thead>
                <tbody>
                    ";
                // line 87
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["lp_data"], "lp_list", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                    // line 88
                    echo "                        <tr>
                            <td>
                                ";
                    // line 90
                    echo $this->getAttribute($context["row"], "learnpath_icon", array());
                    echo "
                                <a href=\"";
                    // line 91
                    echo $this->getAttribute($context["row"], "url_start", array());
                    echo "\">
                                    ";
                    // line 92
                    echo $this->getAttribute($context["row"], "title", array());
                    echo "
                                    ";
                    // line 93
                    echo $this->getAttribute($context["row"], "session_image", array());
                    echo "
                                    ";
                    // line 94
                    echo $this->getAttribute($context["row"], "extra", array());
                    echo "
                                </a>
                            </td>
                            ";
                    // line 97
                    if ((isset($context["is_allowed_to_edit"]) ? $context["is_allowed_to_edit"] : null)) {
                        // line 98
                        echo "                                <td>
                                    ";
                        // line 99
                        if ($this->getAttribute($context["row"], "start_time", array())) {
                            // line 100
                            echo "                                        <span class=\"small\">";
                            echo $this->getAttribute($context["row"], "start_time", array());
                            echo "</span>
                                    ";
                        }
                        // line 102
                        echo "                                </td>
                                <td>
                                    <span class=\"small\">";
                        // line 104
                        echo $this->getAttribute($context["row"], "end_time", array());
                        echo "</span>
                                </td>
                                <td>
                                    ";
                        // line 107
                        echo $this->getAttribute($context["row"], "dsp_progress", array());
                        echo "
                                </td>
                            ";
                    } else {
                        // line 110
                        echo "                                ";
                        if ( !(isset($context["is_invitee"]) ? $context["is_invitee"] : null)) {
                            // line 111
                            echo "                                    <td>
                                        ";
                            // line 112
                            echo $this->getAttribute($context["row"], "dsp_progress", array());
                            echo "
                                    </td>
                                ";
                        }
                        // line 115
                        echo "                            ";
                    }
                    // line 116
                    echo "
                            <td>
                                ";
                    // line 118
                    echo $this->getAttribute($context["row"], "action_build", array());
                    echo "
                                ";
                    // line 119
                    echo $this->getAttribute($context["row"], "action_edit", array());
                    echo "
                                ";
                    // line 120
                    echo $this->getAttribute($context["row"], "action_visible", array());
                    echo "
                                ";
                    // line 121
                    echo $this->getAttribute($context["row"], "action_tracking", array());
                    echo "
                                ";
                    // line 122
                    echo $this->getAttribute($context["row"], "action_publish", array());
                    echo "
                                ";
                    // line 123
                    echo $this->getAttribute($context["row"], "action_subscribe_users", array());
                    echo "
                                ";
                    // line 124
                    echo $this->getAttribute($context["row"], "action_serious_game", array());
                    echo "
                                ";
                    // line 125
                    echo $this->getAttribute($context["row"], "action_reinit", array());
                    echo "
                                ";
                    // line 126
                    echo $this->getAttribute($context["row"], "action_default_view", array());
                    echo "
                                ";
                    // line 127
                    echo $this->getAttribute($context["row"], "action_debug", array());
                    echo "
                                ";
                    // line 128
                    echo $this->getAttribute($context["row"], "action_export", array());
                    echo "
                                ";
                    // line 129
                    echo $this->getAttribute($context["row"], "action_copy", array());
                    echo "
                                ";
                    // line 130
                    echo $this->getAttribute($context["row"], "action_auto_launch", array());
                    echo "
                                ";
                    // line 131
                    echo $this->getAttribute($context["row"], "action_pdf", array());
                    echo "
                                ";
                    // line 132
                    echo $this->getAttribute($context["row"], "action_delete", array());
                    echo "
                                ";
                    // line 133
                    echo $this->getAttribute($context["row"], "action_order", array());
                    echo "
                            </td>
                        </tr>
                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 137
                echo "                </tbody>
            </table>
        </div>
    ";
            }
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lp_data'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 142
        echo "
";
        // line 143
        if (((isset($context["is_allowed_to_edit"]) ? $context["is_allowed_to_edit"] : null) &&  !(isset($context["lp_is_shown"]) ? $context["lp_is_shown"] : null))) {
            // line 144
            echo "    <div id=\"no-data-view\">
        <h2>";
            // line 145
            echo get_lang("LearningPaths");
            echo "</h2>
        <img src=\"";
            // line 146
            echo Template::get_icon_path("scorms.png", 64);
            echo "\" width=\"64\" height=\"64\">
        <div class=\"controls\">
            <a href=\"";
            // line 148
            echo ((((isset($context["web_self"]) ? $context["web_self"] : null) . "?") . $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_cid_query", array())) . "&action=add_lp");
            echo "\" class=\"btn btn-default\">
                ";
            // line 149
            echo get_lang("LearnpathAddLearnpath");
            echo "
            </a>
        </div>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "default/learnpath/list.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  460 => 149,  456 => 148,  451 => 146,  447 => 145,  444 => 144,  442 => 143,  439 => 142,  421 => 137,  411 => 133,  407 => 132,  403 => 131,  399 => 130,  395 => 129,  391 => 128,  387 => 127,  383 => 126,  379 => 125,  375 => 124,  371 => 123,  367 => 122,  363 => 121,  359 => 120,  355 => 119,  351 => 118,  347 => 116,  344 => 115,  338 => 112,  335 => 111,  332 => 110,  326 => 107,  320 => 104,  316 => 102,  310 => 100,  308 => 99,  305 => 98,  303 => 97,  297 => 94,  293 => 93,  289 => 92,  285 => 91,  281 => 90,  277 => 88,  273 => 87,  268 => 84,  263 => 82,  260 => 81,  254 => 79,  251 => 78,  246 => 76,  242 => 75,  238 => 74,  233 => 73,  231 => 72,  227 => 71,  221 => 67,  219 => 66,  215 => 64,  207 => 61,  201 => 60,  198 => 59,  190 => 56,  183 => 55,  175 => 52,  172 => 51,  170 => 50,  167 => 49,  159 => 46,  152 => 45,  144 => 42,  141 => 41,  139 => 40,  131 => 37,  125 => 36,  117 => 33,  110 => 32,  108 => 31,  105 => 30,  102 => 29,  99 => 28,  93 => 26,  91 => 25,  86 => 24,  84 => 23,  79 => 22,  76 => 21,  73 => 20,  70 => 19,  64 => 17,  61 => 16,  59 => 15,  56 => 14,  39 => 13,  34 => 11,  23 => 3,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
