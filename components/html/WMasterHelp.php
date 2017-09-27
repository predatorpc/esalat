<?php
namespace app\components\html;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\common\models\ModFunctions;
use yii\helpers\Url;
//use yii\widgets\Pjax;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 * Version 2.2.3
 */

class WMasterHelp extends Widget
{
    public $menuId;

    public function init()
    {
        parent::init();
        if ($this->menuId === null) {
            $this->menuId = \Yii::$app->params['menuListId']['parent_id'];
        }
    }
    public function run()
    {
      return false;

        if (empty(Category::findOne(['id'=>Yii::$app->params['menuListId']['id'], 'type_master' => 1]))) {

            //
        } else {
            ?>


            <?php
            $catalogHash = Category::find()->select(['id', 'parent_id', 'title', 'alias', 'sort'])->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
            $urls = [];
            $catalogListTree = Catalog::buildTree($catalogHash, $urls);
            $catalogListMenu = array();
            $urls[0] = '/' . Yii::$app->params['catalogPath'];
            foreach ($catalogHash as $key => $value) {
                if (isset($value['parent_id'])) {
                    $catalogListMenu[$value['parent_id']]['group'][$key] = $value;
                    $urls[$value['id']] = (isset($urls[$value['parent_id']])) ? $urls[$value['parent_id']] . '/' . $value['alias'] : '/catalog/' . $value['alias'] . '/';
                    if (isset($branch[$value['parent_id']]['parent_id'])) {
                        $catalogListMenu[$value['parent_id']]['group'][$key]['url'] = (isset($urls[$value['parent_id']])) ? $urls[$value['parent_id']] . '/' . $value['alias'] . '/' : '/catalog/' . $value['alias'];
                    } else {
                        $catalogListMenu[$value['parent_id']]['group'][$key]['url'] = (isset($urls[$value['parent_id']])) ? $urls[$value['parent_id']] . '/' . $value['alias'] . '/' : '/catalog/' . $value['alias'];
                    }
                }
                $catalogListMenu[$key] = $value;
            }
            /*---Можно настроить спомощью параметры (Yii::$app->params['menuListId'])---*/
            // 3- уровень категория;
            $menuListGroup = Category::find()->select(['id', 'parent_id', 'title', 'alias', 'sort'])->where(['active' => 1,'type_master' => 1, 'parent_id' => Yii::$app->params['menuListId']['parent_id']])->indexBy('id')->orderBy('level, sort')->asArray()->all();
            // Состояние окно;
            $session = Yii::$app->session;

            if (Yii::$app->session->get('shopMaster', 0) > 0) {
                $session['menuList'] = true;
            } else {
                if (!empty($session['menuList'])) {
                    unset($session['menuList']);
                }
            }
            // Сохраняем пользователь установка куки 2 часа;
            if (isset($_POST['masterHelp'])) setcookie('masterHelp', true, time() + 3600 * 2, '/');

            // По умолчние стоит раздел продукты;
            $menuList = (!empty($menuListGroup) ? $menuListGroup : $catalogListMenu[$this->menuId]['group']);
            $countThis = 0;
            $menuListArray = array();
            $countMenu = -1;

            //Обход выбранной меню;
            foreach ($menuList as $id => $value) {
                $countMenu++;
                if (empty($value['url'])) $value['url'] = $urls[$value['id']];
                $value['count'] = $countMenu;
                // Позиция товара;
                if (Yii::$app->params['menuListId']['id'] == $id) {
                    $countThis = $countMenu;
                }
                $menuListArray[] = $value;
            }
            // ----Перелисть для категория----;
            $categoryAll = Category::find()->select(['id', 'parent_id', 'title', 'alias', 'sort'])->where(['active' => 1,'type_master' => 1, 'level' => 0])->orderBy('level, sort')->asArray()->all();
            $countThisCatalog = 0;
            foreach ($categoryAll as $k => $v) {
                $urlGroupCategory = Category::find()->where(['active' => 1,'type_master' => 1, 'parent_id' => $v['id']])->orderBy('level, sort')->one();
                $alias = '/catalog/' . $v['alias'] . '/' . $urlGroupCategory['alias'];
                $categoryAll[$k]['url'] = $alias;
                if (Yii::$app->params['menuListId']['catalog_id'] == $v['id']) {
                    $countThisCatalog = $k;
                }
            }
            $categoryAllPage = array_slice($categoryAll, $countThisCatalog, 2);
            $nextCategory = (!empty($categoryAllPage[1]) ? $categoryAllPage[1] : $categoryAll[0]);
            // Назад;
            for ($i = 0; $i < count($categoryAll); $i++) {
                if (!empty($categoryAllPage[0]) && $categoryAllPage[0]['id'] == $categoryAll[$i]['id']) {
                    $j = $i - 1;
                    $prevCategory = ($j >= 0 ? $categoryAll[$j] : end($categoryAll));
                }
            }
            // ----/Перелисть для категория----;
            $menuListPage = array_slice($menuListArray, $countThis, 2);
            // Центер;
            $menuListPage['center'] = (!empty($menuListPage[0]) ? $menuListPage[0] : '');

            // Назад;
            for ($i = 0; $i < count($menuListArray); $i++) {
                if (!empty($menuListPage['center']) && $menuListPage['center']['id'] == $menuListArray[$i]['id']) {
                    $j = $i - 1;
                    $menuListPage['prev'] = ($j >= 0 ? $menuListArray[$j] : $prevCategory);
                }
            }
            // В Перед;
            $menuListPage['next'] = (!empty($menuListPage[1]) ? $menuListPage[1] : $nextCategory);
            // Текущий урл;
            $urlTo = explode('/', Url::to(['index']));
            ?>

            <!--Мастер помощник  || $urlTo[1] == 'catalog'-->
            <div id="help-master">
                <div class="body-master  <?= (!empty($session['menuList']) ? 'open' : '') ?> ">
                    <div class="help">
                        <div class="<?= !empty(Yii::$app->params['en']) ? 'master-icon-en' : '' ?> master-icon _master_h_m_1"></div>
                    </div>
                    <div class="content-master" data-id="<?= Yii::$app->params['menuListId']['id'] ?>"
                         data-parent_id="<?= $this->menuId ?>">
                        <button class="close" type="button">×</button>
                        <div class="text-content  <?= (!empty($_COOKIE['masterHelp']) ? 'hidden' : '') ?>" rel="1">
                            <div class="text">
                                <p style="text-align: center;font-size: 16px; "><b
                                        style=" margin: 5px 0px;display: block;"><?= \Yii::t('app', 'Добро пожаловать в мастер покупок!'); ?></b>
                                </p>
                                <p><?= \Yii::t('app', 'Мастер поможет вам легко и быстро выбрать необходимые товары и поместить их в корзину') ?>
                                    . </p>
                                <?php if (Yii::$app->params['en']): ?>
                                    <p>Just scroll through the categories and fill up your cart.</p>
                                <?php else: ?>
                                    <p>Просто прокручивайте категории товаров стрелочками <b style="color:#f17503">
                                            &#9668; &#9658;</b> и выбирайте товары .</p>
                                <?php endif; ?>
                                <p> <?= \Yii::t('app', 'Затем останется только оплатить покупку!') ?></p>
                                <p></p>
                                <p>
                                    <b style=" margin: 5px 0px;display: block; text-align: center;"><?= \Yii::t('app', 'C чего начнем?') ?></b>
                                </p></div>
                            <div class="select__form min _master_h_m_2">
                                <div rel="1" class="container-select ">
                                    <div data-text-select="Выберите Вкус"
                                         class="option-text tag-value-group-title"><?= \Yii::t('app', 'Выберите категории') ?></div>
                                    <div class="selectbox"></div>
                                </div>
                                <div class="row top">
                                    <?php foreach (Category::find()->where(['active' => 1,'type_master' => 1, 'level' => 0])->orderBy('level, sort')->all() as $group):
                                        $urlGroup = Category::find()->where(['active' => 1,'type_master' => 1, 'parent_id' => $group['id']])->one();
                                        $alias = '/catalog/' . $group['alias'] . '/' . $urlGroup['alias'];
                                        ?>
                                        <div class="option" data-id="<?= $group['id'] ?>"
                                             onclick="masterHelp('<?= $alias ?>');"><?= $group->title ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="goods-carousel-select <?= (empty($_COOKIE['masterHelp']) ? 'hidden' : '') ?>">

                            <div class="nav-master">
                                <a class="next" href="<?= $menuListPage['next']['url']; ?>"></a>
                                <a class="prev" href="<?= $menuListPage['prev']['url']; ?>"></a>
                            </div>
                            <div class="block">
                                <div class="select__form" style="display:block;width: 80%">
                                    <div rel="1" class="container-select">
                                        <div data-text-select="<?= \Yii::t('app', 'Выберите категории') ?>"
                                             data-catalog_id="<?= Yii::$app->params['menuListId']['catalog_id'] ?>"
                                             class="option-text tag-value-group-title"><?= $catalogListMenu[Yii::$app->params['menuListId']['catalog_id']]['title']; ?></div>
                                        <div class="selectbox"></div>
                                    </div>
                                    <div class="row top">
                                        <?php foreach (Category::find()->where(['active' => 1,'type_master' => 1, 'level' => 0])->orderBy('level, sort')->all() as $group):
                                            $urlGroup = Category::find()->where(['active' => 1,'type_master' => 1, 'parent_id' => $group['id']])->orderBy('level, sort')->one();
                                            $alias = '/catalog/' . $group['alias'] . '/' . $urlGroup['alias'];
                                            ?>
                                            <a class="no-border" href="<?= $alias ?>">
                                                <div class="option <?= ($group['id'] == $this->menuId ? '' : '') ?>">
                                                    <span class="colors">
                                                       <?php if(!empty(ModFunctions::img_path('/images/menu/'.$group['id'].'.png'))):  ?>
                                                           <img src="/images/menu/<?= $group['id'] ?>.png"/>
                                                       <?php else: ?>
                                                            <img src="/images/menu/10000116.png"/>
                                                        <?php endif; ?>
                                                    </span>

                                                    <?= $group->title ?>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="select__form min-2 hidden">
                                    <div rel="1" class="container-select">
                                        <div data-text-select="<?= \Yii::t('app', 'Выберите категории') ?>"
                                             class="option-text tag-value-group-title"><?= (($menuListPage['center']['parent_id'] != Yii::$app->params['menuListId']['catalog_id']) ? $catalogListMenu[$menuListPage['center']['parent_id']]['title'] : $menuListPage['center']['title']); ?></div>
                                        <div class="selectbox"></div>
                                    </div>

                                    <div class="row top">
                                        <?php foreach (Category::find()->where(['active' => 1,'type_master' => 1,'parent_id' => Yii::$app->params['menuListId']['catalog_id']])->orderBy('level, sort')->all() as $group): ?>
                                            <a class="no-border" href="<?= $urls[$group['id']] ?>">
                                                <div
                                                    class="option <?= ($group['id'] == $catalogListMenu[$menuListPage['center']['parent_id']] ? 'open' : '') ?>"><?= $group->title ?></div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="items">
                                <a class="good prev "
                                   href="<?= $menuListPage['prev']['url']; ?>"><?= $menuListPage['prev']['title']; ?></a>
                                <a class="good active"
                                   href="<?= $menuListPage['center']['url']; ?>"><?= $menuListPage['center']['title']; ?></a>
                                <a class="good next js-value-master" href="<?= $menuListPage['next']['url']; ?>"
                                   data-id="<?= $menuListPage['next']['id']; ?>"
                                   data-catalog_id="<?= (!empty($menuListPage['next']['parent_id']) ? Yii::$app->params['menuListId']['catalog_id'] : $menuListPage['next']['id']) ?>"><?= $menuListPage['next']['title']; ?></a>
                            </div>
                            <div
                                class="text-min"><?= $menuListPage['center']['count'] + 1; ?> <?= \Yii::t('app', 'из') ?> <?= count($menuList) ?></div>
                        </div>
                    </div>
                </div>
            </div><!--/Мастер помощник-->
            <?php
        }
    }
}