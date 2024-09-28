<?php

namespace Kanboard\Plugin\NotifyPlus\Helper;

use Kanboard\Core\Base;

class DateHelper extends Base
{
    public static function time_elapsed_string($datetime, $full = false) {
        $now = new \DateTime;
        $ago = new \DateTime("@$datetime");
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $diff->d -= $weeks * 7;

        $string = array(
            'y' => t('year'),
            'm' => t('month'),
            'w' => t('week'),
            'd' => t('day'),
            'h' => t('hour'),
            'i' => t('minute'),
            's' => t('second'),
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

        return $parts ? implode(', ', $parts) .' ' . t('ago') : t('just now');
    }
}