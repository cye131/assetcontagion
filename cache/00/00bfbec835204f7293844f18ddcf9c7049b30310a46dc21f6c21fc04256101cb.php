<?php

/* regions.html */
class __TwigTemplate_872a55b1076dfeb6b7b454662dfd4c2afd29e830724cfae80e0a77b5559d3eec extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout-fincontagion.html", "regions.html", 1);
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
        return "regions.html";
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
", "regions.html", "/var/www/correlation/public_html/templates/regions.html");
    }
}
