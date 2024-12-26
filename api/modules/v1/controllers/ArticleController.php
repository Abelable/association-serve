<?php


namespace api\modules\v1\controllers;

use api\modules\v1\forms\ArticleForm;
use common\models\common\ArticleLike;
use Yii;
use api\controllers\OnAuthController;
use common\helpers\ResultHelper;

class ArticleController extends OnAuthController
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
    protected $authOptional = ['category-list','article-list','banner-list','article-info'];


    /**
     * 获取文章类别
     * @return array|mixed
     */
    public function actionCategoryList() {
        $data = Yii::$app->services->articleService->getCategory();
        return ResultHelper::json(200,'',$data);
    }

    /**
     * 获取文章列表
     */
    public function actionArticleList(){
        $model = new ArticleForm();
        $model->scenario = 'article_list';
        $model->attributes = Yii::$app->request->get();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));

        }

        $pageSize = Yii::$app->request->get('page_size', 15);
        $memberId = Yii::$app->user->identity->id ?? 0;
        $data = Yii::$app->services->articleService->getArticle($memberId, $model->attributes,$pageSize);
        return ResultHelper::json(200,'',$data);
    }

    /**
     * 新闻详情
     * @return array|mixed
     */
    public function actionArticleInfo(){
        $model = new ArticleForm();
        $model->scenario = 'article_info';
        $model->attributes = Yii::$app->request->get();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }
        $memberId = Yii::$app->user->identity->id ?? 0;
        $data = Yii::$app->services->articleService->getArticleInfo($memberId,$model->attributes);
        return ResultHelper::json(200,'',$data);
    }

    /**
     * 首页头图
     * @return array|mixed
     */
    public function actionBannerList(){
        $pageSize = Yii::$app->request->get('page_size', 15);
        $page = Yii::$app->request->get('page', 1);
        $data = Yii::$app->services->articleService->getBanner($page, $pageSize);
        return ResultHelper::json(200,'',$data);
    }

    public function actionArticleLike(){
        $model = new ArticleForm();
        $model->scenario = 'article_like';
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $memberId = \Yii::$app->user->identity->id;

        $articleLike = ArticleLike::findOne(['article_id' => $model->article_id,'member_id' => $memberId, 'is_like' => $model->type]);
        if($articleLike){
            return ResultHelper::json(422,'重复操作');
        }

        $res = Yii::$app->services->articleService->articleLike($memberId, $model->attributes);

        if(!$res) {
            return ResultHelper::json(422,'操作失败');
        }
        return ResultHelper::json(200,'成功');
    }

    public function actionArticleLook(){
        $model = new ArticleForm();
        $model->scenario = 'article_info';
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $memberId = \Yii::$app->user->identity->id;

        $res =  Yii::$app->services->articleService->articleLook($memberId, $model->attributes);
        if(!$res) {
            return ResultHelper::json(422,'操作失败');
        }
        return ResultHelper::json(200,'成功');
    }
}