<?php

namespace Zenapply\Calendly;

use Zenapply\Calendly\Exceptions\CalendlyException;
use Zenapply\Request\CurlRequest;
use Zenapply\Request\HttpRequest;

class Calendly {
    const V1 = "v1";

    /**
     * API Host
     * @var string
     */
    protected $host;

    /**
     * API Token
     * @var string
     */
    protected $token;

    /**
     * API Version to use
     * @var string
     */
    protected $version;

    /**
     * The HttpRequest instance that will handle the request
     * @var HttpRequest
     */
    protected $request;

    /**
     * Creates a Calendly instance that can register and unregister webhooks with the API
     * @param string $token   The API token to use
     * @param string $version The API version to use
     * @param string $host    The Host URL
     * @param string $request The HttpRequest instance that will handle the request
     */
    public function __construct($token,$version = self::V1,$host = "calendly.com", HttpRequest $request = null){
        $this->request = $request;
        $this->token = $token;
        $this->version = $version;
        $this->host = $host;
    }

    /**
     * Will register a webhook url for the both invitee events
     * @param  string $url The webhook url you want to use
     * @return array       The response array
     */
    public function registerAllInviteeEvents($url){
        return $this->register(['url'=>$url,'events'=>['invitee.created','invitee.canceled']]);
    }

    /**
     * Will register a webhook url for the invitee.created event
     * @param  string $url The webhook url you want to use
     * @return array       The response array
     */
    public function registerInviteeCreated($url){
        return $this->register(['url'=>$url,'events'=>['invitee.created']]);
    }

    /**
     * Will register a webhook url for the invitee.canceled event
     * @param  string $url The webhook url you want to use
     * @return array       The response array
     */
    public function registerInviteeCanceled($url){
        return $this->register(['url'=>$url,'events'=>['invitee.canceled']]);
    }

    /**
     * Sends a request to delete a hook
     * @param  integer|string $id The ID of the hook you want to delete
     * @return null           returns null when successful
     */
    public function unregister($id){
        $url = $this->buildUrl("hooks") . "/" . $id;
        return $this->exec($url,null,"DELETE");
    }

    /**
     * Sends a request to create a hook with the provided data
     * @param  array $data The data you want to include in the POST request
     * @return array       The response array
     */
    protected function register($data){
        $url = $this->buildUrl("hooks");
        $data = $this->buildPostFields($data);
        return $this->exec($url,$data);
    }

    /**
     * Converts an array into a post fields string for CURL
     * @param  array  $data The data you want to convert
     * @return string
     */
    protected function buildPostFields(array $data){
        $data = http_build_query($data);
        return preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $data);
    }

    /**
     * Builds the URL from the host and version properties
     * @param  string $action The API action you want to execute
     * @return string         
     */
    protected function buildUrl($action = "hooks"){
        return "https://{$this->host}/api/{$this->version}/{$action}";
    }

    /**
     * Returns the HttpRequest instance
     * @param  string $url The URL to request
     * @return HttpRequest
     */
    protected function getRequest($url){
        $request = $this->request;
        if(!$request instanceof HttpRequest){
            $request = new CurlRequest($url);
        }
        return $request;
    }

    /**
     * Executes a CURL command to the Calendly API
     * @param  string $url    The URL to send to
     * @param  string $data   Data from the http_build_query function
     * @param  string $method POST, GET, DELETE, PUT, etc...
     * @return array          The response data as an assoc array
     */ 
    protected function exec($url = "", $data = null, $method = "POST"){
        $request = $this->getRequest($url);

        $request->setOptionArray([
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "X-Token: {$this->token}"
            ],
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_VERBOSE => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $result = $request->execute();
        $code   = $request->getInfo(CURLINFO_HTTP_CODE);
        
        $request->close();

        return $this->handleResponse($result,$code);
    }

    /**
     * Returns the response as an array or throws an Exception if it was unsuccessful
     * @param  string  $result JSON string from the response
     * @param  integer $code   The HTTP response code
     * @return array
     */
    protected function handleResponse($result,$code){
        $data = json_decode($result, true);
        if($code>=200 && $code<300) {
            return $data;
        } else {
            throw new CalendlyException($data['message']);
        }
    }
}