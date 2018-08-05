<?php

/*
Check if url exist. 200 ok.
*/
function urlOK($url)
{
    $headers = @get_headers($url);
    if(strpos($headers[0],'200')===false){return false;}else{return true;}
}

/*
check if rut is valid RUT válido
*/
function isRut($r = false)
{
    if((!$r) or (is_array($r)))
        return false; /* Hace falta el rut */
 
    if(!$r = preg_replace('|[^0-9kK]|i', '', $r))
        return false; /* Era código basura */
 
    if(!((strlen($r) == 8) or (strlen($r) == 9)))
        return false; /* La cantidad de carácteres no es válida. */
 
    $v = strtoupper(substr($r, -1));
    if(!$r = substr($r, 0, -1))
        return false;
 
    if(!((int)$r > 0))
        return false; /* No es un valor numérico */
 
    $x = 2; $s = 0;
    for($i = (strlen($r) - 1); $i >= 0; $i--){
        if($x > 7)
            $x = 2;
        $s += ($r[$i] * $x);
        $x++;
    }
    $dv=11-($s % 11);
    if($dv == 10)
        $dv = 'K';
    if($dv == 11)
        $dv = '0';
    if($dv == $v)
        return number_format($r, 0, '', '.').'-'.$v; /* Formatea el RUT */
    return false;
}

/*
Check for url alias
$string = Contebt to url url
*/
function cleanAliasUrl($string)
{
    $tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'); 
    $en = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'); 
    $string = str_replace ($tr, $en, $string); 
    $string = preg_replace ("`\[.*\]`U", "", $string); 
    $string = preg_replace ('`&(amp;)?#?[a-z0-9]+;`i', '-', $string); 
    $string = htmlentities ($string, ENT_COMPAT, 'utf-8'); 
    $string = preg_replace ("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string); 
    $string = preg_replace (array("`[^a-z0-9]`i", "`[-]+`"), "-", $string); 

    return strtolower (trim ($string, '-')); 
}

/*
code-decode to Base64
$plainText = text to code
*/
function base64url_encode($plainText) 
{
    $base64 = base64_encode($plainText);
    $base64url = strtr($base64, '+/=', '-_,');
    return $base64url;
}

 function base64url_decode($plainText) 
{
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;
}

/*
Check Email format 
Use PregMatch
*/
function isEmail($email)
{
    if(preg_match("~([a-zA-Z0-9!#$%&'*+-/=?^_`{|}~])@([a-zA-Z0-9-]).([a-zA-Z0-9]{2,4})~",$email)) 
    {
       return true;
    }else{
       return false;
    }    
}

/*
Check date format in this format YYYY-MM-DD.
*/
function checkDateFormat($date)
{
    //match the format of the date
    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
    {
        //check weather the date is valid of not
        if(checkdate($parts[2],$parts[3],$parts[1]))
            return true;
        else
        return false;
    }
    else
        return false;
}

/*
Get browser user client
*/
function getUserBrowser()
{
    $useragent = $_SERVER ['HTTP_USER_AGENT'];  
    return $useragent;
}

/*
Check if is https experimental
*/
function isHttps()
{
    //Not work in all servers
    /*
    if ($_SERVER['HTTPS'] !== "on") { 
        return false;
    }else{
        return true;
    }
    */
    return false;
}

/*
make a  CVS from ARRAY
*/
function generateCsv($data, $delimiter = ',', $enclosure = '"') {
    //Where you go to save the result
   $handle = fopen('php://temp', 'r+');
   foreach ($data as $line) {
           fputcsv($handle, $line, $delimiter, $enclosure);
   }
   rewind($handle);
   while (!feof($handle)) {
           $contents .= fread($handle, 8192);
   }
   fclose($handle);
   return $contents;
}

/* 
Gest distance to 2 points
example
-------------------
$point1 = array('lat' => 40.770623, 'long' => -73.964367);
$point2 = array('lat' => 40.758224, 'long' => -73.917404);
$distance = getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
foreach ($distance as $unit => $value) {
    echo $unit.': '.number_format($value,4).'<br />';
}
-------------------
Return
miles: 2.6025
feet: 13,741.4350
yards: 4,580.4783
kilometers: 4.1884
meters: 4,188.3894
*/

function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
    $theta = $longitude1 - $longitude2;
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('miles','feet','yards','kilometers','meters'); 
}


/*
Clean Basic Input Text
*/
function cleanInput($input) 
{
    $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );
    $output = preg_replace($search, '', $input);
    return $output;
}
/*Sanitize Basic Input Text
*/
function sanitize($input) {
    if (is_array($input)) {
       foreach($input as $var=>$val) {
           $output[$var] = sanitize($val);
       }
    }else{
       if (get_magic_quotes_gpc()) {
           $input = stripslashes($input);
       }
       $input  = cleanInput($input);
       $output = mysql_real_escape_string($input);
    }
    return $output;
}

/*
Get current Base_url 
*/
function baseUrl(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/*
OPCIONES DE DESARROLLADOR ON
*/
function developMode($path)
{
    if(SETUP_SHOW_PATH)
    {
       echo "<h6>{".$path."}</h6>";
    }
}

/**************
*@length - length of random string (must be a multiple of 2)!!!!
**************/
function readable_random_string($length = 6){
    $conso=array("b","c","d","f","g","h","j","k","l",
    "m","n","p","r","s","t","v","w","x","y","z");
    $vocal=array("a","e","i","o","u");
    $password="";
    srand ((double)microtime()*1000000);
    $max = $length/2;
    for($i=1; $i<=$max; $i++)
    {
    $password.=$conso[rand(0,19)];
    $password.=$vocal[rand(0,4)];
    }
    return $password;
}

/*************
*@l - length of random string using timestamp
*/
function readable_random_string_timestamp($l = 6){
  $c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  srand((double)microtime()*1000000);
  for($i=0; $i<$l; $i++) {
    $rand.= $c[rand()%strlen($c)];
  }
  return $rand;
 }


/*
Obtiene la ip real del cliente
*/
function getRealIpAddr()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  //to check ip is pass from proxy
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip=$_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}



/*
Guarda un vardump en un archivo
*/

function deployVardump($vartodump,$filename){
  ob_flush();
  ob_start();
  var_dump($vartodump);
  file_put_contents($filename, ob_get_flush());
  ob_end_flush();
}

/*
Obtiene la UF , 
primero la guarda en un archivo con la fecha dentro de una carpeta llamada UF
Retorna el valor de la UF
*/
function nombre_proyecto_getUF(){
  if (!file_exists('uf')) {
      mkdir("uf/", 0777);
  }

  if ( file_exists('uf/'.date('Ymd').'.txt') ){
    $fichero = file_get_contents('uf/'.date('Ymd').'.txt', true);
    return $fichero;
  }else{
    $apiUrl = 'https://mindicador.cl/api';
    //Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
    if ( ini_get('allow_url_fopen') ) {
        $json = file_get_contents($apiUrl);
    } else {
        //De otra forma utilizamos cURL
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
    }
    $dailyIndicators = json_decode($json);
    $uf = $dailyIndicators->uf->valor;
    $str = 'uf/'.date('Ymd').'.txt';
    $out = fopen($str, "w");
    fwrite($out, $uf);
    fclose($out);
    return $uf;
  }
}


/*
     ,-------,  ,  ,   ,-------,
      )  ,' /(  |\/|   )\ ',  (
       )'  /  \ (qp)  /  \  '(
        ) /___ \_\/(_/ ___\ (
         '    '-(   )-'    '
                )w^w(
                (W_W)
                 ((
                  ))
                 ((
                  )  JZB.
                  Basic MSQLI Model CLass
                  JaimeZegpi@yahoo.es
*/
?>