<?php

/* default/social/user_block.tpl */
class __TwigTemplate_075181524783d182cc187919ebb74df21cf1d749b379f41feae04094c5db6fb4 extends Twig_Template
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
        echo "<div class=\"sidebar-avatar\">
    <div class=\"panel-group\" id=\"sn-avatar\" role=\"tablist\" aria-multiselectable=\"true\">
        <div class=\"panel panel-default\">
            <div class=\"panel-heading\" role=\"tab\" id=\"heading-sn\">
                <h4 class=\"panel-title\">
                    <a role=\"button\" data-toggle=\"collapse\" data-parent=\"#sn-avatar\" href=\"#sn-avatar-one\" aria-expanded=\"true\" aria-controls=\"sn-avatar-one\">
                    ";
        // line 7
        echo get_lang("Role");
        echo "
                    </a>
                </h4>
            </div>
            <div id=\"sn-avatar-one\" class=\"panel-collapse collapse in\" role=\"tabpanel\" aria-labelledby=\"heading-sn\">
                <div class=\"panel-body\">
                    ";
        // line 13
        echo (isset($context["social_avatar_block"]) ? $context["social_avatar_block"] : null);
        echo "
                    <ul class=\"list-user-data\">
                        <li class=\"item\">
                            ";
        // line 16
        echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "complete_name", array());
        echo "
                        </li>
                        ";
        // line 18
        if ((isset($context["show_full_profile"]) ? $context["show_full_profile"] : null)) {
            // line 19
            echo "                            <li class=\"item\">
                                <a href=\"";
            // line 20
            echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
            echo "main/messages/new_message.php\">
                                <img src=\"";
            // line 21
            echo Template::get_icon_path("instant_message.png");
            echo "\" alt=\"";
            echo get_lang("Email");
            echo "\">
                                ";
            // line 22
            echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "email", array());
            echo "
                                </a>
                            </li>
                            <li class=\"item\">
                                <a href=\"";
            // line 26
            echo (isset($context["vcard_user_link"]) ? $context["vcard_user_link"] : null);
            echo "\">
                                <img src=\"";
            // line 27
            echo Template::get_icon_path("vcard.png", 16);
            echo "\" alt=\"";
            echo get_lang("BusinessCard");
            echo "\" width=\"16\" height=\"16\">
                                ";
            // line 28
            echo get_lang("BusinessCard");
            echo "
                                </a>
                            </li>

                            ";
            // line 32
            $context["skype_account"] = "";
            // line 33
            echo "                            ";
            $context["linkedin_url"] = "";
            // line 34
            echo "                            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "extra", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["extra"]) {
                // line 35
                echo "                                ";
                if (($this->getAttribute($this->getAttribute($this->getAttribute($context["extra"], "value", array()), "getField", array(), "method"), "getVariable", array(), "method") == "skype")) {
                    // line 36
                    echo "                                    ";
                    $context["skype_account"] = $this->getAttribute($this->getAttribute($context["extra"], "value", array()), "getValue", array(), "method");
                    // line 37
                    echo "                                ";
                }
                // line 38
                echo "
                                ";
                // line 39
                if (($this->getAttribute($this->getAttribute($this->getAttribute($context["extra"], "value", array()), "getField", array(), "method"), "getVariable", array(), "method") == "linkedin_url")) {
                    // line 40
                    echo "                                    ";
                    $context["linkedin_url"] = $this->getAttribute($this->getAttribute($context["extra"], "value", array()), "getValue", array(), "method");
                    // line 41
                    echo "                                ";
                }
                // line 42
                echo "                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['extra'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 43
            echo "
                            ";
            // line 44
            if (((api_get_setting("allow_show_skype_account") == "true") &&  !twig_test_empty((isset($context["skype_account"]) ? $context["skype_account"] : null)))) {
                // line 45
                echo "                                <li class=\"item\">
                                    <a href=\"skype:";
                // line 46
                echo (isset($context["skype_account"]) ? $context["skype_account"] : null);
                echo "?chat\">
                                        <span class=\"fa fa-skype fa-fw\" aria-hidden=\"true\"></span> ";
                // line 47
                echo get_lang("Skype");
                echo "
                                    </a>
                                </li>
                            ";
            }
            // line 51
            echo "
                            ";
            // line 52
            if (((api_get_setting("allow_show_linkedin_url") == "true") &&  !twig_test_empty((isset($context["linkedin_url"]) ? $context["linkedin_url"] : null)))) {
                // line 53
                echo "                                <li class=\"item\">
                                    <a href=\"";
                // line 54
                echo (isset($context["linkedin_url"]) ? $context["linkedin_url"] : null);
                echo "\" target=\"_blank\">
                                        <span class=\"fa fa-linkedin fa-fw\" aria-hidden=\"true\"></span> ";
                // line 55
                echo get_lang("LinkedIn");
                echo "
                                    </a>
                                </li>
                            ";
            }
            // line 59
            echo "                        ";
        }
        // line 60
        echo "                        ";
        if (((isset($context["chat_enabled"]) ? $context["chat_enabled"] : null) == 1)) {
            // line 61
            echo "                            ";
            if (($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "user_is_online_in_chat", array()) != 0)) {
                // line 62
                echo "                                ";
                if (((isset($context["user_relation"]) ? $context["user_relation"] : null) == (isset($context["user_relation_type_friend"]) ? $context["user_relation_type_friend"] : null))) {
                    // line 63
                    echo "                                    <li class=\"item\">
                                        <a onclick=\"javascript:chatWith('";
                    // line 64
                    echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array());
                    echo "', '";
                    echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "complete_name", array());
                    echo "', '";
                    echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "user_is_online", array());
                    echo "','";
                    echo $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "avatar_small", array());
                    echo "')\" href=\"javascript:void(0);\">
                                            <img src=\"";
                    // line 65
                    echo Template::get_icon_path("online.png");
                    echo "\" alt=\"";
                    echo get_lang("Online");
                    echo "\">
                                            ";
                    // line 66
                    echo get_lang("Chat");
                    echo " (";
                    echo get_lang("Online");
                    echo ")
                                        </a>
                                    </li>
                                ";
                    // line 70
                    echo "                                    ";
                    // line 71
                    echo "                                    ";
                    // line 72
                    echo "                                ";
                }
                // line 73
                echo "                            ";
            }
            // line 74
            echo "                        ";
        }
        // line 75
        echo "
                    ";
        // line 76
        if ( !twig_test_empty((isset($context["profile_edition_link"]) ? $context["profile_edition_link"] : null))) {
            // line 77
            echo "                    <li class=\"item\">
                        <a class=\"btn btn-link btn-sm btn-block\" href=\"";
            // line 78
            echo (isset($context["profile_edition_link"]) ? $context["profile_edition_link"] : null);
            echo "\">
                        <em class=\"fa fa-edit\"></em>";
            // line 79
            echo get_lang("EditProfile");
            echo "
                        </a>
                    </li>
                    ";
        }
        // line 83
        echo "                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


";
    }

    public function getTemplateName()
    {
        return "default/social/user_block.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  231 => 83,  224 => 79,  220 => 78,  217 => 77,  215 => 76,  212 => 75,  209 => 74,  206 => 73,  203 => 72,  201 => 71,  199 => 70,  191 => 66,  185 => 65,  175 => 64,  172 => 63,  169 => 62,  166 => 61,  163 => 60,  160 => 59,  153 => 55,  149 => 54,  146 => 53,  144 => 52,  141 => 51,  134 => 47,  130 => 46,  127 => 45,  125 => 44,  122 => 43,  116 => 42,  113 => 41,  110 => 40,  108 => 39,  105 => 38,  102 => 37,  99 => 36,  96 => 35,  91 => 34,  88 => 33,  86 => 32,  79 => 28,  73 => 27,  69 => 26,  62 => 22,  56 => 21,  52 => 20,  49 => 19,  47 => 18,  42 => 16,  36 => 13,  27 => 7,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
