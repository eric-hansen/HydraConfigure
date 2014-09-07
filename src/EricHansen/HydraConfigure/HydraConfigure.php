<?php
/**
 * This project is intended to wrap around the API for http://elt.li.  Config storage server!
 */
namespace EricHansen\HydraConfigure;

class HydraConfigure {
    /**
     * Can pass either the ID and secret or a file ocntaining these.
     *
     * @param string $client_id The client ID generated via the API
     * @param string $client_secret Client secret generated via API
     * @param string $config_file File containing client_id and client_secret JSON entries
     */
    public function __construct($client_id = "", $client_secret = "", $config_file = "config.json"){
        if(!function_exists('curl_init')){
            die("PHP-cURL needs to be installed!");
        }

        if(empty($client_id) && empty($client_secret) && !empty($config_file)){
            $conf = @json_decode(file_get_contents($config_file));
        } else{
            $conf = (object)array("id" => $client_id, "secret" => $client_secret);
        }

        $this->token = null;

        if($conf){
            $auth = $this->_api_call("oauth/token", array(
                "grant_type" => "client_credentials",
                "client_id" => $conf->id,
                "client_secret" => $conf->secret), "POST");

            if($auth->http_code < 400)
                $this->token = $auth->access_token;

            unset($conf);
        }
    }

    /**
     * @return mixed If the token was established, return the access token, else null (not authenticated)
     */
    public function getToken(){
        return $this->token;
    }

    /**
     * @param $verb The HTTP verb to process (GET/POST/etc...)
     * @param $args Any arguments to pass ($args[0] must be the API path [everything after api/])
     * @return mixed Results data from calling the API
     */
    public function __call($verb, $args){
        $uri = "api/";

        if(isset($args[0])){
            $uri .= $args[0];
            unset($args[0]);
        }

        $verb = strtoupper($verb);

        return $this->_api_call($uri, $args, $verb);
    }

    /**
     * @param $uri URI to access for API (i.e.: oauth/token for generating a new access token)
     * @param array $data Any data to be passed to the call
     * @param string $method Verb to call (GET/POST/DELETE/etc...)
     * @return mixed Object of JSON-formatted response by ELI.it server
     */
    private function _api_call($uri, $data = array(), $method="GET"){
        $args = $data;
        $this->raw_args = $data;

        if($method == "GET"){
            if(substr($uri, -1) != "/")
                $uri .= "/";

            $uri .= implode(",", $args);
            $args = null;
        }

        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => $method
        );

        if($args && $method != "GET")
            $curl_opts[CURLOPT_POSTFIELDS] = http_build_query($args);

        /**
         * Add Bearer auth token HTTP header here...
         */
        if(isset($this->token))
            $curl_opts[CURLOPT_HTTPHEADER] = array("Authorization: Bearer ".$this->token);

        $this->url = "http://elt.li/" . $uri;

        $ch = curl_init($this->url);
        curl_setopt_array(
            $ch,
            $curl_opts
        );

        $resp = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $resp = json_decode($resp);

        if($resp)
            $resp->http_code = $http_code;
        else{
            $resp = (object)array('http_code' => $http_code, 'res' => $resp);
        }

        return $resp;
    }

    public function getLastRequest(){
        $dump = new \stdClass();
        $dump->url = $this->url;
        $dump->token = $this->token;
        $dump->raw_data = $this->raw_args;

        return $dump;
    }
}
