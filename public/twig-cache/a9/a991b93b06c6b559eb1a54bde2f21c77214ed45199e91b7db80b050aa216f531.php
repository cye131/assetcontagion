<?php

/* contagion.html */
class __TwigTemplate_ca60c4baf181fea450c93e5223e402b9da1940527f8a9156022b79177ee5153c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "contagion.html", 1);
        $this->blocks = array(
            'staticlinks' => array($this, 'block_staticlinks'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_staticlinks($context, array $blocks = array())
    {
        // line 4
        echo "<script src=\"//code.highcharts.com/stock/indicators/indicators.js\"></script>
<script src=\"//code.highcharts.com/stock/indicators/ema.js\"></script>
<script src=\"//code.highcharts.com/stock/indicators/bollinger-bands.js\"></script>
";
    }

    // line 9
    public function block_content($context, array $blocks = array())
    {
        // line 10
        echo "
<section class=\"container\">
    
    <div class=\"bs-callout bs-callout-primary bg-white\">
        <h5 class=\"text-primary\">Financial Contagion Index</h5>
        <h6 class=\"font-italic mt-3\">What is financial contagion?</h6>
        <p>Financial contagion is the situation where a shock to the price of an asset spreads out to other regions or asset classes. </p>
        <p>For example, in 2008, the downturn in the U.S. subprime mortgage market triggered the bankruptcy of Lehman Brothers, in turn leading to the collapses of its insurers and other major financial institutions. Banks and investors were forced to sell their assets at firesale prices, further crashing the prices of risky securities and and leading to a global plunge in equity prices.</p>
        <p>Due to the increasing interdependence of the global economy and the increased ability of investors to invest into foreign markets and non-traditional asset classes, financial contagion has continued to rise over time.</p>
        <h6 class=\"font-italic mt-3\">The mission of this website</h6>
        <p>This website provides an index that tracks financial contagion and its changes over time, by aggregating the cross-asset correlations between different regions and between different asset classes. My goal is to create a better understanding of the following the general risk level in the world economy to idiosyncratic shocks.</p>
        <h6 class=\"font-italic mt-3\">Methodology</h6>
        <p>Under Construction</p>
    </div>
    
    <div class=\"bs-callout bs-callout-info bg-white\">
        <h5 class=\"text-primary\">Stylized Facts</h5>
        <h6 class=\"font-italic mt-3\">What is financial contagion?</h6>
        <p>Financial contagion is the situation where a shock to the price of an asset spreads out to other regions or asset classes. </p>
        <p>For example, in 2008, the downturn in the U.S. subprime mortgage market triggered the bankruptcy of Lehman Brothers, in turn leading to the collapses of its insurers and other major financial institutions. Banks and investors were forced to sell their assets at firesale prices, further crashing the prices of risky securities and and leading to a global plunge in equity prices.
    </div>
    
</section>


<section class=\"container\">
    <div id=\"chart-fci\"></div>
    
    
    
</section>





";
    }

    public function getTemplateName()
    {
        return "contagion.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 10,  43 => 9,  36 => 4,  33 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
<script src=\"//code.highcharts.com/stock/indicators/indicators.js\"></script>
<script src=\"//code.highcharts.com/stock/indicators/ema.js\"></script>
<script src=\"//code.highcharts.com/stock/indicators/bollinger-bands.js\"></script>
{% endblock %}

{% block content %}

<section class=\"container\">
    
    <div class=\"bs-callout bs-callout-primary bg-white\">
        <h5 class=\"text-primary\">Financial Contagion Index</h5>
        <h6 class=\"font-italic mt-3\">What is financial contagion?</h6>
        <p>Financial contagion is the situation where a shock to the price of an asset spreads out to other regions or asset classes. </p>
        <p>For example, in 2008, the downturn in the U.S. subprime mortgage market triggered the bankruptcy of Lehman Brothers, in turn leading to the collapses of its insurers and other major financial institutions. Banks and investors were forced to sell their assets at firesale prices, further crashing the prices of risky securities and and leading to a global plunge in equity prices.</p>
        <p>Due to the increasing interdependence of the global economy and the increased ability of investors to invest into foreign markets and non-traditional asset classes, financial contagion has continued to rise over time.</p>
        <h6 class=\"font-italic mt-3\">The mission of this website</h6>
        <p>This website provides an index that tracks financial contagion and its changes over time, by aggregating the cross-asset correlations between different regions and between different asset classes. My goal is to create a better understanding of the following the general risk level in the world economy to idiosyncratic shocks.</p>
        <h6 class=\"font-italic mt-3\">Methodology</h6>
        <p>Under Construction</p>
    </div>
    
    <div class=\"bs-callout bs-callout-info bg-white\">
        <h5 class=\"text-primary\">Stylized Facts</h5>
        <h6 class=\"font-italic mt-3\">What is financial contagion?</h6>
        <p>Financial contagion is the situation where a shock to the price of an asset spreads out to other regions or asset classes. </p>
        <p>For example, in 2008, the downturn in the U.S. subprime mortgage market triggered the bankruptcy of Lehman Brothers, in turn leading to the collapses of its insurers and other major financial institutions. Banks and investors were forced to sell their assets at firesale prices, further crashing the prices of risky securities and and leading to a global plunge in equity prices.
    </div>
    
</section>


<section class=\"container\">
    <div id=\"chart-fci\"></div>
    
    
    
</section>





{% endblock %}", "contagion.html", "/var/www/contagion/public/templates/contagion.html");
    }
}
