<?
	/**
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */    

	 /**@addtogroup audiomax
 	 * @{
	 *
	 * @file          AudioMax_Room.class.php
	 * @author        Andreas Brauneis
	 *
	 */

   /**
    * @class AudioMax_Room
    *
    * Definiert ein AudioMax_Room Objekt
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class AudioMax_Room {

	   /**
       * @private
       * ID des AudioMax Server
       */
      private $instanceId;

     	/**
       * @private
       * Variablen Mapping der Befehle
       */
		private $functionMapping;

		/**
       * @public
		 *
		 * Initialisiert einen AudioMax Raum
		 *
	    * @param $instanceId - ID des AudioMax Server.
		 */
      public function __construct($instanceId) {
         $this->instanceId = $instanceId;
			$this->functionMapping = array(
			   AM_FNC_VOLUME 			=> AM_VAR_VOLUME,
			   AM_FNC_MUTE 			=> AM_VAR_MUTE,
			   AM_FNC_BALANCE 		=> AM_VAR_BALANCE,
			   AM_FNC_INPUTSELECT 	=> AM_VAR_INPUTSELECT,
			   AM_FNC_INPUTGAIN 		=> AM_VAR_INPUTGAIN,
			   AM_FNC_TREBLE 			=> AM_VAR_TREBLE,
			   AM_FNC_MIDDLE 			=> AM_VAR_MIDDLE,
			   AM_FNC_BASS 			=> AM_VAR_BASS,
			);
		}

		/**
       * @public
		 *
		 * Liefert den zugehörigen Variablen Namen für eine Message
		 *
	    * @param string $command Kommando
	    * @param string $function Funktion
		 */
		private function GetVariableName($command, $function) {
		   switch($command) {
		      case AM_CMD_ROOM:
		         $variableName = AM_VAR_ROOMPOWER;
		         break;
				case AM_CMD_AUDIO:
		      	$variableName = $this->functionMapping[$function];
		         break;
            default:
               throw new Exception('Unknown Command "'.$command.'", VariableName could NOT be found !!!');
		   }
	      return $variableName;
		}

		/**
       * @public
		 *
		 * Liefert den aktuellen Wert für eine Message
		 *
	    * @param string $command Kommando
	    * @param string $function Funktion
	    * @return string Wert
		 */
		public function GetValue ($command, $function) {
		   $name = $this->GetVariableName($command, $function);
			return GetValue(IPS_GetObjectIDByIdent($name, $this->instanceId));
		}

		/**
       * @public
		 *
		 * Setzt den Wert einer Variable auf den Wert einer Message
		 *
	    * @param string $command Kommando
	    * @param string $function Funktion
	    * @param string $value Wert
		 */
		public function SetValue ($command, $function, $value) {
		   $name        = $this->GetVariableName($command, $function);
	      $variableId  = IPS_GetObjectIDByIdent($name, $this->instanceId);
	      if (GetValue($variableId)<>$value) {
		 		SetValue($variableId, $value);
			}
		}
	}

	/** @}*/
?>