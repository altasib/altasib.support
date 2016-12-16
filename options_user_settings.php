<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeni Pedan                     #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.support/options_user_settings.php");
use Bitrix\Main;
use ALTASIB\Support;
use ALTASIB\Support\Tools;
use Bitrix\Main\Localization\Loc;
if($ID<=0)
    return;
    
if (Main\Loader::includeModule("altasib.support"))
{
    ClearVars("str_altasib_support_");
    $data = Support\ClientTable::getList(array('filter'=>array("USER_ID" => $ID)));
	if (!$arClient = $data->fetch())
	{
			$arClient['RESPONSIBLE_USER_ID'] = '';
	}    
	if (strlen($strError)>0)
	{
		$DB->InitTableVarsForEdit("altasib_support_user", "altasib_support_", "str_altasib_support_");
		$DB->InitTableVarsForEdit("b_user", "altasib_support_", "str_altasib_support_");
        
        $arClient['RESPONSIBLE_USER_ID'] = $str_altasib_support_RESPONSIBLE_USER_ID;
	}
    
    $isSupportTeam = Tools::IsSupportTeam($ID);
	?>
	<input type="hidden" name="profile_module_id[]" value="altasib.support">
	<?if ($USER->IsAdmin())
    {?>
    <?if(!$isSupportTeam):?>
		<tr>
			<td width="40%"><?=GetMessage("altasib_support_RESPONSIBLE_USER_ID")?></td>
			<td width="60%"><?=FindUserID('altasib_support_RESPONSIBLE_USER_ID',$arClient['RESPONSIBLE_USER_ID'],'','user_edit_form');?></td>
		</tr>
    <?endif;?>        
<?
    }
}
?>