<?php


namespace api\modules\admin\forms;

use common\models\api\MemberAuth;
use Yii;
use yii\base\Model;

class MemberAuthForm extends \common\models\base\BaseModel
{
    /**
     * 列表场景
     */
    const SCENARIO_LIST = 'scenario_list';

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var string open_id
     */
    public $open_id = '';

    /**
     * @var string 微信昵称
     */
    public $nickname = '';

    /**
     * @var string 头像
     */
    public $head_portrait = '';

    /**
     * @var int 状态(-1:已删除,0:禁用,1:正常)
     */
    public $status = 1;

    /**
     * @var int created_at
     */
    public $s_time = '';

    /**
     * @var int updated_at
     */
    public $e_time = '';

    public function rules()
    {
        return [
            //列表场景
            [['nickname','s_time','e_time'],'string','on'=>[static::SCENARIO_LIST]],
            [['page','page_size'],'integer', 'on'=>[static::SCENARIO_LIST]]
        ];
    }

    /**
     * @return array|false
     * @note:列表数据
     */
    public function list()
    {
        if (!$this->validate())
        {
            return false;
        }

        $MemberAuth = MemberAuth::find();

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $s_time = $this->s_time ? strtotime($this->s_time) : $this->s_time;
        $e_time = $this->e_time ? strtotime($this->e_time) : $this->e_time;
        $offset = ($this->page-1)*$this->page_size;
        $query = $MemberAuth->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['>', 'created_at', $s_time])
            ->andFilterWhere(['<=', 'created_at', $e_time]);
        $res['total'] = $query->count();
        $list = $query->select(['id','member_id','open_id','nickname','gender','avatar_url','status','created_at','updated_at','province','city'])
            ->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $res['list'] = $list;
        return $res;
    }
}