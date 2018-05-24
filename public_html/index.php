<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Stock/Sector/Industry Correlation Lookup</title>
    <link rel="icon" type=image/ico href="img/favicon.ico"/>
    <meta name=description content="Content." />
    <meta name=keywords content="sectors, gics sectors, gics groups, gics lookup, stock sector correlation, correlation" />
    
    <link rel="stylesheet" href="correlations/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>

    <script src="//code.jquery.com/jquery-git.min.js"></script>
    
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="//code.highcharts.com/stock/highstock.js"></script>
    <script src="//code.highcharts.com/modules/heatmap.js"></script>

    <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

    <script src="correlations/script-ui.js"></script>
    <script src="correlations/script.js"></script>
</head>
<body>
    <header class="clearfix">
        <div class="container-fluid" style="height:10px;background-color:rgba(10, 24, 66,1);margin-bottom:.5rem"></div>
        <div class="container">
            <div class="row">
                <h4 style="text-align:left">Stock/Sector Correlation Matrix</h4>
            </div>
        </div>
        
        <nav class="navbar navbar-expand-xl navbar-light">
            <div class="container">
                <!--<a class="navbar-brand" href="#"></a>-->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbarLg">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse collapse" id="collapsingNavbarLg">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Correlation Calculator</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/test.php">Link</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        
    </header>    
    
    <section class="container">
        <p>Description description description what is this...</p>
    </section>
    
    <section class="container" style="margin-bottom:20px">
        <form class="form-inline">
            <div class = "form-group">
                <label for="stock" >Stock Ticker:</label>
                <input type="text" class="form-control form-control-sm" id="stock" value="" placeholder="e.g., AAPL" title="Test">
                <button class="btn btn-primary btn-sm" type="button" id="submit">Submit</button>
                <div id="errormessage" class="invalid-feedback">Error Message!</div>
            </div>
        </form>        
    </section>
    
    <section class="container" id="spinnercontainer" style="display:none">
        <div class="row">
            <div class="text-center col-12"><h4 style="text-align:center" id="loadmessage"><h4></div>
        </div>
        <div class="row">
            <div class="sk-circle">
                <div class="sk-circle1 sk-child"></div>
                <div class="sk-circle2 sk-child"></div>
                <div class="sk-circle3 sk-child"></div>
                <div class="sk-circle4 sk-child"></div>
                <div class="sk-circle5 sk-child"></div>
                <div class="sk-circle6 sk-child"></div>
                <div class="sk-circle7 sk-child"></div>
                <div class="sk-circle8 sk-child"></div>
                <div class="sk-circle9 sk-child"></div>
                <div class="sk-circle10 sk-child"></div>
                <div class="sk-circle11 sk-child"></div>
                <div class="sk-circle12 sk-child"></div>
            </div>
        </div>
    </section>
    
    <section class="container" id="resultscontainer">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#heatmaptab" role="tab" aria-selected="true">Matrix</a>
          </li>

            
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Stock-to-Industry Correlation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Stock-to-Sector Correlation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Stock-to-Market Correlation</a>
          </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="heatmaptab" role="tabpanel">
            <div class="container">    
                <div class="row">
                    <div class="col-lg-4">
                        <div class="container successmsg"></div>
                    </div>
                    <div class="col-lg-8">
                        <div id="heatmap"></div>
                    </div>
                </div>
            </div>  
          </div>

            
          <div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="tab-1">
            <div id="chart_1" class="chart""></div>
          </div>
          <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab-2">
            <div id="chart_2" class="chart"></div>
          </div>
          <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab-3">
            <div id="chart_3" class="chart"></div>
          </div>
        </div>
    </section>

<footer class="page-footer font-small pt-4 mt-5" style="background: rgba(10, 24, 66,1);">

  <div class="container text-center text-md-left">

    <div class="row">

      <div class="col-md-6 mt-md-0 mt-3">

        <h5 class="text-uppercase">Stuff I haven't finished yet</h5>
        <p>Blah blah blah.</p>

      </div>

      <hr class="clearfix w-100 d-md-none pb-3">

      <div class="col-md-6 mb-md-0 mb-3">

        <h5 class="text-uppercase">Links</h5>

        <ul class="list-unstyled">
          <li>
            <a href="#!">Link 1</a>
          </li>
          <li>
            <a href="#!">Link 2</a>
          </li>
          <li>
            <a href="#!">Link 3</a>
          </li>
          <li>
            <a href="#!">Link 4</a>
          </li>
        </ul>

      </div>

    </div>

  </div>

  <div class="footer-copyright text-center py-3">© 2018 Copyright Charles Ye
    <a href="mailto:cye@outlook.com">Email</a>
  </div>
    <div class="container-fluid" style="height:100px"></div>


</footer>

</body>

</html>
