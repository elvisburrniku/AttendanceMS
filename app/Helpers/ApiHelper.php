<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

class ApiHelper
{
    protected $url = "";
    protected $ip = "46.99.253.82:8089";
    protected $client = null;
    public $response = [];

    public function __contruct()
    {
        $this->response = collect();
    }

    public function url($url) {
        $this->url = $url;

        return $this;
    }

    public function get() {
        $response = Http::withBasicAuth('edmond', 'Edmond@1994')->accept('application/json')->get("http://$this->ip/$this->url");
        
        if($response->successful()) {
            $this->response = collect($response->json());
            return collect($response->json());
        } else if ($response->failed()) {
            // Handle error
            $statusCode = $response->status(); // Get the status code
            // You can also get the error message from the response body if it's JSON
            $errorMessage = $response->json();

            return $errorMessage;
        }
    }

    public function post($data) {
        $response = Http::withBasicAuth('edmond', 'Edmond@1994')->accept('application/json')->post("http://$this->ip/$this->url", $data);

        if($response->successful()) {
            $this->response = collect($response->json());
            return collect($response->json());
        } else if ($response->failed()) {
            // Handle error
            $statusCode = $response->status(); // Get the status code
            // You can also get the error message from the response body if it's JSON
            $errorMessage = $response->json();

            return $errorMessage;
        }

    }

    public function put($id, $data) {
        $response = Http::withBasicAuth('edmond', 'Edmond@1994')->accept('application/json')->put("http://$this->ip/$this->url/$id/?page=1", $data);

        if($response->successful()) {
            $this->response = collect($response->json());
            return collect($response->json());
        } else if ($response->failed()) {
            // Handle error
            $statusCode = $response->status(); // Get the status code
            // You can also get the error message from the response body if it's JSON
            $errorMessage = $response->json();

            return $errorMessage;
        }
    }

    public function delete($id) {
        $response = Http::withBasicAuth('edmond', 'Edmond@1994')->accept('application/json')->delete("http://$this->ip/$this->url/$id/?page=1");

        if($response->successful()) {
            $this->response = collect($response->json());
            return collect($response->json());
        } else if ($response->failed()) {
            // Handle error
            $statusCode = $response->status(); // Get the status code
            // You can also get the error message from the response body if it's JSON
            $errorMessage = $response->json();

            return $errorMessage;
        }
    }

    public function getData() {
        return collect($this->response->get('data'));
    }

}