<?php

namespace App\Services;


class BigcommerceApiService
{
  private $storeHash, $storeToken, $api_url;

  public function __construct()
  {
    $this->storeHash = env('BC_DEV_STORE_HASH');
    $this->storeToken = env('BC_DEV_ACCESS_TOKEN');
    $this->api_url = 'https://api.bigcommerce.com/stores/' . $this->storeHash;
  }

  public function getAllProducts($limit = 250, $page = 1) 
  {   
    $filters['include_fields'] = 'id,name';
    $filters['limit'] = $limit;
    $filters['page'] = $page;
    $url_query = http_build_query($filters);

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products?" . $url_query,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Auth-Token: " . $this->storeToken
      ],
    ]);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return null;
    } else {
      return $info['http_code'] === 200
        ? json_decode($response, true)
        : null;
    }
  }

  public function getProductModifiers($productId)
  {
    $filters['limit'] = 250;
    $filters['include_fields'] = 'id,product_id,option_values';
    $url_query = http_build_query($filters);

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/modifiers?" .  $url_query,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Auth-Token: " . $this->storeToken
      ],
    ]);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return null;
    } else {
      return $info['http_code'] === 200
        ? json_decode($response, true)
        : null;
    }
  }


}