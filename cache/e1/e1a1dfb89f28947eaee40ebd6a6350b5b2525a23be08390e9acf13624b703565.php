<?php

/* stocks.html */
class __TwigTemplate_e1468042f3a0a529b9eef8f0f4a2e57fd09f8098e92d50c09460913604d64e88 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout-correlation.html", "stocks.html", 1);
        $this->blocks = array(
            'description' => array($this, 'block_description'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout-correlation.html";
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
        return "stocks.html";
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
        return new Twig_Source("{% extends \"layout-correlation.html\" %}


{% block description %}
<p>Test</p>
{% endblock %}
", "stocks.html", "/var/www/correlation/public_html/templates/stocks.html");
    }
}
