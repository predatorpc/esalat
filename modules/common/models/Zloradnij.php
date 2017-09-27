<?php

namespace app\modules\common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $level
 * @property string $title
 * @property string $alias
 * @property integer $sort
 * @property integer $active
 *
 * @property Category $parent
 * @property Category[] $categories
 */
class Zloradnij
{
    public $sortField;

    public static function print_arr($list){
        print '<pre style="text-align: left;">';
        print_r($list);
        print '</pre>';
    }

    public static function printArray($list){
    print '<pre style="text-align: left;">';
    print_r($list);
    print '</pre>';
}

    public static function pluralForm($number, $after) {
        $cases = array (2, 0, 1, 1, 1, 2);
        echo $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
    }

}
