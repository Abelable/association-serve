<?php


namespace api\modules\admin\forms;


use common\models\common\Legal;
use common\models\common\Legal1;
use common\models\common\LegalCategory;
use common\models\common\LegalCategory1;
use yii\base\Model;

class LegalForm extends Model
{
    /**
     * 法律汇编分类保存
     */
    const SCENARIO_LEGAL_CATEGORY_SAVE = 'legal_category_save';

    /**
     * 法律汇编分类列表
     */
    const SCENARIO_LEGAL_CATEGORY_LIST = 'legal_category_list';

    /**
     * 法律汇编分类保存
     */
    const SCENARIO_LEGAL_SAVE = 'legal_save';

    /**
     * 法律汇编列表
     */
    const SCENARIO_LEGAL_LIST = 'legal_list';

    /**
     * @var string 分类名称
     */
    public $name;

    /**
     * @var string 配图
     */
    public $image;

    /**
     * @var string 描述
     */
    public $description;

    /**
     * @var int 排序
     */
    public $sort = 0;

    /**
     * @var int id
     */
    public $id;
    public $pid;

    /**
     * @var int 状态
     */
    public $status = 1;

    /**
     * @var string 文章标题
     */
    public $title;

    /**
     * @var string 文章内容
     */
    public $content;

    /**
     * @var int 分类id
     */
    public $category_id;
    public $sub_category_id;

    /**
     * @var string 生效时间
     */
    public $effective_time = 0;

    /**
     * @var array 排序数组
     */
    public $orderBy = [];

    /**
     * @var string 颁布时间
     */
    public $promulgation_time = 0;

    public $page = 0;

    public $page_size = 15;

    public function rules()
    {
        return [
            //公共参数
            [['page','page_size'],'integer', 'on' => [self::SCENARIO_LEGAL_CATEGORY_LIST,self::SCENARIO_LEGAL_LIST]],

            //法律汇编分类保存
            [['name', 'image', 'description'], 'required', 'on' => [self::SCENARIO_LEGAL_CATEGORY_SAVE]],
            [['name', 'image', 'description', 'pid'], 'string', 'on' => [self::SCENARIO_LEGAL_CATEGORY_SAVE]],
            [['id','status','sort'], 'integer', 'on' => [self::SCENARIO_LEGAL_CATEGORY_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_LEGAL_CATEGORY_SAVE]],

            //法律汇编分类列表
            [['name', 'pid'], 'string', 'on' => [self::SCENARIO_LEGAL_CATEGORY_LIST]],

            //法律汇编保存
            [['title', 'image', 'content'], 'required', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            [['title', 'image', 'content'], 'string', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            [['id','status','sort','effective_time', 'promulgation_time', 'category_id', 'sub_category_id'], 'integer', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_LEGAL_SAVE]],
            ['orderBy', 'filter','filter' => function ($value) {
                return $value;
            }],

            //法律汇编列表
            [['category_id', 'sub_category_id'], 'integer','on' => [self::SCENARIO_LEGAL_LIST]],
            [['title'], 'string','on' => [self::SCENARIO_LEGAL_LIST]],
        ];
    }


    /**
     * 法律汇编分类，添加、编辑、新增
     * @return bool
     */
    public function categorySave() {
        if(!$this->validate()) {
            return false;
        }
        $category = LegalCategory::findOne(['id' => $this->id]);
        if(!$category) {
            $category = new LegalCategory();
        }
        $category->name = $this->name;
        $category->image = $this->image;
        $category->description = $this->description;
        $category->sort = $this->sort ? $this->sort : 0;;
        $category->status = $this->status;
        $category->pid = $this->pid ? $this->pid : 0;
        if(!$category->save()) {
            $this->addError('category_save', '保存法律汇编分类异常');
            return false;
        }

        return true;
    }

    public function categoryList(){
        if(!$this->validate()) {
            return false;
        }

        $offset = ($this->page - 1) * $this->page_size;
        $query = LegalCategory::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'name', $this->name]);
        if ($this->pid || $this->pid === '0') {
            $query->andWhere(['=', 'pid', $this->pid]);
        }

        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $res['list'] = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();
        foreach ($res['list'] as $k => $v) {
            if ($v['pid']) {
                $res['list'][$k]['pname'] = LegalCategory::findOne(['id' => $v['pid']])->name;
            }else{
                $res['list'][$k]['pname'] = '';
            }
        }

        return $res;
    }

    /**
     * 法律汇编保存
     * @return bool
     */
    public function legalSave() {
        if(!$this->validate()) {
            return false;
        }
        /*$category = LegalCategory::findOne(['id' => $this->category_id]);
        if(!$category) {
            $this->addError('legal_save', '分类不存在');
            return false;
        }*/

        $model = Legal::find()->where(['id' => $this->id])->one();
        if(empty($model)) {
            $model = new  Legal();
        }
        $model->title = $this->title;
        $model->image = $this->image;
        $model->content = $this->content;
        $model->category_id = $this->category_id ?? 0;
        $model->virtual_views = mt_rand(100,1000);
        $model->virtual_likes = mt_rand(100,1000);
        $model->sort = $this->sort;
        $model->effective_time = $this->effective_time;
        $model->promulgation_time = $this->promulgation_time;
        $model->status = $this->status;
        if(!$model->save()) {
            $this->addError('category_save', '保存法律汇编异常');
            return false;
        }

        return true;
    }

    public function legalList() {
        if (!$this->validate()){
            return false;
        }

        //排序验证
        $orderByKeys = ['effective_time','promulgation_time'];
        $orderByValues = ['asc','desc'];
        foreach ($this->orderBy as $key => $val) {
            if(!in_array($key,$orderByKeys)) {
                $this->addError('legal_list', '排序参数不存在');
                return false;
            }
            if(!in_array($val,$orderByValues)) {
                $this->addError('legal_list', '排序方式不存在');
                return false;
            }
            $this->orderBy[$key] = ($val == 'asc') ?SORT_ASC : SORT_DESC;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = Legal::find()
            ->with('category')
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['category_id' => $this->category_id]);
        if(!empty($this->orderBy)) {
            $query = $query->orderBy($this->orderBy);
        }else {
            $query = $query->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC]);
        }
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }

    /**
     * 法律汇编保存
     * @return bool
     */
    public function legal1Save() {
        if(!$this->validate()) {
            return false;
        }
        /*$category = LegalCategory::findOne(['id' => $this->category_id]);
        if(!$category) {
            $this->addError('legal_save', '分类不存在');
            return false;
        }*/

        $model = Legal1::find()->where(['id' => $this->id])->one();
        if(empty($model)) {
            $model = new  Legal1();
        }
        $model->title = $this->title;
        $model->image = $this->image;
        $model->content = $this->content;
        $model->category_id = $this->category_id ?? 0;
        $model->virtual_views = mt_rand(100,1000);
        $model->virtual_likes = mt_rand(100,1000);
        $model->sort = $this->sort;
        $model->effective_time = $this->effective_time;
        $model->promulgation_time = $this->promulgation_time;
        $model->status = $this->status;
        $model->effective_from = $_POST['effective_from']??'';
        if(!$model->save()) {
            $this->addError('category_save', '保存法律汇编异常');
            return false;
        }

        return true;
    }

    public function legal1List() {
        if (!$this->validate()){
            return false;
        }

        //排序验证
        $orderByKeys = ['effective_time','promulgation_time'];
        $orderByValues = ['asc','desc'];
        foreach ($this->orderBy as $key => $val) {
            if(!in_array($key,$orderByKeys)) {
                $this->addError('legal_list', '排序参数不存在');
                return false;
            }
            if(!in_array($val,$orderByValues)) {
                $this->addError('legal_list', '排序方式不存在');
                return false;
            }
            $this->orderBy[$key] = ($val == 'asc') ?SORT_ASC : SORT_DESC;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = Legal1::find()
            ->with('category')
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['category_id' => $this->category_id]);
        if ($this->sub_category_id) {
            $query = $query->andWhere(['category_id' => $this->sub_category_id]);
        }
        if(!empty($this->orderBy)) {
            $query = $query->orderBy($this->orderBy);
        }else {
            $query = $query->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC]);
        }
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }

    /**
     * 法律汇编分类，添加、编辑、新增
     * @return bool
     */
    public function category1Save() {
        if(!$this->validate()) {
            return false;
        }
        $category = LegalCategory1::findOne(['id' => $this->id]);
        if(!$category) {
            $category = new LegalCategory1();
        }
        $category->name = $this->name;
        $category->image = $this->image;
        $category->description = $this->description;
        $category->sort = $this->sort ? $this->sort : 0;;
        $category->status = $this->status;
        $category->pid = $this->pid ? $this->pid : 0;
        if(!$category->save()) {
            $this->addError('category_save', '保存法律汇编分类异常');
            return false;
        }

        return true;
    }

    public function category1List(){
        if(!$this->validate()) {
            return false;
        }

        $offset = ($this->page - 1) * $this->page_size;
        $query = LegalCategory1::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'name', $this->name]);
        if ($this->pid || $this->pid === '0') {
            $query->andWhere(['=', 'pid', $this->pid]);
        }

        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $res['list'] = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();
        foreach ($res['list'] as $k => $v) {
            if ($v['pid']) {
                $res['list'][$k]['pname'] = LegalCategory1::findOne(['id' => $v['pid']])->name;
            }else{
                $res['list'][$k]['pname'] = '';
            }
        }

        return $res;
    }
}
