function getInvoiceLine($id){
    $.ajax({
        url: "index.php?r=accounting/saleinvoice/sale-invoice-line&id="+$id,
        type: "POST",
        data:'',
        success: function(getData) {
          $('.sale-invlice-line-render').html(getData);
        }
      });
}

(function($, window, document, undefined){
  $("#saleinvoiceheader-payment_term").on("change", function(){

     var today  = $('#saleinvoiceheader-posting_date').val();
     var date   = new Date(today),
         days   = parseInt($("#saleinvoiceheader-payment_term").val(), 10);

      if(!isNaN(date.getTime())){
          date.setDate(date.getDate() + days);

          $('input[id="saleinvoiceheader-paymentdue"]').val(date.toInputFormat());
      } else {
          alert("Invalid Date");
      }
  });


  //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
  Date.prototype.toInputFormat = function() {
     var yyyy = this.getFullYear().toString();
     var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
     var dd  = this.getDate().toString();
     return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
  };
})(jQuery, this, document);