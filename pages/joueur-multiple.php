<?php

if (!isset($_POST) || !isset($_POST['value'])) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
  exit;
}

include_once('../classes/joueur.class.php');


$licences = explode(';',htmlentities($_POST['value']));

$date = htmlentities($_POST['date']);

$ws_fonction = 'ws_getresultbylicencedate';

?>

<div class="row">
  <div style="margin: 5px 0">
    <h2>Classements & Points <small class="text-muted">date du <?php $datef = new DateTime($date); echo  $datef->format('d/m/Y'); ?></small></h2>
  </div>
  <div style="margin: 5px 0">
    <table id="licence-table" class="table table-condensed table-hover table-striped datatable" style="margin-bottom: 0px; width: 100%">
      <thead>
        <tr>
          <th rowspan=2 style="vertical-align:middle;text-align:center">Pr&eacute;nom NOM</th>
          <th rowspan=2 style="vertical-align:middle;text-align:center">Licence</th>
          <th colspan=2 style="text-align:center;border: 0px">Simple</th>
          <th colspan=2 style="text-align:center;border: 0px">Double</th>
          <th colspan=2 style="text-align:center;border: 0px">Mixte</th>
        </tr>
        <tr>
          <th style="text-align:center">Classement</th>
          <th style="text-align:center">Points</th>
          <th style="text-align:center">Classement</th>
          <th style="text-align:center">Points</th>
          <th style="text-align:center">Classement</th>
          <th style="text-align:center">Points</th>
        </tr>        
      </thead>
      <tbody style="text-align:center">
<?php
foreach ($licences as $licence) {
  if (empty($licence)) continue;
  $joueur = new Joueur(array( "Param1" => sprintf("%08d",$licence), "Param2" => $date), $ws_fonction);
  $row = $joueur->getSingleRow();
  if (empty($row)) continue;
  echo $row;
}
?>
      </tbody>
    </table>
  </div>
</div>
