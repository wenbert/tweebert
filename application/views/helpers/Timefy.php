<?php
/**
 * Creates 24 minutes 5 seconds, etc.
 * Credits to: 
 * http://aidanlister.com/2004/04/making-time-periods-readable/
 */
class Zend_View_Helper_Timefy extends Zend_View_Helper_Abstract 
{
    public function timefy($time, $use = null, $zeros = false) 
    {
        $temp = date('Y-m-d H:i:s',strtotime($time));
        $seconds = strtotime(date('Y-m-d H:i:s'))-strtotime($temp);
        
        // Define time periods
        $periods = array (
            'years'     => 31556926,
            'Months'    => 2629743,
            'weeks'     => 604800,
            'days'      => 86400,
            'hours'     => 3600,
            'minutes'   => 60,
            'seconds'   => 1
            );

        // Break into periods
        $seconds = (float) $seconds;
        foreach ($periods as $period => $value) {
            if ($use && strpos($use, $period[0]) === false) {
                continue;
            }
            $count = floor($seconds / $value);
            if ($count == 0 && !$zeros) {
                continue;
            }
            $segments[strtolower($period)] = $count;
            $seconds = $seconds % $value;
        }

        // Build the string
        foreach ($segments as $key => $value) {
            $segment_name = substr($key, 0, -1);
            $segment = $value . ' ' . $segment_name;
            if ($value != 1) {
                $segment .= 's';
            }
            $array[] = $segment;
        }

        $str = implode(', ', $array);
    
        return $str.' ago ';
    }
}
