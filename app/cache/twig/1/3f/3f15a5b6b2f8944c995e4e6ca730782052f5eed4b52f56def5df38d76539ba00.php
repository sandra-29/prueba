<?php

/* default/admin/settings_index.tpl */
class __TwigTemplate_45bc4938524d3d5294fcaeca6054b3f55ad09ae11b2301666ff9f7f0dbe072b5 extends Twig_Template
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
\$(document).ready(function() {
    \$.ajax({
        url:'";
        // line 4
        echo (isset($context["web_admin_ajax_url"]) ? $context["web_admin_ajax_url"] : null);
        echo "?a=version',
        success:function(version){
            \$(\".admin-block-version\").html(version);
        }
    });

";
        // line 10
        if ($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array())) {
            // line 11
            echo "    (function(CKEDITOR) {
        CKEDITOR.replace('extra_content');

        var extraContentEditor = CKEDITOR.instances.extra_content;

        \$('a.admin-edit-block').on('click', function(e) {
            e.preventDefault();

            var \$self = \$(this);

            var extraContent = \$.ajax('";
            // line 21
            echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_ajax", array());
            echo "admin.ajax.php', {
                type: 'post',
                data: {
                    a: 'get_extra_content',
                    block: \$self.data('id')
                }
            });

            \$.when(extraContent).done(function(content) {
                extraContentEditor.setData(content);
                \$('#extra-block').val(\$self.data('id'));
                \$('#modal-extra-title').text(\$self.data('label'));

                \$('#modal-extra').modal('show');
            });
        });
    })(window.CKEDITOR);
";
        }
        // line 39
        echo "});
</script>

<section id=\"settings\">
    ";
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["blocks"]) ? $context["blocks"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["block_item"]) {
            // line 44
            echo "        ";
            if ((($this->getAttribute($context["loop"], "index", array()) % 2) == 0)) {
                // line 45
                echo "        <div class=\"row\">
        ";
            }
            // line 47
            echo "
        <div id=\"tabs-";
            // line 48
            echo $this->getAttribute($context["loop"], "index", array());
            echo "\" class=\"col-md-6\">
            <div class=\"panel panel-default ";
            // line 49
            echo $this->getAttribute($context["block_item"], "class", array());
            echo "\">
                <div class=\"panel-heading\">
                    ";
            // line 51
            echo $this->getAttribute($context["block_item"], "icon", array());
            echo " ";
            echo $this->getAttribute($context["block_item"], "label", array());
            echo "
                    ";
            // line 52
            if (($this->getAttribute($context["block_item"], "editable", array()) && $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array()))) {
                // line 53
                echo "                        <a class=\"admin-edit-block pull-right\" href=\"#\" data-label=\"";
                echo $this->getAttribute($context["block_item"], "label", array());
                echo "\" title=\"";
                echo get_lang("Edit");
                echo "\" data-id=\"";
                echo $this->getAttribute($context["block_item"], "class", array());
                echo "\">
                            <img src=\"";
                // line 54
                echo Template::get_icon_path("edit.png", 22);
                echo "\" width=\"22\" height=\"22\" alt=\"";
                echo get_lang("Edit");
                echo "\" title=\"";
                echo get_lang("Edit");
                echo "\" />
                        </a>
                    ";
            }
            // line 57
            echo "                </div>
                <div class=\"panel-body\">
                <div style=\"display: block;\">
                    ";
            // line 60
            echo $this->getAttribute($context["block_item"], "search_form", array());
            echo "
                </div>
                ";
            // line 62
            if ( !(null === $this->getAttribute($context["block_item"], "items", array()))) {
                // line 63
                echo "                    <div class=\"block-items-admin\">
                    <ul class=\"list-items-admin\">
    \t\t    \t";
                // line 65
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["block_item"], "items", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["url"]) {
                    // line 66
                    echo "    \t\t    \t\t<li>
                            <a href=\"";
                    // line 67
                    echo $this->getAttribute($context["url"], "url", array());
                    echo "\">
                                ";
                    // line 68
                    echo $this->getAttribute($context["url"], "label", array());
                    echo "
                            </a>
                        </li>
    \t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['url'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 72
                echo "                    </ul>
                    </div>
                ";
            }
            // line 75
            echo "
                ";
            // line 76
            if ( !(null === $this->getAttribute($context["block_item"], "extra", array()))) {
                // line 77
                echo "                    <div>
                    ";
                // line 78
                echo $this->getAttribute($context["block_item"], "extra", array());
                echo "
                    </div>
                ";
            }
            // line 81
            echo "
                ";
            // line 82
            if ($this->getAttribute($context["block_item"], "extraContent", array())) {
                // line 83
                echo "                    <div>";
                echo $this->getAttribute($context["block_item"], "extraContent", array());
                echo "</div>
                ";
            }
            // line 85
            echo "            </div>
            </div>
        </div>

        ";
            // line 89
            if ((($this->getAttribute($context["loop"], "index", array()) % 2) == 0)) {
                // line 90
                echo "            </div>
        ";
            }
            // line 92
            echo "    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['block_item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 93
        echo "</section>

";
        // line 95
        if ($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "is_admin", array())) {
            // line 96
            echo "    <div class=\"modal fade\" id=\"modal-extra\">
        <div class=\"modal-dialog\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
            // line 100
            echo get_lang("Close");
            echo "\">
                        <span aria-hidden=\"true\">&times;</span>
                    </button>
                    <h4 class=\"modal-title\" id=\"modal-extra-title\">";
            // line 103
            echo get_lang("Blocks");
            echo "</h4>
                </div>
                <div class=\"modal-body\">
                     ";
            // line 106
            echo (isset($context["extraDataForm"]) ? $context["extraDataForm"] : null);
            echo "
                </div>
            </div>
        </div>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "default/admin/settings_index.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  254 => 106,  248 => 103,  242 => 100,  236 => 96,  234 => 95,  230 => 93,  216 => 92,  212 => 90,  210 => 89,  204 => 85,  198 => 83,  196 => 82,  193 => 81,  187 => 78,  184 => 77,  182 => 76,  179 => 75,  174 => 72,  164 => 68,  160 => 67,  157 => 66,  153 => 65,  149 => 63,  147 => 62,  142 => 60,  137 => 57,  127 => 54,  118 => 53,  116 => 52,  110 => 51,  105 => 49,  101 => 48,  98 => 47,  94 => 45,  91 => 44,  74 => 43,  68 => 39,  47 => 21,  35 => 11,  33 => 10,  24 => 4,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
