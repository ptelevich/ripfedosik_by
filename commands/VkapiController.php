<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Album;
use app\models\Photos;
use app\models\User;
use yii\authclient\clients\VKontakte;
use yii\authclient\OAuthToken;
use yii\console\Controller;
use Yii;
use yii\helpers\StringHelper;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @property VKontakte _vkObject
 * @since 2.0
 */

class VkapiController extends Controller
{
    const VK_GROUP_ID = 113418826;
    const POSTFIX_IMAGE_NAME = 'ripfedosik_by';
    private $_vkObject;
    private $_albumID = '227674630';

    private static function _setLog($data)
    {
        if (isset($data['mess'])) {
           echo $data['mess'] . PHP_EOL;
        }
    }
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $this->start();
    }

    public function start()
    {
$this->_setLog(['mess' => 'starting...']);
        $this->_vkObject = Yii::$app->authClientCollection->getClient('vkontakte');
        if ($this->_makeOAuth()) {
$this->_setLog(['mess' => 'success make OAuth']);
            //$response = $this->_createAlbum();
            //$this->_createAlbumPhoto($response['albumData'], $response['albumThumbs']);
            $this->getPhotos();
        }
    }

    private function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->removeDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
        $this->_setLog(['mess' => 'remove dir - ' . $dir]);
            rmdir($dir);
        }
    }

    private function getPhotos()
    {
        sleep(1);
        $data = $this->_vkObject->api(
            'photos.get',
            'GET',
            [
                'owner_id' => '-' . self::VK_GROUP_ID,
                'album_id' => $this->_albumID,
                'rev' => 0,
                'extended' => 0,
            ]
        );

        $this->_setLog(['mess' => 'find ('.count($data['response']).') photo in album']);

        if(isset($data, $data['response'], $data['response'][0])) {
            foreach ($data['response'] as $response) {
                $pname = 'borisov';
                $pname = self::POSTFIX_IMAGE_NAME.'_'.self::_get_in_translate_to_en($pname).'_'.$response['pid'];
                $pname = preg_replace('/(\,|\.|\/|<.*>| )/', '_', $pname);
                $pname = preg_replace('/(_){1,}/', '_', $pname);
                $photoData[] = [
                    'photo_id' => (int)$response['pid'],
                    'vk_photo_small' => $response['src_small'],
                    'vk_photo_big' => $response['src_big'],
                    'photo_name' => $pname.'.jpg',
                    'text' => $response['text'],
                    'vk_created' => (int)$response['created'],
                    'created_at' => time(),
                    'updated_at' => time(),
                ];
            }

            if ($photoData && $photoData[0]) {

                Yii::$app->db->createCommand('TRUNCATE `'.$this->_getClassBaseName(new Photos()).'`')->execute();

                $isInsert = Yii::$app->db->createCommand()->batchInsert(
                    Photos::tableName(),
                    array_keys($photoData[0]),
                    $photoData
                )->execute();

                if ($isInsert) {

                    $this->removeDir(Yii::getAlias('@fullMediaDir/'));

                    foreach ($photoData as $p_data) {
                        $path_small = Yii::getAlias('@fullMediaDir/small/' . $p_data['photo_name']);
                        if(!is_dir(dirname($path_small))) {
                            mkdir(dirname($path_small), 0777, true);
                        }
                        $path_big = Yii::getAlias('@fullMediaDir/big/' . $p_data['photo_name']);
                        if(!is_dir(dirname($path_big))) {
                            mkdir(dirname($path_big), 0777, true);
                        }

                        $image = file_get_contents($p_data['vk_photo_small']);
                        file_put_contents($path_small, $image);

                        $image = file_get_contents($p_data['vk_photo_big']);
                        file_put_contents($path_big, $image);
                    }
                    
                    $this->_setLog(['mess' => 'save in path small: ' . $path_small]);
                    $this->_setLog(['mess' => 'save in path big: ' . $path_big]);
                }
            }
        }
    }

    private function _getClassBaseName($model)
    {
        return strtolower(StringHelper::basename(get_class($model)));
    }


    /** @var $vk VKontakte */
    private function _makeOAuth()
    {
        if($this->_vkObject) {
            $oAuthToken = new OAuthToken();
            if (isset($_GET['code']) && !$this->_vkObject->getAccessToken()) {
                $oAuthToken = $this->_vkObject->fetchAccessToken($_GET['code']);
                $this->_onAuthSuccess($this->_vkObject);
            } else {
                if (!$this->_vkObject->getAccessToken()) {
                    $token = User::findOne(['id' => 1])->token;
                    if ($oAuthToken->isExpired) {
                        $this->_vkObject->refreshAccessToken($token);
                    } else {
                        $oAuthToken->setToken($token);
                        $this->_vkObject->setAccessToken($oAuthToken);
                    }

                }
                if (!$this->_vkObject->getAccessToken()) {
                    $response = $this->_vkObject->buildAuthUrl();
                    return Yii::$app->controller->redirect($response);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @param $vk VK
     * @return Response
     * @throws Exception
     * @throws \yii\db\Exception
     */
    private function _onAuthSuccess($vk)
    {
        /** @var $this->_vkObject VKontakte */
        $attributes = $this->_vkObject->getUserAttributes();

        $user = User::findOne(['id' => 1]);
        $user->token = $this->_vkObject->getAccessToken()->token;
        return $user->save();
    }

    private static function _get_in_translate_to_en($string, $gost=false)
    {
        if($gost)
        {
            $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                "Е"=>"E","е"=>"e","Ё"=>"E","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                "Й"=>"I","й"=>"i","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n","О"=>"O","о"=>"o",
                "П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t","У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f",
                "Х"=>"Kh","х"=>"kh","Ц"=>"Tc","ц"=>"tc","Ч"=>"Ch","ч"=>"ch","Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch",
                "Ы"=>"Y","ы"=>"y","Э"=>"E","э"=>"e","Ю"=>"Iu","ю"=>"iu","Я"=>"Ia","я"=>"ia","ъ"=>"","ь"=>"");
        }
        else
        {
            $arStrES = array("ае","уе","ое","ые","ие","эе","яе","юе","ёе","ее","ье","ъе","ый","ий");
            $arStrOS = array("аё","уё","оё","ыё","иё","эё","яё","юё","ёё","её","ьё","ъё","ый","ий");
            $arStrRS = array("а$","у$","о$","ы$","и$","э$","я$","ю$","ё$","е$","ь$","ъ$","@","@");

            $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                "Е"=>"Ye","е"=>"e","Ё"=>"Ye","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                "Й"=>"Y","й"=>"y","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n",
                "О"=>"O","о"=>"o","П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t",
                "У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f","Х"=>"Kh","х"=>"kh","Ц"=>"Ts","ц"=>"ts","Ч"=>"Ch","ч"=>"ch",
                "Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch","Ъ"=>"","ъ"=>"","Ы"=>"Y","ы"=>"y","Ь"=>"","ь"=>"",
                "Э"=>"E","э"=>"e","Ю"=>"Yu","ю"=>"yu","Я"=>"Ya","я"=>"ya","@"=>"y","$"=>"ye");

            $string = str_replace($arStrES, $arStrRS, $string);
            $string = str_replace($arStrOS, $arStrRS, $string);
        }

        return iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
    }
}
