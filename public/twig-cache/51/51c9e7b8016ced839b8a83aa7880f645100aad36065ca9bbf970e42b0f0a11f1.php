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
        // line 4
        echo "<script src=\"https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js\"></script>

";
    }

    // line 8
    public function block_content($context, array $blocks = array())
    {
        // line 9
        echo "
    <section class=\"container\">
    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"category\" >Category:</label>
                <select class=\"form-control form-control-sm\" id=\"category\">
                    <option value=\"\">All</option>
                </select>
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form> 
    </section>
    
    <section class=\"container\">
        <div class=\"container\" style=\"max-height:400px;overflow:auto\">
            <table id=\"table1\" style=\"width:100%;text-align:center;\">
                <thead>
                    <tr>
                        <th>s_id</th>
                        <th>fk_id</th>
                        <th>name</th>
                        <th>freq</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>last_updated</th>
                        <th>updated now</th>
                        <th>error message/rows updated</th>
                        <th>attempted url</th>
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
            
            //Populate options text
            for (i=0;i<specsCategories.length;i++) {
                \$('#category').append('<option value=\"' + specsCategories[i].cat_nid + '\">' + specsCategories[i].cat_name + '</option>');
            }
            
            for (i=0;i<tagsSeries.length;i++) {
                \$('#table1').append('<tr id=\"' + tagsSeries[i].s_id + '\"><td>' + tagsSeries[i].s_id + '</td><td>' + tagsSeries[i].b_id + '</td><td>' + tagsSeries[i].name + '</td><td>' + tagsSeries[i].freq + '</td><td>' + tagsSeries[i].obs_start + '</td><td> ' + tagsSeries[i].obs_end +  '</td><td>' + tagsSeries[i].last_updated + '</td><td></td><td></td><td></td></tr>');
            }
            
            \$('#table1').DataTable({
                    paging: false,
                    \"autoWidth\": false

            });

        });

        
        \$(\"#update\").click(function(){
            var category = \$(\"#category\").val();
            var tagsSeriesFiltered = [];
                    
            console.log(tagsSeries);
            j = 0;
            for (i=0; i<tagsSeries.length; i++) {
                if (tagsSeries[i].category.indexOf(category) !== -1) { tagsSeriesFiltered[j] = tagsSeries[i]; j++; }
            }
            
            console.log(tagsSeriesFiltered);
            curlData(category,tagsSeriesFiltered,0);
        });

        function curlData(category,tagsSeries,i) {
            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: ['update_hist_series'],
                    toScript: ['uHistSeries'],
                    fromAjax: {series: tagsSeries[i], category: category}
                    },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results).uHistSeries;
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        console.log(tagsSeries[i].s_id);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').text('Successfully updated #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').text(results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')');

                    }
                    else {
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').html('<b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').text(results.info.errorMsg);
                    }
                    
                    \$('#' + tagsSeries[i].s_id + ' td:nth-child(10)').append(' <a href=\"' + results.info.url + '\">URL</a>');
                    
                    i ++;
                    if (i<tagsSeries.length) curlData(category,tagsSeries,i);
                    else {
                        \$('#table1').DataTable().destroy();
                        \$('#table1').DataTable({
                                    paging: false,
                                    \"autoWidth\": false
    
                        });
                    }
                    
                },
                error: function(e, ts, et){
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').html('<b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').html('<br><span style=\"font-weight:bold\">AJAX ERROR | ' + ts +  ' (s_id: ' + tagsSeries[i].s_id +  ')</span>');
                        i ++;
                        if (i<tagsSeries.length) curlData(category,tagsSeries,i);
                        else {
                            \$('#table1').DataTable().destroy();
                            \$('#table1').DataTable({
                                        paging: false,
                                        \"autoWidth\": false
        
                            });
                        }
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
        return array (  45 => 9,  42 => 8,  36 => 4,  33 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"layout.html\" %}

{% block staticlinks %}
<script src=\"https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js\"></script>

{% endblock %}

{% block content %}

    <section class=\"container\">
    </section>
    
    <section class=\"container\" style=\"margin-bottom:20px\">
        <form class=\"form-inline\">
            <div class = \"form-group\">
                <label for=\"category\" >Category:</label>
                <select class=\"form-control form-control-sm\" id=\"category\">
                    <option value=\"\">All</option>
                </select>
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form> 
    </section>
    
    <section class=\"container\">
        <div class=\"container\" style=\"max-height:400px;overflow:auto\">
            <table id=\"table1\" style=\"width:100%;text-align:center;\">
                <thead>
                    <tr>
                        <th>s_id</th>
                        <th>fk_id</th>
                        <th>name</th>
                        <th>freq</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>last_updated</th>
                        <th>updated now</th>
                        <th>error message/rows updated</th>
                        <th>attempted url</th>
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
            
            //Populate options text
            for (i=0;i<specsCategories.length;i++) {
                \$('#category').append('<option value=\"' + specsCategories[i].cat_nid + '\">' + specsCategories[i].cat_name + '</option>');
            }
            
            for (i=0;i<tagsSeries.length;i++) {
                \$('#table1').append('<tr id=\"' + tagsSeries[i].s_id + '\"><td>' + tagsSeries[i].s_id + '</td><td>' + tagsSeries[i].b_id + '</td><td>' + tagsSeries[i].name + '</td><td>' + tagsSeries[i].freq + '</td><td>' + tagsSeries[i].obs_start + '</td><td> ' + tagsSeries[i].obs_end +  '</td><td>' + tagsSeries[i].last_updated + '</td><td></td><td></td><td></td></tr>');
            }
            
            \$('#table1').DataTable({
                    paging: false,
                    \"autoWidth\": false

            });

        });

        
        \$(\"#update\").click(function(){
            var category = \$(\"#category\").val();
            var tagsSeriesFiltered = [];
                    
            console.log(tagsSeries);
            j = 0;
            for (i=0; i<tagsSeries.length; i++) {
                if (tagsSeries[i].category.indexOf(category) !== -1) { tagsSeriesFiltered[j] = tagsSeries[i]; j++; }
            }
            
            console.log(tagsSeriesFiltered);
            curlData(category,tagsSeriesFiltered,0);
        });

        function curlData(category,tagsSeries,i) {
            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: ['update_hist_series'],
                    toScript: ['uHistSeries'],
                    fromAjax: {series: tagsSeries[i], category: category}
                    },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    results = JSON.parse(results).uHistSeries;
                    console.log(results);
                    if (results.info.insertedHistData === true) {
                        console.log(tagsSeries[i].s_id);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').text('Successfully updated #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').text(results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')');

                    }
                    else {
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').html('<b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').text(results.info.errorMsg);
                    }
                    
                    \$('#' + tagsSeries[i].s_id + ' td:nth-child(10)').append(' <a href=\"' + results.info.url + '\">URL</a>');
                    
                    i ++;
                    if (i<tagsSeries.length) curlData(category,tagsSeries,i);
                    else {
                        \$('#table1').DataTable().destroy();
                        \$('#table1').DataTable({
                                    paging: false,
                                    \"autoWidth\": false
    
                        });
                    }
                    
                },
                error: function(e, ts, et){
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(8)').html('<b>Failed</b> to update #' + i  + ': ' + tagsSeries[i].name);
                        \$('#' + tagsSeries[i].s_id + ' td:nth-child(9)').html('<br><span style=\"font-weight:bold\">AJAX ERROR | ' + ts +  ' (s_id: ' + tagsSeries[i].s_id +  ')</span>');
                        i ++;
                        if (i<tagsSeries.length) curlData(category,tagsSeries,i);
                        else {
                            \$('#table1').DataTable().destroy();
                            \$('#table1').DataTable({
                                        paging: false,
                                        \"autoWidth\": false
        
                            });
                        }
                }
            });
        }

    </script>
    
{% endblock %}", "updatehistseries.html", "/var/www/contagion/public/templates/updatehistseries.html");
    }
}
