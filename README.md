```php
$this->createTable('{{%hits}}', [
    'hit_id' => Schema::TYPE_PK,
    'session_id' => Schema::TYPE_INTEGER . ' NOT NULL',
    'ip' => Schema::TYPE_STRING . ' NOT NULL',
    'target_group' => Schema::TYPE_STRING . ' NOT NULL',
    'target_pk' => Schema::TYPE_INTEGER . ' NOT NULL',
    'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
]);
```