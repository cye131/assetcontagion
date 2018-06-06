<?php

/* financialcontagion.html */
class __TwigTemplate_cf4d1ebf32c5c5a37230c5a1c064dab7e0a7df7650e5a277eec0574f7005a6d9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout-fincontagion.html", "financialcontagion.html", 1);
        $this->blocks = array(
            'description' => array($this, 'block_description'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout-fincontagion.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_description($context, array $blocks = array())
    {
        // line 5
        echo "<p>Test</p>
";
    }

    public function getTemplateName()
    {
        return "financialcontagion.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 5,  32 => 4,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout-fincontagion.html\" %}


{% block description %}
<p>Test</p>
{% endblock %}
", "financialcontagion.html", "/var/www/correlation/public_html/templates/financialcontagion.html");
    }
}
