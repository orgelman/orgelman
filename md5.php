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
   echo md5_file($dir."src".$ds."class.orgelmanFunctions.php"); 
} else {
   echo "0";
}

?>
