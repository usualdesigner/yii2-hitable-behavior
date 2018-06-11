<?php
/**
 * @link https://github.com/usualdesigner/yii2-behaviors-hit
 * @copyright Copyright (c) 2015 Aleksey Bernackiy
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace usualdesigner\yii2\behavior;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\db\ActiveRecord;
use yii\db\IntegrityException;
use yii\db\Query;

/**
 * HitCountableBehavior
 *
 * @property ActiveRecord $owner
 *
 * @author Aleksey Bernackiy <usualdesigner@gmail.com>
 */
class HitableBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'hits_count';
    /**
     * @var bool
     */
    public $group = false;
    /**
     * @var int
     */
    public $delay = 86400;
    /**
     * @var \yii\base\Component
     */
    public $db = false;
    /**
     * @var string
     */
    public $table_name = '{{%hits}}';

    /**
     * @throws IntegrityException
     */
    public function init()
    {
        if (!$this->attribute) {
            throw new IntegrityException('Attribute is not defined');
        }
        
        if(!$this->db)
            $this->db = $this->owner->getDb();

        parent::init();
    }

    /**
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        if (!($owner instanceof BaseActiveRecord)) {
            throw new \RuntimeException('Owner must be instance of yii\db\BaseActiveRecord');
        }
        parent::attach($owner);

        if (!$this->group) {
            $this->group = get_class($this->owner);
        }
    }

    /**
     *
     */
    public function touch()
    {
        if ((!$this->delay || (date('U') - $this->getLatestVisit() > $this->delay)) && !$this->getIsBot()) {
            if ($this->storeHit()) {
                $this->increaseCounter();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getHitsCount()
    {
        return $this->owner->getAttribute($this->attribute);
    }

    /**
     * @return int
     * @throws \yii\db\Exception
     */
    private function storeHit()
    {
        return $this->db->createCommand()
            ->insert($this->table_name, [
                'user_agent' => Yii::$app->getRequest()->getUserAgent(),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'target_group' => $this->group,
                'target_pk' => $this->owner->primaryKey,
                'created_at' => date('U'),
            ])
            ->execute();
    }

    /**
     * @return bool
     */
    private function increaseCounter()
    {
        return $this->owner->updateCounters([
            $this->attribute => 1,
        ]);
    }

    /**
     * @return bool|string
     */
    private function getLatestVisit()
    {
        return (new Query())
            ->select('created_at')
            ->from($this->table_name)
            ->orderBy('created_at DESC')
            ->andWhere([
                'user_agent' => Yii::$app->getRequest()->getUserAgent(),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'target_group' => $this->group,
                'target_pk' => $this->owner->primaryKey,
            ])
            ->scalar();
    }

    /**
     * @return bool
     */
    protected function getIsBot()
    {
        return false;
    }
}
