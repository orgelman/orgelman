<?php 
/**
 * @package orgelman/functions
 * @link    https://github.com/orgelman/functions/
 * @author  Tobias Jonson <git@orgelman.systmes>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

require_once("../autoload.src.php");

$_Orgelman = new orgelmanFunctions();

if($_Orgelman->Verify) {
   echo "1";
} else {
   die("Outdated");
}

echo $_Orgelman->toAscii("It works!");
