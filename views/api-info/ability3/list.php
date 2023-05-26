<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\models\Ability3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability3[] $abilities
 * @var View $this
 * @var array[] $langs
 */

TableResponsiveForceAsset::register($this);

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed">
    <thead>
      <tr>
        <th><code>key</code></th>
<?php foreach ($langs as $lang) { ?>
        <?= Html::tag('th', Html::encode($lang->name), [
          'class' => $lang->htmlClasses,
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($abilities as $ability) { ?>
      <tr>
        <td><code><?= Html::encode($ability->key) ?></code></td>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-ability3', $ability->name, [], $lang->lang),
            'enName' => $ability->name,
            'lang' => $lang->lang,
          ]),
          [
            'class' => $lang->htmlClasses,
            'lang' => $lang->lang,
          ]
        ) . "\n" ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
