<style>

.slow-spin {
  -webkit-animation: fa-spin 6s infinite linear;
  animation: fa-spin 6s infinite linear;
}


.loading-page{
    position:absolute; 
    top:40%; 
    left:50%; 
    z-index:10;
}

@media (max-width: 767px) {
   .loading-page{
        top:30% !important;
        left:35% !important;
   }
}

</style>

<div class="loading-page">
    <div class="text-center">       
        <h1><i class="fab fa-react  slow-spin text-aqua fa-2x"></i></h1>
        <h5 class="text-gray">Loading... <span id="time-count" >0</span> <span>second</span></h5>
        
    </div>
</div>

 
<script> 

let i = 0; 
     
$(document).ready(function(){


    setInterval(() => {
        i = i+1;
        $('body').find('span#time-count').html(i);
    }, 100);

    setTimeout(() => { window.location="?r=site/indexs"; }, 1000); 

});
    
</script>