<?php

/* default/layout/head.tpl */
class __TwigTemplate_b7af8cff6723bfba5ea12cd16f3a57f610a61e44e2f7d7716f15de522f423df9 extends Twig_Template
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
        echo "<meta charset=\"";
        echo (isset($context["system_charset"]) ? $context["system_charset"] : null);
        echo "\" />
<link href=\"https://chamilo.org/chamilo-lms/\" rel=\"help\" />
<link href=\"https://chamilo.org/the-association/\" rel=\"author\" />
<link href=\"https://chamilo.org/the-association/\" rel=\"copyright\" />
<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
<![endif]-->
";
        // line 9
        echo (isset($context["prefetch"]) ? $context["prefetch"] : null);
        echo "
";
        // line 10
        echo (isset($context["favico"]) ? $context["favico"] : null);
        echo "
<link rel=\"apple-touch-icon\" href=\"";
        // line 11
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
        echo "apple-touch-icon.png\" />
<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />
<meta name=\"generator\" content=\"";
        // line 13
        echo $this->getAttribute((isset($context["_s"]) ? $context["_s"] : null), "software_name", array());
        echo " ";
        echo twig_slice($this->env, $this->getAttribute((isset($context["_s"]) ? $context["_s"] : null), "system_version", array()), 0, 1);
        echo "\" />
";
        // line 16
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>";
        // line 17
        echo (isset($context["title_string"]) ? $context["title_string"] : null);
        echo "</title>
";
        // line 18
        echo (isset($context["social_meta"]) ? $context["social_meta"] : null);
        echo "
";
        // line 19
        echo (isset($context["css_static_file_to_string"]) ? $context["css_static_file_to_string"] : null);
        echo "
";
        // line 20
        echo (isset($context["js_file_to_string"]) ? $context["js_file_to_string"] : null);
        echo "
";
        // line 21
        echo (isset($context["extra_headers"]) ? $context["extra_headers"] : null);
        echo "
<script>

/* Global chat variables */
var ajax_url = '";
        // line 25
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_ajax", array());
        echo "chat.ajax.php';
var online_button = '";
        // line 26
        echo (isset($context["online_button"]) ? $context["online_button"] : null);
        echo "';
var offline_button = '";
        // line 27
        echo (isset($context["offline_button"]) ? $context["offline_button"] : null);
        echo "';
var connect_lang = '";
        // line 28
        echo get_lang("ChatConnected");
        echo "';
var disconnect_lang = '";
        // line 29
        echo get_lang("ChatDisconnected");
        echo "';
</script>

";
        // line 32
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/header.js.tpl"), "default/layout/head.tpl", 32)->display($context);
        // line 33
        echo "
";
        // line 34
        echo (isset($context["css_custom_file_to_string"]) ? $context["css_custom_file_to_string"] : null);
        echo "
";
        // line 35
        echo (isset($context["css_style_print"]) ? $context["css_style_print"] : null);
        echo "
";
        // line 36
        echo (isset($context["header_extra_content"]) ? $context["header_extra_content"] : null);
        echo "
";
    }

    public function getTemplateName()
    {
        return "default/layout/head.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 36,  107 => 35,  103 => 34,  100 => 33,  98 => 32,  92 => 29,  88 => 28,  84 => 27,  80 => 26,  76 => 25,  69 => 21,  65 => 20,  61 => 19,  57 => 18,  53 => 17,  50 => 16,  44 => 13,  39 => 11,  35 => 10,  31 => 9,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
