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
    $filters['include_fields'] = 'id,product_id,display_name,type,option_values';
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

  public function deleteProductModifier($productId, $modifierId) 
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/modifiers/" . $modifierId,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "DELETE",
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

  public function getProduct($productId)
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId,
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


  public function getAllProductVariants($productId)
  {
    $filters['limit'] = 250;
    $filters['include_fields'] = 'id,product_id,sku';
    $url_query = http_build_query($filters);

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/variants?" . $url_query,
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


  public function createProductVariant($productId, $data)
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/variants",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
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

  public function getProductOptions($productId)
  {
    $filters['limit'] = 250;
    $url_query = http_build_query($filters);

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/options?" . $url_query,
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

  public function createProductOption($productId, $data)
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/options",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
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

  public function deleteProductOption($productId, $optionId)
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->api_url . "/v3/catalog/products/" . $productId . "/options/" . $optionId,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "DELETE",
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