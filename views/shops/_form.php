<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\widgets\MaskedInput;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;
use app\modules\common\models\UserShop;
use app\modules\common\models\Address;
use app\modules\managment\models\ShopsStores;
use app\modules\managment\models\ShopsStoresTimes;
?>

<div class="shops-form">

    <?php
    $form = ActiveForm::begin();
    $query = new yii\db\Query();
    $comission = $query->select('id,name')->from('comissions')->all();
    //var_dump($timeStart);var_dump($timeEnd);die();
    ?>

    <?= $form->field($model, 'id')->textInput(['readonly' => true,]) ?>
    <?= $form->field($model, 'type_id')->textInput(['readonly' => true, 'value' => 1001]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?php
    echo $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '999-999-99-99', ])->label('Phone')->hint(Yii::t('admin', 'Номер телефона в формате без +7/8, Пример: XXX-XXX-XX-XX'));


    ?>
    <?= $form->field($model, 'name_full')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'contract')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'comission_id')->DropDownList(ArrayHelper::map($comission,'id','name')) ?>
    <?= $form->field($model, 'comission_value')->textInput(['maxlength' => true]) ?>
    <?php // $form->field($model, 'status')->textInput()
    if(1){echo
    $form->field($model, 'status')
        ->checkbox([
            'label' => Yii::t('admin', 'Активный магазин'),
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);
    }else {
        $disabled = false;
        if ($model->status == 1) {
            $disabled = false;
        } else {
            $disabled = true;
        }
        $form->field($model, 'status')
            ->checkbox(
                [
                    'label'        => Yii::t('admin', 'Активный магазин'),
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                    'disabled'     => $disabled,
                ]
            );
    }
    if(1){echo
    $form->field($model, 'show')
        ->checkbox([
            'label' => Yii::t('admin', 'Показ товаров на сайте'),
            'labelOptions' => [
                'style' => 'padding-left:20px;'
            ],
            'disabled' => false,
        ]);
    }else {
        $disabled = false;
        if ($model->show == 1) {
            $disabled = false;
        } else {
            $disabled = true;
        }
        $form->field($model, 'show')
            ->checkbox(
                [
                    'label'        => Yii::t('admin', 'Показ товаров на сайте'),
                    'labelOptions' => [
                        'style' => 'padding-left:20px;'
                    ],
                    'disabled'     => $disabled,
                ]
            );
    }
    ?>





    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Вернуться назад'), ['/shops', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <br><br>
    <table class="table">
        <tr><th>Пользователь</th><th>Действие</th><th>Дата</th><th>Комментарий</th><th>Контактное лицо</th><th>Телефон</th></tr>
        <?php
        $shopsCallback = \app\modules\managment\models\ShopsCallback::find()->where(['status'=>1,'shop_id'=>$model->id])->all();
        if(isset($shopsCallback) && count($shopsCallback)>0) {
            foreach ($shopsCallback as $callback) {?>
                <tr>
                    <td><?=User::find()->where(['id'=>$callback->user_id])->One()->name;?></td>
                    <td><?=$callback->action;?></td>
                    <td><?=$callback->date;?></td>
                    <td><?=$callback->comment;?></td>
                    <td><?=$callback->contact;?>
                    <td><?=$callback->phone;?></td>
                </tr>
            <?php }
        }
        Modal::begin([
            'header' => '<h3><b>'.Yii::t('admin', 'Добавить запись к магазину').':</b><br>'.$model->name.'</h3>',
            'toggleButton' => [
                'tag' => 'button',
                'class' => 'btn btn-primary',
                'label' => Yii::t('admin', 'Добавить Запись'),
            ]
        ]);
        Pjax::begin();
        echo Html::beginForm(['shops/addcallback'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
        echo Html::hiddenInput('shop_id', $model->id);
        echo '<div class="form-group">';
        echo '<b>Номер телефона</b><br>';
        echo Html::input('text', 'phone', Yii::$app->request->post('string'), ['class' => 'form-control phone', 'minlength' => 4, 'maxlength' => 10]);
        echo '</div>';
        echo '<div class="form-group">';
        echo '<b>Контактное лицо</b><br>';
        echo Html::input('text', 'contact', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
        echo '</div>';
        echo '<div class="form-group">';
        echo '<b>Выберите действие</b><br>';
        echo Html::dropDownList('action','',['звонок'=>'звонок','запрос'=>'запрос'],['class' => 'form-control']);
        echo '</div>';
        echo '<div class="form-group">';
        echo '<b>Комментарий</b><br>';
        echo Html::textarea('comment','',['class' => 'form-control']);
        echo '</div>';
        echo '<div class="form-group">';
        echo Html::submitButton(Yii::t('admin', 'Добавить'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
        echo '</div>';
        echo Html::endForm();
        Pjax::end();
        Modal::end();
        ?>
    </table>

    <br><br>

    <?php
    ///////////////////////////////////////////////////////////
    //       SHOP USERS
    //////////////////////////////////////////////////////////
    if($create == false){ ?>

        <div class="form-group">

            <h3><?= Yii::t('admin', 'Пользователи') ?>: <?=$model->name?></h3>
            <?php
            ///////////////////////////////////////////////////////////
            //        MODAL USERS BEGIN
            //////////////////////////////////////////////////////////

            Modal::begin([
                'header' => '<h3><b>'.Yii::t('admin', 'Добавить пользователя к магазину').':</b><br>'.$model->name.'</h3>',
                'toggleButton' => [
                    'tag' => 'button',
                    'class' => 'btn btn-primary',
                    'label' => Yii::t('admin', 'Добавить пользователя'),
                ]
            ]);
            $userRolesArray = User::find()
                ->select('users.id, users.name, users.phone, users.email')
                //->select('users.id, users_roles.user_id, users.name')
                // ->joinWith(['roles'])
                ->where(['users.status'=>1])
                ->orderBy('name')
                ->asArray()
                ->All();
            $userRolesModel = new UserRoles();

            Pjax::begin();
            echo Html::beginForm(['getusername'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
            echo Html::hiddenInput('shop_id', $model->id);
            echo '<b>'.Yii::t('admin', 'Введите Номер телефона без +7').'</b><br>';
            echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
            echo Html::submitButton(Yii::t('admin', 'Найти'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
            echo Html::endForm();
            echo "<br>";
            //print_r($stringHash);
            if(!empty($stringHash)) {
                echo '<b>'.Yii::t('admin', 'Выберите нужного пользователя из списка').':</b><br>';
                foreach ($stringHash as $item) {
                    echo Html::a($item['id'] . " " . $item['name'],
                            'useradd?id=' . $item['id'] . '&shop_id='
                            . $model->id
                        ) . "<br>";
                }
            }else {
                echo "<br>";
            }
            Pjax::end();
            Modal::end();

            ///////////////////////////////////////////////////////////
            //        MODAL USERS BEGIN
            //////////////////////////////////////////////////////////
            //        Generating table for users

            $usersAttached = UserRoles::find()->where(['shop_id' => $id])->all();
            foreach($usersAttached as $key) {
                $user = UserShop::find()->where(['id' => $key->user_id])->asArray()->all();
                $form = ActiveForm::begin(['action' => 'userupdate', 'id'=>"form_".$key->id, 'method' => 'post',]);
                echo Html::hiddenInput('shop_id', $model->id);
                echo Html::hiddenInput('role_id', $key->id);
                echo $form->field($key, 'id')->TextInput(['readonly' => true,'value' => $user[0]['id']])->label('ID');
                echo $form->field($key, 'name')->TextInput(['readonly' => true,'value'=> $user[0]['name']])->label(Yii::t('admin', 'Имя пользователя'));
                echo $form->field($key, 'phone')->TextInput(['readonly' => true,'value'=> $user[0]['phone']])->label(Yii::t('admin', 'Телефон'));
                echo $form->field($key, 'email')->TextInput(['readonly' => true,'value'=> $user[0]['email']])->label('Email');
                $disabled = false;
                if ($key->status == 1) {
                    $disabled = false;
                    echo $form->field($key, 'status')
                        ->checkbox(
                            [
                                'label'        => Yii::t('admin', 'Активность'),
                                'labelOptions' => [
                                    'style' => 'padding-left:20px;'
                                ],
                                //        'disabled' => $disabled,
                            ]
                        );
                } else {
                    $disabled = true;
                    echo $form->field($key, 'status')
                        ->checkbox(
                            [
                                'label'        => Yii::t('admin', 'Активность'),
                                'labelOptions' => [
                                    'style' => 'padding-left:20px;'
                                ],
                                //       'disabled' => $disabled,
                            ]
                        );
                }
                echo Html::submitButton(Yii::t('admin', 'Сохранить статус'), ['class' => 'btn btn-primary']);
                ActiveForm::end();
            }
            ?>

        </div>

    <?php }
    ///////////////////////////////////////////////////////////
    //       SHOP USERS
    //////////////////////////////////////////////////////////
    ?>


    <br><br>


    <?php if($create == false){ ?>

        <div class="form-group">

            <h3><?= Yii::t('admin', 'Склады') ?>: <?=$model->name?></h3>


            <?php
            ///////////////////////////////////////////////////////////
            //        MODAL BEGIN
            //////////////////////////////////////////////////////////
            Modal::begin([
                'header' => '<h3><b>'.Yii::t('admin', 'Добавить склад к магазину').'</b><br>'.$model->name.'</h3>',
                'toggleButton' => [
                    'tag' => 'button',
                    'class' => 'btn btn-primary',
                    'label' => Yii::t('admin', 'Добавить склад'),
                ]
            ]);
            $addressModel = new Address();
            $storeModel = new ShopsStores();
            $storeForm = ActiveForm::begin(['action' => 'storeadd', 'id' => 'addstoreid', 'method' => 'post',]);
            //echo $storeForm->field($storeModel, 'model_id')->HiddenInput()->label('');
            echo $storeForm->field($storeModel, 'shop_id')->hiddenInput(['value' => $model->id])->label('');
            echo $storeForm->field($storeModel, 'city_id')->textInput(['readonly' => true, 'value' => 1001]);
            //echo $storeForm->field($storeModel, 'address')->textInput(['maxlength' => 255]);

            echo $storeForm->field($storeModel, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '999-999-99-99', ])->label('Phone')->hint(Yii::t('admin', 'Номер телефона в формате без +7/8, Пример: XXX-XXX-XX-XX'));


            echo $storeForm->field($addressModel, 'street')->textInput()->label(Yii::t('admin', 'Улица'));
            echo $storeForm->field($addressModel, 'house')->textInput()->label(Yii::t('admin', 'Дом'));
            echo $storeForm->field($addressModel, 'room')->textInput()->label(Yii::t('admin', 'Помещение'));
            echo $storeForm->field($addressModel, 'comments')->textInput()->label(Yii::t('admin', 'Комментарий'));
            echo $storeForm->field($addressModel, 'status')->hiddenInput(['value' => 1])->label('');
            echo $form->field($storeModel, 'status')
                ->checkbox(
                    [
                        'label'        => Yii::t('admin', 'Активный'),
                        'labelOptions' => [
                            'style' => 'padding-left:20px;'
                        ],
                    ]
                );
            echo Html::submitButton(Yii::t('admin', 'Добавить'), ['class' => 'btn btn-primary']);
            ActiveForm::end();
            Modal::end();
            ///////////////////////////////////////////////////////////
            //        MODAL BEGIN
            //////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////
            //       STORES OF THE SHOP
            //////////////////////////////////////////////////////////
            //// Generating table for Stores

            $storesAttached = ShopsStores::find()->where('shop_id = '.$id.' and (status <> -1)')->all();

            foreach ($storesAttached as $keyId => $key) {

                $store = Address::find()
                    ->where(
                        ['id' => $key->address_id]
                    )->all();

                $key_times =ShopsStoresTimes::find()
                    ->where('store_id = '.$key->id)
                    ->andWhere('status = 1')
                    ->orderBy('day DESC')
                    ->one();

                $form = ActiveForm::begin(['action' => 'storeupdate', 'id'=>"form_".$key->id, 'method' => 'post', ]);

                echo Html::hiddenInput('shop_id', $model->id);

                echo Html::hiddenInput('id', $key->id);
                //echo $form->field($key, 'id')->hiddenInput(['value' => ])->label('');

                echo $form->field($key, "[$keyId]id")->TextInput(['readonly' => true])->label('ID');

                echo $form->field($key, "[$keyId]phone")->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '999-999-99-99', 'id'=>'phone_'.$model->id])->label('Phone')->hint(Yii::t('admin', 'Номер телефона в формате без +7/8, Пример: XXX-XXX-XX-XX'));

                //Потом добавим время редактирование
//                if(!empty($key_times)) {
//                    echo $form->field($key_times, 'time_begin')->widget(\yii\widgets\MaskedInput::className(), ['mask' => '99:99:99' ])->label('Начало работы');
//                    echo $form->field($key_times, 'time_end')->widget(\yii\widgets\MaskedInput::className(), ['mask' => '99:99:99' ])->label('Окончание работы');
//                }

                if(empty($key->address) && $store!=NULL)
                {
                    echo $form->field($key, "[$keyId]address")->textInput(
                        ['readonly' => true, 'value' => $store[0]['street']." ".$store[0]['house']]
                    );
                }
                else
                {
                    echo $form->field($key, "[$keyId]address")->textInput(
                        ['readonly' => true]
                    )->label(Yii::t('admin', 'Адрес'));
                }
                $disabled = false;
                if ($key->status == 1) {
                    $disabled = false;
                    echo $form->field($key, "[$keyId]status")
                        ->checkbox(
                            [
                                'label'        => Yii::t('admin', 'Активность'),
                                'labelOptions' => [
                                    'style' => 'padding-left:20px;'
                                ],
                                //        'disabled' => $disabled,
                            ]
                        );
                } else {
                    $disabled = true;
                    echo $form->field($key, "[$keyId]status")
                        ->checkbox(
                            [
                                'label'        => Yii::t('admin', 'Активность'),
                                'labelOptions' => [
                                    'style' => 'padding-left:20px;'
                                ],
                                //       'disabled' => $disabled,
                            ]
                        );
                }
                echo Html::submitButton(Yii::t('admin', 'Сохранить статус'), ['class' => 'btn btn-primary']);
                echo Html::a(Yii::t('admin', 'Удалить'), ['/storedelete', 'id' => $key->id, 'shop_id' => $model->id],
                    [
                        'class' => 'btn btn-danger',
                        'data'  => [
                            'confirm' => Yii::t('admin', 'Точно удалить?'),
                            // 'method'  => 'get',
                        ],
                    ]
                );
                echo ' <a class="btn btn-warning" onclick="window_show(\'shop-stores-timetable/index-modal?store_id='.$key->id.'\',\'Расписание\',\'max\')">Посмотреть расписание</a>';
                ActiveForm::end();

            }


            ?>
        </div>
    <?php }
    ///////////////////////////////////////////////////////////
    //       STORES OF THE SHOP
    //////////////////////////////////////////////////////////
    ?>
</div>