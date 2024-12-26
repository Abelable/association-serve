<?php
/**
 * User: sun
 * Date: 2021/8/12
 * Time: 3:07 下午
 */

namespace api\modules\admin\controllers;


use api\modules\admin\forms\ArticleClassForm;
use api\modules\admin\forms\ArticleForm;
use common\helpers\ResultHelper;
use common\models\api\admin\Article;

class ArticleController extends BaseController
{

    public $authMethod = ['article-info'];


    /**
     * 文章分类列表
     */
    public function actionClassList(){
        $ArticleClassForm = new ArticleClassForm();
        // 设置场景
        $ArticleClassForm->setScenario($ArticleClassForm::SCENARIO_LIST);
        $ArticleClassForm->load(\Yii::$app->request->get(),'');

        if (!$res = $ArticleClassForm->classList()){
            return  ResultHelper::json('422',"获取列表失败",$ArticleClassForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 添加/更新文章分类信息
     */
    public function actionClassSave(){
        $ArticleClassForm = new ArticleClassForm();

        $fromRes = false;
        if (\Yii::$app->request->post('id',0)){
            // 设置场景
            $ArticleClassForm->setScenario($ArticleClassForm::SCENARIO_UPDATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $ArticleClassForm->load(\Yii::$app->request->post(),'');
            $fromRes = $ArticleClassForm->classUpdate();
        }else{
            // 设置场景
            $ArticleClassForm->setScenario($ArticleClassForm::SCENARIO_CREATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $ArticleClassForm->load(\Yii::$app->request->post(),'');
            $fromRes = $ArticleClassForm->classCreate();
        }

        if (!$fromRes){
            return  ResultHelper::json('422',"保存失败",$ArticleClassForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);

    }

    /**
     * 删除文章分类信息
     */
    public function actionClassDel(){
        $ArticleClassForm = new ArticleClassForm();
        // 设置场景
        $ArticleClassForm->setScenario($ArticleClassForm::SCENARIO_DEL);
        $ArticleClassForm->load(\Yii::$app->request->post(),'');

        if (!$res = $ArticleClassForm->classDel()){
            return  ResultHelper::json('422',"删除失败",$ArticleClassForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 获取文章列表数据
     */
    public function actionArticleList(){

        $ArticleForm = new ArticleForm();
        // 设置场景
        $ArticleForm->setScenario($ArticleForm::SCENARIO_LIST);
        // 加载数据
        $ArticleForm->load(\Yii::$app->request->get(),'');
        if (!$res = $ArticleForm->articleList()){
            return  ResultHelper::json('422',"获取列表失败",$ArticleForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);

    }


    /**
     * 文章添加/修改
     */
    public function actionArticleSave(){
        $ArticleForm = new ArticleForm();

        $fromRes = false;
        if (\Yii::$app->request->post('id',0)){
            // 设置场景
            $ArticleForm->setScenario($ArticleForm::SCENARIO_UPDATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $ArticleForm->load(\Yii::$app->request->post(),'');
            $fromRes = $ArticleForm->articleUpdate();
        }else{
            // 设置场景
            $ArticleForm->setScenario($ArticleForm::SCENARIO_CREATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $ArticleForm->load(\Yii::$app->request->post(),'');
            $fromRes = $ArticleForm->articleCreated();
        }

        if (!$fromRes){
            return  ResultHelper::json('422',"保存失败",$ArticleForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 文章详情接口
     */
    public function actionArticleInfo(){
        $ArticleForm = new ArticleForm();
        // 设置场景
        $ArticleForm->setScenario($ArticleForm::SCENARIO_INFO);
        // 加载数据
        $ArticleForm->load(\Yii::$app->request->get(),'');
        if (!$res = $ArticleForm->articleInfo()){
            return  ResultHelper::json('422',"获取文章信息失败",$ArticleForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }


    /**
     * 删除文章
     */
    public function actionArticleDel(){
        $Id  = \Yii::$app->request->post('id',0);
        if (!$Id){
            return  ResultHelper::json('422',"id不能为空",[]);
        }
        if (!$Article = Article::findOne(['id'=>$Id])){
            return  ResultHelper::json('422',"文章不存在",[]);
        }
        $Article->status = Article::STATUS_DELETE;
        if (!$Article->save()){
            return  ResultHelper::json('422',"删除失败",[]);
        }
        return  ResultHelper::json('200',"success",[]);
    }


}