<?php

/* default/layout/show_header.tpl */
class __TwigTemplate_d2d89f6f772f2bc7f0a4b6e9f9cd7ed3951d0d93433701215774fbf6e1a1f218 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'breadcrumb' => array($this, 'block_breadcrumb'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<!--[if lt IE 7]> <html lang=\"";
        // line 2
        echo (isset($context["document_language"]) ? $context["document_language"] : null);
        echo "\" class=\"no-js lt-ie9 lt-ie8 lt-ie7\"> <![endif]-->
<!--[if IE 7]>    <html lang=\"";
        // line 3
        echo (isset($context["document_language"]) ? $context["document_language"] : null);
        echo "\" class=\"no-js lt-ie9 lt-ie8\"> <![endif]-->
<!--[if IE 8]>    <html lang=\"";
        // line 4
        echo (isset($context["document_language"]) ? $context["document_language"] : null);
        echo "\" class=\"no-js lt-ie9\"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang=\"";
        // line 6
        echo (isset($context["document_language"]) ? $context["document_language"] : null);
        echo "\" class=\"no-js\"> <!--<![endif]-->
<head>
";
        // line 8
        $this->displayBlock('head', $context, $blocks);
        // line 11
        echo "</head>
<body dir=\"";
        // line 12
        echo (isset($context["text_direction"]) ? $context["text_direction"] : null);
        echo "\" class=\"";
        echo (isset($context["section_name"]) ? $context["section_name"] : null);
        echo " ";
        echo (isset($context["login_class"]) ? $context["login_class"] : null);
        echo "\">
<noscript>";
        // line 13
        echo get_lang("NoJavascript");
        echo "</noscript>
";
        // line 14
        if (((isset($context["show_header"]) ? $context["show_header"] : null) == true)) {
            // line 15
            echo "<div class=\"wrap\">
    ";
            // line 16
            if (((isset($context["displayCookieUsageWarning"]) ? $context["displayCookieUsageWarning"] : null) == true)) {
                // line 17
                echo "    <!-- Display Cookies validation -->
    <div class=\"toolbar-cookie alert-warning\">
        <form onSubmit=\"\$(this).toggle('slow')\" action=\"\" method=\"post\">
            <input value=1 type=\"hidden\" name=\"acceptCookies\"/>
            <div class=\"cookieUsageValidation\">
                ";
                // line 22
                echo get_lang("YouAcceptCookies");
                echo "
                <span style=\"margin-left:20px;\" onclick=\"\$(this).next().toggle('slow'); \$(this).toggle('slow')\">
                    (";
                // line 24
                echo get_lang("More");
                echo ")
                </span>
                <div style=\"display:none; margin:20px 0;\">
                    ";
                // line 27
                echo get_lang("HelpCookieUsageValidation");
                echo "
                </div>
                <span style=\"margin-left:20px;\" onclick=\"\$(this).parent().parent().submit()\">
                    (";
                // line 30
                echo get_lang("Accept");
                echo ")
                </span>
            </div>
        </form>
    </div>
";
            }
            // line 36
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_header.tpl"), "default/layout/show_header.tpl", 36)->display($context);
            // line 37
            echo "<section id=\"content-section\">
    <div class=\"container\">
        ";
            // line 39
            $this->displayBlock('breadcrumb', $context, $blocks);
            // line 42
            echo "        ";
            echo (isset($context["flash_messages"]) ? $context["flash_messages"] : null);
            echo "
";
        }
    }

    // line 8
    public function block_head($context, array $blocks = array())
    {
        // line 9
        echo "    ";
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/head.tpl"), "default/layout/show_header.tpl", 9)->display($context);
    }

    // line 39
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 40
        echo "            ";
        echo (isset($context["breadcrumb"]) ? $context["breadcrumb"] : null);
        echo "
        ";
    }

    public function getTemplateName()
    {
        return "default/layout/show_header.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  126 => 40,  123 => 39,  118 => 9,  115 => 8,  107 => 42,  105 => 39,  101 => 37,  99 => 36,  90 => 30,  84 => 27,  78 => 24,  73 => 22,  66 => 17,  64 => 16,  61 => 15,  59 => 14,  55 => 13,  47 => 12,  44 => 11,  42 => 8,  37 => 6,  32 => 4,  28 => 3,  24 => 2,  21 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
