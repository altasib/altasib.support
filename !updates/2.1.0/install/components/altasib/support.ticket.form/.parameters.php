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
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}

$arComponentParameters = array(
        "GROUPS" => array(
        ),
        "PARAMETERS" => array(
                /*"AJAX_MODE" => array(),*/
                "SHOW_FULL_FORM" => array(
                        "NAME" => GetMessage("ALTASIB_SUPPORT_SHOW_FULL_FORM"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
                ),                
        ),
);
$arComponentParameters["PARAMETERS"]["TICKET_LIST_URL"] = Array(
        "PARENT" => "URL",
        "NAME" => GetMessage("I_RECEPTION_P_TICKET_LIST"),
        "TYPE" => "STRING",
        "DEFAULT" => '/support/',
        "SORT" => 30,
);
$arComponentParameters["PARAMETERS"]["ID"] = Array(
        "PARENT" => "SETTINGS",
        "NAME" => GetMessage("I_RECEPTION_P_TICKET_ID"),
        "TYPE" => "STRING",
        "DEFAULT" => '={$_REQUEST["ID"]}',
        "SORT" => 30,
);

?>