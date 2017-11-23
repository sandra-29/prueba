<?php

/* default/layout/menu.tpl */
class __TwigTemplate_59497513c4321b48802183fe124147dca25547311af01dbab865c0c327a1543d extends Twig_Template
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
        echo "<nav id=\"menubar\" class=\"navbar navbar-default\">
    <div class=\"container\">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class=\"navbar-header\">
            <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#menuone\" aria-expanded=\"false\">
                <span class=\"sr-only\">Toggle navigation</span>
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
            </button>
        </div>
        <div class=\"collapse navbar-collapse\" id=\"menuone\">
            <ul class=\"nav navbar-nav\">
                ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["menu"]) ? $context["menu"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 15
            echo "                    <li class=\"";
            echo $this->getAttribute($context["item"], "current", array());
            echo "\"><a href=\"";
            echo $this->getAttribute($context["item"], "url", array());
            echo "\">";
            echo $this->getAttribute($context["item"], "title", array());
            echo "</a></li>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "            </ul>
           ";
        // line 18
        if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "logged", array()) == 1)) {
            // line 19
            echo "           <ul class=\"nav navbar-nav navbar-right\">
               ";
            // line 20
            if ( !(null === (isset($context["user_notifications"]) ? $context["user_notifications"] : null))) {
                // line 21
                echo "               <li><a href=\"";
                echo (isset($context["message_url"]) ? $context["message_url"] : null);
                echo "\">";
                echo (isset($context["user_notifications"]) ? $context["user_notifications"] : null);
                echo "</a></li>
               ";
            }
            // line 23
            echo "               ";
            if (($this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "status", array()) != 6)) {
                // line 24
                echo "                <li class=\"dropdown avatar-user\">
                    <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">
                        <img class=\"img-circle\" src=\"";
                // line 26
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "avatar_small", array());
                echo "\" alt=\"";
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "complete_name", array());
                echo "\" />  <span class=\"caret\"></span>
                    </a>
                    <ul class=\"dropdown-menu\" role=\"menu\">
                        <li class=\"user-header\">
                            <div class=\"text-center\">
                            <img class=\"img-circle\" src=\"";
                // line 31
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "avatar_medium", array());
                echo "\" alt=\"";
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "complete_name", array());
                echo "\" />
                            <p class=\"name\"><a href=\"";
                // line 32
                echo (isset($context["profile_url"]) ? $context["profile_url"] : null);
                echo "\">";
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "complete_name", array());
                echo "</a></p>
                            <p><i class=\"fa fa-envelope-o\" aria-hidden=\"true\"></i> ";
                // line 33
                echo $this->getAttribute((isset($context["_u"]) ? $context["_u"] : null), "email", array());
                echo "</p>
                            </div>
                        </li>
                        <li role=\"separator\" class=\"divider\"></li>
                        <li class=\"user-body\">
                            <a title=\"";
                // line 38
                echo get_lang("Inbox");
                echo "\" href=\"";
                echo (isset($context["message_url"]) ? $context["message_url"] : null);
                echo "\">
                                <em class=\"fa fa-envelope\" aria-hidden=\"true\"></em> ";
                // line 39
                echo get_lang("Inbox");
                echo "
                            </a>
                            <a title=\"";
                // line 41
                echo get_lang("MyCertificates");
                echo "\" href=\"";
                echo (isset($context["certificate_url"]) ? $context["certificate_url"] : null);
                echo "\">
                                <em class=\"fa fa-graduation-cap\" aria-hidden=\"true\"></em> ";
                // line 42
                echo get_lang("MyCertificates");
                echo "
                            </a>
                            <a id=\"logout_button\" title=\"";
                // line 44
                echo get_lang("Logout");
                echo "\" href=\"";
                echo (isset($context["logout_link"]) ? $context["logout_link"] : null);
                echo "\" >
                                <em class=\"fa fa-sign-out\"></em> ";
                // line 45
                echo get_lang("Logout");
                echo "
                            </a>
                        </li>
                    </ul>
                </li>
               ";
            }
            // line 51
            echo "            </ul>
            ";
        }
        // line 53
        echo "        </div>
    </div>
</nav>
";
    }

    public function getTemplateName()
    {
        return "default/layout/menu.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  147 => 53,  143 => 51,  134 => 45,  128 => 44,  123 => 42,  117 => 41,  112 => 39,  106 => 38,  98 => 33,  92 => 32,  86 => 31,  76 => 26,  72 => 24,  69 => 23,  61 => 21,  59 => 20,  56 => 19,  54 => 18,  51 => 17,  38 => 15,  34 => 14,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
