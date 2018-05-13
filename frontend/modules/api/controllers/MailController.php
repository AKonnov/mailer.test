<?php
/**
 * Created by PhpStorm.
 * User: olaff
 * Date: 10.05.2018
 * Time: 20:54
 */

namespace frontend\modules\api\controllers;


use common\models\Mail;
use Yii;
use yii\rest\ActiveController;

class MailController extends ActiveController
{
    const DELAY_PRIORITY = "1000";
    const DELAY_TIME = 1; //Default priority
    public $modelClass = "common\models\Mail"; //Default delay time

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /** cors запросы разрешены */
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];
        /* отдаем ответ в json */
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    /**
     * отменяем стандартные действия, кастомизируем create
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        //unset($actions['create'], $actions['delete'], $actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $model = new Mail();
        //надо обрабатывать строку с массивом data... так как пишем в бд
        try {
            $innerData = $this->getFormattingRequest();

            if ($model->load($innerData, '') && $model->validate()) {
                $model->status = Mail::STATUS_IN_QUEUE;
                $model->save();
                //не забываем передать id так как мы потом для него в бд менять статус будем
                $innerData['id'] = $model['id'];
                $this->putMailInQueue(json_encode($innerData, JSON_PRETTY_PRINT));
                Yii::$app->response->setStatusCode(200);
                return $model->id;
            }
        } catch (\Exception $e) {
            return Yii::$app->response->setStatusCode(400);
        }
        return $model;


    }

    /**
     * обработка входящих данных, приведение к нужному виду.
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFormattingRequest()
    {
        $innerData = Yii::$app->getRequest()->getBodyParams();
        if (!$innerData) {
            Yii::$app->response->setStatusCode(422);
            return [];
        }
        if ($innerData['data']) {
            $innerData['data'] = json_encode($innerData['data'], JSON_PRETTY_PRINT);
        }
        return $innerData;
    }

    /**
     *  кладем задачу в очередь. beanstalkd должен быть включен
     * @param string $data
     * @return string
     */
    public function putMailInQueue($data = '')
    {
        if (!$data) {
            return '';
        }
        return Yii::$app->beanstalk->putInTube('tube', $data, self::DELAY_PRIORITY, self::DELAY_TIME);
    }
}