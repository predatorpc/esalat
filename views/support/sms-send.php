<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Messages */

$this->title = 'Отправка СМС';
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Отправка СМС'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<h1><?= Html::encode($this->title) ?></h1><br><br><br>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<script>
    function ajax() {
        var msg = $('#getForm').serialize();
        $.ajax({
            type: 'GET',
            url: '/support/sms-send',
            data: msg,
            success: function(data) {
                alert(data);
                //$('#results').html(data);
            },
            error:  function(xhr, str){
                alert('Возникла ошибка: ' + xhr.responseCode);
            }
        });
    }
</script>

<div class="messages-view">

    <?php if (!empty($u)) $shortFIO = preg_replace('#(.*)\s+(.*).*\s+(.*).*#usi', '$2 $3', $u['name']); ?>

    <div class="row">
        <form class="form-horizontal text-right" action="" method="POST" id="getForm">
            <input type="hidden" name="_csrf" value="<?php Yii::$app->request->csrfToken ?>">
            <div class="col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label><?= Yii::t('admin', 'Номер телефона'); ?></label>
                        <input type="tel" name="phone" value="<?= (!empty($u)) ? trim($u['phone']) : '' ; ?>" id="phone" class="form-control" required>
                    </div>
                </div>
                <textarea class="form-control custom-control" name="text" form="getForm" id="text" placeholder="Введите сообщение..." rows="5" style="resize:vertical"><?= (!empty($u)) ? $shortFIO  . '! ': '' ; ?></textarea><br>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="button" id="formPost" data-target="table1" class="btn btn-primary"
                                onclick="return ajax();"><?= Yii::t('admin', 'Отправить'); ?>
                        </button>
                    </div>

                </div>
            </div>
        </form>



    </div> <!--./row-->
</div>
