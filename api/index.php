<?php
  header('Content-type: application/json');
  include("../config.php");
  $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  $params = explode("/", $_SERVER['REQUEST_URI']);

  // ODDCAST
  use SergiX44\OddcastTTS\Oddcast;
  use \SergiX44\OddcastTTS\Voices\Italian\Raffaele;
  include("../include/Oddcast.php");
  include("../include/OddcastException.php");
  include("../include/Voices/Voice.php");
  include("../include/Voices/Italian/Raffaele.php");


  if($params[3] != "LAP-token") exit();

  if($params[4] == "stazione"){
    if($params[5] == "partenze"){
      if($params[6] == "treni"){
        $data = array();
        $now = date("H:i:s");
        $trains = $db->query("SELECT *, IF(CURRENT_TIME > DATE_SUB(DATE_ADD(OrarioPrevistoPartenza, INTERVAL Ritardo MINUTE), INTERVAL 2 MINUTE), 1, 0) AS InPartenza FROM treni JOIN trenistazioni ON treni.NumTreno = trenistazioni.NumTreno JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione JOIN societa ON treni.IDSocieta = societa.IDSocieta JOIN tipitreno ON tipitreno.IDTipoTreno = treni.IDTipoTreno JOIN trenigiorno ON trenigiorno.NumTreno = trenistazioni.NumTreno AND trenigiorno.Giorno = CURRENT_DATE() WHERE trenistazioni.IDStazione = " . $params[7] . " AND TipoStazione <> 3 AND CURRENT_TIME <= DATE_ADD(OrarioPrevistoPartenza, INTERVAL Ritardo+2 MINUTE) ORDER BY OrarioPrevistoPartenza") or die ($db->error);
        while($row = mysqli_fetch_array($trains)){
          $destinazione = mysqli_fetch_array($db->query("SELECT * FROM trenistazioni JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione WHERE NumTreno = " . $row['NumTreno'] . " AND TipoStazione = 3 LIMIT 1"))['NomeDisplay'];
          if(!isset($row['Ritardo'])) $ritardo = 0; else $ritardo = $row['Ritardo'];
          $orario = new DateTime($row['OrarioPrevistoPartenza']);
          $orario->add(new DateInterval('PT' . $ritardo . 'M'));
          $orario = $orario->format('H:i');
          $train = array(
            "Societa" => $row['NomeSocieta'],
            "SocietaImg" => $row['ImgSocieta'],
            "TipoTreno" => $row['NomeTipoTreno'],
            "TipoTrenoImg" => $row['ImgTipoTreno'],
            "NumTreno" => $row['NumTreno'],
            "Destinazione" => $destinazione,
            "Orario" => date("H:i", strtotime($row['OrarioPrevistoPartenza'])),
            "Ritardo" => $ritardo,
            "Binario" => $row['BinarioPrevisto'],
            "InPartenza" => $row['InPartenza']
          );
          array_push($data, $train);
        }
        echo json_encode($data);
      }

      if($params[6] == "annunci"){

        $partenze = mysqli_fetch_array($db->query("SELECT * FROM treni JOIN trenistazioni ON treni.NumTreno = trenistazioni.NumTreno JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione JOIN societa ON treni.IDSocieta = societa.IDSocieta JOIN tipitreno ON tipitreno.IDTipoTreno = treni.IDTipoTreno JOIN trenigiorno ON trenigiorno.NumTreno = trenistazioni.NumTreno AND trenigiorno.Giorno = CURRENT_DATE() WHERE trenistazioni.IDStazione = " . $params[7] . " AND TipoStazione <> 3 AND CURRENT_TIME < DATE_ADD(OrarioPrevistoPartenza, INTERVAL Ritardo+1 MINUTE) AND CURRENT_TIME > DATE_SUB(DATE_ADD(OrarioPrevistoPartenza, INTERVAL Ritardo MINUTE), INTERVAL 3 MINUTE) LIMIT 1"));
        if(!isset($partenze['NomeTipoTreno'])) exit();
        $destinazione = mysqli_fetch_array($db->query("SELECT * FROM trenistazioni JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione WHERE NumTreno = " . $partenze['NumTreno'] . " AND TipoStazione = 3 LIMIT 1"))['NomeStazione'];

        if($partenze['Ritardo'] == 0){
          $tipoannuncio = "P1";
          $ritardo = "";
        } else {
          $tipoannuncio = "P1"; //Da cambiare in P5
          $ritardo = ", in ritardo,";
        }
		    $db->query("SET NAMES utf8");
        $annuncio = mysqli_fetch_array($db->query("SELECT * FROM annunci WHERE CodAnnuncio = \"" . $tipoannuncio . "\""))['Testo'];
        //Immissione parole nell'annuncio
        $find = ["_TIPOTRENO", "_NUMERO", "_IMPRESA", "_ORAPARTENZA", "_DESTINAZIONE", "_RITARDO", "_BINARIO"];
        $replace = [$partenze['NomeTipoTreno'], trim(chunk_split($partenze['NumTreno'],2, ' ')), $partenze['NomeSocieta'], $partenze['OrarioPrevistoPartenza'], $destinazione, $ritardo, $partenze['BinarioPrevisto']];
        $annuncio = str_replace($find, $replace, $annuncio);
        //Il treno _TIPOTRENO , _NUMERO , di _IMPRESA , delle ore _ORAPARTENZA , per _DESTINAZIONE , è in partenza , _RITARDO dal binario _BINARIO
        $tts = new Oddcast(Raffaele::class);
        $url = $tts->setText($annuncio);
        $url = $tts->getUrl();
        $url = array("Url" => $url);
        echo json_encode($url);
      }
    }

    if($params[5] == "arrivi"){
      if($params[6] == "treni"){
        $data = array();
        $now = date("H:i:s");
        $trains = $db->query("SELECT * FROM treni JOIN trenistazioni ON treni.NumTreno = trenistazioni.NumTreno JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione JOIN societa ON treni.IDSocieta = societa.IDSocieta JOIN tipitreno ON tipitreno.IDTipoTreno = treni.IDTipoTreno JOIN trenigiorno ON trenigiorno.NumTreno = trenistazioni.NumTreno AND trenigiorno.Giorno = CURRENT_DATE() WHERE trenistazioni.IDStazione = " . $params[7] . " AND TipoStazione <> 3 AND CURRENT_TIME <= DATE_ADD(OrarioPrevistoArrivo, INTERVAL Ritardo MINUTE) ORDER BY OrarioPrevistoArrivo") or die ($db->error);
        while($row = mysqli_fetch_array($trains)){
          $provenienza = mysqli_fetch_array($db->query("SELECT * FROM trenistazioni JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione WHERE NumTreno = " . $row['NumTreno'] . " AND TipoStazione = 1 LIMIT 1"))['NomeDisplay'];
          if(!isset($row['Ritardo'])) $ritardo = 0; else $ritardo = $row['Ritardo'];
          $orario = new DateTime($row['OrarioPrevistoArrivo']);
          $orario->add(new DateInterval('PT' . $ritardo . 'M'));
          $orario = $orario->format('H:i');
          $train = array(
            "Societa" => $row['NomeSocieta'],
            "SocietaImg" => $row['ImgSocieta'],
            "TipoTreno" => $row['NomeTipoTreno'],
            "TipoTrenoImg" => $row['ImgTipoTreno'],
            "NumTreno" => $row['NumTreno'],
            "Provenienza" => $provenienza,
            "Orario" => date("H:i", strtotime($row['OrarioPrevistoArrivo'])),
            "Ritardo" => $ritardo,
            "Binario" => $row['BinarioPrevisto']
          );
          array_push($data, $train);
        }
        echo json_encode($data);
      }

      if($params[6] == "annunci"){

        $arrivi = mysqli_fetch_array($db->query("SELECT * FROM treni JOIN trenistazioni ON treni.NumTreno = trenistazioni.NumTreno JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione JOIN societa ON treni.IDSocieta = societa.IDSocieta JOIN tipitreno ON tipitreno.IDTipoTreno = treni.IDTipoTreno JOIN trenigiorno ON trenigiorno.NumTreno = trenistazioni.NumTreno AND trenigiorno.Giorno = CURRENT_DATE() WHERE trenistazioni.IDStazione = " . $params[7] . " AND TipoStazione <> 1 AND CURRENT_TIME < DATE_ADD(OrarioPrevistoArrivo, INTERVAL Ritardo MINUTE) AND CURRENT_TIME > DATE_SUB(DATE_ADD(OrarioPrevistoArrivo, INTERVAL Ritardo MINUTE), INTERVAL 3 MINUTE) LIMIT 1"));
        if(!isset($arrivi['NomeTipoTreno'])) exit();
        $provenienza = mysqli_fetch_array($db->query("SELECT * FROM trenistazioni JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione WHERE NumTreno = " . $arrivi['NumTreno'] . " AND TipoStazione = 1 LIMIT 1"))['NomeStazione'];
        $destinazione = mysqli_fetch_array($db->query("SELECT * FROM trenistazioni JOIN stazioni ON trenistazioni.IDStazione = stazioni.IDStazione WHERE NumTreno = " . $arrivi['NumTreno'] . " AND TipoStazione = 3 LIMIT 1"));

        if($arrivi['Ritardo'] == 0){
          $tipoannuncio = "A1";
          $ritardo = "";
        } else {
          $tipoannuncio = "A1"; //Da cambiare in A3
          $ritardo = ", in ritardo,";
        }
        if(isset($destinazione['NomeStazione'])) $destinazione = ", e diretto a " . $destinazione['NomeStazione']; else $destinazione = "";
		    $db->query("SET NAMES utf8");
        $annuncio = mysqli_fetch_array($db->query("SELECT * FROM annunci WHERE CodAnnuncio = \"" . $tipoannuncio . "\""))['Testo'];
        //Immissione parole nell'annuncio
        $find = ["_TIPOTRENO", "_NUMERO", "_IMPRESA", "_ORAARRIVO", "_PROVENIENZA", "_DESTINAZIONE", "_BINARIO"];
        $replace = [$arrivi['NomeTipoTreno'], trim(chunk_split($arrivi['NumTreno'],2, ' ')), $arrivi['NomeSocieta'], $arrivi['OrarioPrevistoArrivo'], $provenienza, $destinazione, $arrivi['BinarioPrevisto']];
        $annuncio = str_replace($find, $replace, $annuncio);
        //Il treno _TIPOTRENO , _NUMERO , di _IMPRESA , delle ore _ORAARRIVO , proveniente da _PROVENIENZA _DESTINAZIONE , è in arrivo al binario _BINARIO . Attenzione! Allontanarsi dalla linea gialla
        $tts = new Oddcast(Raffaele::class);
        $url = $tts->setText($annuncio);
        $url = $tts->getUrl();
        $url = array("Url" => $url);
        echo json_encode($url);
      }
    }
  }
 ?>
