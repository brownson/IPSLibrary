<?
    /**@addtogroup entertainment_interface
     * @ingroup entertainment
     * @{
     *
     * @file          Entertainment_InterfaceIPSComponentSwitch.inc.php
     * @author        Dominik Zeiger
     * @version
     *  Version 2.50.1, 31.01.2012<br/>
     *
     * Anbindung von IPSComponents
     *
     */

    include_once "Entertainment.inc.php";
    IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');

    // ---------------------------------------------------------------------------------------------------------------------------
    function Entertainment_IPSComponent_ReceiveData($componentParams, $function, $output, $value) {
        
    }

    // ---------------------------------------------------------------------------------------------------------------------------
    function Entertainment_IPSComponent_SendData($parameters) {
        $interfaceName  = $parameters[0];
        $deviceId       = $parameters[1];
        $value          = $parameters[2];
        
        if($value != 1 && $value != 0) {
            IPSLogger_Trc(__file__, "Unsupported value: ".$value);
            return;
        }
        
        $CommConfig = get_CommunicationConfiguration();
        $componentConstructorParams  = $CommConfig[$interfaceName][c_Property_ComponentParams];
        $componentConstructorParams = str_replace("{DEVICE_ID}", $deviceId, $componentConstructorParams);
        
        $component = IPSComponent::CreateObjectByParams($componentConstructorParams);
        $component->SetState($value == 1);
    }

  /** @}*/
?>