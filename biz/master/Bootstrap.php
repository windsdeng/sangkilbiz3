<?php

namespace biz\master;

use Yii;
use biz\master\hooks\CogsHook;
use biz\master\hooks\PriceHook;
use biz\master\hooks\StockHook;
use biz\master\components\UserProperties;
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
        $app->attachBehaviors([
            CogsHook::className() => CogsHook::className(),
            PriceHook::className() => PriceHook::className(),
            StockHook::className() => StockHook::className()
        ]);
        if (ArrayHelper::getValue($config, 'attach_user_behavior', true)) {
            $this->attachUserProperty($app->getUser());
        }
    }

    /**
     * 
     * @param \yii\web\User $user
     */
    protected function attachUserProperty($user)
    {
        $user->attachBehavior(UserProperties::className(), UserProperties::className());
    }

    /**
     * 
     * @param \yii\web\Application $app
     */
    protected function autoDefineModule($app)
    {
        $app->setModule('master', Module::className());
    }
}