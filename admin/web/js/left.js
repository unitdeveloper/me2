var myImg = "images/icon/andriod/ic_launcher.png";
var jobs = 0;

const alertList = (data) => {
  let li  = ``;
  data.alertRaws.map(model => {
    li+= `<li  data-no="` + model.no +`" class="show-duplicate">
            <a href="#"><i class="fa fa-warning text-yellow"></i>` + model.no +` (`+ model.dup +`)</a>
          </li>`;

  })
  let ul = `<ul class="menu">`+li+`</ul>`;
      
  $('body').find('li.notifications-menu').find('.dropdown-menu > li.notice-body').html(ul)
}

const  GenerateData = (path) => {
  fetch("index.php?r=ajax/" + path + '&current=true', {
    method: "POST",
    body: JSON.stringify({ param: { menu: "saleorder" } }),
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
  .then(res => res.json())
  .then(response => {
      $(".ew-data-saleorder").fadeIn().html(response.newjob);
      $(".ew-data-approve").fadeIn().html(response.appData);

      $(".warning-amount").html(response.alert);
      $(".warning-task").html(response.task);
      $(".warning-message").html(response.message);

      if (response.newjob > 0) {
        $("body").find("#alert-menu-marketing").html(response.newjob);

        if (jobs > 0) {
          if (jobs != response.newjob) {
            $("#easyNotify").easyNotify({
              title: "New",
              options: {
                body: response.newjob + " Jobs",
                icon: myImg,
                lang: "en-US"
                //onClick: myFunction
              }
            });
            // Auto Refresh
            //location.reload();
          }
        }
        jobs = response.newjob;
      } else {
        $(".ew-alert-saleorder").remove();
        $("body").find("#alert-menu-marketing").html("");
      } 
      
      alertList(response);
      
  })
  .catch(error => {
      console.log(error);        
  });

  // $.ajax({
  //   url: "index.php?r=ajax/" + module,
  //   type: "POST",
  //   data: { param: { menu: "saleorder" } },
  //   async: true,
  //   dataType: "JSON",
  //   success: function(response) {
      
  //   }
  // });
}

$(document).ready(function() {
  //GenerateData("count-menu");
  setTimeout(function() {
    GenerateData("count-menu");
  }, 3000);
  

  setInterval(function() {
    GenerateData("count-menu");
  }, 300000);
  
});




$('body').on('change','select[name="sale_people_on_right_menu"]',function(){
  changeSalePeople(this);
})

let changeSalePeople = (e) => {
  let id = $(e).val();
  fetch("?r=ajax/change-sale-people", {
    method: "POST",
    body: JSON.stringify({ id: id }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      console.log(response)
    })
    .catch(error => {
      console.log(error);
    });

}


  // ตรวจบิลซ้ำ
  const checkShowDuplicateBill = (obj,callback) => {
    fetch("?r=accounting/ajax/show-duplicate-bill", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        if(response.status===200){
            callback(response);                
        }else{
            $.notify({
                // options
                icon: "fas fa-exclamation-circle",
                message: response.error
            },{
                // settings
                placement: {
                    from: "top",
                    align: "center"
                },
                type: "error",
                delay: 5000,
                z_index: 3000
            });                  
        }
        
    })
    .catch(error => {
        console.log(error);
    });
  }

$('body').on('click', '.show-duplicate', function(){
  
  let no  = $(this).closest('li').attr('data-no');

  $('#modal-show-duplicate-document').modal('show');
$('#modal-show-duplicate-document').find('.modal-body').html('<div class="center">Loading...</div>')
  checkShowDuplicateBill({no:no}, res => {
    let body = ``;  
    res.raws.map((model,key) => {
        let uri = model.status == 'Open' 
                      ? '?r=accounting%2Fsaleinvoice%2Fupdate&id=' + model.id
                      : '?r=accounting%2Fposted%2Fposted-invoice&id=' + btoa(model.id);

        let bg  = model.status == 'Open' 
                      ? 'bg-warning'
                      : 'bg-success';
        body+= `
              <tr data-key="` +model.id+ `" class="` +bg+ `">
                <td>` +(key+1)+ `</td>
                <td><a href="`+uri+`" target="_blank" >` +model.no+ `</a></td>
                <td>` +model.cust+ `</td>
                <td>` +model.status+ `</td>
                <td class="text-center">
                  <a href="`+uri+`" target="_blank" ><i class="fa fa-eye"></i></a>
                </td>
              </tr>
        `;
    })
    let table = `
                <table class="table font-roboto table-bordered table-hover">
                    <thead> 
                      <tr>
                        <th class="bg-dark" style="width:50px;">#</th>
                        <th class="bg-gray">No</th>
                        <th class="bg-gray">Customer</th>
                        <th class="bg-gray">Status</th>
                        <th class="bg-gray"></th>
                      </tr>
                    </thead>
                    <tbody>`+body+`</tbody>
                </table>
    `;

    setTimeout(() => {
      $('#modal-show-duplicate-document').find('.modal-body').html(table);
    }, 800);
    
  })
  
})