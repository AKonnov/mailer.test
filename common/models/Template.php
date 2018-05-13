<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "template".
 *
 * @property int $id
 * @property string $code
 * @property string $modified_at
 * @property string $data_type
 * @property string $subject
 * @property string $body
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * @param string $code
     * @return null|Template
     */
    public static function findByCode($code = '')
    {
        return Template::findOne(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'modified_at',
                'updatedAtAttribute' => 'modified_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'subject', 'body', 'data_type'], 'required'],
            [['modified_at'], 'safe'],
            [['data_type', 'body'], 'string'],
            [['code', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'modified_at' => 'Modified At',
            'data_type' => 'Data Type',
            'subject' => 'Subject',
            'body' => 'Body',
        ];
    }

    public function isHtml()
    {
        if($this->data_type == 'html'){
            return true;
        }
        return false;
    }
}
