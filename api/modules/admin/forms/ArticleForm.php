<?php
/**
 * User: sun
 * Date: 2021/8/12
 * Time: 4:18 下午
 */

namespace api\modules\admin\forms;


use common\models\api\admin\Article;
use common\models\api\admin\ArticleClass;
use yii\base\Model;

class ArticleForm extends Model
{

    /**
     * 创建分类场景
     */
    const SCENARIO_CREATE = 'scenario_create';


    /**
     * 更新分类场景
     */
    const SCENARIO_UPDATE = 'scenario_update';

    /**
     * 获取列表场景
     */
    const SCENARIO_LIST = 'scenario_list';

    /**
     * 文章详情
     */
    const SCENARIO_INFO = 'scenario_info';


    /**
     * @var string 文章标题
     */
    public $title = '';

    /**
     * @var string 图片url
     */
    public $img = '';

    /**
     * @var int 排序
     */
    public $sort = 0;


    /**
     * @var int 虚拟点赞数
     */
    public $virtual_like = 0;

    /**
     * @var int 虚拟观看数
     */
    public $virtual_look = 0;

    /**
     * @var int 状态[-1:删除;0:禁用;1启用]
     */
    public $status = 0;


    /**
     * @var int 页数
     */
    public $page = 1;


    /**
     * @var int 一页的数量
     */
    public $page_size = 20;


    /**
     * @var int 文章分类的id
     */
    public $article_class_id = null;

    /**
     * @var string 文章内容
     */
    public $content = '';

    /**
     * @var int 文章id
     */
    public $id = null;

    /**
     * @var int 修改时间 开始时间
     */
    public $updated_at_start = null;

    /**
     * @var null 修改时间 结束时间
     */
    public $updated_at_end = null;


    public function rules()
    {
        return [
            // 列表数据
            [['page','page_size','article_class_id'],'integer','on'=>[static::SCENARIO_LIST]],
            [['updated_at_start','updated_at_end'],'integer','on'=>[static::SCENARIO_LIST]],
            [['title'],'string','on'=>[static::SCENARIO_LIST]],
            [['title'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_LIST]],

            // 创建文章
            [['title','content','img','sort'],'required','on'=>[static::SCENARIO_CREATE]],
            [['title'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_CREATE]],
            [['content'], 'string','on'=>[static::SCENARIO_CREATE]],
            [['virtual_like','virtual_look','article_class_id'], 'integer','on'=>[static::SCENARIO_CREATE]],
            [['article_class_id'], 'validatorArticleClassId','on'=>[static::SCENARIO_CREATE]],
            [['img'], 'url','on'=>[static::SCENARIO_CREATE]],

            // 更新文章
            [['title','content','img','sort','id'],'required','on'=>[static::SCENARIO_UPDATE]],
            [['title'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_UPDATE]],
            [['content'], 'string','on'=>[static::SCENARIO_UPDATE]],
            [['virtual_like','virtual_look','article_class_id'], 'integer','on'=>[static::SCENARIO_UPDATE]],
            [['article_class_id'], 'validatorArticleClassId','on'=>[static::SCENARIO_UPDATE]],
            [['img'], 'url','on'=>[static::SCENARIO_UPDATE]],

            // 文章详情接口
            [['id'],'integer','on'=>[static::SCENARIO_INFO]],
            [['id'],'required','on'=>[static::SCENARIO_INFO]],
        ];
    }

    /**
     * 验证分类
     */
    public function validatorArticleClassId(){
        if (!ArticleClass::findOne(['id'=>$this->article_class_id,'status'=>ArticleClass::STATUS_ACTIVE])){
            $this->addError('article_class_id','分类不存在或者已经失效');
            return false;
        }

    }

    /**
     * 显示的列表
     */
    public function articleList(){
        if (!$this->validate()){
            return false;
        }



        $ArticleModel = Article::find();
        $ArticleModel->andfilterWhere(['like','title',$this->title]);
        $ArticleModel->andfilterWhere(['<','updated_at',$this->updated_at_end]);
        $ArticleModel->andfilterWhere(['>','updated_at',$this->updated_at_start]);
        $ArticleModel->andfilterWhere(['status'=>Article::STATUS_ACTIVE]);
        $ArticleModel->andFilterWhere(['article_class_id'=>$this->article_class_id]);




        $res['total'] = $ArticleModel->count();


        $offset = ($this->page-1)*$this->page_size;
        $ArticleModel
            ->limit($this->page_size)
            ->with('articleClass')
            ->offset($offset)
            ->orderBy(['sort'=>SORT_DESC]);


        $res['list'] = [];
        $Articles = $ArticleModel->all();
        foreach ($Articles as $Article){
            /**
             * @var Article $Article
             */
            $className = $Article->articleClass ? $Article->articleClass->title : '-';
            $res['list'][] = [
                'id'=>$Article->id,
                'img'=>$Article->img,
                'sort'=>$Article->sort,
                'show_like'=>$Article->showLike,
                'show_look'=>$Article->showLook,
                'created_at'=>$Article->created_at,
                'class_name' =>$className,
                'title'=>$Article->title,
                'updated_at'=>$Article->updated_at
            ];
        }


        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        return $res;
    }


    /**
     * 创建文章
     */
    public function articleCreated(){
        if (!$this->validate()){
            return false;
        }
        $Article = new Article();
        $Article->title = $this->title;
        $Article->article_class_id = $this->article_class_id;
        $Article->img = $this->img;
        $Article->sort = $this->sort;
        $Article->virtual_like = $this->virtual_like;
        $Article->actual_like = 0;
        $Article->virtual_look = $this->virtual_look;
        $Article->actual_look = 0;
        $Article->content = $this->content;
        $Article->status = $Article::STATUS_ACTIVE;
        if (!$Article->save()){
            \Yii::error("添加文章失败 res:".json_encode($Article->errors,JSON_UNESCAPED_UNICODE));
            $this->addError('id','添加文章失败');
            return false;
        }
        return true;
    }


    /**
     * 更新文章信息
     */
    public function articleUpdate(){
        if (!$this->validate()){
            return false;
        }
        if (!$Article = Article::findOne(['id'=>$this->id])){
            $this->addError("id",'文章不存在');
            return false;
        }

        $Article->title = $this->title;
        $Article->article_class_id = $this->article_class_id;
        $Article->img = $this->img;
        $Article->sort = $this->sort;
        $Article->virtual_like = $this->virtual_like;
        $Article->virtual_look = $this->virtual_look;
        $Article->content = $this->content;
        if (!$Article->save()){
            \Yii::error("更新文章失败 res:".json_encode($Article->errors,JSON_UNESCAPED_UNICODE));
            $this->addError('id','更新文章失败');
            return false;
        }
        return true;

    }


    /**
     * 文章详情
     */
    public function articleInfo(){
        if (!$this->validate()){
            return false;
        }

        if (!$Article = Article::findOne(['id'=>$this->id])){
            $this->addError("id",'文章不存在');
            return false;
        }

        return [
            'title'=>$Article->title,
            'article_class_id'=>$Article->article_class_id,
            'img'=>$Article->img,
            'sort'=>$Article->sort,
            'virtual_like'=>$Article->virtual_like,
            'virtual_look'=>$Article->virtual_look,
            'content'=>$Article->content,
        ];
    }
}