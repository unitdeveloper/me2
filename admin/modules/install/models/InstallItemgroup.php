<?php

namespace admin\modules\install\models;

use Yii;
use \common\models\NumberSeries;
use admin\models\Generater;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class InstallItemgroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function createItemGroups(){

        //--------General---------
        // $groups[] = self::genItemGroups((Object)[
        //     'name'      => 'GROUP -> General',
        //     'desc'      => 'General',
        //     'desc_th'   => Yii::t('common','General')
        // ]);
        //--------/.General---------



        //--------Beverages---------
        $groups[] = self::genItemGroups((Object)[
            'name'      => 'GROUP -> Beverages',
            'desc'      => 'Beverages',
            'desc_th'   => 'เครื่องดื่ม',
            'photo'     => 'images/main/beverages.png'
        ]);

            $groups[] = self::genItemGroups((Object)[
                'name'      => 'GROUP -> Beverages -> Water',
                'desc'      => 'Water',
                'child'     => 'Beverages',
                'desc_th'   => 'น้ำ'
            ]);
            
                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Water -> Drinking Water',
                        'desc'      => 'Drinking Water',
                        'child'     => 'Water',
                        'desc_th'   => 'น้ำดื่ม'
                    ]);
                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Water -> SOFT DRINK',
                        'desc'      => 'Soft Drink',
                        'child'     => 'Water',
                        'desc_th'   => 'น้ำอัดลม'
                    ]);




        
            $groups[] = self::genItemGroups((Object)[
                'name'      => 'GROUP -> Beverages -> Water -> Alcohol',
                'desc'      => 'Alcohol',
                'child'     => 'Beverages',
                'desc_th'   => 'แอลกอฮอล์'
            ]);

                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Water -> Alcohol -> Beer',
                        'desc'      => 'Beer',
                        'child'     => 'Alcohol',
                        'desc_th'   => 'เบียร์'
                    ]);

                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Water -> Alcohol -> Whisky',
                        'desc'      => 'Whisky',
                        'child'     => 'Alcohol',
                        'desc_th'   => 'วิสกี้'
                    ]);

                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Water -> Alcohol -> Spirits',
                        'desc'      => 'Spirits',
                        'child'     => 'Alcohol',
                        'desc_th'   => 'เหล้า-สุรา'
                    ]);
        
        
        

            $groups[] = self::genItemGroups((Object)[
                'name'      => 'GROUP -> Beverages -> Milk',
                'desc'      => 'Milk',
                'child'     => 'Beverages',
                'desc_th'   => 'นม'
            ]);

                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Milk -> Soybean Milk',
                        'desc'      => 'Soybean Milk',
                        'child'     => 'Milk',
                        'desc_th'   => 'นมถั่วเหลือง'
                    ]);

                    $groups[] = self::genItemGroups((Object)[
                        'name'      => 'GROUP -> Beverages -> Milk -> UHT Yogurt',
                        'desc'      => 'UHT Yogurt',
                        'child'     => 'Milk',
                        'desc_th'   => 'นมเปรี้ยว'
                    ]);

        //--------/.Beverages---------





        //--------Food---------
        $groups[] = self::genItemGroups((Object)[
            'name'      => 'GROUP -> Food',
            'desc'      => 'Food',
            'desc_th'   => 'อาหาร',
            'photo'     => 'images/main/foods.png'
        ]);

                $groups[] = self::genItemGroups((Object)[
                    'name'      => 'GROUP -> Food -> Canned Food',
                    'desc'      => 'Canned Food',
                    'child'     => 'Food',
                    'desc_th'   => 'อาหารกระป๋อง'
                ]);

                        $groups[] = self::genItemGroups((Object)[
                            'name'      => 'GROUP -> Food -> Canned Food -> Canned Fruit',
                            'desc'      => 'Canned Fruit',
                            'child'     => 'Canned Food',
                            'desc_th'   => 'ผลไม้กระป๋อง'
                        ]);

                        $groups[] = self::genItemGroups((Object)[
                            'name'      => 'GROUP -> Food -> Canned Food -> Canned Fish/Seafood',
                            'desc'      => 'Canned Fish/Seafood',
                            'child'     => 'Canned Food',
                            'desc_th'   => 'ปลากระป๋อง'
                        ]);
        
        
       
                $groups[] = self::genItemGroups((Object)[
                    'name'      => 'GROUP -> Food -> Cooking Ingredients',
                    'desc'      => 'Cooking Ingredients',
                    'child'     => 'Food',
                    'desc_th'   => 'เครื่องปรุง'
                ]);
                

                        $groups[] = self::genItemGroups((Object)[
                            'name'      => 'GROUP -> Food -> Cooking Ingredients -> Sugar',
                            'desc'      => 'Sugar',
                            'child'     => 'Cooking Ingredients',
                            'desc_th'   => 'น้ำตาล'
                        ]);

                        $groups[] = self::genItemGroups((Object)[
                            'name'      => 'GROUP -> Food -> Cooking Ingredients -> Fish Sauce',
                            'desc'      => 'Fish Sauce',
                            'child'     => 'Cooking Ingredients',
                            'desc_th'   => 'น้ำปลา'
                        ]);
        //--------/.Food---------
      
 

        return (Object)[
            'status'    => 200,
            'message'    => $groups,
        ];
        
    }

    static function genItemGroups($obj){
        $model = \common\models\Itemgroup::find()
        ->where(['Description' => $obj->desc])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->one();
        if($model !==null){
            $status = 204;
        }else{
            $model = new \common\models\Itemgroup();
            $model->Description     = $obj->desc;
            $model->Child           = (isset($obj->child))?  self::getChildGroup($obj->child) : '00';
            $model->Status          = '1';
            $model->Description_th  = $obj->desc_th;
            $model->sequent         = '1';
            $model->photo           = (isset($obj->photo))?  $obj->photo : null;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status = 200;
            }else{
                $status = 500; 
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => $obj->name
        ];
    }

    protected function getChildGroup($child){
        $model = \common\models\Itemgroup::find()
        ->where(['Description' => $child])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->one();
        if($model !==null){
            return (string)$model->GroupID;
        }else{
            return '00';
        }
    }


}