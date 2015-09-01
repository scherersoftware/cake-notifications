<?php

if (version_compare(PHP_VERSION, '5.2.0') < 0) {
    //------------------------------------------------------------
    // For PHP < 5.2.0 Services_JSON is used for encoding/decoding
    // Get it from PEAR or http://mike.teczno.com/JSON/JSON.phps
    // See http://pear.php.net/pepr/pepr-proposal-show.php?id=198
    //------------------------------------------------------------
    if (!class_exists("Services_JSON")) {
      require_once('JSON.phps'); // JSON service for JsonWrapper.
    }
}

class WebSmsCom_JsonWrapper {
    //------------------------------------------------------------
    // Modify this JSON Wrapper if you want to use a different
    // JSON service or influence JSON encoding/decoding.
    //------------------------------------------------------------
    private $json_service     = null;
    
    function __construct() {
        if (class_exists("Services_JSON")) {
            $this->json_service = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            if (version_compare(PHP_VERSION, '5.2.0') < 0) {
                trigger_error(__CLASS__ . " requires Services_JSON or similar JSON encoder for PHP_VERSION below 5.2.0.");
            }
        }
    }
    function encode($php_array) {
        return (!is_null($this->json_service)) 
                ? $this->json_service->encode($php_array) 
                : json_encode($php_array); // php >= 5.2.0
    }
    function decode($json) {
        return (!is_null($this->json_service)) 
                ? $this->json_service->decode($json) 
                : (array) json_decode($json); // php >= 5.2.0
    }
}

//-----------------------------------------------------------------
// sms.at Gateway XML interface class
//
// Requires PHP >= 5.0.3
// Max tested version: PHP 5.4.7 VC9
//
// @author Gerd Reifenauer
//-----------------------------------------------------------------
class WebSmsCom_Client {
    private $VERSION              = "1.0.2";
    private $REQUIRED_PHP_VERSION = "5.0.3"; # PHP>=5.0.3
    
    private $username;
    private $pass;
    private $url;
    private $path;
    private $port;
    private $scheme;
    private $host;
    private $verbose;
    
    private $connectionTimeout  = 10;
    private $lastHttpTransfer   = "";
    private $endpoint_json      = "/json/smsmessaging";
    private $endpoint_text      = "/text";
    private $endpoint_binary    = "/binary";
    
    static  $available_http_client_types = array('curl', 'fopen');
    private $httpClientType              = 'curl';
    private $contentType                 = 'application/json;charset=UTF-8';
    private $sslVerifyHost               = 2;
    
    
    //@throws Exception   
    function __construct($user, $pass, $url) {
        $this->initUrl($url);
        
        if ((strlen($this->host) < 4) || !$user || !$pass) {
            throw new WebSmsCom_ParameterValidationException("Invalid call of sms.at gateway class. Check arguments.");
        }
        if (!$this->port) {
            throw new WebSmsCom_ParameterValidationException("Invalid url when calling sms.at gateway class. Missing Port.");
        }
        
        $this->json = new WebSmsCom_JsonWrapper();
        
        $this->username = $user;
        $this->pass     = $pass;
        
    }
    
    private function initUrl($url) {
        // remove trailing slashes from url
        $url = preg_replace('/\/+$/', '', $url);
        
        $parsed_url = parse_url($url);
        $host       = (isset($parsed_url['host']))   ? $parsed_url['host']   : '';
        $path       = (isset($parsed_url['path']))   ? $parsed_url['path']   : '';
        $port       = (isset($parsed_url['port']))   ? $parsed_url['port']   : '';
        $scheme     = (isset($parsed_url['scheme'])) ? $parsed_url['scheme'] : '';
        
        if (!$port && (in_array($scheme, array(
            'https'
        )))) {
            $port = 443;
        }
        if (!$port && (in_array($scheme, array(
            '',
            'http'
        )))) {
            $port = 80;
        }
        
        $this->url    = $url;
        $this->host   = $host;
        $this->path   = $path;
        $this->port   = $port;
        $this->scheme = $scheme;
    }
    
    //-----------------------------------------------
    // send (obj message, int maxSmsPerMessage, bool test)
    //
    //@param $message  - message object of type WebSmsCom_TextMessage or WebSmsCom_BinaryMessage
    //@param $test     - sms will not be sent when true
    //
    //@throws WebSmsCom_AuthorizationFailedException
    //@throws WebSmsCom_ApiException
    //@throws WebSmsCom_HttpConnectionException
    //@throws WebSmsCom_UnknownResponseException
    //@throws Exception    
    //-----------------------------------------------
    function send($message, $maxSmsPerMessage = null, $test = null) {
        
        if (!is_object($message) || !($message instanceof WebSmsCom_Message)) {
            throw new WebSmsCom_ParameterValidationException("Invalid message object given");
        }
        
        if (count($message->getRecipientAddressList()) < 1) {
            throw new WebSmsCom_ParameterValidationException("Missing recipients in message");
        }
        
        if (!is_null($maxSmsPerMessage) && $maxSmsPerMessage == 0) {
            throw new WebSmsCom_ParameterValidationException("maxSmsPerMessage cannot be 0, try null.");
        }
        
        $request_data = $this->build_request_data($message, $maxSmsPerMessage, $test);
        
        $Response = $this->do_request($request_data);
        
        return $Response;
    }
    
    //-------------------------------------------------
    // do_request
    //-------------------------------------------------
    private function do_request($request_data) {
        if ($this->httpClientType == 'curl') {
            $response_data = $this->do_curl_request($request_data);
            
        } elseif ($this->httpClientType == 'fopen') {
            $response_data = $this->do_fopen_request($request_data);
            
        } else {
            throw new Exception("Unsupported sending method (httpClientType)");
            
        }
        
        return $this->build_reponse($response_data);
        
    }
    
    //-------------------------------------------------
    // build_request_data                 
    //-------------------------------------------------
    private function build_request_data($message, $maxSmsPerMessage, $test) {
        $request_data = array();
        
        //TODO: decision for endpoint format json/xml/simple. 
        //      Maybe construct this as RequestObject, so no message cloning 
        
        // contact json endpoint with json data
        $request_data['endpoint_path'] = $this->endpoint_json . (($message instanceof WebSmsCom_BinaryMessage) ? $this->endpoint_binary : $this->endpoint_text);
        $request_data['url']           = $this->url . $request_data['endpoint_path']; # for curl
        $request_data['endpoint_path'] = $this->path . $request_data['endpoint_path']; # for fopen
        
        $request_data['http_header'] = array(
            'Content-Type: ' . $this->contentType,
            'Expect:'
        );
        
        $json_data = $message->getJsonData();
        
        if ($maxSmsPerMessage > 0) {$json_data['maxSmsPerMessage'] = $maxSmsPerMessage;}
        if (is_bool($test))        {$json_data['test'] = $test;}

        $request_data['content']     = $this->json->encode($json_data);
        
        return $request_data;
    }
    
    //-------------------------------------------------
    // build_reponse                 
    //-------------------------------------------------
    private function build_reponse($response_data) {
      
        if (!isset($response_data['http_status']) || $response_data['http_status'] != 200) {
            if (!isset($response_data['http_status']) || $response_data['http_status'] < 1) {
                throw new WebSmsCom_HttpConnectionException("Couldn't connect to remote server");
            } elseif ($response_data['http_status'] == 401) {
                throw new WebSmsCom_AuthorizationFailedException("Basic Authentication failed. Check given username and password. (Account has to be active)");
            } else {
                throw new WebSmsCom_HttpConnectionException("Response HTTP Status: " . $response_data['http_status'] . "\n" . $response_data['content'], $response_data['http_status']);
            }
        }
        
        if (isset($response_data['content_type']) && preg_match('/json/', $response_data['content_type'])) {
            $parsed_content = $this->json->decode($response_data['content']);
        } else {
            throw new WebSmsCom_UnknownResponseException("Received unknown content type '" . $response_data['content_type'] . "'. Content: " . $response_data['content']);
        }
        
        if ($parsed_content['statusCode'] < 2000 || $parsed_content['statusCode'] > 2001) {
            throw new WebSmsCom_ApiException($parsed_content['statusMessage'], $parsed_content['statusCode']);
        }
        
        $Response = new WebSmsCom_Response($response_data['content'], $parsed_content['statusCode'], $parsed_content['statusMessage'], $parsed_content['transferId'], isset($parsed_content['clientMessageId']) ? $parsed_content['clientMessageId'] : NULL);
        
        return $Response;
    }
    
    //-------------------------------------------------
    // do request with curl
    //-------------------------------------------------
    private function do_curl_request($request_data) {
      
        $response_data = array();
        
        if (!extension_loaded('curl')) {
            throw new Exception("cURL is not loaded. Try fsockopen method to send request. Use setter WebSmsCom_Client->setHttpClientType('fopen').");
        }
        $curlinfo_supported              = (version_compare(PHP_VERSION, '5.1.3') >= 0) ? true : false;
        $response_data['request_header'] = "- cURL Request Header not shown for PHP<5.1.3  -\r\n\r\n";
        
        $curlHandle = curl_init($request_data['url']);
        
        curl_setopt($curlHandle,CURLOPT_HTTPAUTH      , CURLAUTH_BASIC);
        curl_setopt($curlHandle,CURLOPT_USERPWD       , $this->username . ":" . $this->pass);
        curl_setopt($curlHandle,CURLOPT_HTTPHEADER    , $request_data['http_header']);
            
        curl_setopt($curlHandle,CURLOPT_POST          , true);
        curl_setopt($curlHandle,CURLOPT_POSTFIELDS    , $request_data['content']);
        curl_setopt($curlHandle,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle,CURLOPT_HEADER        , true);
            
        curl_setopt($curlHandle,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle,CURLOPT_SSL_VERIFYHOST, $this->sslVerifyHost);
        //curl_setopt($curlHandle,CURLOPT_FORBID_REUSE  , true);
        curl_setopt($curlHandle,CURLOPT_FRESH_CONNECT , true);
        curl_setopt($curlHandle,CURLOPT_TIMEOUT       , $this->connectionTimeout);
        curl_setopt($curlHandle,CURLOPT_USERAGENT     , "PHP SDK Client (v" . $this->VERSION . ", PHP" . phpversion() . ")");
            
        curl_setopt($curlHandle,CURLOPT_VERBOSE       , $this->verbose);

        
        if ($curlinfo_supported) {
            curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
        }
        
        $response = curl_exec($curlHandle);
        
        if ($response == false) {
          $response_data['header'] = ''; $response_data['content'] = '';
        } else {
          list($response_data['header'], $response_data['content']) = preg_split('/\r\n\r\n/', $response);
        }
        
        $response_data['http_status']  = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $response_data['content_type'] = curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE);
        
        if ($curlinfo_supported) {
            $response_data['request_header'] = curl_getinfo($curlHandle, CURLINFO_HEADER_OUT);
        }
        
        $this->setLastHttpTransfer($response_data['request_header'] . $request_data['content'], $response);
        
        if ($this->verbose) {
          echo $this->getLastHttpTransfer();
        }
        
        curl_close($curlHandle);
        
        return $response_data;
    }
    
    //-------------------------------------------------
    // do request with fopen
    //-------------------------------------------------
    private function do_fopen_request($request_data) {
      
        $response_data = array();
        
        $fsh = @fsockopen("ssl://" . $this->host, $this->port, $errno, $errstr, $this->connectionTimeout);
        
        if ($errno > 0) {
            if ($this->verbose) {
                echo "\n--fsockopen to port " . $this->port . "--\nerrno: $errno,\nerrstr: $errstr\n--\n";
            }
        }
        
        if (is_resource($fsh)) {
            $header = "";
            $header .= "POST " . $request_data['endpoint_path'] . " HTTP/1.0\r\n";
            $header .= "Authorization: Basic " . base64_encode($this->username . ":" . $this->pass) . "\r\n";
            $header .= "Host: " . $this->host . "\r\n";
            $header .= implode("\r\n", $request_data['http_header']) . "\r\n";
            $header .= "Content-Length: " . strlen($request_data['content']) . "\r\n";
            $header .= "Connection: close\r\n\r\n";
            
            fputs($fsh, $header);
            fputs($fsh, $request_data['content']);
            
            $response = "";
            while (!feof($fsh)) {
                $response .= @fgets($fsh, 2048);
            }
            fclose($fsh);
            
            $this->setLastHttpTransfer($header . $request_data['content'], $response);
            
            if ($this->verbose) {
                echo $this->getLastHttpTransfer();
            }
            
            if (strlen($response) > 10) {
                list($response_header, $response_data['content']) = preg_split('/\r\n\r\n/', $response);
                
                $response_data['http_status']  = (preg_match('/HTTP\/\d\.\d (\d+)/i', $response_header, $m)) ? intval($m[1]) : 0;
                $response_data['content_type'] = (preg_match('/Content-Type: ([^\r\n]+)/i', $response_header, $o)) ? $o[1] : "";
                
            }
        } else {
            throw new Exception("OpenSSL extension for fsockopen probably not enabled in PHP.ini. Alternatively you could try curl method to send request. Use setter WebSmsCom_Client->setHttpClientType('curl').");
        }
        return $response_data;
    }
    
    //----------------------------------------------------------------
    // getVersion()
    //     - returns version string of this WebSmsCom client 
    //----------------------------------------------------------------
    function getVersion() {
        return $this->VERSION;
    }
    //----------------------------------------------------------------
    // getUsername()
    //     - returns username set at WebSmsCom client ceration
    //----------------------------------------------------------------
    function getUsername() {
        return $this->username;
    }
    
    //----------------------------------------------------------------
    // getUrl()
    //     - returns url set at WebSmsCom client ceration
    //----------------------------------------------------------------
    function getUrl() {
        return $this->url;
    }
    
    //----------------------------------------------------------------
    // getContentType()
    //     - returns used content-type in HTTP request
    //         defaults to "application/json;charset=UTF-8"
    //----------------------------------------------------------------
    function getContentType() {
        return $this->content_type;
    }
    //----------------------------------------------------------------
    // setContentType($sting)
    //     - possibility to set different content type
    //----------------------------------------------------------------
    function setContentType($value) {
        $this->content_type = $value;
    }
    
    //----------------------------------------------------------------
    // getLastHttpTransfer()
    //     - returns last HTTP Request and HTTP Response as string.
    //----------------------------------------------------------------
    public function getLastHttpTransfer() {
        return $this->lastHttpTransfer;
    }
    private function setLastHttpTransfer($request_string, $response_string) {
        $this->lastHttpTransfer = "---HTTP-Request---:\n" . 
                                  $request_string . 
                                  "\n---HTTP-Response---:\n" . 
                                  $response_string;
    }
    
    //----------------------------------------------------------------
    // getHttpClientType()
    //     - returns used php connection method 'curl' or 'fopen'
    //----------------------------------------------------------------
    function getHttpClientType() {
        return $this->httpClientType;
    }
    //----------------------------------------------------------------
    // setHttpClientType($httpClientType)
    //     - set http connection method 'curl' or 'fopen'. default is 'curl'
    //----------------------------------------------------------------
    function setHttpClientType($httpClientType) {
        if (in_array($httpClientType, $this->available_http_client_types)) {
            $this->httpClientType = $httpClientType;
        } else {
            throw new WebSmsCom_ParameterValidationException("Unknown HttpClientType.");
        }
    }
    //----------------------------------------------------------------
    // getConnectionTimeout()
    //     - returns timeout in seconds 
    //--------------------------------------
    function getConnectionTimeout() {
        return $this->connectionTimeout;
    }
    //----------------------------------------------------------------
    // setConnectionTimeout($seconds)
    //     - set time in seoncds for curl or fopen connection
    //----------------------------------------------------------------
    function setConnectionTimeout($connectionTimeout = 10) {
        $this->connectionTimeout = $connectionTimeout;
    }
    //----------------------------------------------------------------
    // setVerbose($boolean)
    //     - set verbose to see more information about request (echoes)
    //----------------------------------------------------------------
    function setVerbose($value) {
        $this->verbose = $value;
    }
    //----------------------------------------------------------------
    // setSslVerifyHost($boolean)
    //     - ignore ssl host security
    //----------------------------------------------------------------
    function setSslVerifyHost($value) {
        $this->sslVerifyHost = $value;
    }
    
    
} // end of class WebSmsCom_Client


//-----------------------------------------------------------------
// WebSmsCom_Message 
//
// @throws Exception  
//-----------------------------------------------------------------
abstract class WebSmsCom_Message {
  
    static $availableSenderAdressType = array('national', 'international', 'alphanumeric', 'shortcode');
    
    protected $recipientAddressList = array();
    protected $senderAddress;
    protected $senderAddressType;
    protected $sendAsFlashSms;
    protected $notificationCallbackUrl;
    protected $clientMessageId;
    protected $priority;
    
    //----------------------------------------------------------------
    //  getRecipientAddressList()
    //   - returns array of set recipients
    //----------------------------------------------------------------
    public function getRecipientAddressList() {
        return $this->recipientAddressList;
    }
    //----------------------------------------------------------------
    // setRecipientAddressList($array)
    //   - set array of recipients 
    //     (array of strings containing full international MSISDNs)
    //----------------------------------------------------------------
    public function setRecipientAddressList($recipientAddressList) {
        if (is_array($recipientAddressList)) {
            $this->recipientAddressList = $recipientAddressList;
        } else {
            throw new WebSmsCom_ParameterValidationException("given recipientAddressList is not an array");
        }
    }
    
    //----------------------------------------------------------------
    //  getSenderAddress
    //    - returns set senderAddress
    //----------------------------------------------------------------
    public function getSenderAddress() {
        return $this->senderAddress;
    }
    //----------------------------------------------------------------
    //  setSenderAddress(string senderAdress)
    //    - set string of sender address msisdn or alphanumeric
    //      sender address is dependend on user account
    //----------------------------------------------------------------
    public function setSenderAddress($senderAddress) {
        $this->senderAddress = $senderAddress;
    }
    
    //----------------------------------------------------------------
    //  getSenderAddressType
    //    - returns set sender address type
    //----------------------------------------------------------------
    public function getSenderAddressType() {
        return $this->senderAddressType;
    }
    //----------------------------------------------------------------
    //  setSenderAddressType(string senderAdressType)
    //   - depending on account settings this can be set to
    //     'national', 'international', 'alphanumeric' or 'shortcode'
    //----------------------------------------------------------------
    public function setSenderAddressType($senderAddressType) {
        if (!in_array($senderAddressType, $availableSenderAdressType)) {
            throw WebSmsCom_ParameterValidationException("given senderAdressType '$senderAdressType' is invalid");
        } else {
            $this->senderAddressType = $senderAddressType;
        }
    }
    //----------------------------------------------------------------
    //  getSendAsFlashSms
    //    - returns set SendAsFlashSms flag
    //----------------------------------------------------------------
    public function getSendAsFlashSms() {
        return $this->sendAsFlashSms;
    }
    //----------------------------------------------------------------
    //  setSendAsFlashSms($boolean)
    //    - set send as flash sms flag true or false
    //    (SMS is not saved on SIM, but shown directly on screen)
    //----------------------------------------------------------------
    public function setSendAsFlashSms($sendAsFlashSms) {
        $this->sendAsFlashSms = $sendAsFlashSms;
    }
    //----------------------------------------------------------------
    //  getNotificationCallbackUrl
    //    - returns set notification callback url
    //----------------------------------------------------------------
    public function getNotificationCallbackUrl() {
        return $this->notificationCallbackUrl;
    }
    //----------------------------------------------------------------
    //  setNotificationCallbackUrl(string $url)
    //    - set string og notification callback url
    //    customers url that listens for delivery report notifications
    //    or replies for this message
    //----------------------------------------------------------------
    public function setNotificationCallbackUrl($notificationCallbackUrl) {
        $this->notificationCallbackUrl = $notificationCallbackUrl;
    }
    //----------------------------------------------------------------
    //  getClientMessageId
    //    - returns set clientMessageId
    //----------------------------------------------------------------
    public function getClientMessageId() {
        return $this->clientMessageId;
    }
    //----------------------------------------------------------------
    //  setClientMessageId($string)
    //    - set message id for this message, returned with response
    //      and used for notifications
    //----------------------------------------------------------------
    public function setClientMessageId($clientMessageId) {
        $this->clientMessageId = $clientMessageId;
    }
    //----------------------------------------------------------------
    //  getPriority
    //    - returns set message priority
    //----------------------------------------------------------------
    public function getPriority() {
        return $this->priority;
    }
    //----------------------------------------------------------------
    //  setPriority(int $priority)
    //    - sets message priority as integer (1 to 9)
    //     (if supported by account settings)
    //----------------------------------------------------------------
    public function setPriority($priority) {
        $this->priority = $priority;
    }
    
    //----------------------------------------------------------------
    //  getJsonData
    //    - used to build json array of message for json encoding
    //----------------------------------------------------------------
    public function getJsonData() {
        $object_vars = get_object_vars($this);
        $result      = array();
        foreach ($object_vars as $key => $value) {
            if (is_object($value) && method_exists($value, 'getJsonData')) {
                $value = $value->getJsonData();
            }
            if (!is_null($value)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    //----------------------------------------------------------------
    //  checkRecipientAddressList($recipientAddressList)
    //      - used to check validity of array
    //----------------------------------------------------------------
    public function checkRecipientAddressList($recipientAddressList) {
        if (!is_array($recipientAddressList)) {
            throw new WebSmsCom_ParameterValidationException("Argument 'recipientAddressList' (array) invalid while contructing " . get_class($this));
        }
        foreach ($recipientAddressList as $value) {
            if (!is_numeric($value)) {
                throw new WebSmsCom_ParameterValidationException("Recipient '" . $value . "' is invalid. (must be numeric)");
            }
            if (is_string($value) && (!preg_match('/^\d{1,15}$/', $value) || preg_match('/^0/', $value))) {
                throw new WebSmsCom_ParameterValidationException("Recipient '" . $value . "' is invalid. (max. 15 digits full international MSISDN. Example: 4367612345678)");
            }
            unset($value);
        }
    }
    
}

//-----------------------------------------------------------------
// WebSmsCom_TextMessage
//
// @throws Exception
//-----------------------------------------------------------------
class WebSmsCom_TextMessage extends WebSmsCom_Message {
  
    # string
    protected $messageContent;
    
    function __construct($recipientAddressList, $messageContent) {
        
        $this->checkRecipientAddressList($recipientAddressList);
        
        if (!is_string($messageContent)) {
            throw new WebSmsCom_ParameterValidationException("Argument 'messageContent' (string) invalid while contructing " . __CLASS__);
        }

        $this->messageContent       = $messageContent;
        $this->recipientAddressList = $recipientAddressList;

    }
    
    //----------------------------------------------------------------
    //  getMessageContent()
    //   - returns set messageContent
    //----------------------------------------------------------------
    public function getMessageContent() {
        return $this->messageContent;
    }
    //----------------------------------------------------------------
    //  setMessageContent(string $messageContent)
    //   - set utf8 string message text
    //----------------------------------------------------------------
    public function setMessageContent($messageContent) {
        $this->messageContent = $messageContent;
    }
    
}

//-----------------------------------------------------------------
// WebSmsCom_BinaryMessage
//
// @throws Exception
//-----------------------------------------------------------------
class WebSmsCom_BinaryMessage extends WebSmsCom_Message {
  
    # string
    protected $messageContent;
    protected $userDataHeaderPresent;
    
    function __construct($recipientAddressList, $messageContent, $userDataHeaderPresent) {
        $this->checkRecipientAddressList($recipientAddressList);
        
        if (!is_array($messageContent)) {
            throw new WebSmsCom_ParameterValidationException("Argument 'messageContent' (array) invalid while constructing " . __CLASS__ . ". Must be array of strings containing base64 encoded content.");
        }
        if (!is_bool($userDataHeaderPresent)) {
            throw new WebSmsCom_ParameterValidationException("Argument 'userDataHeaderPresent' (boolean) invalid while contructing " . __CLASS__);
        }
        
        $this->messageContent        = $messageContent;
        $this->userDataHeaderPresent = $userDataHeaderPresent;
        $this->recipientAddressList  = $recipientAddressList;
    }
    
    //----------------------------------------------------------------
    //  getMessageContent()
    //   - returns set messageContent segments (array)
    //----------------------------------------------------------------
    public function getMessageContent() {
        return $this->messageContent;
    }
    //----------------------------------------------------------------
    //  setMessageContent(array $messageContent)
    //   - set binary message content (array of base64 encoded binary strings)
    //----------------------------------------------------------------
    public function setMessageContent($messageContent) {
        $this->messageContent = $messageContent;
    }
    
    //----------------------------------------------------------------
    //  getUserDataHeaderPresent()
    //   - returns set UserDataHeaderPresent flag
    //----------------------------------------------------------------
    public function getUserDataHeaderPresent() {
        return $this->userDataHeaderPresent;
    }
    //----------------------------------------------------------------
    //  setUserDataHeaderPresent(boolean $userDataHeaderPresent)
    //   - set boolean userDataHeaderPresent flag
    //     When set to true, messageContent segments are expected
    //     to contain a UserDataHeader
    //----------------------------------------------------------------
    public function setUserDataHeaderPresent($userDataHeaderPresent) {
        $this->userDataHeaderPresent = $userDataHeaderPresent;
    }
    
}

//----------------------------------------------
// WebSmsCom send Response Object
//----------------------------------------------
class WebSmsCom_Response {
  
    private $rawContent;
    private $statusCode;
    private $statusMessage;
    private $transferId;
    private $clientMessageId;
    
    function __construct($raw_content, $statusCode, $statusMessage, $transferId, $clientMessageId) {
        $this->rawContent       = $raw_content;
        $this->statusCode        = $statusCode;
        $this->statusMessage     = $statusMessage;
        $this->transferId        = $transferId;
        $this->clientMessageId  = $clientMessageId;
    }
    
    //----------------------------------------------------------------
    //  getRawContent()
    //   - returns raw content of response
    //----------------------------------------------------------------
    function getRawContent() {
        return $this->rawContent;
    }
    //----------------------------------------------------------------
    //  getStatusCode()
    //   - returns received StatusCode of API Response
    //----------------------------------------------------------------
    function getStatusCode() {
        return $this->statusCode;
    }
    //----------------------------------------------------------------
    //  getStatusMessage()
    //   - returns received StatusMessage of API Response
    //----------------------------------------------------------------
    function getStatusMessage() {
        return $this->statusMessage;
    }
    //----------------------------------------------------------------
    //  getTransferId()
    //   - returns received TransferId of API Response for successfully sent Message
    //----------------------------------------------------------------
    function getTransferId() {
        return $this->transferId;
    }
    
    //----------------------------------------------------------------
    //  getClientMessageId()
    //   - returns received TransferId of API Response for successfully sent Message
    //----------------------------------------------------------------
    function getClientMessageId() {
        return $this->clientMessageId;
    }
}

//########################## Exceptions #############################

//-------------------------------------------------
// custom ParameterValidationException
//-------------------------------------------------
class WebSmsCom_ParameterValidationException extends Exception {
    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

//-------------------------------------------------
// custom ApiException
//-------------------------------------------------
class WebSmsCom_ApiException extends Exception {
    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

//-------------------------------------------------
// custom AuthorizationFailedException
//-------------------------------------------------
class WebSmsCom_AuthorizationFailedException extends Exception {
    public function __construct($message, $code = 401) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

//-------------------------------------------------
// custom WebSmsCom_HttpConnectionException
//-------------------------------------------------
class WebSmsCom_HttpConnectionException extends Exception {
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

//-------------------------------------------------
// custom WebSmsCom_UnknownResponseException
//-------------------------------------------------
class WebSmsCom_UnknownResponseException extends Exception {
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
 

?>