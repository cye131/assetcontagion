<?php

/* layout-correlation.html */
class __TwigTemplate_fc4feb29211a1a4b1880818c1a7c17e4ee2a08a892d386ef7245193b20985e2a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "layout-correlation.html", 1);
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
        echo "    <script src=\"//code.jquery.com/ui/1.12.1/jquery-ui.min.js\"></script>

    <script src=\"//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js\"></script>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css\"/>

    
    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

    <script src=\"static/script.js\"></script>

";
    }

    // line 21
    public function block_content($context, array $blocks = array())
    {
        // line 22
        echo "
    <section class=\"container\">
        ";
        // line 24
        $this->displayBlock('description', $context, $blocks);
        // line 25
        echo "    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"stock\" >Stock Ticker:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"stock\" value=\"\" placeholder=\"e.g., AAPL\" title=\"Test\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submit\">Submit</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>        
    </section>
    
    <section class=\"container\" id=\"spinnercontainer\" style=\"display:none\">
        <div class=\"row\">
            <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\"><h4></div>
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
                    <div class=\"col-lg-4\">
                        <div class=\"container successmsg\"></div>
                    </div>
                    <div class=\"col-lg-8\">
                        <div id=\"heatmap\"></div>
                    </div>
                </div>
            </div>  
          </div>

            
          <div class=\"tab-pane fade\" id=\"tab1\" role=\"tabpanel\" aria-labelledby=\"tab-1\">
            <div id=\"chart_1\" class=\"chart\"\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab2\" role=\"tabpanel\" aria-labelledby=\"tab-2\">
            <div id=\"chart_2\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab3\" role=\"tabpanel\" aria-labelledby=\"tab-3\">
            <div id=\"chart_3\" class=\"chart\"></div>
          </div>
        </div>
    </section>
    ";
    }

    // line 24
    public function block_description($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "layout-correlation.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  148 => 24,  64 => 25,  62 => 24,  58 => 22,  55 => 21,  37 => 4,  34 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
    <script src=\"//code.jquery.com/ui/1.12.1/jquery-ui.min.js\"></script>

    <script src=\"//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js\"></script>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css\"/>

    
    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

    <script src=\"static/script.js\"></script>

{% endblock %}


{% block content %}

    <section class=\"container\">
        {% block description %}{% endblock %}
    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"stock\" >Stock Ticker:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"stock\" value=\"\" placeholder=\"e.g., AAPL\" title=\"Test\">
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"submit\">Submit</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form>        
    </section>
    
    <section class=\"container\" id=\"spinnercontainer\" style=\"display:none\">
        <div class=\"row\">
            <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\"><h4></div>
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
                    <div class=\"col-lg-4\">
                        <div class=\"container successmsg\"></div>
                    </div>
                    <div class=\"col-lg-8\">
                        <div id=\"heatmap\"></div>
                    </div>
                </div>
            </div>  
          </div>

            
          <div class=\"tab-pane fade\" id=\"tab1\" role=\"tabpanel\" aria-labelledby=\"tab-1\">
            <div id=\"chart_1\" class=\"chart\"\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab2\" role=\"tabpanel\" aria-labelledby=\"tab-2\">
            <div id=\"chart_2\" class=\"chart\"></div>
          </div>
          <div class=\"tab-pane fade\" id=\"tab3\" role=\"tabpanel\" aria-labelledby=\"tab-3\">
            <div id=\"chart_3\" class=\"chart\"></div>
          </div>
        </div>
    </section>
    {% endblock %}", "layout-correlation.html", "/var/www/correlation/public_html/templates/layout-correlation.html");
    }
}
