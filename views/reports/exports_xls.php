<?php
//use Yii;
//print_r($model);die();
//use PHPExcel;

    // Создание файла;
    $PHPExcel = new PHPExcel();
    $PHPExcel->setActiveSheetIndex(0);
    $PHPExcel->getActiveSheet()->setTitle('Страница 1');

    if ($_SESSION['exports']['type'] == 'orders') {
        // Вывод данных;
        $PHPExcel->getActiveSheet()->setCellValue('A1', 'Дата заказа');
        $PHPExcel->getActiveSheet()->setCellValue('B1', 'Номер заказа');
        $PHPExcel->getActiveSheet()->setCellValue('C1', 'Поставщик');
        $PHPExcel->getActiveSheet()->setCellValue('D1', 'Товар');
        $PHPExcel->getActiveSheet()->setCellValue('E1', 'Опции');
        $PHPExcel->getActiveSheet()->setCellValue('F1', 'Цена входная');
        $PHPExcel->getActiveSheet()->setCellValue('G1', 'Наценка');
        $PHPExcel->getActiveSheet()->setCellValue('H1', 'Цена выходная');
        $PHPExcel->getActiveSheet()->setCellValue('I1', 'Скидка');
        $PHPExcel->getActiveSheet()->setCellValue('J1', 'Бонусы');
        $PHPExcel->getActiveSheet()->setCellValue('K1', 'Количество');
        $PHPExcel->getActiveSheet()->setCellValue('L1', 'Сумма');
        $PHPExcel->getActiveSheet()->setCellValue('M1', 'Агентские');
        $PHPExcel->getActiveSheet()->setCellValue('N1', 'Итого');
        $PHPExcel->getActiveSheet()->setCellValue('O1', 'Место доставки');
        $PHPExcel->getActiveSheet()->setCellValue('P1', 'Дата доставки');
        $PHPExcel->getActiveSheet()->setCellValue('Q1', 'Стоимость доставки');
        $PHPExcel->getActiveSheet()->setCellValue('R1', 'Client');
        $PHPExcel->getActiveSheet()->setCellValue('S1', 'tel');
        // Обработка данных;
        foreach ($model['data'] as $i=>$item) {
            // Вывод данных;
            $PHPExcel->getActiveSheet()->setCellValue('A'.($i + 2), $item['date']);
            $PHPExcel->getActiveSheet()->setCellValue('B'.($i + 2), $item['order_id']);
            $PHPExcel->getActiveSheet()->setCellValue('C'.($i + 2), $item['shop_name']);
            $PHPExcel->getActiveSheet()->setCellValue('D'.($i + 2), $item['good_name']);
            $PHPExcel->getActiveSheet()->setCellValue('E'.($i + 2), $item['tags']);
            $PHPExcel->getActiveSheet()->setCellValue('F'.($i + 2), $item['price_in']);
            $PHPExcel->getActiveSheet()->setCellValue('G'.($i + 2), $item['comission']);
            $PHPExcel->getActiveSheet()->setCellValue('H'.($i + 2), $item['price_out']);
            $PHPExcel->getActiveSheet()->setCellValue('I'.($i + 2), $item['discount']);
            $PHPExcel->getActiveSheet()->setCellValue('J'.($i + 2), $item['bonus']);
            $PHPExcel->getActiveSheet()->setCellValue('K'.($i + 2), $item['count']);
            $PHPExcel->getActiveSheet()->setCellValue('L'.($i + 2), $item['money']);
            $PHPExcel->getActiveSheet()->setCellValue('M'.($i + 2), $item['fee']);
            $PHPExcel->getActiveSheet()->setCellValue('N'.($i + 2), $item['sum']);
            $PHPExcel->getActiveSheet()->setCellValue('O'.($i + 2), $item['delivery_name']);
            $PHPExcel->getActiveSheet()->setCellValue('P'.($i + 2), $item['delivery_date']);
            $PHPExcel->getActiveSheet()->setCellValue('Q'.($i + 2), $item['delivery_price']);
            $PHPExcel->getActiveSheet()->setCellValue('R'.($i + 2), $item['name']);
            $PHPExcel->getActiveSheet()->setCellValue('S'.($i + 2), $item['phone']);
        }

    }

    $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');

    // Настройка заголовков;
    /*header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="exports.xls"');
    header('Cache-Control: max-age=0');*/

    // Выдача файла;
    $objWriter->save('php://output');
    //Yii::$app->response->sendContentAsFile($objWriter->save('php://output'), 'export_xml_'.Date('Y.m.d-H-i-s',time()).'.xls');


?>