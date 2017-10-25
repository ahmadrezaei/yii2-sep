<?php

namespace ahmadrezaei\yii\sep\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%sep_log}}".
 *
 * @property integer $id
 * @property string $ResNum
 * @property string $RefNum
 * @property string $CardNumber
 * @property string $data
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class SepLog extends ActiveRecord
{
    const STATUS_SUCCESS = 10;
    const STATUS_PENDING = 5;
    const STATUS_UNSUCCESS = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sep_log}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_SUCCESS, self::STATUS_UNSUCCESS]],
            [['data'], 'string'],
            [['ResNum', 'RefNum'], 'string', 'max' => 255],
            [['CardNumber'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ResNum' => Yii::t('app', 'Res Num'),
            'RefNum' => Yii::t('app', 'Ref Num'),
            'CardNumber' => Yii::t('app', 'Card Number'),
            'data' => Yii::t('app', 'Data'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        $count = self::find()->count();
        return ($count < 1);
    }
}
