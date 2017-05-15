<?php
/**
 * @author Harry Tang <harry@modernkernel.com>
 * @link https://modernkernel.com
 * @copyright Copyright (c) 2017 Modern Kernel
 */

namespace modernkernel\support\models;

use common\models\Account;
use common\models\Setting;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ticket_content".
 *
 * @property integer $id
 * @property integer $id_ticket
 * @property string $content
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Account $createdBy
 * @property Ticket $ticket
 */
class Content extends ActiveRecord
{


    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;


    /**
     * get status list
     * @param null $e
     * @return array
     */
    public static function getStatusOption($e = null)
    {
        $option = [
            self::STATUS_ACTIVE => Yii::$app->getModule('support')->t('Active'),
            self::STATUS_INACTIVE => Yii::$app->getModule('support')->t('Inactive'),
        ];
        if (is_array($e))
            foreach ($e as $i)
                unset($option[$i]);
        return $option;
    }

    /**
     * get status text
     * @return string
     */
    public function getStatusText()
    {
        $status = $this->status;
        $list = self::getStatusOption();
        if (!empty($status) && in_array($status, array_keys($list))) {
            return $list[$status];
        }
        return Yii::$app->getModule('support')->t('Unknown');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%support_ticket_content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_ticket', 'content'], 'required'],
            [['id_ticket', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['id_ticket'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['id_ticket' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->getModule('support')->t('ID'),
            'id_ticket' => Yii::$app->getModule('support')->t('Id Ticket'),
            'content' => Yii::$app->getModule('support')->t('Content'),
            'created_by' => Yii::$app->getModule('support')->t('Created By'),
            'created_at' => Yii::$app->getModule('support')->t('Created At'),
            'updated_at' => Yii::$app->getModule('support')->t('Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Account::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'id_ticket']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($insert) {
            if ($this->created_by != $this->ticket->created_by) {
                $email = $this->ticket->createdBy->email;
                Yii::$app->language=$this->ticket->createdBy->language;
            } else {
                $email = Setting::getValue('adminMail');
            }

            /* send email */
            $subject = Yii::$app->getModule('support')->t('[{APP} Ticket #{ID}] Re: {TITLE}', ['APP' => Yii::$app->name, 'ID' => $this->ticket->id, 'TITLE' => $this->ticket->title]);
            Yii::$app->mailer
                ->compose(
                    [
                        'html' => 'reply-ticket-html',
                        'text' => 'reply-ticket-text'
                    ],
                    ['title' => $subject, 'model' => $this]
                )
                ->setFrom([Setting::getValue('outgoingMail') => Yii::$app->name])
                ->setTo($email)
                ->setSubject($subject)
                ->send();

        }
    }
}
