<?php

  function curl_get_file_contents_custom($URL)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents)
      return $contents;
    else
      return FALSE;
  }

  function curl_get_html_file_contents_custom($url)
  {

    $headers[]  = "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:30.0) " .
                  "Gecko/20140610 Firefox/30.0";
    $headers[]  = "Accept: text/html;q=0.8, application/xhtml+xml;q=1, "
                . "application/xml;q=0.9,*/*;q=0.7";
    $headers[]  = "Accept-Language: en-us;q=1, en-gb;q=0.8, en;q=0.5";
    $headers[]  = "Accept-Encoding: gzip;q=1, deflate;q=0.5, compress;q=0.3, " .
                  "identity;q=0.1";
    $headers[]  = "Accept-Charset: utf-8;q=1, ISO-8859-1;q=0.7, *;q=0.6";
    $headers[]  = "DNT: 1";
    $headers[]  = "Keep-Alive:115";
    $headers[]  = "Connection:keep-alive";
    $headers[]  = "Cache-Control:max-age=0";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
  }

  //@formatter:off
  $sports = curl_get_html_file_contents_custom (
    'http://www.edinburgh.gov.uk/api/directories/25/entries.xml?api_key=' .
    COUNCIL_API_KEY . '&per_page=100&page=1' );
  //@formatter:on

  $sports = new SimpleXMLElement($sports);

  foreach ( $sports->xpath( '//entry[fields/field[@name=\'Activities\'][contains(text(),\'Football\')]][fields/field[@name=\'Opening hours\'][contains(text(),\'Monday\')]]' ) as $club )
  {
      $address = $club->xpath('fields/field[@name=\'Address\']/text()');
      if ( count ( $address ) )
        echo $club->title, '  at  ', $address [0], '<br />', PHP_EOL;
      else
        echo $club->title, '<br />', PHP_EOL;
  }

?>
