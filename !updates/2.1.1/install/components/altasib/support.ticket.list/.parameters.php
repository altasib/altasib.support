<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
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
                "SHOW_FILTER" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("ALTASIB_SUPPORT_TL_P_SHOW_FILTER"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
                ),
                "NUM_TICKETS" => Array(
                        "NAME" => GetMessage("ALTASIB_SUPPORT_TL_P_NUM_TP"),
                        "TYPE" => "STRING",
                        "MULTIPLE" => "N",
                        "PARENT" => "ADDITIONAL_SETTINGS",
                        "DEFAULT" => "10"
                ),

        ),
);
?>