<?php 
/**
 * @package orgelman/functions
 * @link    https://github.com/orgelman/functions/
 * @author  Tobias Jonson <git@orgelman.systmes>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

$ds  = DIRECTORY_SEPARATOR;
$dir = __dir__.$ds;
if(file_exists($dir."src".$ds."class.orgelmanFunctions.php")) {
   require_once ($dir."src".$ds."class.orgelmanFunctions.php"); 
}
if(file_exists($dir."src".$ds."class.text.php")) {
   require_once ($dir."src".$ds."class.text.php"); 
}
if(file_exists($dir."src".$ds."class.browser.php")) {
   require_once ($dir."src".$ds."class.browser.php"); 
}
if(file_exists($dir."src".$ds."class.sql.php")) {
   require_once ($dir."src".$ds."class.sql.php"); 
}
?>
