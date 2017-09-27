<?php

namespace app\modules\common\models;

class Menu extends \yii\db\ActiveRecord
{
    public static function getStructure($category = 0, $flag = false)
    {
        $fl = $flag;

        if ($category == 0) {
            $result = (new \yii\db\ActiveRecord())
                ->find()
                ->from('category')
                ->where('')
                ->orderby('sort ASC')
                ->asarray()
                ->all();
        } else {
            $result = (new \yii\db\ActiveRecord())
                ->find()
                ->from('category')
                ->where('category.id = ' . $category)
                ->orWhere('category.parent_id = ' . $category)
                ->orderby('sort ASC')
                ->asarray()
                ->all();
        }

        if (!$result) {
            return null;
        }

        // $arr_cat будет создаваться массив категорий, где индексы, это parent_id
        $arr_cat = array();

        //В цикле формируем массив
        for ($i = 0; $i < count($result); $i++) {
            $row = $result[$i];
            if ($row['parent_id'] == null) {
                $row['parent_id'] = 0;
                //$row['count'] = 0;
            }
            //Формируем массив, где ключами являются id родительской категории
            if (empty($arr_cat[$row['parent_id']])) {
                $arr_cat[$row['parent_id']] = array();
                //$arr_cat[$row['count']] = 0;
            }

            $arr_cat[$row['parent_id']][] = $row;
            //$arr_cat[$row['count']][] = count($row);
        }

        // $view_cat - лямда функция для создания массива категорий, который будет передан в отображение
        $view_cat = function ($data, $parent_id = 0) use (& $view_cat, & $fl) {
            $result = null;
            if (empty($data[$parent_id])) {
                return;
            }

            $result = array();

            //перебираем в цикле массив и выводим на экран
            for ($i = 0; $i < count($data[$parent_id]); $i++) {
                if ($i == 0 && $fl == true) {
                    $result[] = ['label'  => $data[$parent_id][$i]['title']
                        . "(" . count($data[$parent_id]) . ")",
                                 'url'    => 'catalog/'
                                     . $data[$parent_id][$i]['alias']/* . '/'
                                     . $data[$parent_id][$i]['id']*/,
                                 'alias'  => $data[$parent_id][$i]['alias'],
                                 //можно пометить какой либо пункт как активный
                                 'active' => $data[$parent_id][$i]['id'] == 8,

                                 'options' => ['class' => 'dropdown'],
                                 'items'   => $view_cat(
                                     $data, $data[$parent_id][$i]['id']
                                 ),];
                } else {
                    $result[] = ['label'  => $data[$parent_id][$i]['title'],
                                 'url'    => 'catalog/'
                                     . $data[$parent_id][$i]['alias']/* . '/'
                                     . $data[$parent_id][$i]['id']*/,
                                 'alias'  => $data[$parent_id][$i]['alias'],
                                 //можно пометить какой либо пункт как активный
                                 'active' => $data[$parent_id][$i]['id'] == 8,

                                 'options' => ['class' => 'dropdown'],
                                 'items'   => $view_cat(
                                     $data, $data[$parent_id][$i]['id']
                                 ),];
                }
                //рекурсия - проверяем нет ли дочерних категорий
            }
            return $result;
        };

        $result = $view_cat($arr_cat);
        return $result;
    }
}