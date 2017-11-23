<?php

/* default/layout/login_form.tpl */
class __TwigTemplate_e07709773e57801a07e6d0d421d90ee0111371b64862caa36a59ddb9795297ba extends Twig_Template
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
        if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "logged", array()) == 0)) {
            // line 2
            if ((isset($context["login_form"]) ? $context["login_form"] : null)) {
                // line 3
                echo "    <div id=\"login_block\" class=\"panel panel-default\">
        <div class=\"panel-body\">
        ";
                // line 5
                echo (isset($context["login_language_form"]) ? $context["login_language_form"] : null);
                echo "

        ";
                // line 7
                if ( !(null === (isset($context["plugin_login_top"]) ? $context["plugin_login_top"] : null))) {
                    // line 8
                    echo "            <div id=\"plugin_login_top\">
                ";
                    // line 9
                    echo (isset($context["plugin_login_top"]) ? $context["plugin_login_top"] : null);
                    echo "
            </div>
        ";
                }
                // line 12
                echo "
        ";
                // line 13
                echo (isset($context["login_failed"]) ? $context["login_failed"] : null);
                echo "
        ";
                // line 14
                echo (isset($context["login_form"]) ? $context["login_form"] : null);
                echo "

        ";
                // line 16
                if (((api_get_setting("allow_lostpassword") == "true") || (api_get_setting("allow_registration") == "true"))) {
                    // line 17
                    echo "            <ul class=\"nav nav-pills nav-stacked\">
                ";
                    // line 18
                    if ((api_get_setting("allow_registration") != "false")) {
                        // line 19
                        echo "                    <li><a href=\"";
                        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                        echo "auth/inscription.php\"> ";
                        echo get_lang("SignUp");
                        echo " </a></li>
                ";
                    }
                    // line 21
                    echo "
                ";
                    // line 22
                    if ((api_get_setting("allow_lostpassword") == "true")) {
                        // line 23
                        echo "                    <li><a href=\"";
                        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                        echo "auth/lostPassword.php\"> ";
                        echo get_lang("LostPassword");
                        echo " </a></li>
                ";
                    }
                    // line 25
                    echo "            </ul>
        ";
                }
                // line 27
                echo "
        ";
                // line 28
                if ( !(null === (isset($context["plugin_login_bottom"]) ? $context["plugin_login_bottom"] : null))) {
                    // line 29
                    echo "            <div id=\"plugin_login_bottom\">
                ";
                    // line 30
                    echo (isset($context["plugin_login_bottom"]) ? $context["plugin_login_bottom"] : null);
                    echo "
            </div>
        ";
                }
                // line 33
                echo "        </div>
    </div>
";
            }
        }
    }

    public function getTemplateName()
    {
        return "default/layout/login_form.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 33,  95 => 30,  92 => 29,  90 => 28,  87 => 27,  83 => 25,  75 => 23,  73 => 22,  70 => 21,  62 => 19,  60 => 18,  57 => 17,  55 => 16,  50 => 14,  46 => 13,  43 => 12,  37 => 9,  34 => 8,  32 => 7,  27 => 5,  23 => 3,  21 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
