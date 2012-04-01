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

	/**@defgroup ipswatering_configuration IPSWatering Konfiguration
	 * @ingroup ipswatering
	 * @{
	 *
	 * @file          IPSWatering_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 11.03.2012<br/>
	 *
	 * Konfigurations File für IPSWatering
	 *
	 */

	IPSUtils_Include ("IPSWatering_Constants.inc.php",      "IPSLibrary::app::modules::IPSWatering");

	/**
	 *
	 * Definition der Bewässerungs Kreise
	 * Die Konfiguration erfolgt in Form eines Arrays, für jeden Bewässerungs Kreis wird ein Eintrag im Array erzeugt.
	 * 
	 * Der Eintrag "c_Property_Name" spezifiziert den Namen des Bewässerungskreises, der im WebFront und in den Log's angezeigt
	 * wird.
	 *
	 * Der Eintrag "c_Property_Component" spezifiziert die Hardware, es kann jeder "Switch" Component String Konstruktor
	 * angegeben werden. Detailiertere Informationen kann man auch im core Modul IPSComponent finden.
	 *
	 * Der Eintrag "c_Property_Sensor" ist optional, über in ist es möglich einen Regensensor in die Steuerung einzubinden. 
	 * Die Angabe erfogt in Form des Pfades zur Variable oder durch die ID selbst und muss die Regenmenge beinhalten, die zum 
	 * Vergleich mit den Einstellungen hergenommen wird.
	 *
	 * Eine Änderung des Parameters c_Property_Name erfordert ein Ausführen der Installation, ebenso wie das hinzufügen eines
	 * Bewässerungs Kreises.
	 * Parameter c_Property_Component und c_Property_Sensor können ohne Installation verändert werden.
	 *
	 * Beispiel:
	 * @code
        function get_WateringConfiguration() {
          return array(
            c_WateringCircle_1  =>  array(
               c_Property_Name           =>   'Vorgarten',
               c_Property_Component      =>   'IPSComponentSwitch_Homematic,12345',
               c_Property_Sensor         =>   'Program.Weather.Station.RainLast3Days',
             ));
        }
	 * @endcode
	 *
	 * @return string Liefert Array mit Bewässerungs Kreisen
	 */

	function get_WateringConfiguration() {
		return array(
			c_WateringCircle_1  =>	array(
				c_Property_Name           =>   'Rasen 1',
				c_Property_Component      =>   'IPSComponentSwitch_Dummy,12345',
				c_Property_Sensor         =>   '',
			),
			c_WateringCircle_2  =>	array(
				c_Property_Name           =>   'Rasen 2',
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
	
	/**
	 * Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	 */
	define ("c_LogMessage_Count",			9);

	/** @}*/
?>