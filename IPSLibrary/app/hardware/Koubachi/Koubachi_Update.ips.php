<?

	/**@defgroup koubachi_interface Koubachi Update Script
	* @ingroup koubachi
	* @{
	*
	* Update script for Koubachi, which is executed regularly in order to get new data from the koubachi api.
	*
	* @file Koubachi_Update.inc.php
	* @author Dominik Zeiger
	* @version Version 0.1, 15.10.2012<br/>
	*/

	namespace domizei\koubachi;
	
	IPSUtils_Include ("Koubachi.inc.php",            "IPSLibrary::app::hardware::Koubachi");
	Koubachi_Update();
	
?>