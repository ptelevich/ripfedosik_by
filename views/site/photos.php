<?php
$items = [];
foreach ($photos as $photo) {
    $items[] = [
        'url' => Yii::getAlias('@mediaDir/big/' . $photo['photo_name']),
        'src' => Yii::getAlias('@mediaDir/small/' . $photo['photo_name']),
        'options' => array('title' => '')
    ];
}
?>
<?= dosamigos\gallery\Gallery::widget(['items' => $items]);?>