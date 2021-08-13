<?php

namespace admin\modules\install\models;

use Yii;
use \common\models\NumberSeries;
use \common\models\SetupNoSeries;
use \common\models\RuningNoseries;
use admin\models\Generater;
use \common\models\VatType;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class Uninstall extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function UninstallModule($module){

         
        switch ($module) {
            case 'series':
                $UNINSTALL[] = self::RuningNumberSeries();
                $UNINSTALL[] = self::SetupNumberSeries();
                $UNINSTALL[] = self::NumberSeries();       
                break;

            case 'vat':
                $UNINSTALL[]  = self::vatType();
                break;
            
            default:
                $UNINSTALL[]  = (Object)[
                    'status'    => 500,
                    'name'      => 'No Action'
                ];
                break;
        }
       
       
        
        return (Object)[
            'status'    => 200,
            'message'    => $UNINSTALL,
        ];
        
    }

    static function vatType(){
        
        if(VatType::deleteAll(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])){
            return (Object)[
                'status'    => 200,
                'name'      => 'Vat Percent'
            ];
        }else{
            return (Object)[
                'status'    => 404,
                'name'      => 'Vat Percent'
            ];
        }
    }


    static function RuningNumberSeries(){
        
        if(RuningNoseries::deleteAll(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])){
            return (Object)[
                'status'    => 200,
                'name'      => 'Runing Number Series'
            ];
        }else{
            return (Object)[
                'status'    => 404,
                'name'      => 'Runing Number Series'
            ];
        }
    }

    static function NumberSeries(){
        
        if(NumberSeries::deleteAll(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])){
            return (Object)[
                'status'    => 200,
                'name'      => 'Number Series'
            ];
        }else{
            return (Object)[
                'status'    => 404,
                'name'      => 'Number Series'
            ];
        }
    }

    static function SetupNumberSeries(){
        
        if(SetupNoSeries::deleteAll(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])){
            return (Object)[
                'status'    => 200,
                'name'      => 'Setup Number Series'
            ];
        }else{
            return (Object)[
                'status'    => 404,
                'name'      => 'Setup Number Series'
            ];
        }
    }


    
}