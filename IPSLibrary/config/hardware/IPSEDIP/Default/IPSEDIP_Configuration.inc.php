<?
	/**@defgroup ipsedip_configuration EDIP Konfiguration
	 * @ingroup ipsedip
	 * @{
	 *
	 * EDIP Konfiguration
	 *
	 * @file          IPSEDIP\IPSEDIP_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script enthält die Konfigurations Einstellungen für die EDIP Displays.
	 *
	 * Änderungen an den Konfigurations Parametern erforderte ein erneutes Ausführen des Installations
	 * Scriptes IPSEDIP_Installation.ips.php.
	 *
	 */

  /**
   * Definition Refresh Timer
   *
   * Hier kann ein beliebiger Wert in Sekunden angegeben werden. Nach Ablauf der spezifizierten Zeit werden
   * alle Displays refresht, deren Parameter EDIP_CONFIG_REFRESHMETHODE den Wert EDIP_REFRESHMETHODE_TIMER
   * enthält.
   */
	define ('EDIP_CONFIG_REFRESHTIMER',				60);

  /**
   * Definition der EDIP Displays.
   *
   * Es können beliebig viele Displays mit Hilfe eines Arrays definiert werden, jedes Array muss folgende
   * Eigenschaften aufweisen:
   * - EDIP_CONFIG_NAME:           Name des  EDIP Displays
   *
   * - EDIP_CONFIG_REGISTER:       ID der Register Variable, die für das EDIP Display verwendet werden soll. Alternativ kann auch das ComPort angegeben
   *                               werden (in diesem Fall wird die Register Variable durch die Installations Prozedur automatisch angelegt).
   *
   * - EDIP_CONFIG_COMPORT:        Com Port, das zur Kommunikation mit dem EDIP Display genützt werden soll. Wird nichts angegeben, muß die IO-Instance
   *                               manuell angelegt werden. 
   *
   * - EDIP_CONFIG_ROOT:           Root ID, die für das 1. EDIP Display verwendet werden soll.
   *
   * - EDIP_CONFIG_CLASSNAME:      Name der Klasse, die zur Ansteuerung des Displays verwendet werden soll, folgende Klassen stehen derzeit zur Auswahl:
   *     <UL>
   *       <LI>EDIP_CLASSNAME_EDIP43,  dient zur Ansteuerung von EDIP Displays vom Type "EDIP43A"</LI>
   *       <LI>EDIP_CLASSNAME_EDIP240, dient zur Ansteuerung von EDIP Displays vom Type "EDIP240" (wartet zur Zeit noch auf eine Implementierung)</LI>
   *     </UL>
   *
   * - EDIP_CONFIG_REFRESHMETHOD: Refresh Methode, die für das EDIP verwendet werden soll, folgende Methoden stehen zur Auswahl
   *     <UL>
   *       <LI>EDIP_REFRESHMETHOD_TIMER, ein zyklischer Timer wird verwendet, um das Display nach x Sekunden wieder neu aufzubauen</LI>
   *       <LI>EDIP_REFRESHMETHOD_EVENT, es werden on Demand Events angelegt, die das Display unmittelbar nach einer Änderung der angezeigten Variablen wieder neu aufbauen.</LI>
   *       <LI>EDIP_REFRESHMETHOD_NONE, keine autom. Aktualisierung</LI>
   *       <LI>EDIP_REFRESHMETHOD_BOTH, Aktualisierung mit Timer und Events</LI>
   *     </UL>
   *
   * Beispiel:
   *@code
   *  $EDIP_CONFIGURATION = array(
   *     EDIP_CONFIG_NR1 => array(
   *       EDIP_CONFIG_NAME              => 'Vorzimmer',
   *       EDIP_CONFIG_REGISTER          => 12345,
   *       EDIP_CONFIG_COMPORT           => '',
   *       EDIP_CONFIG_ROOT              => 12345,
   *       EDIP_CONFIG_REFRESHMETHOD     => EDIP_REFRESHMETHOD _NONE,
   *       EDIP_CONFIG_CLASSNAME         => EDIP_CLASSNAME_EDIP43));
   *@endcode
   *
   * Mit speziellen Tags in der Description von Links kann die Visualisierung beeinflusst werden. Voraussetzung ist, dass
   * die Description mit "##" beginnt, Mehrere Tags müssen mit einem "," getrennt werden.
   * Folgende Tags werden zur Zeit unterstützt:
   *   - DisplayType=XXXX, folgende Werte werden unterstützt: "BigText"
   *     <UL>
   *       <LI>BigText - Anzeige des Wertes erfolgt mit doppelter Schriftgrösse
   *       <LI>Inline  - ermöglicht die direkte Anzeige aller Assoziationen (normalerweise wird nur der aktuelle Wert angezeit und es wird beim editieren ein Fenster mit allen Werten eingeblendet)
   *       <LI>Block   - ermöglicht die direkte Anzeige aller Assoziationen über die volle Breite
   *     </UL>
   *   - WidthX=YY, ermöglicht das einstellen einer spezifischen Breite für eine Assoziatione im Inline/Block Modus. Es werden zur Zeit nur die Werte 50 und 100 unterstützt.
   *
   * Beispiel:
   *@code
      ##DisplayType=Inline,Width0=100,Width1=50,Width2=50,Width3=50,Width4=50
      ##DisplayType=Block
      ##DisplayType=BigText
   *@endcode
   *
   */

	function IPSEDIP_GetConfiguration() {
		return array(
		);
	}


	/** @}*/
?>