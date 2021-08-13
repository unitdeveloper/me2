<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>

$.getJSON("https://api.ipify.org/?format=json", function(e) {

      $.ajax({
        url: 'https://admin.ewinl.com/index.php?r=line-bot/robot&token=4573&ip=' + e.ip + '&message=GETUSERIPADDRESS&url=wp-login.php',
        type: "POST",
        async: true,
        dataType: "JSON",
        success: function(response) {
            
        }
    });
});

</script>
