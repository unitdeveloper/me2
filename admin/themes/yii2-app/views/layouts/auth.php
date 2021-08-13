<?php
 
use common\models\AppsRules;
use common\models\Company;
use common\models\AuthAssignment;
use common\models\Authentication;




if(Permission()){
    return Yii::$app->response->redirect(['install/default']);
    //return $this->redirect(['install/default']);
}



if(!Yii::$app->session->get('Rules')){
    $cookies = \Yii::$app->response->cookies;
        
    

    $session = \Yii::$app->session;

    $session->set('collapse', 'sidebar-collapse');

    if(\Yii::$app->user->identity){
            
        $AppsRules = AppsRules::find()
                ->where(['user_id' => \Yii::$app->user->identity->id])
                ->one();


        if(AppsRules::find()->where(['user_id' => \Yii::$app->user->identity->id])->exists()){
            
            cookieregister('myCompany',$AppsRules->comp_id);

        
            $session->set('sales_id',  $AppsRules->sales_id);
            $session->set('user.sales_id',  $AppsRules->sales_id);
            
            $session->set('myCompany', $AppsRules->comp_id);
            $session->set('user.myCompany', $AppsRules->comp_id);

            $session->set('Rules',     $AppsRules);
            $session->set('user.Rules',     $AppsRules);

            $company = Company::findOne($AppsRules->comp_id);
            $session->set('company',$company->name);
            $session->set('brand',$company->brand);
            /**
             * SET PACKAGE
            */
            if(!$session->get('PACKAGE')){                
                $session->set('PACKAGE',$company->package);                
            }

        
            Authentications($AppsRules);

            if((Yii::$app->session->get('workyears')==NULL) || (Yii::$app->session->get('workyears')== 1970)) 
            { 
                Yii::$app->session->set('workyears',date('Y')); 
            }

        }


        
        $cookies->add(new \yii\web\Cookie([
            'name' => 'myCompany',
            'value' => Yii::$app->session->get('myCompany'),               
        ]));
    }

}


function Permission(){
    if(\Yii::$app->user->identity){
        $rules      = AppsRules::findOne(['user_id' => \Yii::$app->user->identity->id]);
        $permission = AuthAssignment::findOne(['user_id' => \Yii::$app->user->identity->id]);

        if($permission ==null){          
            return true;
        }else if($rules ==null){          
            return true;
        }else {
            return false;
        }
    }else{
        return false;  
    }
}



function cookieregister($cookies_name,$value)
{
    $cookies = \Yii::$app->response->cookies;    //Enable cookie editing permissions.
    $cookies->add(new \yii\web\Cookie([
        'name' => $cookies_name,
        'value' => $value,              //set "CartSessionID" = encryp value
    ]));
}


function Authentications($obj){

    $model = Authentication::findOne(['user_id' => Yii::$app->user->identity->id]);
    if ($model != null){
        $model->token      = Yii::$app->session->getId();
        $model->X_CSRF_Token = base64_encode(Yii::$app->session->getId());
        $model->user_id    = \Yii::$app->user->identity->id;
        $model->comp_id    =  $obj->comp_id;
        $model->date       =  date('Y-m-d H:i:s');
        $model->ip          = Yii::$app->getRequest()->getUserIP();
        $model->save();
    }else{
        $Authen = new Authentication();
        $Authen->token      = Yii::$app->session->getId();
        $Authen->X_CSRF_Token = base64_encode(Yii::$app->session->getId());
        $Authen->user_id    = \Yii::$app->user->identity->id;
        $Authen->comp_id    =  $obj->comp_id;
        $Authen->date       =  date('Y-m-d H:i:s');
        $Authen->ip         = Yii::$app->getRequest()->getUserIP();
        $Authen->save();
    }
    

    
}