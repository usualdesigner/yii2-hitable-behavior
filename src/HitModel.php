<?php

namespace usualdesigner\behavior;

class HitModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return \usualdesigner\behavior\HitBehavior::$table_name;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
            'session_attribute' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => ['session'],
                'value' => function () {
                    return '111';
                }
            ],
            'ip_attribute' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => ['ip'],
                'value' => function () {
                    return '222';
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['session_id', 'integer'],
            ['ip', 'string', 'max' => 255],
            ['target_group', 'string', 'max' => 255],
            ['target_pk', 'integer'],
            ['created_at', 'integer'],
        ];
    }
}