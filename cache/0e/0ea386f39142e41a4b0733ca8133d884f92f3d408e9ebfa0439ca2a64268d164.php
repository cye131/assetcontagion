<?php

/* layout-fincontagion.html */
class __TwigTemplate_5c7f26fe2039aa74a91bee810ee0b60174226710d272904ce8c2f8a334eb9e49 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "layout-fincontagion.html", 1);
        $this->blocks = array(
            'staticlinks' => array($this, 'block_staticlinks'),
            'content' => array($this, 'block_content'),
            'description' => array($this, 'block_description'),
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
        echo "<script src=\"//code.highcharts.com/stock/highstock.js\"></script>
<script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

<script src=\"//cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js\"></script>
<script src=\"//code.highcharts.com/maps/modules/map.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/world-robinson.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/europe.js\"></script>

<script src=\"static/script-fincontagion.js\"></script>
<script src=\"static/mapGenerator.js\"></script>

";
    }

    // line 17
    public function block_content($context, array $blocks = array())
    {
        // line 18
        echo "    <div class=\"overlay\">
    </div>
    
    <section class=\"container\">
        ";
        // line 22
        $this->displayBlock('description', $context, $blocks);
        // line 23
        echo "    </section>
    
    
    <section class=\"container\" id=\"spinnercontainer\" style=\"\">
        <div class=\"row\">
            <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\">Loading data and making graphs...</h4></div>
        </div>
        <div class=\"row\">
            <div class=\"sk-circle\">
                <div class=\"sk-circle1 sk-child\"></div>
                <div class=\"sk-circle2 sk-child\"></div>
                <div class=\"sk-circle3 sk-child\"></div>
                <div class=\"sk-circle4 sk-child\"></div>
                <div class=\"sk-circle5 sk-child\"></div>
                <div class=\"sk-circle6 sk-child\"></div>
                <div class=\"sk-circle7 sk-child\"></div>
                <div class=\"sk-circle8 sk-child\"></div>
                <div class=\"sk-circle9 sk-child\"></div>
                <div class=\"sk-circle10 sk-child\"></div>
                <div class=\"sk-circle11 sk-child\"></div>
                <div class=\"sk-circle12 sk-child\"></div>
            </div>
        </div>
    </section>
    
    <section class=\"container\" id=\"resultscontainer\">
        <ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">
            
          <li class=\"nav-item\">
            <a class=\"nav-link active\" data-toggle=\"tab\" href=\"#heatmaptab\" role=\"tab\" aria-selected=\"true\">Matrix</a>
          </li>

            
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab1\" role=\"tab\" aria-controls=\"tab1\" aria-selected=\"true\">Stock-to-Industry Correlation</a>
          </li>
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab2\" role=\"tab\" aria-controls=\"tab2\" aria-selected=\"false\">Stock-to-Sector Correlation</a>
          </li>
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab3\" role=\"tab\" aria-controls=\"tab3\" aria-selected=\"false\">Stock-to-Market Correlation</a>
          </li>
        </ul>
        
        <div class=\"tab-content\" id=\"myTabContent\">
          <div class=\"tab-pane fade show active\" id=\"heatmaptab\" role=\"tabpanel\">
            <div class=\"container\">    
                <div class=\"row\">
                    <div class=\"col-lg-12\">
                        <div id=\"heatmap\"></div>
                    </div>
                </div>
            </div>  
          </div>

            
          <div class=\"tab-pane fade\" id=\"tab1\" role=\"tabpanel\" aria-labelledby=\"tab-1\">
            <div id=\"chart_1\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab2\" role=\"tabpanel\" aria-labelledby=\"tab-2\">
            <div id=\"chart_2\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab3\" role=\"tabpanel\" aria-labelledby=\"tab-3\">
            <div id=\"chart_3\" class=\"chart\"></div>
          </div>
        </div>
    </section>
    
    <section class=\"container\">
        <form class=\"form-inline\">
            <div class = \"form-group\">

                <label for=\"showLines\" >Draw connections between closely correlated countries?</label>
                <input type=\"checkbox\" checked=\"checked\" name=\"showLines\" id=\"showLines\">
            </div>
        </form>
        
        <form class=\"form-inline\">
            <div class = \"form-group\">

                <label for=\"minLines\" >Minimum correlation (0 to 1):</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"minLines\" value=\"0.80\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submitLines\" >Submit</button>
                <div id=\"errormessageLines\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>


        <div class=\"row\">
            <div class=\"col-lg-12\" id=\"highMap\"></div>
        </div>

        
        <div class=\"row\">
            <div></div>
            <div class=\"col-lg-12 float-right\" id=\"highMapEurope\"></div>
        </div>
    </section>

    
    ";
    }

    // line 22
    public function block_description($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "layout-fincontagion.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  167 => 22,  63 => 23,  61 => 22,  55 => 18,  52 => 17,  37 => 4,  34 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
<script src=\"//code.highcharts.com/stock/highstock.js\"></script>
<script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

<script src=\"//cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js\"></script>
<script src=\"//code.highcharts.com/maps/modules/map.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/world-robinson.js\"></script>
<script src=\"//code.highcharts.com/mapdata/custom/europe.js\"></script>

<script src=\"static/script-fincontagion.js\"></script>
<script src=\"static/mapGenerator.js\"></script>

{% endblock %}

{% block content %}
    <div class=\"overlay\">
    </div>
    
    <section class=\"container\">
        {% block description %}{% endblock %}
    </section>
    
    
    <section class=\"container\" id=\"spinnercontainer\" style=\"\">
        <div class=\"row\">
            <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\">Loading data and making graphs...</h4></div>
        </div>
        <div class=\"row\">
            <div class=\"sk-circle\">
                <div class=\"sk-circle1 sk-child\"></div>
                <div class=\"sk-circle2 sk-child\"></div>
                <div class=\"sk-circle3 sk-child\"></div>
                <div class=\"sk-circle4 sk-child\"></div>
                <div class=\"sk-circle5 sk-child\"></div>
                <div class=\"sk-circle6 sk-child\"></div>
                <div class=\"sk-circle7 sk-child\"></div>
                <div class=\"sk-circle8 sk-child\"></div>
                <div class=\"sk-circle9 sk-child\"></div>
                <div class=\"sk-circle10 sk-child\"></div>
                <div class=\"sk-circle11 sk-child\"></div>
                <div class=\"sk-circle12 sk-child\"></div>
            </div>
        </div>
    </section>
    
    <section class=\"container\" id=\"resultscontainer\">
        <ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">
            
          <li class=\"nav-item\">
            <a class=\"nav-link active\" data-toggle=\"tab\" href=\"#heatmaptab\" role=\"tab\" aria-selected=\"true\">Matrix</a>
          </li>

            
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab1\" role=\"tab\" aria-controls=\"tab1\" aria-selected=\"true\">Stock-to-Industry Correlation</a>
          </li>
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab2\" role=\"tab\" aria-controls=\"tab2\" aria-selected=\"false\">Stock-to-Sector Correlation</a>
          </li>
          <li class=\"nav-item\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tab3\" role=\"tab\" aria-controls=\"tab3\" aria-selected=\"false\">Stock-to-Market Correlation</a>
          </li>
        </ul>
        
        <div class=\"tab-content\" id=\"myTabContent\">
          <div class=\"tab-pane fade show active\" id=\"heatmaptab\" role=\"tabpanel\">
            <div class=\"container\">    
                <div class=\"row\">
                    <div class=\"col-lg-12\">
                        <div id=\"heatmap\"></div>
                    </div>
                </div>
            </div>  
          </div>

            
          <div class=\"tab-pane fade\" id=\"tab1\" role=\"tabpanel\" aria-labelledby=\"tab-1\">
            <div id=\"chart_1\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab2\" role=\"tabpanel\" aria-labelledby=\"tab-2\">
            <div id=\"chart_2\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab3\" role=\"tabpanel\" aria-labelledby=\"tab-3\">
            <div id=\"chart_3\" class=\"chart\"></div>
          </div>
        </div>
    </section>
    
    <section class=\"container\">
        <form class=\"form-inline\">
            <div class = \"form-group\">

                <label for=\"showLines\" >Draw connections between closely correlated countries?</label>
                <input type=\"checkbox\" checked=\"checked\" name=\"showLines\" id=\"showLines\">
            </div>
        </form>
        
        <form class=\"form-inline\">
            <div class = \"form-group\">

                <label for=\"minLines\" >Minimum correlation (0 to 1):</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"minLines\" value=\"0.80\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submitLines\" >Submit</button>
                <div id=\"errormessageLines\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>


        <div class=\"row\">
            <div class=\"col-lg-12\" id=\"highMap\"></div>
        </div>

        
        <div class=\"row\">
            <div></div>
            <div class=\"col-lg-12 float-right\" id=\"highMapEurope\"></div>
        </div>
    </section>

    
    {% endblock %}", "layout-fincontagion.html", "/var/www/correlation/public_html/templates/layout-fincontagion.html");
    }
}
