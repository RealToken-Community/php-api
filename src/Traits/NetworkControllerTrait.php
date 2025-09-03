<?php

namespace App\Traits;

trait NetworkControllerTrait
{
  /**
   * Make cURL request.
   *
   * @param string $uri
   * @param bool $decode
   * @param array $headers
   *
   * @return bool|mixed|string
   */
  public function curlRequest(string $uri, bool $decode = false, array $headers = []): mixed
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $uri,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if ($decode) {
      return json_decode($response, true);
    }

    return $response;
  }
}
