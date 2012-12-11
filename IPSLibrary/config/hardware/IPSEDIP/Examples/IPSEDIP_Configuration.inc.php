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
	 * Dieses Script enth�lt die Konfigurations Einstellungen f�r die EDIP Displays.
	 *
	 * �nderungen an den Konfigurations Parametern erforderte ein erneutes Ausf�hren des Installations
	 * Scriptes IPSEDIP_Installation.ips.php.
	 *
	 */

  /**
   * Definition Refresh Timer
   *
   * Hier kann ein beliebiger Wert in Sekunden angegeben werden. Nach Ablauf der spezifizierten Zeit werden
   * alle Displays refresht, deren Parameter EDIP_CONFIG_REFRESHMETHODE den Wert EDIP_REFRESHMETHODE_TIMER
   * enth�lt.
   */
	define ('EDIP_CONFIG_REFRESHTIMER',				60);

  /**
   * Definition der EDIP Displays.
   *
   * Es k�nnen beliebig viele Displays mit Hilfe eines Arrays definiert werden, jedes Array muss folgende
   * Eigenschaften aufweisen:
   * - EDIP_CONFIG_NAME:           Name des  EDIP Displays
   *
   * - EDIP_CONFIG_REGISTER:       ID der Register Variable, die f�r das EDIP Display verwendet werden soll. Alternativ kann auch das ComPort angegeben
   *                               werden (in diesem Fall wird die Register Variable durch die Installations Prozedur automatisch angelegt).
   *
   * - EDIP_CONFIG_ROOT:           Root ID, die f�r das 1. EDIP Display verwendet werden soll.
   *
   * - EDIP_CONFIG_CLASSNAME:      Name der Klasse, die zur Ansteuerung des Displays verwendet werden soll, folgende Klassen stehen derzeit zur Auswahl:
   *     <UL>
   *       <LI>EDIP_CLASSNAME_EDIP43,  dient zur Ansteuerung von EDIP Displays vom Type "EDIP43A"</LI>
   *       <LI>EDIP_CLASSNAME_EDIP240, dient zur Ansteuerung von EDIP Displays vom Type "EDIP240" (wartet zur Zeit noch auf eine Implementierung)</LI>
   *     </UL>
   *
   * - EDIP_CONFIG_REFRESHMETHOD: Refresh Methode, die f�r das EDIP verwendet werden soll, folgende Methoden stehen zur Auswahl
   *     <UL>
   *       <LI>EDIP_REFRESHMETHOD_TIMER, ein zyklischer Timer wird verwendet, um das Display nach x Sekunden wieder neu aufzubauen</LI>
   *       <LI>EDIP_REFRESHMETHOD_EVENT, es werden on Demand Events angelegt, die das Display unmittelbar nach einer �nderung der angezeigten Variablen wieder neu aufbauen.</LI>
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
   *       EDIP_CONFIG_ROOT              => 12345,
   *       EDIP_CONFIG_REFRESHMETHOD     => EDIP_REFRESHMETHOD _NONE,
   *       EDIP_CONFIG_CLASSNAME         => EDIP_CLASSNAME_EDIP43));
   *@endcode
   *
   * Mit speziellen Tags in der Description von Links kann die Visualisierung beeinflusst werden. Voraussetzung ist, dass
   * die Description mit "##" beginnt, Mehrere Tags m�ssen mit einem "," getrennt werden.
   * Folgende Tags werden zur Zeit unterst�tzt:
   *   - DisplayType=XXXX, folgende Werte werden unterst�tzt: "BigText"
   *     <UL>
   *       <LI>BigText - Anzeige des Wertes erfolgt mit doppelter Schriftgr�sse
   *       <LI>Inline  - erm�glicht die direkte Anzeige aller Assoziationen (normalerweise wird nur der aktuelle Wert angezeit und es wird beim editieren ein Fenster mit allen Werten eingeblendet)
   *       <LI>Block   - erm�glicht die direkte Anzeige aller Assoziationen �ber die volle Breite
   *     </UL>
   *   - WidthX=YY, erm�glicht das einstellen einer spezifischen Breite f�r eine Assoziatione im Inline/Block Modus. Es werden zur Zeit nur die Werte 50 und 100 unterst�tzt.
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
		   EDIP_CONFIG_NR1 => array(EDIP_CONFIG_NAME					=> 'Vorzimmer',
											 EDIP_CONFIG_REGISTER 			=> '',
											 EDIP_CONFIG_ROOT					=> 19585,
											 EDIP_CONFIG_REFRESHMETHOD		=> EDIP_REFRESHMETHOD_BOTH,
											 EDIP_CONFIG_CLASSNAME			=> EDIP_CLASSNAME_EDIP43),

		   EDIP_CONFIG_NR2 => array(EDIP_CONFIG_NAME					=> 'Obergeschoss',
											 EDIP_CONFIG_REGISTER 			=> '',
											 EDIP_CONFIG_ROOT					=> 'Visualization.EDIP.Obergeschoss',
											 EDIP_CONFIG_REFRESHMETHOD		=> EDIP_REFRESHMETHOD_NONE,
											 EDIP_CONFIG_CLASSNAME			=> EDIP_CLASSNAME_EDIP43),
		);
	}


	/** @}*/
?>