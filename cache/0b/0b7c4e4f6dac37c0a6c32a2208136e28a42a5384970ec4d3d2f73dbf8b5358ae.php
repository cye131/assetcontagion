<?php

/* index.html */
class __TwigTemplate_4f256351b113a16258f9b2e5b4fb0ce2f0c4e7ebb8a0d74d52f3968fe34cac0f extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout-correlation.html", "index.html", 1);
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
        return "index.html";
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
", "index.html", "/var/www/correlation/public_html/templates/index.html");
    }
}
