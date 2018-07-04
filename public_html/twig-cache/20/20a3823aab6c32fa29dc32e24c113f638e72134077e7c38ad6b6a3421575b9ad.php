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
    <meta name=keywords content=\"sectors, gics sectors, gics groups, gics lookup, stock sector correlation, correlation\" />
    
    <link rel=\"stylesheet\" href=\"static/style.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css\" integrity=\"sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB\" crossorigin=\"anonymous\">
    
    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>

    <script src=\"//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>
    ";
        // line 20
        $this->displayBlock('staticlinks', $context, $blocks);
        // line 21
        echo "    ";
        echo ($context["pageJS"] ?? null);
        echo "
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
                            <a class=\"nav-link\" href=\"/regions\">Cross-Regional Correlations</a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/\">Correlation Calculator (Under Development)</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        
    </header>    
    ";
        // line 53
        $this->displayBlock('content', $context, $blocks);
        // line 55
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
        // line 120
        echo ($context["bodyScript"] ?? null);
        echo "
</script>

</body>

</html>";
    }

    // line 20
    public function block_staticlinks($context, array $blocks = array())
    {
    }

    // line 53
    public function block_content($context, array $blocks = array())
    {
        // line 54
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
        return array (  174 => 54,  171 => 53,  166 => 20,  156 => 120,  89 => 55,  87 => 53,  51 => 21,  49 => 20,  34 => 8,  25 => 1,);
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
    
    <script src=\"//code.jquery.com/jquery-git.min.js\"></script>

    <script src=\"//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>
    <script src=\"//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>
    {% block staticlinks %}{% endblock %}
    {{ pageJS | raw }}
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
                            <a class=\"nav-link\" href=\"/regions\">Cross-Regional Correlations</a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/\">Correlation Calculator (Under Development)</a>
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

</html>", "layout.html", "/var/www/correlation/public_html/templates/layout.html");
    }
}
