<?php

/* updatehistseries.html */
class __TwigTemplate_73a7adbdf821daf19a96776913c138b786b23fd8e98c9a282c2f1c02f42187c8 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "updatehistseries.html", 1);
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
                <label for=\"category\" >Category:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"category\" value=\"\" placeholder=\"e.g., reg\">
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
            var category = \$(\"#category\").val();
            console.log(category);
                    
            console.log(tagsSeries);
            curlData(0);
                        
        });

        function curlData(i) {    
            \$.ajax({
                url: '/test/update_hist_series.ajax.php',
                type: 'POST',
                data: {ajax: tagsSeries[i]},
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results);
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        \$(\"#info\").append('<br><span>Successfully updated #' + i  + ': ' + tagsSeries[i].name + '(' + tagsSeries[i].freq + ') with ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                    }
                    else {
                        \$(\"#info\").append('<br><span><b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name +  ' | ' + results.info.errorMsg + '</span>');
                    }
                    
                    \$(\"#info\").append(' <a href=\"' + results.info.url + '\">URL</a>');
                    i ++;
                    if (i<tagsSeries.length) curlData(i);
                    
                    //historicalData[i] = JSON.parse(results);
                    //results = JSON.parse(results);
                },
                error:function(e, ts, et){
                        \$(\"#info\").append('<br><span style=\"font-weight:bold\">AJAX ERROR ON #' + i  + ': ' + tagsSeries[i].name +  ' | ' + ts +  ' (s_id: ' + tagsSeries[i].s_id +  ')</span>');
                        i ++;
                        if (i<tagsSeries.length) curlData(i);
                }
            });
        }

    </script>
    
";
    }

    public function getTemplateName()
    {
        return "updatehistseries.html";
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
                <label for=\"category\" >Category:</label>
                <input type=\"text\" class=\"form-control form-control-sm\" id=\"category\" value=\"\" placeholder=\"e.g., reg\">
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
            var category = \$(\"#category\").val();
            console.log(category);
                    
            console.log(tagsSeries);
            curlData(0);
                        
        });

        function curlData(i) {    
            \$.ajax({
                url: '/test/update_hist_series.ajax.php',
                type: 'POST',
                data: {ajax: tagsSeries[i]},
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results);
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        \$(\"#info\").append('<br><span>Successfully updated #' + i  + ': ' + tagsSeries[i].name + '(' + tagsSeries[i].freq + ') with ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                    }
                    else {
                        \$(\"#info\").append('<br><span><b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name +  ' | ' + results.info.errorMsg + '</span>');
                    }
                    
                    \$(\"#info\").append(' <a href=\"' + results.info.url + '\">URL</a>');
                    i ++;
                    if (i<tagsSeries.length) curlData(i);
                    
                    //historicalData[i] = JSON.parse(results);
                    //results = JSON.parse(results);
                },
                error:function(e, ts, et){
                        \$(\"#info\").append('<br><span style=\"font-weight:bold\">AJAX ERROR ON #' + i  + ': ' + tagsSeries[i].name +  ' | ' + ts +  ' (s_id: ' + tagsSeries[i].s_id +  ')</span>');
                        i ++;
                        if (i<tagsSeries.length) curlData(i);
                }
            });
        }

    </script>
    
{% endblock %}", "updatehistseries.html", "/var/www/correlation/public_html/templates/updatehistseries.html");
    }
}
