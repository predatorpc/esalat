<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "messages_types".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 *
 * @property Messages[] $messages
 */
class ModFunctions extends \yii\db\ActiveRecord
{
    // Обработка цены;
    public static function money($value, $decimal = 0)
    {
        return number_format($value, $decimal, '.', ' ').' <small class="rubznak">p</small>';
    }
    // Обработка срезаем запятую цены;
    public static function moneyFloat($value, $decimal = 0)
    {
        return number_format($value, $decimal, '.', ' ');
    }
    // Форматирования цены;
    public static function moneyFormat($value, $decimal = 2)
    {
        return number_format($value, $decimal, '.', ' ');
    }
    // Обработка бонус;
    public static function bonus($value, $decimal = 0)
    {
        return number_format($value, $decimal, '.', ' ').' &beta;.';
    }
    // Обработка Фио;
    public static function userName($string,$type=false)
    {
        // Обработка данные;
        $string = trim($string);
        $string = rtrim($string, "!,.-");
        if($type) {
            // Выводить только имя;
            $string = preg_replace('#(.*)\s+(.*).*\s+(.*).*#usi', '$2', $string);
        }else{
            $string = preg_replace('#(.*)\s+(.).*\s+(.).*#usi', '$1 $2.$3.', $string);
        }
        return $string;
    }
    // Обработка дата;
    public static function date($date)
    {
        if (date("Y-m-d", strtotime($date)) == $date) {
            $timestamp = strtotime($date);
        } else {
            $timestamp = $date;
        }
        return date("d.m.Y", $timestamp);
    }
    // Обработка формат дата;
    public static function date_format($timestamp = false, $format = '%d.%m.%Y', $default_date = '')
    {
        if(isset($timestamp) && !empty($timestamp)) {
            $timestamp = strtotime($timestamp);
            $_win_from = array('%D', '%h', '%n', '%r', '%R', '%t', '%T');
            $_win_to = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
            if (strpos($format, '%e') !== false) {
                $_win_from[] = '%e';
                $_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
            }
            if (strpos($format, '%l') !== false) {
                $_win_from[] = '%l';
                $_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
            }
            // Замена дня недели;
            if (strpos($format, '%A') !== false) {
                $days = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
                $to = $days[date("w", $timestamp)];
                $format = str_replace('%A', $to, $format);
            }
            // Замена месяца;
            if (strpos($format, '%B') !== false) {
                $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
                $to = $months[date("n", $timestamp) - 1];
                $format = str_replace('%B', $to, $format);
            }
            $format = str_replace($_win_from, $_win_to, $format);
            return strftime($format, $timestamp);
        }else{
            return date("d.m.Y");
        }
    }
    // Обработка дата время;
    public static function datetime($date)
    {
        if (date("Y-m-d H:i:s", strtotime($date)) == $date) {
            $timestamp = strtotime($date);
        } else {
            $timestamp = $date;
        }
        return date("d.m.Y в H:i", $timestamp);
    }
    // Обработка телефон;
    public static function phone($phone)
    {
        return str_replace('+7', '', $phone);
    }
    public static function variations_id($variations_id)
    {
        $variations_id = explode(':', $variations_id);
        return $variations_id[1];
    }
    // Окончание для числительных;
    public static function pluralForm($number, $after) {
        $cases = array (2, 0, 1, 1, 1, 2);
        echo $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
    }
    //NumToStr(int, 'секунду', 'секунды', 'секунд')
    // Окончание для числительных;
    public static function NumToStr($num, $end1, $end2, $end3) {
        $num100 = $num % 100;
        $num10 = $num % 10;
        if ($num100 >= 5 && $num100 <= 20) $end = $end3;
        else if ($num10 == 0) $end = $end3;
        else if ($num10 == 1) $end = $end1;
        else if ($num10 >= 2 && $num10 <= 4) $end = $end2;
        else if ($num10 >= 5 && $num10 <= 9) $end = $end3;
        else $end = $end3;
        return number_format($num, 0, '.', ' ').' '.$end;
    }
    // Сокращеный чисел;
    public static function numberSize($size)
    {
        $name = array("", "К", "М", "Г", "Т", "П", "Э", "З", "И");
        return $size ? round($size / pow(1000, ($i = floor(log($size, 1000)))), 2) .' '. $name[$i] : '0';
    }

    // Обработка фотография;
    public static function img_resize($src, $dest, $width, $height =0, $rgb = 0xFFFFFF, $quality = 100)
    {
        if (!file_exists($src))
            return false;

        $size = getimagesize($src);

        if ($size === false)
            return false;

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $icfunc = 'imagecreatefrom'.$format;

        if (!function_exists($icfunc))
            return false;

        $x_ratio = $width  / $size[0];
        $y_ratio = $height / $size[1];

        if ($height == 0)
        {
            $y_ratio = $x_ratio;
            $height  = $y_ratio * $size[1];
        }
        elseif ($width == 0)
        {
            $x_ratio = $y_ratio;
            $width   = $x_ratio * $size[0];
        }

        $ratio       = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);

        $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
        $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
        $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width)   / 2);
        $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

        // если не нужно увеличивать маленькую картинку до указанного размера
        if ($size[0]<$new_width && $size[1]<$new_height)
        {
            $width = $new_width = $size[0];
            $height = $new_height = $size[1];
        }

        $isrc  = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);

        $i = strrpos($dest,'.');
        if (!$i) return '';
        $l = strlen($dest) - $i;
        $ext = substr($dest,$i+1,$l);
        // Если png создаем прозрачность фон;
        if($ext == 'png') {
            $transparent = imagecolorallocatealpha($idest, 0, 0, 0, 127);
            imagefill($idest, 0, 0, $transparent);
            imagesavealpha($idest, true);

        }else{
             imagefill($idest, 0, 0, $rgb);
        }
        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

        switch ($ext)
        {
            case 'jpeg':
            case 'jpg':
                imagejpeg($idest,$dest,$quality);
                break;
            case 'gif':
                imagegif($idest,$dest);
                break;
            case 'png':
                imagepng($idest,$dest);
                break;
        }
        imagedestroy($isrc);
        imagedestroy($idest);
        return true;
    }

    // Проверка изображения;
    public static function img_path($dir) {
        if(!empty($dir) && file_exists($_SERVER['DOCUMENT_ROOT'].$dir)) {
             return true;
        }
        return false;
    }

}