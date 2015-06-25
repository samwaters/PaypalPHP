<?php
/**
 * Created by sam
 * At: 06/06/2014 13:52
 */

namespace PaypalPHP\Lookups;

use PaypalPHP\Structs\StateInfo;

/**
 * US and CA State lookups
 * @package Paypal\Lookups
 */
class States
{
  /** @var array Code to State lookup */
  private static $_codeToState = array(
    "AB"=>"Alberta", //CA
    "AL"=>"Alabama",
    "AK"=>"Alaska",
    "AZ"=>"Arizona",
    "AR"=>"Arkansas",
    "BC"=>"British Columbia", //CA
    "CA"=>"California",
    "CO"=>"Colorado",
    "CT"=>"Connecticut",
    "DE"=>"Delaware",
    "DC"=>"District Of Columbia",
    "FL"=>"Florida",
    "GA"=>"Georgia",
    "HI"=>"Hawaii",
    "ID"=>"Idaho",
    "IL"=>"Illinois",
    "IN"=>"Indiana",
    "IA"=>"Iowa",
    "KS"=>"Kansas",
    "KY"=>"Kentucky",
    "LA"=>"Louisiana",
    "ME"=>"Maine",
    "MD"=>"Maryland",
    "MA"=>"Massachusetts",
    "MB"=>"Manitoba", //CA
    "MI"=>"Michigan",
    "MN"=>"Minnesota",
    "MS"=>"Mississippi",
    "MO"=>"Missouri",
    "MT"=>"Montana",
    "NE"=>"Nebraska",
    "NV"=>"Nevada",
    "NB"=>"New Brunswick", //CA
    "NH"=>"New Hampshire",
    "NJ"=>"New Jersey",
    "NL"=>"Newfoundland and Labrador", //CA
    "NM"=>"New Mexico",
    "NS"=>"Nova Scotia", //CA
    "NY"=>"New York",
    "NC"=>"North Carolina",
    "ND"=>"North Dakota",
    "OH"=>"Ohio",
    "OK"=>"Oklahoma",
    "ON"=>"Ontario", //CA
    "OR"=>"Oregon",
    "PA"=>"Pennsylvania",
    "PE"=>"Prince Edward Island", //CA
    "QC"=>"Quebec", //CA
    "RI"=>"Rhode Island",
    "SC"=>"South Carolina",
    "SD"=>"South Dakota",
    "SK"=>"Saskatchewan", //CA
    "TN"=>"Tennessee",
    "TX"=>"Texas",
    "UT"=>"Utah",
    "VT"=>"Vermont",
    "VA"=>"Virginia",
    "WA"=>"Washington",
    "WV"=>"West Virginia",
    "WI"=>"Wisconsin",
    "WY"=>"Wyoming"
  );

  /** @var array State to Code lookup */
  private static $_stateToCode = array(
    "ALBERTA" => "AB", //CA
    "ALABAMA"=>"AL",
    "ALASKA"=>"AK",
    "ARIZONA"=>"AZ",
    "ARKANSAS"=>"AR",
    "BRITISH COLUMBIA" => "BC", //CA
    "CALIFORNIA"=>"CA",
    "COLORADO"=>"CO",
    "CONNECTICUT"=>"CT",
    "DELAWARE"=>"DE",
    "DISTRICT OF COLUMBIA"=>"DC",
    "FLORIDA"=>"FL",
    "GEORGIA"=>"GA",
    "HAWAII"=>"HI",
    "IDAHO"=>"ID",
    "ILLINOIS"=>"IL",
    "INDIANA"=>"IN",
    "IOWA"=>"IA",
    "KANSAS"=>"KS",
    "KENTUCKY"=>"KY",
    "LOUISIANA"=>"LA",
    "MAINE"=>"ME",
    "MARYLAND"=>"MD",
    "MASSACHUSETTS"=>"MA",
    "MANITOBA" => "MB", //CA
    "MICHIGAN"=>"MI",
    "MINNESOTA"=>"MN",
    "MISSISSIPPI"=>"MS",
    "MISSOURI"=>"MO",
    "MONTANA"=>"MT",
    "NEBRASKA"=>"NE",
    "NEVADA"=>"NV",
    "NEW BRUNSWICK" => "NB", //CA
    "NEW HAMPSHIRE"=>"NH",
    "NEW JERSEY"=>"NJ",
    "NEWFOUNDLAND" => "NL", //CA
    "NEWFOUNDLAND AND LABRADOR" => "NL", //CA
    "LABRADOR" => "NL", //CA
    "NEW MEXICO"=>"NM",
    "NOVA SCOTIA" => "NS", //CA
    "NEW YORK"=>"NY",
    "NORTH CAROLINA"=>"NC",
    "NORTH DAKOTA"=>"ND",
    "OHIO"=>"OH",
    "OKLAHOMA"=>"OK",
    "ONTARIO" => "ON", //CA
    "OREGON"=>"OR",
    "PENNSYLVANIA"=>"PA",
    "PRINCE EDWARD ISLAND" => "PE", //CA
    "QUEBEC" => "QC", //CA
    "RHODE ISLAND"=>"RI",
    "SOUTH CAROLINA"=>"SC",
    "SOUTH DAKOTA"=>"SD",
    "SASKATCHEWAN" => "SK", //CA
    "TENNESSEE"=>"TN",
    "TEXAS"=>"TX",
    "UTAH"=>"UT",
    "VERMONT"=>"VT",
    "VIRGINIA"=>"VA",
    "WASHINGTON"=>"WA",
    "WEST VIRGINIA"=>"WV",
    "WISCONSIN"=>"WI",
    "WYOMING"=>"WY"
  );

  /**
   * Look up a State name by code
   * @param string $code State code
   * @return null|StateInfo State details (name and code) or null if invalid
   */
  public static function getStateByCode($code)
  {
    $code = preg_replace("/[^A-z\s]/", "", $code); //Remove punctuation
    $code = strtoupper($code);
    if(isset(self::$_codeToState[$code]))
    {
      return new StateInfo($code, self::$_codeToState[$code]);
    }
    return null;
  }

  /**
   * Look up a State code by name
   * @param string $state State name
   * @return null|StateInfo State details (name and code) or null if invalid
   */
  public static function getCodeByState($state)
  {
    //Remove e-acute For CA States
    $state = str_replace("é", "e", $state);
    $state = str_replace("É", "E", $state);
    $state = strtoupper($state);
    if(isset(self::$_stateToCode[$state]))
    {
      return new StateInfo(self::$_stateToCode[$state], $state);
    }
    return null;
  }
} 
