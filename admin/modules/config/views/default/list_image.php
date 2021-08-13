<div id="render-images"></div>
<?php
$Yii = "Yii"; 
$js =<<<JS
  
   
   const renderImage = (data) => {
       let html = '<div  class="row">';
       let i = 0;
        data.map(model => {
            i++;
            let name = model.file.split('assets.ewinl.com/images/product');
     
            html+= `<div class="col-xs-2 text-center mt-2"> 
                        <div style="position:absolute; left:20px;">` + i + `</div>
                        <div style="position:absolute; right:20px;">
                          <i class="far fa-trash-alt text-danger pointer remove-file" data-src="` +model.path+ `"></i>
                        </div>
                        <img src="` +model.file+ `" title="` +model.name+ `" alt="` +name[1]+ `"  class="img-thumbnail img-responsive" style="max-height: 118px;"/> 
                         
                    </div>`;
        })

        html+= ' </div>';

        $('#render-images').html(html);
   }
  
  const listFile = (obj) => {

    fetch("?r=config/default/list-file", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
      })
      .then(res => res.json())
      .then(response => { 
     
        renderImage(response);
        
      })
      .catch(error => {
          console.log(error);
      });
  }
  
 
  $(document).ready(function(){
    listFile({now:true});
    
  });
   

  $('body').on('click', 'i.remove-file', function(){
    let el  = $(this);
    let src = $(this).attr('data-src');

    if(confirm("Confirm ?")){
      fetch("?r=config/default/remove-file", {
        method: "POST",
        body: JSON.stringify({src:src}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
      })
      .then(res => res.json())
      .then(response => {
        if(response.status===200){
          el.closest('div.col-xs-2').remove();
        }
        
      })
      .catch(error => {
          console.log(error);
      });
    }

  });


JS;
$this->registerJS($js);
?>
