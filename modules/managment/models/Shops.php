<?php

namespace app\modules\managment\models;

use app\modules\catalog\models\Goods;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;
use app\modules\common\models\UserShop;
use Yii;
use yii\web\HttpException;

/**
 * This is the model class for table "shops".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $name
 * @property string $name_full
 * @property string $contract
 * @property string $tax_number
 * @property string $description
 * @property string $phone
 * @property string $min_order
 * @property string $delivery_delay
 * @property integer $delay
 * @property integer $comission_id
 * @property string $comission_value
 * @property integer $count
 * @property integer $show
 * @property integer $notice
 * @property string $registration
 * @property integer $status
 *
 * @property UsersRoles[] $usersRoles
 */
class Shops extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'delay', 'comission_id', 'count', 'show', 'notice', 'status','edit_count_good'], 'integer'],
//            [['description'], 'required'],
            [['name', 'name_full', 'contract', 'comission_id', 'comission_value', 'phone'], 'required'],
            [['description'], 'string'],
            [['min_order', 'comission_value'], 'number'],
            [['delivery_delay', 'registration'], 'safe'],
            [['name', 'phone'], 'string', 'max' => 64],
            [['name_full', 'contact'], 'string', 'max' => 128],
            [['contract', 'tax_number'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'type_id'         => 'Тип ID',
            'name'            => 'Название',
            'name_full'       => 'Полное название',
            'contract'        => 'Договор',
            'contact'         => 'Контактное лицо',
            'tax_number'      => 'Tax Number',
            'description'     => 'Описание',
            'phone'           => 'Телефон',
            'min_order'       => 'Минимальный заказ',
            'delivery_delay'  => 'Задержка доставки',
            'delay'           => 'Задержка',
            'comission_id'    => 'ID Комиссии',
            'comission_value' => 'Процент комиссии',
            'count'           => 'Кол-во',
            'show'            => 'Показ товаров на сайте',
            'notice'          => 'Notice',
            'registration'    => 'Регистрация',
            'status'          => 'Активный',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(ShopsStores::className(), ['shop_id' => 'id'])->andWhere(['status' => 1]);
    }

    public function getUsersRoles()
    {
        return $this->hasMany(UserRoles::className(), ['shop_id' => 'id']);
    }

    public function getUsers()
    {
        return $this->hasOne(UserShop::className(), ['id' => 'user_id']);
    }

    public function getManagers()
    {
        return User::find()->leftJoin('users_roles', 'users_roles.user_id = users.id')->where(['users_roles.shop_id' => $this->id])->all();
    }

    public function disableShopProducts($check)
    {
        $products = Goods::find()
            ->leftJoin('shop_group_variant_link', 'shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group_related', 'shop_group_related.shop_group_id = shop_group_variant_link.shop_group_id')
            ->where([
                'shop_group_related.shop_id' => $this->id,
                'goods.status'               => 1,
            ])
            ->all();

        if (!$products) {
            echo 'Нет продуктов для данного магазина';
            die();
        } else {
            foreach ($products as $product) {
                if ($product->show == 0 && $check == 1) {
                    $product->show = 1;
                    $product->save();
                }
                if ($product->show == 1 && $check == 0) {
                    $product->show = 0;
                    $product->save();
                }
            }
        }
    }

    public static function getNotRelatedShops()
    {
        return Shops::find()
            ->leftJoin('shop_group_related', 'shop_group_related.shop_id = shops.id')
            ->where([
                'shop_group_related.id' => NULL,
            ])->all();
    }

    public function getAllStoresQuery()
    {
        return ShopsStores::find()->where(['shop_id' => $this->id]);
    }

    public function inGroup()
    {
        return ShopGroupRelated::find()->where(['shop_id' => $this->id])->one() ? true : false;
    }

    public function getProducts()
    {
        return Goods::find()
            ->leftJoin(ShopGroupVariantLink::tableName(), 'shop_group_variant_link.product_id = goods.id')
            ->leftJoin(ShopGroupRelated::tableName(), 'shop_group_related.shop_group_id = shop_group_variant_link.shop_group_id')
            ->where([ShopGroupRelated::tableName() . '.shop_id' => $this->id])
            ->all();
    }

    public function getProductsQuery()
    {
        return Goods::find()
            ->leftJoin(ShopGroupVariantLink::tableName(), 'shop_group_variant_link.product_id = goods.id')
            ->leftJoin(ShopGroupRelated::tableName(), 'shop_group_related.shop_group_id = shop_group_variant_link.shop_group_id')
            ->where([ShopGroupRelated::tableName() . '.shop_id' => $this->id]);
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        // Если магазин отключили
        if (!$insert && ($changedAttributes['status'] == 1 && $this->status == 0)) {
            $stores = $this->getAllStoresQuery();
            $stores = $stores->all();
            if (!$stores) {

            } else {
                foreach ($stores as $store) {
                    $store->removeAllCountItems();
                }
            }
        }
        // Если магазин создали/включили
        if (($insert && $this->status == 1) || ($changedAttributes['status'] == 0 && $this->status == 1)) {
            $stores = $this->getAllStoresQuery();
            $stores = $stores->andWhere(['status' => 1])->all();
            if (!$stores) {

            } else {
                foreach ($stores as $store) {
                    $store->setAllCountItems();
                }
            }
        }
    }
}
