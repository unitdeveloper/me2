<?php
 
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'EWIN Live System';

 

?>
 


<!-- Content Wrapper. Contains page content -->
 
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        History
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">System Update</li>
      </ol>
    </section>

    <!-- Main content -->
<section class="content">
<div class="row">
	<div class="col-sm-3">
	<!-- Map box -->
	          <div class="box box-solid bg-light-blue-gradient">
	            <div class="box-header">
	              <!-- tools box -->
	              <div class="pull-right box-tools">
	                <button type="button" class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range">
	                  <i class="fa fa-calendar"></i></button>
	                <button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
	                  <i class="fa fa-minus"></i></button>
	              </div>
	              <!-- /. tools -->

	              <i class="fa fa-map-marker"></i>

	              <h3 class="box-title">
	                About
	              </h3>
	            </div>
	            <div class="box-body">
	              <div id="world-map" style="height: 450px; width: 100%;">
				  	<p>Ewin  </p>
					  <div style="margin-left:15px;"> เป็นระบบ ERP ที่ถูกพัฒนาขึ้นเมื่อวันที่ 01/04/2017  </div>
					  <div>โดยมีจุดประสงค์ เพื่อ รับออเดอร์จาก เซลล์ที่อยู่ตามพื้นที่ต่างจังหวัด(ทั่วประเทศ)</div>
					  <br>
					  <div>เข้าสู่กระบวนการ ในการจัดส่งสินค้า ตลอดจนการวางแผนการสั่งซ์้อ และการผลิต</div>
					  <br >
					  Feature การทำงานหลักๆของระบบ
					  <ul>
							<li>ระบบขาย</li>
							<li>ระบบซื้อ</li>
							<li>ระบบการผลิต(อัตโนมัติ)
								<ul>
									<li>Make to Order</li>
									<li>Make to Stock</li>
								</ul>
							</li>
					  </ul>
				  </div>
	            </div>
	            <!-- /.box-body-->
	            <div class="box-footer no-border">
	              <div class="row">
	                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
	                  <div id="sparkline-1"></div>
	                  <div class="knob-label"> </div>
	                </div>
	                <!-- ./col -->
	                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
	                  <div id="sparkline-2"></div>
	                  <div class="knob-label"> </div>
	                </div>
	                <!-- ./col -->
	                <div class="col-xs-4 text-center">
	                  <div id="sparkline-3"></div>
	                  <div class="knob-label"> </div>
	                </div>
	                <!-- ./col -->
	              </div>
	              <!-- /.row -->
	            </div>
	          </div>
	          <!-- /.box -->
	</div> 

	<div class="col-sm-9">
		<!-- Chat box -->
		<div class="box box-success">
		<div class="box-header">
		  <i class="fa fa-folder-o"></i>
		  <h3 class="box-title">EWINL Update History</h3>
		</div>
		<div class="box-body chat" id=" ">


		<!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew.png" alt="user image" class="online">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 9/06/2017 <i class="fa fa-clock-o"></i> 09:10</small>
		        System update
		      </a>
		      System update.
		    </p>
		    <div class="attachment">
		      <h4>ส่วนการปรับปรุง:</h4>

		      	 

		      	<p class="filename"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ปรับปรุง ระบบค้นหาลูกค้า ใน Sale Order</p>

		      		<p class="filename">&nbsp;&nbsp; - ยกเลิก Popup เลือกลูกค้าเมื่อ Create Sale Order</p>
		      		<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      		 
		      		 
		      	<br>

		      	 
		       
		    </div>
		    <!-- /.attachment -->
		  </div>
		  <!-- /.item -->	




		<!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew.png" alt="user image" class="online">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 8/06/2017 <i class="fa fa-clock-o"></i> 09:10</small>
		        System update
		      </a>
		      System update.
		    </p>
		    <div class="attachment">
		      <h4>ส่วนการปรับปรุง:</h4>

		      	 

		      	<p class="filename"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ปรับปรุง Sale Order ให้สามารถแก้ไข Sale Line ได้</p>

		      		<p class="filename">&nbsp;&nbsp; - สามารถคลิกแล้วแก้ ราคา,จำนวนได้</p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุงการเรียกดูข้อมูลส่วน Order List ให้แสดง วันที่/เวลา (สำหรับหน้าจอมือถือ)</p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุงการแสดงผล ส่วนของการสรุปยอด Sale Order [View]</p>
		      		 
		      		 
		      	<br>


		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ติดตั้ง ระบบค้นหาลูกค้า ใน Sale Order</p>

		      		<p class="filename">&nbsp;&nbsp; - หากเปิดใบงานใหม่ ระบบจะให้เลือกลูกค้าทันที</p>
		      		<p class="filename">&nbsp;&nbsp; - สามารถเลือกลูกค้าได้ในภายหลัง</p>
		      		 
		      		 
		      		 
		      	<br>

		      	 
		       
		    </div>
		    <!-- /.attachment -->
		  </div>
		  <!-- /.item -->	




		<!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew.png" alt="user image" class="online">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 7/06/2017 <i class="fa fa-clock-o"></i> 20:09</small>
		        System update
		      </a>
		      System update first time.
		    </p>
		    <div class="attachment">
		      <h4>ส่วนการปรับปรุง:</h4>

		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ติดตั้งระบบ Multiple Customer</p>
		      		<p class="filename">&nbsp;&nbsp; - Sales หลายคน สามารถดูแลลูกค้ารายเดียวกันได้ </p>

		      		 
		      	<br>

		      	<p class="filename"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ปรับปรุง Sale Order ให้รองรับ ​Multiple Sales</p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุงส่วนการเรียกใช้ Customer (เฉพาะบุคคล) </p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุงส่วนการเข้าถึงข้อมูลที่ไม่จำเป็น </p>
		      		 
		      		<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>

		      	<p class="filename"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ปรับปรุง Security And Rules</p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุงระบบ Role Based Access Control (RBAC)</p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับสิทธิ์การเข้าถึงเมนูต่างๆ </p>
		      		 
		      		<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>
		       
		    </div>
		    <!-- /.attachment -->
		  </div>
		  <!-- /.item -->	





		<!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew.png" alt="user image" class="online">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 6/06/2017 <i class="fa fa-clock-o"></i> 00:05</small>
		        System update
		      </a>
		      System update first time.
		    </p>
		    <div class="attachment">
		      <h4>ส่วนการปรับปรุง:</h4>

		      	<p class="filename"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i> ปรับปรุง ระบบขายสินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - ระบบแจ้งยกเลิกออเดอร์ </p>
		      		<p class="filename">&nbsp;&nbsp; - แสดงภาพสินค้า ในรายการสินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - Link แสดงรายละเอียดสินค้าที่สั่งซื้อ </p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุง ส่วนการแสดงผลใน มือถือ/Tablet </p>
		      		<p class="filename">&nbsp;&nbsp; - ปรับปรุง ส่วนการแสดงผลใน มือถือ/Tablet </p>
		      		<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>

		       
		    </div>
		    <!-- /.attachment -->
		  </div>
		  <!-- /.item -->







		  <!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew.png" alt="user image" class="online">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 5/06/2017 <i class="fa fa-clock-o"></i> 00:01</small>
		        เปิดใช้งาน 
		      </a>
		      เปิดใช้งานระบบ eWiNl อย่างเป็นทางการ
		    </p>
		    <div class="attachment">
		      <h4>ระบบที่เปิดใช้งาน:</h4>

		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ระบบจัดการสินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - ระบบจัดการหมวดหมู่สินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - ระบบจัดการรูปภาพ </p>
		      		<p class="filename">&nbsp;&nbsp; - การจัดชุดสินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - ระบบต้นทุนสินค้า </p>
		      		<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>

		      	<div class="pull-right">
			        <a href="index.php?r=SaleOrders/customer"   class="btn btn-primary btn-sm btn-flat" target="_blank">Open</a>
			    </div>
		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ระบบจัดการลูกค้า </p>
		      	<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>


		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ระบบจัดการพนักงานขาย </p>
		      	<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>

		      	<div class="pull-right">
			        <a href="index.php?r=SaleOrders/saleorder"   class="btn btn-primary btn-sm btn-flat" target="_blank">Open</a>
			    </div>
		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ระบบขายสินค้า </p>
			      	<p class="filename">&nbsp;&nbsp; - การเปิดออเดอร์ใหม่ </p>
			      	<p class="filename">&nbsp;&nbsp; - ระบบเลือกสินค้าอัฉริยะ </p>
			      	<p class="filename">&nbsp;&nbsp; - ระบบส่งอนุมัติออเดอร์</p>
			      	<p class="filename">&nbsp;&nbsp; - ระบบแสดงสถานะ </p>

			      	<p class="filename">&nbsp;&nbsp; - ระบบปริ๊นใบสั่งขาย PDF </p>
			      	<p class="filename">&nbsp;&nbsp; - ระบบปริ๊นใบส่งสินค้า PDF </p>

			      	

			      	
			      	<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>


		      	

		      	


		      	<p class="filename"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> โครงสร้างหลักอื่นๆ... </p>
		      	<p class="filename">&nbsp;&nbsp; - Register </p>
		      	<p class="filename">&nbsp;&nbsp; - Member Relation </p>
		      	<p class="filename">&nbsp;&nbsp; - etc. _____ </p>
		      	<br>

		      	


		      
		    </div>
		    <!-- /.attachment -->
		  </div>
		  <!-- /.item -->
		  <!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew-x.jpg" alt="user image" class="offline">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 30/05/2017 <i class="fa fa-clock-o"></i> 10:00</small>
		        Pre-Test
		      </a>
		      ทีมงานทำการทดสอบระบบร่วมกัน 
		    </p>
		  </div>
		  <!-- /.item -->
		  <!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew-x.jpg" alt="user image" class="offline">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 24/05/2017 <i class="fa fa-clock-o"></i> 10:09</small>
		        Server Prepair
		      </a>
		      การติดตั้งความพร้อมของ Server สำหรับการใช้งาน eWiNL

		    </p>
		  </div>
		  <!-- /.item -->
		  <!-- chat item -->
		  <div class="item">
		    <img src="images/logo-ew-x.jpg" alt="user image" class="offline">

		    <p class="message">
		      <a href="#" class="name">
		        <small class="text-muted pull-right"> 01/04/2017 <i class="fa fa-clock-o"></i> 08:00</small>
		        Develop
		      </a>
		      ทีมพัฒนา ทำการวิเคราะห์และเขียนโปรแกรม
		    </p>
		  </div>
		  <!-- /.item -->
		</div>
		<!-- /.chat -->

		</div>
		<!-- /.box (chat box) -->	
	</div>
</div>
</section>


