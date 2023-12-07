<?php

if ( ! function_exists("parse_date_to_gmt2_eloquent")) {
    function parse_date_to_gmt2_eloquent($date) {
        if ($date) {
            $dt = new DateTime($date);
            $tz = new DateTimeZone('Europe/Madrid');
            $dt->setTimezone($tz);
            return $dt->format('d/m/Y H:i:s');
        }
        return $date;
    }
}