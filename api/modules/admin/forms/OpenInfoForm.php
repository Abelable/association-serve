<?php

namespace api\modules\admin\forms;

use common\models\common\OpenInfo;
use yii\base\Model;

class OpenInfoForm extends Model
{
    /**
     * 文章新增、编辑、删除
     */
    const SCENARIO_OPEN_INFO_SAVE = 'open_info_save';

    /**
     * 文章列表
     */
    const SCENARIO_OPEN_INFO_LIST = 'open_info_list';

    /**
     * @var string 标题
     */
    public $title;

    /**
     * @var string 封面
     */
    public $cover;

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
            // 公共参数
            [['page','page_size'],'integer'],

            // 公开信息添加、编辑、删除
            [['title', 'cover', 'content', 'sort'], 'required', 'on' => [self::SCENARIO_OPEN_INFO_SAVE]],
            [['id','status','sort'], 'integer', 'on' => [self::SCENARIO_OPEN_INFO_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_OPEN_INFO_SAVE]],

            // 公开信息列表
            [['title'], 'string', 'on' => [self::SCENARIO_OPEN_INFO_LIST]],
        ];
    }


    /**
     * 公开信息管理，添加、编辑、新增
     * @return bool
     */
    public function openInfoSave() {
        if(!$this->validate()) {
            return false;
        }
        $info = OpenInfo::findOne(['id' => $this->id]);
        if(!$info) {
            $info = new OpenInfo();
        }
        $info->title = $this->title;
        $info->cover = $this->cover;
        $info->content = $this->content;
        $info->sort = $this->sort;
        $info->status = $this->status;
        if(!$info->save()) {
            $this->addError('open_info_save', '保存作者信息异常');
            return false;
        }

        return true;
    }

    public function openInfoList() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = OpenInfo::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title]);
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