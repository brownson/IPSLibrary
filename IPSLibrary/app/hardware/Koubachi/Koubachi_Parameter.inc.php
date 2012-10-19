<?php

	/**@defgroup koubachi_configuration Koubachi Parameter
	* @ingroup koubachi
	* @{
	*
	* Internes Konfigurations File fuer Koubachi.
	*
	* @file Koubachi_Parameter.inc.php
	* @author Dominik Zeiger
	* @version Version 0.1, 19.10.2012<br/>
	*
	*/

	namespace domizei\koubachi;
	
	define("PATH_WEBFRONT", "Visualization.WebFront.Hardware.Koubachi");
	define("PATH_DATA_PLANTS", "Program.IPSLibrary.data.hardware.Koubachi.Plants");
	define("PATH_DATA_DEVICES", "Program.IPSLibrary.data.hardware.Koubachi.Devices");
	
	define("API_BASE", "http://api.koubachi.com/v2/");
	define("API_DEVICES", API_BASE."user/smart_devices/");
	define("API_PLANTS", API_BASE."plants/");
	
	define("API_XML_DEVICE", "smart-device-device");
	define("API_XML_PLANT", "plant");
	
	// device xml tags
	define("API_XML_DEVICE_MAC_ADDRESS", "mac-address");
	define("API_XML_DEVICE_BATTERY_LEVEL", "virtual-battery-level");
	define("API_XML_DEVICE_LAST_TRANSMISSION", "last-transmission");
	define("API_XML_DEVICE_NEXT_TRANSMISSION", "next-transmission");
	
	// plant xml tags
	define("API_XML_PLANT_IS_DEVICE_ASSOCIATED", "has-smart-device-associated");
	define("API_XML_PLANT_ID", "id");
	define("API_XML_PLANT_LOCATION", "location");
	define("API_XML_PLANT_NAME", "name");
	define("API_XML_PLANT_LAST_UPDATE", "updated-at");
	define("API_XML_PLANT_LAST_MIST", "last-mist-at");
	define("API_XML_PLANT_LAST_WATER", "last-water-at");
	define("API_XML_PLANT_LAST_FERTILIZER", "last-fertilizer-at");
	define("API_XML_PLANT_NEXT_MIST", "next-mist-at");
	define("API_XML_PLANT_NEXT_WATER", "next-water-at");
	define("API_XML_PLANT_NEXT_FERTILIZER", "next-fertilizer-at");
	define("API_XML_PLANT_WATER_PENDING", "vdm-water-pending");
	define("API_XML_PLANT_WATER_LEVEL", "vdm-water-level");
	define("API_XML_PLANT_WATER_INSTRUCTION", "vdm-water-instruction");
	define("API_XML_PLANT_MIST_PENDING", "vdm-mist-pending");
	define("API_XML_PLANT_MIST_LEVEL", "vdm-mist-level");
	define("API_XML_PLANT_MIST_INSTRUCTION", "vdm-mist-instruction");
	define("API_XML_PLANT_TEMPERATURE_LEVEL", "vdm-temperature-level");
	define("API_XML_PLANT_TEMPERATURE_INSTRUCTION", "vdm-temperature-instruction");
	define("API_XML_PLANT_TEMPERATURE_ADVICE", "vdm-temperature-advice");
	define("API_XML_PLANT_TEMPERATURE_HINT", "vdm-temperature-hint");
	define("API_XML_PLANT_LIGHT_LEVEL", "vdm-light-level");
	define("API_XML_PLANT_LIGHT_INSTRUCTION", "vdm-light-instruction");
	define("API_XML_PLANT_LIGHT_ADVICE", "vdm-light-advice");
	define("API_XML_PLANT_LIGHT_HINT", "vdm-light-hint");
	
	function getDeviceVariableTypeMapping() {
		return array(
			API_XML_DEVICE_MAC_ADDRESS => array("string"),
			API_XML_DEVICE_BATTERY_LEVEL => array("float", true),
			API_XML_DEVICE_LAST_TRANSMISSION => array("datetime"),
			API_XML_DEVICE_NEXT_TRANSMISSION => array("datetime"),
		);
	}
	
	function getPlantVariableTypeMapping() {
		return array(
			API_XML_PLANT_IS_DEVICE_ASSOCIATED => array("boolean", true),
			API_XML_PLANT_ID => array("integer"),
			API_XML_PLANT_LOCATION => array("string"),
			API_XML_PLANT_NAME => array("string"),
			API_XML_PLANT_LAST_UPDATE => array("datetime"),
			API_XML_PLANT_LAST_MIST => array("datetime"),
			API_XML_PLANT_LAST_WATER => array("datetime"),
			API_XML_PLANT_LAST_FERTILIZER => array("datetime"),
			API_XML_PLANT_NEXT_MIST => array("datetime"),
			API_XML_PLANT_NEXT_WATER => array("datetime"),
			API_XML_PLANT_NEXT_FERTILIZER => array("datetime"),
			API_XML_PLANT_WATER_PENDING => array("boolean"),
			API_XML_PLANT_WATER_LEVEL => array("float", true),
			API_XML_PLANT_WATER_INSTRUCTION => array("string"),
			API_XML_PLANT_MIST_PENDING => array("boolean"),
			API_XML_PLANT_MIST_LEVEL => array("float", true),
			API_XML_PLANT_MIST_INSTRUCTION => array("string"),
			API_XML_PLANT_TEMPERATURE_LEVEL => array("float", true),
			API_XML_PLANT_TEMPERATURE_INSTRUCTION => array("string"),
			API_XML_PLANT_TEMPERATURE_ADVICE => array("string"),
			API_XML_PLANT_TEMPERATURE_HINT => array("string"),
			API_XML_PLANT_LIGHT_LEVEL => array("float", true),
			API_XML_PLANT_LIGHT_INSTRUCTION => array("string"),
			API_XML_PLANT_LIGHT_ADVICE => array("string"),
			API_XML_PLANT_LIGHT_HINT => array("string"),
		);
	}
?>