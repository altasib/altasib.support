<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

if (!Main\Loader::includeModule("altasib.support")) {
    return;
}

use ALTASIB\Support;

Loc::loadMessages(__FILE__);

$UserRight = $APPLICATION->GetUserRight("altasib.support");
if ($UserRight < "W") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "altasib_support_settings_sla_list";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$adminList = new CAdminList($sTableID, $oSort);

$request = Main\Context::getCurrent()->getRequest();

if ($adminList->EditAction()) {
    foreach ($FIELDS as $ID => $arFields) {
        if (!$adminList->IsUpdated($ID)) {
            continue;
        }

        $result = Support\SlaTable::update($ID, $arFields);
        if (!$result->isSuccess()) {
            $adminList->AddUpdateError("(ID=" . $ID . ") " . implode("<br>", $result->getErrorMessages()), $ID);
        }

    }
}
if ($arID = $adminList->GroupAction()) {
    if ($_REQUEST['action_target'] == 'selected') {
        $obC = Support\SlaTable::getList(array('order' => Array($by => $order), 'select' => $arFilter));
        while ($arRes = $obC->fetch()) {
            $arID[] = $arRes['ID'];
        }
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0) {
            continue;
        }

        switch ($_REQUEST['action']) {
            case "delete":
                $result = Support\SlaTable::delete($ID);
                if (!$result) {
                    $adminList->AddGroupError("(ID=" . $ID . ") " . implode("<br>", $result->getErrorMessages()), $ID);
                }
                break;
        }
    }
}
$APPLICATION->SetTitle(Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_TITLE"));

$map = Support\SlaTable::getMap();
$adminList->AddHeaders(array(
    array("id" => "NAME", "content" => $map['NAME']['title'], "sort" => "", "default" => true),
    array("id" => "DESCRIPTION", "content" => $map['DESCRIPTION']['title'], "default" => true),
    array("id" => "RESPONSE_TIME", "content" => $map['RESPONSE_TIME']['title'], "default" => true),
    array("id" => "NOTICE_TIME", "content" => $map['NOTICE_TIME']['title'], "default" => false),
    array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
    array("id" => "SORT", "content" => $map['SORT']['title'], "sort" => "SORT", "default" => false),
));

$data = Support\SlaTable::getList(array('order' => array($by => $order)));
$data = new CAdminResult($data, $sTableID);
$data->NavStart();
$adminList->NavText($data->GetNavPrint(Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_NAV_TITLE")));

while ($sla = $data->Fetch()) {
    $EditLink = "altasib_support_settings_sla_edit.php?lang=" . LANG . "&ID=" . $sla['ID'];
    $row =& $adminList->AddRow($sla['ID'], $sla, $EditLink);

    $row->AddInputField("NAME", array("size" => 25));
    $row->AddViewField("NAME", $sla['NAME']);

    $row->AddInputField("RESPONSE_TIME", array("size" => 25));
    $row->AddViewField("RESPONSE_TIME", $sla['RESPONSE_TIME']);

    $row->AddInputField("SORT", array("size" => 25));
    $row->AddViewField("SORT", $sla["SORT"]);

    $row->AddViewField("ID", "<a href=\"$EditLink\">" . $sla['ID'] . "</a>");
}

$adminList->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $data->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);

$aContext = array(
    array(
        "TEXT" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_CONTEXT_ADD"),
        "ICON" => "btn_new",
        "LINK" => "altasib_support_settings_sla_edit.php?lang=" . LANG,
        "TITLE" => Loc::getMessage("ALTASIB_SUPPORT_SETTINGS_SLA_CONTEXT_ADD"),
        "LINK_PARAM" => "",
    ),
);
$adminList->AddAdminContextMenu($aContext);
$adminList->AddGroupActionTable(Array("delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE")));
$chain = $adminList->CreateChain();
$adminList->ShowChain($chain);
$adminList->CheckListMode();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$adminList->DisplayList();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");