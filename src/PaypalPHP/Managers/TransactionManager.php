<?php
/**
 * Created by Sam.
 * At: 24/06/2015 18:50
 */
namespace PaypalPHP\Managers;
use PaypalPHP\Exceptions\TransactionSearchException;
use PaypalPHP\Responses\TransactionSearchResponse;

/**
 * Transaction Manager
 * @package Paypal\Managers
 */
class TransactionManager
{
  /** @var resource Curl client */
  private $_client;
  /** @var array Configuration options */
  private $_config;

  /**
   * Create a new Transaction manager
   * @param resource $client Curl client
   * @param array $config Configuration options
   */
  public function __construct($client, array $config)
  {
    $this->_client = $client;
    $this->_config = $config;
  }

  /**
   * @param array $searchParameters
   * @return TransactionSearchResponse[]
   * @throws TransactionSearchException
   */
  public function transactionSearch(array $searchParameters)
  {
    $allowedParameters = array(
      "STARTDATE", "ENDDATE", "EMAIL", "RECEIVER", "RECEPITID", "TRANSACTIONID", "INVNUM", "ACCT", "AUCTIONITEMNUMBER",
      "TRANSACTIONCLASS", "AMT", "CURRENCYCODE", "STATUS", "PROFILEID", "SALUTATION", "FIRSTNAME", "MIDDLENAME",
      "LASTNAME", "SUFFIX"
    );
    $localClient = curl_copy_handle($this->_client);
    $url = $this->_config["nvp_endpoint"];
    $url .= "?METHOD=TransactionSearch&VERSION=114.0";
    $url .= "&USER=" . $this->_config["username"];
    $url .= "&PWD=" . $this->_config["password"];
    $url .= "&SIGNATURE=" . $this->_config["signature"] . "&";
    if(count($searchParameters) == 0)
    {
      throw new TransactionSearchException("At least one search parameter must be specified");
    }
    foreach($searchParameters as $parameter => $value)
    {
      $parameter = strtoupper($parameter);
      if(!in_array($parameter, $allowedParameters))
      {
        throw new TransactionSearchException("$parameter is not a valid search parameter");
      }
      $url .= $parameter . "=" . urlencode($value);
    }
    curl_setopt($localClient, CURLOPT_URL, $url);
    $rawResponse = curl_exec($localClient);
    $results = array();
    $filter = "/^l_([a-z]+)([0-9]+)$/";
    $responseData = explode("&", $rawResponse);
    foreach($responseData as $response)
    {
      $responseValues = explode("=", $response);
      if(count($responseValues) < 2)
      {
        continue;
      }
      $key = strtolower($responseValues[0]);
      if($key == "ack" && $responseValues[1] != "Success")
      {
        throw new TransactionSearchException("Could not perform search: $rawResponse");
      }
      $matches = array();
      if(preg_match($filter, $key, $matches))
      {
        $field = $matches[1];
        $offset = $matches[2];
        if(!isset($results[$offset]))
        {
          $results[$offset] = new TransactionSearchResponse();
        }
        $results[$offset]->$field = urldecode($responseValues[1]);
      }
    }
    return $results;
  }
}
