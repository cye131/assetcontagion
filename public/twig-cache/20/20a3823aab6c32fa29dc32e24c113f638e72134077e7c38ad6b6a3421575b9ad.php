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
            'staticlinks' => array($this, 'block_staticlinks'),
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
    <meta name=keywords content=\"financial contagion, financial contagion index, cross-asset contagion, cross-asset contagion\" />
    
    <link rel=\"stylesheet\" href=\"static/style.css\">
    <link rel=\"stylesheet\" href=\"static/bootstrap.min.css\">
    
    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>

    <script src=\"//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>
    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/stock/highcharts-more.js\"></script>

<!--
    <script type=\"text/javascript\" async src=\"https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML\">
        MathJax.Hub.Config({
          extensions: [\"tex2jax.js\"],
          jax: [\"input/TeX\",\"output/HTML-CSS\"]
        });
    </script>
-->

    ";
        // line 32
        $this->displayBlock('staticlinks', $context, $blocks);
        // line 33
        echo "    ";
        echo ($context["pageJS"] ?? null);
        echo "
</head>
<body>
    <header class=\"clearfix\">
        <div class=\"container-fluid\" style=\"height:10px;background-color:rgba(10, 24, 66,1);margin-bottom:.5rem\"></div>
        <div class=\"container\">
            <div class=\"row\">
                <h4 style=\"text-align:left\">Assetcontagion.com</h4>
            </div>
        </div>
        
        <nav class=\"navbar navbar-expand-xl navbar-dark bg-dark\" id=\"navbar\">
            <div class=\"container\">
                <!--<a class=\"navbar-brand\" href=\"#\"></a>-->
                <button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapsingNavbarLg\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"navbar-collapse collapse\" id=\"collapsingNavbarLg\">
                    <ul class=\"navbar-nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/contagion\">Financial Contagion Index</a>
                        </li>
                        <li class=\"nav-item dropdown\">
                            <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"/regions\">Cross-Regional Correlation</a>
                            <div class=\"dropdown-menu bg-dark\">
                              <a class=\"dropdown-item\" href=\"/regions-hm\">Correlation Matrix</a>
                              <a class=\"dropdown-item\" href=\"/regions-map\">Map</a>
                              <a class=\"dropdown-item\" href=\"/regions-ts\">Historical Data</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>    
    ";
        // line 68
        $this->displayBlock('content', $context, $blocks);
        // line 70
        echo "

<footer class=\"page-footer font-small pt-4 mt-5\" style=\"background: rgba(10, 24, 66,1);\">
  <div class=\"container text-center text-md-left\">
    <div class=\"row\">
      <div class=\"col-md-6 mt-md-0 mt-3\">
        <h5 class=\"text-uppercase\">\\(a \\ne 0\\)</h5>
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


<div class=\"overlay h-100\" id=\"overlay\" style=\"display:none\">
    <div class=\"row h-25\">
        <div class=\"\"></div>
    </div>
    <div class=\"row\">
        <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\">Loading ...</h4></div>
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
</div>


<input type=\"hidden\" id=\"data\"></input>

<script>
  ";
        // line 135
        echo ($context["bodyScript"] ?? null);
        echo "
</script>

</body>

</html>";
    }

    // line 32
    public function block_staticlinks($context, array $blocks = array())
    {
    }

    // line 68
    public function block_content($context, array $blocks = array())
    {
        // line 69
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
        return array (  189 => 69,  186 => 68,  181 => 32,  171 => 135,  104 => 70,  102 => 68,  63 => 33,  61 => 32,  34 => 8,  25 => 1,);
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
    <meta name=keywords content=\"financial contagion, financial contagion index, cross-asset contagion, cross-asset contagion\" />
    
    <link rel=\"stylesheet\" href=\"static/style.css\">
    <link rel=\"stylesheet\" href=\"static/bootstrap.min.css\">
    
    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>

    <script src=\"//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>
    <script src=\"//code.highcharts.com/stock/highstock.js\"></script>
    <script src=\"//code.highcharts.com/stock/highcharts-more.js\"></script>

<!--
    <script type=\"text/javascript\" async src=\"https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML\">
        MathJax.Hub.Config({
          extensions: [\"tex2jax.js\"],
          jax: [\"input/TeX\",\"output/HTML-CSS\"]
        });
    </script>
-->

    {% block staticlinks %}{% endblock %}
    {{ pageJS | raw }}
</head>
<body>
    <header class=\"clearfix\">
        <div class=\"container-fluid\" style=\"height:10px;background-color:rgba(10, 24, 66,1);margin-bottom:.5rem\"></div>
        <div class=\"container\">
            <div class=\"row\">
                <h4 style=\"text-align:left\">Assetcontagion.com</h4>
            </div>
        </div>
        
        <nav class=\"navbar navbar-expand-xl navbar-dark bg-dark\" id=\"navbar\">
            <div class=\"container\">
                <!--<a class=\"navbar-brand\" href=\"#\"></a>-->
                <button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapsingNavbarLg\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"navbar-collapse collapse\" id=\"collapsingNavbarLg\">
                    <ul class=\"navbar-nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/contagion\">Financial Contagion Index</a>
                        </li>
                        <li class=\"nav-item dropdown\">
                            <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"/regions\">Cross-Regional Correlation</a>
                            <div class=\"dropdown-menu bg-dark\">
                              <a class=\"dropdown-item\" href=\"/regions-hm\">Correlation Matrix</a>
                              <a class=\"dropdown-item\" href=\"/regions-map\">Map</a>
                              <a class=\"dropdown-item\" href=\"/regions-ts\">Historical Data</a>
                            </div>
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
        <h5 class=\"text-uppercase\">\\(a \\ne 0\\)</h5>
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


<div class=\"overlay h-100\" id=\"overlay\" style=\"display:none\">
    <div class=\"row h-25\">
        <div class=\"\"></div>
    </div>
    <div class=\"row\">
        <div class=\"text-center col-12\"><h4 style=\"text-align:center\" id=\"loadmessage\">Loading ...</h4></div>
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
</div>


<input type=\"hidden\" id=\"data\"></input>

<script>
  {{ bodyScript|raw }}
</script>

</body>

</html>", "layout.html", "/var/www/contagion/public/templates/layout.html");
    }
}
