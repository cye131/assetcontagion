<?php

/* update.html */
class __TwigTemplate_b4de069472ee0647ec59bf52d9cc9420b350a7d0e473caea64ec7b708a6701f1 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "update.html", 1);
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

    <script>
        
        
        \$(\"#update\").click(function(){
            historicalData = [];
            i = 0;
            curlData(0);/*
            tagsSeries.forEach(element => {
                //console.log(element);console.log(i);
                curlData(i);
                i++;
            });*/
            console.log(historicalData);
        });

        function curlData(i) {    
            \$.ajax({
                url: '/test/update_hist_series.ajax.php',
                type: 'POST',
                data: {ajax: tagsSeries[i]},
                dataType: 'html',
                cache: false,
                timeout: 20000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results);
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        \$(\"#info\").append('<br><span>Successfully updated #' + i  + ': ' + tagsSeries[i].name + ' with ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                    }
                    else {
                        \$(\"#info\").append('<br><span style=\"font-weight:bold\">Failed to update #' + i  + ': ' + tagsSeries[i].name + '| Error Message: ' + results.info. errorMsg + '</span>');
                    }
                    i ++;
                    if (i<tagsSeries.length) curlData(i);
                    
                    //historicalData[i] = JSON.parse(results);
                    //results = JSON.parse(results);
                },
                error:function(){
                        \$(\"#update\").append('<br><span>Failed to update #' + i  + ': ' + tagsSeries[i].name + '</span>');
                }
            });
        }

    </script>
    
";
    }

    public function getTemplateName()
    {
        return "update.html";
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

    <script>
        
        
        \$(\"#update\").click(function(){
            historicalData = [];
            i = 0;
            curlData(0);/*
            tagsSeries.forEach(element => {
                //console.log(element);console.log(i);
                curlData(i);
                i++;
            });*/
            console.log(historicalData);
        });

        function curlData(i) {    
            \$.ajax({
                url: '/test/update_hist_series.ajax.php',
                type: 'POST',
                data: {ajax: tagsSeries[i]},
                dataType: 'html',
                cache: false,
                timeout: 20000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results);
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        \$(\"#info\").append('<br><span>Successfully updated #' + i  + ': ' + tagsSeries[i].name + ' with ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                    }
                    else {
                        \$(\"#info\").append('<br><span style=\"font-weight:bold\">Failed to update #' + i  + ': ' + tagsSeries[i].name + '| Error Message: ' + results.info. errorMsg + '</span>');
                    }
                    i ++;
                    if (i<tagsSeries.length) curlData(i);
                    
                    //historicalData[i] = JSON.parse(results);
                    //results = JSON.parse(results);
                },
                error:function(){
                        \$(\"#update\").append('<br><span>Failed to update #' + i  + ': ' + tagsSeries[i].name + '</span>');
                }
            });
        }

    </script>
    
{% endblock %}", "update.html", "/var/www/correlation/public_html/templates/update.html");
    }
}
