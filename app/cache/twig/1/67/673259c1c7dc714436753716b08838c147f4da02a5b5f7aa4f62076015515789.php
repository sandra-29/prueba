<?php

/* default/social/skills_block.tpl */
class __TwigTemplate_40d6e5bb013a92a9e7297aa51d3ad601eee44545868955066605bc2c3b3a3b14 extends Twig_Template
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
jQuery(document).ready(function(){
    jQuery('.scrollbar-inner').scrollbar();
});
</script>
<div class=\"panel-group\" id=\"skill-block\" role=\"tablist\" aria-multiselectable=\"true\">
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\" role=\"tab\" id=\"headingOne\">
            <h4 class=\"panel-title\">
                <a role=\"button\" data-toggle=\"collapse\" data-parent=\"#skill-block\" href=\"#skillList\" aria-expanded=\"true\" aria-controls=\"skillList\">
                    ";
        // line 11
        echo get_lang("Skills");
        echo "
                </a>
                <div class=\"btn-group pull-right\">
                    <a class=\"btn btn-xs btn-default dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">
                        <span class=\"caret\"></span>
                    </a>
                    <ul class=\"dropdown-menu\">
                        ";
        // line 18
        if ((isset($context["show_skills_report_link"]) ? $context["show_skills_report_link"] : null)) {
            // line 19
            echo "                            <li>
                                <a href=\"";
            // line 20
            echo ($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "social/my_skills_report.php");
            echo "\"> ";
            echo get_lang("SkillsReport");
            echo "</a>
                            </li>
                        ";
        }
        // line 23
        echo "                        <li>
                            <a href=\"";
        // line 24
        echo ($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "social/skills_wheel.php");
        echo "\"> ";
        echo get_lang("SkillsWheel");
        echo "</a>
                        </li>
                        <li>
                            <a href=\"";
        // line 27
        echo ($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "social/skills_ranking.php");
        echo "\"> ";
        echo sprintf(get_lang("YourSkillRankingX"), (isset($context["ranking"]) ? $context["ranking"] : null));
        echo "</a>
                        </li>
                    </ul>
                </div>
            </h4>
        </div>
        <div id=\"skillList\" class=\"panel-collapse collapse in\" role=\"tabpanel\" aria-labelledby=\"headingOne\">
            <div class=\"panel-body\">
                ";
        // line 35
        if ((isset($context["skills"]) ? $context["skills"] : null)) {
            // line 36
            echo "                    <div class=\"scrollbar-inner badges-sidebar\">
                        <ul class=\"list-unstyled list-badges\">
                            ";
            // line 38
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["skills"]) ? $context["skills"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["skill"]) {
                // line 39
                echo "                                <li class=\"thumbnail\">
                                    <a href=\"";
                // line 40
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web", array());
                echo "skill/";
                echo $this->getAttribute($context["skill"], "id", array());
                echo "/user/";
                echo (isset($context["user_id"]) ? $context["user_id"] : null);
                echo "\" target=\"_blank\">
                                        <img title=\"";
                // line 41
                echo $this->getAttribute($context["skill"], "name", array());
                echo "\" class=\"img-responsive\" src=\"";
                echo (($this->getAttribute($context["skill"], "icon", array())) ? ($this->getAttribute($context["skill"], "web_icon_thumb_path", array())) : (Template::get_icon_path("badges-default.png", 64)));
                echo "\" width=\"64\" height=\"64\" alt=\"";
                echo $this->getAttribute($context["skill"], "name", array());
                echo "\">
                                        <div class=\"caption\">
                                            <p class=\"text-center\">";
                // line 43
                echo $this->getAttribute($context["skill"], "name", array());
                echo "</p>
                                        </div>
                                    </a>
                                </li>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['skill'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 48
            echo "                        </ul>
                    </div>
                ";
        } else {
            // line 51
            echo "                    <p>";
            echo get_lang("WithoutAchievedSkills");
            echo "</p>
                    <p>
                        <a href=\"";
            // line 53
            echo ($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array()) . "social/skills_wheel.php");
            echo "\">";
            echo get_lang("SkillsWheel");
            echo "</a>
                    </p>
                ";
        }
        // line 56
        echo "            </div>
        </div>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "default/social/skills_block.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  138 => 56,  130 => 53,  124 => 51,  119 => 48,  108 => 43,  99 => 41,  91 => 40,  88 => 39,  84 => 38,  80 => 36,  78 => 35,  65 => 27,  57 => 24,  54 => 23,  46 => 20,  43 => 19,  41 => 18,  31 => 11,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
