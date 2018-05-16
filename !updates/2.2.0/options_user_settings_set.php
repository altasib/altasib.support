<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

$altasib_supportWarningTmp = "";
$altasib_support_res = true;
use Bitrix\Main;
use ALTASIB\Support;
use Bitrix\Main\Type;
use ALTASIB\Support\Tools;

if (Main\Loader::includeModule("altasib.support") && check_bitrix_sessid())
{
	$dataSupportUser = Array(
	);

	if ($USER->IsAdmin())
    {
        $isSupportTeam = Tools::IsSupportTeam($ID);
        if(!$isSupportTeam && $altasib_support_RESPONSIBLE_USER_ID>0)
        {
            $dataSupportUser["RESPONSIBLE_USER_ID"] = (int)$altasib_support_RESPONSIBLE_USER_ID;
        
        	$data = Support\ClientTable::getList(array('filter'=>array("USER_ID" => $ID),'select'=>array('ID')));
        	if ($arData=$data->fetch())
        	{
        		Support\ClientTable::update($arData["ID"], $dataSupportUser);
        	}
            else
        	{
        		$dataSupportUser["USER_ID"] = $ID;
                Support\ClientTable::add($dataSupportUser);
        	}
        }
    }    
}