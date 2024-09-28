<?php

namespace Kanboard\Plugin\NotifyPlus\Helper;

class DateHelper
{
    public static function time_elapsed_string($datetime, $full = false) {
        $now = new \DateTime;
        $ago = new \DateTime("@$datetime");
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $diff->d -= $weeks * 7;

        $string = array(
            'y' => 'año',
            'm' => 'mes',
            'w' => 'semana',
            'd' => 'día',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        );

        $parts = [];
        foreach ($string as $k => $v) {
            if ($k === 'w') {
                if ($weeks) {
                    $parts[] = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
                }
            } elseif (isset($diff->$k) && $diff->$k) {
                $parts[] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
        }

        if (!$full) {
            $parts = array_slice($parts, 0, 1);
        }

        return $parts ? 'hace ' . implode(', ', $parts) : 'justo ahora';
    }
}