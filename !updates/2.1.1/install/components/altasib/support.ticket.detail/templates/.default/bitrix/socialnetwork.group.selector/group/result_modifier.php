<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$myGroupCache = new CPHPCache;
$cachePath = str_replace(array(":", "//"), "/", "/".SITE_ID."/".$componentName);
$cacheId = "socnet_user_groups_".SITE_ID.'_'.$arParams["GROUPS_PAGE_SIZE"]."_".$USER->GetID()."_".$arParams['SUPPORT_ROLE'];
$cacheTime = 31536000;
$cacheTime=0;
if ($myGroupCache->InitCache($cacheTime, $cacheId, $cachePath))
{
	$vars = $myGroupCache->GetVars();
	$arResult["MY_GROUPS"] = $vars["arMyGroups"];
}
else
{
	if (defined("BX_COMP_MANAGED_CACHE"))
	{
		$GLOBALS["CACHE_MANAGER"]->StartTagCache($cachePath);
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("sonet_user2group_U".$USER->GetID());
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("sonet_group");
	}
    
    $arResult["MY_GROUPS"] = array();
    
    if($arParams['SUPPORT_ROLE'] == 'W')
    {
        $data = Bitrix\Socialnetwork\WorkgroupTable::getList(array(
            'select' => array('ID','NAME','DESCRIPTION'),
            'filter' => array('CLOSED'=>'N')
        ));
        
        while($group = $data->fetch())
        {
            $arResult["MY_GROUPS"][] = array(
                'ID' => $group['ID'],
                'id' => $group['ID'],
                'title' => $group['NAME'],
                'description' => $group['DESCRIPTION']
            );
        }
    }
    else
    {
        $filter = array(
			"USER_ID" => $USER->GetID(),
			"GROUP_ACTIVE" => "Y"
        );
        if($arParams['SUPPORT_ROLE'] == 'C')
            $filter["<=ROLE"] = SONET_ROLES_MODERATOR;        
        
        if($arParams['SUPPORT_ROLE'] == 'E')
            $filter["<=ROLE"] = SONET_ROLES_USER;        
        
    	
    	$rsGroups = CSocNetUserToGroup::GetList(
    		array("GROUP_NAME" => "ASC"),
            $filter,
    		false,
    		array("nPageSize" => $arParams["GROUPS_PAGE_SIZE"], "bDescPageNumbering" => false),
    		array("ID", "GROUP_ID", "GROUP_NAME", "GROUP_DESCRIPTION", "GROUP_IMAGE_ID")
    	);
    	while($arGroup = $rsGroups->Fetch())
    	{
    		if (
    			isset($GLOBALS["arExtranetGroupID"])
    			&& is_array($GLOBALS["arExtranetGroupID"])
    			&& in_array($arGroup["GROUP_ID"], $GLOBALS["arExtranetGroupID"])
    		)
    		{
    			$arGroup["GROUP_IS_EXTRANET"] = "Y";
    		}
    
    		$arResult["MY_GROUPS"][] = group2JSItem($arGroup, "GROUP_");
    	}
    }
        //p($arResult["MY_GROUPS"]);
	if (defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->EndTagCache();

	$myGroupCache->StartDataCache($cacheTime, $cacheId, $cachePath);
	$myGroupCache->EndDataCache(array("arMyGroups" => $arResult["MY_GROUPS"]));
}        
?>