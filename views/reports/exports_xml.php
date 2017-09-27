<?php
//print_r($model);
//print_r($_SESSION);die();
if (!empty($model)) {

    // Создание документа;
    $xml = new DOMDocument('1.0', 'utf-8');
    // Создание основного узла;
    $exports = $xml->createElement('exports');
    // Создание списка документов;
    $documents = $xml->createElement('documents');
    // Обработка данных;
    foreach ($model['data'] as $key=>$value) {
        // Создание документа;
        $document = $xml->createElement('document');
        // Создание узла для поля;
        $type = $xml->createElement('type');
        $type->appendChild($xml->createTextNode($value['type']));
        $document->appendChild($type);
        // Создание узла для поля;
        $shop_code = $xml->createElement('code');
        $shop_code->appendChild($xml->createTextNode($value['code']));
        $document->appendChild($shop_code);
        // Создание узла для поля;
        $shop_name = $xml->createElement('name');
        $shop_name->appendChild($xml->createTextNode($value['name']));
        $document->appendChild($shop_name);
        // Создание списка продаж;
        $items = $xml->createElement('items');
        // Обработка данных;
        foreach ($value['items'] as $k=>$v) {
            // Создание продажи;
            $item = $xml->createElement('item');
            // Создание узла для поля;
            $code = $xml->createElement('good_code');
            $code->appendChild($xml->createTextNode($v['good_code']));
            $item->appendChild($code);
            // Создание узла для поля;
            $name = $xml->createElement('good_name');
            $name->appendChild($xml->createTextNode($v['good_name']));
            $item->appendChild($name);
            // Создание узла для поля;
            $price_in = $xml->createElement('price_in');
            $price_in->appendChild($xml->createTextNode($v['price_in']));
            $item->appendChild($price_in);
            // Создание узла для поля;
            $price_out = $xml->createElement('price_out');
            $price_out->appendChild($xml->createTextNode($v['price_out']));
            $item->appendChild($price_out);
            // Создание узла для поля;
            $count = $xml->createElement('count');
            $count->appendChild($xml->createTextNode($v['count']));
            $item->appendChild($count);
            // Создание узла для поля;
            $discount = $xml->createElement('discount');
            $discount->appendChild($xml->createTextNode($v['discount']));
            $item->appendChild($discount);
            // Создание узла для поля;
            $comission = $xml->createElement('comission');
            $comission->appendChild($xml->createTextNode($v['comission']));
            $item->appendChild($comission);
            // Добавление узла;
            $items->appendChild($item);
        }
        // Добавление узла;
        $items->appendChild($item);
        // Добавление узла;
        $document->appendChild($items);
        // Добавление узла;
        $documents->appendChild($document);
    }




    // Добавление узлов в список;
    $exports->appendChild($documents);
    // Добавление узлов в документ;
    $xml->appendChild($exports);

    // Обработка комментария;
    if (isset($model['comments'])) {
        // Создание узла для комментария;
        $comments = $xml->createElement('comments');
        $comments->appendChild($xml->createTextNode($model['comments']));
        $exports->appendChild($comments);
    }
    // Выдача файла;
    echo $xml->saveXML();

    //Yii::$app->response->content = $xml->saveXML();

    //Yii::$app->response->sendContentAsFile($xml->saveXML(), 'export_xml_'.Date('Y.m.d-H-i-s',time()).'.xml')->send();
}

?>