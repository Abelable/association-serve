<?php

namespace api\modules\v1\forms;

use common\models\common\Album;
use yii\base\Model;

class AlbumForm extends Model
{
    /**
     * 相册列表
     */
    const SCENARIO_ALBUM_LIST = 'album_list';

    /**
     * 相册详情
     */
    const SCENARIO_ALBUM_DETAIL = 'album_detail';

    public $page = 1;

    public $page_size = 10;

    /**
     * @var string 开始时间
     */
    public $start_time;

    /**
     * @var string 结束时间
     */
    public $end_time;

    /**
     * @var int 地区
     */
    public $city_id;

    /**
     * @var int id
     */
    public $id;

    public function rules()
    {
        return [
            // 公共参数
            [['page', 'page_size'], 'integer'],

            // 相册列表
            [['start_time', 'end_time'],'string','on'=>[static::SCENARIO_ALBUM_LIST]],
            [['city_id'],'integer','on'=>[static::SCENARIO_ALBUM_LIST]],

            // 相册详情
            [['id'],'required','on'=>[static::SCENARIO_ALBUM_DETAIL]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = Album::find()->where(['status' => 1]);
        if ($this->city_id) {
            $query->andWhere(['city_id' => $this->city_id]);
        }
        if ($this->start_time) {
            $query->andfilterWhere(['<','created_at',$this->start_time]);
            $query->andfilterWhere(['>','created_at',$this->end_time]);
        }
        $offset = ($this->page - 1) * $this->page_size;
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $res['list'] = $query->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $res;
    }

    public function detail() {
        return Album::find()
            ->where(['status' => 1, 'id' => $this->id])
            ->one();
    }
}
