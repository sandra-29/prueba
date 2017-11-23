<?php

/* default/layout/layout_1_col.tpl */
class __TwigTemplate_a5a52cf91962a1d1e8b8f2e4267e0271f19c5ecedec11eec2d5c9e364bffc456 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page.tpl"), "default/layout/layout_1_col.tpl", 1);
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        if ((isset($context["plugin_main_top"]) ? $context["plugin_main_top"] : null)) {
            // line 5
            echo "        <div id=\"plugin_main_top\" class=\"col-md-12\">
            ";
            // line 6
            echo (isset($context["plugin_main_top"]) ? $context["plugin_main_top"] : null);
            echo "
        </div>
    ";
        }
        // line 9
        echo "    ";
        if ((isset($context["plugin_content_top"]) ? $context["plugin_content_top"] : null)) {
            // line 10
            echo "        <div id=\"plugin_content_top\" class=\"col-md-12\">
            ";
            // line 11
            echo (isset($context["plugin_content_top"]) ? $context["plugin_content_top"] : null);
            echo "
        </div>
    ";
        }
        // line 14
        echo "
    <div class=\"col-xs-12 col-md-12\">
        ";
        // line 16
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_body.tpl"), "default/layout/layout_1_col.tpl", 16)->display($context);
        // line 17
        echo "        ";
        $this->displayBlock('content', $context, $blocks);
        // line 24
        echo "        &nbsp;
    </div>

    ";
        // line 27
        if ((isset($context["plugin_content_bottom"]) ? $context["plugin_content_bottom"] : null)) {
            // line 28
            echo "        <div id=\"plugin_content_bottom\" class=\"col-md-12\">
            ";
            // line 29
            echo (isset($context["plugin_content_bottom"]) ? $context["plugin_content_bottom"] : null);
            echo "
        </div>
    ";
        }
        // line 32
        echo "
    ";
        // line 33
        if ((isset($context["plugin_main_bottom"]) ? $context["plugin_main_bottom"] : null)) {
            // line 34
            echo "        <div id=\"plugin_main_bottom\" class=\"col-md-12\">
            ";
            // line 35
            echo (isset($context["plugin_main_bottom"]) ? $context["plugin_main_bottom"] : null);
            echo "
        </div>
    ";
        }
    }

    // line 17
    public function block_content($context, array $blocks = array())
    {
        // line 18
        echo "            ";
        if ( !(null === (isset($context["content"]) ? $context["content"] : null))) {
            // line 19
            echo "                <section id=\"main_content\">
                ";
            // line 20
            echo (isset($context["content"]) ? $context["content"] : null);
            echo "
                </section>
            ";
        }
        // line 23
        echo "        ";
    }

    public function getTemplateName()
    {
        return "default/layout/layout_1_col.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 23,  105 => 20,  102 => 19,  99 => 18,  96 => 17,  88 => 35,  85 => 34,  83 => 33,  80 => 32,  74 => 29,  71 => 28,  69 => 27,  64 => 24,  61 => 17,  59 => 16,  55 => 14,  49 => 11,  46 => 10,  43 => 9,  37 => 6,  34 => 5,  31 => 4,  28 => 3,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
