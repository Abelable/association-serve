<?php
/**
 * User: sun
 * Date: 2021/8/12
 * Time: 3:09 下午
 */

namespace api\modules\admin\forms;


use common\models\api\admin\Article;
use common\models\api\admin\ArticleClass;
use yii\base\Model;

class ArticleClassForm extends Model
{

    /**
     * 分类列表数据
     */
    const SCENARIO_LIST = 'scenario_list';


    /**
     * 创建分类场景
     */
    const SCENARIO_CREATE = 'scenario_create';


    /**
     * 更新分类场景
     */
    const SCENARIO_UPDATE = 'scenario_update';

    /**
     * 删除分类场景
     */
    const SCENARIO_DEL = 'scenario_del';


    /**
     * @var string 分类标题
     */
    public $title = '';


    /**
     * @var int 默认排序 越大显示越靠前
     */
    public $sort = 255;


    /**
     * @var int 文章分类的id
     */
    public $id = 0;

    /**
     * @var int 页码
     */
    public $page = 1;

    /**
     * @var int 一页的数量
     */
    public $page_size = 20;


    public function rules()
    {
        return [
            // 创建场景
            [['title','sort'],'required','on'=>[static::SCENARIO_CREATE]],
            [['sort'],'integer','on'=>[static::SCENARIO_CREATE]],
            [['sort'],'integer','on'=>[static::SCENARIO_CREATE]],
            [['title'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_CREATE]],


            // 更新分类场景
            [['title','sort','id'],'required','on'=>[static::SCENARIO_UPDATE]],
            [['sort','id'],'integer','on'=>[static::SCENARIO_UPDATE]],
            [['title'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_UPDATE]],

            // 删除场景
            [['id'],'required','on'=>[static::SCENARIO_DEL]],
            [['id'],'integer','on'=>[static::SCENARIO_DEL]],

            // 查询列表场景
            [['page','page_size'],'integer','on'=>[static::SCENARIO_LIST]],
        ];
    }


    /**
     * 创建分类信息
     */
    public function classCreate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $ArticleClass = new ArticleClass();
        $ArticleClass->title = $this->title;
        $ArticleClass->sort = $this->sort;
        $ArticleClass->status = $ArticleClass::STATUS_ACTIVE;
        if (!$ArticleClass->save()){
            \Yii::error("创建分类失败 res:".json_encode($ArticleClass->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'创建分类失败');
            return false;
        }
        return true;
    }

    /**
     * 更新分类信息
     */
    public function classUpdate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$ArticleClass = ArticleClass::findOne(['id'=>$this->id])){
            $this->addError("EnterFrom",'分类不存在');
            return false;
        }
        $ArticleClass->title = $this->title;
        $ArticleClass->sort = $this->sort;
        if (!$ArticleClass->save()){
            \Yii::error("更新分类失败 res:".json_encode($ArticleClass->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'更新分类失败');
            return false;
        }
        return true;
    }


    /**
     * 删除分类
     */
    public function classDel(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$ArticleClass = ArticleClass::findOne(['id'=>$this->id])){
            $this->addError("EnterFrom",'分类不存在');
            return false;
        }

        $Transaction = \Yii::$app->db->beginTransaction();
        // 现有的该分类下的文章id 重置为0
        Article::updateAll(['article_class_id'=>0],['article_class_id'=>$ArticleClass->id]);

        $ArticleClass->status = $ArticleClass::STATUS_DELETE;
        if (!$ArticleClass->save()){
            $Transaction->rollBack();
            \Yii::error("删除分类失败 res:".json_encode($ArticleClass->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'删除分类失败');
            return false;
        }
        $Transaction->commit();
        return true;
    }


    /**
     * 获取列表数据
     */
    public function classList(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        $ArticleClassModel = ArticleClass::find()
            ->where(['status'=>ArticleClass::STATUS_ACTIVE]);




        $res['total'] = $ArticleClassModel->count();

        $offset = ($this->page-1)*$this->page_size;
        $ArticleClassModel
            ->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort'=>SORT_DESC]);

        $res['list'] = $ArticleClassModel->asArray()->all();
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        return $res;

    }

}