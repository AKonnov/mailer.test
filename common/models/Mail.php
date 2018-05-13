<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mail".
 *
 * @property int $id
 * @property string $from
 * @property string $to
 * @property string $status
 * @property string $template
 * @property string $data
 * @property string $date_added
 * @property string $date_modified
 */
class Mail extends \yii\db\ActiveRecord
{
    const STATUS_IN_QUEUE = 0;
    const STATUS_SEND_OK = 1;
    const STATUS_SEND_FAIL = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from', 'to', 'template'], 'required'],
            [['data'], 'string'],
            [['status'], 'safe'],
            [['date_added', 'date_modified'], 'safe'],
            [['from', 'to', 'template'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'From',
            'to' => 'To',
            'template' => 'Template',
            'data' => 'Data',
            'date_added' => 'Date Added',
            'date_modified' => 'Date Modified',
        ];
    }
}
