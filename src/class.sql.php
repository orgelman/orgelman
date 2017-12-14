<?php if(get_included_files()[0]==__FILE__){header("HTTP/1.1 403 Forbidden");die('<h1 style="font-family:arial;">Error 403: Forbidden</h1>');} 
/**
 * @package orgelman/functions
 * @link    https://github.com/orgelman/functions/
 * @author  Tobias Jonson <git@orgelman.systmes>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * -- 
 *
 * define("SQL_HOST"       , "host");
 * define("SQL_PORT"       , "port");
 * define("SQL_NAME"       , "database");
 * define("SQL_USERNAME"   , "username");
 * define("SQL_PASSWORD"   , "password");
 * define("SQL_PREFIX"     , "");
 * define("SQL_SOCKET"     , "");
 */
class orgelmanSQL {
   public  $DBh            = "";
   
   private $SQL_HOST       = "";
   private $SQL_USERNAME   = "";
   private $SQL_PASSWORD   = "";
   private $SQL_NAME       = "";
   private $SQL_PREFIX     = "";
   private $SQL_SOCKET     = "";
   
   public function __construct($SQL_HOST="",$SQL_USERNAME="",$SQL_PASSWORD="",$SQL_NAME="",$SQL_PREFIX="",$SQL_SOCKET="") {
      //Setting SQL constants
      if(defined("SQL_HOST")) {
         $this->SQL_HOST      = constant("SQL_HOST");
      } else {
         $this->SQL_HOST      = $SQL_HOST;
         define("SQL_HOST",$this->SQL_HOST);
      }
      if(defined("SQL_USERNAME")) {
         $this->SQL_USERNAME  = constant("SQL_USERNAME");
      } else {
         $this->SQL_USERNAME  = $SQL_USERNAME;
         define("SQL_USERNAME",$this->SQL_USERNAME);
      }
      if(defined("SQL_PASSWORD")) {
         $this->SQL_PASSWORD  = constant("SQL_PASSWORD");
      } else {
         $this->SQL_PASSWORD  = $SQL_PASSWORD;
         define("SQL_PASSWORD",$this->SQL_PASSWORD);
      }
      if(defined("SQL_NAME")) {
         $this->SQL_NAME      = constant("SQL_NAME");
      } else {
         $this->SQL_NAME      = $SQL_NAME;
         define("SQL_NAME",$this->SQL_NAME);
      }
      if(defined("SQL_PREFIX")) {
         $this->SQL_PREFIX    = constant("SQL_PREFIX");
      } else {
         $this->SQL_PREFIX    = $SQL_PREFIX;
         define("SQL_PREFIX",$this->SQL_PREFIX);
      }
      if(defined("SQL_SOCKET")) {
         $this->SQL_SOCKET    = constant("SQL_SOCKET");
      } else {
         $this->SQL_SOCKET    = $SQL_SOCKET;
         define("SQL_SOCKET",$this->SQL_SOCKET);
      }
      
      $this->DBh = $this->StartDBConnection();
   }
   public function __destruct() {
      $this->StopDBConnection();
   }
   
   public function StartDBConnection() {
      $DBh = false;
      if((isset($DBh)) && ($DBh!="")) { 
            
      } else {
         $DBh = "";
         $DBh = @mysqli_connect(SQL_HOST,SQL_USERNAME,SQL_PASSWORD,SQL_NAME) or die("SQL ERROR: Connection error: ".__LINE__);
         if (mysqli_connect_errno()) {
            die("SQL ERROR: Connection error: ".__LINE__);
         }
         if($DBh=="") {
            die("SQL ERROR: Connection error: ".__LINE__);
         }
      }
      $this->DBh = $DBh;
      return $DBh;
   }
   public function StopDBConnection(){
      if((isset($this->DBh)) && ($this->DBh!="")) { 
         @mysqli_close($this->DBh);
      }
   }
   private function wash($q) {
      echo $q."\n";
      return $q;
   }
   private function verify($q,$allow,$caller) {
      if(strtolower(substr(trim($q), 0, strlen("drop"))) === strtolower("drop")) {
         die("SQL ERROR: ".$q."<br>\nCan not drop table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
      }
      if(strtolower(substr(trim($q), 0, strlen("trunkate"))) === strtolower("trunkate")) {
         die("SQL ERROR: ".$q."<br>\nCan not trunkate table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
      }
      if(strtolower(substr(trim($q), 0, strlen("alter"))) === strtolower("alter")) {
         die("SQL ERROR: ".$q."<br>\nCan not alter table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
      }
      if((strtolower(substr(trim($q), 0, strlen($allow))) === strtolower($allow)) && ((substr(trim($q), -1) === ';'))) {
         return $q;
      } else {
         if(strtolower(substr(trim($q), 0, strlen($allow))) !== strtolower($allow)) {
            die("SQL ERROR: ".$q."<br>\nString type not match<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         } elseif(substr(trim($q), -1) !== ';') {
            die("SQL ERROR: ".$q."<br>\nString end not match<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         } else {
            die("SQL ERROR: ".$q."<br>\nUnknown Error<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         }
         return false;
      }
      return false;
   }
   public function insert($variable) {
      $old = array("  ");
      $new = array(" ");
      $variable = $this->DBh->real_escape_string(str_replace($old,$new,trim(urldecode($variable))));
      
      return $variable; 
   }
   public function getRequest($str="") {
      if(isset($_REQUEST[$str])) {
         $str = $this->SQL_insert($_REQUEST[$str]);
      } else {
         return false;
      }
      return $str; 
   }
   public function SQL($q,$allow="select") { 
      $arr     = array();
      $sel     = "Select";
      $prefix  = "[[DB]]";
      $caller = debug_backtrace()[0];
      
      if((is_array($q)) || (is_object($q))) {
         $query = "SELECT \n";
         $i=0;
         foreach($q as $v => $qu) {
            if(strpos($qu, $prefix) == false) {
               die("SQL ERROR: Missing ".$prefix." Called: ". $caller["file"]." [".$caller["line"]."]");
            }
            if($qu!="") {
               $strpos = strpos($qu,$prefix);
               if(substr($qu,($strpos-1),1) == "`") {
                  $qu = str_replace($prefix,constant("SQL_NAME")."`.`".constant("SQL_PREFIX")."",$qu);
               } else {
                  $qu = str_replace($prefix,"`".constant("SQL_NAME")."`.`".constant("SQL_PREFIX")."",$qu);
               }
            }
            $this->verify($qu,$allow,$caller);
            if($i!=0) {
               $query .= ",\n";
            }
            $query .= "   (".trim($qu,";").") as `".$v."`";
            $i++;
         }
         $q = $query.";";
      } else {
         if(strpos($q, $prefix) == false) {
            die("SQL ERROR: Missing ".$prefix." Called: ". $caller["file"]." [".$caller["line"]."]");
         }
         if($q!="") {
            $strpos = strpos($q,$prefix);
            if(substr($q,($strpos-1),1) == "`") {
               $q = str_replace($prefix,constant("SQL_NAME")."`.`".constant("SQL_PREFIX")."",$q);
            } else {
               $q = str_replace($prefix,"`".constant("SQL_NAME")."`.`".constant("SQL_PREFIX")."",$q);
            }
         }
         $this->verify($q,$allow,$caller);
      }
      if($q!="") {
         $q = $this->wash($q);
         $this->verify($q,$allow,$caller);
         if(isset($this->DBh)) {} else {
            $this->DBh = $this->StartDBConnection();
         }
         $MySQLi[0]["Result"] = $this->DBh->query($q);

         if(strtolower(substr($q, 0, strlen($sel))) === strtolower($sel)) {
            if(!$MySQLi[0]["Result"]) {
               die("SQL ERROR: ".$q."<br>\nSQL ERROR: ".$this->DBh->error);
            } elseif($MySQLi[0]["Result"]->num_rows>0) {
               while($MySQLi[0]["Rows"]=$MySQLi[0]["Result"]->fetch_object()){
                  $arr[] = $MySQLi[0]["Rows"];
               }
               return $arr;
            } elseif($MySQLi[0]["Result"]->num_rows==0) {
               return false;
            }
         } else {
            if(!$MySQLi[0]["Result"]) {
               die("SQL ERROR: ".$q."<br>\nSQL ERROR: ".$this->DBh->error);
               return false;
            }
            return true;
         }
      }
      return false;
   }
   public function SQLBackup($tables = '*', $path = '', $title = '') {
      $return = "";
      if($tables!='*') {
         $tables = "`".constant("SQL_PREFIX").$tables."`";
      }
      if($tables == '*') {
         $tables = array();
         $result = $this->DBh->query('SHOW TABLES');
         while($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
         }
      } else {
         $tables = is_array($tables) ? $tables : explode(',',$tables);
      }

      foreach($tables as $table) {
         $result = $this->DBh->query('SELECT * FROM '.$table);
         if(!is_bool($result)) {
            $num_fields = mysqli_num_fields($result);

            $return.= '-- phpMyAdmin SQL Dump'."\n";
            $return.= '-- http://www.phpmyadmin.net'."\n"."\n";
            $return.= '-- Generation Time '.date("Y-m-d H:i:s e")."\n"."\n";
            $return.= '-- orgelman systems'."\n"."\n";
            $return.= '--'."\n";
            $return.= '-- Database: '.constant("SQL_NAME").''."\n";
            $return.= '--'."\n"."\n";
            $return.= '-- --------------------------------------------------------'."\n";
            $return.= '--'."\n";
            $return.= '-- Table structure for table '.$table."\n";
            $return.= '--'."\n"."\n";

            $return.= 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = mysqli_fetch_row($this->DBh->query('SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";

            $return.= '--'."\n";
            $return.= '-- Dumping data for table '.$table."\n";
            $return.= '--'."\n"."\n";

            for ($i = 0; $i < $num_fields; $i++) {
               while($row = mysqli_fetch_row($result)) {
                  $return.= 'INSERT INTO '.$table.' VALUES(';
                  for($j=0; $j < $num_fields; $j++) {
                     $row[$j] = addslashes($row[$j]);
                     $row[$j] = str_replace("\n","\\n",$row[$j]);
                     if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                     if ($j < ($num_fields-1)) { $return.= ','; }
                  }
                  $return.= ");\n";
               }
            }
            $return.="\n\n\n";
         }
      }
      if($path=="") {
         $path = __DIR__;
      }
      if($title!="") {
         $title = $title." ";
      }
      $paths = rtrim($path,"/").'/'.$this->toAscii($title).'db-backup-'.$this->toAscii(date("YmdHis")).'-'.(md5(implode(',',$tables))).'.sql';
      $handle = fopen($paths,'w+');
      fwrite($handle,$return);
      fclose($handle);
      return $paths;
   }
   
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
