<?php

/* default/layout/hot_courses.tpl */
class __TwigTemplate_dccb10cc3eab9b72923ed4516ddaf607a549f172195bac81e91c8d345e40b959 extends Twig_Template
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
        if (( !(null === (isset($context["hot_courses"]) ? $context["hot_courses"] : null)) &&  !twig_test_empty((isset($context["hot_courses"]) ? $context["hot_courses"] : null)))) {
            // line 2
            echo "
<script>
\$(document).ready( function() {
    \$('.star-rating li a').on('click', function(event) {
        var id = \$(this).parents('ul').attr('id');
        \$('#vote_label2_' + id).html(\"";
            // line 7
            echo get_lang("Loading");
            echo "\");
        \$.ajax({
            url: \$(this).attr('data-link'),
            success: function(data) {
                \$(\"#rating_wrapper_\"+id).html(data);
                if (data == 'added') {
                    //\$('#vote_label2_' + id).html(\"";
            // line 13
            echo get_lang("Saved");
            echo "\");
                }
                if (data == 'updated') {
                    //\$('#vote_label2_' + id).html(\"";
            // line 16
            echo get_lang("Saved");
            echo "\");
                }
            }
        });
    });
});
</script>
<section class=\"hot-courses\">
    <div class=\"hot-course-head\">
        <h4 class=\"hot-course-title\">
            ";
            // line 26
            echo get_lang("HottestCourses");
            echo "
            ";
            // line 27
            if ($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array())) {
                // line 28
                echo "            <span class=\"pull-right\">
                <a title=\"";
                // line 29
                echo get_lang("Hide");
                echo "\" alt=\"";
                echo get_lang("Hide");
                echo "\" href=\"";
                echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
                echo "admin/settings.php?search_field=show_hot_courses&submit_button=&_qf__search_settings=&category=search_setting\">
                    <i class=\"fa fa-eye\" aria-hidden=\"true\"></i>
                </a>
            </span>
            ";
            }
            // line 34
            echo "        </h4>
    </div>
    <div class=\"grid-courses\">
        <div class=\"row\">
            ";
            // line 38
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/hot_course_item.tpl"), "default/layout/hot_courses.tpl", 38)->display($context);
            // line 39
            echo "        </div>
    </div>
</section>
";
        }
    }

    public function getTemplateName()
    {
        return "default/layout/hot_courses.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 39,  83 => 38,  77 => 34,  65 => 29,  62 => 28,  60 => 27,  56 => 26,  43 => 16,  37 => 13,  28 => 7,  21 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
