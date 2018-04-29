<?php 
/**
 * @package orgelman/orgelman
 * @link    https://github.com/orgelman/orgelman/
 * @author  Tobias Jonson <git@orgelman.systmes>
 * @license https://opensource.org/licenses/MIT The MIT License
 */

$ds  = DIRECTORY_SEPARATOR;
$dir = __dir__.$ds;

if(file_exists($dir."include".$ds."autoload.php")) {
   require_once ($dir."include".$ds."autoload.php"); 
}
if(file_exists($dir."src".$ds."class.debug.php")) {
   require_once ($dir."src".$ds."class.debug.php"); 
}
if(file_exists($dir."src".$ds."class.orgelmanFunctions.php")) {
   require_once ($dir."src".$ds."class.orgelmanFunctions.php"); 
}
if(file_exists($dir."src".$ds."class.text.php")) {
   require_once ($dir."src".$ds."class.text.php"); 
}
if(file_exists($dir."src".$ds."class.sql.php")) {
   require_once ($dir."src".$ds."class.sql.php"); 
}
?>
