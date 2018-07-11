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
                    <option value=\"\">Select a Category</option>
                </select>
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form> 
    </section>
    
    <section class=\"container\">
        <div id=\"categoryinfo\">
            
        </div>
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
                        <th>corr_type</th>
                        <th>b_id_1 (name_1)</th>
                        <th>b_id_2 (name_2)</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>obs_end_val</th>
                        <th>last_updated</th>

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
            
            \$('#category').on('change', function() {
                category = \$('#category').val();
                if (category == '') {
                    console.log('Choose a category');
                    return;
                }
                for (i=0;i<specsCategories.length;i++) {
                    if (specsCategories[i].cat_nid == category) {
                        \$('#categoryinfo').html('<li> Frequencies to be calculated: ' + specsCategories[i].cat_freqtrails + '</li>');
                        specsCategoriesFiltered = [];
                        specsCategoriesFiltered = specsCategories[i];
                        updateTableAndStoreWindowData(category,specsCategoriesFiltered);
                        
                        break;
                    }
                }
                
            });

            function updateTableAndStoreWindowData(category,specsCategoriesFiltered) {
                window.tagsCorrelFiltered = []; window.tagsSeriesFiltered = []; window.existingUniqIdentifiers = [];
                
                j = 0;
                for (i=0;i<tagsCorrel.length;i++) {
                    if (tagsCorrel[i].category !== category) continue;
                    tagsCorrelFiltered[j] = tagsCorrel[i];
                    tagsCorrelFiltered[j].uniq_nid = tagsCorrelFiltered[i].category + '.' +  tagsCorrelFiltered[i].fk_id_1 + '.' + tagsCorrelFiltered[i].fk_id_2 + '.' + tagsCorrelFiltered[i].freq + '.' + tagsCorrelFiltered[i].trail + '.' + tagsCorrelFiltered[i].corr_type;
                    existingUniqIdentifiers.push(tagsCorrelFiltered[j].uniq_nid);
                    j++;
                }
                
                j = 0;
                for (i=0;i<tagsSeries.length;i++) {
                    if (!tagsSeries[i].category.includes(category)) continue;
                    tagsSeriesFiltered[j] = tagsSeries[i];
                    j++;
                }
            
                for (i=0;i<tagsCorrelFiltered.length;i++) {
                    \$('#table1').append('<tr id=\"' + tagsCorrelFiltered[i].uniq_nid + '\"><td>' + tagsCorrelFiltered[i].s_corr_id + '</td><td>' + tagsCorrelFiltered[i].category + '</td><td>' + tagsCorrelFiltered[i].freq + '</td><td>' + tagsCorrelFiltered[i].trail + '</td><td>'+ tagsCorrelFiltered[i].corr_type +'</td><td>' + tagsCorrelFiltered[i].b_id_1 + ' ' + tagsCorrelFiltered[i].name_1 + '</td><td>' + tagsCorrelFiltered[i].b_id_2 + ' ' + tagsCorrelFiltered[i].name_2 + '</td><td> ' + tagsCorrelFiltered[i].obs_start +  '</td><td>' + tagsCorrelFiltered[i].obs_end + '</td><td>' + tagsCorrelFiltered[i].obs_end_val + '</td><td>' + tagsCorrelFiltered[i].last_updated + '</td><td></td><td></td></tr>');
                }
                
                \$('#table1').DataTable({
                        paging: false,
                        \"autoWidth\": false
                });
                var arr = specsCategoriesFiltered.cat_freqtrails.split(\",\");
                var arr2 = specsCategoriesFiltered.cat_corrtypes.split(\",\");

                \$('#expectedrows').text('Expected number of rows = ' + (tagsSeriesFiltered.length-1) * (tagsSeriesFiltered.length)/2 * arr.length * arr2.length);
                
                
                window.specsCategoriesFiltered = specsCategoriesFiltered;
                window.tagsCorrelFiltered = tagsCorrelFiltered;
                window.tagsSeriesFiltered = tagsSeriesFiltered;
            
            }
            
        });

        
        \$(\"#update\").click(function(){
            
            if (!window.tagsSeriesFiltered || !window.specsCategoriesFiltered || !window.existingUniqIdentifiers ) {
                alert('Choose a valid category.');
                return;
            } else {
                updateData();
            }
        });

        function updateData() {
            console.log(window.specsCategoriesFiltered);
            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: ['update_tags_correl'],
                    toScript: ['uTagsCorr'],
                    fromAjax: {
                        specsCategories: window.specsCategoriesFiltered,
                        tagsSeries: window.tagsSeriesFiltered,
                        existingUniqIdentifiers: window.existingUniqIdentifiers
                        }
                    },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    uTagsCorr = JSON.parse(results).uTagsCorr;
                    console.log(uTagsCorr);
                    /*
                    for (i=0;i<uTagsCorr.length;i++) {
                        if (uTagsCorr.updated === true) {
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(10)').text('Successfully updated #' + i  + ': ' + uTagsCorr[i].s_corr_nid);
                            //\$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(11)').text(results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')');
                        }
                        else {
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(10)').html('<b>Failed</b> to update #' + i  + ': ' + uTagsCorr[i].s_corr_nid);
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(11)').text(uTagsCorr[i].errorMsg);
                        }
                        
                    }
                    
                    
                    \$('#table1').DataTable().destroy();
                    \$('#table1').DataTable({
                                paging: false,
                                \"autoWidth\": false
                    });
    
                    */
                },
                error: function(e, ts, et){
                    console.log('AJAX ERROR');
                }
            });
        }

    </script>

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
                    <option value=\"\">Select a Category</option>
                </select>
                <button class=\"btn btn-primary btn-sm\" type=\"button\" id=\"update\">Update</button>
                <div id=\"errormessage\" class=\"invalid-feedback\">Error Message!</div>
            </div>
        </form> 
    </section>
    
    <section class=\"container\">
        <div id=\"categoryinfo\">
            
        </div>
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
                        <th>corr_type</th>
                        <th>b_id_1 (name_1)</th>
                        <th>b_id_2 (name_2)</th>
                        <th>obs_start</th>
                        <th>obs_end</th>
                        <th>obs_end_val</th>
                        <th>last_updated</th>

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
            
            \$('#category').on('change', function() {
                category = \$('#category').val();
                if (category == '') {
                    console.log('Choose a category');
                    return;
                }
                for (i=0;i<specsCategories.length;i++) {
                    if (specsCategories[i].cat_nid == category) {
                        \$('#categoryinfo').html('<li> Frequencies to be calculated: ' + specsCategories[i].cat_freqtrails + '</li>');
                        specsCategoriesFiltered = [];
                        specsCategoriesFiltered = specsCategories[i];
                        updateTableAndStoreWindowData(category,specsCategoriesFiltered);
                        
                        break;
                    }
                }
                
            });

            function updateTableAndStoreWindowData(category,specsCategoriesFiltered) {
                window.tagsCorrelFiltered = []; window.tagsSeriesFiltered = []; window.existingUniqIdentifiers = [];
                
                j = 0;
                for (i=0;i<tagsCorrel.length;i++) {
                    if (tagsCorrel[i].category !== category) continue;
                    tagsCorrelFiltered[j] = tagsCorrel[i];
                    tagsCorrelFiltered[j].uniq_nid = tagsCorrelFiltered[i].category + '.' +  tagsCorrelFiltered[i].fk_id_1 + '.' + tagsCorrelFiltered[i].fk_id_2 + '.' + tagsCorrelFiltered[i].freq + '.' + tagsCorrelFiltered[i].trail + '.' + tagsCorrelFiltered[i].corr_type;
                    existingUniqIdentifiers.push(tagsCorrelFiltered[j].uniq_nid);
                    j++;
                }
                
                j = 0;
                for (i=0;i<tagsSeries.length;i++) {
                    if (!tagsSeries[i].category.includes(category)) continue;
                    tagsSeriesFiltered[j] = tagsSeries[i];
                    j++;
                }
            
                for (i=0;i<tagsCorrelFiltered.length;i++) {
                    \$('#table1').append('<tr id=\"' + tagsCorrelFiltered[i].uniq_nid + '\"><td>' + tagsCorrelFiltered[i].s_corr_id + '</td><td>' + tagsCorrelFiltered[i].category + '</td><td>' + tagsCorrelFiltered[i].freq + '</td><td>' + tagsCorrelFiltered[i].trail + '</td><td>'+ tagsCorrelFiltered[i].corr_type +'</td><td>' + tagsCorrelFiltered[i].b_id_1 + ' ' + tagsCorrelFiltered[i].name_1 + '</td><td>' + tagsCorrelFiltered[i].b_id_2 + ' ' + tagsCorrelFiltered[i].name_2 + '</td><td> ' + tagsCorrelFiltered[i].obs_start +  '</td><td>' + tagsCorrelFiltered[i].obs_end + '</td><td>' + tagsCorrelFiltered[i].obs_end_val + '</td><td>' + tagsCorrelFiltered[i].last_updated + '</td><td></td><td></td></tr>');
                }
                
                \$('#table1').DataTable({
                        paging: false,
                        \"autoWidth\": false
                });
                var arr = specsCategoriesFiltered.cat_freqtrails.split(\",\");
                var arr2 = specsCategoriesFiltered.cat_corrtypes.split(\",\");

                \$('#expectedrows').text('Expected number of rows = ' + (tagsSeriesFiltered.length-1) * (tagsSeriesFiltered.length)/2 * arr.length * arr2.length);
                
                
                window.specsCategoriesFiltered = specsCategoriesFiltered;
                window.tagsCorrelFiltered = tagsCorrelFiltered;
                window.tagsSeriesFiltered = tagsSeriesFiltered;
            
            }
            
        });

        
        \$(\"#update\").click(function(){
            
            if (!window.tagsSeriesFiltered || !window.specsCategoriesFiltered || !window.existingUniqIdentifiers ) {
                alert('Choose a valid category.');
                return;
            } else {
                updateData();
            }
        });

        function updateData() {
            console.log(window.specsCategoriesFiltered);
            \$.ajax({
                url: 'routerAjax.php',
                type: 'POST',
                data: {
                    model: ['update_tags_correl'],
                    toScript: ['uTagsCorr'],
                    fromAjax: {
                        specsCategories: window.specsCategoriesFiltered,
                        tagsSeries: window.tagsSeriesFiltered,
                        existingUniqIdentifiers: window.existingUniqIdentifiers
                        }
                    },
                dataType: 'html',
                cache: false,
                timeout: 10000,
                success: function(results){
                    console.log(\"Success\");
                    console.log(results);
                    uTagsCorr = JSON.parse(results).uTagsCorr;
                    console.log(uTagsCorr);
                    /*
                    for (i=0;i<uTagsCorr.length;i++) {
                        if (uTagsCorr.updated === true) {
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(10)').text('Successfully updated #' + i  + ': ' + uTagsCorr[i].s_corr_nid);
                            //\$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(11)').text(results.info.rowsChg + ' rows (' + results.info.firstDate + ' to ' + results.info.lastDate + ')');
                        }
                        else {
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(10)').html('<b>Failed</b> to update #' + i  + ': ' + uTagsCorr[i].s_corr_nid);
                            \$('#' + uTagsCorr[i].s_corr_nid + ' td:nth-child(11)').text(uTagsCorr[i].errorMsg);
                        }
                        
                    }
                    
                    
                    \$('#table1').DataTable().destroy();
                    \$('#table1').DataTable({
                                paging: false,
                                \"autoWidth\": false
                    });
    
                    */
                },
                error: function(e, ts, et){
                    console.log('AJAX ERROR');
                }
            });
        }

    </script>

{% endblock %}", "updatetagscorrel.html", "/var/www/correlation/public_html/templates/updatetagscorrel.html");
    }
}
