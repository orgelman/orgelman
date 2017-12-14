<!DOCTYPE HTML>
<html>
	<head>
      <link rel="stylesheet" href="//cdn.orgelman.systems/bootstrap/css/bootstrap.css" />
      <link rel="stylesheet" href="//cdn.orgelman.systems/font-awesome/css/font-awesome.css" />
      <script src="//code.jquery.com/jquery-3.2.0.min.js"></script>
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
   <body>
      <div id="cookieModal" class="modal fade" role="dialog">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">About Cookies</h4>
               </div>
               <div class="modal-body">
                  <h2>What is a cookie?</h2>
                  <p>A cookie is a small text file that the website you visit saves on your computer.</p>
                  <p>There are two types of cookies. The first type saves a file for a longer period of time on your computer. It is used, for example, by functions that tell what news has happened since the user last visited the current website. The other type
                     is called session cookies. While surfing on a site, these cookies are stored temporarily on your computer to keep track of, for example, what language you have chosen. Session cookies are not stored for a long time but are deleted when
                     you shut down your web browser. If you do not want to allow cookie storage on your computer you may switch off the function on your web browser.</p>
                  <p>Cookies are harmless for the computer, <strong>cannot</strong> contain viruses and are easy to delete.</p>
                  <p>If you want to delete any cookies that are already on your computer, please refer to the instructions for your file management software to locate the file or directory that stores cookies.</p>
                  <p>Information on deleting or controlling cookies is available at <a href="www.aboutcookies.org" target="_blank" rel="nofollow">www.AboutCookies.org</a>. Please note that by deleting our cookies or disabling future cookies you may not be able
                     to access certain areas or features of our site.</p>

                  <h2>Why you have to understand</h2>
                  <blockquote>
                     <p>The Cookie Law is a piece of privacy legislation that requires websites to get consent from visitors to store or retrieve any information on a computer, smartphone or tablet.</p>
                     <p>It was designed to protect online privacy, by making consumers aware of how information about them is collected and used online, and give them a choice to allow it or not.&nbsp;</p>
                  </blockquote>
                  <p>Read more at: <a href="https://www.cookielaw.org/" target="_blank">cookielaw.org</a></p>

            
                  <h2>Analytical tools for web statistics</h2>
                  <p>Our website uses Google Analytics to help us see how our visitors use the website. Following cookies are created by the website: _utmb is used to determine the number of page views and the length of the visit on the website, it is saved 30
                     minutes after its creation or update, _utmz is used to keep track of how visitors find their way to the website and is saved for six months, _utma is used to identify unique visitors and is saved for two years.</p>
                  <p>The information created by these cookies, by your usage of the website (including you IP-address), is forwarded to and stored by Google's servers in the US. Our purpose, with using Google Analytics, is to evaluate how the website is used,
                     to be able to improve its content, navigation and structure. Google may also transfer this information to a third party, if the law requires it, or when a third party processes the information for Google.</p>
                  <strong>No to web statistics?</strong>
                  <p><a href="//tools.google.com/dlpage/gaoptout" class="redirect" title="you can download the add-on from google" rel="nofollow">You can download the add-on from Google</a></p>
               </div>
               <div class="modal-footer">
                  <a class="btn btn-primary agreeCookies"   title="I agree"    rel="nofollow"><i class="fa fa-check"></i> I agree</a>
                  <a class="btn btn-danger disagreeCookies" title="I disagree" rel="nofollow"><i class="fa fa-times"></i> I disagree</a>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
            </div>
         </div>
      </div>
<?php if(isset($_COOKIE["ilikecookies"])) { } else { ?>
      <div style="background-color: rgba(17, 17, 17,0.9); z-index: 1000; position: fixed; padding: 15px 5px; width: 100%; left: 0px; box-shadow: 0px 0px 8px rgb(0, 0, 0); bottom: 0px;" id="aboutThecookies">
         <div style="width: 100%; margin: 0px auto; text-align: center; color: white;">
            <h4>This site uses cookies</h4>
            <hr>
            <div id="cookiesButtons">
               <a class="btn btn-primary agreeCookies" title="I agree" rel="nofollow"><i class="fa fa-check"></i> I agree</a>
               <a class="btn btn-danger disagreeCookies" title="I disagree" rel="nofollow"><i class="fa fa-times"></i> I disagree</a>
               <a class="btn btn-warning readCookies" rel="nofollow"><i class="fa fa-book" ></i> Read more...</a>
            </div>
            <div id="cookiesLoad" style="display: none;position: absolute;top: 0;bottom: 0;left: 0;right: 0;background-color: rgba(17, 17, 17, 0.9);">
               <i class="fa fa-circle-o-notch fa-spin cookieLoader" style="display:block;"></i>
            </div>
            <script>
               $(document).ready(function() {
                  $(".cookieLoader").css("margin-top",($(".cookieLoader").parent().height() - $(".cookieLoader").height())/2 + 'px' );
               });
            </script>
         </div>
      </div>
<?php } ?>
      <script>
         $(".readCookies").click(function() {
            $('#cookieModal').modal('show');
         });
         $(".agreeCookies").click(function() {
            $("#cookiesLoad").show();
            $('#cookieModal').modal('hide');
            //KOD
         });
         $(".disagreeCookies").click(function() {
            $("#cookiesLoad").show();
            $('#cookieModal').modal('hide');
            //KOD
         });
      </script>
   </body>
</html>
