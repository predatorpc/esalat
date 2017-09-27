<?php

?>
<script>
    // Добавить баннер;
    function add_banners() {
        loading('show');
        $('.container').load(window.location.href, {'action': 'add'}, function () {
            loading('hide');
        });
        return false;
    }
    // Редактировать баннер;
    function edit_banners(id) {
        loading('show');
        $('.container').load(window.location.href, {'action': 'edit','id': id}, function () {
            loading('hide');
        });
        return false;
    }
    // Удалить баннер;
    function delete_banners(id,type) {
        if (confirm('Удалить баннер?')) {
            loading('show');
            $.post(location.href, {'delete': true, 'id': id, 'type':type}, function (response) {
                location.reload();
            });
        }
        return false;
    }
</script>
    <h1 class="title my"><?= Yii::t('admin', 'Список баннеров') ?></h1>
    <div id="cms-banners">
     <?php if(isset($message) and $message):?><div class="alert success"><?=$message?></div><?php endif;?>
     <?php if(isset($_POST['action']) and $_POST['action'] == 'add' or (!empty($error) and $error)) {?>
        <div class="form-cms">
            <h2 class="name"><?= Yii::t('admin', 'Добавить') ?></h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Название') ?>:</div>
                    <input type="text" class="string" value="<?php if(isset($_POST['name'])): ?><?=$_POST['name']?><?php endif;?>" name="name">
                     <?php if(!empty($error['name'])): ?><div class="error"><?=$error['name']?></div><?php endif;?>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'URL адресс:') ?></div>
                    <input type="text" class="string" value="  <?php if(isset($_POST['url'])): ?><?=$_POST['url']?><?php endif;?>" name="url">
                    <?php if(!empty($error['url'])): ?><div class="error"><?=$error['url']?></div><?php endif;?>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Позиция') ?>:</div>
                    <input type="text" class="number-min" value="  <?php if(isset($_POST['position'])): ?><?=$_POST['position']?><?php else: ?>0<?php endif;?>" name="position">
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Тип') ?>:</div>
                    <select name="type">
                        <option value="0" ><?= Yii::t('admin', 'Нет') ?></option>
                        <?php foreach($type_array as $key=>$i): ?>
                           <option value="<?=$key?>" <?php if(isset($_POST['position']) and $_POST['position'] == $key): ?>selected=""<?php endif;?>><?=$i?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Активность:') ?></div>
                    <select name="status"><option value="0"><?= Yii::t('admin', 'Нет') ?></option><option value="1" selected=""><?= Yii::t('admin', 'Да') ?></option></select>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Выбирете файл') ?>:</div>
                    <input type="file" name="banner" value="<?= Yii::t('admin', 'Выбрать') ?>" class="file">
                    <?php if(!empty($error['banner'])): ?><div class="error"><?=$error['banner']?></div><?php endif;?>
                </div>
                <div class="button field">
                    <input class="button_pun" type="submit" value="<?= Yii::t('admin', 'Добавить') ?>" name="add">
                    <div class="reset_button cms" onclick="location.href = '/seo-tool/banners';"><div><?= Yii::t('admin', 'Отмена') ?></div></div>
                </div>
            </form>
            <div class="clear"></div>
        </div>

        <?php }elseif(isset($_POST['action']) and $_POST['action']  == 'edit' or !empty($error_ed) and $error_ed) {?>
        <div class="form-cms">
            <h2 class="name"><?= Yii::t('admin', 'Редактировать') ?></h2>
            <form action="/seo-tool/banners" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Название') ?>:</div>
                    <input type="text" class="string" value="<?=$banner['name']?>" name="name">
                    <input type="hidden" class="string" value="<?=$banner['id']?>" name="id">
                    <?php if(!empty($error_ed['name'])): ?><div class="error"><?=$error_ed['name']?></div><?php endif;?>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'URL адресс:') ?></div>
                    <input type="text" class="string" value="<?=$banner['url']?>" name="url">
                    <?php if(!empty($error_ed['url'])): ?><div class="error"><?=$error_ed['url']?></div><?php endif;?>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Позиция') ?>:</div>
                    <input type="text" class="number-min" value="<?=$banner['position']?>" name="position">
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Тип') ?>:</div>
                    <select name="type">
                        <option value="0"><?= Yii::t('admin', 'Нет') ?></option>
                        <?php foreach($type_array as $key=>$i): ?>
                            <option value="<?=$key?>" <?php if(isset($banner['type']) and $banner['type'] == $key): ?>selected=""<?php endif;?>><?=$i?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Активность:') ?></div>
                    <select name="status"><option value="0"><?= Yii::t('admin', 'Нет') ?></option><option value="1" selected=""><?= Yii::t('admin', 'Да') ?></option></select>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Выбирете файл') ?>:</div>
                    <input type="file" name="banner" value="<?= Yii::t('admin', 'Выбрать') ?>" class="file">
                    <?php if(!empty($error_ed['banner'])): ?><div class="error"><?=$error_ed['banner']?></div><?php endif;?>
                </div>
                <div class="field">
                    <div class="field-title"><?= Yii::t('admin', 'Баннер') ?>:</div>
                    <div class="img"><img src="/files/<?php if(isset($banner['type']) and $banner['type'] == '3'): ?>slides<?php else:?>posters<?php endif;?>/<?=$banner['id']?>.jpg" alt=""> </div>
                </div>
                <div class="button field">
                    <input class="button_pun" type="submit" value="<?= Yii::t('admin', 'Сохранить') ?>" name="edit">
                    <div class="reset_button cms" onclick="location.href = '/seo-tool/banners';"><div><?= Yii::t('admin', 'Отмена') ?></div></div>
                </div>

            </form>
        </div>
        <?php }else{?>
        <div class="list-table">
            <div class="button_pun cms" onclick="add_banners(); return false;"><div><?= Yii::t('admin', 'Добавить') ?></div></div>
            <div class="clear"></div>
            <table cellpadding="0" cellspacing="0" border="0" class="my-table cms-table">
                <tr class="grey res">
                    <th>№</th>
                    <th><?= Yii::t('admin', 'Изображения') ?></th>
                    <th><?= Yii::t('admin', 'Имя') ?></th>
                    <th>URL</th>
                    <th><?= Yii::t('admin', 'Тип') ?></th>
                    <th><?= Yii::t('admin', 'Позиция') ?></th>
                    <th><?= Yii::t('admin', 'Действия') ?></th>
                </tr>

                <?php $i = 0; ?>
                <?php foreach($banner_list as $key=> $item): ?>
                    <tr <?php if(isset($item['status']) and $item['status']  != '1'):?> class="on"<?php endif;?>>
                    <td class="number">  <?= $i++; ?></td>
                    <td class="images"><img src="/files/<?php if(isset($item['type']) and $item['type']  == '3'):?>slides<?php else:?>posters<?php endif;?>/<?= $item['id']?>.jpg" alt=""> </td>
                    <td class="name"><a href="<?= $item['url']?>" class="no-border" target="_blank"><?= $item['name']?></a></td>
                    <td class="url"><?= $item['url']?></td>
                    <td class="type"><?= $item['type_name']?></td>
                    <td class="position"><?=$item['position']?></td>
                    <td class="action"><a href="#" class="red" onclick="edit_banners('<?= $item['id']?>'); return false;"><?= Yii::t('admin', 'Редактировать') ?></a> / <a href="#" class="off" onclick="delete_banners('<?= $item['id']?>{$item.id}','<?= $item['type']?>{$item.type}'); return false;"><?= Yii::t('admin', 'Удалить') ?></a></td>
                    </tr>

             <?php endforeach;?>
            </table>
        </div>
  <?php } ?>

</div>