### Main migration

```php
$this->createTable('{{%hits}}', [
    'hit_id' => Schema::TYPE_PK,
    'user_agent' => Schema::TYPE_STRING . ' NOT NULL',
    'ip' => Schema::TYPE_STRING . ' NOT NULL',
    'target_group' => Schema::TYPE_STRING . ' NOT NULL',
    'target_pk' => Schema::TYPE_INTEGER . ' NOT NULL',
    'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
]);
```

### Configuring

```php
<?php

class Post extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'hit' => [
                'class' => \usualdesigner\behavior\HitCountableBehavior::className(),
                'attribute' => 'hits_count',    //attribute which should contain uniquie hits value
                'group' => false,               //group name of the model (class name by default)
                'delay' => 60 * 60,             //register the same visitor every hour
                'table_name' => '{{%hits}}'     //table with hits data
            ]
        ];
    }
}
```

### Basic usage

```php
$post = Post::findOne(1);

//increase counter
$post->getBehavior('hit')->touch();


//get hits count
echo $post->getBehavior('hit')->getHitsCount();
```
