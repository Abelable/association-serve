<?php


namespace api\modules\admin\forms;


use common\models\common\WisdomLibrary;
use yii\base\Model;

class WisdomLibraryForm extends Model
{
    /**
     * 作者新增、编辑、删除
     */
    const SCENARIO_ARTICLE_SAVE = 'article_save';

    /**
     * 列表
     */
    const SCENARIO_ARTICLE_LIST = 'article_list';



    /**
     * @var string 人物姓名
     */
    public $name;

    /**
     * @var string 称号
     */
    public $title;

    /**
     * @var string 领域
     */
    public $field;

    /**
     * @var string 头像
     */
    public $head_img;

    /**
     * @var string 荣誉
     */
    public $honor;

    /**
     * @var string 内容
     */
    public $content;

    /**
     * @var int 排序
     */
    public $sort = 0;

    /**
     * @var int id
     */
    public $id;

    /**
     * @var int 状态
     */
    public $status = 1;

    public $page = 0;

    public $page_size = 15;

    public function rules()
    {
        return [
            //公共参数
            [['page','page_size'],'integer'],

            //作者添加、编辑、删除
            [['name', 'field', 'head_img', 'content','sort'], 'required', 'on' => [self::SCENARIO_ARTICLE_SAVE]],
            [['title', 'honor'], 'string', 'on' => [self::SCENARIO_ARTICLE_SAVE]],
            [['id','status','sort'], 'integer', 'on' => [self::SCENARIO_ARTICLE_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_ARTICLE_SAVE]],

            //智库列表
            [['name'], 'string', 'on' => [self::SCENARIO_ARTICLE_LIST]],
        ];
    }


    /**
     * 文章管理，添加、编辑、新增
     * @return bool
     */
    public function articleSave() {
        if(!$this->validate()) {
            return false;
        }
        $author = WisdomLibrary::findOne(['id' => $this->id]);
        if(!$author) {
            $author = new WisdomLibrary();
        }
        $author->name = $this->name;
        $author->title = $this->title;
        $author->field = $this->field;
        $author->head_img = $this->head_img;
        $author->honor = $this->honor;
        $author->content = $this->content;
        $author->sort = $this->sort;
        $author->status = $this->status;
        if(!$author->save()) {
            $this->addError('author_save', '保存作者信息异常');
            return false;
        }

        return true;
    }

    public function articleList() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = WisdomLibrary::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'name', $this->name]);
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }
}