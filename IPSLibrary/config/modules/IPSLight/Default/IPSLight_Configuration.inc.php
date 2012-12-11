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

	/**@defgroup ipslight_configuration IPSLight Konfiguration
	 * @ingroup ipslight
	 * @{
	 *
	 * @file          IPSLight_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 26.07.2012<br/>
	 *
	 * Konfigurations File f�r IPSLight
	 *
	 */

	/**
	 *
	 * Definition der Beleuchtungs Elemente
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, f�r jedes Beleuchtungselement wird ein Eintrag im Array erzeugt.
	 *
	 * F�r jedes Beleuchtungselement werden dann die Eigenschaften in einem gesonderten Array hinterlegt:
	 *
	 * IPSLIGHT_NAME  - spezifiziert den Namen der Beleuchtung in der GUI, �nderungen an dieser Eigenschaft werden erst nach einem
	 *                  erneuten Ausf�hren der Installationsprozedur sichtbar.
	 *
	 * IPSLIGHT_GROUP - beinhaltet eine Liste aller Gruppen, der das Beleuchtungselement zugeordnet ist. Diese Eigenschaft kann
	 *                  jederzeit ge�ndert werden (vorausgesetzt die Gruppe ist bereits definiert, siehe weiter unten).
	 *
	 * IPSLIGHT_TYPE  - spezifiziert den Type der Beleuchtung, zur Zeit werden 3 Beleuchtungstypen unterst�tzt:
	 *    - IPSLIGHT_TYPE_SWITCH:  Normale Beleuchtung mit Ein/Aus Funktionalit�t
	 *    - IPSLIGHT_TYPE_RGB:     RGB Beleuchtung
	 *    - IPSLIGHT_TYPE_DIMMER:  Dimmbare Beleuchtung
	 *                  �nderungen an diesem Parameter erfordern ein Ausf�hren der Installations Prozedure.
	 *
	 * IPSLIGHT_COMPONENT - dieser Eintrag spezifiziert die Hardware, die Angabe des Component Strings muss mit dem spezifizierten
	 *                      Beleuchtungstypen (siehe oben) zusammenpassen (Type Dimmer ben�tigt zB eine Klasse IPSComponentDimmer).
	 *
	 * IPSLIGHT_POWERCIRCLE - Hier kann spezifiziert werden an welchem Stromkreis die Lampe angeschlossen ist. Dieser Parameter ist
	 *                        optional.
	 *
	 * IPSLIGHT_POWERWATT - Spezifiert die maximale Leistung der Beleuchtung. Zusammen mit dem Parameter IPSLIGHT_POWERCIRCLE ist es 
	 *                      nun m�glich die aktuelle Leistung eines Stromkreises abzufragen. Details siehe auch im WIKI.
	 *
	 * Eine ausf�hrliche Beispielliste findet sich auch im Example Ordner
	 *
	 *
	 * Beispiel:
	 * @code
        function IPSLight_GetLightConfiguration() {
          return array(
            'Kueche'  =>  array(
               IPSLIGHT_NAME            => 'K�che',
               IPSLIGHT_GROUPS          => 'Erdgeschoss,All',
               IPSLIGHT_TYPE            => IPSLIGHT_TYPE_SWITCH',
               IPSLIGHT_COMPONENT       => 'IPSComponentSwitch_Homematic,12345',
               IPSLIGHT_POWERCIRCLE     => 1,
               IPSLIGHT_POWERWATT       => 60),
            'Ambiente'  =>  array(
               IPSLIGHT_NAME            => 'Ambiente',
               IPSLIGHT_GROUPS          => 'Erdgeschoss,All',
               IPSLIGHT_TYPE            => IPSLIGHT_TYPE_RGB,
               IPSLIGHT_COMPONENT       => 'IPSComponentRGB_IPS868,12345'),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit Beleuchtungs Elementen
	 */
	function IPSLight_GetLightConfiguration() {
		return array(
			'Wohnzimmer'       =>	array('Wohnzimmer',    'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Dummy,12345'),
			'Kueche'           =>	array('K�che',         'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,12345'),
		);
	}


	/**
	 *
	 * Definition der Beleuchtungs Gruppen
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, f�r jede Beleuchtungsgruppe wird ein Eintrag im Array erzeugt.
	 *
	 * F�r jede Beleuchtungsgruppe werden dann die Eigenschaften in einem gesonderten Array hinterlegt:
	 *
	 * IPSLIGHT_NAME  - spezifiziert den Namen der Gruppe in der GUI, �nderungen an dieser Eigenschaft werden erst nach einem
	 *                  erneuten Ausf�hren der Installationsprozedur sichtbar.
	 *
	 * IPSLIGHT_ACTIVATABLE - gibt an, ob die Gruppe �ber die GUI eingeschaltet werden kann
	 *
	 * Eine Liste mit diversen Beispiel Konfigurationen findet sich auch im Example Ordner
	 *
	 *
	 * Beispiel:
	 * @code
        function IPSLight_GetGroupConfiguration() {
          return array(
            'All'  =>  array(
               IPSLIGHT_NAME            => 'All',
               IPSLIGHT_ACTIVATABLE     => false),
            'Erdgeschoss'  =>  array(
               IPSLIGHT_NAME            => 'Erdgeschoss',
               IPSLIGHT_ACTIVATABLE     => false),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit Beleuchtungs Gruppen
	 */
	function IPSLight_GetGroupConfiguration() {
		return array('All'             =>	array('All',            IPSLIGHT_ACTIVATABLE => false,),
		             'Erdgeschoss'     =>	array('Erdgeschoss',    IPSLIGHT_ACTIVATABLE => false,),
	   );
	}

	/**
	 *
	 * Definition der Beleuchtungs Programme
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, f�r jedes Beleuchtungsprogramm wird ein Eintrag im Array erzeugt.
	 *
	 * F�r jedes Beleuchtungsprogramm werden dann die einzelnen Programme ebenfalls als Array hinterlegt, diese wiederum haben ihre
	 * Eigenschaften nochmals in einem Array gespeichert:
	 *
	 * IPSLIGHT_PROGRAMON  - Liste mit Beleuchungselementen, die bei diesem Programm eingeschaltet sein sollen.
	 *
	 * IPSLIGHT_PROGRAMOFF  - Liste mit Beleuchungselementen, die bei diesem Programm ausgeschaltet sein sollen.
	 *
	 * IPSLIGHT_PROGRAMLEVEL  - Liste mit Beleuchungselementen, die auf einen bestimmten Dimm Level gestellt werden sollen
	 *
	 * Eine Liste mit diversen Beispiel Konfigurationen findet sich auch im Example Ordner
	 *
	 *
	 * Beispiel:
	 * @code
        function IPSLight_GetProgramConfiguration() {
          return array(
				'Aus'  	=>	array(
					IPSLIGHT_PROGRAMOFF		=> 	'WellnessWand,WellnessDecke,WellnessSauna,WellnessDusche,WellnessAmbiente',
				),
				'TV'  	=>	array(
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessWand,30',
					IPSLIGHT_PROGRAMOFF		=> 	'WellnessDecke,WellnessSauna,WellnessDusche,WellnessAmbiente',

				),
				'Relax'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'WellnessSauna,WellnessDusche,WellnessAmbiente',
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessDecke,30,WellnessWand,30',

				),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit Beleuchtungs Gruppen
	 */
	function IPSLight_GetProgramConfiguration() {
		return array(
		);
	}

	/**
	 *
	 * Definition der WebFront GUI
	 *
	 * Die Konfiguration der WebFront Oberfl�che ist NICHT dokumentiert, ist aber analog zur normalen WebFront Konfigurator GUI
	 * aufgebaut.
	 *
	 * Beispiele finden sich im Example Ordner
	 *
	 * @return string Liefert Array zum Aufbau des WebFronts
	 */
	function IPSLight_GetWebFrontConfiguration() {
		return array(
		);
	}

	/**
	 *
	 * Definition der Mobile GUI
	 *
	 * Die Konfiguration der Mobile GUI ist NICHT dokumentiert
	 *
	 * Beispiele finden sich im Example Ordner
	 *
	 * @return string Liefert Array zum Aufbau der Mobile GUI
	 */
	function IPSLight_GetMobileConfiguration() {
		return array(
		);
	}

	/** @}*/
?>