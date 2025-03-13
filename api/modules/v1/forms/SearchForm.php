<?php


namespace api\modules\v1\forms;


use common\models\common\ClassRoom;
use common\models\common\Legal;
use common\models\common\Legal1;
use common\models\common\OpenInfo;
use common\models\common\WisdomLibrary;
use yii\base\Model;

class SearchForm extends Model
{
    /**
     * 网商课堂、智库、法律汇编，综合搜索
     */
    const SCENARIO_SEARCH = 'search';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 搜索内容
     */
    public $word;

    /**
     * @var int 0-综合 1-网商课堂 2-法律汇编 3-网商智库
     */
    public $type;

    public function rules()
    {
        return [
            //公共参数
            [['page', 'page_size'], 'integer'],

            //网商智库列表
            [['word','type'],'required','on'=>[static::SCENARIO_SEARCH]],
        ];
    }

    /**
     * 网商搜索
     * @return array|false
     */
    public function search() {
        if (!$this->validate()) {
            return false;
        }

        $classRoom = [];
        $legal = [];
        $wisdomLibrary = [];
        $openInfo = [];

        switch ($this->type) {
            case 1:
                $classRoom = $this->getClassRoom($this->word);
                break;
            case 2:
                $legal = $this->getLegal($this->word);
                break;
            case 3:
                $wisdomLibrary = $this->getWisdomLibrary($this->word);
                break;
            case 4:
                $openInfo = $this->getOpenInfo($this->word);
                break;
            default:
                $classRoom = $this->getClassRoom($this->word);
                $legal = $this->getLegal($this->word);
                $wisdomLibrary = $this->getWisdomLibrary($this->word);
                $openInfo = $this->getOpenInfo($this->word);
        }


        return [
            'class_room' => $classRoom,
            'legal' => $legal,
            'wisdom_library' => $wisdomLibrary,
            'open_info' => $openInfo,
        ];
    }

    /**
     * 网商搜索
     * @return array|false
     */
    public function search1() {
        if (!$this->validate()) {
            return false;
        }

        $classRoom = [];
        $legal = [];
        $wisdomLibrary = [];
        $openInfo = [];

        switch ($this->type) {
            case 1:
                $classRoom = $this->getClassRoom($this->word);
                break;
            case 2:
                $legal = $this->getLegal1($this->word);
                break;
            case 3:
                $wisdomLibrary = $this->getWisdomLibrary($this->word);
                break;
            case 4:
                $openInfo = $this->getOpenInfo($this->word);
                break;
            default:
                $classRoom = $this->getClassRoom($this->word);
                $legal = $this->getLegal1($this->word);
                $wisdomLibrary = $this->getWisdomLibrary($this->word);
                $openInfo = $this->getOpenInfo($this->word);
        }


        return [
            'class_room' => $classRoom,
            'legal' => $legal,
            'wisdom_library' => $wisdomLibrary,
            'open_info' => $openInfo,
        ];
    }

    /**
     * 网商课堂搜索
     * @param $word
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getClassRoom($word) {
        return ClassRoom::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$word])
            ->asArray()
            ->all();
    }

    /**
     * 法律汇编搜索
     * @param $word
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLegal1($word) {
        return Legal1::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$word])
            ->asArray()
            ->all();
    }

    /**
     * 法律汇编搜索
     * @param $word
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLegal($word) {
        return Legal::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$word])
            ->asArray()
            ->all();
    }

    /**
     * 网商智库搜索
     * @param $word
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getWisdomLibrary($word) {
        return WisdomLibrary::find()
            ->where(['status' => 1])
            ->andFilterWhere(['or',['like','name',$word], ['like','field',$word]])
            ->asArray()
            ->all();
    }

    /**
     * 公开信息搜索
     * @param $word
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOpenInfo($word) {
        return OpenInfo::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$word])
            ->asArray()
            ->all();
    }
}
