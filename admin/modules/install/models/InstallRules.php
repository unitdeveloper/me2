<?php

namespace admin\modules\install\models;

use Yii;
use common\models\AuthAssignment;
use common\models\User;
use common\models\Register;
use common\models\AppsRules;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class InstallRules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function createRules(){

        $user       = User::findOne(Yii::$app->user->identity->id);

     
        //var_dump(Yii::$app->session->get('Rules')['comp_id']); exit;
        $rules[] = self::genRules([
            'name'      => Yii::$app->user->identity->username,
            'user_id'   => Yii::$app->user->identity->id,
            'rules_id'  => 1,
            'comp_id'   => Yii::$app->session->get('Rules')['comp_id']//$user->register->comp_id
        ]);
    
           
        
        
        // ถ้าเป็นเจ้าของร้าน
        if($user->owner){

            $rules[] = self::genPermission((Object)[
                'rules'     => 'Admin-Company',
                'name'      => 'Permissions -> Administrator',
                'user_id'   => (string)Yii::$app->user->identity->id,
                
            ]);

            $rules[] = self::genPermission((Object)[
                'rules'     => 'Owner',
                'name'      => 'Permissions -> Owner',
                'user_id'   => (string)Yii::$app->user->identity->id,
            ]);
        }else{

            $rules[] = self::genPermission((Object)[
                'rules'     => 'sales',
                'name'      => 'Permissions -> Sales',
                'user_id'   => (string)Yii::$app->user->identity->id,
            ]);

        }
        
        
        return (Object)[
            'status'    => 200,
            'message'    => $rules,
        ];
        
    }

    static function genRules($obj){
        $obj        = (Object)$obj;
        $regis      = Register::findOne([
                        'user_id' => Yii::$app->user->identity->id,
                        'comp_id' => $obj->comp_id
                    ]);
        $model      = AppsRules::find()->where([
                        'user_id' => $obj->user_id,
                        'comp_id' => $obj->comp_id
                    ])->one();
            
        $message    = '';
        
        if($model !=null){
            $status = 204;
        }else{

            $model                  = new AppsRules();
            $model->name            = $obj->name;
            $model->user_id         = $obj->user_id;
            //$model->permission_id   = 1;
            $model->date_created    = date('Y-m-d H:i:s');
            $model->rules_id        = $obj->rules_id;
            $model->sales_id        = 'CENTER';
            $model->sale_id         = self::getSaleDefault(['comp_id' => $obj->comp_id,'code' => 'CENTER']);
            $model->sale_code       = '';
            $model->status          = 1;
            $model->sprit_code      = 'ew'.($regis != null ? $regis->comp_id : $obj->comp_id);
            $model->comp_id         = $obj->comp_id;//$user->register->comp_id;
            $model->users           = $obj->user_id;
            if($model->save()){
                $status = 200;
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        // ถ้าเป็นเจ้าของร้าน
        if($regis != null){
            $name = $regis->regis_name;
            
        }else{
            $name = Yii::t('common','Company');
        }

        return (Object)[
            'status'    => $status,
            'name'      => $name.' - '.Yii::$app->session->get('Rules')['comp_id'],
            'message'   => $message
        ];
    }

    static function genPermission($obj){
        $user       = User::findOne(Yii::$app->user->identity->id);
        $model      = AuthAssignment::findOne(['user_id' => $obj->user_id,'item_name' => $obj->rules]);
        $message    = $obj->name;

        if($model !==null){
            $status = 204;
        }else{
            $model = new AuthAssignment();
            $model->item_name       = $obj->rules;
            $model->user_id         = $obj->user_id;            
            $model->created_at      = strtotime(date('Y-m-d H:i:s'));
            if($model->save()){
                $status = 200;
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => $obj->name,
            'message'   => $message
        ];
    }

    protected function getSaleDefault($obj){
        $obj = (Object)($obj);
        $model = \common\models\SalesPeople::findOne(['code' => $obj->code,'comp_id' => $obj->comp_id]);
        if($model !==null){
            return $model->id;
        }else{
            return null;
        }
    }


    


}