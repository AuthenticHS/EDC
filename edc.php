<?php
/*
 emuparadise.me Downloader CLI
 https://mit-license.org
 10 2020 - Hmwr466
*/
set_time_limit(0);
error_reporting(0);

$cli = php_sapi_name() === 'cli';
if (!$cli) {
  echo 'Untuk penggunaan CLI saja';
  exit(-1);
}

echo "\033[1mMasukan link ISO / game ID: \e[0m";
$input = trim(fgets(STDIN));

if (!preg_match('/^[0-9]+$/', $input)) {
  $parsepath = parse_url($input)["path"];
  $patharr = explode("/", $parsepath);
  $gameid = $patharr["3"]; // contoh URL: https://www.emuparadise.me/Sony_Playstation_2_ISOs/ICO_(USA)/150725
} else {
  $gameid = $input;
}

if (preg_match('/^[0-9]+$/', $gameid)) {
  $url = "https://www.emuparadise.me/roms/get-download.php?test=true&gid=$gameid";
  $tempfile = fopen('php://memory', 'r+');
  $cinit = curl_init();
  curl_setopt($cinit, CURLOPT_TIMEOUT, "20");
  curl_setopt($cinit, CURLOPT_POST, true);
  curl_setopt($cinit, CURLOPT_URL, "$url");
  curl_setopt($cinit, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36"); // selene
  curl_setopt($cinit, CURLOPT_REFERER, "$url");
  curl_setopt($cinit, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($cinit, CURLOPT_VERBOSE, true);
  curl_setopt($cinit, CURLOPT_STDERR, $tempfile);
  curl_setopt($cinit, CURLOPT_HEADER, true);

  $response = curl_exec($cinit);
  $header_size = curl_getinfo($cinit, CURLINFO_HEADER_SIZE);
  $header = substr($response, 0, $header_size);
  curl_close($cinit);
  if (preg_match_all("/location\:\ ([^\r\n]+)/i", $header, $link)) {
    $link = str_replace(" ", "%20", $link[1][0]);
    echo "\r\n\r\n\033[1mHasil: \e[0m\r\n".$link;
  } else {
    echo "\r\n\r\n\033[1mGagal!\e[0m\r\nCoba lagi, cek link / game ID";
  }
} else {
    echo "\r\n\r\n\033[1mError!\e[0m\r\nInputan / link tidak valid";
}
// EOL
