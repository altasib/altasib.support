<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("altasib.support"))
{
        ShowError(GetMessage("ALTASIB_SUPPORT_MODULE_NOT_INSTALL"));
        return;
}

$arComponentParameters = array(
        "GROUPS" => array(
        ),
        "PARAMETERS" => array(
                "FILE_HASH" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("ALTASIB_SUPPORT_FILE_HASH"),
                        "TYPE" => "STRING",
                        "DEFAULT" => '={$_REQUEST["HASH"]}',
                ),
                "PROFILE_PATH" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("ALTASIB_SUPPORT_PROFILE_PATH"),
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
                ),                                        
        ),
);
?>