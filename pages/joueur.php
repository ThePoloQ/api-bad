<?php

if (!isset($_POST) || !isset($_POST['value'])) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
  exit;
}

include_once('../classes/joueur.class.php');

function enteteTableau($strCol, $EVR_SLUG, $result){
    $output = "";
    if (strcmp($EVR_SLUG,$result['EVR_SLUG']) !== 0){
      if ($EVR_SLUG != NULL) {
        $output .= "</tbody></table></div></div>";
      }
      
      $pClass = "panel-default";
      if($result['IsInRanking']) $pClass = "panel-success";
      
      $points = "";
      $valid = "";

      if($result['EVR_IS_VALID'] == 0) $valid .= ' <small> - <span class="text-warning">En cours de validation</span></small>';
      if ($result['EVR_RESULTAT'] > 0.0001) $points="<small> - ".$result['EVR_RESULTAT']." pts</small>";
      
      $output .= '<div class="panel '.$pClass.'">';
        $output .= '<div class="panel-heading">';
          $output .= "<b>".$result['EVN_NOM']."</b>".$points.$valid."<br/>";
        $output .= "</div>";
        $output .= '<div class="panel-body">';
          $output .= "<small>";
          $output .= $result['DATE_CONCAT']."<br/>";
          if ($result['EMT_COEFF_C']) $output .= "<i>Valeur: ".$result['EMT_COEFF_C']."</i>, ";
          if($result['IsInRanking']) $output .= '<b style="color: #3c763d">';
          $output .= "Resultat: ".$result['EVR_RESULTAT'];
          if($result['IsInRanking']) $output .= '</b>';
          $output .= "</small>";
          
          $output .= $strCol;
    }
    return array($output,$result['EVR_SLUG']);
}

$joueur = new Joueur(array( "Param1" => sprintf("%08d",htmlentities($_POST['value']))));

$infos = $joueur->getInfos();
$classemt = $joueur->getClassements();
$results = $joueur->getResultats();
$clstSP =  $classemt[0];
$clstDB =  $classemt[1];
$clstMX =  $classemt[2];

echo $joueur->getHTMLPresentation();

echo $joueur->getHTMLClassements();
?>
 
<div class="row">
  <div style="margin: 5px 0">
   <div id="res-tabs">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#simple" aria-controls="simple" role="tab" data-toggle="tab">Simple</a></li>
        <li role="presentation"><a href="#double" aria-controls="double" role="tab" data-toggle="tab">Double</a></li>
        <li role="presentation"><a href="#mixte" aria-controls="mixte" role="tab" data-toggle="tab">Mixte</a></li>
      </ul>
    </div>

    <!-- Tab panes -->
    <div class="tab-content" style="padding-top: 5px;">
      <div role="tabpanel" class="tab-pane active" id="simple">
        <div class="row" style="margin: 0 5px">
        <div class="col-md-4 col-md-offset-4 panel panel-default" style="padding:0 2px">
          <div class="panel-body" style="text-align:center;padding:5px">
            <?php
              $resC = explode('|',str_replace(",","",$clstSP['CHE_LISTE_RESULTAT']));
              $resR = explode('|',$clstSP['CHE_LISTE_RESULTAT_COMPLET']);
              foreach ($resC as $k => $r){
                echo "<span class='label label-primary' style='margin: 0 2px'>".sprintf('%01.2f',$r);
                if (strcmp($r,$resR[$k]) != 0 ){
                  echo "* (".sprintf('%01.2f',$resR[$k]).")";
                }
                echo "</span>";
              }
            ?>
          </div>
        </div>
        </div>

        <?php
        $EVR_SLUG = NULL;
        
        foreach ($results as $result){
          if ($result['EMA_TDI_ID'] != 1 && $result['EMA_TDI_ID'] != 2 ) continue;
          $strCol = '<table class="table table-condensed" style="margin-bottom:0px"><thead><th></th><th>Stade</th><th>Score</th><th colspan="2">Adversaire</th></tr></thead><tbody style="border-bottom: 1px solid #ddd">';
          $resEnt = enteteTableau($strCol,$EVR_SLUG,$result);
          echo $resEnt[0];
          $EVR_SLUG = $resEnt[1];
          
          echo "<tr>
          <td class=\"".($result['EPA_IS_VICTOIRE'] ? 'bg-success' : 'bg-danger')."\">&nbsp;</td>
          <td>".$result['TOUR']."</td>
          <td>".Joueur::renderScore($result['EMA_SCORE'],$result['EPA_IS_VICTOIRE'])."</td>";
          if (isset($result['ADVERSAIRE'][0]))
            $cols = "<td><a href=\"/ffbad/?value=".$result['ADVERSAIRE'][0]['EVI_LICENCE']."\">".$result['ADVERSAIRE'][0]['EVI_PRENOM'].' '.$result['ADVERSAIRE'][0]['EVI_NOM']."</a></td>
            <td>".Joueur::renderClassement($result['ADVERSAIRE'][0]['TCL_NOM'])."</td>";
          else
            $cols = "<td></td><td></td>";
          echo $cols."</tr>";
        }
        if ($EVR_SLUG != NULL) {
          echo "</tbody></table></div></div>";
        }
        ?>
      </div>
      <div role="tabpanel" class="tab-pane" id="double">
        <div class="row" style="margin: 0 5px">
        <div class="col-md-4 col-md-offset-4 panel panel-default" style="padding:0 2px">
          <div class="panel-body" style="text-align:center; padding: 5px">
            <?php
              $resC = explode('|',str_replace(",","",$clstDB['CHE_LISTE_RESULTAT']));
              $resR = explode('|',$clstDB['CHE_LISTE_RESULTAT_COMPLET']);
              foreach ($resC as $k => $r){
                echo "<span class='label label-primary' style='margin: 0 2px'>".sprintf('%01.2f',$r);
                if (strcmp($r,$resR[$k]) != 0 ){
                  echo "* (".sprintf('%01.2f',$resR[$k]).")";
                }
                echo "</span>";
              }
            ?>
          </div>
        </div>
        </div>
        
      <?php
            
        $EVR_SLUG = NULL;
        
        foreach ($results as $result){
          if ($result['EMA_TDI_ID'] != 3 && $result['EMA_TDI_ID'] != 4 ) continue;
          $nbsets = intval($result['EMA_SET_WIN_NB'])+ intval($result['EMA_SET_LOS_NB']);
          $strCol = '<table class="table table-condensed" style="margin-bottom:0px"><thead><tr><th></th><th>Stade</th><th colspan="3">Score</th><th colspan="2">Match</th></tr></thead><tbody style="border-bottom: 2px solid #ddd">';
          $resEnt = enteteTableau($strCol,$EVR_SLUG,$result);
          echo $resEnt[0];
          $EVR_SLUG = $resEnt[1];
            
          $row1 = "<tr style=\"border-top: 2px solid #ddd\">
          <td rowspan='4' class=\"".($result['EPA_IS_VICTOIRE'] ? 'bg-success' : 'bg-danger')."\">&nbsp;</td>
          <td rowspan='4'>".$result['TOUR']."</td>";

          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE1']."</td>";
          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE2']."</td>";
          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE3']."</td>";

          $row2 = "<tr style=\"border-top: 2px solid #ddd\">";

          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE1']."</td>";
          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE2']."</td>";
          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE3']."</td>";

          if (isset($result['PARTENAIRE'][0]))
            $part = "<td><a href=\"/ffbad/?value=".$result['PARTENAIRE'][0]['EVI_LICENCE']."\">".$result['PARTENAIRE'][0]['EVI_PRENOM'].' '.$result['PARTENAIRE'][0]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['PARTENAIRE'][0]['TCL_NOM'])."</a></td></tr>";
          else
            $part = "<td></td><td></td></tr>";
          
          $part .= "<tr style=\"border-bottom:2px solid #ddd;\">
          <td><b>".$joueur->getPrenom().' '.$joueur->getNom()."</td><td>".Joueur::renderClassement($result['TCL_NOM'])."</b></td>
          </tr>";

          
          if (isset($result['ADVERSAIRE'][0]))
            $adv = "<td><a href=\"/ffbad/?value=".$result['ADVERSAIRE'][0]['EVI_LICENCE']."\">".$result['ADVERSAIRE'][0]['EVI_PRENOM'].' '.$result['ADVERSAIRE'][0]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['ADVERSAIRE'][0]['TCL_NOM'])."</a></td>
            ";
          else
            $adv .= "<td></td><td></td>";
          $adv .= "</tr>";
          $adv .= "<tr>"; 
          if (isset($result['ADVERSAIRE'][1]))
            $adv .= "
            <td><a href=\"/ffbad/?value=".$result['ADVERSAIRE'][1]['EVI_LICENCE']."\">".$result['ADVERSAIRE'][1]['EVI_PRENOM'].' '.$result['ADVERSAIRE'][1]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['ADVERSAIRE'][1]['TCL_NOM'])."</a></td>
            ";
          else
            $adv .= "<td></td><td></td>";
          $adv .= "</tr>";

          if ($result['EPA_IS_VICTOIRE']) {
            echo $row1.$part.$row2.$adv;
          } else {
            echo $row1.$adv.$row2.$part;
          }

        }
        if ($EVR_SLUG != NULL) {
          echo "</tbody></table></div></div>";
        }
      ?>
          </tbody>
        </table>
      </div>

      
      <div role="tabpanel" class="tab-pane" id="mixte">
        <div class="row" style="margin: 0 5px">
        <div class="col-md-4 col-md-offset-4 panel panel-default" style="padding:0 2px">
          <div class="panel-body" style="text-align:center;padding: 5px">
            <?php
              $resC = explode('|',str_replace(",","",$clstMX['CHE_LISTE_RESULTAT']));
              $resR = explode('|',$clstMX['CHE_LISTE_RESULTAT_COMPLET']);
              foreach ($resC as $k => $r){
                echo "<span class='label label-primary' style='margin: 0 2px'>".sprintf('%01.2f',$r);
                if (strcmp($r,$resR[$k]) != 0 ){
                  echo "* (".sprintf('%01.2f',$resR[$k]).")";
                }
                echo "</span>";
              }
            ?>
          </div>
        </div>
        </div>

        <?php
        
        $EVR_SLUG = NULL;
        
        foreach ($results as $result){
          if ($result['EMA_TDI_ID'] != 5) continue;
          $strCol = '<table class="table table-condensed" style="margin-bottom:0px"><thead><tr><th></th><th>Stade</th><th colspan="3">Score</th><th colspan="2">Match</th></tr></thead><tbody style="border-bottom: 2px solid #ddd">';
          $resEnt = enteteTableau($strCol,$EVR_SLUG,$result);
          echo $resEnt[0];
          $EVR_SLUG = $resEnt[1];

          $row1 = "<tr style=\"border-top: 2px solid #ddd\">
          <td rowspan='4' class=\"".($result['EPA_IS_VICTOIRE'] ? 'bg-success' : 'bg-danger')."\">&nbsp;</td>
          <td rowspan='4'>".$result['TOUR']."</td>";

          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE1']."</td>";
          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE2']."</td>";
          $row1 .= "<td rowspan='2'>".$result['EMA_SET_WIN_SCORE3']."</td>";

          $row2 = "<tr  style=\"border-top: 2px solid #ddd\">";

          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE1']."</td>";
          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE2']."</td>";
          $row2 .= "<td rowspan='2'>".$result['EMA_SET_LOS_SCORE3']."</td>";

          if (isset($result['PARTENAIRE'][0]))
            $part = "<td><a href=\"/ffbad/?value=".$result['PARTENAIRE'][0]['EVI_LICENCE']."\">".$result['PARTENAIRE'][0]['EVI_PRENOM'].' '.$result['PARTENAIRE'][0]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['PARTENAIRE'][0]['TCL_NOM'])."</a></td></tr>";
          else
            $part = "<td></td><td></td></tr>";

          $part .= "<tr style=\"border-bottom:2px solid #ddd;\">
          <td><b>".$joueur->getPrenom().' '.$joueur->getNom()."</td><td>".Joueur::renderClassement($result['TCL_NOM'])."</b></td>
          </tr>";


          if (isset($result['ADVERSAIRE'][0]))
            $adv = "<td><a href=\"/ffbad/?value=".$result['ADVERSAIRE'][0]['EVI_LICENCE']."\">".$result['ADVERSAIRE'][0]['EVI_PRENOM'].' '.$result['ADVERSAIRE'][0]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['ADVERSAIRE'][0]['TCL_NOM'])."</a></td>
            ";
          else
            $adv .= "<td></td><td></td>";
          $adv .= "</tr>";
          $adv .= "<tr>";
          if (isset($result['ADVERSAIRE'][1]))
            $adv .= "
            <td><a href=\"/ffbad/?value=".$result['ADVERSAIRE'][1]['EVI_LICENCE']."\">".$result['ADVERSAIRE'][1]['EVI_PRENOM'].' '.$result['ADVERSAIRE'][1]['EVI_NOM']."</td><td>".Joueur::renderClassement($result['ADVERSAIRE'][1]['TCL_NOM'])."</a></td>
            ";
          else
            $adv .= "<td></td><td></td>";
          $adv .= "</tr>";

          if ($result['EPA_IS_VICTOIRE']) {
            echo $row1.$part.$row2.$adv;
          } else {
            echo $row1.$adv.$row2.$part;
          }            
        }
        if ($EVR_SLUG != NULL) {
          echo "</tbody></table></div></div>";
        }
        ?>
          </tbody>
        </table>
      </div>
  
    </div>
  </div>
</div>
  


