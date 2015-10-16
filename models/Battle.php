<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $level
 * @property integer $rank_id
 * @property boolean $is_win
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property string $start_at
 * @property string $end_at
 * @property string $at
 * @property integer $agent_id
 * @property integer $level_after
 * @property integer $rank_after_id
 * @property integer $rank_exp
 * @property integer $rank_exp_after
 * @property integer $cash
 * @property integer $cash_after
 * @property integer $lobby_id
 * @property string $kill_ratio
 * @property integer $gender_id
 * @property integer $fest_title_id
 * @property integer $my_team_color_hue
 * @property integer $his_team_color_hue
 * @property string $my_team_color_rgb
 * @property string $his_team_color_rgb
 * @property integer $my_point
 * @property integer $my_team_final_point
 * @property integer $his_team_final_point
 * @property string $my_team_final_percent
 * @property string $his_team_final_percent
 * @property boolean $is_knock_out
 * @property integer $my_team_count
 * @property integer $his_team_count
 *
 * @property Agent $agent
 * @property FestTitle $festTitle
 * @property Gender $gender
 * @property Lobby $lobby
 * @property Map $map
 * @property Rank $rank
 * @property Rank $rankAfter
 * @property Rule $rule
 * @property User $user
 * @property Weapon $weapon
 * @property BattleDeathReason[] $battleDeathReasons
 * @property DeathReason[] $reasons
 * @property BattleImage[] $battleImages
 */
class Battle extends \yii\db\ActiveRecord
{
    public static function find()
    {
        $query = new query\BattleQuery(get_called_class());
        $query->orderBy('{{battle}}.[[id]] DESC');
        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle';
    }

    public function init()
    {
        parent::init();
        $this->on(\yii\db\ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'setKillRatio']);
        $this->on(\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'setKillRatio']);

        $this->on(\yii\db\ActiveRecord::EVENT_AFTER_INSERT, [$this, 'updateUserStat']);
        $this->on(\yii\db\ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'updateUserStat']);
        $this->on(\yii\db\ActiveRecord::EVENT_AFTER_DELETE, [$this, 'updateUserStat']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'at'], 'required'],
            [['user_id', 'rule_id', 'map_id', 'weapon_id', 'level', 'rank_id'], 'integer'],
            [['rank_in_team', 'kill', 'death', 'agent_id'], 'integer'],
            [['level_after', 'rank_after_id', 'rank_exp', 'rank_exp_after', 'cash', 'cash_after'], 'integer'],
            [['lobby_id', 'gender_id', 'fest_title_id', 'my_team_color_hue', 'his_team_color_hue'], 'integer'],
            [['my_point', 'my_team_final_point', 'his_team_final_point', 'my_team_count', 'his_team_count'], 'integer'],
            [['is_win', 'is_knock_out'], 'boolean'],
            [['start_at', 'end_at', 'at'], 'safe'],
            [['kill_ratio', 'my_team_final_percent', 'his_team_final_percent'], 'number'],
            [['my_team_color_rgb', 'his_team_color_rgb'], 'string', 'min' => 6, 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'level' => 'Level',
            'rank_id' => 'Rank ID',
            'is_win' => 'Is Win',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'at' => 'At',
            'agent_id' => 'Agent ID',
            'level_after' => 'Level After',
            'rank_after_id' => 'Rank After ID',
            'rank_exp' => 'Rank Exp',
            'rank_exp_after' => 'Rank Exp After',
            'cash' => 'Cash',
            'cash_after' => 'Cash After',
            'lobby_id' => 'Lobby ID',
            'kill_ratio' => 'Kill Ratio',
            'gender_id' => 'Gender ID',
            'fest_title_id' => 'Fest Title ID',
            'my_team_color_hue' => 'My Team Color Hue',
            'his_team_color_hue' => 'His Team Color Hue',
            'my_team_color_rgb' => 'My Team Color Rgb',
            'his_team_color_rgb' => 'His Team Color Rgb',
            'my_point' => 'My Point',
            'my_team_final_point' => 'My Team Final Point',
            'his_team_final_point' => 'His Team Final Point',
            'my_team_final_percent' => 'My Team Final Percent',
            'his_team_final_percent' => 'His Team Final Percent',
            'is_knock_out' => 'Is Knock Out',
            'my_team_count' => 'My Team Count',
            'his_team_count' => 'His Team Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitle()
    {
        return $this->hasOne(FestTitle::className(), ['id' => 'fest_title_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::className(), ['id' => 'gender_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLobby()
    {
        return $this->hasOne(Lobby::className(), ['id' => 'lobby_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankAfter()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_after_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleDeathReasons()
    {
        return $this->hasMany(BattleDeathReason::className(), ['battle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReasons()
    {
        return $this
            ->hasMany(DeathReason::className(), ['id' => 'reason_id'])
            ->viaTable('battle_death_reason', ['battle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::className(), ['battle_id' => 'id']);
    }

    public function getBattleImageJudge()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_JUDGE]);
    }

    public function getBattleImageResult()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_RESULT]);
    }

    public function getIsNawabari()
    {
        return $this->getIsThisGameMode('regular');
    }

    public function getIsGachi()
    {
        return $this->getIsThisGameMode('gachi');
    }

    private function getIsThisGameMode($key)
    {
        if ($this->rule_id === null) {
            return false;
        }
        if (!$rule = $this->getRule()->with('mode')->one()) {
            return false;
        }
        return $rule->mode && $rule->mode->key === $key;
    }

    public function getIsMeaningful()
    {
        $props = [
            'rule_id', 'map_id', 'weapon_id', 'is_win', 'rank_in_team', 'kill', 'death',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null) {
                return true;
            }
        }
        return true;
    }

    public function getPeriodId()
    {
        // 開始時間があれば開始時間から5秒(適当)引いた値を使うを使う。
        // 終了時間があれば終了時間から3分15秒(適当)引いた値を仕方ないので使う。
        // どっちもなければ登録時間から3分30秒(適当)引いた値を仕方ないので使う。
        if ($this->start_at) {
            $time = strtotime($this->start_at) - 5;
        } elseif ($this->end_at) {
            $time = strtotime($this->end_at) - (180 + 15);
        } else {
            $time = strtotime($this->at) - (180 + 30);
        }
        return \app\components\helpers\Battle::calcPeriod($time);
    }

    public function setKillRatio()
    {
        if ($this->kill === null || $this->death === null) {
            $this->kill_ratio = null;
            return;
        }
        if ($this->death == 0) {
            $this->kill_ratio = ($this->kill == 0) ? 1.00 : 99.99;
            return;
        }
        $this->kill_ratio = sprintf('%.2f', $this->kill / $this->death);
    }

    public function updateUserStat()
    {
        if (!$stat = UserStat::findOne(['user_id' => $this->user_id])) {
            $stat = new UserStat();
            $stat->user_id = $this->user_id;
        }
        $stat->createCurrentData();
        $stat->save();
    }
}
