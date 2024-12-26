<?php


namespace services\api;

use \common\models\common\Article;
use common\components\Service;
use common\helpers\ImageHelper;
use common\helpers\UploadHelper;
use common\models\common\Attachment;
use common\models\common\ClassRoom;
use common\models\common\Legal;
use common\models\common\Legal1;
use common\models\common\WisdomLibrary;
use EasyWeChat\Factory;

class ShareService extends Service
{
    public function getShareInfo($userId,$params){
        $obj = new self();
        switch ($params['type']){
            case 1:
                $method = [$obj,'getArticle'];
                $params['user_id'] = $userId;
                $paramArr = [$params];
                break;
            case 2:
                $method = [$obj,'getLegal'];
                $params['user_id'] = $userId;
                $paramArr = [$params];
                break;
            case 3:
                $method = [$obj,'getWisdomLibrary'];
                $params['user_id'] = $userId;
                $paramArr = [$params];
                break;
            case 4:
                $method = [$obj,'getClassRoom'];
                $params['user_id'] = $userId;
                $paramArr = [$params];
                break;
            default:
                break;
        }

        return call_user_func_array($method,$paramArr);
    }

    /**
     * 新闻分享
     * @param $params
     * @return array
     */
    public function getArticle($params){
        $coverImg = $params['cover_img'] ?? '';
        $article = Article::findOne(['id' => $params['article_id'],'status' => 1]);
        $miniAppPath = sprintf('%s?article_id=%s&class_id=%s',\Yii::$app->params['wechatMiniProgramConfig']['pages']['article'] , $params['article_id'] ?? '',$article->article_class_id);
        return [
            'title'      => $article->title ?? '',
            'sub_title'   => $params['sub_title'] ?? '',
            'content'   =>  $article->content ?? '',
            'cover_img' => $article->img ?? '',
            'logo'           => 'https://img-gov.oss-cn-hangzhou.aliyuncs.com/logo.jpg',
            'web_url'     => $params['h5_url'] ?? '',
            'mini_app_id'    => \Yii::$app->params['wechatMiniProgramConfig']['app_id'],
            'mini_app_path'  => $miniAppPath,
            'app_code' => $this->getUnlimited($params['article_id'].'-'.$article->article_class_id,\Yii::$app->params['wechatMiniProgramConfig']['pages']['article']),
            'share_type'     => [
                'wechat_friends'       => false,
                'wechat_moments'       => false,
                'mini_program'         => true,
                'mini_program_picture' => true,
            ],
        ];
    }

    public function getLegal($params){
        $legal = Legal::findOne(['id' => $params['legal_id'],'status' => 1]);
        $miniAppPath = sprintf('%s?legal_id=%s',\Yii::$app->params['wechatMiniProgramConfig']['pages']['legal'] , $params['legal_id'] ?? '');
        return [
            'title'      => $legal->title ?? '',
            'sub_title'   => $params['sub_title'] ?? '',
            'content'   =>  $legal->content ?? '',
            'cover_img' => $legal->image ?? '',
            'logo'           => 'https://img-gov.oss-cn-hangzhou.aliyuncs.com/logo.jpg',
            'web_url'     => $params['h5_url'] ?? '',
            'mini_app_id'    => \Yii::$app->params['wechatMiniProgramConfig']['app_id'],
            'mini_app_path'  => $miniAppPath,
            'app_code' => $this->getUnlimited($params['legal_id'],\Yii::$app->params['wechatMiniProgramConfig']['pages']['legal']),
            'share_type'     => [
                'wechat_friends'       => false,
                'wechat_moments'       => false,
                'mini_program'         => true,
                'mini_program_picture' => true,
            ],
        ];
    }

    public function getLegal1($params){
        $legal = Legal1::findOne(['id' => $params['legal_id'],'status' => 1]);
        $miniAppPath = sprintf('%s?legal_id=%s',\Yii::$app->params['wechatMiniProgramConfig']['pages']['legal'] , $params['legal_id'] ?? '');
        return [
            'title'      => $legal->title ?? '',
            'sub_title'   => $params['sub_title'] ?? '',
            'content'   =>  $legal->content ?? '',
            'cover_img' => $legal->image ?? '',
            'logo'           => 'https://img-gov.oss-cn-hangzhou.aliyuncs.com/logo.jpg',
            'web_url'     => $params['h5_url'] ?? '',
            'mini_app_id'    => \Yii::$app->params['wechatMiniProgramConfig']['app_id'],
            'mini_app_path'  => $miniAppPath,
            'app_code' => $this->getUnlimited($params['legal_id'],\Yii::$app->params['wechatMiniProgramConfig']['pages']['legal']),
            'share_type'     => [
                'wechat_friends'       => false,
                'wechat_moments'       => false,
                'mini_program'         => true,
                'mini_program_picture' => true,
            ],
        ];
    }

    public function getWisdomLibrary($params) {
        $wisdom = WisdomLibrary::findOne(['id' => $params['wisdom_library_id'],'status' => 1]);
        $miniAppPath = sprintf('%s?wisdom_library_id=%s',\Yii::$app->params['wechatMiniProgramConfig']['pages']['wisdom_library'] , $params['wisdom_library_id'] ?? '');
        return [
            'title'      => $wisdom->name ?? '',
            'sub_title'   => $params['sub_title'] ?? '',
            'content'   =>  $wisdom->content ?? '',
            'cover_img' => '',
            'logo'           => 'https://img-gov.oss-cn-hangzhou.aliyuncs.com/logo.jpg',
            'web_url'     => $params['h5_url'] ?? '',
            'mini_app_id'    => \Yii::$app->params['wechatMiniProgramConfig']['app_id'],
            'mini_app_path'  => $miniAppPath,
            'app_code' => $this->getUnlimited($params['wisdom_library_id'],\Yii::$app->params['wechatMiniProgramConfig']['pages']['wisdom_library']),
            'share_type'     => [
                'wechat_friends'       => false,
                'wechat_moments'       => false,
                'mini_program'         => true,
                'mini_program_picture' => true,
            ],
        ];
    }

    public function getClassRoom($params) {
        $classRoom = ClassRoom::findOne(['id' => $params['class_room_id'],'status' => 1]);
        $miniAppPath = sprintf('%s?class_room_id=%s',\Yii::$app->params['wechatMiniProgramConfig']['pages']['class_room'] , $params['class_room_id'] ?? '');
        return [
            'title'      => $classRoom->title ?? '',
            'sub_title'   => $params['sub_title'] ?? '',
            'content'   =>  $classRoom->introduction ?? '',
            'cover_img' => $classRoom->cover_img,
            'logo'           => 'https://img-gov.oss-cn-hangzhou.aliyuncs.com/logo.jpg',
            'web_url'     => $params['h5_url'] ?? '',
            'mini_app_id'    => \Yii::$app->params['wechatMiniProgramConfig']['app_id'],
            'mini_app_path'  => $miniAppPath,
            'app_code' => $this->getUnlimited($params['class_room_id'],\Yii::$app->params['wechatMiniProgramConfig']['pages']['class_room']),
            'share_type'     => [
                'wechat_friends'       => false,
                'wechat_moments'       => false,
                'mini_program'         => true,
                'mini_program_picture' => true,
            ],
        ];
    }

    public function getUnlimited($scene,$page){
        $app = Factory::miniProgram(\Yii::$app->params['wechatMiniProgramConfig']);
        $response = $app->app_code->getUnlimit($scene, [
            'page'  => $page,
            'width' => 600,
        ]);
        // $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败为数组或你指定的 API 返回类型

        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
//            $filePath = \Yii::getAlias("@runtime/appCode");
            $filePath = '/var/www/government/web/attachment/';
            $filename = uniqid().'appcode.png';
            $response->saveAs($filePath, $filename);
            //上传到oss
            //var_dump( $filePath.'/'.$filename);exit();
            $file = $filePath.'/'.$filename;
            return 'https://api.zjseca.com/attachment/'.$filename;
            $fp = fopen ( $file , "r" );
            $fr = fread($fp, filesize($file));
            $base64File = base64_encode($fr);
//            $upload = new UploadHelper(['file' => $file,'drive' => 'oss'], Attachment::UPLOAD_TYPE_IMAGES);
//            $upload->verifyFile();
//            $upload->save();
//            $base64File = base64_encode($file);
            $upload = new UploadHelper(['image' => $base64File,'drive' => 'oss', 'extend' => 'png'], Attachment::UPLOAD_TYPE_IMAGES);
            $upload->verifyBase64($base64File, 'png');
            $upload->save(base64_decode($base64File));
            unlink($filePath.'/'.$filename);

            $url = str_replace('http','https',$upload->getBaseInfo()['url']);
            return $url;
        }

        return '';
    }

    /** 文件转base64输出
     * @param String $file 文件路径
     * @return String base64 string
     */
    public function fileToBase64(string $file){
        $base64_file = '';
        if(file_exists($file)){
            $mime_type= mime_content_type($file);
            $base64_data = base64_encode(file_get_contents($file));
            $base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
        }
        return $base64_file;
    }
}
