<?php

/* default/admin/teacher_time_report.tpl */
class __TwigTemplate_5580221c5b403fbe764bc9b98d9c9fcda1be88916fcaea44df149e8fd03dba01 extends Twig_Template
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
\$(document).on('ready', function () {
    \$('#course').on('change', function () {
        \$('#session').prop('selectedIndex', 0);
        \$('#teacher').prop('selectedIndex', 0);
    });

    \$('#session').on('change', function () {
        \$('#course').prop('selectedIndex', 0);
        \$('#teacher').prop('selectedIndex', 0);
    });

    \$('#teacher').on('change', function () {
        \$('#course').prop('selectedIndex', 0);
        \$('#session').prop('selectedIndex', 0);
    });

    \$('#daterange').on('apply.daterangepicker', function (ev, picker) {
        \$('[name=\"from\"]').val(picker.startDate.format('YYYY-MM-DD'));
        \$('[name=\"until\"]').val(picker.endDate.format('YYYY-MM-DD'));
    }).on('cancel.daterangepicker', function (ev, picker) {
        \$('#daterange, [name=\"from\"], [name=\"until\"]').val('');
    });
});
</script>

<div class=\"col-md-12\">
    <div class=\"actions\">
        <a href=\"";
        // line 29
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
        echo "admin/teachers_time_by_session_report.php\">
            ";
        // line 30
        echo Template::get_image("session.png", 32, get_lang("Sessions"));
        echo "
        </a>
        <div class=\"pull-right\">
            <a href=\"";
        // line 33
        echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_self", array()) . "?") . twig_urlencode_filter(array("export" => "pdf", "from" => (isset($context["selected_from"]) ? $context["selected_from"] : null), "until" => (isset($context["selected_until"]) ? $context["selected_until"] : null), "course" => (isset($context["selected_course"]) ? $context["selected_course"] : null), "session" => (isset($context["selected_session"]) ? $context["selected_session"] : null), "teacher" => (isset($context["selected_teacher"]) ? $context["selected_teacher"] : null))));
        echo "\">
                ";
        // line 34
        echo Template::get_image("pdf.png", 32, get_lang("ExportToPDF"));
        echo "
            </a>
            <a href=\"";
        // line 36
        echo (($this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_self", array()) . "?") . twig_urlencode_filter(array("export" => "xls", "from" => (isset($context["selected_from"]) ? $context["selected_from"] : null), "until" => (isset($context["selected_until"]) ? $context["selected_until"] : null), "course" => (isset($context["selected_course"]) ? $context["selected_course"] : null), "session" => (isset($context["selected_session"]) ? $context["selected_session"] : null), "teacher" => (isset($context["selected_teacher"]) ? $context["selected_teacher"] : null))));
        echo "\">
                ";
        // line 37
        echo Template::get_image("export_excel.png", 32, get_lang("ExportExcel"));
        echo "
            </a>
        </div>
    </div>
</div>

<h1 class=\"page-header\">";
        // line 43
        echo get_lang("TeacherTimeReport");
        echo "</h1>
";
        // line 44
        echo (isset($context["form"]) ? $context["form"] : null);
        echo "
<h2 class=\"page-header\">";
        // line 45
        echo (isset($context["report_title"]) ? $context["report_title"] : null);
        echo " <small>";
        echo (isset($context["report_sub_title"]) ? $context["report_sub_title"] : null);
        echo "</small></h2>

<table class=\"table\">
    <thead>
        <tr>
            ";
        // line 50
        if ((isset($context["with_filter"]) ? $context["with_filter"] : null)) {
            // line 51
            echo "                <th>";
            echo get_lang("Session");
            echo "</th>
                <th>";
            // line 52
            echo get_lang("Course");
            echo "</th>
            ";
        }
        // line 54
        echo "            <th>";
        echo get_lang("Coach");
        echo "</th>
            <th>";
        // line 55
        echo get_lang("TotalTime");
        echo "</th>
        </tr>
    </thead>
    <tbody>
        ";
        // line 59
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["rows"]) ? $context["rows"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 60
            echo "            <tr>
                ";
            // line 61
            if ((isset($context["with_filter"]) ? $context["with_filter"] : null)) {
                // line 62
                echo "                    <td>";
                echo (($this->getAttribute($context["row"], "session", array())) ? ($this->getAttribute($this->getAttribute($context["row"], "session", array()), "name", array())) : ("&nbsp"));
                echo "</td>
                    <td>";
                // line 63
                echo $this->getAttribute($this->getAttribute($context["row"], "course", array()), "name", array());
                echo "</td>
                ";
            }
            // line 65
            echo "                <td>";
            echo $this->getAttribute($this->getAttribute($context["row"], "coach", array()), "completeName", array());
            echo " (";
            echo $this->getAttribute($this->getAttribute($context["row"], "coach", array()), "username", array());
            echo ")</td>
                <td>";
            // line 66
            echo $this->getAttribute($context["row"], "totalTime", array());
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "    </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "default/admin/teacher_time_report.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 69,  149 => 66,  142 => 65,  137 => 63,  132 => 62,  130 => 61,  127 => 60,  123 => 59,  116 => 55,  111 => 54,  106 => 52,  101 => 51,  99 => 50,  89 => 45,  85 => 44,  81 => 43,  72 => 37,  68 => 36,  63 => 34,  59 => 33,  53 => 30,  49 => 29,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
