<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <title>คำนวนเวลาเข้า-ออก</title>
    <style> 
        @media print {
            @page {
                size: A4 landscape;
            }
 
            body{
                background-color:#fff !important;
            }
        }
    </style>
  </head>
  <body style="background-color:#ccc;">
    <div class="container" style="background-color:#fff; height:100%;"> 
        <div class="content">
            <div class="row">
                <div class="col-lg-6">
                    
                    <form action="" method="POST" role="form">
                        <legend>Data</legend>
                    
                        <div class="form-group">                          
                            <input type="file" class="form-control" id="" name="dat-file" placeholder="Input field">
                        </div>
                    </form>

                </div>
                <div class="col-12 ">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Raw data</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Date</a>
                            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Employee</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active renders" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">B</div>
                        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">C</div>
                    </div>
                </div>
            </div>
        </div>

    <script>


   // Sort time example
            //var times = ['01:00 am', '06:00 pm', '12:00 pm', '08:20 am', '08:30 am','12:00 pm', '12:01 pm','17:32 pm', '03:00 am', '12:00 am'];
            

            let calculateTime  = (times) => {
            times.sort(function (a, b) {
                return new Date('1970/01/01 ' + a) - new Date('1970/01/01 ' + b);
            });

            console.log(times);

            // หาคาบเช้า
            let startTime           = '05:00';      // อนุญาตให้เข้างาน _________________[1] 
            let minimumWork         = '60';         // เวลาทำงานน้อยที่สุด (นาที)
            let morningRangStart    = '08:30';      // เริ่มนับกะเช้า
            let morningRangEnd      = '12:00';      // สิ้นสุดกะเช้า
            let goOutSign           = '11:00';      // อนุญาตให้เลิกงานได้
            let pm_allow_in         = '11:01';      // อนุญาตให้เข้างานช่วงบ่าย
            let pm_out              = '17:30';      // เวลาเลิกงาน
            let pm_allow_out        = '15:00';      // เริ่มให้เลิกงานได้
            let pm_allow_out_to     = '23:30';      // เลิกได้จนถึงเวลา x


            // หาช่วงเวลาเช้า                                _________________[2]
            // เวลาที่เข้าแรกสุด (เริ่มจากเวลาที่อนุญาตให้เข้างาน)    _________________[3]
            // เวลาที่ออกแรกสุด                               _________________[4]

            let AtMorningIn   = (times) => {
                let AtMorning = [];
                times.map(el => {
                    if((el > startTime && el <= goOutSign)){                         //_________________[1][2]
                        AtMorning.push(el);
                    }
                })
                return AtMorning;
            }
            let ThisMorning= AtMorningIn(times);

            console.log(ThisMorning);

            

            let firstTimeAtMorning   = (AtMorning) => {
                let newTime = '';
                AtMorning.sort(function (a, b) {
                    return new Date('1970/01/01 ' + b) - new Date('1970/01/01 ' + a);
                });
                AtMorning.map(el => {                                               //_________________[3]
                    newTime = el;
                })
                return newTime;
            }
            console.log(firstTimeAtMorning(ThisMorning));
            

            let AtMorningOut   = (times) => {                                       //_________________[4]
                let OutGoing = [];
                times.map(el => {
                    if(el >= goOutSign){                         
                        OutGoing.push(el);
                    }
                })
                
                OutGoing.sort(function (a, b) {
                    return new Date('1970/01/01 ' + b) - new Date('1970/01/01 ' + a);
                });

                let  newTime = '';
                OutGoing.map(el => {                                               
                    newTime = el;
                })
                return newTime;

            }


            let MorningOut= AtMorningOut(times);

        
            console.log(MorningOut);



            // Afternoon
            let AtAfternoonIn = (times) => {
                let Incomming = [];
                times.map(el => {
                    if(el >= pm_allow_in){          // เวลาที่เข้างานต้องมากกว่าหรือเท่ากับ เวลาที่อนุญาตให้เข้างาน
                        if(el > MorningOut){        // เวลาต้องมากกว่า "เวลาที่ออกในตอนเช้า"
                            Incomming.push(el);
                        }                    
                    }
                    
                })

                Incomming.sort(function (a, b) {
                    return new Date('1970/01/01 ' + b) - new Date('1970/01/01 ' + a);
                });

                let  newTime = '';
                Incomming.map(el => {                                               
                    newTime = el;
                })

                return newTime;
            }

            let AfternoonIn = AtAfternoonIn(times);
            console.log(AfternoonIn);


            let AtAfternoonOut = (times) => {
                let OutGoing = [];
                times.map(el => {
                    if(el >= pm_allow_out){          // 
                        if(el > AfternoonIn){        //
                            OutGoing.push(el);
                        }                    
                    }
                    
                })

                OutGoing.sort(function (a, b) {
                    return new Date('1970/01/01 ' + b) - new Date('1970/01/01 ' + a);
                });

                let  newTime = '';
                OutGoing.map(el => {                                               
                    newTime = el;
                })

                return newTime;
            }

            let AfternoonOut = AtAfternoonOut(times);
            console.log(AfternoonOut);
        }

        var times = ['04:50','08:20:32','08:20:31', '08:30','10:59','11:01','12:00', '12:01','17:32','17:35'];

        calculateTime(times);

        let timeFormat = (times) => {

            function addZero(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
                }

            function myFunction(times) {
                var d = times;
                 
                var h = addZero(d.getHours());
                var m = addZero(d.getMinutes());
                var s = addZero(d.getSeconds());
                return  h + ":" + m + ":" + s;
            }

            return myFunction(times);
        }




        
        $('body').on('change','input[type="file"]',function(e){
            readFile(e);

            let promise = readFileToArray(e);

            
            promise.then(el => {
                // Prifile
                let userId  = [];
                let calendar= [];
                let i       = 0;
                let users   = '<table class="table table-bordered">';
                Object.values(el).map(model => {
                    
                    if(model.id){

                       
                        
                        if (userId.indexOf(model.id) > -1) { // ถ้ามีอยู่แล้ว
                            
                        } else {                                
                                                                
                            i++;
                            userId.push(model.id);              // ถ้ายังไม่มี user ให้เพิ่มเข้าไปใน Array(เพื่อตรวจสอบในรอบถัดไป)

                            let morningIn = Object.values(el).filter(e =>{  // กรองวันทีึ่
                                                if(e.id===model.id) {
                                                    return model.date ;
                                                }
                                            });
                            //console.log(morningIn)
                            users+= '<tr><td width="50">'+i+'</td><td>'+model.id+'</td><td>'+morningIn[0].date+'</td></tr>';  // สร้างตารางแสดงผล
                        }
                        
                    }
                
                })
                 users+= '</table>';
                $('body').find('#nav-contact').html(users);



                // Contact
                let tmp_date = [];
                let xi       = 0;
                let render_date = '<table class="table table-bordered">';
                Object.values(el).map(model => {
                    
                    if(model.date){
                        //แยกเอาแค่วัน
                        let onlyDate = model.date.split(' ');
                        
                        if (tmp_date.indexOf(onlyDate[0]) > -1) { // ถ้ามีอยู่แล้ว
                            
                        } else {                                
                                                                
                            xi++;
                            tmp_date.push(onlyDate[0]);             

                            render_date+= '<tr><td width="50">'+xi+'</td><td>'+onlyDate[0]+'</td></tr>';
                           
                        }
                        
                    }
                
                })

                console.log(tmp_date);
                render_date+= '</table>';
                $('body').find('#nav-profile').html(render_date);


            });

            
        })

        let readFileToArray = (evt) => {

            return new Promise((resolve, reject) => {

                var files = evt.target.files;
                var file = files[0];           
                var reader = new FileReader();
    
                let arrayData = [];
                reader.onload = (event) => {
                    let data = event.target.result; 
                    var lines = data.split('\n');
                    
                    lines.map(item => {
                        var tabs = item.split('\t');
                        arrayData.push({
                            'id':tabs[0].trim(),
                            'date':tabs[1],
                            'a':tabs[2],
                            'b':tabs[3],
                            'c':tabs[4],
                            'd':tabs[5]
                        }); 
                    });
                    resolve(arrayData)
                    
                    
                }
                reader.readAsText(file)
                 
            });
        }

        let readFile = (evt) => {
            var files = evt.target.files;
            var file = files[0];           
            var reader = new FileReader();
            
            
            reader.onload = (event) => {
                //console.log(event.target.result);
                let data = event.target.result; 

                
                // var arr1 = [];
                // var arr2 = [];
                // var arr3 = [];
                // var arr4 = [];
                // var arr5 = []; // assuming 5 tabs
                var lines = data.split('\n');
                let html = '<table class="table table-bordered">';
                 
                let userId = [];
                
                let i = 0;
                lines.map(item => {
                    i++;
                    var tabs = item.split('\t');

                    let date2   = new Date(tabs[1]);

                     
                     
                        html += '<tr><td>'+i+ '</td><td>'+tabs[0]+ "</td><td>"+tabs[1]+ "</td><td>"+timeFormat(date2)+ "</td><td>"+tabs[2]+ "</td><td>"+tabs[3]+ "</td><td>"+tabs[4]+ "</td><td>"+tabs[5]+ "</td></tr>"; 
                    
                    //if(parseInt(tabs[0])===parseInt('1097')){
                        
                        
                        //console.log(timeFormat(date2));

                        //html += '<tr><td>'+timeFormat(date2)+'</td></tr>'; 


                        // let date2   = tabs[1].split(' ');
                        // let date2_d = date2[0].split('/');
                        // let date2_t = date2[1].split(':');

                        // let dateCompare = new Date(date2_d[2]+'-'+date2_d[1]+'-'+date2_d[0]+' 00:00:00');
                        // let firDate     = new Date('2019-02-16 00:00:00');

                        // // console.log(dateCompare);
                        // // console.log(firDate);

                        // if(dateCompare.getTime()==firDate.getTime()){
                            
                            
                            //html += '<tr><td>'+tabs[0]+ "</td><td>"+tabs[1]+ "</td><td>"+timeFormat(date2)+ "</td><td>"+tabs[3]+ "</td><td>"+tabs[4]+ "</td><td>"+tabs[5]+ "</td></tr>"; 

                            //console.log("0",tabs[0], "1",tabs[1], "2",tabs[2],"3", tabs[3],"4", tabs[4], "5", tabs[5]);
                        //} 
                    //} 
                    
                    
                    // arr1.push(tabs[0]);
                    // arr2.push(tabs[1]);
                    // arr3.push(tabs[2]);
                    // arr4.push(tabs[3]);
                    // arr5.push(tabs[4]);
                    
                });
                // // test two of the arrays after reading:
                // for (var i = 0; i < data.length; i++) {
                //     console.log(arr1[i], arr2[i]);
                // }; 
                html +="</table>";
                 

                $('.renders').html(html);
                
            }
            

            reader.readAsText(file)
        }

        
         
                
    </script>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>