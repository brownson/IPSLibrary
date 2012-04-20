<?
	/**@defgroup ipsedip_configuration EDIP Konfiguration
	 * @ingroup ipsedip
	 * @{
	 *
	 * EDIP Konfiguration
	 *
	 * @file          Default/IPSEDIP_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @author        André Czwalina
	 * @version
	 * Version 2.50.2, 16.04.2012<br/>
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
	define ('EDIP_CONFIG_REFRESHTIMER',				600);

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
   * - EDIP_CONFIG_BACKLIGHT_LOW:   Display Helligkeit bei runtergeregeltem Betrieb Wertbereich 0..100. Auf diesen Wert wird gedimmt nach der Zeit EDIP_CONFIG_BACKLIGHT_TIMER.
   *                                ACHTUNG Es wird nur auf diesen Wert gedimmt, wenn die aktuelle Helligkeit > EDIP_CONFIG_BACKLIGHT_LOW ist.
	*											Ist die aktuelle Helleigkeit niedriger als EDIP_CONFIG_BACKLIGHT_LOW (Nachtmodus) wird nicht hoch gedimmt.
   *
   * - EDIP_CONFIG_BACKLIGHT_HIGH:  Display Helligkeit im Normal Betrieb Wertbereich 0..100. Auf diesen Wert wird gedimmt, wenn eine Taste/Bargraph betätigt wird.
   *                                ACHTUNG es wird nur auf diesen Wert gedimmt, wenn die aktulle Helligkeit dem EDIP_CONFIG_BACKLIGHT_LOW Wert entspricht. (Helligkeit wurd automatisch runtergeregelt)
   *                                Ist das nicht der Fall ist manueller Modus und die Helligkeit bleibt. Automatische Abschaltung manueller Modus nach Zeit EDIP_CONFIG_BACKLIGHT_TIMER.
   *
   * - EDIP_CONFIG_BACKLIGHT_TIMER: Zeit in Minuten nach der das Display auf die Einstellung EDIP_CONFIG_BACKLIGHT_LOW eingestellt werden soll. 0 = abgeschaltet.
	*											ACHTUNG Nur wenn die aktuelle Helligkeit > EDIP_CONFIG_BACKLIGHT_LOW ist.
	*											Ist die aktuelle Helligkeit niedriger als EDIP_CONFIG_BACKLIGHT_LOW (Nachtmodus) wird nicht gedimmt.
	*
	* - EDIP_CONFIG_ROOT_TIMER:      Zeit in Minuten bis wieder auf die Rootseite zurückgesprungen wird. 0 = abgeschaltet
	*
   * Unter jedem Data Verzeichnisses eines Displays befindet sich die Variable 'OBJECT_Backlight'.
	* Wenn man sie unter der Visualisierung ein Link auf diese Variable zieht und in der Beschreibung des Links die Displayoption '##DisplayType=BarGraph' schreibt, kann die Helligkeit am EDIP direkt eingestellt werden
   *
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
   *       EDIP_CONFIG_CLASSNAME         => EDIP_CLASSNAME_EDIP43,
	*		  EDIP_CONFIG_BACKLIGHT_LOW     => 30,
	*		  EDIP_CONFIG_BACKLIGHT_HIGH    => 100,
	* 		  EDIP_CONFIG_BACKLIGHT_TIMER   => 1,
	* 		  EDIP_CONFIG_ROOT_TIMER		  => 5)
	*		  );
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
   *       <LI>Select  - Gedacht für die Steuerung von Variablen mit Steuertasten. Assoziation ist für 3 Tasten (+, Wert, -). Spaltenbreite kann auf 30% eingestellt werden. Ist dann real ca. 25,50,25%
   *       <LI>Color   - Hier kann die Textvordergrundfarbe eingegeben werden 1...16. Tabelle auf EDIP Handbuch. 3 = Rot. Kombination mit BigText möglich.
   *       <LI>BarGraph   - Anzeige und bedienung von Bargraphen.
   *     </UL>
   *   - WidthX=YY, ermöglicht das einstellen einer spezifischen Breite für eine Assoziatione im Inline/Block Modus. Es werden zur Zeit nur die Werte 50 und 100 unterstützt.
   *
   *
   * Beispiel:
   *@code
      ##DisplayType=Inline,Width0=100,Width1=50,Width2=50,Width3=50,Width4=50
      ##DisplayType=Select,Width0=30,Width1=30,Width2=30
      ##DisplayType=Block
      ##DisplayType=BigText
      ##DisplayType=Color=3
      ##DisplayType=BarGraph
      ##DisplayType=BarGraph,Color=3
   *@endcode
   *
   */

	function IPSEDIP_GetConfiguration() {
		return array(
		   EDIP_CONFIG_NR1 => array(EDIP_CONFIG_NAME					 => 'Vorimmer',
								         EDIP_CONFIG_REGISTER           => '', //per Hand nachtragen
								         EDIP_CONFIG_COMPORT            => '',
								         EDIP_CONFIG_ROOT               => 'Visualization.EDIP.Vorimmer',
								         EDIP_CONFIG_REFRESHMETHOD      => EDIP_REFRESHMETHOD_BOTH,
								         EDIP_CONFIG_CLASSNAME          => EDIP_CLASSNAME_EDIP43,
											EDIP_CONFIG_BACKLIGHT_LOW      => 25,
											EDIP_CONFIG_BACKLIGHT_HIGH     => 100,
											EDIP_CONFIG_BACKLIGHT_TIMER    => 1,
											EDIP_CONFIG_ROOT_TIMER    		 => 5
											),

		);
	}


	/** @}*/
?>