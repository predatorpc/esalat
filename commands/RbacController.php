<?php

/*
*
*  Инициализация модуля RBAC для extremeshop.ru
*  predator_pc@11/04/2016
*
*
*/

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

/*
*
*  Инициализация режима GodMode
*
*
*/

    public function actionGodmode()
    {
        $auth = Yii::$app->authManager;

        $GodMode = $auth->createRole('GodMode');
        $auth->add($GodMode);
		$GodModePermission = $auth->createPermission('GodModePermissions');
        $auth->add($GodModePermission);
        $auth->addChild($GodMode, $GodModePermission);

		return true;
	}

//ВСЕ ВОЗМОЖНЫЕ ДЕЙСТВИЯ МАГАЗИНА

//Пермиссии ЗАКАЗА

    public function actionInit()
    {
        $auth = Yii::$app->authManager;

//ВСЕ ВОЗМОЖНЫЕ ДЕЙСТВИЯ МАГАЗИНА

//Пермиссии ЗАКАЗА
        $createOrder = $auth->createPermission('createOrder');
        $createOrder->description = 'Создать заказ';
        $auth->add($createOrder);

        $viewOrder = $auth->createPermission('viewOrder');
        $viewOrder->description = 'Просмотр заказа';
        $auth->add($viewOrder);

        $updateOrder = $auth->createPermission('updateOrder');
        $updateOrder->description = 'Изменить заказ';
        $auth->add($updateOrder);

        $cancelOrder = $auth->createPermission('cancelOrder');
        $cancelOrder->description = 'Отменить заказ';
        $auth->add($cancelOrder);

        $statusOrder = $auth->createPermission('statusOrder');
        $statusOrder->description = 'Изменение статуса заказа';
        $auth->add($statusOrder);

        $takeOrder = $auth->createPermission('takeOrder');
        $takeOrder->description = 'Принять заказ';
        $auth->add($takeOrder);

//Пермиссии МАГАЗИНА

        $createShop = $auth->createPermission('createShop');
        $createShop->description = 'Создать магазин';
        $auth->add($createShop);

        $updateShop = $auth->createPermission('updateShop');
        $updateShop->description = 'Изменить магазин';
        $auth->add($updateShop);

        $cancelShop = $auth->createPermission('cancelShop');
        $cancelShop->description = 'Сделать не активным магазин';
        $auth->add($cancelShop);

        $updateShopWarehouse = $auth->createPermission('updateShopWarehouse');
        $updateShopWarehouse->description = 'Редактировать склад';
        $auth->add($updateShopWarehouse);

        $updateShopInfo = $auth->createPermission('updateShopInfo');
        $updateShopInfo->description = 'Редактирование информации о магазине';
        $auth->add($updateShopInfo);

        $approveShopManager = $auth->createPermission('approveShopManager');
        $approveShopManager->description = 'Добавить менеджера магазина';
        $auth->add($approveShopManager);

//Пермиссии ДОСТАВКИ

        $viewDelivery = $auth->createPermission('viewDelivery');
        $viewDelivery->description = 'Просмотр доставки';
        $auth->add($viewDelivery);

        $updateDelivery = $auth->createPermission('updateDelivery');
        $updateDelivery->description = 'Редактирование доставки';
        $auth->add($updateDelivery);

        $cancelDelivery = $auth->createPermission('cancelDelivery');
        $cancelDelivery->description = 'Отмена доставки';
        $auth->add($cancelDelivery);

        $updateDeliveryCost = $auth->createPermission('cancelDeliveryCost');
        $updateDeliveryCost->description = 'Изменение стоимости доставки';
        $auth->add($updateDeliveryCost);

//Пермиссии ТОВАРА

        $createGood = $auth->createPermission('createGood');
        $createGood->description = 'Создать товар';
        $auth->add($createGood);

        $updateGood = $auth->createPermission('updateGood');
        $updateGood->description = 'Изменить товар';
        $auth->add($updateGood);

        $cancelGood = $auth->createPermission('cancelGood');
        $cancelGood->description = 'Удалить товар';
        $auth->add($cancelGood);

        $linkupGood = $auth->createPermission('linkupGood');
        $linkupGood->description = 'Привязать товар к категории';
        $auth->add($linkupGood);
		
        $commissionGood = $auth->createPermission('commissionGood');
        $commissionGood->description = 'Установить процент комиссии';
        $auth->add($commissionGood);

        $approveGood = $auth->createPermission('approveGood');
        $approveGood->description = 'Одобрить товар для продажи';
        $auth->add($approveGood);
		
//Пермиссии ОТЧЕТОВ

        $viewReports = $auth->createPermission('viewReports');
        $viewReports->description = 'Просмотр отчетов';
        $auth->add($viewReports);

        $updateReports = $auth->createPermission('updateReports');
        $updateReports->description = 'Редактирование отчетов';
        $auth->add($updateReports);

//Пермиссии БИЛЛИНГА

        $viewBilling = $auth->createPermission('viewBilling');
        $viewBilling->description = 'Просмотр отчетов';
        $auth->add($viewBilling);

        $updateBilling = $auth->createPermission('updateBilling');
        $updateBilling->description = 'Редактирование отчетов';
        $auth->add($updateBilling);

	
//ВСЕ ВОЗМОЖНЫЕ РОЛИ И ПРИВЯЗКИ ДЕЙСТВИЙ

        $user = $auth->createRole('user');
        $auth->add($user);
        $auth->addChild($user, $createOrder);

        $shopManager = $auth->createRole('shopManager');
        $auth->add($shopManager);
        $auth->addChild($shopManager, $takeOrder);
        $auth->addChild($shopManager, $statusOrder);
        $auth->addChild($shopManager, $createGood);
        $auth->addChild($shopManager, $updateGood);
        $auth->addChild($shopManager, $cancelGood);

        $shopOwner = $auth->createRole('shopOwner');
        $auth->add($shopOwner);
        $auth->addChild($shopOwner, $takeOrder);
        $auth->addChild($shopOwner, $updateOrder);
        $auth->addChild($shopOwner, $createGood);
        $auth->addChild($shopOwner, $updateGood);
        $auth->addChild($shopOwner, $cancelGood);
        $auth->addChild($shopOwner, $updateShopInfo);
        $auth->addChild($shopOwner, $updateShopWarehouse);

        $categoryManager = $auth->createRole('categoryManager');
        $auth->add($categoryManager);
        $auth->addChild($categoryManager, $createGood);
        $auth->addChild($categoryManager, $updateGood);
        $auth->addChild($categoryManager, $cancelGood);
        $auth->addChild($categoryManager, $commissionGood);
        $auth->addChild($categoryManager, $approveGood);
        $auth->addChild($categoryManager, $updateShopInfo);
        $auth->addChild($categoryManager, $updateShopWarehouse);
        $auth->addChild($categoryManager, $createShop);
        $auth->addChild($categoryManager, $cancelShop);
        $auth->addChild($categoryManager, $viewReports);

        $conflictManager = $auth->createRole('conflictManager');
        $auth->add($conflictManager);
        $auth->addChild($conflictManager, $viewReports);
        $auth->addChild($conflictManager, $updateReports);
        $auth->addChild($conflictManager, $viewOrder);
        $auth->addChild($conflictManager, $cancelOrder);
        $auth->addChild($conflictManager, $updateOrder);
        $auth->addChild($conflictManager, $statusOrder);
        $auth->addChild($conflictManager, $viewBilling);
        $auth->addChild($conflictManager, $updateBilling);
        $auth->addChild($conflictManager, $viewDelivery);
        $auth->addChild($conflictManager, $updateDelivery);

        $helperManager = $auth->createRole('helperManager');
        $auth->add($helperManager);
        $auth->addChild($helperManager, $viewDelivery);
        $auth->addChild($helperManager, $updateDelivery);
        $auth->addChild($helperManager, $updateDeliveryCost);
        $auth->addChild($helperManager, $viewOrder);

        $callcenterOperator = $auth->createRole('callcenterOperator');
        $auth->add($callcenterOperator);
        $auth->addChild($callcenterOperator, $viewDelivery);
        $auth->addChild($callcenterOperator, $updateDelivery);
        $auth->addChild($callcenterOperator, $viewOrder);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
//        $auth->assign($author, 2);
  //      $auth->assign($admin, 1);
    }
}