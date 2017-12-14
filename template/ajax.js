$(function() {
   var eurl = "js/ajax/error.php";
   var url  = "js/ajax/session.php";
   var data = "test=test";
   
   $.ajax({
      url:url,
      global: false,
      crossDomain: true,
      cache: false,
      dataType: "text",
      type:"POST",
      data:data,
      success:function(data){
         console.log(data);
         try { 
            var obj = JSON.parse(data); 
            console.log(obj);
            if(obj && typeof obj === "object"){ 
                //KOD
            } 
         } 
         catch(error) { 
            $.ajax({global:false,crossDomain:true,type:"POST",url:eurl,data:"error=jQueryError&url="+url+"&data="+data+"&response="+encodeURIComponent(data)}); 

            console.log("Error: " + error);
            console.log(data);
            var x = "hideId" + Math.floor((Math.random() * 1000000) + 1);
            $("#jsError").append("<div id='"+x+"' class='alert alert-warning'><i class='fa fa-exclamation-circle'></i> jQuery error<br>"+error+"</div>").slideDown();
            setTimeout(function() {$("#"+x).slideUp()},60000);
         } 
      },
      error: function(XMLHttpRequest,textStatus,errorThrown) { 
         $.ajax({global:false,crossDomain:true,type:"POST",url:eurl,data:"error=AjaxError&url="+url+"&data="+data}); 
      
         console.log(textStatus + "Error: " + errorThrown + ".");
         var x = "hideId" + Math.floor((Math.random() * 1000000) + 1);
         $("#jsError").append("<div id='"+x+"' class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> Ajax error<br>"+error+"</div>").slideDown();
         setTimeout(function() {$("#"+x).slideUp()},60000);
      }
   }).always(function(){
      console.log("Ajax");
   });
});
