<?php

if (!isset($_POST) || !isset($_POST['value'])) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
  exit;
}

include_once('../classes/joueur.class.php');


$licences = explode(';',htmlentities($_POST['value']));

$date = htmlentities($_POST['date']);

$ws_fonction = 'ws_getrankingallbyarrayoflicencedate';

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
$arrLicenses = array ();
foreach ($licences as $licence) {
  if (empty($licence)) continue;
  $arrLicenses[] = sprintf("%08d",$licence);
}

  $joueurs = new Joueur($arrLicenses, "Param2" => $date), $ws_fonction);
  $rows = $joueurs->getSingleRows();
  if (!empty($rows)) {
    echo $rows;
  }
?>
      </tbody>
    </table>
  </div>
</div>
