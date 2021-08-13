<?php

namespace admin\modules\install\models;

use Yii;
use common\models\NumberSeries;
use admin\models\Generater;
use common\models\VatType;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class InstallVatType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function createVat(){

        $vattype[] = self::genVat((Object)[
            'name'      => 'Vat',
            'desc'      => 'Vat 7%',
            'value'     => 7,
        ]);

        $vattype[] = self::genVat((Object)[
            'name'      => 'No Vat',
            'desc'      => 'Vat 0%',
            'value'     => 0,
        ]);
       
        
        return (Object)[
            'status'    => 200,
            'message'    => $vattype,
        ];
        
    }

    public static function genVat($obj){
        $model = \common\models\VatType::find()
        ->where(['vat_value' => $obj->value])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->one();
        $message = 'Success';
        if($model !==null){
            $status = 204;
        }else{
            $model = new \common\models\VatType();
            $model->name            = $obj->name;
            $model->description     = $obj->desc;
            $model->vat_value       = $obj->value;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status = 200;                
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => $obj->desc,
            'message'   => $message
        ];
    }

}