<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
$siteLabel = 'Памяти Николая Николаевича Федосика';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <meta name="description" content="Федосик Николай Николаевич, Федосик Николай Николаевич Борисов, память" />
    <meta name="keywords" content="Федосик Николай Николаевич, Федосик Николай Николаевич Борисов, память" />
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="3 month">
    <title><?= $siteLabel ?></title>
    <link rel="shortcut icon" href="<?= Yii::getAlias('@rootDir') ?>/favicon.ico" />
    <!-- Федосик Николай Николаевич Борисов -->
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap" style="background-color: black; min-width: 800px;">
    <?php
    NavBar::begin([
        'brandLabel' => $siteLabel,
        'brandUrl' => Yii::$app->homeUrl,
        'screenReaderToggleText' => $siteLabel,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            //['label' => 'ГЛАВНАЯ', 'url' => ['/site/index']],
            //['label' => 'О НАС', 'url' => ['/site/about']],
            //['label' => 'НАШИ КОНТАКТЫ', 'url' => ['/site/contact']],
            [
                'label' => Html::img('/img/vk.png', ['width' => '20px']),
                'url' => '//vk.com/ripfedosik',
                'linkOptions' => ['target' => '_blank'],
                'encode' => false,
            ]
            /*Yii::$app->user->isGuest ?
                ['label' => 'Login', 'url' => ['/site/login']] :
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ],*/
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <div style="color: white; font-family: fantasy; text-align: center;">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <div style="font-size: 48px; line-height: 50px;">
                Посвящается светлой Памяти<br>
                Николая Николаевича Федосика
            </div>
            <div style="font-size: 18px;">трагически погибшего 5 октября 2013 года</div>
        </div>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
<?php if(YII_ENV_PROD): ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-73107998-1', 'auto');
        ga('send', 'pageview');

    </script>
<?php endif; ?>
</html>
<?php $this->endPage() ?>
