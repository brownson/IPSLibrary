<?
	/**@addtogroup ipscomponent
	 * @{
 	 *
	 * @file          IPSModule.class.php
	 * @author        Andreas Brauneis
	 *
	 */

  /**
    * @class IPSModuleException
    *
    * Definiert eine Module Exception
    *
    */
	class IPSModuleException extends Exception {
	}

	/**
    * @class IPSModule
    *
    * Definiert ein IPSModule Object, das als Basis Object für die Ansteuerung aller IPSLibrary Module dient
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSModule {

		private static function IncludeClassByName($className) {
			$pos = strpos($className, '_');
			if ($pos===false) {
				return;
			}
			$componentType = substr($className, 0, $pos);
			$componentType = str_replace('IPSModule','IPSComponent', $componentType);
			IPSUtils_Include ($className.'.class.php', 'IPSLibrary::app::core::IPSComponent::'.$componentType);
		}
	
		/**
		 * @public
		 *
		 * Generiert anhand des übergebenen Parameter Strings ein IPSModule Object.
		 *
		 * @param array $params Parameter Array (erster Parameter entspricht Klassenname, alle anderen werden als Parameter übergeben)
		 * @return IPSModule IPSModule Object
		 */
		public static function CreateObjectByArray($params) {
			if (count($params)>0) {
				self::IncludeClassByName($params[0]);
			}
			switch (count($params)) {
				case 1:
					$object = new $params[0]();
					break;
				case 2:
					$object = new $params[0]($params[1]);
					break;
				case 3:
					$object = new $params[0]($params[1],$params[2]);
					break;
				case 4:
					$object = new $params[0]($params[1],$params[2],$params[3]);
					break;
				case 5:
					$object = new $params[0]($params[1],$params[2],$params[3],$params[4]);
					break;
				case 0:
					throw new IPSComponentException('Empty Array, at least a Class Name is needed to create an Object');
				default:
					throw new IPSComponentException('Too many Parameters, Currently a maximum of 4 Parameters is allowed for an Object Constructor');
			}
			return $object;
		}

		/**
		 * @public
		 *
		 * Generiert anhand des übergebenen Parameter Strings ein IPSModule Object.
		 *
		 * @param string $params Parameter String (Liste mit Comma separierten Parameter Werten)
		 * @return IPSModule IPSModule Object
		 */
		public static function CreateObjectByParams($params) {
			$params = explode(',', $params);
			return self::CreateObjectByArray($params);
		}


	}

	/** @}*/
?>