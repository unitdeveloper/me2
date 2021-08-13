<?php

namespace admin\modules\Manufacturing\models;

use Yii;

/**
 * This is the model class for table "kitbom_line".
 *
 * @property integer $id
 * @property integer $kitbom_no
 * @property string $item_no
 * @property string $name
 * @property string $description
 * @property string $quantity
 * @property string $color_style
 * @property integer $comp_id
 * @property integer $user_id
 */
class KitbomLine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kitbom_line';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kitbom_no', 'item_no', 'comp_id', 'user_id'], 'required'],
            [['kitbom_no', 'comp_id', 'user_id'], 'integer'],
            [['description'], 'string'],
            [['quantity'], 'number'],
            [['item_no', 'name'], 'string', 'max' => 255],
            [['color_style'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'kitbom_no' => Yii::t('common', 'Kitbom No'),
            'item_no' => Yii::t('common', 'Item No'),
            'name' => Yii::t('common', 'Name'),
            'description' => Yii::t('common', 'Description'),
            'quantity' => Yii::t('common', 'Quantity'),
            'color_style' => Yii::t('common', 'Color Style'),
            'comp_id' => Yii::t('common', 'Comp ID'),
            'user_id' => Yii::t('common', 'User ID'),
        ];
    }

    public function getItems()
    {
        return $this->hasOne(\common\models\Items::className(), ['No' => 'item_no']);
    }

    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['id' => 'comp_id']);
    }

    public function getUsers()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
}
