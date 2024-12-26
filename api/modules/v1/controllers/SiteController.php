<?php

namespace api\modules\v1\controllers;

use api\modules\v1\forms\MiniProgramLoginForm;
use common\enums\AccessTokenGroupEnum;
use common\enums\MemberAuthEnum;
use common\enums\StatusEnum;
use common\helpers\UploadHelper;
use common\models\member\Auth;
use EasyWeChat\Factory;
use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use api\modules\v1\forms\UpPwdForm;
use api\controllers\OnAuthController;
use api\modules\v1\forms\LoginForm;
use api\modules\v1\forms\RefreshForm;
use api\modules\v1\forms\MobileLogin;
use api\modules\v1\forms\SmsCodeForm;
use api\modules\v1\forms\RegisterForm;

/**
 * 登录接口
 *
 * Class SiteController
 * @package api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SiteController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['login', 'refresh', 'mobile-login', 'sms-code', 'register', 'up-pwd', 'oauth', 'oauth-login'];

    /**
     * 登录根据用户信息返回accessToken
     *
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 登出
     *
     * @return array|mixed
     */
    public function actionLogout()
    {
        if (Yii::$app->services->apiAccessToken->disableByAccessToken(Yii::$app->user->identity->access_token)) {
            return ResultHelper::json(200, '退出成功');
        }

        return ResultHelper::json(422, '退出失败');
    }

    /**
     * 重置令牌
     *
     * @param $refresh_token
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionRefresh()
    {
        $model = new RefreshForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
    }

    /**
     * 手机验证码登录Demo
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionMobileLogin()
    {
        $model = new MobileLogin();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 获取验证码
     *
     * @return int|mixed
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSmsCode()
    {
        $model = new SmsCodeForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model->send();
    }

    /**
     * 注册
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = new Member();
        $member->attributes = ArrayHelper::toArray($model);
        $member->merchant_id = !empty($this->getMerchantId()) ? $this->getMerchantId() : 0;
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group);
    }

    /**
     * 密码重置
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    private function actionUpPwd()
    {
        $model = new UpPwdForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = $model->getUser();
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group);
    }


    /**
     * 微信小程序绑定
     * @return array|mixed|null
     * @throws NotFoundHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionWechatMiniBind(){
        $model = new MiniProgramLoginForm();
        $model->scenario = 'bind';
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $wechatUserInfo = $model->getUser();

        if ($member = $model->getMember($wechatUserInfo['unionId'])) {
            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($member,AccessTokenGroupEnum::WECHAT_MINI));
        } else {
            $member = Yii::$app->services->member->findByMobile($model->mobile);
            $gender = 0;
            if (!$member) {
                $member = $this->createMember($model->mobile, $wechatUserInfo['avatarUrl'], $gender, $wechatUserInfo['nickName']);
            }

            $auth = Yii::$app->services->memberAuth->create([
                'oauth_client' => Auth::CLIENT_WECHAT_MINI,
                'unionid' => $wechatUserInfo['unionId'] ?? '',
                'member_id' => $member['id'],
                'oauth_client_user_id' => $model->auth['openid'],
                'gender' => $gender,
                'nickname' => $wechatUserInfo['nickName'],
                'head_portrait' => $wechatUserInfo['avatarUrl'],
                'country' => $wechatUserInfo['country'],
                'province' => $wechatUserInfo['province'],
                'city' => $wechatUserInfo['city'],
            ]);
            if ($auth) {
                $data =Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group);
                $data['openid'] = $model->auth['openid'];
                return $data;
            }

            return ResultHelper::json(422, '绑定失败');
        }

    }

    /**
     * 微信小程序登陆
     * @return array|mixed|null
     * @throws \yii\base\Exception
     */
    public function actionWechatMiniLogin(){
        $model = new MiniProgramLoginForm();
        $model->scenario = 'login';
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $data = $model->getPhoneNumber();
        $member = Yii::$app->services->member->findByMobile($data['phoneNumber']);
        if($member){
            $data = Yii::$app->services->apiAccessToken->getAccessToken($member, AccessTokenGroupEnum::WECHAT_MINI);
            $data['openid'] = $model->auth['openid'];
        }

        return $data;
    }

    public function actionOauthLogin(){
        $code = Yii::$app->request->get('code');
        $config = [
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/api/v1/site/oauth?code='.$code,
            ]
        ];

        $app = Factory::officialAccount(array_merge(Yii::$app->params['wechatConfig'],$config));
        $oauth = $app->oauth;

        // 未登录
        if (empty($_SESSION['wechat_user'])) {var_dump($oauth->redirect());exit();
            $_SESSION['target_url'] = '/api/v1/site/oauth-login';
            $oauth->redirect()->send();
        }

        // 已经登录过
        $user = $_SESSION['wechat_user'];
        $model = new MiniProgramLoginForm();
        if ($member = $model->getMember($user['unionId'])) {
            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($member,AccessTokenGroupEnum::WECHAT));
        } else {
            $auth = Yii::$app->services->memberAuth->create([
                'oauth_client' => MemberAuthEnum::WECHAT,
                'unionid' => $user['unionId'] ?? '',
                'member_id' => $member['id'],
                'oauth_client_user_id' => $model->auth['openid'],
                'gender' => 0,
                'nickname' => $user['nickName'],
                'head_portrait' => $user['avatarUrl'],
                'country' => $user['country'],
                'province' => $user['province'],
                'city' => $user['city'],
            ]);
            if ($auth) {
                $data =Yii::$app->services->apiAccessToken->getAccessToken($member, AccessTokenGroupEnum::WECHAT);
                $data['openid'] = $model->auth['openid'];
                return $data;
            }

            return ResultHelper::json(422, '绑定失败');
        }

    }

    public function actionOauth() {var_dump(22);exit();
        /*header('Location:/api/v1/site/oauth-login');
        exit();*/

        $app = Factory::officialAccount(Yii::$app->params['wechatConfig']);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        $_SESSION['wechat_user'] = $user->toArray();

        $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];

        header('location:'. $targetUrl);
    }

    /**
     * 重组数据
     * @param $data
     * @return mixed
     */
    protected function regroupMember($data)
    {
        // 优惠券数量
        $data['member']['coupon_num'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($data['member']['id']);
        // 订单数量统计
        $data['member']['order_synthesize_num'] = Yii::$app->tinyShopService->order->getOrderCountGroupByMemberId($data['member']['id']);
        // 购物车数量
        $data['member']['cart_num'] = Yii::$app->tinyShopService->memberCartItem->count($data['member']['id']);

        /** 等级赋值 */
        if (!empty($data['member']['merchant_id'])) {
            $merchantLevelInfo = \common\models\merchant\Account::find()
                ->where(['merchant_id' => $data['member']['merchant_id']])
                ->with([
                    'merchantLevel' => function ($q) {
                        $q->select('level');
                    }
                ])
                ->select('merchant_level_id')
                ->asArray()
                ->one();
            $data['merchant']['level'] = $merchantLevelInfo['merchantLevel']['level'] ?? 0;
        }
        /** end */

        return $data;
    }

    protected function createMember($mobile, $head_portrait='', $gender='', $nickname='')
    {
        if ($head_portrait) {
            // 下载图片
            $upload = new UploadHelper(['writeTable' => StatusEnum::DISABLED], 'images');
            $imgData = $upload->verifyUrl($head_portrait);
            $upload->save($imgData);
            $baseInfo = $upload->getBaseInfo();
        }

        // 注册新账号
        $member = new Member();
        $member = $member->loadDefaultValues();
        $member->merchant_id = Yii::$app->services->merchant->getNotNullId();
        $member->pid = 1;

        $member->attributes = [
            'mobile' => $mobile,
            'gender' => $gender,
            'nickname' => $nickname,
            'head_portrait' => $baseInfo['url'] ?? '',
        ];
        $member->save();

        return $member;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['index', 'view', 'update', 'create', 'delete'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
