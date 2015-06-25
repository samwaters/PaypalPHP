<?php
/**
 * Created by sam
 * At: 24/06/2014 14:45
 */

namespace PaypalPHP\Structs;
use PaypalPHP\Responses\ExpressCheckoutToken;

/**
 * Order Information Container
 * @package Paypal\Structs
 */
class OrderInfo
{
  /** @var string Order ID */
  public $orderId;
  /** @var \PaypalPHP\Responses\ExpressCheckoutToken|null PayPal request token */
  public $token;
  /** @var string Order total */
  public $total;
  /** @var string Order currency code (e.g. USD, GBP) */
  public $currency;
  /** @var string Order description */
  public $description;
  /** @var string PayPal billing period (Daily, Weekly, Monthly, Yearly) */
  public $billingPeriod;
  /** @var string Number of times per billing period to bill */
  public $billingFrequency; //3, 6, 12 (per billing period)
  /** @var bool Whether this order has a trial */
  public $hasTrial; //bool
  /** @var string Trial billing period */
  public $trialPeriod; //Monthly, Yearly
  /** @var string Trial billing frequency */
  public $trialFrequency; //3, 6, 12 (per billing period)
  /** @var string Trial length (in billing periods) */
  public $trialLength; //Number of billing periods
  /** @var string Trial price */
  public $trialAmount;
  /** @var string Item name */
  public $itemName;
  /** @var string Item description */
  public $itemDescription;
  /** @var string Item number */
  public $itemNumber;

  /**
   * Order Information Container
   * @param string $id Order ID
   * @param ExpressCheckoutToken|null $token PayPal request token
   * @param string $total Order total
   * @param string $currency Order currency code (e.g. USD, GBP)
   * @param string $description Order description
   * @param string $billingPeriod PayPal billing period (Daily, Weekly, Monthly, Yearly)
   * @param string $billingFrequency Number of times per billing period to bill
   */
  public function __construct($id, $token, $total, $currency, $description, $billingPeriod, $billingFrequency)
  {
    $this->orderId = $id;
    if(!$token instanceof ExpressCheckoutToken)
    {
      $token = null;
    }
    $this->token = $token;
    $this->total = $total;
    $this->currency = $currency;
    $this->description = rawurlencode(trim($description));
    $this->billingPeriod = $billingPeriod;
    $this->billingFrequency = $billingFrequency;
    $this->hasTrial = false;
  }

  /**
   * Add a trial period to the order information
   * @param string $period Trial billing period (Month, Year etc)
   * @param string $frequency Number of times to bill per billing period
   * @param string $length Number of billing periods the trial includes
   * @param string $amount Trial price
   */
  public function addTrial($period, $frequency, $length, $amount)
  {
    $this->hasTrial = true;
    $this->trialPeriod = $period;
    $this->trialFrequency = $frequency;
    $this->trialLength = $length;
    $this->trialAmount = $amount;
  }

  /**
   * Add an item's details to appear on the PayPal screen
   * @param string $name Item name
   * @param string $description Item description
   * @param string $number Item number
   */
  public function setItemDetails($name, $description, $number)
  {
    $this->itemName = rawurlencode(trim($name));
    $this->itemDescription = rawurlencode(trim($description));
    $this->itemNumber = $number;
  }
} 
