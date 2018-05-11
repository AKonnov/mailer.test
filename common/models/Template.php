<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\filters\VerbFilter;

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
}
