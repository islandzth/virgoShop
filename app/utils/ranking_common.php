<?php

class Ranking_Common
{
    public static function hot($ups, $downs, $created_at)
    {
        $score = $ups - $downs;
        $order = log10(max(abs($score), 1));

        if ($score > 0) {
            $sign = 1;
        } elseif ($score < 0) {
            $sign = -1;
        } else {
            $sign = 0;
        }

        //$seconds = intval(($created_at - mktime(0, 0, 0, 1, 1, 1970)) / 86400);
        $seconds = $created_at - 1387179472;
        $long_number = $order + $sign * $seconds / 45000;

        return round($long_number, 7);
    }

    public static function confidence($ups, $downs)
    {
        if ($ups + $downs == 0) {
            return 0;
        } else {
            $n = $ups + $downs;

            if ($n == 0) return 0;

            $z = 1.0;
            $phat = (float)$ups / $n;
            return sqrt($phat + $z * $z / (2 * $n) - $z * (($phat * (1 - $phat) + $z * $z / (4 * $n)) / $n)) / (1 + $z * $z / $n);
        }
    }
}