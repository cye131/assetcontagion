<?php

/* regions-map.html */
class __TwigTemplate_b6afc78286dab5bd4acdd4c1200c8dd1b94d7d7faf752353a943daee87ce54d6 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "regions-map.html", 1);
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
        echo "<script src=\"//cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js\"></script>
<script src=\"//code.highcharts.com/maps/modules/map.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/world-robinson.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/europe.js\"></script>

";
    }

    // line 11
    public function block_content($context, array $blocks = array())
    {
        // line 12
        echo "    
<main class=\"container bg-white py-5 px-2\">
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <form class=\"form-inline justify-content-center\" method=\"post\" action=\"\" id=\"corrselector\">
            <div class = \"form-group\">
                <label for=\"freqtrail\" style=\"font-weight:600\" >Data frequency</label>
                <select class=\"form-control form-control-sm\" id=\"freqtrail\"></select>
                <input type=\"hidden\" id=\"freq\" name=\"freq\"></input>
                <input type=\"hidden\" id=\"trail\" name=\"trail\"></input>
            </div>
            <div class = \"form-group\" style=\"margin-left:10px\">
                <label for=\"corr_type\" style=\"font-weight:600\" >Correlation Type</label>
                <select class=\"form-control form-control-sm\" name=\"corr_type\" id=\"corr_type\"></select>
                <button class=\"btn btn-primary btn-sm\" type=\"submit\" id=\"submitcorrselector\" >Change</button>
            </div>
        </form>
    </div></div>
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"showLines\" style=\"font-weight:600\" >Draw connections between closely correlated countries?</label>
                <input type=\"checkbox\" checked=\"checked\" name=\"showLines\" id=\"showLines\">
            </div>
        </form>
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"minLines\"  style=\"font-weight:600\"  >Minimum correlation (0 to 1):</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"minLines\" value=\"0.75\" style=\"max-width:80px\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submitLines\" >Submit</button>
                <div id=\"errormessageLines\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>
    </div></div>
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <div class=\"col-lg-12\" id=\"highMap\"></div>
    </div></div>
                
    <div class=\"row\"><div class=\"col-lg-12\">
        <div class=\"col-lg-12 float-right\" id=\"highMapEurope\"></div>
    </div></div>

    
</main>
    

    
";
    }

    public function getTemplateName()
    {
        return "regions-map.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 12,  45 => 11,  36 => 4,  33 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
<script src=\"//cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js\"></script>
<script src=\"//code.highcharts.com/maps/modules/map.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/world-robinson.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/europe.js\"></script>

{% endblock %}

{% block content %}
    
<main class=\"container bg-white py-5 px-2\">
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <form class=\"form-inline justify-content-center\" method=\"post\" action=\"\" id=\"corrselector\">
            <div class = \"form-group\">
                <label for=\"freqtrail\" style=\"font-weight:600\" >Data frequency</label>
                <select class=\"form-control form-control-sm\" id=\"freqtrail\"></select>
                <input type=\"hidden\" id=\"freq\" name=\"freq\"></input>
                <input type=\"hidden\" id=\"trail\" name=\"trail\"></input>
            </div>
            <div class = \"form-group\" style=\"margin-left:10px\">
                <label for=\"corr_type\" style=\"font-weight:600\" >Correlation Type</label>
                <select class=\"form-control form-control-sm\" name=\"corr_type\" id=\"corr_type\"></select>
                <button class=\"btn btn-primary btn-sm\" type=\"submit\" id=\"submitcorrselector\" >Change</button>
            </div>
        </form>
    </div></div>
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"showLines\" style=\"font-weight:600\" >Draw connections between closely correlated countries?</label>
                <input type=\"checkbox\" checked=\"checked\" name=\"showLines\" id=\"showLines\">
            </div>
        </form>
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"minLines\"  style=\"font-weight:600\"  >Minimum correlation (0 to 1):</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"minLines\" value=\"0.75\" style=\"max-width:80px\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submitLines\" >Submit</button>
                <div id=\"errormessageLines\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>
    </div></div>
    
    <div class=\"row\"><div class=\"col-lg-12\">
        <div class=\"col-lg-12\" id=\"highMap\"></div>
    </div></div>
                
    <div class=\"row\"><div class=\"col-lg-12\">
        <div class=\"col-lg-12 float-right\" id=\"highMapEurope\"></div>
    </div></div>

    
</main>
    

    
{% endblock %}", "regions-map.html", "/var/www/contagion/public/templates/regions-map.html");
    }
}
