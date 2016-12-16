<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2016 ALTASIB             #
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule("altasib.support"))
        return;

$Role = $APPLICATION->GetUserRight("altasib.support");
if(ALTASIB\Support\Tools::IsSupportTeam($USER->GetID()))
{

        $aMenu = array(
                "parent_menu" => "global_menu_services",
                "section" => "ALTASIB_SUPPORT",
                "sort" => 200,
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_MAIN"),
                "title" => "",
                "icon" => "altasib_support_menu_icon",
                "page_icon" => "altasib_support_page_icon",
                "items_id" => "menu_altasib_support_list",
                "items" => array()
        );
}
if($Role>="W")
{
        $aMenu["items"][1] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING"),
                "items_id" => "menu_ir_settings",
                "items" => array()
        );

        $aMenu["items"][1]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_CATEGORY"),
                "url" => "altasib_support_settings_category.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_settings_category_edit.php"),
        );

        $aMenu["items"][1]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_STATUS"),
                "url" => "altasib_support_settings_status.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_settings_status_edit.php"),
        );

        $aMenu["items"][1]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_SLA"),
                "url" => "altasib_support_settings_sla.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_settings_sla_edit.php"),
        );
        
        $aMenu["items"][2] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_CONN"),
                "items_id" => "menu_support_conn",
                "items" => array()
        );
        $aMenu["items"][2]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_CONN_CÑ"),
                "url" => "altasib_support_client2clientworker.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_client2clientworker_edit.php"),
        );
        $aMenu["items"][2]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_CONN_EC"),
                "url" => "altasib_support_worker2client.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_worker2client_edit.php"),
        );
        
        $aMenu["items"][1]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_WORKERS"),
                "url" => "altasib_support_workers.php?lang=".LANGUAGE_ID,
        );        

        $aMenu["items"][1]["items"][] = array(
                "text" => GetMessage("ALTASIB_SUPPORT_MENU_SETTING_QR"),
                "url" => "altasib_support_settings_quick_response.php?lang=".LANGUAGE_ID,
                "more_url" => Array("altasib_support_settings_quick_response_edit.php"),
        );        
        
}        
        return $aMenu;
?>