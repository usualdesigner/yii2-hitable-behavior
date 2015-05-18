<?php
/**
 * @link https://github.com/usualdesigner/yii2-behaviors-hit
 * @copyright Copyright (c) 2015 Aleksey Bernackiy
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace usualdesigner\behavior;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * HItBehavior
 *
 * @property ActiveRecord $owner
 *
 * @author Aleksey Bernackiy <usualdesigner@gmail.com>
 */
class HitBehavior extends Behavior
{
    public $attribute = 'hit_count';
    public $group;

    public $table_name = '{{%hits}}';

    public function init()
    {
        if (!$this->attribute) {
            throw new IntegrityException('Attribute is not defined');
        }

        if (!$this->group) {
            throw new IntegrityException('Group is not defined');
        }

        parent::init();
    }

    public function touch()
    {
        $this->owner->getDb()
            ->createCommand()
            ->insert($this->table_name, [
                'session_id' => Yii::$app->getSession()->getId(),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'target_group' => $this->group,
                'target_pk' => $this->owner->primaryKey,
                'created_at' => new Expression('NOW()'),
            ])
            ->execute();
    }
}
