<?php
/**
 * Created by Sam.
 * At: 25/06/2015 13:30
 */

namespace PaypalPHP\Helpers;

class UrlHelper
{
  public static function urlToObject($url, &$object)
  {
    $responseData = explode("&", $url);
    foreach($responseData as $response)
    {
      $responseValues = explode("=", $response);
      if(count($responseValues) < 2)
      {
        continue;
      }
      $key = strtolower($responseValues[0]);
      $object->$key = urldecode($responseValues[1]);
    }
  }
}
