<?php
/**
 * Created by sam
 * At: 10/06/2014 15:41
 */

namespace PaypalPHP\Managers;

use PaypalPHP\Exceptions\ExpressCheckoutDetailsException;
use PaypalPHP\Exceptions\ExpressCheckoutSubscriptionException;
use PaypalPHP\Exceptions\ExpressCheckoutTokenException;
use PaypalPHP\Structs\AddressInfo;
use PaypalPHP\Responses\ExpressCheckoutDetails;
use PaypalPHP\Responses\ExpressCheckoutSubscription;
use PaypalPHP\Responses\ExpressCheckoutToken;
use PaypalPHP\Structs\OrderInfo;

/**
 * Express Checkout Manager
 * @package Paypal\Managers
 */
class ExpressCheckout
{
  /** @var resource Curl client */
  private $_client;
  /** @var array Configuration options */
  private $_config;

  /**
   * Create a new ExpressCheckout manager
   * @param resource $client Curl client
   * @param array $config Configuration options
   */
  public function __construct($client, array $config)
  {
    $this->_client = $client;
    $this->_config = $config;
  }

  /**
   * Generate the Express Checkout URL to redirect the user to
   * @param OrderInfo $orderInfo Order details
   * @return string URL to redirect to
   * @throws \PaypalPHP\Exceptions\ExpressCheckoutTokenException
   */
  public function getExpressCheckoutURL(OrderInfo $orderInfo)
  {
    if($orderInfo->token == null || !$orderInfo->token instanceof ExpressCheckoutToken)
    {
      throw new ExpressCheckoutTokenException("Token is not set (ExpressCheckoutURL)");
    }
    $url = $this->_config["sale_endpoint"] . "/cgi-bin/webscr?cmd=_express-checkout";
    $url .= "&token=" . $orderInfo->token->token;
    return $url;
  }

  /**
   * Make a one-off payment
   * @param OrderInfo $orderInfo Order Information
   * @param string $payerID PayPal PayerID
   * @return string Raw details of the payment
   * @throws \PaypalPHP\Exceptions\ExpressCheckoutTokenException
   */
  public function makePayment(OrderInfo $orderInfo, $payerID)
  {
    if($orderInfo->token == null || !$orderInfo->token instanceof ExpressCheckoutToken)
    {
      throw new ExpressCheckoutTokenException("Token is not set (ExpressCheckoutURL)");
    }
    $localClient = curl_copy_handle($this->_client);
    $url = $this->_config["nvp_endpoint"];
    $url .= "?METHOD=DoExpressCheckoutPayment&VERSION=114.0";
    $url .= "&USER=" . $this->_config["username"];
    $url .= "&PWD=" . $this->_config["password"];
    $url .= "&SIGNATURE=" . $this->_config["signature"];
    $url .= "&TOKEN=" . $orderInfo->token->token;
    $url .= "&PAYERID=" . $payerID;
    $url .= "&PAYMENTREQUEST_0_AMT=" . $orderInfo->total;
    $url .= "&PAYMENTREQUEST_0_CURRENCYCODE=" . $orderInfo->currency;
    $url .= "&PAYMENTREQUEST_0_PAYMENTACTION=Sale";
    curl_setopt($localClient, CURLOPT_URL, $url);
    $rawResponse = curl_exec($localClient);
    //todo: Create a class for the response
    return $rawResponse;
  }

  /**
   * Create a subscription
   * @param OrderInfo $orderInfo Order Information
   * @param ExpressCheckoutDetails $checkoutDetails Optional. Details about the express checkout session
   * @param bool $includeDetails Whether to include the item details on the PayPal screen
   * @return ExpressCheckoutSubscription Subscription details
   * @throws \PaypalPHP\Exceptions\ExpressCheckoutSubscriptionException
   */
  public function createSubscription(OrderInfo $orderInfo, ExpressCheckoutDetails $checkoutDetails = null, $includeDetails = false)
  {
    if($checkoutDetails == null)
    {
      $checkoutDetails = $this->getExpressCheckoutDetails($orderInfo->token);
    }
    $localClient = curl_copy_handle($this->_client);
    $url = $this->_config["nvp_endpoint"];
    $url .= "?METHOD=CreateRecurringPaymentsProfile&VERSION=114.0";
    $url .= "&USER=" . $this->_config["username"];
    $url .= "&PWD=" . $this->_config["password"];
    $url .= "&SIGNATURE=" . $this->_config["signature"];
    $url .= "&TOKEN=" . $orderInfo->token->token;
    $url .= "&PROFILESTARTDATE=" . date("Y-m-d") . "T00:00:00Z";
    $url .= "&PROFILEREFERENCE=" . $orderInfo->itemNumber;
    $url .= "&DESC=" . $orderInfo->description;
    $url .= "&BILLINGPERIOD=" . $orderInfo->billingPeriod;
    $url .= "&BILLINGFREQUENCY=" . $orderInfo->billingFrequency;
    $url .= "&AMT=" . $orderInfo->total;
    if($orderInfo->hasTrial)
    {
      $url .= "&TRIALBILLINGPERIOD=" . $orderInfo->trialPeriod;
      $url .= "&TRIALBILLINGFREQUENCY=" . $orderInfo->trialFrequency;
      $url .= "&TRIALTOTALBILLINGCYCLES=" . $orderInfo->trialLength;
      $url .= "&TRIALAMT=" . $orderInfo->trialAmount;
    }
    $url .= "&CURRENCYCODE=" . $orderInfo->currency;
    $url .= "&EMAIL=" . urlencode($checkoutDetails->email);
    $url .= "&PAYERID=" . $checkoutDetails->payerid;
    $url .= "&STREET=" . urlencode($checkoutDetails->shiptostreet);
    $url .= "&CITY=" . urlencode($checkoutDetails->shiptocity);
    $url .= "&STATE=" . urlencode($checkoutDetails->shiptostate);
    $url .= "&COUNTRYCODE=" . urlencode($checkoutDetails->countrycode);
    $url .= "&ZIP=" . urlencode($checkoutDetails->shiptozip);
    if($includeDetails)
    {
      $url .= "&L_PAYMENTREQUEST_0_NAME0=" . $orderInfo->itemName;
      $url .= "&L_PAYMENTREQUEST_0_DESC0=" . $orderInfo->itemDescription;
      $url .= "&L_PAYMENTREQUEST_0_AMT0=" . $orderInfo->total;
      $url .= "&L_PAYMENTREQUEST_0_NUMBER0=" . $orderInfo->itemNumber;
      $url .= "&L_PAYMENTREQUEST_0_QTY0=1";
    }
    curl_setopt($localClient, CURLOPT_URL, $url);
    $rawResponse = curl_exec($localClient);
    $subscription = new ExpressCheckoutSubscription($rawResponse);
    if($subscription->ack != "Success")
    {
      throw new ExpressCheckoutSubscriptionException("Could not create subscription - " . $subscription->l_shortmessage0);
    }
    return $subscription;
  }

  /**
   * Get details for an express checkout session
   * @param ExpressCheckoutToken $token Token to get details for
   * @return ExpressCheckoutDetails Details of the Express Checkout session
   * @throws \PaypalPHP\Exceptions\ExpressCheckoutDetailsException
   */
  public function getExpressCheckoutDetails(ExpressCheckoutToken $token)
  {
    $localClient = curl_copy_handle($this->_client);
    $url = $this->_config["nvp_endpoint"];
    $url .= "?METHOD=GetExpressCheckoutDetails&VERSION=114.0";
    $url .= "&USER=" . $this->_config["username"];
    $url .= "&PWD=" . $this->_config["password"];
    $url .= "&SIGNATURE=" . $this->_config["signature"];
    $url .= "&TOKEN=" . $token->token;
    curl_setopt($localClient, CURLOPT_URL, $url);
    $rawResponse = curl_exec($localClient);
    $details = new ExpressCheckoutDetails($rawResponse);
    if($details->ack != "Success")
    {
      throw new ExpressCheckoutDetailsException("Could not get checkout details - " . $details->l_shortmessage0);
    }
    return $details;
  }

  /**
   * Get a request token for interacting with the API
   * @param OrderInfo $orderInfo Order Information
   * @param AddressInfo|null $addressInfo Address information
   * @param bool $recurring Whether the token will be used for a recurring subscription
   * @param bool $includeDetails Whether to include the item details on the PayPal screen
   * @return ExpressCheckoutToken The request token
   * @throws \PaypalPHP\Exceptions\ExpressCheckoutTokenException
   */
  public function getRequestToken(OrderInfo $orderInfo, $addressInfo, $recurring = false, $includeDetails = false)
  {
    if($addressInfo != null && !$addressInfo instanceof AddressInfo)
    {
      $addressInfo = null;
    }
    $localClient = curl_copy_handle($this->_client);
    $url = $this->_config["nvp_endpoint"];
    $url .= "?METHOD=SetExpressCheckout&VERSION=114.0";
    $url .= "&USER=" . $this->_config["username"];
    $url .= "&PWD=" . $this->_config["password"];
    $url .= "&SIGNATURE=" . $this->_config["signature"];
    $url .= "&PAYMENTREQUEST_0_AMT=" . $orderInfo->total;
    $url .= "&PAYMENTREQUEST_0_ITEMAMT=" . $orderInfo->total;
    $url .= "&PAYMENTREQUEST_0_CURRENCYCODE=" . $orderInfo->currency;
    $url .= "&PAYMENTREQUEST_0_PAYMENTACTION=Sale";
    $url .= "&L_BILLINGAGREEMENTDESCRIPTION0=" . $orderInfo->description;
    //$url .= "&SOLUTIONTYPE=Sole";
    if($addressInfo != null && $addressInfo->isComplete())
    {
      $url .= "&ADDROVERRIDE=1";
      $url .= "&EMAIL=" . $addressInfo->email;
      $url .= "&PAYMENTREQUEST_0_SHIPTONAME=" . $addressInfo->name;
      $url .= "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $addressInfo->street1;
      $url .= "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $addressInfo->street2;
      $url .= "&PAYMENTREQUEST_0_SHIPTOCITY=" . $addressInfo->city;
      $url .= "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $addressInfo->state;
      $url .= "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $addressInfo->countrycode;
      $url .= "&PAYMENTREQUEST_0_SHIPTOZIP=" . $addressInfo->zip;
      $url .= "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $addressInfo->phone;
    }
    $url .= "&LANDINGPAGE=Billing";
    if($includeDetails)
    {
      $url .= "&L_PAYMENTREQUEST_0_NAME0=" . $orderInfo->itemName;
      $url .= "&L_PAYMENTREQUEST_0_DESC0=" . $orderInfo->itemDescription;
      $url .= "&L_PAYMENTREQUEST_0_AMT0=" . $orderInfo->total;
      $url .= "&L_PAYMENTREQUEST_0_NUMBER0=" . $orderInfo->itemNumber;
      $url .= "&L_PAYMENTREQUEST_0_QTY0=1";
    }
    if($recurring)
    {
      $url .= "&L_BILLINGTYPE0=RecurringPayments";
    }
    $url .= "&RETURNURL=" . urlencode($this->_config["checkout_return_url"]);
    $url .= "&CANCELURL=" . urlencode($this->_config["checkout_cancel_url"]);
    curl_setopt($localClient, CURLOPT_URL, $url);
    $rawResponse = curl_exec($localClient);
    $token = new ExpressCheckoutToken($rawResponse);
    if($token->ack != "Success")
    {
      if($addressInfo != null && strpos($token->l_shortmessage0, "Shipping Address Invalid") !== false)
      {
        //Try without the address to prevent PayPal throwing a fit
        return $this->getRequestToken($orderInfo, null, $recurring, $includeDetails);
      }
      throw new ExpressCheckoutTokenException("Could not get token - " . $token->l_shortmessage0);
    }
    return $token;
  }
} 
