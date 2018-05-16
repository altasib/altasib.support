<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

global $APPLICATION, $DBType;
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arClassesList = array(
        "ALTASIB\Support\WtCTable" => "lib/ties/worker2client.php",
        "ALTASIB\Support\C2CWTable" => "lib/ties/client2clientworker.php",
        "ALTASIB\Support\User" => "lib/user.php",
        "ALTASIB\Support\Priority" => "lib/priority.php",
        "ALTASIB\Support\Tools" => "lib/tools.php",
        "ALTASIB\Support\UserTable" => "lib/userT.php",
);

CModule::AddAutoloadClasses(
        "altasib.support",
        $arClassesList
);


CJSCore::RegisterExt('jq_chosen', array(
        'js' => array('/bitrix/js/altasib.support/chosen.jquery.min.js'),
        'css' => array('/bitrix/js/altasib.support/chosen.min.css'),
        'rel' => array('jquery')
));
CJSCore::RegisterExt('add_js_css', array(
        'js' => array('/bitrix/js/altasib.support/highlight/highlight.pack.js', '/bitrix/js/altasib.support/ui/jquery-ui.min.js'),
        'css' => array('/bitrix/js/altasib.support/highlight/styles/default.css', '/bitrix/js/altasib.support/font/font-awesome.min.css', '/bitrix/js/altasib.support/media.css'),
        'rel' => array('jquery')
));