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
	 * Konfigurations File für IPSLight
	 *
	 */

	/**
	 *
	 * Definition der Beleuchtungs Elemente
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, für jedes Beleuchtungselement wird ein Eintrag im Array erzeugt.
	 *
	 * Für jedes Beleuchtungselement werden dann die Eigenschaften in einem gesonderten Array hinterlegt:
	 *
	 * IPSLIGHT_NAME  - spezifiziert den Namen der Beleuchtung in der GUI, Änderungen an dieser Eigenschaft werden erst nach einem
	 *                  erneuten Ausführen der Installationsprozedur sichtbar.
	 *
	 * IPSLIGHT_GROUP - beinhaltet eine Liste aller Gruppen, der das Beleuchtungselement zugeordnet ist. Diese Eigenschaft kann
	 *                  jederzeit geändert werden (vorausgesetzt die Gruppe ist bereits definiert, siehe weiter unten).
	 *
	 * IPSLIGHT_TYPE  - spezifiziert den Type der Beleuchtung, zur Zeit werden 3 Beleuchtungstypen unterstützt:
	 *    - IPSLIGHT_TYPE_SWITCH:  Normale Beleuchtung mit Ein/Aus Funktionalität
	 *    - IPSLIGHT_TYPE_RGB:     RGB Beleuchtung
	 *    - IPSLIGHT_TYPE_DIMMER:  Dimmbare Beleuchtung
	 *                  Änderungen an diesem Parameter erfordern ein Ausführen der Installations Prozedure.
	 *
	 * IPSLIGHT_COMPONENT - dieser Eintrag spezifiziert die Hardware, die Angabe des Component Strings muss mit dem spezifizierten
	 *                      Beleuchtungstypen (siehe oben) zusammenpassen (Type Dimmer benötigt zB eine Klasse IPSComponentDimmer).
	 *
	 * IPSLIGHT_POWERCIRCLE - Hier kann spezifiziert werden an welchem Stromkreis die Lampe angeschlossen ist. Dieser Parameter ist
	 *                        optional.
	 *
	 * IPSLIGHT_POWERWATT - Spezifiert die maximale Leistung der Beleuchtung. Zusammen mit dem Parameter IPSLIGHT_POWERCIRCLE ist es 
	 *                      nun möglich die aktuelle Leistung eines Stromkreises abzufragen. Details siehe auch im WIKI.
	 *
	 * Eine ausführliche Beispielliste findet sich auch im Example Ordner
	 *
	 *
	 * Beispiel:
	 * @code
        function IPSLight_GetLightConfiguration() {
          return array(
            'Kueche'  =>  array(
               IPSLIGHT_NAME            => 'Küche',
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
			// ===== Erdgeschoss ==================================================================
			'Esstisch'         =>	array('Esstisch',      'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,32626','L1',110),
			'Wohnzimmer'       =>	array('Wohnzimmer',    'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,48611','L1',140),
			'Ambiente'         =>	array('Ambiente',      'Erdgeschoss,All', 'RGB',    'IPSComponentRGB_IPS868,36525','L1',10),
			'Wohnbereich'      =>	array('Wohnbereich',   'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,17605','L1',110),
			'Kueche'           =>	array('Küche',         'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,27179','L2',10),
			'Arbeitszimmer'    =>	array('Arbeitszimmer', 'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,24592','L1',12),
			'Vorzimmer'        =>	array('Vorzimmer',     'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,11022','L3',90),
			'WC'               =>	array('WC',            'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,21180','L3',40),
			'Abstellraum'      =>	array('Abstellraum',   'Erdgeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,13545','L3',110),

			// ===== Obergeschoss ==================================================================
			'Bad'              =>	array('Bad',           'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,51577','L1',0),
			'Gaestezimmer'     =>	array('Spielezimmer',  'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,32432','L1',0),
			'Kinderzimmer'     =>	array('Zimmer Jonas',  'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,52619','L1',0),
			'Schlafzimmer'     =>	array('Schlafzimmer',  'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,45579','L1',0),
			'Schrankraum'      =>	array('Schrankraum',   'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,18743','L1',0),
			'Vorraum'          =>	array('Vorraum',       'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,59085','L1',0),
			'Stiege OG'        =>	array('Stiege OG',     'Obergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,19661','L1',0),

			// ===== Kellergeschoss ==================================================================
			'Stiege KG'        =>	array('Bad',          'Kellergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,21631','L1',0),
			'Technikraum'      =>	array('Technikraum',  'Kellergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,40611','L1',0),
			'Werkstatt'        =>	array('Werkstatt',    'Kellergeschoss,All', 'Switch', 'IPSComponentSwitch_Homematic,39307','L1',0),
			'WellnessWand'     =>	array('Wandleuchte',  'Kellergeschoss,All,Wellness', 'Dimmer', 'IPSComponentDimmer_Homematic,10608','L2',380),
			'WellnessDecke'    =>	array('Decke',        'Kellergeschoss,All,Wellness', 'Dimmer', 'IPSComponentDimmer_Homematic,22847','L2',60),
			'WellnessSauna'    =>	array('Sauna',        'Kellergeschoss,All,Wellness', 'Switch', 'IPSComponentSwitch_Homematic,43774','L2',60),
			'WellnessDusche'   =>	array('Dusche',       'Kellergeschoss,All,Wellness', 'Switch', 'IPSComponentSwitch_Homematic,21540','L2',8),
			'WellnessAmbiente' =>	array('Ambiente',     'Kellergeschoss,All,Wellness', 'Switch', 'IPSComponentSwitch_Homematic,28131','L2',4),

			// ===== Aussen ==================================================================
			'TerrasseHauswand' =>	array('Hauswand',    'Aussen,All', 'Switch', 'IPSComponentSwitch_Homematic,34233','L1',120),
			'TerrasseGarten'   =>	array('Garten',      'Aussen,All', 'Switch', 'IPSComponentSwitch_Homematic,50936','L1',60),
			'TerrassePergola'  =>	array('Pergola',     'Aussen,All', 'Switch', 'IPSComponentSwitch_Homematic,34391','L1',8),
			'TerrasseTisch'    =>	array('Tisch',       'Aussen,All', 'Switch', 'IPSComponentSwitch_Homematic,58524','L1',4),
		);
	}


	/**
	 *
	 * Definition der Beleuchtungs Gruppen
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, für jede Beleuchtungsgruppe wird ein Eintrag im Array erzeugt.
	 *
	 * Für jede Beleuchtungsgruppe werden dann die Eigenschaften in einem gesonderten Array hinterlegt:
	 *
	 * IPSLIGHT_NAME  - spezifiziert den Namen der Gruppe in der GUI, Änderungen an dieser Eigenschaft werden erst nach einem
	 *                  erneuten Ausführen der Installationsprozedur sichtbar.
	 *
	 * IPSLIGHT_ACTIVATABLE - gibt an, ob die Gruppe über die GUI eingeschaltet werden kann
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
		             'Obergeschoss'    =>	array('Obergeschoss',   IPSLIGHT_ACTIVATABLE => false,),
		             'Kellergeschoss'  =>	array('Kellergeschoss', IPSLIGHT_ACTIVATABLE => false,),
		             'Wellness'        =>	array('Wellness',       IPSLIGHT_ACTIVATABLE => true,),
		             'Aussen'          =>	array('Aussen',         IPSLIGHT_ACTIVATABLE => true,),
	   );
	}

	/**
	 *
	 * Definition der Beleuchtungs Programme
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, für jedes Beleuchtungsprogramm wird ein Eintrag im Array erzeugt.
	 *
	 * Für jedes Beleuchtungsprogramm werden dann die einzelnen Programme ebenfalls als Array hinterlegt, diese wiederum haben ihre
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
			// ===== Wellness ==================================================================
			'WellnessProgram'  	=>	array(
				'Aus'  	=>	array(
					IPSLIGHT_PROGRAMOFF		=> 	'WellnessWand,WellnessDecke,WellnessSauna,WellnessDusche,WellnessAmbiente',
				),
				'Normal'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'WellnessSauna,WellnessDusche,WellnessAmbiente',
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessDecke,100,WellnessWand,100',
				),
				'Sauna'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'WellnessSauna,WellnessDusche,WellnessAmbiente',
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessDecke,50',
					IPSLIGHT_PROGRAMOFF		=> 	'WellnessWand',
				),
				'TV'  	=>	array(
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessWand,30',
					IPSLIGHT_PROGRAMOFF		=> 	'WellnessDecke,WellnessSauna,WellnessDusche,WellnessAmbiente',

				),
				'Relax'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'WellnessSauna,WellnessDusche,WellnessAmbiente',
					IPSLIGHT_PROGRAMLEVEL	=> 	'WellnessDecke,30,WellnessWand,30',

				),
			),
			// ===== Terrasse ==================================================================
			'TerrasseProgram'  	=>	array(
				'Aus'  	=>	array(
					IPSLIGHT_PROGRAMOFF		=> 	'TerrasseHauswand,TerrasseGarten,TerrassePergola,TerrasseTisch',
					IPSLIGHT_PROGRAMLEVEL	=> 	'',
				),
				'Garten'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'TerrasseGarten,TerrassePergola',
					IPSLIGHT_PROGRAMOFF		=> 	'TerrasseHauswand,TerrasseTisch',
					IPSLIGHT_PROGRAMLEVEL	=> 	'',
				),
				'Abend'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'TerrasseGarten,TerrassePergola,TerrasseTisch',
					IPSLIGHT_PROGRAMOFF		=> 	'TerrasseHauswand',
					IPSLIGHT_PROGRAMLEVEL	=> 	'',
				),
				'Terrasse'  	=>	array(
					IPSLIGHT_PROGRAMON		=> 	'TerrasseHauswand,TerrasseGarten,TerrassePergola,TerrasseTisch',
					IPSLIGHT_PROGRAMLEVEL	=> 	'',
				),
			),
	   );
	}

	/**
	 *
	 * Definition der WebFront GUI
	 *
	 * Die Konfiguration der WebFront Oberfläche ist NICHT dokumentiert
	 *
	 * Beispiele finden sich im Example Ordner
	 *
	 * @return string Liefert Array zum Aufbau des WebFronts
	 */
	function IPSLight_GetWebFrontConfiguration() {
		return array(
			'Übersicht' => array(
				array(IPSLIGHT_WFCSPLITPANEL, 'Light_1_SPv1x',       'LightTP',        'Übersicht','Bulb',1,33,0,0,'true'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_1_SPv1h1x',   'Light_1_SPv1x',   null,null,0,270,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_1_CAv1h1',  'Light_1_SPv1h1x', null,null),
				array(IPSLIGHT_WFCLINKS,            '',              'Light_1_CAv1h1',  'Obergeschoss,Erdgeschoss,Kellergeschoss,Wellness,Aussen'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_1_CAv1h2',  'Light_1_SPv1h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Wellness',      'Light_1_CAv1h2',  'WellnessWand,WellnessDecke,WellnessSauna,WellnessDusche,WellnessAmbiente', 'Decke,Wand,Sauna,Dusche,Ambiente'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_1_SPv23',     'Light_1_SPv1x',   null,null,1,50,0,0,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_1_CAv2',    'Light_1_SPv23',   null,null),
				array(IPSLIGHT_WFCGROUP,            'Erdgeschoss',   'Light_1_CAv2',    'Wohnbereich,Wohnzimmer,Ambiente,Esstisch,Kueche,Arbeitszimmer,Vorzimmer,WC,Abstellraum', 'Wohnbereich,Wohnzimmer,Ambiente,Esstisch,Küche,Arbeitszimmer,Vorzimmer,WC,Abstellraum'),
				array(IPSLIGHT_WFCGROUP,            'Aussen',        'Light_1_CAv2',    'TerrasseHauswand,TerrasseGarten,TerrassePergola,TerrasseTisch', 'Hauswand,Garten,Pergola,Tisch'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_1_CAv3',    'Light_1_SPv23',   null,null),
				array(IPSLIGHT_WFCGROUP,            'Obergeschoss',  'Light_1_CAv3',    'Stiege OG,Vorraum,Bad,Gaestezimmer,Kinderzimmer,Schlafzimmer,Schrankraum', 'Stiege OG,Vorraum,Bad,Gästezimmer,Kinderzimmer,Schlafzimmer,Schrankraum'),
				array(IPSLIGHT_WFCGROUP,            'Kellergeschoss','Light_1_CAv3',    'Stiege KG,Technikraum,Werkstatt'),
				),
			'Obergeschoss' => array(
				array(IPSLIGHT_WFCSPLITPANEL, 'Light_2_SPv1',        'LightTP',        'Obergeschoss',null,1,50,0,0,'true'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_2_SPv1h1x',   'Light_2_SPv1',    null,null,0,100,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_2_CAv1h1',  'Light_2_SPv1h1x', null,null),
				array(IPSLIGHT_WFCLINKS,            '',              'Light_2_CAv1h1',  'Obergeschoss'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_2_CAv1h2',  'Light_2_SPv1h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Obergeschoss',  'Light_2_CAv1h2',  'Stiege OG,Vorraum,Bad,Gaestezimmer,Kinderzimmer,Schlafzimmer,Schrankraum', 'Stiege OG,Vorraum,Bad,Gästezimmer,Kinderzimmer,Schlafzimmer,Schrankraum'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_2_SPv2h1x',   'Light_2_SPv1',    null,null,0,500,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_2_CAv2h1',  'Light_2_SPv2h1x', null,null),
				array(IPSLIGHT_WFCCATEGORY,       'Light_2_CAv2h2',  'Light_2_SPv2h1x', null,null),
				),
			'Erdgeschoss' => array(
				array(IPSLIGHT_WFCSPLITPANEL, 'Light_3_SPv1',        'LightTP',        'Erdgeschoss',null,1,50,0,0,'true'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_3_SPv1h1x',   'Light_3_SPv1',    null,null,0,100,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_3_CAv1h1',  'Light_3_SPv1h1x', null,null),
				array(IPSLIGHT_WFCLINKS,            '',              'Light_3_CAv1h1',  'Erdgeschoss'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_3_CAv1h2',  'Light_3_SPv1h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Erdgeschoss',   'Light_3_CAv1h2',  'Vorzimmer,WC,Abstellraum,Wohnbereich,Wohnzimmer,Esstisch,Kueche,Arbeitszimmer', 'Vorzimmer,WC,Abstellraum,Wohnbereich,Wohnzimmer,Esstisch,Küche,Arbeitszimmer'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_3_SPv2h1x',   'Light_3_SPv1',    null,null,0,500,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_3_CAv2h1',  'Light_3_SPv2h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Ambiente',      'Light_3_CAv2h1', 'Ambiente,Ambiente#Color,Ambiente#Level', 'Power,Farbe,Helligkeit'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_3_CAv2h2',  'Light_3_SPv2h1x', null,null),
				),
			'Keller/Aussen' => array(
				array(IPSLIGHT_WFCSPLITPANEL, 'Light_4_SPv1',        'LightTP',        'Keller/Aussen',null,1,50,0,0,'true'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_4_SPv1h1x',   'Light_4_SPv1',    null,null,0,200,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_4_CAv1h1',  'Light_4_SPv1h1x', null,null),
				array(IPSLIGHT_WFCLINKS,            '',              'Light_4_CAv1h1',  'Kellergeschoss,Wellness,Aussen'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_4_CAv1h2',  'Light_4_SPv1h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Keller',        'Light_4_CAv1h2',  'Stiege KG,Technikraum,Werkstatt'),
				array(IPSLIGHT_WFCGROUP,            'Aussen',        'Light_4_CAv1h2',  'TerrasseProgram,TerrasseHauswand,TerrasseGarten,TerrassePergola,TerrasseTisch', 'Programm,Hauswand,Garten,Pergola,Tisch'),
				array(IPSLIGHT_WFCSPLITPANEL,   'Light_4_SPv2h1x',   'Light_4_SPv1',    null,null,0,400,0,1,'true'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_4_CAv2h1',  'Light_4_SPv2h1x', null,null),
				array(IPSLIGHT_WFCGROUP,            'Wellness',      'Light_4_CAv2h1',  'WellnessProgram,WellnessDecke,WellnessDecke#Level,WellnessWand,WellnessWand#Level,WellnessSauna,WellnessDusche,WellnessAmbiente', 'Program,Decke,Helligkeit Decke,Wand,Helligkeit Wand,Sauna,Dusche,Ambiente'),
				array(IPSLIGHT_WFCCATEGORY,       'Light_4_CAv2h2',  'Light_4_SPv2h1x', null,null,''),
				),
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
			'Obergeschoss,Erdgeschoss,Kellergeschoss,Wellness,Aussen',
			'Obergeschoss' => array(
				array(IPSLIGHT_WFCLINKS,            '',             'Obergeschoss'),
				array(IPSLIGHT_WFCGROUP,            'Obergeschoss', 'Stiege OG,Vorraum,Bad,Gaestezimmer,Kinderzimmer,Schlafzimmer,Schrankraum', 'Stiege OG,Vorraum,Bad,Gästezimmer,Kinderzimmer,Schlafzimmer,Schrankraum'),
				),
			'Erdgeschoss' => array(
				array(IPSLIGHT_WFCLINKS,            '',             'Erdgeschoss'),
				array(IPSLIGHT_WFCGROUP,            'Erdgeschoss',  'Vorzimmer,WC,Abstellraum,Wohnbereich,Wohnzimmer,Esstisch,Kueche,Arbeitszimmer', 'Vorzimmer,WC,Abstellraum,Wohnbereich,Wohnzimmer,Esstisch,Küche,Arbeitszimmer'),
				array(IPSLIGHT_WFCGROUP,            'Ambiente',     'Ambiente,Ambiente#Color,Ambiente#Level', 'Power,Farbe,Helligkeit'),
				),
			'Keller/Aussen' => array(
				array(IPSLIGHT_WFCLINKS,            '',             'Kellergeschoss,Wellness,Aussen'),
				array(IPSLIGHT_WFCGROUP,            'Keller',       'Stiege KG,Technikraum,Werkstatt'),
				array(IPSLIGHT_WFCGROUP,            'Aussen',       'TerrasseProgram,TerrasseHauswand,TerrasseGarten,TerrassePergola,TerrasseTisch', 'Programm,Hauswand,Garten,Pergola,Tisch'),
				array(IPSLIGHT_WFCGROUP,            'Wellness',     'WellnessProgram,WellnessDecke,WellnessDecke#Level,WellnessWand,WellnessWand#Level,WellnessSauna,WellnessDusche,WellnessAmbiente', 'Program,Decke,Helligkeit,Wand,Helligkeit,Sauna,Dusche,Ambiente'),
				),
	   );
	}

	/** @}*/
?>