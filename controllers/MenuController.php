<?php

namespace app\controllers;

use app\modules\catalog\models\Category;
use Yii;
use app\modules\pages\models\Pages;
use app\modules\pages\models\PagesMenus;
use yii\helpers\Url;
use yii\filters\AccessControl;


class MenuController {
    static public function getPageParams(){
        return Pages::find()
                ->where(['url' => substr(Url::to(''),1)])
                ->asArray()
                ->one();
    }

    static public function getPageMenu(){
        $page = self::getPageParams();
        $menu = PagesMenus::find()
                ->where(['status' => 1])
                ->indexBy('key')
                ->groupBy('key')
                ->orderBy('key')
                ->asArray()
                ->all();

        foreach($menu as $key => $link){
            $page['menus'][$key] = PagesMenus::find()
                ->select([
                        'pages.id',
                        'pages.url',
                        'IF (pages_menus.name IS NOT NULL, pages_menus.name,pages.name) AS name',
                        'pages_menus.anchor',
                        'pages_menus.page_id'
                        ])
                ->joinWith(['pages'])
                ->andWhere('pages_menus.key = "'.$key.'"')
                ->andWhere('pages.level <= 2')
                ->andWhere('pages_menus.status = 1')
                ->andWhere('pages.status = 1')
                ->orderBy('pages_menus.position ASC')
                ->asArray()
                ->all();

            foreach ($page['menus'][$key] as $i=>$item) {
                // Обработка адреса страницы;
                $page['menus'][$key][$i]['url'] = $item['id'].($item['anchor'] ? '#'.$item['anchor'] : '');
            }
        }
        return $page;
    }

    public static function getCatalogTopMenu(){
        $removeCategories = [
            //10000051,
            //10000052,
            //10000053,
            //10000054,
            //10000055,
            //10000056,
            //10000057,
            //10000058,
            //10000059,
            //10000102,
            //10000096,
            //10000101,
            //10000112,
            //10000093,
            //10000094,
            //10000095,
        ];

        $groups = Category::find()->where(['active' => 1])->orderBy('level,sort')->asArray()->all();
        $groupsIndexed = [];


        if(!empty($groups)) {
            foreach($groups as $item){
                $groupsIndexed[$item['id']] = $item;

            }
            $menu = [];
            foreach ($groupsIndexed as $key => $group) {
                if ($group['parent_id'] == NULL) {
                    $menu[$group['id']] = $group;
                }
            }
            foreach ($groupsIndexed as $key => $group) {
                if($group['parent_id'] > 0){
                    if(isset($menu[$group['parent_id']])){
                        $menu[$group['parent_id']]['items'][$group['id']] = $group;
                    }else{
                        foreach($menu as $valueSub){
                            if(isset($valueSub['items'])){
                                foreach($valueSub['items'] as $k => $items){
                                    if($group['title'] == 'Перчатки'){
                                    }
                                    if($items['id'] == $group['parent_id']){
                                        $menu[$items['parent_id']]['items'][$group['parent_id']]['items'][$group['id']] = $group;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //print_arr($menu);die();
            return $menu;
        }
    }
}
