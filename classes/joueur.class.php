<?php
//  var FFBaDLogin = "";
//  var FFBadPwd = "";
//  var FFBadURL = "https://ws.ffbad.org/rest/";
//  url = FFBadURL+'?AuthJson={"Login":"'+FFBaDLogin+'","Password":"'+FFBadPwd+'"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"06774602"}}';
//  url = FFBadURL+'?AuthJson={"Login":"'+FFBaDLogin+'","Password":"'+FFBadPwd+'"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"06774602"}}';
//  url = FFBadURL+'?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"07019781"}}';
//  url = FFBadURL+'?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"00556828"}}';
//  url = FFBadURL+'?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"06633042"}}';
//  https://ws.ffbad.org/rest/?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getresultbylicence","Param":{"Param1":"07034602"}}
//  https://ws.ffbad.org/rest/?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getrefereesbynamepart","Param":{"Param1":"HARLIER"}}
//  https://ws.ffbad.org/rest/?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getlicenceinfobystartnom","Param":{"Param1":"%CHARLIER"}}
//  https://ws.ffbad.org/rest/?AuthJson={"Login":"FFBaDLogin","Password":"FFBadPwd"}&QueryJson={"Function":"ws_getlicenceinfolistbyarrayoflicence","Param":{"Param1":["00556828","00556828"]}}

include_once('../config/config.php');
include_once('../classes/ffbadSoap.class.php');

class joueur{

  private $retour = array();
  private $results = array();
  private $infos = array();
  private $classemts = array();
  private $estValide = false;
  private $flash = "";

  private static function trierResultats($res1,$res2){

    $datetime1 = new DateTime($res1['EMA_DATE']);
    $datetime2 = new DateTime($res2['EMA_DATE']);
    $diffDate = ($datetime2->format('U')-$datetime1->format('U'));

    if ($diffDate == 0 ) {
      $diffSlug = strcmp($res1["EVR_SLUG"], $res2["EVR_SLUG"]);
      if ( $diffSlug == 0)  return ($res1['ETP_ORDRE']-$res2['ETP_ORDRE']);
      return $diffSlug;
    }

    return $diffDate;
  }

  public function __construct($params = array ( "Param1" => "0"), $ws_fonction = "ws_getresultbylicence") {
      global $defaultRes;
      global $wsAuthJson;

      //Contruction de l'objet Requête
      $Query["Function"] = $ws_fonction;
      //Construction avec 2 paramêtres
      $Query["Param"] = $params;

      $QueryJson = json_encode($Query);

      if (preg_match('/[0-9]+/',$params['Param1']) && intval($params['Param1'])==1) {
        $Return = $defaultRes;
      }else{
        try {
          $clientSOAP = ffbadSoap::connect();
          $Return = $clientSOAP->getResult($QueryJson,$wsAuthJson);
        } catch (Exception $e) {
          $this->flash = "Impossible de joindre le webservice ";
          $this->flash .= $e->getMessage();
        }
      }


      $ArrayRes = json_decode($Return, true);

      if ($ArrayRes == NULL) {
        $this->flash = "Erreur de syntaxe lors de r&eacute;cup&eacute;ration des donn&eacute;es";
        return -2;
      }

      if (strcmp($ArrayRes["Statut"],"OK") !== 0) {
        $this->flash = "Mauvais statut du webservice: ".$ArrayRes["Statut"];
        return -3;
      }


      switch ($ws_fonction){
        case "ws_getresultbylicence":
        case "ws_getresultbylicencedate":
          $ArrResJoueur = $ArrayRes["Retour"];

          if (count($ArrResJoueur["PlayerInformation"]) == 0) {
            $this->flash = "Joueur Introuvable";
            return -4;
          }
          $this->infos = $ArrResJoueur["PlayerInformation"][0];
          $this->classemts = $ArrResJoueur["Ranking"];
          $results = $ArrResJoueur["Results"];

          usort($results,array('joueur','trierResultats'));

          $this->results = $results;

          break;
        case "ws_getlicenceinfobystartnom":
          break;
        case "ws_getrankingallbyarrayoflicencedate":
          break;
        default:
          break;
      }


      $this->retour = $ArrayRes["Retour"];

      $this->estValide = true;

      return 0;

  }

  public function getFlash(){
    $flash = $this->flash;
    $this->flash = "";
    return $flash;
  }

  public function getRetour(){
    return $this->retour;
  }

  public function getResultats(){
    return $this->results;
  }

  public function getInfos(){
    return $this->infos;
  }

  public function getClassements(){
    return $this->classemts;
  }

  public function getPrenom(){
    if (!$this->estValide) return NULL;
    return $this->infos['PER_PRENOM'];
  }

  public function getNom(){
    if (!$this->estValide) return NULL;
    return $this->infos['PER_NOM'];
  }

  public static function renderClassement($strClassement){
    global $bgColorN;global $bgColorR;global $bgColorD;global $bgColorP;global $bgColorNC;
    global $colorNC;global $colorP;global $colorD;global $colorR;global $colorN;

    $lettre=substr($strClassement,0,1);
    $bgColor = "white";
    $color = "black";
    if (strcmp($lettre,'N') == 0) {
      if(strcmp($strClassement,"NC") ==0 ){
        $bgColor = $bgColorNC;
        $color = $colorNC;
      }
      else{
        $bgColor = $bgColorN;
        $color = $colorN;
      }
    }elseif (strcmp($lettre,'R') == 0) {
      $bgColor = $bgColorR;
      $color = $colorR;
    }elseif (strcmp($lettre,'D') == 0) {
      $bgColor = $bgColorD;
      $color = $colorD;
    }elseif (strcmp($lettre,'P') == 0) {
      $bgColor = $bgColorP;
      $color = $colorP;
    }

    return '<span class="badge" style="background-color:'.$bgColor.';color:'.$color.'">'.$strClassement.'</span>';
  }

  public static function renderScore($score,$estVictoire,$estWO = 0, $estAbandon = 0){
    if ($estWO == 1) { return "- wo -"; }
    if ($estAbandon == 1) { return $score." - ab"; }
    return $score; //temporaire
  }

  public static function renderMatchDouble($match = array(), $joueur){
    $nbsets = intval($match['EMA_SET_WIN_NB'])+ intval($match['EMA_SET_LOS_NB']);

    $row1 = "<tr style=\"border-top: 2px solid #ddd\">
    <td rowspan='4' class=\"".($match['EPA_IS_VICTOIRE'] ? 'bg-success' : 'bg-danger')."\">&nbsp;</td>
    <td rowspan='4'>".$match['TOUR']."</td>";

    $row2 = "<tr style=\"border-top: 2px solid #ddd\">";

    if ($match['EPA_IS_WO'] == 1){
      $row1 .= "<td rowspan='2' colspan='3'></td>";
      $row2 .= "<td rowspan='2' colspan='3' style='text-align:center'>&nbsp;- wo -&nbsp;</td>";
    }elseif ($match['EPA_IS_ABANDON'] == 1) {
      $sets = explode('/',$match['EMA_SCORE']);
      foreach ($sets as $k => $set) {
        $marques = explode('-',$set);
        $row1 .= "<td rowspan='2'>".intval($marques[0])."</td>";
        $row2 .= "<td rowspan='2'>".intval($marques[1]).(($k == (count($sets) -1)) ? "&nbsp;(ab)": "")."</td>";
      }
      if (count($sets) < 2 ){
        $row1 .= "<td rowspan='2'></td>";
        $row2 .= "<td rowspan='2'></td>";
      }
      if (count($sets) < 3 ){
        $row1 .= "<td rowspan='2'></td>";
        $row2 .= "<td rowspan='2'></td>";
      }
    }else {
      $row1 .= "<td rowspan='2'>".$match['EMA_SET_WIN_SCORE1']."</td>";
      $row1 .= "<td rowspan='2'>".$match['EMA_SET_WIN_SCORE2']."</td>";
      $row1 .= "<td rowspan='2'>".$match['EMA_SET_WIN_SCORE3']."</td>";

      $row2 .= "<td rowspan='2'>".$match['EMA_SET_LOS_SCORE1']."</td>";
      $row2 .= "<td rowspan='2'>".$match['EMA_SET_LOS_SCORE2']."</td>";
      $row2 .= "<td rowspan='2'>".$match['EMA_SET_LOS_SCORE3']."</td>";
    }

    if (isset($match['PARTENAIRE'][0]))
      $part = "<td><a href=\"/ffbad/?value=".$match['PARTENAIRE'][0]['EVI_LICENCE']."\">".$match['PARTENAIRE'][0]['EVI_PRENOM'].' '.$match['PARTENAIRE'][0]['EVI_NOM']."</td><td>".self::renderClassement($match['PARTENAIRE'][0]['TCL_NOM'])."</a></td></tr>";
    else
      $part = "<td></td><td></td></tr>";

    $part .= "<tr style=\"border-bottom:2px solid #ddd;\">
    <td><b>".$joueur->getPrenom().' '.$joueur->getNom()."</td><td>".self::renderClassement($match['TCL_NOM'])."</b></td>
    </tr>";


    if (isset($match['ADVERSAIRE'][0]))
      $adv = "<td><a href=\"/ffbad/?value=".$match['ADVERSAIRE'][0]['EVI_LICENCE']."\">".$match['ADVERSAIRE'][0]['EVI_PRENOM'].' '.$match['ADVERSAIRE'][0]['EVI_NOM']."</td><td>".self::renderClassement($match['ADVERSAIRE'][0]['TCL_NOM'])."</a></td>
      ";
    else
      $adv .= "<td></td><td></td>";
    $adv .= "</tr>";
    $adv .= "<tr>";
    if (isset($match['ADVERSAIRE'][1]))
      $adv .= "
      <td><a href=\"/ffbad/?value=".$match['ADVERSAIRE'][1]['EVI_LICENCE']."\">".$match['ADVERSAIRE'][1]['EVI_PRENOM'].' '.$match['ADVERSAIRE'][1]['EVI_NOM']."</td><td>".self::renderClassement($match['ADVERSAIRE'][1]['TCL_NOM'])."</a></td>
      ";
    else
      $adv .= "<td></td><td></td>";
    $adv .= "</tr>";

    $output = "";

    if ($match['EPA_IS_VICTOIRE']) {
      $output .= $row1.$part.$row2.$adv;
    } else {
      $output .= $row1.$adv.$row2.$part;
    }

    return $output;
  }

  public static function renderMatchSimple($match = array()){
    $output = "<tr>
    <td class=\"".($match['EPA_IS_VICTOIRE'] ? 'bg-success' : 'bg-danger')."\">&nbsp;</td>
    <td>".$match['TOUR']."</td>
    <td>".self::renderScore($match['EMA_SCORE'],$match['EPA_IS_VICTOIRE'],$match['EPA_IS_WO'],$match['EPA_IS_ABANDON'])."</td>";
    if (isset($match['ADVERSAIRE'][0]))
      $cols = "<td><a href=\"/ffbad/?value=".$match['ADVERSAIRE'][0]['EVI_LICENCE']."\">".$match['ADVERSAIRE'][0]['EVI_PRENOM'].' '.$match['ADVERSAIRE'][0]['EVI_NOM']."</a></td>
      <td>".self::renderClassement($match['ADVERSAIRE'][0]['TCL_NOM'])."</td>";
    else
      $cols = "<td></td><td></td>";
    $output .= $cols."</tr>";
    return $output;
  }

  public static function renderMoyenne($moyCalc = array(), $moyReelle = array()){
    $output = "";
    foreach ($moyCalc as $k => $r){
       $output .= "<span class='label label-primary' style='margin: 0 2px'>".sprintf('%01.2f',$r);
      if (strcmp($r,$moyReelle[$k]) != 0 ){
        $output .= "* (".sprintf('%01.2f',$moyReelle[$k]).")";
      }
      $output .= "</span>";
    }
    return $output;
  }

  public function getHTMLPresentation(){
    if (!$this->estValide) return NULL;
    $today = new DateTime('now');
    $birthday = new DateTime($this->infos['PER_NAISSANCE']);
    $age = $today->diff($birthday)->format('%y');

    $output = '<div style="margin: 5px 0">';
    $output .= '<div class="col-md-4 col-md-offset-4 panel panel-default">';
    $output .= '  <div class="panel-body">';
    $output .= '    <h2 style="text-align:center">'.($this->getPrenom()).' '.($this->getNom()).'</h2>';
    $output .= '    <div style="text-align:center">'.$this->infos['INS_NOM'].' ('.$this->infos["INS_SIGLE"].')</div>';
    $output .= '    <div style="text-align:center"><small>('.$age."&nbsp;ans".($this->infos['JOU_IS_MUTE'] == 1 ? "&nbsp;- mut&eacute;".($this->infos['PER_PES_ID'] != 1 ? "e" : "") : "").')</small></div>';
    $output .= '  </div>';
    $output .= '</div>';
    $output .= '</div>';
    //print_r($output); exit();
    return $output;
  }

  public function getHTMLClassements(){
    if (!$this->estValide) return NULL;
    $output = '  <div class="row">';
    $output .= '    <div class="col-md-4 col-md-offset-4 panel panel-default">';
    $output .= '      <div class="panel-body">';
    $output .= '        <table class="table table-condensed" style="margin-bottom: 0px;">';
    $output .= '          <thead>';
    $output .= '            <tr>';
    $output .= '             <th style="text-align:center">Simple</th>';
    $output .= '             <th style="text-align:center">Double</th>';
    $output .= '             <th style="text-align:center">Mixte</th>';
    $output .= '            </tr>';
    $output .= '          </thead>';
    $output .= '          <tbody style="text-align:center">';
    $output .= '            <tr>';
    $output .= '              <td>'.self::renderClassement($this->classemts[0]["TCL_NOM"]).'<BR/>'.$this->classemts[0]["CHE_COTE_FFBAD"].'<BR/><small>('.$this->classemts[0]["CHE_RANG"].')</small></td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[1]["TCL_NOM"]).'<BR/>'.$this->classemts[1]["CHE_COTE_FFBAD"].'<BR/><small>('.$this->classemts[1]["CHE_RANG"].')</small></td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[2]["TCL_NOM"]).'<BR/>'.$this->classemts[2]["CHE_COTE_FFBAD"].'<BR/><small>('.$this->classemts[2]["CHE_RANG"].')</small></td>';
    $output .= '            </tr>';
    $output .= '          </tbody>';
    $output .= '        </table>';
    $output .= '      </div>';
    $output .= '    </div>';
    $output .= '  </div>';

    return $output;
  }

  public function getSingleRows(){
    if (!$this->estValide) return NULL;
    $output = "";
    $rets = $this->retour;
    foreach ($rets as $ret) {
      $output .=  '            <tr>';
      $output .= '              <td>'.$ret["PER_PRENOM"].' '.$ret["PER_NOM"].'</td>';
      $output .= '              <td><a href="/ffbad/?value='.$ret["PER_LICENCE"].'">'.$ret["PER_LICENCE"].'</a></td>';
      $output .= '              <td>'.self::renderClassement($ret["SIMPLE_NOM"]).'</td><td>'.$ret["SIMPLE_COTE_FFBAD"].'</td>';
      $output .= '              <td>'.self::renderClassement($ret["DOUBLE_NOM"]).'</td><td>'.$ret["DOUBLE_COTE_FFBAD"].'</td>';
      $output .= '              <td>'.self::renderClassement($ret["MIXTE_NOM"]).'</td><td>'.$ret["MIXTE_COTE_FFBAD"].'</td>';
      $output .= '            </tr>';
    }

    return $output;
  }
  public function getRows(){
    if (!$this->estValide) return NULL;
    $output = '';

    foreach ($this->retour as $joueur){
      $output .=  '            <tr>';
      $output .= '              <td>'.$joueur["PER_NOM"].'</td>';
      $output .= '              <td>'.$joueur["PER_PRENOM"].'</td>';
      $output .= '              <td>'.$joueur["INS_SIGLE"].'</td>';
      $output .= '              <td>'.$joueur["INS_NOM"].' ('.$joueur["INS_NUMERO_DEPT"].')</td>';
      $output .= '              <td>'.$joueur["PER_LICENCE"].'</td>';
      $output .= '              <td><a href="/ffbad/?value='.$joueur["PER_LICENCE"].'"><span class="glyphicon glyphicon-search"></span></td>';
      $output .= '            </tr>';
    }
    //var_dump($output); exit;
    return $output;
  }
}
