<?php

/**
* @file providing the service that return time by timezone.
*
*/

namespace  Drupal\timezone;

use Drupal\Core\Datetime\DateFormatter;


class CurrentTimeByTimezone {
 
 /**
  * @var DateFormatter $dateFormatter.
  */ 
 protected $dateFormatter;
 
 /**
   * @param Drupal\Core\Datetime\DateFormatter $dateFormatter;
   */
 public function __construct(DateFormatter $dateFormatter) {
   $this->dateFormatter = $dateFormatter;
 }

 public function  getTimeWithTimezone(){

    // Get country, city and timezone values which is set in timezone config form.
    $country = \Drupal::config('config.timezone_config_form')->get('timezone_config_country');
    $city = \Drupal::config('config.timezone_config_form')->get('timezone_config_city');
    $timezone = \Drupal::config('config.timezone_config_form')->get('timezone_config_timezone');  
    if($country && $city && $timezone){

      // Use dateFormatter service to get time by timezone.
      $date_time = $this->dateFormatter->format(time(), 'custom', 'jS M Y - h:i A', $timezone);
      return $date_time;
    }
 }
}