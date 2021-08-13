 
    //eRar-AI
    
    $(function(){
        $('button[id="search-btn"]').on('click',function(e){

            $('.ew-speech-ai').html('<audio src="" class="speech" hidden></audio>');
            e.preventDefault();
            var text= $('input.ew-all-search').val();
            text = encodeURIComponent(text);
            var wellcom = "e-Win ยินดีต้อนรับ' ขณะ'นี้ยังไม่เปิดให้ใช้งานระบบการค้นหาค่ะ";
            wellcom = encodeURIComponent(wellcom);
            var url = "http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=ewinl&q="+wellcom+"-&tl=th";
            $('audio').attr('src',url).get(0).play();
        })
    });

 