<?
	/**@defgroup entertainment Entertainment Steuerung
	 * @{
	 *
	 * Entertainment Steuerung - Ermöglicht die Anbindung und Ansteuerung von diversen Entertainment
	 * Komponenten mittels IRTrans, TCP/IP, serieller Schnittstelle...
	 *
	 * @file          Entertainment.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Entertainment Include File
	 *
	 * Dieses File inkludiert alle benötigten Files der Entertainment Steuerung um alle wesentlichen
	 * Funktionen der Entertainment Steuerung ansteueren zu können.
    */

	IPSUtils_Include ("IPSLogger.inc.php",                   "IPSLibrary::app::core::IPSLogger");
	include_once "Entertainment_Constants.inc.php";
	IPSUtils_Include ("Entertainment_IDs.inc.php",           "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ("Entertainment_Configuration.inc.php", "IPSLibrary::config::modules::Entertainment");
	include_once "Entertainment_Communication.inc.php";
	include_once "Entertainment_Control.inc.php";
	include_once "Entertainment_Room.inc.php";
	include_once "Entertainment_Power.inc.php";
	include_once "Entertainment_Device.inc.php";
	include_once "Entertainment_Source.inc.php";
	IPSUtils_Include ("Entertainment_Custom.inc.php", "IPSLibrary::config::modules::Entertainment");
	include_once "Entertainment_Connect.inc.php";
	include_once "Entertainment_RemoteControl.inc.php";
   /** @}*/

	/**@defgroup entertainment_visu Entertainment Visualisierung
	 * @ingroup entertainment
	 * @{
	 *
	 *@page visu_entertainment_webfront Entertainment WebFront Visualisierung
	 *
	 * Alle Funktionen der Entertainment Steuerung können auch über WebFront Interface bedient werden.
	 * 
	 * Die Visualisierung bietet eine Übersicht mit dem Zustand aller Räume und Geräte
	 *@image html Entertainment_WebFrontOverview.png "Übersicht" 
	 *
	 * Steuerung der Funktionen eines Raumes
	 *@image html Entertainment_WebFrontRoomOn.png "Ansicht Raum" 
	 *
	 * Im ausgeschalteten Zustand werden alle Controls des Raumes automatisch ausgeblendet,
	 *@image html Entertainment_WebFrontRoomOff.png "Ansicht ausgeschalteter Raum" 
	 *
	 *@page visu_entertainment_mobile Entertainment Mobile Visualisierung
	 *
	 * Alle Funktionen der Entertainment Steuerung können auch über das Mobile Interface bedient werden.
	 *
	 * Die Visualisierung bietet eine Übersicht mit dem Zustand aller Räume und Geräte
	 *@image html Entertainment_MobileOverview1.png "Übersicht Räume" 
	 *@image html Entertainment_MobileOverview2.png "Übersicht Geräte" 
	 *
	 * Steuerung der Funktionen eines Raumes
	 *@image html Entertainment_MobileRoom.png "Ansicht Raum" 
	 *
	 * Sound Einstellungen
	 *@image html Entertainment_MobileSound.png "Sound Einstellungen" 
	 *
	 * Um den Eingang des jeweiligen Raumes Steuern zu können, wird die Entsprechende Fernbedienung eingeblendet
	 *@image html Entertainment_MobileSource.png "Fernbedienung" 
	 *
	 */
   /** @}*/

   
   /*
Control
	function get_CommandListKey($KeyValues) {
	function get_CommandList() {
	function bool2OnOff($bool) {
	function isDeviceControl($ControlId) {
	function isRoomControl($ControlId) {
	function isDevicePoweredOnByDeviceName($DeviceName)
	function get_RoomControlIdByDeviceControlId($DeviceControlId) {
	function get_DeviceControlIdByRoomControlId($RoomControlId) {
	function get_ControlType($ControlId) {
	function get_ControlNameByDeviceName($DeviceName, $ControlType) {
	function get_ControlIdByDeviceName($DeviceName, $ControlType) {
	function get_ControlNameByRoomName($RoomName, $ControlType) {
	function get_ControlIdByRoomId($RoomId, $ControlType) {
	function get_ActiveRoomIds () {
	function get_ActiveDeviceNames() {
	function get_DeviceNamesByRoomId($RoomId) {
	function get_SourceIdxByRoomId($RoomId) {
	function get_SourceDeviceTypes($RoomId, $SourceIdx) {
	function get_SourceListByDeviceName($DeviceName) {
	function get_SourceName($RoomId, $SourceIdx) {
	function get_RoomId($RoomName) {
	function get_TemplateIndex($DeviceName, $ControlType, $CommType, $Template) {


Communication
	function Entertainment_ReceiveData($Data) {
	function Entertainment_SendData($DeviceName, $ControlType, $CommParams, $CommType) {
	function Entertainment_SendDataByDeviceName($DeviceName, $ControlType, $CommTypeList) {
	function Entertainment_SendDataBySourceIdx($RoomId, $SourceIdx) {
Power
	function Entertainment_SetDevicePowerByDeviceName($DeviceName, $Value) {
	function Entertainment_SetDevicePowerByRoomId($RoomId, $Value) {
	function Entertainment_SetDevicePower($PowerId, $Value) {
	function Entertainment_PowerOffUnusedDevices() {
Room
	function Entertainment_SetRoomVisible($PowerId, $Value) {
	function Entertainment_SetRoomPower($PowerId, $Value) {
	function Entertainment_SetRoomPowerByDeviceName($DeviceName, $Value) {
	function Entertainment_PowerOffUnusedRoomes() {
	function IsRoomPoweredOn($RoomId) {
	function Entertainment_SetRoomPowerByRoomId($RoomId, $Value) {
Source
	function Entertainment_SyncAllRoomControls() {
	function Entertainment_SyncRoomControls($RoomId) {
	function Entertainment_SetSource($SourceId, $Value, $MessageType) {
	function Entertainment_SetSourceByRoomId($RoomId, $SourceIdx) {
Device
	function get_MaxValueByControlId($ControlId) {
	function Entertainment_SetProgramPrev($Id, $MessageType=c_MessageType_Action) {
	function Entertainment_SetProgramNext($Id, $MessageType=c_MessageType_Action) {
	function Entertainment_SetProgram($Id, $Value, $MessageType=c_MessageType_Action) {
	function Entertainment_SetMode($Id, $Value, $MessageType=c_MessageType_Action) {
	function Entertainment_SetVolume($Id, $Value) {
	function Entertainment_SetMuting($Id, $Value) {
	function Entertainment_SetControl ($ControlId, $Value) {
	function Entertainment_SetDeviceControl($DeviceControlId, $Value) {
	function Entertainment_SetRoomControlByDeviceControlId($DeviceControlId, $Value) {
	function Entertainment_SetDeviceControlByRoomControlId($RoomControlId, $Value) {
	function Entertainment_SetDeviceControlByRoomId($RoomId, $ControlType, $Value) {
	function Entertainment_SetDeviceControlByDeviceName($DeviceName, $ControlType, $Value) {
*/






?>