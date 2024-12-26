<?php

namespace api\modules\admin\forms;

use common\models\api\admin\MiniWxUser;
use yii\base\Model;
use common\helpers\RegularHelper;

class MiniWxUserForm extends Model
{
    /**
     * 列表数据场景
     */
    const SCENARIO_LIST = 'scenario_list';


    /**
     * 更场景
     */
    const SCENARIO_CREATE = 'scenario_create';

    /**
     * 更新场景
     */
    const SCENARIO_UPDATE = 'scenario_update';


    /**
     * 状态更新场景
     */
    const SCENARIO_ACTIVE = 'scenario_active';

    /**
     * 获取表单详情信息
     */
    const SCENARIO_INFO = 'scenario_info';

    /**
     * @var int 小程序会员的id
     */
    public $id = 0;

    /**
     * @var string 微信昵称
     */
    public $nick_name = '';

    /**
     * @var string 手机号
     */
    public $mobile = '';

    /**
     * @var string 开始时间
     */
    public $s_time = '';

    /**
     * @var string 结束时间
     */
    public $e_time = '';

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var int 活动状态
     */
    public $status = 0;

    public function rules()
    {
        return [
            // 列表数据
            [['page','page_size'],'integer','on'=>[static::SCENARIO_LIST]],
            [['nick_name','mobile','s_time','e_time'],'string','on'=>[static::SCENARIO_LIST]],
        ];
    }

    /**
     * @return array|false
     * @author 陈一华
     * @note:列表数据
     */
    public function formList()
    {
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $MiniWxUser = MiniWxUser::find();

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $s_time = $this->s_time ? strtotime($this->s_time) : $this->s_time;
        $e_time = $this->e_time ? strtotime($this->e_time) : $this->e_time;
        $offset = ($this->page-1)*$this->page_size;
        $query = $MiniWxUser->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['>', 'created_at', $s_time])
            ->andFilterWhere(['<=', 'created_at', $e_time]);
        $res['total'] = $query->count();
        $list = $query->select(['id','avatar','nick_name','mobile','status','created_at','updated_at'])
            ->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $res['list'] = $list;
        return $res;
    }
}