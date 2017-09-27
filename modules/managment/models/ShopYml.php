<?php
/*
 * Version - 2.0.0 v
 * Xml/Yml Генератор;
 * */

namespace app\modules\managment\models;

use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Goods;
use app\modules\common\models\ModFunctions;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use Yii;

class ShopYml
{
    // Настройки XML Документ;
    const NAME_YML = 'Catalog products'; // Название парсера;
    const COMPANY_YML = 'Esalad';
    const URL_YML = 'http://www.esalad.ru';
    const CURRENCY_YML = 'RUR'; // Валюта по умл RUR;
    const SALES_NOTES = 'Необходима предоплата.';
    const STR_COUNT = 250; // Обрезание описание товара;

    // Настройка Google Merchant
    const DESCRIPTION_YML = ''; // Описание в header;
    const CURRENCY_XML = 'RUB'; // Валюта по умл RUB;
    const AVAILABILITY = 'in stock'; // Статус товара по умол. есть в наличие;
    const CONDITION = 'new'; // Тип товара НОВОЕ, Б/У, БРАК;

    // Структура Yml;
    public function getBodyYml()
    {
       // header ("Content-Type:text/xml");
        $yml = '<?xml version="1.0" encoding="utf-8"?>';
        $yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
        $yml .= '<yml_catalog date="'.date('Y-m-d H:i').'">';
           $yml .= '<shop>';
               $yml .= '<name>'.self::NAME_YML.'</name>';
               $yml .= '<company>'.self::COMPANY_YML.'</company>';
               $yml .= '<url>'.self::URL_YML.'</url>';
               $yml .= '<currencies><currency id="'.self::CURRENCY_YML.'" rate="1" plus="0"/></currencies>';
               // Загрузка категории;
              $category = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
               // Категории;
               $yml .= '<categories>';
                      foreach($category as $key => $item) {
                          if(empty($item['parent_id'])) {
                              $yml .= '<category id="' . $item['id'] . '">' . $item['title'] . '</category>';
                          }else{
                              $yml .= '<category id="' . $item['id'] . '" parentId="' . $item['parent_id'] . '">' . $item['title'] . '</category>';
                          }
                      }
               $yml .= '</categories>';

               // Загрузка товаров;
               $yml .= '<offers>';
                     $model = $this->findModelAll();
                     foreach($model as $key=> $cat) {
                         foreach($cat->allProductsClear as $k => $product) {
                             if(!empty($product->id)) {
                                 $yml .= '<offer id="' . $product->id . '">';
                                 $yml .= '<url>' . self::URL_YML . Url::toRoute('/'.$product->category->catalogPath.$product->id). '</url>';
                                 $yml .= '<price>' . $product->getPriceVariant() . '</price>';
                                 $yml .= '<currencyId>' . self::CURRENCY_YML . '</currencyId>';
                                 $yml .= '<categoryId>' . $product->category->id . '</categoryId>';
                                 $yml .= '<picture>' . self::URL_YML . Goods::findProductImage($product->id, 'min') . '</picture>';
                                 $yml .= '<name>' . self::getTextFromHTML($product->name) . '</name>';
                                 //  $yml .= '<vendor>Михайленко, ИП</vendor>';
                                 //$yml .= '<param name="Цвет">сине-белый, черный</param>';
                                 //$yml .= '<param name="Размер">40-42, 42-44</param>';
                                 $yml .= '<description>'.StringHelper::truncate(self::getTextFromHTML($product->description), self::STR_COUNT, '...').'</description>';
                                 $yml .= '<sales_notes>'.self::SALES_NOTES.'</sales_notes>';
                                 $yml .= '</offer>';
                             }
                         }
                     }
               $yml .= '</offers>';
           $yml .= '</shop>';
        $yml .= '</yml_catalog>';
       // echo($yml);
       // die();
        return $yml;
    }

    // Структура Xml Google Merchant;
    public function getBodyXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
            $xml .= '<channel>';
                $xml .= '<title>'.self::NAME_YML.'</title>';
                $xml .= '<link>'.self::URL_YML.'</link>';
                $xml .= '<description>'.self::DESCRIPTION_YML.'</description>';
                // Загрузка товаров;
                $model = $this->findModelAll();
                foreach($model as $key=> $cat) {
                    foreach ($cat->allProductsClear as $k => $product) {
                        if(!empty($product->id)) {
                             $xml .= '<item>';
                                 $xml .= '<link>'. self::URL_YML . Url::toRoute('/'.$product->category->catalogPath.$product->id). '?utm_medium=GoogleMerchant&amp;utm_source=GoogleMerchant&amp;utm_campaign='.self::COMPANY_YML.'&amp;utm_term='. self::getTextFromHTML($product->name) .'</link>';
                                 $xml .= '<g:id>'.$product->id.'</g:id>';
                                 $xml .= '<g:price>'.$product->getPriceVariant().' ' . self::CURRENCY_XML . '</g:price>';
                                 $xml .= '<g:condition>' . self::CONDITION . '</g:condition>';
                                 $xml .= '<g:availability>' . self::AVAILABILITY . '</g:availability>';
                                 $xml .= '<g:adwords_grouping>'.$product->category->title.'</g:adwords_grouping>';
                                 $xml .= '<g:image_link>'. self::URL_YML . Goods::findProductImage($product->id, 'min') .'</g:image_link>';
                                 $xml .= ' <title>'. self::getTextFromHTML($product->name) .'</title>';
                                 $xml .= '<description>'.StringHelper::truncate(self::getTextFromHTML($product->description), self::STR_COUNT, '...').'</description>';
                             $xml .= '</item>';
                        }
                    }
                }
            $xml .= '</channel>';
        $xml .= '</rss>';
         //echo($xml);
        // die();
        return $xml;
    }

    // Генерация yml или XML
    public function getGenerateXML($path, $type){
          $fp = fopen($path, "w+");
          fwrite($fp, '');
          fwrite($fp, $type);
          fclose($fp);
    }

    // Фильтр очитска тэги;
    public function getTextFromHTML($htmlText) {
        $search = array("\r\n", "\r", "\n", "\t","&","#39;","");
        $html_clear = str_replace($search, " ", $htmlText);
        $html_clear = strip_tags($html_clear);
        $html_clear = html_entity_decode($html_clear);
      //  $html_clear = str_replace('<', " ", $html_clear);
        // Чистка пробелы;
        //$htmlText = preg_replace('/\s\s+/', ' ', $htmlText);
        return $html_clear;
    }

    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelAll()
    {
        if ($model = Category::find()->where(['active' => 1])->orderBy('level, sort')->all()){
            return $model;
        } else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}