<?php

/* stocksectorcorrelation.html */
class __TwigTemplate_76cb9e80934c04594312ba462fdfb95e2c55802cc5ff368385f67e5d179bd1a9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout-correlation.html", "stocksectorcorrelation.html", 1);
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
        return "stocksectorcorrelation.html";
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
", "stocksectorcorrelation.html", "/var/www/correlation/public_html/templates/stocksectorcorrelation.html");
    }
}
