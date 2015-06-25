<?php
/**
 * Created by sam
 * At: 11/06/2014 16:05
 */

namespace PaypalPHP\Responses;
use PaypalPHP\Helpers\UrlHelper;

/**
 * Express Checkout Subscription Container
 * @package Paypal\Structs
 */
class ExpressCheckoutSubscription
{
  /** @var string Subscription ID */
  public $profileid;
  /** @var string Subscription status */
  public $profilestatus;
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
  /** @var string Error message */
  public $l_shortmessage0;

  /**
   * Create subscription data from raw NVP data
   * @param string $rawResponse Raw NVP data from PayPal
   */
  public function __construct($rawResponse)
  {
    UrlHelper::urlToObject($rawResponse, $this);
  }
} 
