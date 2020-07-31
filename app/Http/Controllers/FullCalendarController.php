<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTimeZone;
use DateTime;

// PHP will fatal error if we attempt to use the DateTime class without this being set.
date_default_timezone_set('UTC');


class Event {

  // Tests whether the given ISO8601 string has a time-of-day or not
  const ALL_DAY_REGEX = '/^\d{4}-\d\d-\d\d$/'; // matches strings like "2013-12-29"

  public $title;
  public $allDay; // a boolean
  public $start; // a DateTime
  public $end; // a DateTime, or null
  public $properties = array(); // an array of other misc properties


  // Constructs an Event object from the given array of key=>values.
  // You can optionally force the timeZone of the parsed dates.
  public function __construct($array, $timeZone=null) {

    $this->title = $array['title'];

    if (isset($array['allDay'])) {
      // allDay has been explicitly specified
      $this->allDay = (bool)$array['allDay'];
    }
    else {
      // Guess allDay based off of ISO8601 date strings
      $this->allDay = preg_match(self::ALL_DAY_REGEX, $array['start']) &&
        (!isset($array['end']) || preg_match(self::ALL_DAY_REGEX, $array['end']));
    }

    if ($this->allDay) {
      // If dates are allDay, we want to parse them in UTC to avoid DST issues.
      $timeZone = null;
    }

    // Parse dates
    $this->start = parseDateTime($array['start'], $timeZone);
    $this->end = isset($array['end']) ? parseDateTime($array['end'], $timeZone) : null;

    // Record misc properties
    foreach ($array as $name => $value) {
      if (!in_array($name, array('title', 'allDay', 'start', 'end'))) {
        $this->properties[$name] = $value;
      }
    }
  }


  // Returns whether the date range of our event intersects with the given all-day range.
  // $rangeStart and $rangeEnd are assumed to be dates in UTC with 00:00:00 time.
  public function isWithinDayRange($rangeStart, $rangeEnd) {

    // Normalize our event's dates for comparison with the all-day range.
    $eventStart = stripTime($this->start);

    if (isset($this->end)) {
      $eventEnd = stripTime($this->end); // normalize
    }
    else {
      $eventEnd = $eventStart; // consider this a zero-duration event
    }

    // Check if the two whole-day ranges intersect.
    return $eventStart < $rangeEnd && $eventEnd >= $rangeStart;
  }


  // Converts this Event object back to a plain data array, to be used for generating JSON
  public function toArray() {

    // Start with the misc properties (don't worry, PHP won't affect the original array)
    $array = $this->properties;

    $array['title'] = $this->title;

    // Figure out the date format. This essentially encodes allDay into the date string.
    if ($this->allDay) {
      $format = 'Y-m-d'; // output like "2013-12-29"
    }
    else {
      $format = 'c'; // full ISO8601 output, like "2013-12-29T09:00:00+08:00"
    }

    // Serialize dates into strings
    $array['start'] = $this->start->format($format);
    if (isset($this->end)) {
      $array['end'] = $this->end->format($format);
    }

    return $array;
  }

}


// Date Utilities
//----------------------------------------------------------------------------------------------


// Parses a string into a DateTime object, optionally forced into the given timeZone.
function parseDateTime($string, $timeZone=null) {
  $date = new DateTime(
    $string,
    $timeZone ? $timeZone : new DateTimeZone('UTC')
      // Used only when the string is ambiguous.
      // Ignored if string has a timeZone offset in it.
  );
  if ($timeZone) {
    // If our timeZone was ignored above, force it.
    $date->setTimezone($timeZone);
  }
  return $date;
}


// Takes the year/month/date values of the given DateTime and converts them to a new DateTime,
// but in UTC.
function stripTime($datetime) {
  return new DateTime($datetime->format('Y-m-d'));
}

class FullCalendarController extends Controller
{
    public function getTimeZones()
    {
        return json_encode(DateTimeZone::listIdentifiers());
    }

    public function getEvents(Request $request)
    {
        // Short-circuit if the client did not give us a date range.
        if (!isset($request->start) || !isset($request->end)) {
            die("Please provide a date range.");
        }
        
        // Parse the start/end parameters.
        // These are assumed to be ISO8601 strings with no time nor timeZone, like "2013-12-29".
        // Since no timeZone will be present, they will parsed as UTC.
        $range_start = parseDateTime($request->start);
        $range_end = parseDateTime($request->end);
        
        // Parse the timeZone parameter if it is present.
        $time_zone = null;
        if (isset($request->timeZone)) {
            $time_zone = new DateTimeZone($request->timeZone);
        }
        
        // Read and parse our events JSON file into an array of event data arrays.
        // $json = file_get_contents(dirname(__FILE__) . '/../json/events.json');
        // $input_arrays = json_decode($json, true);
        
        // Accumulate an output array of event data arrays.
        $output_arrays = array();
        // foreach ($input_arrays as $array) {
        
        //     // Convert the input array into a useful Event object
        //     $event = new Event($array, $time_zone);
        
        //     // If the event is in-bounds, add it to the output
        //     if ($event->isWithinDayRange($range_start, $range_end)) {
        //     $output_arrays[] = $event->toArray();
        //     }
        // }
        
        // Send JSON to the client.
        return json_encode($output_arrays);
    }
}
