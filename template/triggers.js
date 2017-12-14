<script>
   $(function() {
      console.log("Document is loaded");
   });
   $(document).ready(function () {
      console.log("DOM (Document Object Model) is ready");
   });
   $(document).scroll(function () {
      console.log("Scrolling detected");
   });
   $(document).mouseleave(function () {
      console.log("Mouse left document");
   });
   $(document).mouseenter(function () {
      console.log("Mouse entered document");
   });
   $(document).ajaxStart(function() {
      console.log("Ajax is started");
   });
   $(document).ajaxStop(function() {
      console.log("Ajax is stoped");
   });
   
   $(window).resize(function(){
      console.log("Window was resized");
   });
   $(window).on("blur", function() {
      console.log("Tab changed");
   });
   $(window).on("focus", function() {
      console.log("Tab in focus");
   });

   window.onbeforeunload = function(e) {
      console.log("User is leaving site");
      var dialogText = "";
      e.returnValue = dialogText;
      return dialogText;
   };
</script>
