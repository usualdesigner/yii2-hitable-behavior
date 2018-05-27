<?php
namespace Testing\models;
class Company extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'hit' => [
                'class' => \usualdesigner\yii2\behavior\HitableBehavior::className(),
                'attribute' => 'hits_count',    //attribute which should contain uniquie hits value
                'group' => false,               //group name of the model (class name by default)
                'delay' => 60 * 60,             //register the same visitor every hour
                'table_name' => '{{%hits}}'     //table with hits data
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
            ['name', 'unique', 'targetClass' => '\Testing\models\Company'],
        ];
    }
}
