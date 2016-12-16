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

$arComponentDescription = array(
        "NAME" => GetMessage("ALTASIB_SUPPORT"),
        "DESCRIPTION" => GetMessage("ALTASIB_SUPPORT_DESC"),
        "ICON" => "/images/icon.gif",
        "CACHE_PATH" => "Y",
        "COMPLEX" => "Y",
        "PATH" => array(
                "ID" => "IS-MARKET.RU",
                "NAME" => GetMessage("ALTASIB_DESC_SECTION_NAME"),
                "CHILD" => array(
                                "ID" => "altasib_support_cmpx",
                                "NAME" => GetMessage("ALTASIB_DESC_SUPPORT_SECTION_NAME"),
                ),
        ),
);
?>
