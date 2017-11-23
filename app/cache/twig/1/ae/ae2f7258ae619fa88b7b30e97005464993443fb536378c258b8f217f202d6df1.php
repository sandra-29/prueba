<?php

/* default/layout/page.tpl */
class __TwigTemplate_2ceccb27d3c76a212cfcb5458539dbd0fac36b714f0d3b8d5958b1e47cd04957 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'breadcrumb' => array($this, 'block_breadcrumb'),
            'body' => array($this, 'block_body'),
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
<div class=\"wrap\">
    ";
        // line 15
        if (((isset($context["displayCookieUsageWarning"]) ? $context["displayCookieUsageWarning"] : null) == true)) {
            // line 16
            echo "    <!-- Display Cookies validation -->
    <div class=\"toolbar-cookie alert-warning\">
        <form onSubmit=\"\$(this).toggle('slow')\" action=\"\" method=\"post\">
            <input value=1 type=\"hidden\" name=\"acceptCookies\"/>
            <div class=\"cookieUsageValidation\">
                ";
            // line 21
            echo get_lang("YouAcceptCookies");
            echo "
                <span style=\"margin-left:20px;\" onclick=\"\$(this).next().toggle('slow'); \$(this).toggle('slow')\">
                    (";
            // line 23
            echo get_lang("More");
            echo ")
                </span>
                <div style=\"display:none; margin:20px 0;\">
                    ";
            // line 26
            echo get_lang("HelpCookieUsageValidation");
            echo "
                </div>
                <span style=\"margin-left:20px;\" onclick=\"\$(this).parent().parent().submit()\">
                    (";
            // line 29
            echo get_lang("Accept");
            echo ")
                </span>
            </div>
        </form>
    </div>
    ";
        }
        // line 35
        echo "
    ";
        // line 36
        if (((isset($context["show_header"]) ? $context["show_header"] : null) == true)) {
            // line 37
            echo "    ";
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_header.tpl"), "default/layout/page.tpl", 37)->display($context);
            // line 38
            echo "    ";
        }
        // line 39
        echo "    ";
        if ( !(null === (isset($context["show_course_shortcut"]) ? $context["show_course_shortcut"] : null))) {
            // line 40
            echo "        <div class=\"nav-tools\">
            ";
            // line 41
            echo (isset($context["show_course_shortcut"]) ? $context["show_course_shortcut"] : null);
            echo "
        </div>
    ";
        }
        // line 44
        echo "\t<section id=\"content-section\">
        <div class=\"container\">
            ";
        // line 46
        $this->displayBlock('breadcrumb', $context, $blocks);
        // line 49
        echo "
            ";
        // line 50
        $this->displayBlock('body', $context, $blocks);
        // line 53
        echo "        </div>
\t</section>

    ";
        // line 56
        if (((isset($context["show_footer"]) ? $context["show_footer"] : null) == true)) {
            // line 57
            echo "\t";
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_footer.tpl"), "default/layout/page.tpl", 57)->display($context);
            // line 58
            echo "    ";
        }
        // line 59
        echo "    </div>
  </body>
</html>";
    }

    // line 8
    public function block_head($context, array $blocks = array())
    {
        // line 9
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/head.tpl"), "default/layout/page.tpl", 9)->display($context);
    }

    // line 46
    public function block_breadcrumb($context, array $blocks = array())
    {
        // line 47
        echo "                ";
        echo (isset($context["breadcrumb"]) ? $context["breadcrumb"] : null);
        echo "
            ";
    }

    // line 50
    public function block_body($context, array $blocks = array())
    {
        // line 51
        echo "                ";
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "
            ";
    }

    public function getTemplateName()
    {
        return "default/layout/page.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 51,  166 => 50,  159 => 47,  156 => 46,  152 => 9,  149 => 8,  143 => 59,  140 => 58,  137 => 57,  135 => 56,  130 => 53,  128 => 50,  125 => 49,  123 => 46,  119 => 44,  113 => 41,  110 => 40,  107 => 39,  104 => 38,  101 => 37,  99 => 36,  96 => 35,  87 => 29,  81 => 26,  75 => 23,  70 => 21,  63 => 16,  61 => 15,  56 => 13,  48 => 12,  45 => 11,  43 => 8,  38 => 6,  33 => 4,  29 => 3,  25 => 2,  22 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
