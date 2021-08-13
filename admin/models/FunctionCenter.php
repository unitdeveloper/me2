<?php

namespace admin\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\Profile;
use common\models\Itemgroup;
use common\models\Items;

use common\models\Property;
use common\models\PropertyHasGroup;
use common\models\ItemsHasProperty;

use common\models\AppsRules;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionCenter extends Model
{

    public function RegisterRule()
    {
        // Register Zone

        // +---[EW 2017®]----+
        // |                 |
        // |                 |
        // |     Welcome     |
        // |      https      |
        // |                 |
        // |    eWiN Live    |
        // |                 |
        // |                 |
        // |                 |
        // +-----[eWiNL]-----+


        $session = \Yii::$app->session;

        

        if(isset(Yii::$app->user->identity->id)){
            
            if(AppsRules::find()->where(['user_id' => \Yii::$app->user->identity->id])->exists())
            {
                $AppsRules = AppsRules::find()
                    ->where(['user_id' => \Yii::$app->user->identity->id])
                    ->one();

                if($AppsRules->status != 1){
                    Yii::$app->user->logout();
                    echo '<script>window.location.href = "index.php?r=user%2Fsecurity%2Flogin";</script>';
                    exit;
                }
    
                $this->cookieregister('myCompany',$AppsRules->comp_id);
    
    
                $Company = \common\models\Company::findOne($AppsRules->comp_id);

    
                $session->set('sales_id',  $AppsRules->sales_id);
                $session->set('user.sales_id',  $AppsRules->sales_id);
                $session->set('logo',$Company->companyLogo);
                $session->set('myCompany', $AppsRules->comp_id);
                $session->set('user.myCompany', $AppsRules->comp_id);
    
                $session->set('Rules',     $AppsRules);
                $session->set('user.Rules',     $AppsRules);
    
                $session->set('rules_id',  $AppsRules->rules_id);
                $session->set('digit',  (Object)[
                    'stock' => $Company->stock_digit,
                    'inv' => $Company->invoice_digit
                ]);
                
                $profile = Profile::findOne(Yii::$app->user->identity->id);
                $session->set('theme',  $profile->theme);
                $session->set('workdate', Yii::$app->session->get('workdate') ? Yii::$app->session->get('workdate') : date('Y-m-d'));
                $session->set('workyears', Yii::$app->session->get('workyears') ? Yii::$app->session->get('workyears') : date('Y'));
                
    
            }
        }
        

    }
    public function cookieregister($cookies_name,$value)
    {
        $cookies = \Yii::$app->response->cookies;    //Enable cookie editing permissions.
        $cookies->add(new \yii\web\Cookie([
            'name' => $cookies_name,
            'value' => $value,              //set "CartSessionID" = encryp value
        ]));
    }

	public function MenuGroup($id)
	{
        $model = Itemgroup::find()
        ->where(['Child' => $id])
        ->andwhere(['status' => 1])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->orderBy(['sequent' => SORT_ASC])
        ->all();

		$data = '';
		foreach ($model as $model) {
            $count = Itemgroup::find()->where(['Child' => $model->GroupID, 'status' => 1, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->count();

            if($count>0)
            {
                $data.= '<li style="margin-left:25px;">
                            <span ><i class="fa fa-caret-down pull-left" aria-hidden="true"></i>'.$model->Description_th.'</span>
                        </li>';

                $data.= '<ul class="ew-ul-sub" style="margin-left:0px;"> 
                            '.self::MenuGroup($model->GroupID).'
                         </ul>';

            }else {
                $count = ($model->countItem > 0)? '('.$model->countItem.')' : ' ';

                $data.= '<li style="margin-left:0px;">
                            <small>-</small> 
                            <a href="#cat='.$model->GroupID.'" class="ew-filter-onclick">'.mb_substr($model->Description_th,0,20).'
                            <small class="text-warning">'.$count.'</small>
                            </a> 
                        </li>';
            }

		}
		//$data.= '<li class="divider"></li>';

		return $data;
	}





	public function ItemEdit($id)
	{

	}

	public static function ItemGroupChild()
    {
        $id = 1414; // ถ้าหาสินค้าไม่เจอ มันจะ error(404 Page not found) ในหน้าสร้าง item 
        
        // if(isset($_GET['id']))
    	// {
    		$itemNo     = (isset($_GET['id']))? $_GET['id'] : $id;
	    	$Items      = self::findItems($itemNo);
	    	$Heading    = Itemgroup::find()->where(['GroupID'   => $Items->category])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();
	        $query      = Itemgroup::find()->where(['Child'     => $Items->category])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();

            //var_dump($Items->category); exit();

	        $div = '<div class="panel panel-info ew-content">
	                    <div class="panel-heading" id="ew-panel-header" ew-data="1">'.Yii::t('common',($Heading ? $Heading->Description_th : 'Unknow')).' > <span id="ew-sub-header"></span></div>
	                    <div class="panel-body ew-panel-body">';
	        $div.= '        <div data-toggle="">
	                            <ul class="ew-ul-itemgroup">';
	        foreach ($query as $value) {
	            $count  = Itemgroup::find()->where(['Child' => $value->GroupID])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->count();
	            if($count>0){
	                    $div.= '    <li class="col-sm-3">
	                                    <label class="text-light-blue" style="display: block; width: 100%; text-align:left;">'.$value->Description_th.'</label>
                                        <ul class="col-sm-12">'.self::FindChild($value->GroupID,$value->Description_th,$Items).'</ul>
                                    </li>';
	                }else {
	                    $div.= '    <li class="col-sm-3">
	                                    <label class="text-muted" style="display:width: 100%;">
                                            <input type="radio" name="ItemGroup" class="ew-radio" ew-radio-data="'.$value->GroupID.'"> '.$value->Description_th.'
                                        </label>
	                                </li>';
	                }


	        }
	        $div.= '            </ul>
	                        </div>
	                    </div>
	                </div>';

	    // }else {
        //     $div = '<div class="panel panel-info ew-content"></div>';
	    // }

	    return $div;
    }


    protected static function FindChild($id,$Desc,$Items)
    {
        $model  = Itemgroup::find()->where(['Child' => $id])
                    ->andWhere(['Status' => 1])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->all();
        $div    = '<ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])
                        ->andWhere(['Status' => 1])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->count();
            if($count>0){
                    $div.= '<li class="col-sm-12">
                                <label class="text-aqua" style="display: block; width: 100%; text-align:left;"><b> '.$value->Description_th.'</b></label>
                                <ul class="col-sm-12">'.self::FindChild($value->GroupID,$Desc.' > '.$value->Description_th,$Items).'</ul>
                            </li>';
                }else {
                	if($value->GroupID==$Items->ItemGroup)
                	{
                		$active = 'focus active';
                		$selected = 'checked';
                	}else {
                		$active = NULL;
                		$selected = NULL;
                	}
                    $div.= '<li class="col-sm-12">
                                <label class="text-muted ew-radio ew-selected '.$active.'" ew-radio-data="'.$value->GroupID.'" ew-desc="'.$Desc. ' > '.$value->Description_th.'">
                                <input type="radio" name="ItemGroup" ew-input-desc="'.$Desc. ' > '.$value->Description_th.'" '.$selected.'> '.$value->Description_th.'</label>
                            </li>';
                }
        }
        $div.= '</ul>';
        return $div;
    }




	// Canceled 11/05/2017
	public function ItemGroupChildSub()
    {
    	if(isset($_GET['id']))
    	{
    		$itemNo = $_GET['id'];
    	}else {
    		$itemNo = '1^GL';
    	}

    	$Items = self::findItems($itemNo);

        $model = Itemgroup::find()->where(['Child' => $Items->category])
                ->andWhere(['Status' => 1])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

        $div = '<div data-toggle="buttons">
                    <ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {

            $count = Itemgroup::find()->where(['Child' => $value->GroupID])
                    ->andWhere(['Status' => 1])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->count();

            if($count>0){
                    $div.= '<li>
                                <label class="btn btn-info">'.$value->Description.'</label>
                            </li>';
                    $div.= '<li>'.self::FindChild($value->GroupID,$Items).'</li>';
                }else {
                	if($value->GroupID==$Items->ItemGroup)
                	{
                		$active = 'focus active';
                		$selected = 'checked';
                	}else {
                		$active = NULL;
                		$selected = NULL;
                	}
                    $div.= '<li>
                        <button class="btn btn-default '.$active.'" style="display: block; width: 100%;">
                            <input type="radio" '.$selected.' name="ItemGroup" class="ew-radio" ew-radio-data="'.$value->GroupID.'"> '.$value->Description.'</button>
                    </li>';
                }


        }
        $div.= '    </ul>
                </div>';
        return $div;
    }

    // Canceled 11/05/2017
    public function FindChildSub($id,$Items)
    {
        $model = Itemgroup::find()->where(['Child' => $id])->all();

        $div = '<ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();

            if($count>0){
                    $div.= '<li>
                                <label class="btn btn-default" style="display: block; width: 100%; text-align:left;"><b>'.$value->Description.'</b></label>
                            </li>';
                    $div.= '<li>'.self::FindChild($value->GroupID,$Items).'</li>';
                }else {
                	if($value->GroupID==$Items->ItemGroup)
                	{
                		$active = 'focus active';
                		$selected = 'checked';
                	}else {
                		$active = NULL;
                		$selected = NULL;
                	}
                    $div.= '<li>
                                <button class="btn btn-default ew-radio '.$active.'" ew-radio-data="'.$value->GroupID.'" style="display: block; width: 100%; text-align:left;">
                                <input type="radio" name="ItemGroup" '.$selected.'> '.$value->Description.' '.$Items->ItemGroup.'</button>
                            </li>';
                }
        }
        $div.= '</ul>';
        return $div;
    }




    public function JsonLoadChild($id)
    {
        // Find Child
        $CountChild = Itemgroup::find()->where(['GroupID' => $id]);


        $data = '';
        if($CountChild->count() >0)
        {

            $Child = $CountChild->one();
            //Itemgroup::find()->where(['GroupID' => $id])->one();

            if($Child->Child != 0)
            {

                $data.= $this->JsonLoadChild($Child->Child);
                $data.= $this->JsonGetGroup($Child->Child);

                return $data;
            }else {
                return NULL;
            }


        }else {
            return NULL;
        }


    }

    public function JsonGetGroup($id)
    {
        $model = PropertyHasGroup::find()
        ->where(['itemgroup' => $id])
        ->orderBy(['priority' => SORT_ASC])
        ->all();

        $Json = '';
        foreach ($model as $value) {

            $Json.= $this->JsonLoadProperty($value);

        }

        return $Json;
    }


    public function JsonLoadProperty($has)
    {

        // 2. Find Property
        $model = Property::find()->where(['id' => $has->property])->all();


        $div = '<li class="form-group ">';
        foreach ($model as $key => $value) {

            $PtValue = $this->JsonGetProperty($value->id,$_GET['param']['itemno']);

            //$div.= '';
            //$div.= '<li>';
            $div.= '<label class="control-label" for="'.$value->id.'"> '.$value->description.' </label>';
            $div.= '<div class="input-group">
                      <span class="input-group-addon" id="basic-addon1">
                        <i class="fa fa-arrows" aria-hidden="true" style="cursor:move;" data-key="'.$value->id.'" data-priority="'.$key.'" data-id="'.$has->id.'"></i>
                      </span>';
            $div.= '  <input type="text" class="form-control ew-ajax-save" id="'.$value->id.'" ew-pt-id="'.$value->id.'" value="'.$PtValue.'">';
            $div.= '</div>';
            //$div.= '</li>';
            $div.= '<div class="help-block"></div>
                    ';
        }
        $div.= '</li>';
        // $div.= "<script type=\"text/javascript\">
        //           $('#sortable".$id."').sortable({
        //         		update: function(e,ui){
        //                    var lis = $('#sortable".$id." i');
        //                    var ids = lis.map(function(i,el){
        //                    		return {id:el.dataset.key, priority:el.dataset.priority}
        //                     }).get();
        //                    console.log(JSON.stringify(ids));
        //         					//  $.ajax({
        //         					// 	 url:'index.php?r=itemgroup%2Fitemgroup%2Fview&id='+$('div.itemgroup-view').attr('data-key'),
        //         					// 	 type:'POST',
        //         					// 	 data:{ids:ids},
        //         					// 	 success:function(data){
        //         					// 		 $('.Navi-Title').html(data);
        //         					// 	 }
        //         					//  });
        //
        //
        //                  }
        //         	});
        //
        //         </script>";
        return $div;
    }

    public function JsonGetProperty($id,$itemno)
    {
        $count = ItemsHasProperty::find()
        ->where(['Items_No' => $itemno])
        ->andwhere(['property_id' => $id])
        ->orderBy(['priority' => SORT_ASC]);

        if($count->count() >0){

            $iHas = $count->one();
            // $iHas = ItemsHasProperty::find()
            // ->where(['Items_No' => $itemno])
            // ->andwhere(['property_id' => $id])
            // ->one();


            return $iHas->values;
        }else {
            return NULL;
        }

        //return $_GET['param']['itemno'];


    }



    protected static function findItems($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function removeImage($Path,$image)
    {
        $Product    = '../../frontend/web/images/product/'.$Path;
        $trash      = '../../frontend/web/images/trash-file/'.$Path;

        if ((file_exists($Product.'/'.$image)) && ($image <> ''))
        {
            if (!file_exists($trash))
            {
                mkdir($trash, 0775, true);
            }
            //rename($Product.'/'.$image, $trash.'/'.$image);
            //unlink($Path.'/'.$image);
        }

    }

    public function moveImage($image,$oldpath,$newpath)
    {
        if (!file_exists($newpath)){
            mkdir($newpath, 0775, true);
        }

        if ((file_exists($oldpath.'/'.$image)) && ($image <> '')){

            //if(rename($oldpath.'/'.$image, $newpath.'/'.$image)) // Cancel 26/06/2020
            if(copy($oldpath.'/'.$image, $newpath.'/'.$image)){
                chmod($newpath.'/'.$image, 0775);
                return true;
            }else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

    }

    public function DateThai($strDate,$format)
    {
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("n",strtotime($strDate));
        $strDay= date("j",strtotime($strDate));
        $strHour= date("H",strtotime($strDate));
        $strMinute= date("i",strtotime($strDate));
        $strSeconds= date("s",strtotime($strDate));
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$strMonth];
        //return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
        switch ('dd-MM-YY') {
            case 'value':
                return "$strDay $strMonthThai $strYear";
                break;

            default:
                return "$strDay $strMonthThai $strYear";
                break;
        }

    }



    public function getOS() { 

   

        $os_platform  = "Unknown OS Platform";
    
        $os_array     = array(
                              '/windows nt 10/i'      =>  'Windows 10',
                              '/windows nt 6.3/i'     =>  'Windows 8.1',
                              '/windows nt 6.2/i'     =>  'Windows 8',
                              '/windows nt 6.1/i'     =>  'Windows 7',
                              '/windows nt 6.0/i'     =>  'Windows Vista',
                              '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                              '/windows nt 5.1/i'     =>  'Windows XP',
                              '/windows xp/i'         =>  'Windows XP',
                              '/windows nt 5.0/i'     =>  'Windows 2000',
                              '/windows me/i'         =>  'Windows ME',
                              '/win98/i'              =>  'Windows 98',
                              '/win95/i'              =>  'Windows 95',
                              '/win16/i'              =>  'Windows 3.11',
                              '/macintosh|mac os x/i' =>  'Mac OS X',
                              '/mac_powerpc/i'        =>  'Mac OS 9',
                              '/linux/i'              =>  'Linux',
                              '/ubuntu/i'             =>  'Ubuntu',
                              '/iphone/i'             =>  'iPhone',
                              '/ipod/i'               =>  'iPod',
                              '/ipad/i'               =>  'iPad',
                              '/android/i'            =>  'Android',
                              '/blackberry/i'         =>  'BlackBerry',
                              '/webos/i'              =>  'Mobile'
                        );
    
        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
                $os_platform = $value;
    
        return $os_platform;
    }
    
    public function getBrowser() {
    
        $browser        = "Unknown Browser";
    
        $browser_array = array(
                                '/msie/i'      => 'Internet Explorer',
                                '/firefox/i'   => 'Firefox',
                                '/safari/i'    => 'Safari',
                                '/chrome/i'    => 'Chrome',
                                '/edge/i'      => 'Edge',
                                '/opera/i'     => 'Opera',
                                '/netscape/i'  => 'Netscape',
                                '/maxthon/i'   => 'Maxthon',
                                '/konqueror/i' => 'Konqueror',
                                '/mobile/i'    => 'Handheld Browser'
                         );
    
        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
                $browser = $value;
    
        return $browser;
    }
}
