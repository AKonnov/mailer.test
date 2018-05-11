<?php
/**
 * Created by PhpStorm.
 * User: olaff
 * Date: 10.05.2018
 * Time: 20:54
 */

namespace app\controllers;


use app\models\Mail;
use Yii;
use yii\rest\ActiveController;

class MailController extends ActiveController
{
    public $modelClass = "app\models\Mail";

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
        unset($actions['create'], $actions['delete'], $actions['index']);
        return $actions;
    }

    public function actionCreate()
    {
        $model = new Mail();
        //надо обрабатывать строку с массивом data... так как пишем в бд
        $innerData = Yii::$app->getRequest()->getBodyParams();
        if (!$innerData) {
            Yii::$app->response->setStatusCode(422);
            return;
        }
        if ($innerData['data']) {
            $innerData['data'] = json_encode($innerData['data'], JSON_PRETTY_PRINT);
        }

        if ($model->load($innerData, '') && $model->validate()) {
            $model->save();
            Yii::$app->response->setStatusCode(200);
            return;
        }
        return $model;

    }
}