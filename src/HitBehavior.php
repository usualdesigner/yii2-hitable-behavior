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

    public static $table_name = '{{%hits}}';

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
        $hit = new \HitModel();
        $hit->save();
    }
}
