# Hitable behavior for Yii2

### Installation
Run command

```
composer require usualdesigner/yii2-hitable-behavior "dev-master"
```

or add

```
"usualdesigner/yii2-hitable-behavior": "dev-master"
```

to the require section of your `composer.json` file.

### Main migration

```php
$this->createTable('{{%hits}}', [
    'hit_id' => $this->primaryKey(),
    'user_agent' => $this->string()->notNull(),
    'ip' => $this->string()->notNull(),
    'target_group' => $this->string()->notNull(),
    'target_pk' => $this->string()->notNull(),
    'created_at' => $this->integer()->notNull(),
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
                'class' => \usualdesigner\yii2\behavior\HitableBehavior::class(),
                'attribute' => 'hits_count',    //attribute which should contain uniquie hits value
                'group' => false,               //group name of the model (class name by default)
                'delay' => 60 * 60,             //register the same visitor every hour
                'table_name' => '{{%hits}}'     //table with hits data
                'db' => Yii::$app->db,          //cross DB connection (optional)
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

### Tests
[How to run the tests](tests/README.md)
