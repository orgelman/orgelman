<?php 
/**
 * @package orgelman/orgelman
 * @link    https://github.com/orgelman/orgelman/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

if(get_included_files()[0]==__FILE__){header("HTTP/1.1 403 Forbidden");die('<h1 style="font-family:arial;">Error 403: Forbidden</h1>');} 

class orgelmanDebug {
   private $foreground_colors = array();
   private $background_colors = array();
   
   private $dateFormat        = 'Y-m-d H:i:s e';
   private $indent            = 3;
   
   private $state             = true;
   private $size              = (1024 * 25);
   private $time              = (60 * 60);
   
   //set time
   public function __construct($start=null,$path="") {
      $this->scriptStartTime     = microtime(true);
      if($start!=null) {
         $this->scriptStartTime  = $start;
      }
      $this->log                 = array();
      
      // Set colors for cli
      $this->initColoredString();
      $this->uid                 = uniqid();
      
      define("DEBUG_ERRORHANDLER_START", $this->scriptStartTime);
      define("DEBUG_ERRORHANDLER_UID", $this->uid);
      define("DEBUG_ERRORHANDLER_INDENT", $this->indent);
      define("DEBUG_ERRORHANDLER_DATEFORMAT", $this->dateFormat);
      
      if($path=="") {
         $this->setPath(str_replace(array("/","\\"),constant("DIRECTORY_SEPARATOR"),"/".trim(__DIR__,"/")."/logs/"));
      } else {
         $this->setPath($path);
      }
      
      if(isset($_SERVER['argv'])) {
         foreach($_SERVER['argv'] as $arg) {
            $e=explode("=",$arg);
            if(count($e)==2) {
               $this->log('SETTING $_GET["'.$e[0].'"] = "'.$e[1].'"');
               $_GET[$e[0]] = $e[1];
            } else {
               $this->log('SETTING $_GET["'.$e[0].'"] = true');
               $_GET[$e[0]] = 1;
            }   
         }
      }
      
      $this->log("DEBUG FUNCTION CONSTRUCTED");
   }
   public function __destruct() {
      $this->log("DEBUG FUNCTION DESTRUCTED",true);
      if($this->is()) {
         $ds = constant("DIRECTORY_SEPARATOR");
         $path = $this->logPath.$ds.date("Y").$ds.date("m").$ds.date("d").$ds;
         echo $this->logPath;
         exit();
         if(!file_exists($path)) {
            mkdir($path, 0777, true);
         }
         $logFile = fopen($path.date("U").uniqid("_log_").".log", "w") or die("Unable to open file!");
         fwrite($logFile, $this->printDebug());
         fclose($logFile);
      }
      
      $this->rrmdir($this->logPath);
   }
   private function rrmdir($dir='',$i=0) {
      if(!defined("DEBUG_ERRORHANDLER_PATH")) {
         define("DEBUG_ERRORHANDLER_PATH",$dir);
      }
      if(file_exists(constant("DEBUG_ERRORHANDLER_PATH")."log.log")) {
         if(is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
               if ($object != "." && $object != "..") { 
                  if(is_dir($dir."/".$object)) {
                     $this->rrmdir($dir."/".$object,$i++);
                  } else {
                     if((file_exists($dir."/".$object)) && ($object!="log.log") && (time()-filemtime($dir."/".$object) > ($this->time)) && (pathinfo($dir."/".$object, PATHINFO_EXTENSION) == "log")) {
                        unlink($dir."/".$object); 
                     }
                  }
               } 
            }
            if(($dir != $this->logPath) && (count(glob($dir."/*")) === 0)) {
               rmdir($dir); 
            }
         } 
      }
   }

   public function setTime($time) {
      if((is_numeric($time)) && ($time>0)){
         $this->time = $time;
      }
      return $this->time;
   }
   public function setSize($size) {
      if((is_numeric($size)) && ($size>0)){
         $this->size = $size;
      }
      return $this->size;
   }
   public function setPath($path) {
      $path = str_replace(array("/","\\"),constant("DIRECTORY_SEPARATOR"),$path);
      if(file_exists($path)) {
         
      } else {
         mkdir($path, 0777, true);
      }
      $this->logPath = str_replace(array("/","\\"),constant("DIRECTORY_SEPARATOR"),$path);
      define("DEBUG_ERRORHANDLER_PATH",$this->logPath);
   }
   public function set($state=true) {
      if($state==true) {
         set_error_handler('Orgelman_debugRichErrorHandler');
         $this->state = $state;
      } else {
         set_error_handler('Orgelman_debugNiceErrorHandler');
         $this->state = $state;
      }
      return $this->state;
   }
   public function is() {
      return $this->state;
   }
   
   //Save in debug log
   public function log($log,$trunk=false) {
      if(!$this->is()) {
         return;
      }
      $debugStart = microtime(true);
      $date = "[".date($this->dateFormat)."] ";
      $uid  = str_repeat(' ',$this->indent)."[ID: ".$this->uid."]";
      
      //get backtrace
      $calls = debug_backtrace();
      $time = number_format((microtime(true) - $this->scriptStartTime), 6);
      $back = debug_backtrace()[0];
      
      if(strlen($date) > strlen($uid)) {
         $spac = str_repeat(' ',strlen($date));
      } else {
         $spac = str_repeat(' ',strlen($uid));
      }

      $str  = $this->getColoredString($date,"light_purple").$this->getColoredString('After '.$time.' seconds | FILE: '.$back["file"].' | LINE: '.$back["line"],"light_red")."\n";
      $i = 0;
      foreach($calls as $call) {
         $i++;
         if((isset($call["line"])) && (isset($call["function"])) && (isset($call["file"]))) {
            if($uid!="") {
               $spa = $this->getColoredString($uid,"light_purple").str_repeat(' ',strlen($date)-strlen($uid));
               $uid = "";
            } else {
               $spa = str_repeat(' ',strlen($date));
            }
            $str .= $spa.$this->getColoredString($i.': CALLED BY: function: '.$call["function"].'() | '.$call["file"].' | LINE: '.$call["line"],"light_red")."\n";
         }
      }
      $str .= $this->writeLog($log,strlen($spac));
      
      if((file_exists($this->logPath)) && (file_exists($this->logPath.'log.log'))) {
         $file = $this->logPath.'log.log';
         $current = file_get_contents($file);
         $current .= $str."\n\n";
         if($trunk) {
            $current = mb_substr($current,-($this->size));
         }
         file_put_contents($file, $current);
      }
      
      $str .= $spac.$this->getColoredString("Log written in: ".number_format((microtime(true) - $debugStart), 6)." seconds","light_red");
      
      $this->cli[] = $str;
      $this->log[] = "\n".preg_replace("/\\033(\S{2,5})m/", "", $str);
      return $str."\n";
   }
   
   public function writeLog($log,$space="",$ext="",$i=0) {
      if(!$this->is()) {
         return;
      }
      if(is_numeric($space)) {
         $ind  = str_repeat(' ',$space);
         if($i>0) {
            $j=0;
            while($j < $i) {
               $ind .= $this->getColoredString("|","light_blue").str_repeat(' ',($this->indent)-1);
               $j++;
            }
         }
      } 
      if($ext!=="") {
         $ext = $this->getColoredString($ext." => ","light_blue");
      }
      if(is_array($log)) {
         $str = $ind.$ext.$this->getColoredString('ARRAY with '.count($log).' elements:',"light_blue")."\n";
      } else if(is_object($log)) {
         $str = $ind.$ext.$this->getColoredString('OBJECT with '.count($log).' elements:',"light_blue")."\n";
      } else if(is_string($log)) {
         
      } else {
         $str = $ind.$ext."STRING ERROR";
      }

      if((is_object($log)) || (is_array($log))) {
         $i++;
         foreach($log as $key => $lo) {
            $str .= $this->writeLog($lo,$space,$key,$i);
         }
         $str .= $ind."\n";
      } else {
         $str = $ind.$ext.$this->getColoredString($log,"yellow")."\n";
      }
      
      return $str;
   }
   
   
   // Skriv in något som ska loggas som sparas i loggen
   public function printDebug() {
      global $globalErrorLog;
      ob_start();
      if($this->is()) {
         ?><!DOCTYPE html>
      <html><head><title>Debug log: <?php echo $this->uid; ?></title><link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"></head>
      <body>
         <div class="container">
         <div class="well">
            FILE : <?php echo $_SERVER["SCRIPT_FILENAME"]; ?><br>
            SAPI : <?php echo php_sapi_name(); ?><br>
<?php if((isset($_SERVER["HTTP_HOST"])) && (isset($_SERVER["REQUEST_URI"]))) { ?>
            URL  : <?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?><br>
<?php } ?>
            UID  : <?php echo $this->uid; ?><br>
<?php if(isset($_SERVER["REMOTE_ADDR"])) { ?>
            USER : <?php echo $_SERVER["REMOTE_ADDR"]; ?><br>
<?php } ?>
<?php if(isset($_SERVER["HTTP_USER_AGENT"])) { ?>
            AGENT: <?php echo $_SERVER["HTTP_USER_AGENT"]; ?><br>
<?php } ?>
         </div><?php
         $this->log("SAVING LOG, GOODBY!");
         ?>
         <p style="text-align:right;">Log printed: <?php echo date($this->dateFormat); ?></p>
         <h4 style="margin-bottom:0px;margin-top:1em;">Included classes:</h4>
         <pre><?php
         foreach(class_uses($this) as $classes) {
            echo "   ".$classes."\n";
         }?>
         </pre>
         <h4 style="margin-bottom:0px;margin-top:1em;">Included files:</h4>
         <pre><?php
         foreach(get_included_files() as $files) {
            echo "   ".$files."\n";
         }?>
         </pre>
<?php if(isset($_SERVER)) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">$_SERVER</h4>
         <pre><?php print_r($_SERVER);?></pre>
<?php } ?>
<?php if(isset($_GET)) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">$_GET</h4>
         <pre><?php print_r($_GET);?></pre>
<?php } ?>
<?php if(isset($_POST)) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">$_POST</h4>
         <pre><?php print_r($_POST);?></pre>
<?php } ?>
<?php if(isset($_SESSION)) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">$_SESSION</h4>
         <pre><?php print_r($_SESSION);?></pre>
<?php } ?>
<?php if(isset($_COOKIE)) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">$_COOKIE</h4>
         <pre><?php print_r($_COOKIE);?></pre>
<?php } ?>
<?php if((isset($globalErrorLog)) && (!empty($globalErrorLog))) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">Global error log:</h4>
         <pre><?php 
         foreach($globalErrorLog as $log) {
            echo $log."\n";
         }?>
         </pre>
<?php } ?>  
<?php if((isset($this->log)) && (!empty($this->log))) { ?>
         <h4 style="margin-bottom:0px;margin-top:1em;">Log:</h4>
         <pre><?php
         foreach($this->log as $log) {
            echo $log."\n";
         }?>
         </pre>
<?php } ?>  
<?php
      echo "Log complete, goodbye!";
      unset($this->log);
      $this->log = array();?>
      </div></body></html>
<?php
      }
      $content = ob_get_clean();
      return $content;
   }
   
   public function initColoredString() {
      // Set up shell colors
      $this->foreground_colors['black'] = '0;30';
      $this->foreground_colors['dark_gray'] = '1;30';
      $this->foreground_colors['blue'] = '0;34';
      $this->foreground_colors['light_blue'] = '1;34';
      $this->foreground_colors['green'] = '0;32';
      $this->foreground_colors['light_green'] = '1;32';
      $this->foreground_colors['cyan'] = '0;36';
      $this->foreground_colors['light_cyan'] = '1;36';
      $this->foreground_colors['red'] = '"0";31';
      $this->foreground_colors['light_red'] = '1;31';
      $this->foreground_colors['purple'] = '0;35';
      $this->foreground_colors['light_purple'] = '1;35';
      $this->foreground_colors['brown'] = '0;33';
      $this->foreground_colors['yellow'] = '1;33';
      $this->foreground_colors['light_gray'] = '0;37';
      $this->foreground_colors['white'] = '1;37';

      $this->background_colors['black'] = '40';
      $this->background_colors['red'] = '41';
      $this->background_colors['green'] = '42';
      $this->background_colors['yellow'] = '43';
      $this->background_colors['blue'] = '44';
      $this->background_colors['magenta'] = '45';
      $this->background_colors['cyan'] = '46';
      $this->background_colors['light_gray'] = '47';
   }
   // Returns colored string
   public function getColoredString($string, $foreground_color = null, $background_color = null) {
      $colored_string = "";

      // Check if given foreground color found
      if (isset($this->foreground_colors[$foreground_color])) {
         $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
      }
      // Check if given background color found
      if (isset($this->background_colors[$background_color])) {
         $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
      }

      // Add string and end coloring
      $colored_string .=  $string . "\033[0m";

      return $colored_string;
   }
}

function Orgelman_debugNiceErrorHandler($errno, $errstr, $errfile, $errline) {
   global $globalErrorLog;
   global $globalErrorArr;
   $errid = $errstr.$errfile.$errline;
   if((isset($globalErrorArr)) && (is_array($globalErrorArr))) { } else {
      $globalErrorArr = array();
   }
   
   $date = "[".date(constant("DEBUG_ERRORHANDLER_DATEFORMAT"))."] ";
   $uid  = str_repeat(' ',constant("DEBUG_ERRORHANDLER_INDENT"))."[ID: ".constant("DEBUG_ERRORHANDLER_UID")."]";
      
   //get backtrace
   $calls = debug_backtrace();
   $time = number_format((microtime(true) - constant("DEBUG_ERRORHANDLER_START")), 6);
   $back = debug_backtrace()[0];
      
   if(strlen($date) > strlen($uid)) {
      $spac = str_repeat(' ',strlen($date));
   } else {
      $spac = str_repeat(' ',strlen($uid));
   }
   
   $str  = $date.'After '.$time.' seconds | FILE: '.$back["file"].' | LINE: '.$back["line"]."\n";
   $i = 0;
   foreach($calls as $call) {
      $i++;
      if((isset($call["line"])) && (isset($call["function"])) && (isset($call["file"]))) {
         if($uid!="") {
            $spa = $uid.str_repeat(' ',strlen($date)-strlen($uid));
            $uid = "";
         } else {
            $spa = str_repeat(' ',strlen($date));
         }
         $str .= $spa.$i.': CALLED BY: function: '.$call["function"].'() | '.$call["file"].' | LINE: '.$call["line"]."\n";
      }
   }
   $str .= $spac.$errstr;
   
   if((defined("DEBUG_ERRORHANDLER_PATH")) && (!in_array($errid,$globalErrorArr))) {
      $ds = constant("DIRECTORY_SEPARATOR");
      $path = constant("DEBUG_ERRORHANDLER_PATH").$ds."error".$ds;
      if(!file_exists($path)) {
         mkdir($path, 0777, true);
      }
      $logFile = fopen($path.date("U").uniqid("_err_").".log", "w") or die("Unable to open file!");
      fwrite($logFile, $str);
      fclose($logFile);
   }
   $globalErrorArr[] = $errid;
   $globalErrorLog[] = strip_tags($str);
   if(($errno==E_USER_ERROR) || ($errno==E_STRICT) || ($errno==E_ALL)) {
      error_log(strip_tags($str));
      header("HTTP/1.1 500 Internal Server Error");
      die($str);
   } else {
      echo "\n".$errstr."\n";
   }
}
function Orgelman_debugRichErrorHandler($errno, $errstr, $errfile, $errline) {
   global $globalErrorLog;
   global $globalErrorArr;
   $errid = $errstr.$errfile.$errline;
   if((isset($globalErrorArr)) && (is_array($globalErrorArr))) { } else {
      $globalErrorArr = array();
   }
   
   $date = "[".date(constant("DEBUG_ERRORHANDLER_DATEFORMAT"))."] ";
   $uid  = str_repeat(' ',constant("DEBUG_ERRORHANDLER_INDENT"))."[ID: ".constant("DEBUG_ERRORHANDLER_UID")."]";
      
   //get backtrace
   $calls = debug_backtrace();
   $time = number_format((microtime(true) - constant("DEBUG_ERRORHANDLER_START")), 6);
   $back = debug_backtrace()[0];
      
   if(strlen($date) > strlen($uid)) {
      $spac = str_repeat(' ',strlen($date));
   } else {
      $spac = str_repeat(' ',strlen($uid));
   }
   
   $str  = $date.'After '.$time.' seconds | FILE: '.$back["file"].' | LINE: '.$back["line"]."\n";
   $i = 0;
   foreach($calls as $call) {
      $i++;
      if((isset($call["line"])) && (isset($call["function"])) && (isset($call["file"]))) {
         if($uid!="") {
            $spa = $uid.str_repeat(' ',strlen($date)-strlen($uid));
            $uid = "";
         } else {
            $spa = str_repeat(' ',strlen($date));
         }
         $str .= $spa.$i.': CALLED BY: function: '.$call["function"].'() | '.$call["file"].' | LINE: '.$call["line"]."\n";
      }
   }
   $str .= $spac.$errstr;
   
   if((defined("DEBUG_ERRORHANDLER_PATH")) && (!in_array($errid,$globalErrorArr))) {
      $ds = constant("DIRECTORY_SEPARATOR");
      $path = constant("DEBUG_ERRORHANDLER_PATH").$ds."error".$ds;
      if(!file_exists($path)) {
         mkdir($path, 0777, true);
      }
      $logFile = fopen($path.date("U").uniqid("_err_").".log", "w") or die("Unable to open file!");
      fwrite($logFile, $str);
      fclose($logFile);
   }
   $globalErrorArr[] = $errid;
   $str = "<pre>".$str."</pre>";
   $globalErrorLog[] = strip_tags($str);
   if(($errno==E_USER_ERROR) || ($errno==E_STRICT) || ($errno==E_ALL)) {
      error_log(strip_tags($str));
      header("HTTP/1.1 500 Internal Server Error");
      die($str);
   } else {
      echo "\n\n\n".$str."\n\n\n";
   }
}
