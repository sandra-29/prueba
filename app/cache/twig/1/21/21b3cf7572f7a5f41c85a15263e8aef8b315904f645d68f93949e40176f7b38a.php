<?php

/* default/layout/show_footer.tpl */
class __TwigTemplate_e6e06b6c2d5ac8a70daab90a19bb1579ee40e940ae5ddb37291590033bd8b3c0 extends Twig_Template
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
        if (((isset($context["show_footer"]) ? $context["show_footer"] : null) == true)) {
            // line 2
            echo "    </div>
    </section>
    ";
            // line 4
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/page_footer.tpl"), "default/layout/show_footer.tpl", 4)->display($context);
        } else {
            // line 6
            echo "    ";
            $this->loadTemplate(((isset($context["template"]) ? $context["template"] : null) . "/layout/footer.js.tpl"), "default/layout/show_footer.tpl", 6)->display($context);
        }
        // line 8
        echo "    </div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "default/layout/show_footer.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 8,  28 => 6,  25 => 4,  21 => 2,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
