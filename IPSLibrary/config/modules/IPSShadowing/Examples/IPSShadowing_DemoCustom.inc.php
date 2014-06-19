<?
	/*
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

	/**@addtogroup ipsshadowing_configuration
	 * @{
	 * 
	 * Es gibt derzeit 4 Callback Methoden, diese ermöglichen es unter anderem eine eigene Programm Logik einzubinden und bevor bzw. nach der Aktivierung 
	 * einer Beschattung bestimmte Aktionen auszuführen.
	 * 
	 * Funktionen:
	 * - function IPSShadowing_IsWorkingDay() 
	 * - function IPSShadowing_BeforeActivateShutter($deviceId ,$command) 
	 * - function IPSShadowing_AfterActivateShutter($deviceId ,$command) 
	 * - function IPSShadowing_ProgramCustom($DeviceId, $isDay)
	 * 
	 * @file          IPSShadowing_DemoCustom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 11.03.2012<br/>
	 *
	 * Callback Methoden für IPSShadowing
	 *
	 */

	/** 
	 * Diese Funktion wird von der Beschattungs Steuerung aufgerufen um zu ermitteln ob der aktuelle Tag ein Werktag ist
	 *
	 *  @return boolean TRUE für Werktag, FALSE für Wochenende oder Feiertag. Bei NULL erfolgt die Ermittlung durch die Steuerung (Feiertage werden dabei NICHT berücksichtigt)
	 *
	 */
	function IPSShadowing_IsWorkingDay() {
		return null;
	}
	 
	/** 
	 * Diese Funktion wird vor dem aktivieren eines Beschattungs Devices ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $deviceId  ID of current Shadowing Device (means Program.IPSLibrary.data.modules.IPSShadowing.Devices.MyCurrentDevice)
	 *   @param boolean $command Beschattungs Befehl, der gerade ausgeführt wird (mögliche Werte: define ("c_Movement_Down","c_Movement_Stop" und "c_Movement_Up")
	 *
	 */

	function IPSShadowing_BeforeActivateShutter($deviceId, $command) {
		return true;
	}


	/** 
	 * Diese Funktion wird nach dem aktivieren eines Beschattungs Devices ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $deviceId  ID of current Shadowing Device (means Program.IPSLibrary.data.modules.IPSShadowing.Devices.MyCurrentDevice)
	 *   @param boolean $command Beschattungs Befehl, der gerade ausgeführt wird (mögliche Werte: define ("c_Movement_Down","c_Movement_Stop" und "c_Movement_Up")
	 *
	 */

	function IPSShadowing_AfterActivateShutter($deviceId, $command) {

	}

	/** 
	 *
	 * Function will be called by the Program Timer when no manual Invervention has occured, present
	 * is not activated and Automatic is enabled.
	 * Shadowing can be activated as described below, to prevent other Programs from being executed,
	 * the function has to return true;
	 *
	 * Executing a Program:
	 *   $device = new IPSShadowing_Device($DeviceId);
	 *   $device->MoveByProgram(c_ProgramId_Opened);    // Open 
	 *   $device->MoveByProgram(c_ProgramId_Closed);    // Close a Shutter
	 *   $device->MoveByProgram(c_ProgramId_Dimout);    // Close a Jalousie
	 *   $device->MoveByProgram(c_ProgramId_Shadowing); // Shadowing a Jalousie
	 *
	 * Parameters:
	 *   @param integer $DeviceId  ID of current Shadowing Device (means Program.IPSLibrary.data.modules.IPSShadowing.Devices.MyCurrentDevice)
	 *   @param boolean $isDay Value, indicating Day or Night
	 *
	 */

   function IPSShadowing_ProgramCustom($DeviceId,$isDay,&$programInfo) {
 	
		return false;
	}
	
	/** @}*/

?>