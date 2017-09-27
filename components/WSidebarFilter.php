<?php
namespace app\components;
use yii\base\Widget;

class WSidebarFilter extends Widget {

    public function run(){
        ?>
            <!--Фильтры-->
            <div class="category___sidebar filter-list">
                <!--Пошаговая констркуция-->
                <div class="filters step__wid">
                    <div class="step-item filter">
                        <div class="main-name">Фильтр по:</div>
                        <div class="step-container ">
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name">BSN</div>
                                <div class="clear"></div>
                            </div>
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name"><span class="color" style="background: red"></span>Красный</div>
                                <div class="clear"></div>
                            </div>
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name">1 000 - 2 000 р.</div>
                                <div class="clear"></div>
                            </div>
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name">ewrwerwer erwerwer wer</div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="button_oran"><div>Очистить все</div></div>
                    </div>
                    <div class="step-item">
                        <div class="title">Цена</div>
                        <div class="step-container form__filter prices">
                            <div class="input-filter"><input type="text" class="price number min" value="0" data-min="0"/></div>
                            <div class="input-filter"><input type="text" class="price number max" value="0" data-max="30000"/></div>
                            <div id="slider-price">
                                <div class="br-slider"></div>
                            </div>
                        </div>
                        <div class="button_oran"><div>Применить</div></div>
                    </div>
                    <div class="step-item">
                        <div class="title">Бренд</div>
                        <div class="step-container form__filter">
                            <div class="checkbox"><label class="checkbox__label" ><input type="checkbox" value="" name="" checked disabled/><span class="checkbox-checked">BSN</span><span class="counts">(2)</span></label></div>
                            <div class="checkbox"><label class="checkbox__label" ><input type="checkbox" value="" name="" checked/><span class="checkbox-checked">Dymatize</span></label></div>
                            <div class="checkbox"><label class="checkbox__label" ><input type="checkbox" value="" name="" disabled/><span class="checkbox-checked">Optimum nut2fsfsffdfs</span><span class="counts">(2)</span></label></div>
                            <div class="checkbox"><label class="checkbox__label" ><input type="checkbox" value="" name=""/><span class="checkbox-checked">Optimum nutsdfs dfsdfs</span><span class="counts">(2)</span></label></div>
                            <div class="more"><a href="#" class="icon-more">Показать все</a></div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="title">Цвет</div>
                        <div class="step-container colors">
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name"><span class="color" style="background: red"></span><span class="name-color">Красный</span> (3)</div>
                                <div class="clear"></div>
                            </div>
                            <div class="filter-button open">
                                <div class="close">&times;</div>
                                <div class="name"><span class="color" style="background: red"></span><span class="name-color">Красный</span> (10)</div>
                                <div class="clear"></div>
                            </div>
                            <div class="filter-button">
                                <div class="close">&times;</div>
                                <div class="name"><span class="color" style="background: red"></span><span class="name-color">Красный werwerwe werwe</span> (2)</div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="title">Меню</div>
                        <div class="step-container category">
                            <?=WLeftCatalogMenu::widget(['key' => 10000002]) ?>
                        </div>
                    </div>
                </div>  <!--.Пошаговая констркуция-->
            </div><!--/Фильтры-->


        <?php
    }
}