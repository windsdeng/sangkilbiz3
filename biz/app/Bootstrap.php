<?php

namespace biz\app;

use Yii;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\validators\Validator;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Description of Bootstrapt
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Bootstrap extends \biz\app\base\Bootstrap
{

    /**
     * 
     * @param \yii\base\Application $app
     */
    protected function initialize($app, $config)
    {
        $this->diConfig(isset($config['di']) ? $config['di'] : []);
        if (ArrayHelper::getValue($config, 'attach_transaction_event', true)) {
            $app->db->on(Connection::EVENT_BEGIN_TRANSACTION, ['mdm\logger\RecordLogger', 'begin']);
            $app->db->on(Connection::EVENT_COMMIT_TRANSACTION, ['mdm\logger\RecordLogger', 'commit']);
            $app->db->on(Connection::EVENT_ROLLBACK_TRANSACTION, ['mdm\logger\RecordLogger', 'rollback']);
        }
    }

    /**
     * 
     * @param array $params
     */
    protected function diConfig($params)
    {
        $config = [
            'BizTimestampBehavior' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'update_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'update_at',
                ],
                'value' => new Expression('NOW()')
            ],
            'BizBlameableBehavior' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['create_by', 'update_by'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'update_by',
                ],
            ],
            'BizStatusConverter' => [
                'class' => 'mdm\converter\EnumConverter',
                'attributes' => [
                    'nmStatus' => 'status'
                ],
                'enumPrefix' => 'STATUS_'
            ],
        ];
        $currentDefinitions = Yii::$container->definitions;
        foreach ($config as $class => $definition) {
            if (isset($currentDefinitions[$class])) {
                continue;
            }
            if (isset($params[$class]) && is_array($params[$class])) {
                $definition = array_merge($definition, $params[$class]);
            }
            Yii::$container->set($class, $definition);
        }
    }

    protected function validatorConfig($params)
    {
        $config = [
            'doubleVal' => [
                'class' => 'yii\validators\FilterValidator',
                'filter' => function($val) {
                return (double) str_replace(',', '', $val);
            }
            ]
        ];
        foreach ($config as $name => $definition) {
            if (isset($params[$name]) && is_array($params[$name])) {
                $definition = array_merge($definition, $params[$name]);
            }
            Validator::$builtInValidators[$name] = $definition;
        }
    }
}