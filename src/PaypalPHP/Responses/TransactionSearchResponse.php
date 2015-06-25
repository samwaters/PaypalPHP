<?php
/**
 * Created by Sam.
 * At: 24/06/2015 19:07
 */
namespace PaypalPHP\Responses;

class TransactionSearchResponse
{
  public $timestamp;
  public $timezone;
  public $type;
  public $email;
  public $transactionid;
  public $status;
  public $amt;
  public $currencycode;
  public $feeamt;
  public $netamt;
}
