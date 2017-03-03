<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgenió Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2013 ALTASIB             #
#################################################
?>
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("altasib.support"))
{
        ShowError("ALTASIB_SUPPORT_MODULE_NOT_INSTALL");
        return;
}

function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;
  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    else
      $merged [$key] = $value;
  }
return $merged;
}
//list
CComponentUtil::__IncludeLang("/bitrix/components/altasib/support.ticket.list/",".parameters.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/altasib/support.ticket.list/.parameters.php");
$arComponentParametersList = $arComponentParameters;
unset($arComponentParameters);
//add
CComponentUtil::__IncludeLang("/bitrix/components/altasib/support.ticket.form/",".parameters.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/altasib/support.ticket.form/.parameters.php");
$arComponentParametersAdd = $arComponentParameters;
unset($arComponentParameters);

//detail
CComponentUtil::__IncludeLang("/bitrix/components/altasib/support.ticket.detail/",".parameters.php");                
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/altasib/support.ticket.detail/.parameters.php");                
$arComponentParametersDetail = $arComponentParameters;
unset($arComponentParameters);

$arComponentParameters = array(
        "PARAMETERS" => array(
                "VARIABLE_ALIASES" => Array(
                        "TICKET_ID" => Array("NAME" => GetMessage("ALTASIB_SUPPORT_VAR_TICKET_ID_DESC")),
                        "FILE_HASH" => Array("NAME" => GetMessage("ALTASIB_SUPPORT_VAR_FILE_HASH_DESC")),
                ),        
                "SEF_MODE" => Array(
                        "ticket_list" => array(
                                "NAME" => GetMessage("ALTASIB_SUPPORT_SEF_LIST_DESC"),
                                "DEFAULT" => "",
                                "VARIABLES" => array(),
                        ),
                        "ticket_detail" => array(
                                "NAME" => GetMessage("ALTASIB_SUPPORT_SEF_DETAIL_DESC"),
                                "DEFAULT" => "ticket/#ID#/",
                                "VARIABLES" => array(),
                        ),
                        "get_file" => array(
                                "NAME" => GetMessage("ALTASIB_SUPPORT_SEF_FILE_DESC"),
                                "DEFAULT" => "ticket/#ID#/file/#FILE_HASH#/",
                                "VARIABLES" => array(),
                        ),                        
                        "desktop" => array(
                                "NAME" => GetMessage("ALTASIB_SUPPORT_SEF_DESKTOP_DESC"),
                                "DEFAULT" => "desktop/",
                                "VARIABLES" => array(),
                        ),                        
                ),
)
);

$arComponentParametersPre = array_merge_recursive_distinct($arComponentParametersList,$arComponentParametersAdd);
$arComponentParameters = array_merge_recursive_distinct($arComponentParameters,$arComponentParametersPre,$arComponentParametersDetail);
?>