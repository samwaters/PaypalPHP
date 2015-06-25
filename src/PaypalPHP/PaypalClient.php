<?php
/**
 * Created by sam
 * At: 29/05/2014 11:52
 */

namespace PaypalPHP;

use PaypalPHP\Managers\TransactionManager;
use PaypalPHP\Exceptions\ConfigException;
use PaypalPHP\Managers\ExpressCheckout;

/**
 * PayPal Client for Express Checkouts
 * @package Paypal
 */
class PaypalClient
{
  /** @var resource Global curl object */
  private $_client;
  /** @var array Configuration details */
  private $_config;

  /**
   * Create a new PayPal client
   * @param array $config Configuration
   * @throws ConfigException
   */
  public function __construct($config)
  {
    $requiredConfigOptions = array("nvp_endpoint", "sale_endpoint", "username", "password", "signature", "checkout_return", "checkout_cancel", "shipping");
    if(!is_array($config))
    {
      throw new ConfigException("Invalid config passed to Paypal Client");
    }
    foreach($requiredConfigOptions as $requiredOption)
    {
      if(!isset($config[$requiredOption]))
      {
        throw new ConfigException("$requiredOption is not set in the config");
      }
    }
    $this->_config = $config;
    $this->_client = curl_init();
    $this->_setClientOptions();
  }

  /**
   * Set global client options
   */
  private function _setClientOptions()
  {
    curl_setopt($this->_client, CURLOPT_RETURNTRANSFER, true);
  }

  /**
   * Express checkout manager
   * @return ExpressCheckout
   */
  public function ExpressCheckout()
  {
    return new ExpressCheckout($this->_client, $this->_config);
  }

  /**
   * Express checkout manager
   * @return TransactionManager
   */
  public function TransactionManager()
  {
    return new TransactionManager($this->_client, $this->_config);
  }
} 
