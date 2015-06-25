<?php
/**
 * Created by sam
 * At: 06/06/2014 17:06
 */

namespace PaypalPHP\Structs;

/**
 * State Information Container
 * @package Paypal\Structs
 */
class StateInfo
{
  /** @var string State code (2 characters) */
  public $stateCode;
  /** @var string State name */
  public $stateName;

  /**
   * Create a new state info object
   * @param string $code State code
   * @param string $name State name
   */
  public function __construct($code, $name)
  {
    $this->stateCode = $code;
    $this->stateName = $name;
  }
} 
