<?php

namespace admin\modules\Manufacturing\models;

use Yii;

use yii\web\UploadedFile;
/**
 * This is the model class for table "kitbom_header".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property integer $item_set
 * @property integer $max_val
 * @property integer $priority
 * @property integer $comp_id
 * @property integer $user_id
 */
class KitbomHeader extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $upload_folder ='uploads/kitbom';


    public static function tableName()
    {
        return 'kitbom_header';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description','format_gen','format_type'], 'string'],
            [['max_val', 'priority', 'comp_id', 'user_id','multiple','running_digit','status'], 'integer'],
            [['comp_id', 'user_id','name','code'], 'required'],
            [['code'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            ['item_set', 'each', 'rule' => ['string']],
            [['photo'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'png,jpg'
            ]
        ];
    }

    public function upload($model,$attribute)
    {
        $photo  = UploadedFile::getInstance($model, $attribute);
        
        // if ($photo !== null) {
        //     $this->photo->saveAs('uploads/kitbom/' . $this->photo->baseName . '.' . $this->photo->extension);
        //     return true;
        // } else {
        //     return false;
        // }


        $path = $this->getUploadPath();
        if ($photo !== null) {

            $fileName = md5($photo->baseName.time()) . '.' . $photo->extension;
            //$fileName = $photo->baseName . '.' . $photo->extension;
            if($photo->saveAs($path.$fileName)){
              return $fileName;
            }
        }
        return $model->isNewRecord ? false : $model->getOldAttribute($attribute);
    }

    public function getUploadPath(){
      return Yii::getAlias('@webroot').'/'.$this->upload_folder.'/';
    }

    public function getUploadUrl(){
      return Yii::getAlias('@web').'/'.$this->upload_folder.'/';
    }

    public function getPhotoViewer(){
      return empty($this->photo) ? Yii::getAlias('@web').'/uploads/kitbom/img.png' : $this->getUploadUrl().$this->photo;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'code' => Yii::t('common', 'Code'),
            'name' => Yii::t('common', 'Name'),
            'description' => Yii::t('common', 'Description'),
            'item_set' => Yii::t('common', 'Item Set'),
            'max_val' => Yii::t('common', 'Max Val'),
            'priority' => Yii::t('common', 'Priority'),
            'comp_id' => Yii::t('common', 'Comp ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'multiple' => Yii::t('common','Multiple'),
            'format_gen' => Yii::t('common','Item format generation'),
            'format_type' => Yii::t('common','Format type'),
            'running_digit' => Yii::t('common','Running digit'),
            'status' => Yii::t('common','Status'),
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['id' => 'comp_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    public function getItemset()
    {         
        if(strpos(",", $this->item_set) !== false){
             
        }else{            
            return $this->hasOne(\common\models\Itemset::className(), ['id' => 'item_set']);
             
        }
    }

    // public function getItemset()
    // {
    //     return $this->hasOne(\common\models\Itemset::className(), ['id' => 'item_set']);
    // }
    
}
