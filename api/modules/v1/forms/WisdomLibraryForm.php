<?php


namespace api\modules\v1\forms;


use common\models\common\WisdomLibrary;
use common\models\common\WisdomLibraryCollect;
use common\models\common\WisdomLibraryLike;
use yii\base\Model;

class WisdomLibraryForm extends Model
{
    /**
     * 网商智库列表
     */
    const SCENARIO_WISDOM_LIBRARY_LIST = 'wisdom_library_list';

    /**
     * 网商智库详情
     */
    const SCENARIO_WISDOM_LIBRARY_DETAIL = 'wisdom_library_detail';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 名称
     */
    public $name;

    /**
     * @var string 领域
     */
    public $field;

    /**
     * @var int id
     */
    public $id;

    public function rules()
    {
        return [
            //公共参数
            [['page', 'page_size'], 'integer'],

            //网商智库列表
            [['name','field'],'string','on'=>[static::SCENARIO_WISDOM_LIBRARY_LIST]],

            //网商智库详情
            [['id'],'required','on'=>[static::SCENARIO_WISDOM_LIBRARY_DETAIL]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = WisdomLibrary::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','name',$this->name])
            ->andFilterWhere(['like','field',$this->field]);
        $offset = ($this->page - 1) * $this->page_size;
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['list'] = [];
        $res['total'] = $query->count();
        $res['list'] = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $res;
    }

    public function detail() {
        WisdomLibrary::updateAllCounters(['views' => 1], ['id' => $this->id]);
        $item = WisdomLibrary::find()
            ->where(['status' => 1, 'id' => $this->id])
            ->one();

        $res = $item ? $item->toArray() : [];
        $userId = \Yii::$app->user->id;

        $like = WisdomLibraryLike::find()->where(['wisdom_library_id' => $this->id, 'user_id' => $userId, 'is_like'=> 1])->one();
        $res['is_like'] = $like ? 1 : 0;
        $collect = WisdomLibraryCollect::find()->where(['wisdom_library_id' => $this->id, 'user_id'=> $userId, 'is_collect'=> 1])->one();
        $res['is_collect'] = $collect ? 1 : 0;


        return $res;
    }
}
