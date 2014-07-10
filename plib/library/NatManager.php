<?php

class Modules_Nat_NatManager
{

    public static function getIpAddresses()
    {
        $apiResponse = pm_ApiRpc::getService()->call('<ip><get/></ip>');
        $list = array();
        foreach ($apiResponse->ip->get->result->addresses->ip_info as $addressNode) {
            $list[(string)$addressNode->ip_address] = (string)$addressNode->public_ip_address;
        }

        return $list;
    }

    public static function updateAddress($mainIp, $publicIp)
    {
        $apiResponse = pm_ApiRpc::getService()->call(
            '<ip>' . 
                '<set>' . 
                    "<filter><ip_address>$mainIp</ip_address></filter>" .
                    "<public_ip_address>$publicIp</public_ip_address>" .
                '</set>' .
            '</ip>');

        // TODO: add error handler
    }

}
