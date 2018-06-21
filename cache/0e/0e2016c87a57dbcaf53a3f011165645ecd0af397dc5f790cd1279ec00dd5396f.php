<?php

/* updatehistcorrel.html */
class __TwigTemplate_bf26b55b0cf4d5a89835611b47e2e439983c7691db325f713eba15439bdfd816 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "updatehistcorrel.html", 1);
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
        <div class=\"container\" style=\"max-height:400px;overflow:auto\">
            <span id=\"expectedrows\"></span>
            <table id=\"table1\" style=\"width:100%;text-align:center;\">
                <thead>
                    <tr>
                        <th>s_corr_id</th>
                        <th>category</th>
                        <th>freq</th>
                        <th>trail</th>
                        <th>b_id_1 (name_1)</th>
                        <th>b_id_2 (name_2)</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>obs_end_val</th>
                        <th>last_updated</th>

                        <th>error message/rows updated</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"container\">
        <div class=\"container\" id=\"info\">
        </div>
    </section>

    <script>
        \$(document).ready(function() {
            for (i=0;i<tagsCorrel.length;i++) {
                \$('#table1').append('<tr id=\"' + tagsCorrel[i].s_corr_id + '\"><td>' + tagsCorrel[i].s_corr_id + '</td><td>' + tagsCorrel[i].category + '</td><td>' + tagsCorrel[i].freq + '</td><td>' + tagsCorrel[i].trail + '</td><td>' + tagsCorrel[i].b_id_1 + ' ' + tagsCorrel[i].name_1 + '</td><td>' + tagsCorrel[i].b_id_2 + ' ' + tagsCorrel[i].name_2 + '</td><td> ' + tagsCorrel[i].obs_start +  '</td><td>' + tagsCorrel[i].obs_end + '</td><td>' + tagsCorrel[i].obs_end_val + '</td><td>' + tagsCorrel[i].last_updated + '</td><td></td></tr>');
            }
        });

        \$(\"#update\").click(function(){
            var category = \$(\"#category\").val();
            console.log(category);
                    
            console.log(tagsCorrel);
            calcCorrel(0);

        });

        function calcCorrel(i) {
            var model = [];
            model[0] = 'update_hist_correl';
            toScript = ['uHistCorrel'];

            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: model,
                    toScript: toScript,
                    fromAjax: {corrTag: tagsCorrel[i]}
                },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results).uHistCorrel;
                    console.log(results);
                    console.log(tagsCorrel[i].s_corr_nid);
                    
                    
                    if (results.info.insertedHistData === true) {
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<span>Successfully updated #' + i  + ': ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(7)').html(results.info.firstDate);
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(8)').html(results.info.lastDate);
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(9)').html(results.info.lastFirstInput);

                    }
                    else {
                        console.log('#' + tagsCorrel[i].s_corr_nid + ' td:nth-child(11)');
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<span><b>Failed</b> to update #' + i  + ': '  + results.info.errorMsg + '</span>');
                    }
                    
                    i ++;
                    if (i<tagsCorrel.length) calcCorrel(i);
                },
                error:function(e, ts, et){
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<br><span style=\"font-weight:bold\">AJAX ERROR ON #' + i  + ': ' + tagsCorrel[i].s_corr_nid +  ' | ' + ts +  '</span>');
                        i ++;
                        if (i<tagsCorrel.length) calcCorrel(i);
                }
            });
        }

    </script>
    
";
    }

    public function getTemplateName()
    {
        return "updatehistcorrel.html";
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
        <div class=\"container\" style=\"max-height:400px;overflow:auto\">
            <span id=\"expectedrows\"></span>
            <table id=\"table1\" style=\"width:100%;text-align:center;\">
                <thead>
                    <tr>
                        <th>s_corr_id</th>
                        <th>category</th>
                        <th>freq</th>
                        <th>trail</th>
                        <th>b_id_1 (name_1)</th>
                        <th>b_id_2 (name_2)</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>obs_end_val</th>
                        <th>last_updated</th>

                        <th>error message/rows updated</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </section>

    <section class=\"container\">
        <div class=\"container\" id=\"info\">
        </div>
    </section>

    <script>
        \$(document).ready(function() {
            for (i=0;i<tagsCorrel.length;i++) {
                \$('#table1').append('<tr id=\"' + tagsCorrel[i].s_corr_id + '\"><td>' + tagsCorrel[i].s_corr_id + '</td><td>' + tagsCorrel[i].category + '</td><td>' + tagsCorrel[i].freq + '</td><td>' + tagsCorrel[i].trail + '</td><td>' + tagsCorrel[i].b_id_1 + ' ' + tagsCorrel[i].name_1 + '</td><td>' + tagsCorrel[i].b_id_2 + ' ' + tagsCorrel[i].name_2 + '</td><td> ' + tagsCorrel[i].obs_start +  '</td><td>' + tagsCorrel[i].obs_end + '</td><td>' + tagsCorrel[i].obs_end_val + '</td><td>' + tagsCorrel[i].last_updated + '</td><td></td></tr>');
            }
        });

        \$(\"#update\").click(function(){
            var category = \$(\"#category\").val();
            console.log(category);
                    
            console.log(tagsCorrel);
            calcCorrel(0);

        });

        function calcCorrel(i) {
            var model = [];
            model[0] = 'update_hist_correl';
            toScript = ['uHistCorrel'];

            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: model,
                    toScript: toScript,
                    fromAjax: {corrTag: tagsCorrel[i]}
                },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results).uHistCorrel;
                    console.log(results);
                    console.log(tagsCorrel[i].s_corr_nid);
                    
                    
                    if (results.info.insertedHistData === true) {
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<span>Successfully updated #' + i  + ': ' + results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')</span>');
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(7)').html(results.info.firstDate);
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(8)').html(results.info.lastDate);
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(9)').html(results.info.lastFirstInput);

                    }
                    else {
                        console.log('#' + tagsCorrel[i].s_corr_nid + ' td:nth-child(11)');
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<span><b>Failed</b> to update #' + i  + ': '  + results.info.errorMsg + '</span>');
                    }
                    
                    i ++;
                    if (i<tagsCorrel.length) calcCorrel(i);
                },
                error:function(e, ts, et){
                        \$('#' + tagsCorrel[i].s_corr_id + ' td:nth-child(11)').html('<br><span style=\"font-weight:bold\">AJAX ERROR ON #' + i  + ': ' + tagsCorrel[i].s_corr_nid +  ' | ' + ts +  '</span>');
                        i ++;
                        if (i<tagsCorrel.length) calcCorrel(i);
                }
            });
        }

    </script>
    
{% endblock %}", "updatehistcorrel.html", "/var/www/correlation/public_html/templates/updatehistcorrel.html");
    }
}
