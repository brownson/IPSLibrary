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

	/**@defgroup ipshomematic_configuration IPSHomematic Konfiguration
	 * @ingroup ipshomematic
	 * @{
	 *
	 * @file          IPSHomematic_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.07.2012<br/>
	 *
	 */

	// Device Configuration
	// --------------------------------------------------------------------------
	function get_HomematicConfiguration() {
		return array(
			// ===== Werkstatt ========================================================================
			'HW127Wer_Inp1'  	=>	array('HEQ0150317', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp2'  	=>	array('HEQ0150317', 2, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp3'  	=>	array('HEQ0150317', 3, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp4'  	=>	array('HEQ0150317', 4, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp5'  	=>	array('HEQ0150317', 5, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp6'  	=>	array('HEQ0150317', 6, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp7'  	=>	array('HEQ0150317', 7, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp8'  	=>	array('HEQ0150317', 8, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp9'  	=>	array('HEQ0150317', 9, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Wer_Inp10'  	=>	array('HEQ0150317',10, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),

			'HW127Wer_Inp1' => array('HEQ0150317', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp1' => array('IEQ0241773', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp2' => array('IEQ0241773', 2, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp3' => array('IEQ0241773', 3, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp4' => array('IEQ0241773', 4, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp5' => array('IEQ0241773', 5, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp6' => array('IEQ0241773', 6, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp7' => array('IEQ0241773', 7, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Inp8' => array('IEQ0241773', 8, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HW127Ter_Out1' => array('IEQ0241773', 13, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SWITCH),
			'HW127Ter_Out2' => array('IEQ0241773', 14, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SWITCH),
			'HW127Ter_Out3' => array('IEQ0241773', 15, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SWITCH),
			'HW127Ter_Out4' => array('IEQ0241773', 16, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SWITCH),
			'HWShuMar1_Inp1' => array('FEQ0059849', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HWShuMar1_Inp2' => array('FEQ0059849', 2, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HWShuMar1_Out' => array('FEQ0059849', 3, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SHUTTER),
			'HWShuMar2_Inp1' => array('HEQ0236625', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HWShuMar2_Inp2' => array('HEQ0236625', 2, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'HWShuMar2_Out' => array('HEQ0236625', 3, HM_PROTOCOL_BIDCOSWI, HM_TYPE_SHUTTER),

			// ===== HomeControl ====================================================================
			'Verteiler_Sprechanlage' => array('HEQ0150195', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'KG_127HC_13_WLAN' => array('HEQ0150195', 13, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'KG_127HC_14_IRTrans' => array('HEQ0150195', 14, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'KG_127HC_15_Wohnzimmer' => array('HEQ0150195', 15, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'KG_127HC_16_Wellness' => array('HEQ0150195', 16, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'KG_127HC_17_Firewall' => array('HEQ0150195', 17, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'KG_127HC_18_Camera' => array('HEQ0150195', 18, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),

			// ===== Werkstatt ======================================================================
			'Werkstatt_Licht' => array('HEQ0150317', 13, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'Werkstatt_Garten1' => array('HEQ0150317', 14, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'Werkstatt_Garten2' => array('HEQ0150317', 15, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),

			// ===== Wellness==== ==================================================================
			'Wellness_Sauna' => array('HEQ0150259', 13, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'Wellness_Sockel' => array('HEQ0150259', 14, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'Wellness_Dusche' => array('HEQ0150259', 15, HM_PROTOCOL_BIDCOSWI, HM_TYPE_LIGHT),
			'Wellness_Wand' => array('IEQ0000135', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_DIMMER),
			'Wellness_Decke' => array('IEQ0000135', 2, HM_PROTOCOL_BIDCOSRF, HM_TYPE_DIMMER),
			'Wellness_Schalter1' => array('HEQ0150259', 1, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter2' => array('HEQ0150259', 2, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter3' => array('HEQ0150259', 3, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter4' => array('HEQ0150259', 4, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter5' => array('HEQ0150259', 5, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter6' => array('HEQ0150259', 6, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter7' => array('HEQ0150259', 7, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter8' => array('HEQ0150259', 8, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter9' => array('HEQ0150259', 9, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter10' => array('HEQ0150259', 10, HM_PROTOCOL_BIDCOSWI, HM_TYPE_BUTTON),
			'Wellness_Schalter13' => array('HEQ0360531', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_BUTTON),
			'Wellness_Schalter14' => array('HEQ0360531', 2, HM_PROTOCOL_BIDCOSRF, HM_TYPE_BUTTON),

			// ===== Erdgeschoss ==================================================================
			'Licht_Esstisch' => array('IEQ0021066', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Wohnzimmer' => array('IEQ0021445', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Küche' => array('IEQ0021077', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Wohnbereich' => array('IEQ0021074', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Abstellraum' => array('IEQ0021049', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Arbeitszimmer' => array('IEQ0021062', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_WC' => array('IEQ0021479', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Vorzimmer' => array('IEQ0021114', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Terrasse' => array('IEQ0021395', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),

			// ===== Obergeschoss ==================================================================
			'Licht_Bad' => array('IEQ0021160', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Kinderzimmer' => array('IEQ0021333', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Schlafzimmer' => array('IEQ0021334', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Schrankraum' => array('IEQ0021350', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Gästezimmer' => array('IEQ0021056', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_VorraumOG' => array('IEQ0021182', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_StiegeOG' => array('IEQ0021112', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),

			// ===== Kellergeschoss ==================================================================
			'Licht_StiegeKG' => array('IEQ0021054', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),
			'Licht_Technikraum' => array('IEQ0021186', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_LIGHT),

			// ===== Jalousien ==================================================================
			'Jalousie_Kinderzimmer' => array('HEQ0353925', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SHUTTER),
			'Jalousie_Gästezimmer' => array('HEQ0354020', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SHUTTER),
			'Jalousie_Küche' => array('HEQ0353832', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SHUTTER),
			'Jalousie_Wohnzimmer' => array('HEQ0354005', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SHUTTER),
			'Jalousie_Terrasse' => array('HEQ0353776', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SHUTTER),

			// ===== Rauchmelder ==================================================================
			'Rauchmelder_OG' => array('HEQ0403640', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SMOKEDETECTOR),
			'Rauchmelder_KG' => array('IEQ0509539', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SMOKEDETECTOR),

			// ===== Beregnungssteuerung ==================================================================
			'Gardena_1' => array('IEQ0040620', 1, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SWITCH),
			'Gardena_2' => array('IEQ0040620', 2, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SWITCH),
			'Gardena_3' => array('IEQ0040620', 3, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SWITCH),
			'Gardena_4' => array('IEQ0040620', 4, HM_PROTOCOL_BIDCOSRF, HM_TYPE_SWITCH),
		);
	}

	/** @}*/

?>