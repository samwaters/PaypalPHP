<?php
/**
 * Created by sam
 * At: 11/06/2014 15:16
 */

namespace PaypalPHP\Responses;
use PaypalPHP\Helpers\UrlHelper;

/**
 * Express Checkout Details Container
 * @package Paypal\Structs
 */
class ExpressCheckoutDetails
{
  //API info
  /** @var string Request token */
  public $token;
  /** @var string Billing agreement status */
  public $billingagreementacceptedstatus;
  /** @var string Checkout status */
  public $checkoutstatus;
  /** @var string Request timestamp */
  public $timestamp;
  /** @var string Request correlation ID */
  public $correlationid;
  /** @var string Request status */
  public $ack;
  /** @var string API version */
  public $version;
  /** @var string API build */
  public $build;
  //User
  /** @var string User email */
  public $email;
  /** @var string PayPal ID */
  public $payerid;
  /** @var string PayPal verification status */
  public $payerstatus;
  /** @var string User first name */
  public $firstname;
  /** @var string User last name */
  public $lastname;
  /** @var string User country code */
  public $countrycode;
  //Shipping
  /** @var string Shipping name */
  public $shiptoname;
  /** @var string Shipping address line 1 */
  public $shiptostreet;
  /** @var string Shipping address line 2 */
  public $shiptostreet2;
  /** @var string Shipping address city */
  public $shiptocity;
  /** @var string Shipping address state */
  public $shiptostate;
  /** @var string Shipping address ZIP */
  public $shiptozip;
  /** @var string Shipping address country code (2 digits) */
  public $shiptocountrycode;
  /** @var string Shipping address country name */
  public $shiptocountryname;
  /** @var string Shipping address verification status */
  public $addressstatus;
  //Payments
  /** @var string Currency code (e.g. USD, GBP) */
  public $currencycode;
  /** @var string Order amount */
  public $amt;
  /** @var string Shipping amount */
  public $shippingamt;
  /** @var string Handling amount */
  public $handlingamt;
  /** @var string Tax amount */
  public $taxamt;
  /** @var string Insurance amount */
  public $insuranceamt;
  /** @var string Shipping discount amount */
  public $shipdiscamt;
  //Error
  /** @var string Error message */
  public $l_shortmessage0;

  /**
   * Construct Express Checkout Details from raw NVP data
   * @param string $rawResponse Raw NVP data from PayPal
   */
  public function __construct($rawResponse)
  {
    UrlHelper::urlToObject($rawResponse, $this);
  }
} 
