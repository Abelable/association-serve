<?php


namespace api\modules\admin\forms;

use common\helpers\ArrayHelper;
use common\models\common\CompanyEvaluation;
use common\models\common\CompanySentence;
use yii\base\Model;

class CompanyEvaluationForm extends Model
{
    /**
     * 保存
     */
    const SCENARIO_COMPANY_EVALUATION_SAVE = 'company_evaluation_save';
    const SCENARIO_COMPANY_SENTENCE_SAVE = 'company_sentence_save';

    /**
     * 列表
     */
    const SCENARIO_COMPANY_EVALUATION_LIST = 'company_evaluation_list';
    const SCENARIO_COMPANY_SENTENCE_LIST = 'company_sentence_list';

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

    public $page = 1;

    public $page_size = 15;

    public $company_name='';

    public $evaluation='';

    public $created_at='';
    public $s_time='';
    public $e_time='';
    public $ps_time='';
    public $pe_time='';
    public $introduction='';

    public function rules()
    {
        return [
            //列表
            [['page','page_size','created_at','promulgation_time','s_time','e_time','ps_time','pe_time'],'integer', 'on' => [self::SCENARIO_COMPANY_EVALUATION_LIST,self::SCENARIO_COMPANY_SENTENCE_LIST]],
            [['title','company_name'],'string', 'on' => [self::SCENARIO_COMPANY_EVALUATION_LIST,self::SCENARIO_COMPANY_SENTENCE_LIST]],

            //保存
            [['image', 'content'], 'required', 'on' => [self::SCENARIO_COMPANY_EVALUATION_SAVE,self::SCENARIO_COMPANY_SENTENCE_SAVE]],
            [['image', 'content','company_name','title','introduction'], 'string', 'on' => [self::SCENARIO_COMPANY_EVALUATION_SAVE,self::SCENARIO_COMPANY_SENTENCE_SAVE]],
            [['id','status','sort','evaluation','promulgation_time'], 'integer', 'on' => [self::SCENARIO_COMPANY_EVALUATION_SAVE,self::SCENARIO_COMPANY_SENTENCE_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_COMPANY_EVALUATION_SAVE,self::SCENARIO_COMPANY_SENTENCE_SAVE]],

            //列表
//            [['name'], 'string', 'on' => [self::SCENARIO_LEGAL_CATEGORY_LIST]],

            //法律汇编保存
            [['title', 'image', 'content'], 'required', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            [['title', 'image', 'content'], 'string', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            [['id','status','sort','effective_time', 'promulgation_time', 'category_id'], 'integer', 'on' => [self::SCENARIO_LEGAL_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_LEGAL_SAVE]],
            ['orderBy', 'filter','filter' => function ($value) {
                return $value;
            }],

            //法律汇编列表
            [['category_id'], 'integer','on' => [self::SCENARIO_LEGAL_LIST]],
            [['title'], 'string','on' => [self::SCENARIO_LEGAL_LIST]],
        ];
    }


    /**
     * 会员信用画像 企业评价，添加、编辑、新增
     * @return bool
     */
    public function companyEvaluationSave() {
        if(!$this->validate()) {
            return false;
        }
        $category = CompanyEvaluation::findOne(['id' => $this->id]);
        if(!$category) {
            $category = new CompanyEvaluation();
        }
        $category->title = $this->title;
        $category->image = $this->image;
        $category->company_name = $this->company_name;
        $category->content = $this->content;
        $category->sort = $this->sort;
        $category->status = $this->status;
        $category->promulgation_time = $this->promulgation_time;
        $category->evaluation = $this->evaluation;
        $category->introduction = $this->introduction;
        if(!$category->save()) {
            $this->addError(self::SCENARIO_COMPANY_EVALUATION_SAVE, '保存异常');
            return false;
        }

        return true;
    }

    public function companyEvaluationList(){
        if(!$this->validate()) {
            return false;
        }
        $offset = ($this->page - 1) * $this->page_size;
        $query = CompanyEvaluation::find()
//            ->where(['status' => 1])
            ->where(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['>=','created_at', $this->s_time])
            ->andFilterWhere(['<','created_at', $this->e_time])
            ->andFilterWhere(['>=','promulgation_time', $this->ps_time])
            ->andFilterWhere(['<','promulgation_time', $this->pe_time])
            ->andFilterWhere(['>','status', -1]);


        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $res['list'] = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();

        $res = ArrayHelper::itemsNumber($res);

        return $res;
    }


    /**
     * 会员信用画像 判决案例，添加、编辑、新增
     * @return bool
     */
    public function companySentenceSave() {
        if(!$this->validate()) {
            return false;
        }
        $category = CompanySentence::findOne(['id' => $this->id]);
        if(!$category) {
            $category = new CompanySentence();
        }
        $category->title = $this->title;
        $category->image = $this->image;
        $category->company_name = $this->company_name;
        $category->content = $this->content;
        $category->sort = $this->sort;
        $category->status = $this->status;
        $category->promulgation_time = $this->promulgation_time;
        $category->introduction = $this->introduction;
        $category->evaluation = $this->evaluation; //1-处罚，2-舆情，3-投诉，4-违法线索，5-投诉举报，6-负面舆情
        if(!$category->save()) {
            $this->addError(self::SCENARIO_COMPANY_EVALUATION_SAVE, '保存异常');
            return false;
        }

        return true;
    }

    public function companySentenceList(){
        if(!$this->validate()) {
            return false;
        }
        $offset = ($this->page - 1) * $this->page_size;
        $query = CompanySentence::find()
//            ->where(['status' => 1])
            ->where(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['>=','created_at', $this->s_time])
            ->andFilterWhere(['<','created_at', $this->e_time])
            ->andFilterWhere(['>=','promulgation_time', $this->ps_time])
            ->andFilterWhere(['<','promulgation_time', $this->pe_time])
            ->andFilterWhere(['>','status', -1]);


        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $res['list'] = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();

        $res = ArrayHelper::itemsNumber($res);

        return $res;
    }
}
