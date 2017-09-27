<?php
?>
<script>
    //Добавить метка;
    function add_meta() {
        loading('show');
        var inputs = $("#cms-autolinks form").serialize();
        $("#cms-autolinks div.alert").html('').hide();
        $.post(location.href, inputs, function (error) {
            if(error){
                $("#cms-autolinks div.alert.error").html(error).show();
                //Таймер после 3 сек закрываем окно;
                setTimeout(function(){
                    $("#cms-autolinks div.alert.error").html(error).fadeOut();
                }, 3000);
            }else {
                $.post(location.href, {}, function(html) {
                    $("#cms-autolinks div.items").html($(html).find("#cms-autolinks div.items").html());
                    $("#cms-autolinks div.alert.success").text('Успешно добавлен!').show();
                    setTimeout(function(){
                        $("#cms-autolinks div.alert.success").fadeOut();
                    }, 3000);
                });
            }
            loading('hide');
        });
        return false;
    }

    // Форма редактирования;
    function form_meta(id) {
        loading('show');
        $.post(location.href, {'form':true,'id':id}, function (html) {
            $("#cms-autolinks div.form-cms").html($(html).find("#cms-autolinks div.form-cms").html());
            loading('hide');
        });
    }
    // Редактировать метка;
    function edit_meta() {
        loading('show');
        var inputs = $("#cms-autolinks form").serialize();
        $("#cms-autolinks div.alert").html('').hide();
        $.post(location.href, inputs, function (error) {
            if(error){
                $("#cms-autolinks div.alert.error").html(error).show();
                setTimeout(function(){
                    $("#cms-autolinks div.alert.error").html(error).fadeOut();
                }, 3000);
            }else {
                $.post(location.href, {}, function(html) {
                    $("#cms-autolinks div.items").html($(html).find("#cms-autolinks div.items").html());
                    $("#cms-autolinks div.alert.success").text('Успешно сохранено!').show();
                    setTimeout(function(){
                        $("#cms-autolinks div.alert.success").fadeOut();
                    }, 3000);
                });
            }
            loading('hide');
        });
        return false;
    }
    // Удалить метка;
    function delete_meta(id) {
        loading('show');
        $.post(location.href, {'delete': true, 'id': id}, function (html) {
            $("#cms-autolinks div.items").html($(html).find("#cms-autolinks div.items").html());
            loading('hide');
        });
        return false;
    }
</script>
<div id="cms-autolinks">
    <div class="alert success hidden_r"></div>
    <div class="alert error hidden_r"></div>
    <div class="items">
        <?php if(!empty($meta) and isset($meta)): ?>
             <?php foreach($meta as $key=>$item): ?>
                    <div class="item  <?php if(!empty($item['status']) != '1'): ?>  off<?php endif; ?>" onclick="form_meta('<?= $item['id']; ?>');"><span class="meta" title="<?= $item['url']; ?>"><?= $item['meta']; ?></span><div class="delete" title="Удалить метка" onclick="delete_meta('<?= $item['id']; ?>');"></div></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
    <div class="form-cms">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="field">

                <?php if(!empty($meta_edit) and isset($meta_edit)): ?>
                <input type="hidden" class="string" value="true" name="edit">
                <input type="hidden" class="string" value="<?= $meta_edit['id']?>" name="id">

                <?php  else:?>
                <input type="hidden" class="string" value="true" name="add">

                <?php  endif;?>
                <div class="field-title"><?= Yii::t('admin', 'Метка:') ?></div>
                <input type="text" class="string" value="<?php if(isset($_POST['meta']) and !empty($_POST['meta'])): ?> <?= $_POST['meta']?> <?php endif;?> <?php if(!empty($meta_edit) and isset($meta_edit)): ?>  <?= $meta_edit['meta'];?><?php endif;?>" name="meta">
            </div>
            <div class="field">
                <div class="field-title"><?= Yii::t('admin', 'URL адресс:') ?></div>
                <input type="text" class="string" value="<?php if(isset($_POST['url']) and !empty($_POST['url'])): ?> <?= $_POST['url']?> <?php endif;?> <?php if(!empty($meta_edit) and isset($meta_edit)): ?>  <?= $meta_edit['url'];?><?php endif;?>" name="url">
            </div>
            <div class="field">
                <div class="field-title"><?= Yii::t('admin', 'Активность:') ?></div>
                <select name="status"><option value="0"><?= Yii::t('admin', 'Нет') ?></option><option value="1" selected=""><?= Yii::t('admin', 'Да') ?></option></select>
            </div>
            <div class="button field">

                <?php if(!empty($meta_edit) and isset($meta_edit)): ?>
                <input class="button_pun" type="button" value="<?= Yii::t('admin', 'Сохранить') ?>" name="edit" onclick="edit_meta();">

                <?php  else:?>
                <input class="button_pun" type="button" value="<?= Yii::t('admin', 'Добавить') ?>" name="add"  onclick="add_meta();">

                <?php  endif;?>
                <div class="reset_button cms" onclick="location.reload();"><div><?= Yii::t('admin', 'Отмена') ?></div></div>
            </div>
        </form>
    </div>