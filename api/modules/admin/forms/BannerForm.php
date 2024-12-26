<?php


namespace api\modules\admin\forms;


use common\models\common\Banner;
use yii\base\Model;

class BannerForm extends Model
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
     * 详情场景
     */
    const SCENARIO_DETAIL = 'scenario_detail';


    /**
     * 状态更新场景
     */
    const SCENARIO_ACTIVE = 'scenario_active';

    /**
     * 获取表单详情信息
     */
    const SCENARIO_INFO = 'scenario_info';

    /**
     * 删除表单场景
     */
    const SCENARIO_DEL = 'scenario_del';

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var int bannerID
     */
    public $id = 0;

    /**
     * @var int 活动状态
     */
    public $status = 0;

    /**
     * @var string banner标题
     */
    public $title = '';

    /**
     * @var string 图片url
     */
    public $img = '';

    /**
     * @var int 是否展示 0不展示 1展示
     */
    public $is_show = '';

    /**
     * @var int 排序
     */
    public $sort = 0;

    /**
     * @var string 开始时间
     */
    public $s_time = '';

    /**
     * @var string 结束时间
     */
    public $e_time = '';

    /**
     * @var string 跳转url
     */
    public $redirect_url = '';

    /**
     * @var int 跳转类型；[0-不跳转 1-新闻 2-h5]
     */
    public $link_type = 0;

    /**
     * @var int 新闻id
     */
    public $article_id = 0;

    public function rules()
    {
        return [
            // 列表数据
            [['page','page_size','is_show'],'integer','on'=>[static::SCENARIO_LIST]],
            [['title'],'string','on'=>[static::SCENARIO_LIST]],

            // 创建数据
            [['sort','is_show','link_type','article_id'],'integer','on'=>[static::SCENARIO_CREATE]],
            [['title','img','s_time','e_time','redirect_url'],'string','on'=>[static::SCENARIO_CREATE]],

            // 更新数据
            [['id','sort','is_show','link_type','article_id'],'integer','on'=>[static::SCENARIO_UPDATE]],
            [['title','img','s_time','e_time','redirect_url'],'string','on'=>[static::SCENARIO_UPDATE]],

            //删除数据
            [['id'],'integer','on'=>[static::SCENARIO_DEL,static::SCENARIO_DETAIL]],
        ];
    }

    /**
     * @return array|false
     * @note:列表数据
     */
    public function formList()
    {
        if (!$this->validate())
        {
            return false;
        }

        $Banner = Banner::find();

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = $Banner
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['is_show' => $this->is_show]);
        $res['total'] = $query->count();
        $list = $query->select(['id','title','img','sort','s_time','e_time','link_type','article_id','redirect_url','is_show','status','created_at'])
            ->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $res['list'] = $list;
        return $res;
    }

    /**
     * @return bool
     * @note:创建表单
     */
    public function formCreate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $this->s_time = $this->s_time ? ($this->s_time / 1000) : '';
        $this->e_time = $this->e_time ? ($this->e_time / 1000) : '';

        $Banner = new Banner();
        $Banner->title = $this->title;
        $Banner->img = $this->img;
        $Banner->sort = $this->sort;
        $Banner->link_type = $this->link_type;
        $Banner->article_id = $this->article_id;
        $Banner->s_time = $this->s_time;
        $Banner->e_time = $this->e_time;
        $Banner->redirect_url = $this->redirect_url;
        $Banner->is_show = $this->is_show;
        if (!$Banner->save()){
            \Yii::error("创建表单失败 res:".json_encode($Banner->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据添加失败');
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @note:创建/修改报表列表数据
     */
    public function formUpdate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$Banner = Banner::findOne(['id' => $this->id])){
            $this->addError("BannerFrom",'数据不存在');
            return false;
        }

        $this->s_time = $this->s_time ? ($this->s_time / 1000) : '';
        $this->e_time = $this->e_time ? ($this->e_time / 1000) : '';

        $Banner->title = $this->title;
        $Banner->img = $this->img;
        $Banner->sort = $this->sort;
        $Banner->link_type = $this->link_type;
        $Banner->article_id = $this->article_id;
        $Banner->s_time = $this->s_time;
        $Banner->e_time = $this->e_time;
        $Banner->redirect_url = $this->redirect_url;
        $Banner->is_show = $this->is_show;
        if (!$Banner->save()){
            \Yii::error("更新数据失败 res:".json_encode($Banner->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("BannerFrom",'更新数据失败');
            return false;
        }
        return true;
    }

    /**
     * @return Banner|false|null
     * @note:详情
     */
    public function detail()
    {
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        if (!$Banner = Banner::findOne(['id'=>$this->id])){
            $this->addError("BannerFrom",'数据不存在');
            return false;
        }

        $Banner->s_time = $Banner->s_time * 1000;
        $Banner->e_time = $Banner->e_time * 1000;
        return $Banner;
    }

    /**
     * 删除banner
     */
    public function formDel(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$Banner = Banner::findOne(['id'=>$this->id])){
            $this->addError("BannerFrom",'数据不存在');
            return false;
        }

        $Transaction = \Yii::$app->db->beginTransaction();

        $Banner->status = $Banner::STATUS_DELETE;
        if (!$Banner->save()){
            $Transaction->rollBack();
            \Yii::error("删除数据失败 res:".json_encode($Banner->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("BannerFrom",'删除数据失败');
            return false;
        }
        $Transaction->commit();
        return true;
    }
}