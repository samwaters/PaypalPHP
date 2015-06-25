<?php
/**
 * Created by sam
 * At: 07/07/2014 15:42
 */

namespace PaypalPHP\Structs;

use PaypalPHP\Lookups\States;

/**
 * Address information
 * @package Paypal\Structs
 */
class AddressInfo
{
  /** @var string Name */
  public $name;
  /** @var string Address line 1 */
  public $street1;
  /** @var string Address line 2 */
  public $street2;
  /** @var string City */
  public $city;
  /** @var string State */
  public $state;
  /** @var string Country Code */
  public $countrycode;
  /** @var string Postal Code / ZIP */
  public $zip;
  /** @var string E-mail */
  public $email;
  /** @var string Phone number */
  public $phone;

  /**
   * Create a new Addres information object
   * @param string $name Name
   * @param string $email E-mail
   * @param string|null $phone Phone number
   */
  public function __construct($name, $email, $phone=null)
  {
    $this->name = rawurlencode($name);
    $this->email = rawurlencode($email);
    $this->phone = rawurlencode($phone);
  }

  /**
   * Set and verify the address (the state needs to be valid for US and CA)
   * @param string $street1 Address line 1
   * @param string $street2 Address line 2
   * @param string $city City
   * @param string $state State
   * @param string $countrycode Country code
   * @param string $zip Postal code / ZIP
   */
  public function setAddress($street1, $street2, $city, $state, $countrycode, $zip)
  {
    $this->street1 = rawurlencode($street1);
    $this->street2 = rawurlencode($street2);
    $this->city = rawurlencode($city);
    //Paypal wants iso state codes like CA or FL
    if($countrycode == "US" || $countrycode == "CA")
    {
      if(States::getStateByCode($state) == null)
      {
        //Check if it's a valid state name
        $stateInfo = States::getCodeByState($state);
        if($stateInfo != null)
        {
          $this->state = $stateInfo->stateCode;
        }
        else
        {
          $this->state = null;
        }
      }
      else
      {
        //Matched a valid state, we can use it
        $this->state = $state;
      }
    }
    $this->countrycode = strtoupper($countrycode);
    $this->zip = rawurlencode($zip);
  }

  /**
   * Is this address information object complete?
   * @return bool
   */
  public function isComplete()
  {
    if($this->name ==  null || $this->street1 == null || $this->city == null || $this->countrycode == null || $this->zip == null || $this->email == null)
    {
      return false;
    }
    $countriesRequiringStates = array("AR", "BR", "CA", "CN", "ID", "IN", "JP", "MX", "TH", "US");
    if($this->state == null && in_array($this->countrycode, $countriesRequiringStates))
    {
      return false;
    }
    return true;
  }
} 
