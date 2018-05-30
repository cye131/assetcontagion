<?php

/* layout.html */
class __TwigTemplate_67552a73ce0761e65f5b7d5c91174c5c5f8b81ae993a8c7c3a3318eeaae97406 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en-US\">

<head>
    <meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">

    <title>";
        // line 8
        echo twig_escape_filter($this->env, ($context["title"] ?? null));
        echo "</title>

    <link rel=\"icon\" type=image/ico href=\"/static/favicon.ico\"/>
    <meta name=description content=\"Content.\" />
    <meta name=keywords content=\"sectors, gics sectors, gics groups, gics lookup, stock sector correlation, correlation\" />
    
    <link rel=\"stylesheet\" href=\"static/style.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css\" integrity=\"sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB\" crossorigin=\"anonymous\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css\"/>

    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>
    
    <script src=\"//code.jquery.com/ui/1.12.1/jquery-ui.min.js\"></script>

    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>

    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

    <script src=\"//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js\"></script>

    <script src=\"static/script-ui.js\"></script>
    <script src=\"static/script.js\"></script>
    
    <script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: \"ca-pub-9558447444041339\",
        enable_page_level_ads: true
      });
    </script>
</head>
<body>
    <header class=\"clearfix\">
        <div class=\"container-fluid\" style=\"height:10px;background-color:rgba(10, 24, 66,1);margin-bottom:.5rem\"></div>
        <div class=\"container\">
            <div class=\"row\">
                <h4 style=\"text-align:left\">Stock/Sector Correlation Matrix</h4>
            </div>
        </div>
        
        <nav class=\"navbar navbar-expand-xl navbar-light\">
            <div class=\"container\">
                <!--<a class=\"navbar-brand\" href=\"#\"></a>-->
                <button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapsingNavbarLg\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"navbar-collapse collapse\" id=\"collapsingNavbarLg\">
                    <ul class=\"navbar-nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/\">Correlation Calculator</a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/test\">Link</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        
    </header>    
    ";
        // line 74
        $this->displayBlock('content', $context, $blocks);
        // line 76
        echo "
<footer class=\"page-footer font-small pt-4 mt-5\" style=\"background: rgba(10, 24, 66,1);\">

  <div class=\"container text-center text-md-left\">

    <div class=\"row\">

      <div class=\"col-md-6 mt-md-0 mt-3\">

        <h5 class=\"text-uppercase\">Stuff I haven't finished yet</h5>
        <p>Blah blah blah.</p>

      </div>

      <hr class=\"clearfix w-100 d-md-none pb-3\">

      <div class=\"col-md-6 mb-md-0 mb-3\">

        <h5 class=\"text-uppercase\">Links</h5>

        <ul class=\"list-unstyled\">
          <li>
            <a href=\"#!\">Link 1</a>
          </li>
          <li>
            <a href=\"#!\">Link 2</a>
          </li>
          <li>
            <a href=\"#!\">Link 3</a>
          </li>
          <li>
            <a href=\"#!\">Link 4</a>
          </li>
        </ul>

      </div>

    </div>

  </div>

  <div class=\"footer-copyright text-center py-3\">© 2018 Copyright Charles Ye
    <a href=\"mailto:cye@outlook.com\">Email</a>
  </div>
    <div class=\"container-fluid\" style=\"height:100px\"></div>


</footer>

<script>
  ";
        // line 126
        echo ($context["script"] ?? null);
        echo "
</script>

</body>

</html>";
    }

    // line 74
    public function block_content($context, array $blocks = array())
    {
        // line 75
        echo "    ";
    }

    public function getTemplateName()
    {
        return "layout.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 75,  166 => 74,  156 => 126,  104 => 76,  102 => 74,  33 => 8,  24 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html lang=\"en-US\">

<head>
    <meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">

    <title>{{ title|e }}</title>

    <link rel=\"icon\" type=image/ico href=\"/static/favicon.ico\"/>
    <meta name=description content=\"Content.\" />
    <meta name=keywords content=\"sectors, gics sectors, gics groups, gics lookup, stock sector correlation, correlation\" />
    
    <link rel=\"stylesheet\" href=\"static/style.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css\" integrity=\"sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB\" crossorigin=\"anonymous\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css\"/>

    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>
    
    <script src=\"//code.jquery.com/ui/1.12.1/jquery-ui.min.js\"></script>

    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>

    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/modules/heatmap.js\"></script>

    <script src=\"//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js\"></script>
    <script src=\"//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js\"></script>
    <script src=\"//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js\"></script>

    <script src=\"static/script-ui.js\"></script>
    <script src=\"static/script.js\"></script>
    
    <script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: \"ca-pub-9558447444041339\",
        enable_page_level_ads: true
      });
    </script>
</head>
<body>
    <header class=\"clearfix\">
        <div class=\"container-fluid\" style=\"height:10px;background-color:rgba(10, 24, 66,1);margin-bottom:.5rem\"></div>
        <div class=\"container\">
            <div class=\"row\">
                <h4 style=\"text-align:left\">Stock/Sector Correlation Matrix</h4>
            </div>
        </div>
        
        <nav class=\"navbar navbar-expand-xl navbar-light\">
            <div class=\"container\">
                <!--<a class=\"navbar-brand\" href=\"#\"></a>-->
                <button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapsingNavbarLg\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"navbar-collapse collapse\" id=\"collapsingNavbarLg\">
                    <ul class=\"navbar-nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/\">Correlation Calculator</a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/test\">Link</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        
    </header>    
    {% block content %}
    {% endblock %}

<footer class=\"page-footer font-small pt-4 mt-5\" style=\"background: rgba(10, 24, 66,1);\">

  <div class=\"container text-center text-md-left\">

    <div class=\"row\">

      <div class=\"col-md-6 mt-md-0 mt-3\">

        <h5 class=\"text-uppercase\">Stuff I haven't finished yet</h5>
        <p>Blah blah blah.</p>

      </div>

      <hr class=\"clearfix w-100 d-md-none pb-3\">

      <div class=\"col-md-6 mb-md-0 mb-3\">

        <h5 class=\"text-uppercase\">Links</h5>

        <ul class=\"list-unstyled\">
          <li>
            <a href=\"#!\">Link 1</a>
          </li>
          <li>
            <a href=\"#!\">Link 2</a>
          </li>
          <li>
            <a href=\"#!\">Link 3</a>
          </li>
          <li>
            <a href=\"#!\">Link 4</a>
          </li>
        </ul>

      </div>

    </div>

  </div>

  <div class=\"footer-copyright text-center py-3\">© 2018 Copyright Charles Ye
    <a href=\"mailto:cye@outlook.com\">Email</a>
  </div>
    <div class=\"container-fluid\" style=\"height:100px\"></div>


</footer>

<script>
  {{ script|raw }}
</script>

</body>

</html>", "layout.html", "/var/www/correlation/public_html/templates/layout.html");
    }
}
