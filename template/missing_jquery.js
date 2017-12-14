var jQueryScriptOutputted = false;
initJQuery();
function initJQuery() {
   var eurl = "ajax/error.php";
   if (typeof(jQuery) == "undefined") {
      console.log("jQuery undefined");
      if(!jQueryScriptOutputted) {
         jQueryScriptOutputted = true;
         
         var script  = document.createElement("script");
         script.src  = "//code.jquery.com/jquery-3.2.0.min.js";
         script.type = "text/javascript";
         document.getElementsByTagName("head")[0].appendChild(script);
      }
      setTimeout(initJQuery, 50);
   } else {
      console.log("jQuery defined");
      $(function() {
         try { 
            //KOD
         }
         catch(error) { 
            $.ajax({global:false,crossDomain:true,type:"POST",url:eurl,data:"error=jQueryError&url="+url+"&data="+data+"&response="+encodeURIComponent(data)}); 
            
            console.log("Error: " + error);
            var x = "hideId" + Math.floor((Math.random() * 1000000) + 1);
            $("#jsError").append("<div id='"+x+"' class='alert alert-warning'><i class='fa fa-exclamation-circle'></i> jQuery error</div>").slideDown();
            setTimeout(function() {$("#"+x).slideUp()},60000);
         }
      });
   }
}
