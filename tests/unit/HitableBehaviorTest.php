<?php
namespace Testing;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use Testing\models\Post;

class HitableBehaviorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {

        $migration = new Migration();
        $migration->createTable('{{%hits_testing}}', [
            'hit_id' => Schema::TYPE_PK,
            'user_agent' => $migration->string()->notNull(),
            'ip' => $migration->string()->notNull(),
            'target_group' => $migration->string()->notNull(),
            'target_pk' => $migration->string()->notNull(),
            'created_at' => $migration->integer()->notNull(),
        ]);
        $migration->createTable(Post::tableName(), [
            'id'   => $migration->primaryKey(),
            'name' => $migration->string()->notNull()->unique(),
            'hits_count' => $migration->integer()->defaultValue(0)
        ]);
        $post = new Post();
        $post->name = "Newest Posting";
        $post->save();
    }

    protected function _after()
    {
        $migration = new Migration();
        $migration->dropTable('{{%hits_testing}}');
        $migration->dropTable(Post::tableName());
    }

    public function testCannotAttachBehaviorToAnythingButActiveRecord()
    {
        try {
            $model = new Model();
            $model->attachBehavior('hit', \usualdesigner\yii2\behavior\HitableBehavior::className());
            $this->assertTrue(false, "No Exception thrown");
        } catch (\Exception $e) {
            $this->assertInstanceOf('RuntimeException', $e);
        }
    }

    public function testTryToSetHitCountAttributeToEmptyValue()
    {
        try {
            $model = new ActiveRecord();
            $model->attachBehavior('hit', new \usualdesigner\yii2\behavior\HitableBehavior(['attribute' => '']));
            $this->assertTrue(false, "No Exception thrown");
        } catch (\Exception $e) {
            $this->assertInstanceOf('yii\db\IntegrityException', $e);
        }
    }

    // tests
    public function testNotTouchedYet()
    {
        $post = Post::findOne(1);
        $this->assertEquals(null, $post->getBehavior('hit')->getHitsCount());
    }

    public function testWithNormalDelay()
    {
        Yii::$app->request->ipHeaders = [];
        $_SERVER["REMOTE_ADDR"] = "127.0.0.2";
        Yii::$app->request->headers->set('User-Agent', "Test");

        $post = Post::findOne(1);

        $post->getBehavior('hit')->touch();
        $this->assertEquals(1, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(1, $post->hits_count);

        $post->getBehavior('hit')->touch();
        $this->assertEquals(1, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(1, $post->hits_count);
    }

    public function testNoDelay()
    {
        Yii::$app->request->ipHeaders = [];
        $_SERVER["REMOTE_ADDR"] = "127.0.0.2";
        Yii::$app->request->headers->set('User-Agent', "Test");

        $post = Post::findOne(1);
        $post->getBehavior('hit')->delay = 0; // delay off

        $post->getBehavior('hit')->touch();
        $this->assertEquals(1, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(1, $post->hits_count);
        $post->getBehavior('hit')->touch();
        $this->assertEquals(2, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(2, $post->hits_count);
        $post->getBehavior('hit')->touch();
        $this->assertEquals(3, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(3, $post->hits_count);
    }

    public function testDelayTest()
    {
        Yii::$app->request->ipHeaders = [];
        $_SERVER["REMOTE_ADDR"] = "127.0.0.2";
        Yii::$app->request->headers->set('User-Agent', "Test");

        $post = Post::findOne(1);
        $post->getBehavior('hit')->delay = 3; // delay = 3s

        $post->getBehavior('hit')->touch();
        $this->assertEquals(1, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(1, $post->hits_count);

        sleep(4); // Wait till delay time is over

        $post->getBehavior('hit')->touch();
        $this->assertEquals(2, $post->getBehavior('hit')->getHitsCount());
        $this->assertEquals(2, $post->hits_count);
    }
}
