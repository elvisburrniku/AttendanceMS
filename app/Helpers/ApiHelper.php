<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

class ApiHelper
{
    protected $url = "";
    protected $ip = "";
    protected $username = "";
    protected $password = "";
    protected $client = null;
    public $response = [];

    public function __construct()
    {
        $this->response = collect();
        $this->ip = config("app.server_ip");
        $this->username = config("app.server_username");
        $this->password = config("app.server_password");

        // Debugging
        if (is_null($this->ip) || is_null($this->username) || is_null($this->password)) {
            throw new \Exception('Environment variables not set correctly');
        }
    }

    public function url($url) {
        $this->url = $url;

        return $this;
    }

    public function get() {
        $response = Http::withBasicAuth($this->username, $this->password)->accept('application/json')->get("http://$this->ip/$this->url");
        
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
        // dd($this->username, $this->password, "http://$this->ip/$this->url", $data);
        $response = Http::withBasicAuth($this->username, $this->password)->accept('application/json')->post("http://$this->ip/$this->url", $data);

        if($response->successful()) {
            $this->response = collect($response->json());
            return collect($response->json());
        } else if ($response->failed()) {
            // Handle error
            $statusCode = $response->status(); // Get the status code
            // You can also get the error message from the response body if it's JSON
            $errorMessage = $response->json();

            $errorMessage = json_encode($errorMessage);

            throw new \Error($errorMessage);

            return $errorMessage;
        }

    }

    public function put($id, $data) {
        $response = Http::withBasicAuth($this->username, $this->password)->accept('application/json')->put("http://$this->ip/$this->url/$id/?page=1", $data);

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
        $response = Http::withBasicAuth($this->username, $this->password)->accept('application/json')->delete("http://$this->ip/$this->url/$id/?page=1");

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