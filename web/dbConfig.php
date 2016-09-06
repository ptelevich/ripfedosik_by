<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
$get_param = isset($_GET, $_GET['param']) ? $_GET['param'] : null;

if (
    $get_param &&
    strlen($get_param) == 10 &&
    preg_match('/[^a-z0-9]+/', $get_param) !== false
) {
echo 'param => ' . $get_param . PHP_EOL;
    $content = "<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname={$get_param}_fedos',
    'username' => '{$get_param}_root',
    'password' => 'ripfedosik',
    'charset' => 'utf8',
];";

    file_put_contents('../config/db.php', $content);
}
exit;