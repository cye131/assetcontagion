<?php

/* updatetagscorrel.html */
class __TwigTemplate_177deded1185abd80afdd552083fc22b83a3ed5001a6e75b91c3b561dd86b901 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "updatetagscorrel.html", 1);
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
    }

    // line 6
    public function block_content($context, array $blocks = array())
    {
        // line 7
        echo "
    <section class=\"container\">
    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"stock\" >Stock Ticker:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"stock\" value=\"\" placeholder=\"e.g., AAPL\" title=\"Test\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>        
    </section>
    
    <section class=\"container\">
        <div class=\"container\" id=\"info\">
        </div>
    </section>
    
";
    }

    public function getTemplateName()
    {
        return "updatetagscorrel.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 7,  38 => 6,  33 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
{% endblock %}

{% block content %}

    <section class=\"container\">
    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"stock\" >Stock Ticker:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"stock\" value=\"\" placeholder=\"e.g., AAPL\" title=\"Test\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>        
    </section>
    
    <section class=\"container\">
        <div class=\"container\" id=\"info\">
        </div>
    </section>
    
{% endblock %}", "updatetagscorrel.html", "/var/www/correlation/public_html/templates/updatetagscorrel.html");
    }
}
