<?php

/* default/layout/footer.js.tpl */
class __TwigTemplate_b8b7af56478aca4eed1f3b81b0b1a2289d63c2dfaf4ff8ba80f3f38342de7b81 extends Twig_Template
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
    /* Makes row highlighting possible */
    \$(document).ready( function() {
        // Date time settings.
        moment.locale('";
        // line 5
        echo (isset($context["locale"]) ? $context["locale"] : null);
        echo "');
        \$.datepicker.setDefaults(\$.datepicker.regional[\"";
        // line 6
        echo (isset($context["locale"]) ? $context["locale"] : null);
        echo "\"]);
        \$.datepicker.regional[\"local\"] = \$.datepicker.regional[\"";
        // line 7
        echo (isset($context["locale"]) ? $context["locale"] : null);
        echo "\"];

        // Chosen select
        \$(\".chzn-select\").chosen({
            disable_search_threshold: 10,
            no_results_text: '";
        // line 12
        echo twig_escape_filter($this->env, get_lang("SearchNoResultsFound"), "js");
        echo "',
            placeholder_text_multiple: '";
        // line 13
        echo twig_escape_filter($this->env, get_lang("SelectSomeOptions"), "js");
        echo "',
            placeholder_text_single: '";
        // line 14
        echo twig_escape_filter($this->env, get_lang("SelectAnOption"), "js");
        echo "',
            width: \"100%\"
        });

        // Bootstrap tabs.
        \$('.tab-wrapper a').click(function (e) {
            e.preventDefault();
            \$(this).tab('show');

            //\$('#tabs a:first').tab('show') // Select first tab
        });

        // Fixes bug when loading links inside a tab.
        \$('.tab-wrapper .tab-pane a').unbind();

        /**
         * Advanced options
         * Usage
         * <a id=\"link\" href=\"url\">Advanced</a>
         * <div id=\"link_options\">
         *     hidden content :)
         * </div>
         * */
        \$(\".advanced_options\").on(\"click\", function (event) {
            event.preventDefault();
            var id = \$(this).attr('id') + '_options';
            var button = \$(this);
            button.toggleClass('active');
            \$(\"#\" + id).toggle();
        });

        /**
         * <a class=\"advanced_options_open\" href=\"http://\" rel=\"div_id\">Open</a>
         * <a class=\"advanced_options_close\" href=\"http://\" rel=\"div_id\">Close</a>
         * <div id=\"div_id\">Div content</div>
         * */
        \$(\".advanced_options_open\").on(\"click\", function (event) {
            event.preventDefault();
            var id = \$(this).attr('rel');
            \$(\"#\" + id).show();
        });

        \$(\".advanced_options_close\").on(\"click\", function (event) {
            event.preventDefault();
            var id = \$(this).attr('rel');
            \$(\"#\" + id).hide();
        });

        // Adv multi-select search input.
        \$('.select_class_filter').each( function () {
            var inputId = \$(this).attr('id');
            inputId = inputId.replace('-filter', '');
            \$(\"#\" + inputId).filterByText(\$(\"#\" + inputId + \"-filter\"));
        });

        \$(\".jp-jplayer audio\").addClass('skip');

        // Mediaelement
        if ( ";
        // line 72
        echo (isset($context["show_media_element"]) ? $context["show_media_element"] : null);
        echo " == 1) {
            jQuery('video:not(.skip), audio:not(.skip)').mediaelementplayer(/* Options */);
        }

        // Table highlight.
        \$(\"form .data_table input:checkbox\").click(function () {
            if (\$(this).is(\":checked\")) {
                \$(this).parentsUntil(\"tr\").parent().addClass(\"row_selected\");
            } else {
                \$(this).parentsUntil(\"tr\").parent().removeClass(\"row_selected\");
            }
        });

        /* For non HTML5 browsers */
        if (\$(\"#formLogin\".length > 1)) {
            \$(\"input[name=login]\").focus();
        }

        // Tool tip (in exercises)
        var tip_options = {
            placement: 'right'
        };
        \$('.boot-tooltip').tooltip(tip_options);
        var more = '";
        // line 95
        echo twig_escape_filter($this->env, get_lang("SeeMore"), "js");
        echo "';
        var close = '";
        // line 96
        echo twig_escape_filter($this->env, get_lang("Close"), "js");
        echo "';
        \$('.list-teachers').readmore({
            speed: 75,
            moreLink: '<a href=\"#\">' + more + '</a>',
            lessLink: '<a href=\"#\">' + close + '</a>',
            collapsedHeight: 35,
            blockCSS: 'display: block; width: 100%;'
        });
    });
</script>
";
    }

    public function getTemplateName()
    {
        return "default/layout/footer.js.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  140 => 96,  136 => 95,  110 => 72,  49 => 14,  45 => 13,  41 => 12,  33 => 7,  29 => 6,  25 => 5,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
