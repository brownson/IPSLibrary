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

	IPSUtils_Include ("IPSWatering_Constants.inc.php",      "IPSLibrary::app::modules::IPSWatering");

	function get_WateringConfiguration() {
		return array(
			c_WateringCircle_1  =>	array(
				c_Property_Name           =>   'Rasen',
				c_Property_Component      =>   'IPSComponentSwitch_Dummy,12345',
				c_Property_Sensor         =>   '',
			),
			c_WateringCircle_2  =>	array(
				c_Property_Name           =>   'Vorgarten',
				c_Property_Component      =>   'IPSComponentSwitch_Dummy,12345',
				c_Property_Sensor         =>   '',
			),
			c_WateringCircle_3  =>	array(
				c_Property_Name           =>   'Tropfschlauch',
				c_Property_Component      =>   'IPSComponentSwitch_Dummy,12345',
				c_Property_Sensor         =>   '',
			),
	   );
	}
	
	define ("c_LogMessage_Count",			9);

?>