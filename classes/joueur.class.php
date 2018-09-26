<?php
//  var FFBaDLogin = "********";
//  var FFBadPwd = "********";
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
  
  public static function renderScore($score,$estVictoire){  
    return $score; //temporaire
  }
  
  public function getHTMLPresentation(){
    if (!$this->estValide) return NULL;
    $output = '<div style="margin: 5px 0">';
    $output .= '<div class="col-md-4 col-md-offset-4 panel panel-default">';
    $output .= '  <div class="panel-body">';
    $output .= '    <h2 style="text-align:center">'.($this->getPrenom()).' '.($this->getNom()).'</h2>';
    $output .= '    <div style="text-align:center">'.$this->infos['INS_NOM'].' ('.$this->infos["INS_SIGLE"].')</div>';
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
    $output .= '              <td>'.self::renderClassement($this->classemts[0]["TCL_NOM"]).'<BR/>'.$this->classemts[0]["CHE_COTE_FFBAD"].'</td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[1]["TCL_NOM"]).'<BR/>'.$this->classemts[1]["CHE_COTE_FFBAD"].'</td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[2]["TCL_NOM"]).'<BR/>'.$this->classemts[2]["CHE_COTE_FFBAD"].'</td>';
    $output .= '            </tr>';
    $output .= '          </tbody>';
    $output .= '        </table>';
    $output .= '      </div>';
    $output .= '    </div>';
    $output .= '  </div>';
    
    return $output;
  }
  
  public function getSingleRow(){
    if (!$this->estValide) return NULL;
    $output =  '            <tr>';
    $output .= '              <td>'.$this->infos["PER_PRENOM"].' '.$this->infos["PER_NOM"].'</td>';
    $output .= '              <td><a href="/ffbad/?value='.$this->infos["PER_LICENCE"].'">'.$this->infos["PER_LICENCE"].'</a></td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[0]["TCL_NOM"]).'</td><td>'.$this->classemts[0]["CHE_COTE_FFBAD"].'</td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[1]["TCL_NOM"]).'</td><td>'.$this->classemts[1]["CHE_COTE_FFBAD"].'</td>';
    $output .= '              <td>'.self::renderClassement($this->classemts[2]["TCL_NOM"]).'</td><td>'.$this->classemts[2]["CHE_COTE_FFBAD"].'</td>';
    $output .= '            </tr>';
    
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
