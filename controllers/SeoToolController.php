<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\managment\models\ShopYml;
use app\modules\my\models\Feedback;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;


class SeoToolController extends BackendController
{
    public $defaultAction = 'seo-tool';
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'seo-tool',
                            'banners',
                            'index-xml',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'conflictManager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
        ];
    }
    // Автолинк добавления Тэги;
    public function actionSeoTool()
    {
        // Загрузка Тэги asArray;
        $db = Yii::$app->getDb();
        $sql = "SELECT * FROM `auto_links` ORDER BY `id` DESC ";
        $meta = $db->createCommand($sql)->queryAll();

        // Добавить мета теги;
        if(isset($_POST['add'])) {
            // Проверка полей;
            if (!$_POST['meta'] or !$_POST['url']) die('Не все поля заполнены!');
            // Сохранение данных;
            $sql = "INSERT INTO `auto_links`(`meta`, `url`,`status`) VALUES ('".$_POST['meta']."', '".$_POST['url']."', '".$_POST['status']."')";
            if ($db->createCommand($sql)->execute()) {
                die();
            }
        }

        // Редактировать мета теги;
        if(isset($_POST['edit'])) {
            // Проверка полей;
            if (!$_POST['meta'] or !$_POST['url']) die('Не все поля заполнены!');
            $id = intval($_POST['id']);
            // Сохранение данных;
            $sql = "UPDATE `auto_links` SET `meta` = '" . $_POST['meta'] . "', `url` = '" . $_POST['url'] . "',`status` = '" . $_POST['status'] . "' WHERE `id` = '" . $id . "' ";
            if ($db->createCommand($sql)->execute()){
                die();
            }
        }

        // Выбрать мета теги;
        if(isset($_POST['form'])) {
            $id = intval($_POST['id']);
            $sql = "SELECT * FROM `auto_links` WHERE `id`='".$id."' LIMIT 1";
            $meta_edit = $db->createCommand($sql)->queryOne();
        }

         // Удалить мета теги;
        if(isset($_POST['delete'])) {
            // Проверка полей;
            $id = intval($_POST['id']);
            // Сохранение данных;
            $sql = "DELETE FROM `auto_links` WHERE `id` = '".$id."'";
            if ($db->createCommand($sql)->execute()) {
                //$this->redirect('/seo-tool/');
                header('location: /seo-tool/');
                die();
            }
        }
        // Передаем на шаблон;
        return $this->render('seo-tool',
            [
                'meta'=> $meta,
                'meta_edit' => $meta_edit = isset($meta_edit) ? $meta_edit : '',
            ]);
    }
    // Загрузка файлов;
    public static function upload($file, $filename)
    {

        if(!empty($filename)) {
            $fileinfo = pathinfo(strtolower($file['name']));
            if (isset($fileinfo['extension']) && isset($fileinfo) && in_array($fileinfo['extension'], array('jpg', 'jpeg'))) {
                // Загрузка файла на сервер;
                if (move_uploaded_file($file['tmp_name'], $filename)) {
                    // обработка баннер 1110,395;
                    Feedback::loadPhoto($filename,870,350);
                }
                    return true;
            }
        }
        return false;
    }
    // Удаление файлов;
    public static function delete_img($dir, $files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($dir.$file)) unlink($dir.$file);
            }
        } else {
            if (file_exists($dir.$files)) unlink($dir.$files);
        }
    }
    // Тип файла;
   public static function file_type($file, $binary = false)
   {
       IF(!empty($file)) {
           if (!$binary) $file = file_get_contents($file);
           $bytes = bin2hex($file[0] . $file[1]);
           $types = array(
               'ffd8' => 'jpg',
               '8950' => 'png',
               '4749' => 'gif',
               '4357' => 'swf'
           );
           return (isset($types[$bytes]) ? $types[$bytes] : '');
       }
       return false;
    }
    // Список банееров;
    public function actionBanners()
    {
        $db = Yii::$app->getDb();
        /*----------Функция Обработка файл------------*/

        // Переменные модуля;
        $filePosters = $_SERVER['DOCUMENT_ROOT'].'/files/posters/';
        $fileSlides = $_SERVER['DOCUMENT_ROOT'].'/files/slides/';


        //Загрузка тип баннеры;
       // $type = array(1 =>'Справо (верх маленький 1)',2 =>'Слева (верх большой)',3 =>'Слайдер',4 => 'Справо (вниз маленький 1)',5 =>'Слева (вниз большой)',6 =>'Справо (вниз маленький 2)',7 =>'Справо (верх маленький 2)');
        $type = array(3 =>'Слайдер');

        //Если пост пустой то по умл выводим список;
        $view = isset($_POST['action']) ? $_POST['action'] : '';

        // Вывести список товар;
        switch ($view) {
            // Добавить баннер;
            case 'add':
                //
                break;
            // Редактировать баннер;
            case 'edit':
                 $id =  intval($_POST['id']);
                // Загрузка список;
                $sql = "SELECT * FROM `banners` WHERE `id`='".$id."' LIMIT 1 ";
                $banner = $db->createCommand($sql)->queryOne();
                break;

            // Загрузка список;
            default:
                // Загрузка список;
                $sql = "SELECT * FROM `banners` WHERE `type` = '3' AND (`status`='1' OR `status` = '0') ORDER BY `type` ";
                if($banner_list = $db->createCommand($sql)->queryAll()){
                    foreach($banner_list as $key=>$value) {
                        $banner_list[$key]['type_name'] = $type[$value['type']];
                    }
                }
        }
        // Проверка флага;
        if (isset($_SESSION['message'])) {
            $message =  $_SESSION['message'];
            unset($_SESSION['message']);
        }
        // Добавить баннер;
        if(isset($_POST['add'])) {
            // Проверка полей;
            if (!$_POST['name']) $error['name'] = 'Введите название';
            if(!$_POST['url']) $error['url'] = 'Введите урл адресс';
            if(!$_FILES['banner']) $error['banner'] = 'Загрузите баннер';
            if (!empty($error) and $error) {
                //
            } else {
                // Отправка данных;
                if ($_POST['add']) {
                    $name = Html::encode($_POST['name']);
                    $url = Html::encode($_POST['url']);
                    $type = intval($_POST['type']);
                    $position = intval($_POST['position']);
                    $status = intval($_POST['status']);
                    // Сохранение данных;
                    $sql = "INSERT INTO `banners`(`name`, `url`, `type`,`position`,`text`,`date`,`status`) VALUES ('".$name."','".$url."',".$type.",'".$position."','', NOW(),'".$status."')";
                    if ($db->createCommand($sql)->execute()) {
                        $id = Yii::$app->db->getLastInsertID();
                        // Загрузка файл;
                        $fileImg =  ($_POST['type'] == 3) ? $fileSlides : $filePosters;
                        if($filename = $fileImg.$id.'.'.$this->file_type($_FILES['banner']['tmp_name'])) {
                            $this->upload($_FILES['banner'], $filename);
                        }
                        // Установка флага о сохранении данных;
                        $_SESSION['message'] = 'Успешно добавлено';
                        // Перезагрузка страницы;
                        header('location: /seo-tool/banners');
                        die();
                    } else {
                        //
                    }
                }
            }
        }
        // Редактировать  баннер;
        if (isset($_POST['edit'])) {
            $id = intval($_POST['id']);
            // Загрузка список;
            $sql = "SELECT * FROM `banners` WHERE `id`='".$id."' LIMIT 1 ";
            $banner = $db->createCommand($sql)->queryOne();
            // Проверка полей;
            if (!$_POST['name']) $error_ed['name'] = 'Введите название';
            if (!$_POST['url']) $error_ed['url'] = 'Введите урл адресс';
            if (!$_FILES['banner']) $error_ed['banner'] = 'Загрузите баннер';
            if (!empty($error_ed) and $error_ed) {
              //
            } else {
                // Отправка данных;
                if ($_POST['edit']) {
                    // Сохранение данных;
                    $sql = "UPDATE `banners` SET `name` = '" . $_POST['name'] . "', `url` = '" . $_POST['url'] . "', `type` = '" . $_POST['type'] . "', `position` = '" . $_POST['position'] . "', `date` = NOW(), `status` = '" . $_POST['status'] . "' WHERE `id` = '" . $id . "' ";
                    if ($db->createCommand($sql)->execute()) {
                        $fileImg =  ($_POST['type'] == 3) ? $fileSlides : $filePosters;
                        // Загрузка файл;
                        if ($filename = $fileImg . $id . '.' . $this->file_type($_FILES['banner']['tmp_name'])) {
                            $this->upload($_FILES['banner'], $filename);
                        }
                        // Установка флага о сохранении данных;
                        $_SESSION['message'] = 'Успешно сохранено';
                         // Перезагрузка страницы;
                         header('location: /seo-tool/banners');
                        die();
                    } else {
                       //
                    }
                }
            }
        }

        // Удалить из таблицы;
        if(isset($_POST['delete'])) {
            $fileImg =  (isset($_POST['type']) and $_POST['type'] == 3) ? $fileSlides : $filePosters;
            $id = intval($_POST['id']);
            // Сохранение данных;
            $sql = "UPDATE `banners` SET `status` = '-1' WHERE `id` = '" . $id . "' ";
            if($db->createCommand($sql)->execute()) {
               // $this->delete_img($fileImg, $_POST['id'] . '.jpg');
                die();
            }
        }
        // Загрузка в шаблон;
        return $this->render('banners',[
            'banner_list'=>(isset($banner_list) ? $banner_list : ''),
            'banner'=>(isset($banner) ? $banner : ''),
            'type_array'=> $type,
            'message'=>(isset($message) ? $message : ''),
            'error' => (isset($error) ? $error : ''),
            'error_ed' => (isset($error_ed) ? $error_ed : ''),
        ]);
    }
    // Выгрузка YML и XML формат;
    public function actionIndexXml()
    {
        // Путь к файлу;
        $fileYml = $_SERVER['DOCUMENT_ROOT'].'/files/xml/'.time().'__'.date('Y_m_d').'_yml.xml';
        $fileXml = $_SERVER['DOCUMENT_ROOT'].'/files/xml/'.time().'__'.date('Y_m_d').'_xml.xml';
        $dirName = $_SERVER['DOCUMENT_ROOT'].'/files/xml/';

        // Считываем с каталога;
        $entries = scandir($dirName);
        $filelist = array();
        foreach($entries as $entry) {
            if (strpos($entry, ".xml")) {
                $filelist[] = $entry;
            }
        }
        arsort($filelist);

        $yml = new ShopYml();

        //$yml->getGenerateXML($fileYml,$yml->getBodyXml());

        // Генерация для YML формат;
        if(Yii::$app->request->post('YML')) {

            $yml->getGenerateXML($fileYml,$yml->getBodyYml());
            Yii::$app->session->setFlash('successFiles');
            //успешно сгенирирован;
            return $this->refresh();
            //return $this->redirect('/seo-tool/index-xml');
        }

        // Генерация для XML формат;
        if(Yii::$app->request->post('XML')) {
            $yml->getGenerateXML($fileXml,$yml->getBodyXml());
            Yii::$app->session->setFlash('successFiles');
            //успешно сгенирирован;
            return $this->refresh();
        }

        // Удалить файл;
        if(Yii::$app->request->post('delete')) {
            self::delete_img($dirName,Yii::$app->request->post('delete'));
            return $this->redirect('/seo-tool/index-xml');
        }

        return $this->render('index-xml',[
            'filelist' => $filelist,
        ]);
    }

}