<?php

namespace api\modules\admin\forms;

use common\models\common\Album;
use yii\base\Model;

class AlbumForm extends Model
{
    /**
     * 相册新增、编辑、删除
     */
    const SCENARIO_ALBUM_SAVE = 'album_save';

    /**
     * 相册列表
     */
    const SCENARIO_ALBUM_LIST = 'album_list';

    /**
     * @var string 标题
     */
    public $title;

    /**
     * @var int 地区
     */
    public $city_id;

    /**
     * @var string 照片
     */
    public $photo_list;

    /**
     * @var int id
     */
    public $id;

    /**
     * @var int 状态
     */
    public $status = 1;

    public $page = 0;

    public $page_size = 10;

    public function rules()
    {
        return [
            // 公共参数
            [['page','page_size'],'integer'],

            // 公开信息添加、编辑、删除
            [['title', 'city_id', 'photo_list'], 'required', 'on' => [self::SCENARIO_ALBUM_SAVE]],
            [['id','status'], 'integer', 'on' => [self::SCENARIO_ALBUM_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_ALBUM_SAVE]],

            // 公开信息列表
            [['title'], 'string', 'on' => [self::SCENARIO_ALBUM_LIST]],
            [['city_id'], 'integer', 'on' => [self::SCENARIO_ALBUM_LIST]],
        ];
    }


    /**
     * 公开信息管理，添加、编辑、新增
     * @return bool
     */
    public function save() {
        if(!$this->validate()) {
            return false;
        }
        $info = Album::findOne(['id' => $this->id]);
        if(!$info) {
            $info = new Album();
        }
        $info->title = $this->title;
        $info->city_id = $this->city_id;
        $info->photo_list = $this->photo_list;
        $info->status = $this->status;
        if(!$info->save()) {
            $this->addError('album_save', '保存相册信息异常');
            return false;
        }
        return true;
    }

    public function list() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = Album::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andWhere(['city_id' => $this->city_id]);
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        $res['total'] = $query->count();
        $res['list'] = $list;

        return $res;
    }
}