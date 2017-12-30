<?php 
/**
 * @package orgelman/functions
 * @link    https://github.com/orgelman/functions/
 * @author  Tobias Jonson <git@orgelman.systmes>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

if(get_included_files()[0]==__FILE__){header("HTTP/1.1 403 Forbidden");die('<h1 style="font-family:arial;">Error 403: Forbidden</h1>');} 

class orgelmanText {
   private $key           = "asdyfhasugfwauisOUGSAD8hdfaegu";
   private $secondKey     = "ajlgsd97asd";
   
   private $replaceNew    = array("Bi01010000NA","Bi00101101NA","Bi01010011NA","Bi01000010NA","Bi00100000NA","Bi00100110NA","Bi00111011NA","Bi00111010NA","Bi00111101NA","Bi00100010NA","Bi00100111NA","Bi01100000NA","Bi00100101NA","Bi00100100NA");
   private $replaceOld    = array("+"           ,"-"           ,"/"           ,"\\"          ," "           ,"&"           ,";"           ,":"           ,"="           ,'"'           ,"'"           ,"`"           ,"%"           ,"$"           );
   private $saltMaxLength = 255;
   
   private $debug         = false;
   
   public function __construct() {
      $this->key        = $this->md5hash($this->key);
      $this->secondKey  = $this->md5hash($this->secondKey);
   }
   
   public function set_debug($bool) {
      $this->debug = $bool;
   }
   public function set_key($str) {
      $this->key        = $this->md5hash($str);
   }
   public function set_secondKey($str) {
      $this->secondKey  = $this->md5hash($str);
   }
   
   
   
   public function saveText_strip($str) {
      $str = str_replace(array("\n","\r"), array("",""), $str);
      $str = str_replace(array("<br>","<br />","<hr>","<hr />"), array("<br>","<br>","<hr>","<hr>"), $str);

      return trim($this->saveText(strip_tags($str,'<br><hr>')));
   }
   public function saveText_stripStand($str) {
      $str = str_replace(array("\n","\r"), array("",""), $str);
      $str = str_replace(array("<br>","<br />","<hr>","<hr />"), array("<br>","<br>","<hr>","<hr>"), $str);
      return trim($this->saveText(strip_tags($str,"<div><br><hr><ul><ol><li><p><h1><h2><h3><h4><h5><h6><small><span><big><sub><sup><i><em><b><strong><u><s><a><img><blockquote><cite><pre><code><samp><table><tbody><thead><tfoot><tr><td><th>")));
   }
   public function saveText_stripFull($str,$newline=0) {
      if(is_array($str)) {
         $new = "";
         foreach($str as $st) {
            $new .= " - ".$st;
         }
         $str = $new;
      }
      return trim($this->saveText(strip_tags($str),0,$newline));
   }
   public function saveText($str) {
      $str                 = str_replace($this->replaceNew,$this->replaceOld,$str);
      $str                 = str_replace(array("\n","\r","|"),array("<br>\n","","&#124;"),html_entity_decode(html_entity_decode($str)));
      $str                 = str_replace(array("<br>","<br />","<hr>","<hr />"), array("<br>","<br>","<hr>","<hr>"), $str);
      $str                 = preg_replace('/\s\s+/', ' ',$str);
      $str                 = addslashes(utf8_encode(htmlentities(preg_replace('!\s+!', ' ', str_replace(array("\n","\r"),"",$str )))));
      
      $taglist             = array("");
      preg_match_all("/<(\/|)([a-zA-Z1-9]{1,})(.*?)([a-zA-Z1-9]|\/| |'|\")>/xi", $str, $output_array);
      
      return trim($str);
   }
   public function loadText($str) {
      $str = stripslashes($str);
      $e = explode("|",$str,2);
      $str = $e[0];
      if(isset($e[1])) {
         $str = $e[1];
      } 
      
      $str = $this->decrypt($str,"savingtext");
      if(strpos($str, '|') !== false) {
         $tags          = explode("|",stripslashes($str));
         $str           = str_replace("&amp;","&",$this->decrypt($tags[0],"savetextfi"));
         $tag           = str_replace("&amp;","&",$this->decrypt($tags[1],"savetextse"));
                  
         $tags          = explode("|",stripslashes($tag));
         
         if(isset($tags[0])) {
            foreach($tags as $n => $tag) {
               if(strpos($tag, '/TA()XYX()GG/') !== false) {
                  $replace = explode("/TA()XYX()GG/",$tag);
                  
                  $str     = str_replace($replace[0],html_entity_decode($replace[1]),$str);
               }
            }
         }
      }
      $str             = preg_replace('!\s+!', ' ', $str);
      
      return $this->shortCode(html_entity_decode($str));
   }
   public function shortCode($str="",$over=false) {
      $debug = $this->debug;
      if($over) {
         $debug = false;
      }
      if(($str!="") && (!$debug)) {
         $str = preg_replace("/(console.(...)\((\"|\')(.*)(\"|\'))\);/ix", "", $str);
         $str = preg_replace("/<!--[^[if][^<![](.|\s)*?-->/", "", $str);
         $str = preg_replace('!/\*.*?\*/!s', '', $str);
         
         $pattern = "/<li[^>]*><\\/li[^>]*>/"; 
         //$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";  use this pattern to remove any empty tag
         $str = preg_replace($pattern, '', $str); 
         
         //$str = preg_replace('/\n\s*\n/', "\n", $str);
         //$str = preg_replace('/\s+/', ' ',str_replace(array("  ","\n","\r"),array(" ","",""),$str));
            
         //$str = preg_replace("/>\s*</isx", "><", $str);
         //$str = preg_replace("/;\s*/isx", ";", $str);
         //$str = preg_replace("/\/\/?\s*\*[\s\S]*?\*\s*\/\/?/ix", "",$str);
         $str = str_replace(array("\n","\r","console.log(obj);","console.log(data);"),"",preg_replace("/\s{2,}/", ' ',$str));
            
         $old= array('( ',' )','function ()',') {',', funct','if (','if(! ',' == ',' === '," != "," !== ",'", "',"', '",'(! ');
         $new= array('(' ,')' ,'function()' ,'){' ,',funct' ,'if(' ,'if(!' ,'=='  ,'==='  ,"!="  ,"!=="  ,'","' ,"','" ,'(!' );
           
         //$str = str_replace($old,$new,$str);
         
         
         //$str = preg_replace('/\s+/', ' ',$str);
         //$str = preg_replace("/^\s/", ' ',$str);
         //$str = trim($str);
      }
      $old= array('[[n]]');
      $new= array("\n"   );
      
      $str = str_replace($old,$new,$str);
      return stripslashes($str);
   }
   
   
   public function md5hash($str) {
      return str_replace($this->replaceOld,$this->replaceNew,md5($str));
   }
   
   public function encryptEmail($email) {
      return trim($email);
   }
   public function decryptEmail($emailhash) {
      if (strpos($emailhash,'@') !== false) {
         return $emailhash;
      } else {
         return $emailhash;
      }
   } 
   
   public function encrypt($string, $key="", $prekey="") {
      if($key=="") {
         $key = $this->secondKey;
      }
      $prekey  = str_replace(" ","",$prekey);
      $key     = str_replace(" ","",$key);
      $u = substr(substr($this->toAscii(md5(uniqid())), 0, (10-strlen(substr($this->toAscii($prekey), 0, 10)))).substr($this->toAscii($prekey), 0, 10), 0, 10);
      
      if($string!="") {
         $string = $string;
      }
      
      return $string; 
   }
   public function decrypt($encrypted, $key="") {
      return $encrypted;
   }
   public function generateHash($password, $user) {
      $user = strtolower($user);
      if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
         $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, $this->saltMaxLength);
         $hash = crypt($password, $salt);
      }
      return $hash;
   }
   public function verifyHash($password, $hashedPassword, $user) {
      $user = strtolower($user);
      
      return crypt($password, $hashedPassword) == $hashedPassword;
   }
   
   // Clean string URL
   public function toAscii($str, $replace=array(), $delimiter='-') {
      if( !empty($replace) ) {
         $str = str_replace((array)$replace, ' ', $str);
      }

      $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
      $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
      $clean = strtolower(trim($clean, '-'));
      $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

      return $clean;
   }
}
