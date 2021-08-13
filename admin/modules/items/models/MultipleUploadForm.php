<?PHP
namespace admin\modules\items\models;

use yii;
use yii\base\Model;


class MultipleUploadForm extends Model
{
    /**
     * @var UploadedFile[] files uploaded
     */
    public $files;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        [['files'], 'file', 'extensions' => 'jpg', 'mimeTypes' => 'image/jpeg', 'maxFiles' => 10, 'skipOnEmpty' => false],
        ];
    }



    public function ImageIsset($ItemGroup,$images)
    {
        if($images==NULL){
            return 'images/nopic.png';
        }else {
            $whitelist = array(
                '127.0.0.1',
                '::1'
            );
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                return "//assets.ewinl.com/images/product/".$ItemGroup."/thumbnail/".$images;
            }else{
                return "../../../app-assets/images/product/".$ItemGroup."/thumbnail/".$images;
            }
            
        }
    }

    public function ImageIssetView($ItemGroup,$images)
    {
        if($images==NULL){
            return 'images/nopic.png';
        }else {
            //return Yii::$app->urlManagerFrontend->baseUrl."/images/product/".$ItemGroup."/thumbnail/".$images;
            return "//assets.ewinl.com/images/product/".$ItemGroup."/thumbnail/".$images;
        }
    }



    public function ImageRender($model){

        $thumbnail_img = [
                            $model->thumbnail1,
                            $model->thumbnail2,
                            $model->thumbnail3,
                            $model->thumbnail4,
                            $model->thumbnail5
                         ];
        $img = '';
        foreach ($thumbnail_img as $value) {
             # code...
            if($value!=NULL){
                $whitelist = array(
                    '127.0.0.1',
                    '::1'
                );
                if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                    $img.= '<img src="//assets.ewinl.com/images/product/'.$model->ItemGroup.'/thumbnail/'.$value.'"   class="img-thumbnail img-product"/>'."\r\n";
                }else{
                    $img.= '<img src="../../../app-assets/images/product/'.$model->ItemGroup.'/thumbnail/'.$value.'"   class="img-thumbnail img-product"/>'."\r\n";
                }
                
            }

         }
         return $img;
    }
}
