<?php

/* default/layout/page_footer.tpl */
class __TwigTemplate_f976aee22d5ee18ff92aa3db5663d0ff5ed7a5c6d7211e2ba87eb23ecfa21c89 extends Twig_Template
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
        echo "<footer id=\"footer-section\" class=\"sticky-footer bgfooter\">
    <div class=\"container\">
        <div class=\"pre-footer\">
            ";
        // line 4
        if ( !(null === (isset($context["plugin_pre_footer"]) ? $context["plugin_pre_footer"] : null))) {
            // line 5
            echo "            <div id=\"plugin_pre_footer\">
                ";
            // line 6
            echo (isset($context["plugin_pre_footer"]) ? $context["plugin_pre_footer"] : null);
            echo "
            </div>
            ";
        }
        // line 9
        echo "        </div>
        <div class=\"sub-footer\">
            <div class=\"row\">
                <div class=\"col-md-4\">
                    ";
        // line 13
        if ( !(null === (isset($context["session_teachers"]) ? $context["session_teachers"] : null))) {
            // line 14
            echo "                    <div class=\"session-teachers\">
                        ";
            // line 15
            echo (isset($context["session_teachers"]) ? $context["session_teachers"] : null);
            echo "
                    </div>
                    ";
        }
        // line 18
        echo "                    ";
        if ( !(null === (isset($context["teachers"]) ? $context["teachers"] : null))) {
            // line 19
            echo "                    <div class=\"teachers\">
                        ";
            // line 20
            echo (isset($context["teachers"]) ? $context["teachers"] : null);
            echo "
                    </div>
                    ";
        }
        // line 23
        echo "                    ";
        if ( !(null === (isset($context["plugin_footer_left"]) ? $context["plugin_footer_left"] : null))) {
            // line 24
            echo "                    <div id=\"plugin_footer_left\">
                        ";
            // line 25
            echo (isset($context["plugin_footer_left"]) ? $context["plugin_footer_left"] : null);
            echo "
                    </div>
                    ";
        }
        // line 28
        echo "                </div>
                <div class=\"col-md-4\">
                    ";
        // line 30
        if ( !(null === (isset($context["plugin_footer_center"]) ? $context["plugin_footer_center"] : null))) {
            // line 31
            echo "                    <div id=\"plugin_footer_center\">
                        ";
            // line 32
            echo (isset($context["plugin_footer_center"]) ? $context["plugin_footer_center"] : null);
            echo "
                    </div>
                    ";
        }
        // line 35
        echo "                </div>
                <div class=\"col-md-4 text-right\">
                    ";
        // line 37
        if ( !(null === (isset($context["administrator_name"]) ? $context["administrator_name"] : null))) {
            // line 38
            echo "                    <div class=\"administrator-name\">
                        ";
            // line 39
            echo (isset($context["administrator_name"]) ? $context["administrator_name"] : null);
            echo "
                    </div>
                    ";
        }
        // line 42
        echo "                    <div class=\"software-name\">
\t                <a href=\"";
        // line 43
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
        echo "\" target=\"_blank\">
                            ";
        // line 44
        echo sprintf(get_lang("PoweredByX"), $this->getAttribute((isset($context["_s"]) ? $context["_s"] : null), "software_name", array()));
        echo "
                        </a>&copy; ";
        // line 45
        echo twig_date_format_filter($this->env, "now", "Y");
        echo "
                    </div>
                    ";
        // line 47
        if ( !(null === (isset($context["plugin_footer_right"]) ? $context["plugin_footer_right"] : null))) {
            // line 48
            echo "                    <div id=\"plugin_footer_right\">
                        ";
            // line 49
            echo (isset($context["plugin_footer_right"]) ? $context["plugin_footer_right"] : null);
            echo "
                    </div>
                    ";
        }
        // line 52
        echo "                </div>
            </div>
        </div>
        <div class=\"extra-footer\">
            ";
        // line 56
        echo (isset($context["footer_extra_content"]) ? $context["footer_extra_content"] : null);
        echo "
        </div>
    </div>
</footer>

<div class=\"modal fade\" id=\"expand-image-modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"expand-image-modal-title\" aria-hidden=\"true\">
    <div class=\"modal-dialog modal-lg\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
        // line 65
        echo get_lang("Close");
        echo "\"><span aria-hidden=\"true\">&times;</span></button>
                <h4 class=\"modal-title\" id=\"expand-image-modal-title\">&nbsp;</h4>
            </div>
            <div class=\"modal-body\">
            </div>
        </div>
    </div>
</div>
";
        // line 74
        echo "<div class=\"modal fade\" id=\"global-modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"global-modal-title\" aria-hidden=\"true\">
    <div class=\"modal-dialog modal-lg\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
        // line 78
        echo get_lang("Close");
        echo "\">
                    <span aria-hidden=\"true\">&times;</span>
                </button>
                <h4 class=\"modal-title\" id=\"global-modal-title\">&nbsp;</h4>
            </div>
            <div class=\"modal-body\">
            </div>
        </div>
    </div>
</div>

";
        // line 89
        $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/footer.js.tpl"), "default/layout/page_footer.tpl", 89)->display($context);
        // line 90
        echo "
";
        // line 91
        echo (isset($context["execution_stats"]) ? $context["execution_stats"] : null);
    }

    public function getTemplateName()
    {
        return "default/layout/page_footer.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  187 => 91,  184 => 90,  182 => 89,  168 => 78,  162 => 74,  151 => 65,  139 => 56,  133 => 52,  127 => 49,  124 => 48,  122 => 47,  117 => 45,  113 => 44,  109 => 43,  106 => 42,  100 => 39,  97 => 38,  95 => 37,  91 => 35,  85 => 32,  82 => 31,  80 => 30,  76 => 28,  70 => 25,  67 => 24,  64 => 23,  58 => 20,  55 => 19,  52 => 18,  46 => 15,  43 => 14,  41 => 13,  35 => 9,  29 => 6,  26 => 5,  24 => 4,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
