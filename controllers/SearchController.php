<?php

namespace app\controllers;

use app\modules\common\controllers\FrontController;
use Yii;
use yii\filters\VerbFilter;

//Морфей
use phpMorphy;
use yii\filters\AccessControl;

////////////////////////////////////////////////////////////////////////////////
// Описание
////////////////////////////////////////////////////////////////////////////////
//
// Поисковый модуль для поиска по базе данных, в товарах им категориях
// Последние обновление 30/05/2016 @ 16-59
//
////////////////////////////////////////////////////////////////////////////////
// SETTINGS
////////////////////////////////////////////////////////////////////////////////
// Установки для управления модулем поиска
// SEARCH_DEV_MODE - Включение режима разработки (отладочная инфа)
// SEARCH_STAT_MODE - Только статистика по выбранным категориям и товарам
// SEARCH_DEV_DEBUG_LEVEL - уровень режима отладки (0-4) мин-макс
// SEARCH_DEV_MAX_TAGS_RESULT - макс. кол-во тэгов в выдаче поиска
define("VERSION","2.3.1a");
define("SEARCH_DEV_MODE",false);
define("SEARCH_STAT_MODE",false);
//define("SEARCH_DEV_MODE",true);
//define("SEARCH_STAT_MODE",true);
define("SEARCH_DEV_DEBUG_LEVEL",4);
define("SEARCH_CAT_MAX_SCORE",1); // Баллы для прохода в результат категорий
define("SEARCH_GOOD_MAX_SCORE",1);  //Баллы для прохода в результат товаров
define("SEARCH_MAX_WORD",2);
define("SEARCH_DEV_MAX_TAGS_RESULT",3);

class SearchController extends FrontController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    //Убирам рпедлоги из строки
    public static function typoSearch($str){
        $str = trim($str);
        $predlogs=array('в', 'без','до','из','к','на','по','о','от','перед','при','через','с', 'со', 'у','и','нет','за','над','для', 'об', 'под', 'про');
        foreach($predlogs as $p){
            $str = str_replace(' '.$p.' ',' ',$str);
            $str = str_replace('  ',' ',$str);
        }
        return $str;
    }

    public static function utf8_str_split($str) {
        // place each character of the string into and array
        $split=1;
        $array = array();
        for ( $i=0; $i < strlen( $str ); ){
            $value = ord($str[$i]);
            if($value > 127){
                if($value >= 192 && $value <= 223)
                    $split=2;
                elseif($value >= 224 && $value <= 239)
                    $split=3;
                elseif($value >= 240 && $value <= 247)
                    $split=4;
            }else{
                $split=1;
            }
            $key = NULL;
            for ( $j = 0; $j < $split; $j++, $i++ ) {
                $key .= $str[$i];
            }
            array_push( $array, $key );
        }
        return $array;
    }

    public static function clearstr($str){
        $sru = 'ёйцукенгшщзхъфывапролджэячсмитьqwertyuiopasdfghjklzxcvbnm';
        $s1 = array_merge(self::utf8_str_split($sru), self::utf8_str_split(strtoupper($sru)), range('A', 'Z'), range('a','z'), range('0', '9'), array('&',' ','#',';','%','?',':','(',')','-','_','=','+','[',']',',','.','/','\\'));
        $codes = array();
        for ($i=0; $i<count($s1); $i++){
            $codes[] = ord($s1[$i]);
        }
        $str_s = self::utf8_str_split($str);
        for ($i=0; $i<count($str_s); $i++){
            if (!in_array(ord($str_s[$i]), $codes)){
                $str = str_replace($str_s[$i], '', $str);
            }
        }
        return $str;
    }

    public function actionIndex()
    {

        $this->view->registerCssFile('css/catalog.css');

        //get connection
        $db = Yii::$app->getDb();
        ////////////////////////////////////////////////////////////////////////////////
        // VARS
        ////////////////////////////////////////////////////////////////////////////////
        $activeCat = [];  //Список активных категорий
        $activeCatTags = [];  //Список категорий из Тэгов
        $activeGoodsStat = [];
        $catStat = []; // Статистика категорий
        $arrTmp = []; // Временный массив
        $sortedTags = []; //Сортированные тэги
        $activeGoods = []; //Список активных товаров
        $sortedGoods = []; //Сортированный список товаров
        $sortedCats = []; // Сортированные категории
        $sortedMaybeCats = []; //Сортированные возможные категории
        $sortedMaybeGoods = []; //Сортированные возможные товары
        $counter=0;
		$result=[];
		$error='';

        //Опции морфея
        $opts = array(
            'storage' => phpMorphy::STORAGE_FILE,
            'predict_by_suffix' => true,
            'predict_by_db' => true,
            'graminfo_as_text' => true,
        );
        $lang = 'ru_RU';
        try {
        //new morphy
              $morphy = new phpMorphy(Yii::getAlias('@morphy').'/vendor/makhov/phpmorphy/dicts', $lang, $opts);
        //    $morphy = new phpMorphy($dir, $lang, $opts);
        } catch(phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
        }

        $searchText = Yii::$app->request->post('search'); //Полчаем поисковую строку

         if(empty($searchText))
                $searchText = Yii::$app->request->get('search'); //Полчаем поисковую строку

        $search['text'] = $searchText; //Поисковая строка в массив страницы для смарти
        $searchText = self::typoSearch($searchText);
        $searchParam = Yii::$app->request->get('category');//Получаем параметры если есть

        ////////////////////////////////////////////////////////////////////////////////
        // START SEARCH
        ////////////////////////////////////////////////////////////////////////////////
        // Проверяем строку поиска и все остальное тоже параметры и настраиваем
        // морфея, разбиваем строки на массивы
        $mas = preg_split("##u",$search['text'],-1,1);
        $mascount = array_count_values($mas);

        if (count($mascount) == 1) {
            return $this->render('result', [
                'error' => 'Уточните условия поиска',
            ]);
            //$search['error'] = 'Уточните условия поиска';
        }
        else if (mb_strlen($search['text']) >= 3) {
            $searchText = self::clearstr($searchText); //выбрасываем спецсимволы
            $searchText = mb_strtoupper($searchText); //Переводим все в ВЕРХНИЙ РЕГИСТР для использования морфея
            $searchWordList = explode(' ', $searchText); //разделяем по пробелу

            if (SEARCH_DEV_MODE) {
                echo '<h1>Режим разработки поискового модуля '.VERSION.'</h1>';
                echo '<h2>Исходный текст</h2>';
                var_dump($search['text']);
                echo '<h2>Исходные параметры</h2>';
                var_dump($searchParam);
                echo '<h2>Обработанный текст</h2>';
                var_dump($searchText);
                echo '<h2>Что будем искать?</h2>';
                var_dump($searchWordList);
                foreach ($searchWordList as $item) {
                    var_dump($morphy->getPseudoRoot($item));
                    var_dump($morphy->getPartOfSpeech($item));
                }
            }
            //Устанавливаем кодировку поиска у нас УТФ-8 и словарь такой-же
            if (function_exists('iconv')) {
                foreach ($searchWordList as &$word) {
                    $word = iconv('utf-8', $morphy->getEncoding(), $word);
                }
                unset($word);
            }

            if (SEARCH_STAT_MODE || SEARCH_STAT_MODE) {
                echo "<h1>КАТЕГОРИИ</h1>";
            }

        ////////////////////////////////////////////////////////////////////////////////
        // CAT SEARCH
        ////////////////////////////////////////////////////////////////////////////////
        //  Выбираем только категории, и заносим их в наш список активных категорий,
        //  парралаельно добавляем статистику в соседний массив

            $catFlag=false; //Флажок для составления и исполнения запросов
            $sqlOR = "SELECT `id`,`title` FROM `category` WHERE `title` LIKE";
            $sqlAND = "SELECT `id`,`title` FROM `category` WHERE `title` LIKE";

            for ($i = 0; $i < count($searchWordList); $i++) {
                //Определяем часть речи
                $root = $morphy->getPseudoRoot($searchWordList[$i]);
                $root2 = $morphy->getPartOfSpeech($searchWordList[$i]);
                //Составляем 2 запроса И и ИЛИ к базе динамически
                //только из существительных
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                    echo "<h3>Части речи</h3>";
                    var_dump($root2[0]);
                    var_dump($searchWordList[$i]);
                }
                //если слово на Русском языке у него есть корень, если нет, то Анлийское
                if (!empty($root[0])) {
                    //if (!empty($root2[0])) {
                    if ($root2[0]=='С') {
                        if ($catFlag==true)
                        {
                            $sqlOR .= ' OR `title` LIKE ';
                            $sqlAND .= ' AND `title` LIKE ';
                        }
                        $sqlOR .= " '$root[0]%' ";
                        $sqlAND .= " '$root[0]%' ";
        //                $sqlOR .= " '%$root[0]%' ";
        //                $sqlAND .= " '%$root[0]%' ";
                                $next = $morphy->getPartOfSpeech($searchWordList[$i]);

                        if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                            && $next == 'С'
                        ) {
                            $sqlOR .= ' OR `title` LIKE ';
                            $sqlAND .= ' AND `title` LIKE ';
                        }
                        $catFlag=true;
                    }
                    //Статус проверяем тоже
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `active`=1 ';
                        $sqlAND .= ' AND `active`=1 ';
                    }
                } else {
                    if ($catFlag==true)
                    {
                        $sqlOR .= ' OR `title` LIKE ';
                        $sqlAND .= ' AND `title` LIKE ';
                    }
                    //Повторяем все тоже самое для Анлийского языка
                    $sqlOR .= " '%$searchWordList[$i]%' ";
                    $sqlAND .= " '%$searchWordList[$i]%' ";
                    $next = $morphy->getPartOfSpeech($searchWordList[$i]);

                    if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                        && $next == 'С'
                    ) {
                        $sqlOR .= ' OR `title` LIKE ';
                        $sqlAND .= ' AND `title` LIKE ';
                    }
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `active`=1 ';
                        $sqlAND .= ' AND `active`=1 ';
                    }
                    $catFlag=true;
                }
                if($i>=SEARCH_MAX_WORD) break;
            }
            if($catFlag==true) {
                $responseOR =  $db->createCommand($sqlOR)->queryAll();
                 $responseAND = $db->createCommand($sqlAND)->queryAll();
                //Получаем ответы и разбираем, что у нас там
                $responseANDOR = array_merge($responseOR, $responseAND);
            }

            if (!empty($responseANDOR)) {
                foreach ($responseANDOR as $item) {
                    if (!empty($item)) {
                        $catId = $item['id'];

                        if (!empty($catId)) {
                            $sql
                                = "SELECT `id`,`alias`,`parent_id`,`title` FROM `category`"
                                . " WHERE `id` = " . $catId;
                            $current = $db->createCommand($sql)->queryAll();
                            $activeCat[$catId]['id'] = $catId;
                            $activeCat[$catId]['parent'] = $current[0]['parent_id'];
                            $activeCat[$catId]['title'] = $current[0]['title'];
                            $activeCat[$catId]['alias'] = $current[0]['alias'];
                            //+1000 очков для категории полученной сразу
                            if(!empty($catStat[$catId]))
                                $catStat[$catId] += 1000;
                            else
                                $catStat[$catId] = 1000;
                        }
                    }
                }
            }
            //Доп информация для отладки
            if (SEARCH_DEV_MODE && $catFlag==true) {
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                    echo '<h2>Выбираем из названий категорий 1 - (' . count($responseANDOR) . ')</h2>';
                    echo '<h3>SQL Запросы</h3>';
                    var_dump($sqlOR);
                    var_dump($sqlAND);
                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                        echo '<h3>Результат в responseANDOR</h3>';
                        var_dump($responseANDOR);
                        echo '<h3>Результат в catStat</h3>';
                        var_dump($catStat);
                    }
                }
            }
            //выводим статистику, можем вывести только статистику если нужно
            if (SEARCH_STAT_MODE && $catFlag==true) {
                echo '<h3>Результат статистики Категорий после поиска в Категориях</h3>';
                var_dump($catStat);
                var_dump($activeCat);
            }

            if (SEARCH_STAT_MODE || SEARCH_STAT_MODE) {
                echo "<h1>ТОВАРЫ</h1>";
            }

        ////////////////////////////////////////////////////////////////////////////////
        // GOODS SEARCH
        ////////////////////////////////////////////////////////////////////////////////
        // Проверяем наличие таких слов в товарах
        // потом поттягиваем товар чтобы пробить шоп ИД и статус + ИД ШОПА и статус шопа
        // товары с кривым статусом и с кривым шопом (-666/-1/0)
        // не выводим, потом все ищем и...
        // И добавляем в массив активных категорий $activeCat[]

            $goodsFlag=false;
            $sqlOR = "SELECT `id`,`name`,`status` FROM `goods` WHERE `name` LIKE ";
            $sqlAND = "SELECT `id`,`name`,`status` FROM `goods` WHERE `name` LIKE ";

            for ($i = 0; $i < count($searchWordList); $i++) {
                //Определяем часть речи
                $root = $morphy->getPseudoRoot($searchWordList[$i]);
                $root2 = $morphy->getPartOfSpeech($searchWordList[$i]);
                //Составляем 2 запроса И и ИЛИ к базе динамически
                //только из существительных
                //если слово на Русском языке у него есть корень, если нет, то Анлийское
                //Остальное тоже самое что и в предыдущем поиске
                if (!empty($root[0])) {
                    if (!empty($root2[0])) {
                        if ($goodsFlag==true)
                        {
                            $sqlOR .= ' OR `name` LIKE ';
                            $sqlAND .= ' AND `name` LIKE ';
                        }
                        $sqlOR .= " '$root[0]%' ";
                        $sqlAND .= " '$root[0]%' ";
        //                $sqlOR .= " '%$root[0]%' ";
        //                $sqlAND .= " '%$root[0]%' ";
                        $next = $morphy->getPartOfSpeech($searchWordList[$i]);

                        if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                            && $next == 'С'
                        ) {
                            $sqlOR .= ' OR `name` LIKE ';
                            $sqlAND .= ' AND `name` LIKE ';
                        }
                        $goodsFlag=true;
                    }
                    //проверяем статусы
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `status`=1 ';
                        $sqlAND .= ' AND `status`=1 ';
                    }

                } else {
                    if ($goodsFlag==true)
                    {
                        $sqlOR .= ' OR `name` LIKE ';
                        $sqlAND .= ' AND `name` LIKE ';
                    }
                    $sqlOR .= " '%$searchWordList[$i]%' ";
                    $sqlAND .= " '%$searchWordList[$i]%' ";

                    $next = $morphy->getPartOfSpeech($searchWordList[$i]);

                    if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                        && $next == 'С'
                    ) {
                        $sqlOR .= ' OR `name` LIKE ';
                        $sqlAND .= ' AND `name` LIKE ';
                    }
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `status`=1 AND `show`=1 ';
                        $sqlAND .= ' AND `status`=1 AND `show`=1 ';
                    }
                    $goodsFlag=true;
                }
                if($i>=SEARCH_MAX_WORD) break;
            }

            if($goodsFlag==true) {
                //получаем ответы
                $responseOR =  $db->createCommand($sqlOR)->queryAll();
                $responseAND = $db->createCommand($sqlAND)->queryAll();
                $responseANDOR = array_merge($responseOR, $responseAND);
            }

            //разбираем ответ по полочка, что товар, а что категория
            if (!empty($responseANDOR)) {
                foreach ($responseANDOR as $item) {
                    $sql = "SELECT `category_id` FROM `category_links` "
                        . " WHERE `product_id`=" . $item['id']
                        . " GROUP BY `category_id`";
                    $catIdentifier = $db->createCommand($sql)->queryScalar();
                    if(!empty($catIdentifier))
                    {

                        $sql = "SELECT `id` FROM `category` WHERE `id`=".$catIdentifier." AND `active`=1";
                        $catId = $db->createCommand($sql)->queryScalar();

                        $sql
                            = "SELECT `status`,`name`,`full_name` FROM `goods` WHERE `id`=".$item['id']." AND `show`=1";
                        //var_dump($sql);
                        $goodStatus = $db->createCommand($sql)->queryAll();//$db->all($sql);

                        //FIX  shop_id_  последнее подчеркивание
                        $sql
                            = "SELECT `status`,`name`,`full_name`,`shop_id` FROM `goods` WHERE `id`="
                            . $item['id']. "  AND `show`=1";
                        $goodStatus = $db->createCommand($sql)->queryAll();//$db->all($sql);

                        if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                            echo '<h2>Выбираем из названий товаров 1 - (' . count(
                                    $responseANDOR
                                ) . ')</h2>';
                            var_dump($catId);
                            var_dump($sql);
                            var_dump($goodStatus);
                        }

        $sql = "SELECT shops.status
            FROM goods
            LEFT JOIN shop_group_variant_link ON shop_group_variant_link.product_id = goods.id
            LEFT JOIN shop_group ON shop_group.id = shop_group_variant_link.shop_group_id
            LEFT JOIN shop_group_related ON shop_group_related.shop_group_id = shop_group.id
            LEFT JOIN shops ON shops.id = shop_group_related.shop_id
            WHERE shops.status =1
            AND shop_group.status =1 AND goods.id = ". $item['id'] ." GROUP BY shops.id";
                    $shopStatus = $db->createCommand($sql)->queryScalar();//$db->all($sql);

                        if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {

                            var_dump($sql);
                            var_dump($shopStatus);
                        }

                        if (!empty($goodStatus[0]['status']) == "1"
                        && !empty($shopStatus) == "1"
                        ) {
                            //если задан поиск, то выбираем еще товары из данного контекста
                            //пишем все и статистику считаем
                            if (intval($searchParam) == intval($catId)) {
                                $activeGoods[$item['id']]['name'] = $goodStatus[0]['name'];
                                $activeGoods[$item['id']]['full_name']
                                    = $goodStatus[0]['full_name'];
                                $activeGoods[$item['id']]['shop_id'] = $goodStatus[0]['shop_id'];
                                //+100 очков если товар найден по названию товара (основного)

                            if(!empty($activeGoodsStat[$item['id']]))
                                $activeGoodsStat[$item['id']]+= 100;
                            else
                                $activeGoodsStat[$item['id']] = 100;
                            }

                            //определяем категории пишем в массивчик
                            if (!empty($item)) {
                                if (!empty($catId)) {
                                    $sql
                                        = "SELECT `id`,`alias`,`parent_id`,`title` FROM `category`"
                                        . " WHERE `id` = " . $catId;
                                    $current = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);


                                    $activeCat[$catId]['id'] = $catId;
                                    $activeCat[$catId]['parent'] = $current[0]['parent_id'];
                                    $activeCat[$catId]['title'] = $current[0]['title'];
                                    $activeCat[$catId]['alias'] = $current[0]['alias'];
                                    //+100 очков если категория найдена по названию товара (основного)

                                    if(!empty($catStat[$catId]))
                                        $catStat[$catId] += 100;
                                    else
                                        $catStat[$catId] = 100;

                                }
                            }
                        }
                    }
                }
            }

            //доп информация для отладки
            if (SEARCH_DEV_MODE) {
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                    echo '<h2>Выбираем из названий товаров 2 - (' . count($responseANDOR) . ')</h2>';
                    echo '<h3>SQL запросы</h3>';
                    var_dump($sqlOR);
                    var_dump($sqlAND);
                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                        echo '<h3>Результат в responseANDOR</h3>';
                        var_dump($responseANDOR);
                    }
                }
            }

            //статистика
            if (SEARCH_STAT_MODE) {
                echo '<h3>Результат статистики Категорий после поиска в Товарах</h3>';
                var_dump($activeCat);
                var_dump($catStat);
                echo '<h3>Результат статистики Товаров после поиска в Товарах</h3>';
                var_dump($activeGoods);
                var_dump($activeGoodsStat);

            }

            if (SEARCH_STAT_MODE || SEARCH_STAT_MODE) {
                echo "<h1>ВАРИАЦИИ</h1>";
            }

        ////////////////////////////////////////////////////////////////////////////////
        // VARIATIONS SEARCH
        ////////////////////////////////////////////////////////////////////////////////
        // Проверяем наличие таких слов в названии вариаций товаров
        // потом поттягиваем товар чтобы пробить шоп ИД и статус
        // товары с кривым статусом и с кривым шопом (-666/-1/0)
        // не выводим, потом все ищем и...
        // И добавляем в массив активных категорий $activeCat[]

            $varFlag = false;
            $sqlOR
                = "SELECT `good_id`,`full_name` FROM `goods_variations` WHERE `full_name` LIKE ";
            $sqlAND
                = "SELECT `good_id`,`full_name` FROM `goods_variations` WHERE `full_name` LIKE ";

            for ($i = 0; $i < count($searchWordList); $i++) {
                //Определяем часть речи
                $root = $morphy->getPseudoRoot($searchWordList[$i]);
                $root = $morphy->getPseudoRoot($searchWordList[$i]);
                $root2 = $morphy->getPartOfSpeech($searchWordList[$i]);
                //Составляем 2 запроса И и ИЛИ к базе динамически
                //только из существительных
                if (!empty($root[0])) {
                    //если слово Русское, то есть корень
                    if ($root2[0]=='С') {
                        if ($varFlag==true)
                        {
                            $sqlOR .= ' OR `full_name` LIKE ';
                            $sqlAND .= ' AND `full_name` LIKE ';
                        }
                        $sqlOR .= " '$root[0]%' ";
                        $sqlAND .= " '$root[0]%' ";
        //                $sqlOR .= " '%$root[0]%' ";
        //                $sqlAND .= " '%$root[0]%' ";
                        $next = $morphy->getPartOfSpeech($searchWordList[$i]);
                        if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                            && $next == 'С'
                        ) {
                            $sqlOR .= ' OR `full_name` LIKE ';
                            $sqlAND .= ' AND `full_name` LIKE ';
                        }
                        $varFlag = true;
                    }
                    //проверяем статус
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `status`=1 ';
                        $sqlAND .= ' AND `status`=1 ';
                    }
                } else {
                    if ($varFlag==true)
                    {
                        $sqlOR .= ' OR `full_name` LIKE ';
                        $sqlAND .= ' AND `full_name` LIKE ';
                    }
                    $sqlOR .= " '%$searchWordList[$i]%' ";
                    $sqlAND .= " '%$searchWordList[$i]%' ";
                    if ($i >= 0 && ($i != intval(count($searchWordList)) - 1)
                        && $next == 'С'
                    ) {
                        $sqlOR .= ' OR `full_name` LIKE ';
                        $sqlAND .= ' AND `full_name` LIKE ';
                    }
                    if ($i == intval(count($searchWordList)) - 1) {
                        $sqlOR .= ' AND `status`=1 ';
                        $sqlAND .= ' AND `status`=1 ';
                    }
                    $varFlag = true;
                }
            }
            if($varFlag==true) {
                //разбираем ответы получаем результаты
                $responseOR = $db->createCommand($sqlOR)->queryAll();//$db->all($sqlOR);
                $responseAND = $db->createCommand($sqlAND)->queryAll();//$db->all($sqlAND);
                $responseANDOR = array_merge($responseOR, $responseAND);
            }
            //разрбрать по полочкам
            foreach ($responseANDOR as $item) {
                if(!empty($item['good_id'])){
                    $sql
                        = "SELECT `status`,`name`,`full_name`,`shop_id` FROM `goods` WHERE `id`="
                        . $item['good_id']." AND `show`=1 ";
                    $goodStatus = $db->createCommand($sql)->queryAll();//$db->all($sql);

                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                        echo '<b>Статус товара:</b><br>';
                        var_dump($sql);
                        var_dump($goodStatus);
                    }

        $sql = "SELECT shops.status
            FROM goods
            LEFT JOIN shop_group_variant_link ON shop_group_variant_link.product_id = goods.id
            LEFT JOIN shop_group ON shop_group.id = shop_group_variant_link.shop_group_id
            LEFT JOIN shop_group_related ON shop_group_related.shop_group_id = shop_group.id
            LEFT JOIN shops ON shops.id = shop_group_related.shop_id
            WHERE shops.status =1
            AND shop_group.status =1 AND goods.id = ". $item['good_id'] ." GROUP BY shops.id";
                    $shopStatus = $db->createCommand($sql)->queryScalar();//$db->one($sql);

                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                        echo '<b>Статус магазина:</b><br>';
                        var_dump($sql);
                        var_dump($shopStatus);
                    }

                    if (!empty($goodStatus[0]['status']) == "1"
                    && $shopStatus == "1"
                    ) {

                        $sql = "SELECT `category_id` FROM `category_links` "
                            . " WHERE `product_id`=" . $item['good_id']
                            . " GROUP BY `category_id`";
                        $catIdentifier = $db->createCommand($sql)->queryScalar();//$db->one($sql);

                        if(!empty($catIdentifier))
                        {

                            $sql = "SELECT `id` FROM `category` WHERE `id`=".$catIdentifier." AND `active`=1";
                            $catId = $db->createCommand($sql)->queryScalar();//$db->one($sql);

                            if (intval($searchParam) == intval($catId)) {
                                $activeGoods[$item['good_id']]['name'] = $goodStatus[0]['name'];
                                $activeGoods[$item['good_id']]['full_name']
                                    = $goodStatus[0]['full_name'];
                                $activeGoods[$item['good_id']]['shop_id'] = $goodStatus[0]['shop_id'];
                                //Товару тоже даем +10 очков за вариацию

                                if(!empty($activeGoodsStat[$item['good_id']]))
                                    $activeGoodsStat[$item['good_id']]+= 100;
                                else
                                    $activeGoodsStat[$item['good_id']] = 100;
                                }

                                if (!empty($catId)) {
                                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                                        var_dump($sql);
                                    }
                                    $sql = "SELECT `id`,`alias`,`parent_id`,`title` FROM `category`"
                                        . " WHERE `id` = " . $catId;
                                    $current = $db->createCommand($sql)->queryAll();//$db->all($sql);
                                    $activeCat[$catId]['id'] = $catId;
                                    $activeCat[$catId]['parent'] = $current[0]['parent_id'];
                                    $activeCat[$catId]['title'] = $current[0]['title'];
                                    $activeCat[$catId]['alias'] = $current[0]['alias'];
                                    //Категории даем, +10 очков за то, что она найдена в вариациях

                                    if(!empty($catStat[$catId]))
                                        $catStat[$catId] = $catStat[$catId] + 10;
                                    else
                                        $catStat[$catId] = 10;
                                }

                        }
                    }
                }
            }

            //отладочная информация
            if (SEARCH_DEV_MODE) {
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                    echo '<h2>Выбираем из названий вариаций - (' . count($responseANDOR)
                        . ')</h2>';
                    echo '<h3>SQL запросы</h3>';
                    var_dump($sqlOR);
                    var_dump($sqlAND);

                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                        echo '<h3>Результат в responseANDOR</h3>';
                        var_dump($responseANDOR);
                    }
                }
            }

            //Дополнительная статистика уже солидная сборочка
            if (SEARCH_STAT_MODE) {
                echo '<h3>Результат статистики Категорий после поиска в Вариациях</h3>';
                var_dump($activeCat);
                var_dump($catStat);
                echo '<h3>Результат статистики Товаров после поиска в Вариациях</h3>';
                var_dump($activeGoods);
                var_dump($activeGoodsStat);

            }

            if (SEARCH_STAT_MODE || SEARCH_STAT_MODE) {
                echo "<h1>ТЭГИ</h1>";
            }

        ////////////////////////////////////////////////////////////////////////////////
        // TAGS SEARCH
        ////////////////////////////////////////////////////////////////////////////////
        // выбираем тэги которые совпадают по условию ИЛИ, потом собираем вариации
        // которые имеют эти тэги, и выбираем товары, проверяем статусы и шопы.

            //if(!empty($searchParam)) {
            if (SEARCH_DEV_MODE) {
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                    echo '<h2>Выбираем из названий тэгов относящихся к вариациям</h2>';
                }
            }
            $wordCounter = 0;
            ///спрашиваем нет ли похожих тэгов в базе
            foreach ($searchWordList as $word) {
                if ($wordCounter >= SEARCH_MAX_WORD) {
                    break;
                }
                //Определяем части речи
                $root = $morphy->getBaseForm($word);
                $root1 = $morphy->lemmatize($word);
                $root2 = $morphy->getPartOfSpeech($word);
                $root3 = $morphy->getPseudoRoot($word);
                $root4 = $morphy->getAllForms($word);

                if (!empty($root3[0])) {
                    $sql
                        = "SELECT `id` FROM `tags` WHERE `value` LIKE '%$root3[0]%'"
                        . " GROUP BY `id` ORDER BY `id` DESC";
                    $response = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);

                    //отладочная информация
                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                        echo '<h3>SQL Запрос</h3>';
                        var_dump($sql);
                        echo '<h3>Результат в response</h3>';
                        var_dump($response);
                    }
                    if (!empty($response)) {
                        foreach ($response as $item) {
                            //Получили список вариаций содержащих одно из поисковых слов
                            $sql
                                = "SELECT `variation_id` FROM `tags_links` WHERE `tag_id` = "
                                . $item['id']
                                . " GROUP BY `variation_id` ORDER BY `variation_id` ASC";

                            $query = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);

                            if (!empty($query)) {
                                foreach ($query as $value) {
                                    //находим товары, чти это вариации
                                    $sql
                                        = "SELECT `good_id` FROM `goods_variations` WHERE `id`="
                                        . $value['variation_id']
                                        . " GROUP BY `good_id` ORDER BY `good_id` DESC";
                                    $goodId = $db->createCommand($sql)->queryScalar();//$db->all($sql);$db->one($sql);
                                        $sql
                                            = 'SELECT `status`,`name`,`full_name`,`shop_id` FROM `goods` WHERE `id`='.$goodId.' AND `status`=1 AND `show`=1';
                                        $goodStatus = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);

                                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 3) {
                                        echo '<b>Статус товара:</b><br>';
                                        var_dump($sql);
                                        var_dump($goodStatus);
                                    }

                                        if(!empty($goodStatus[0]['status'])) {

        $sql = "SELECT shops.status
            FROM goods
            LEFT JOIN shop_group_variant_link ON shop_group_variant_link.product_id = goods.id
            LEFT JOIN shop_group ON shop_group.id = shop_group_variant_link.shop_group_id
            LEFT JOIN shop_group_related ON shop_group_related.shop_group_id = shop_group.id
            LEFT JOIN shops ON shops.id = shop_group_related.shop_id
            WHERE shops.status =1
            AND shop_group.status =1 AND goods.id = ". $goodId ." GROUP BY shops.id";
                    $shopStatus = $db->createCommand($sql)->queryScalar();//$db->all($sql);$db->one($sql);

                                        if (SEARCH_DEV_MODE
                                            && SEARCH_DEV_DEBUG_LEVEL > 3
                                        ) {
                                            echo '<b>Статус магазина:</b><br>';
                                            var_dump($sql);
                                            var_dump($shopStatus);
                                        }

                                        if ($goodStatus[0]['status'] == "1"
                                             && $shopStatus == "1"
                                        ) {

                                            if (!empty($goodId)) {
                                                //потом находим таки категории по товарам
                                                $sql
                                                    = "SELECT `category_id` FROM `category_links` "
                                                    . " WHERE `product_id`="
                                                    . $goodId
                                                    . " GROUP BY `category_id`";

                                                $catIdentifier = $db->createCommand($sql)->queryScalar();//$db->all($sql);$db->one($sql);
                                                if(!empty($catIdentifier)) {
                                                    $sql
                                                        = "SELECT `id` FROM `category` WHERE `id`="
                                                        . $catIdentifier
                                                        . " AND `active`=1";
                                                    $catId = $db->createCommand($sql)->queryScalar();//$db->all($sql);$db->one($sql);
                                                }
                                                if (!empty($catId)) {

                                                    $catStatCount[$catId]=+1;

                                                    $sql
                                                        = "SELECT `id`,`alias`,`parent_id`,`title` FROM `category`"
                                                        . " WHERE `id` = " . $catId;
                                                    $current = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);

                                                    if (intval($searchParam) == intval($catId))
                                                    {
                                                        $activeGoods[$goodId]['name']
                                                            = $goodStatus[0]['name'];
                                                        $activeGoods[$goodId]['full_name']
                                                            = $goodStatus[0]['full_name'];

                                                        if(!empty($activeGoodsStat[$goodId]))
                                                            $activeGoodsStat[$goodId]+= 5;
                                                        else
                                                            $activeGoodsStat[$goodId] = 5;
                                                    }

                                                    $activeCat[$catId]['id']
                                                        = $catId;
                                                    $activeCat[$catId]['parent']
                                                        = $current[0]['parent_id'];
                                                    $activeCat[$catId]['title']
                                                        = $current[0]['title'];
                                                    $activeCat[$catId]['alias']
                                                        = $current[0]['alias'];
                                                    $activeCatTags[$catId]['count']
                                                        = count(
                                                        $query
                                                    );

                                                    if(!empty($catStat[$catId]))
                                                        $catStat[$catId] = $catStat[$catId] + 1;
                                                    else
                                                        $catStat[$catId] = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $wordCounter++;
            }

            if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 1) {
                if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                    echo '<h3>Результат в responseANDOR</h3>';
                    var_dump($responseANDOR);
                }
            }

            if (SEARCH_STAT_MODE) {
                echo '<h3>Результат статистики Категорий после поиска в Тэгах</h3>';
                var_dump($activeCat);
                var_dump($catStat);
                echo '<h3>Результат статистики Товаров после поиска в Тэгах</h3>';
                var_dump($activeGoods);
                var_dump($activeGoodsStat);

            }

        ////////////////////////////////////////////////////////////////////////////////
        // RESULTS CALCULATING
        ////////////////////////////////////////////////////////////////////////////////
        //Получаем парента для категории первого-второго уровня
        //если это категория 0 уровня ничего не делаем
        //Считаем результаты сортируем массивы
              $j=0;

            foreach ($activeCat as $i => $item) {
                //получаем парентов для категорий, если есть
                if (!empty($item['parent'])) {
                    $sql = "SELECT `id`, `title`, `alias` FROM `category` WHERE `id`="
                        . $item['parent'];
                    $parentSQL = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);
                    $parentAlias = $parentSQL[0]['alias'];
                    $parentTitle = $parentSQL[0]['title'];
                    $activeCat[$i]['parent_alias'] = $parentAlias;
                    $activeCat[$i]['parent_title'] = $parentTitle;
                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                        echo '<h3>Item</h3>';
                        var_dump($item);
                        echo '<h3>SQL</h3>';
                        var_dump($sql);
                        echo '<h3>Результат ParentSQL</h3>';
                        var_dump($parentSQL);
                        echo '<h3>Результат ParentAlias</h3>';
                        var_dump($parentAlias);
                        echo '<h3>Результаты массив ParentAlias</h3>';
                        var_dump($activeCat[$i]['parent_alias']);
                    }
                }

            }
            //тоже самое делаем для тэгов
            foreach ($activeCatTags as $i => $item) {
                if (!empty($item['parent'])) {
                    $sql = "SELECT `id`, `title`, `alias` FROM `category` WHERE `id`="
                        . $item['parent'];
                    $parentSQL = $db->createCommand($sql)->queryAll();//$db->all($sql);$db->all($sql);
                    $parentAlias = $parentSQL[0]['alias'];
                    $parentTitle = $parentSQL[0]['title'];

                    $item['parent_alias'] = $parentAlias;
                    $item['parent_title'] = $parentTitle;
                    if (SEARCH_DEV_MODE && SEARCH_DEV_DEBUG_LEVEL > 2) {
                        var_dump($parentAlias);
                    }

                $arrTmp[$j]['parent'] = $item['parent'];
                $arrTmp[$j]['count'] = $item['count'];
                $arrTmp[$j]['title'] = $item['title'];
                $arrTmp[$j]['alias'] = $item['alias'];
                $arrTmp[$j]['parent_alias'] = $parentAlias;
                $arrTmp[$j]['parent_title'] = $parentTitle;
                $arrTmp[$j]['id'] = $i;

                }
            }

            foreach ($arrTmp as $i => $item) $sortedTags[] = $item;

            foreach ($sortedTags as $item) {
                if ($counter <= SEARCH_DEV_MAX_TAGS_RESULT - 1) {
                    $output[$item['id']] = $item;
                    $counter++;
                }
            }

            if (SEARCH_STAT_MODE) {
                echo '<h3>Результат статистики Категорий после поиска в Тэгах</h3>';
                var_dump($activeCat);
                var_dump($catStat);
                echo '<h3>Результат статистики Товаров после поиска в Тэгах</h3>';
                var_dump($activeGoods);
                var_dump($activeGoodsStat);

            }

        ////////////////////////////////////////////////////////////////////////////////
        // CONSUMING RESULTS
        ////////////////////////////////////////////////////////////////////////////////
        // Формируем представление массивов для вывода в окно просмотра
        // пользователю. Все передаем смарти

            //Отсортируем категории (по возрастанию)
            arsort($catStat);
            //Сортируем по статистике категории
            foreach ($catStat as $i => $item) {
                if ($item > SEARCH_CAT_MAX_SCORE) {
                    $sortedCats[$i] = $activeCat[$i];
                } else {
                    $sortedMaybeCats[$i] = $activeCat[$i];
                }
            }
            //Сортируем по статистике товары
            arsort($activeGoodsStat);
            foreach ($activeGoodsStat as $i => $item) {
                if(!empty($activeGoods[$i])){
                    if ($item >= SEARCH_GOOD_MAX_SCORE) {
                        //echo $i."<br>";
                        $sortedGoods[$i] =
                                $activeGoods[$i];
                        $sortedGoods[$i]['id'] = $i;
                    } else {
                        $sortedMaybeGoods[$i] = $activeGoods[$i];
                        $sortedMaybeGoods[$i]['id'] = $i;
                    }
                }
            }
            arsort($sortedGoods);
            arsort($sortedMaybeGoods);
        //
        //    var_dump("!!!!!!!!!!!!!");
        //    var_dump($activeGoodsStat);
        //    var_dump($activeGoods);
        //    var_dump($sortedGoods);
        //    var_dump("!!!!!!!!!!!!!");

        ////////////////////////////////////////////////////////////////////////////////
        // RESULTS OUTPUT
        ////////////////////////////////////////////////////////////////////////////////
        // Выдача результатов, все массивы сформированы

            //немного отладочной информации для финишного вида debug режима
            if (SEARCH_DEV_MODE) {
                echo '<h1>Результаты</h1>';
                var_dump($catStat);
                echo '<h2>Результаты сортировки Категорий</h2>';
                var_dump($sortedCats);
                echo '<h2>Результаты сортировки Товаров</h2>';
                var_dump($sortedGoods);
                echo '<h2>Возможные результаты Категорий</h2>';
                var_dump($sortedMaybeCats);
                echo '<h2>Возможные результаты Товаров</h2>';
                var_dump($sortedMaybeGoods);
            }

//var_dump($result);die();

            if (!empty($searchParam)) {

                //если есть товары
                if (!empty($sortedGoods)) {
                    foreach ($sortedGoods as $item) {
                        /*
                        $sql = "SELECT `goods_images`.`id` FROM `goods_images` WHERE `good_id`='".$item['id']."' ORDER BY `goods_images`.`position` ASC LIMIT 1";
                        $item['image'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                        $result['goods'][] = $item;
                        */
                        $item['image_id'] = $item['id'];
                        $result['goods'][] = $item;
                    }
                } else {
                    $search['error'] = 'Уточните условия поиска';
                }
                //если есть возможные товары
                if (!empty($sortedMaybeGoods)) {
                    foreach ($sortedMaybeGoods as $item) {
                        $sql = "SELECT `goods_images`.`id` FROM `goods_images` WHERE `good_id`='".$item['id']."' ORDER BY `goods_images`.`position` ASC LIMIT 1";
                        $item['image'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);

                        $result['maybegoods'][] = $item;
                    }
                }
            } else {
                //если категории
                if (!empty($sortedCats)) {

                    foreach ($sortedCats as $item) {
                        // Загрузка изображения для категория;
                        $sql = "SELECT `goods_images`.`good_id` FROM `goods_images`
                                 LEFT JOIN `category_links` ON `goods_images`.`good_id` = `category_links`.`product_id`
                                 WHERE `category_links`.`category_id` = '".$item['id']."' AND `goods_images`.`status` = '1' ORDER BY `goods_images`.`position` ASC LIMIT 1";
                        $item['image_id'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                        if(!empty($item['count']))$item['count'] = $catStatCount[$item['id']];


                        $result['category'][] = $item;
                    }
                } else {
                    $search['error'] = 'Уточните условия поиска';
                }
                //если есть возможные товары
                if (!empty($sortedMaybeCats)) {
                    foreach ($sortedMaybeCats as $item) {

                        // Загрузка изображения для категория;
                        $sql = "SELECT `goods_images`.`good_id` FROM `goods_images`
                                 LEFT JOIN `category_links` ON `goods_images`.`good_id` = `category_links`.`product_id`
                                 WHERE `category_links`.`category_id` = '".$item['id']."' AND `goods_images`.`status` = '1' ORDER BY `goods_images`.`position` ASC LIMIT 1";
                        $item['image_id'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);

                        $result['maybe'][] = $item;
                    }
                }
            }

            if(!empty($result))
	            return $this->render('result', [
    	            'result' => $result,
        	        'search' =>  $search['text'],
            	    ]);
			else
	          return $this->render('result', [
        	        'error' => 'Уточните условия поиска',
    	            ]);
		
        }
        else
        {
          return $this->render('result', [
                'error' => 'Уточните условия поиска',
                ]);
        }

    }
}

