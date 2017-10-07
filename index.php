<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="./favicon.ico">

    <title>FFBad : Recherche</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">  
    <!-- Bootstrap theme -->
    <!-- <link href="./css/bootstrap-theme.min.css" rel="stylesheet"> -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./css/dataTables.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap-datetimepicker.min.css"/>
 
    <link href="./css/my-theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="./js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.min.js"></script>
      <script src="./js/respond.min.js"></script>
    <![endif]-->
  </head>
  
  <body role="document">
    
    <div class="container-fluid" role="main">
    
      <div class="row">
       <div class="col-md-8 col-md-offset-2" style="margin-top: 20px">
          <div class="col-md-9">
            <div class="input-group input-group-lg">
              <input id="rechercher-input" type="text" class="form-control" placeholder="Rechercher par nom ou licence ...">
              <span class="input-group-btn" >
                <button id="rechercher" class="btn btn-default btn-lg" type="button" ><span class="glyphicon glyphicon-search"></span></button>
              </span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group date input-group-lg datetimepicker">
              <input id="date-input" type="text" class="form-control" placeholder="Date JJ/MM/AAAA ...">
              <span class="input-group-addon" >
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <p class="message"></p>
        </div>
      </div>
      
      <div class="container-fluid">
        <p class="result"></p>
      </div>
    
    </div>
    
    
    
    
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="./js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="./js/transition.js"></script>
    <script type="text/javascript" src="./js/collapse.js"></script>
    <script type="text/javascript" src="./js/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap.min.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
    
    <script type="text/javascript" src="./js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="./js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="./js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="./js/responsive.bootstrap.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap-datetimepicker.min.js"></script>


    <script src="./js/my-javascript.js"></script>
  </body>
</html>
