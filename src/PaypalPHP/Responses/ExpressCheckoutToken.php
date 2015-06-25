<?php
/**
 * Created by sam
 * At: 10/06/2014 15:57
 */

namespace PaypalPHP\Responses;
use PaypalPHP\Helpers\UrlHelper;

/**
 * Express Checkout Token Container
 * @package Paypal\Structs
 */
class ExpressCheckoutToken
{
  /** @var string Request token */
  public $token;
  /** @var string Token timestamp */
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
   * Create token details from raw response
   * @param string $rawResponse Raw NVP data from PayPal
   */
  public function __construct($rawResponse)
  {
    UrlHelper::urlToObject($rawResponse, $this);
  }
} 
