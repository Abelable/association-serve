<?php


namespace services\api;


use common\models\common\Article;
use common\models\common\ArticleClass;
use common\components\Service;
use common\models\common\ArticleLike;
use common\models\common\ArticleLook;
use common\models\common\Banner;
use yii\data\Pagination;

class ArticleService extends Service
{

    public function getCategory(){
        $list = ArticleClass::find()
            ->where(['status' => 1])
            ->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();
        return $list;
    }

    public function getArticle($memberId, $params,$pageSize){
        $query = Article::find()
            ->with(['articleClass'])
            ->where(['status' => 1]);
        if(!empty($params['class_id'])) {
            $query = $query->andWhere(['article_class_id' => $params['class_id']]);
        }

        //$count = $query->count();

        // 使用总数来创建一个分页对象
        //$pagination = new Pagination(['totalCount' => $count,'validatePage' => false]);
        $articles = $query->offset(($params['page'] -1 ) * $pageSize)
            ->limit($pageSize)
            ->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->asArray()
            ->all();
        foreach ($articles as &$val) {
            $val['like_num'] = $val['virtual_like'] + $val['actual_like'];
            $val['look_num'] = $val['virtual_look'] + $val['actual_look'];
            if(!empty($memberId)) {
                $articleLike = ArticleLike::findOne(['member_id' => $memberId, 'article_id' => $val['id'], 'is_like' => 1]);
                $articleLook = ArticleLook::findOne(['member_id' => $memberId, 'article_id' => $val['id']]);
                $val['is_like'] = !empty($articleLike) ? 1 : 0;
                $val['is_look'] = !empty($articleLook) ? 1 : 0;
            }else{
                $val['is_like'] = 0;
                $val['is_look'] = 0;
            }

        }
        return $articles;
    }

    public function getArticleInfo($memberId, $params){
        if($params['action'] == 1) {  //下滑
            $cal = '<';
            $sort = SORT_DESC;
        }else{  //上滑
            $cal = '>';
            $sort = SORT_DESC;
        }

        $query = Article::find()
            ->with(['articleClass'])
            ->where(['status' => 1])
            ->andWhere(['<>','id',$params['article_id']]);

        $first = [];
        if(!empty($params['last_id'])){
            $query = $query->andWhere([$cal, 'id', $params['last_id']])->limit($params['limit']);
        }else{
            $first = Article::find()
                ->with(['articleClass'])
                ->where(['status' => 1,'id' => $params['article_id']])
                ->asArray()
                ->one();
            $params['limit'] -= 1;

        }

        $query = $query->andWhere(['article_class_id' => $params['class_id']]);


        $articles = $query
            ->orderBy(['id' => $sort])
            ->limit((int)$params['limit'])
            ->asArray()
            ->all();
        if(empty($params['last_id'])) {
            array_unshift($articles,$first);
        }


        foreach ($articles as &$val) {
            $val['like_num'] = $val['virtual_like'] + $val['actual_like'];
            $val['look_num'] = $val['virtual_look'] + $val['actual_look'];

            if(!empty($memberId)) {
                $articleLike = ArticleLike::findOne(['member_id' => $memberId, 'article_id' => $val['id'], 'is_like' => 1]);
                $articleLook = ArticleLook::findOne(['member_id' => $memberId, 'article_id' => $val['id']]);
                $val['is_like'] = !empty($articleLike) ? 1 : 0;
                $val['is_look'] = !empty($articleLook) ? 1 : 0;
            }else{
                $val['is_like'] = 0;
                $val['is_look'] = 0;
            }
        }


        return $articles;
    }

    public function getBanner($page,$pageSize){
        $timeNow = time();
        $query = Banner::find()
            ->where(['is_show' => 1, 'status' => 1])
            ->andWhere(['<=','s_time',$timeNow])
            ->andWhere(['>=','e_time',$timeNow]);

        //$count = $query->count();
        // 使用总数来创建一个分页对象
        //$pagination = new Pagination(['totalCount' => $count,'validatePage' => false]);
        $list = $query->offset(($page-1) * $pageSize)
            ->limit($pageSize)
            ->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();
        return $list;
    }

    public function articleLike($memberId, $params){

        $model = ArticleLike::findOne(['article_id' => $params['article_id'],'member_id' => $memberId]);
        if(!$model){
            $model = new ArticleLike();
        }

        $model->article_id = $params['article_id'];
        $model->member_id = $memberId;
        $model->is_like = $params['type'];
        if(!$model->save()) {
            return false;
        }

        //更新文章点赞数
        $article = Article::findOne(['id' => $params['article_id'], 'status' => 1]);
        if($params['type'] == 1) {
            $like = 1;
        }else{
            $like = -1;
        }
        $article->actual_like += $like;
        if(!$article->save()){
            return false;
        }

        return true;
    }

    public function articleLook($memberId, $params){
        $model = new ArticleLook();
        $model->member_id = $memberId;
        $model->article_id = $params['article_id'];
        if(!$model->save()) {
            return false;
        }

        //更新文章阅读数
        $article = Article::findOne(['id' => $params['article_id'], 'status' => 1]);
        $article->actual_look += 1;
        if(!$article->save()){
            return false;
        }

        return true;
    }
}