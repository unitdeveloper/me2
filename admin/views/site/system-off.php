<!DOCTYPE html>
<html lang="en">
<head>
	<title>System Shut Down</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
    <script src="https://use.fontawesome.com/09e178150f.js"></script>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/shutdown-util.css">
	<link rel="stylesheet" type="text/css" href="css/shutdown-main.css">
<!--===============================================================================================-->
</head>
<body>
<?php 

$setTime = "2019-12-02 11:40:00";

date_default_timezone_set("Asia/Bangkok");

$remaining = strtotime($setTime) - time();
$d = floor($remaining / 86400);
$h = floor(($remaining % 86400) / 3600);
$i = floor(($remaining % 3600) / 60);
$s = ($remaining % 60);

//echo "There are $d days and $h hours left $i : $s";


// เปิดการใช้งานอัตโนมัติ เมื่อถึงเวลาที่กำหนด

$dateTime = new DateTime(date($setTime));
if ($dateTime->diff(new DateTime)->format('%R') == '+') {
	$model = \common\models\Options::find()
    ->where(['table_name'       => 'options'])
    ->andWhere(['table_case'    => 'system_status'])
    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
    ->one();

	$model->enabled 	= 1;
	$model->modify_date = date('Y-m-d H:i:s');
	$model->user_id     = Yii::$app->user->identity->id;
    //$model->save(false);
	
}
?>
	
	<div class="bg-img1 size1 flex-w flex-c-m p-t-55 p-b-55 p-l-15 p-r-15" style="background-image: url('images/bg01.jpg');">
		<div class="wsize1 bor1 bg1 p-t-175 p-b-45 p-l-15 p-r-15 respon1">
			<div class="wrappic1">
				<img src="images/icon/ewinl-.png" width="150" alt="LOGO">
			</div>

			<p class="txt-center m1-txt1 p-t-2 p-b-68">
				ขออภัย !<br />
				Our website is under construction
				<br />
				กำลังปรับปรุงระบบ เพื่อความถูกต้องของข้อมูล
			</p>
			

			<div class="wsize2 flex-w flex-c hsize1 cd100">
				<div class="flex-col-c-m size2 how-countdown" style="display:none;">
					<span class="l1-txt1 p-b-9 days"><?=$d?></span>
					<span class="s1-txt1">Days</span>
				</div>

				<div class="flex-col-c-m size2 how-countdown">
					<span class="l1-txt1 p-b-9 hours"><?=$h?></span>
					<span class="s1-txt1">Hours</span>
				</div>

				<div class="flex-col-c-m size2 how-countdown">
					<span class="l1-txt1 p-b-9 minutes"><?=$i?></span>
					<span class="s1-txt1">Minutes</span>
				</div>

				<div class="flex-col-c-m size2 how-countdown">
					<span class="l1-txt1 p-b-9 seconds"><?=$s?></span>
					<span class="s1-txt1">Seconds</span>
				</div>
			</div>
			<p class="txt-center m1-txt1 p-t-3 p-b-68">
				ระบบจะพร้อมใช้งานอีกครั้ง (ตามเวลาที่กำหนด)
			</p>
			<form class="flex-w flex-c-m contact100-form validate-form p-t-55" style="visibility: hidden;">
				<div class="wrap-input100 validate-input where1" data-validate = "Email is required: ex@abc.xyz">
					<input class="s1-txt2 placeholder0 input100" type="text" name="email" placeholder="Your Email">
					<span class="focus-input100"></span>
				</div>

				
				<button class="flex-c-m s1-txt3 size3 how-btn trans-04 where1">
					Get Notified
				</button>
				
			</form>

			<p class="s1-txt4 txt-center p-t-10"  style="visibility: hidden;">
				I promise to <span class="bor2">never</span> spam
			</p>
			
		</div>
	</div>



	

<!--===============================================================================================-->	
	<script src="css/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="css/vendor/bootstrap/js/popper.js"></script>
	<script src="css/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="css/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="css/vendor/countdowntime/moment.min.js"></script>
	<script src="css/vendor/countdowntime/moment-timezone.min.js"></script>
	<script src="css/vendor/countdowntime/moment-timezone-with-data.min.js"></script>
    <script src="css/vendor/countdowntime/countdowntime.js"></script>

	<script>
		$('.cd100').countdown100({
			/*Set Endtime here*/
			/*Endtime must be > current time*/
			endtimeYear: 2019,
			endtimeMonth: 12,
			endtimeDate: 2,
			endtimeHours: 8,
			endtimeMinutes: 30,
			endtimeSeconds: 0,
			timeZone: "" 
			// ex:  timeZone: "America/New_York"
			//go to " http://momentjs.com/timezone/ " to get timezone
		});
	</script>
<!--===============================================================================================-->
	<script src="css/vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/shutdown-main.js"></script>

</body>
</html>
 