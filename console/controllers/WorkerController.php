<?php

namespace console\controllers;

use common\models\Mail;
use common\models\Template;
use Pheanstalk\Job;
use udokmeci\yii2beanstalk\BeanstalkController;
use Yii;
use yii\base\Module;
use yii\helpers\StringHelper;
use yii\log\Logger;


/**
 * Created by PhpStorm. * User: olaff
 * Date: 11.05.2018
 * Time: 9:22
 */
class WorkerController extends BeanstalkController
{
    const DELAY_PRIORITY = "1000"; //Default priority
    const DELAY_TIME = 5; //Default delay time
    const DELAY_MAX = 3;
    private $_log;

    public function __construct($id, Module $module, array $config = [])
    {
        $this->_log = new Logger();
        parent::__construct($id, $module, $config);
    }

    /**
     * список workers для задач
     * @return array
     */
    public function listenTubes()
    {
        return ["tube"];
    }

    public function actionTest()
    {
        Yii::info('start', 'mails');
    }

    /**
     * основное действие -- отсылка письма и обработка отсылки
     * @param Job $job
     * @return string
     */
    public function actionTube(Job $job)
    {
        Yii::info("mail start", 'mails');
        $emailObj = $job->getData();
        //принудительно переводим в массив
        $email = json_decode(json_encode($emailObj), true);

        try {
            $template = $this->getTemplate($email['template']);

            if ($template) {
                //форматируем все данные , в том числе делаем замены
                $email = $this->formatMail($email, $template);
            } else {
                Yii::info('template not found', 'mails');
                return self::DELETE;
            }

            $resultSend = $this->sendMail($email, $template->isHtml());

            if ($resultSend) {
                echo 'mail send';
                Yii::info('mail ' . $email['id'] . ' send', 'mails');

                $model = Mail::findOne($email['id']);
                $model->status = Mail::STATUS_SEND_OK;
                $model->save();
            }

            return self::DELETE;

        } catch (\Exception $e) {
            echo 'mail end fail';
            Yii::info('mail end fail', 'mails');
            $model = Mail::findOne($email['id']);
            $model->status = Mail::STATUS_SEND_FAIL;
            $model->save();
            return self::DELETE;
        }
    }

    /**
     * @param string $code
     * @return null|Template
     */
    private function getTemplate($code = '')
    {
        return Template::findByCode($code);
    }

    /**
     *
     * подстановку надо делать именно через массивы ,иначе конструкция '{'.$key.'}' воспронимается как переменная
     * @param $email
     * @param Template $template
     * @return array
     */
    private function formatMail($email, Template $template)
    {
        $email['data'] = json_decode($email['data'], true);
        $search = [];
        $replace = [];
        foreach ($email['data'] as $key => $value) {
            $search[] = '{' . $key . '}';
            $replace[] = $value;
        }
        $email['body'] = str_replace($search, $replace, $template->body);
        $email['subject'] = str_replace($search, $replace, $template->subject);
        $email['text'] = strip_tags($email['body']);
        return $email;
    }

    /**
     * @param $emailData
     * @param bool $isHtml
     * @return bool результат отправки
     */
    private function sendMail($emailData, $isHtml = false)
    {
        $mail = Yii::$app->mailer->compose();

        //убираем лишние символы в названии почты - это надо для mailer
        $to = StringHelper::explode($emailData['to'], '<', '<>"');
        if (!empty($to[1])) {
            $mail->setTo([$to[1] => $to[0]]);
        } else {
            $mail->setTo([$emailData['to'] => $emailData['to']]);
        }

        $from = StringHelper::explode($emailData['from'], '<', '<>"');
        if (!empty($from[1])) {
            $mail->setFrom([$from[1] => $from[0]]);
        } else {
            $mail->setFrom([$emailData['from'] => $emailData['from']]);
        }

        $mail->setSubject($emailData['subject']);
        if ($isHtml) {
            $mail->setHtmlBody($emailData['body']);
        }
        $mail->setTextBody($emailData['text']);
        return $mail->send();
    }
}