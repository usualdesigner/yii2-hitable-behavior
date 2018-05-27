<?php
namespace Testing\models;

use \usualdesigner\yii2\behavior\HitableBehavior;

class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post_testing}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'hit' => [
                'class' => HitableBehavior::className(),
                'attribute' => 'hits_count',    //attribute which should contain uniquie hits value
                'group' => false,               //group name of the model (class name by default)
                'delay' => 60 * 60,             //register the same visitor every hour
                'table_name' => '{{%hits_testing}}'     //table with hits data
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\Testing\models\Post'],
        ];
    }
}
