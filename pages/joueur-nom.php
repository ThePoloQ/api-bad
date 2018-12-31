<?php

if (!isset($_POST) || !isset($_POST['value'])) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
  exit;
}

include_once('../classes/joueur.class.php');


$recherche = htmlentities(preg_replace('/\s+/','|',$_POST['value']));

//$ws_fonction = 'ws_getlicenceinfobystartnom';
$ws_fonction = 'search2016';


?>

<div class="row">
  <div style="margin: 5px 0">
    <h2><small class="text-muted">Recherche: <?php echo $recherche ?></small></h2>
  </div>
  <div style="margin: 5px 0">
    <table id="recherche-table" class="table table-condensed table-hover table-striped datatable" style="margin-bottom: 0px; width: 100%">
      <thead>
        <tr>
          <th style="text-align:center">NOM</th>
          <th style="text-align:center">Pr&eacute;nom</th>
          <th style="text-align:center">Sigle</th>
          <th style="text-align:center">Club</th>
          <th style="text-align:center">Simple</th>
          <th style="text-align:center">Double</th>
          <th style="text-align:center">Mixte</th>
          <th style="text-align:center">Licence</th>
          <th></th>
        </tr>
      </thead>
      <tbody style="text-align:center">
<?php

//  $joueurs = new Joueur(array( "Param1" => '%'.$recherche), $ws_fonction);
  $joueurs = new Joueur(array( "token" => $recherche,"start" => 0, "max" => 50), $ws_fonction);

  echo $joueurs->getRows();
?>
      </tbody>
    </table>
  </div>
</div>
