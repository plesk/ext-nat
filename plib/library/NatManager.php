<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
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
        $ips = self::getIpAddresses();

        if (!isset($ips[$mainIp])) {
            throw new pm_Exception('Unknown IP address.');
        }

        $apiResponse = pm_ApiRpc::getService()->call(
            '<ip>' . 
                '<set>' . 
                    "<filter><ip_address>$mainIp</ip_address></filter>" .
                    "<public_ip_address>$publicIp</public_ip_address>" .
                '</set>' .
            '</ip>');

        if ('error' === (string)$apiResponse->ip->set->result->status) {
            throw new pm_Exception($apiResponse->ip->set->result->errtext);
        }
    }

}
