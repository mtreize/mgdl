<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  function search($query){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://www.files-seekr.com/api/search/torrents/".$query,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
       CURLOPT_VERBOSE, true,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "postman-token: 5976eaad-770e-f56c-44ea-164b8aeaa924",
        "Cookie: isApp=0"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
  
    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      return $response;
    }
  }

function searchByTerme($search)
{
    $ygg = new Ygg($search);
    if ($ygg->login()) {
        $ygg->searchTorrent();
        return $ygg->getTorrents();
        
    } else {
        echo 'Unable to login. Please check your credentials';
    }
}
function download($file)
{
    $ygg = new Ygg();
    if ($ygg->download($file)) {
        echo 'Torrent downloaded';
    }
}

  

?>