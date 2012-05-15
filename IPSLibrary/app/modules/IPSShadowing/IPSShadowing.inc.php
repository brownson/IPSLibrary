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

    /**@defgroup ipsshadowing IPSShadowing
     * @ingroup modules
     * @{
     *
     * IPSShadowing ist ein Modul der IPSLibrary, um die Beschattung eines Hauses zu steuern. 
     *
     * Das Script bietet diverse Programme um eine autom. Beschattung abhängig von Tag, Nacht, Temperatur, Sonnenstand, Wetter und 
     * auch An- oder Abwesenheit individuell zu realisieren.
     *
     * Folgende Parameter können für die Beschattung ausgewertet werden:
     * - Zeit
     * - Temperatur
     * - Helligkeit
     * - Sonnenstand
     * - Windgeschwindigkeit
     * - Regen
     * 
     * Detailierte Beschreibung der Beschattungssteuerung ist auch im WIKI zu finden:
     *
     *
     * @file          IPSShadowing.inc.php
     * @author        Andreas Brauneis
     * @version
     *  Version 2.50.1, 21.03.2012<br/>
     *
     */

    IPSUtils_Include ("IPSLogger.inc.php",                      "IPSLibrary::app::core::IPSLogger");
    IPSUtils_Include ("IPSComponent.class.php",                 "IPSLibrary::app::core::IPSComponent");
    IPSUtils_Include ("IPSShadowing_Constants.inc.php",         "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Configuration.inc.php",     "IPSLibrary::config::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Custom.inc.php",            "IPSLibrary::config::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Logging.inc.php",           "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Azimuth.inc.php",           "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Device.class.php",          "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ProfileTemp.class.php",     "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ProfileSun.class.php",      "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ProfileWeather.class.php",  "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ProfileTime.class.php",     "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ProfileManager.class.php",  "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_Scenario.class.php",        "IPSLibrary::app::modules::IPSShadowing");
    IPSUtils_Include ("IPSShadowing_ScenarioManager.class.php", "IPSLibrary::app::modules::IPSShadowing");


    /** @}*/
?>