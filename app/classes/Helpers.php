<?php
class Helpers {
    public static function getCurrentDate()
    {
        return date('d/m/Y');
    }
    public static function getCurrentMySQLDate()
    {
        return date('Y-m-d');
    }

    public static function getCurrentTime()
    {
        return date('H:i:s');
    }

    public static function toJSDate($date)
    {
        if ($date == '0000-00-00' || empty($date) || !isset($date))
        {
            return '';
        }
        else
        {
            return date('d/m/Y', strtotime($date));
        }
    }


    public static function fromMySQLToThaiDate($date)
    {
        if ($date == '0000-00-00' || empty($date) || !isset($date))
        {
            return '';
        }
        else
        {
            $newdate = explode('-', $date);
            $year = $newdate[0] + 543;
            $month = $newdate[1];
            $date = $newdate[2];

            return $date . '/' . $month . '/' . $year;
        }
    }

    public static function toMySQLDate($date)
    {
        if (empty($date) || !isset($date))
        {
            return '';
        }
        else
        {
            $new_date = date_create_from_format('d/m/Y', $date);
            $new_date = date_format($new_date, 'Y-m-d');

            return $new_date;
        }
    }

    public static function countAge($birth)
    {
        $obj = new stdClass();

        if ($birth && $birth != '0000-00-00')
        {
            $c_y = date('Y');
            $c_m = date('m');
            $c_d = date('d');

            $birth = date_create_from_format('Y-m-d', $birth);

            $b_y = date_format($birth, 'Y');
            $b_m = date_format($birth, 'm');
            $b_d = date_format($birth, 'd');

            if ($c_d >= $b_d) $age_d = $c_d - $b_d;
            else $c_m = $c_m - 1; $c_d = $c_d + 30; $age_d = $c_d - $b_d;

            if ($c_m >= $b_m) $age_m = $c_m - $b_m;
            else $c_y = $c_y - 1; $c_m = $c_m + 12; $age_m = $c_m - $b_m;

            $age_y = $c_y - $b_y;

            $obj->year = $age_y;
            $obj->month = $age_m;
            $obj->day = $age_d;
        }
        else
        {
            $obj->year = 0;
            $obj->month = 0;
            $obj->day = 0;
        }

        return $obj;
    }
}









