<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

IncludeModuleLangFile(__FILE__);

$module_id = "altasib.support";
$RF_RIGHT = $APPLICATION->GetGroupRight($module_id);

if (!CModule::IncludeModule($module_id)) {
    die();
}
use ALTASIB\Support;
use Bitrix\Main;
use Bitrix\Main\Type;

if (isset($_REQUEST["import"]) && $_REQUEST["import"] == "go") {
    define("SUP_IMPORT", true);
    CModule::IncludeModule("support");

    $Status = array();
    $obStatus = Support\StatusTable::getList();
    while ($arStatus = $obStatus->fetch()) {
        $Status[$arStatus["ID"]] = $arStatus['NAME'];
    }

    $arPriority = Support\Priority::get();
    $arCategory = array();
    $dataCategory = Support\CategoryTable::getList();
    while ($ar = $dataCategory->fetch()) {
        $arCategory[$ar["NAME"]] = $ar;
    }

    $i = 0;
    $obTickets = CTicket::GetList($by = "s_id", $order = "asc", Array(/*"ID"=>1164*/), $if, $a = "N");
    while ($arTicket = $obTickets->Fetch()) {
        $statusId = 0;
        if (strlen($arTicket['STATUS_NAME']) > 0) {
            $statusId = array_search($arTicket['STATUS_NAME'], $Status);
        }

        $PRIORITY_ID = '';
        if (strlen($arTicket["CRITICALITY_NAME"]) > 0) {
            foreach ($arPriority as $k => $priority) {
                if (substr($priority, 0, 4) == substr($arTicket["CRITICALITY_NAME"], 0, 4)) {
                    $PRIORITY_ID = $k;
                    break;
                }
            }
        }

        $categoryId = 1;
        if (strlen($arTicket['CATEGORY_NAME']) > 0) {
            if (array_key_exists($arTicket['CATEGORY_NAME'], $arCategory)) {
                $categoryId = $arCategory[$arTicket['CATEGORY_NAME']]['ID'];
            }
        }

        $arTicketFields = Array(
            "TITLE" => $arTicket["TITLE"],
            "OWNER_USER_ID" => $arTicket["OWNER_USER_ID"],
            "CREATED_USER_ID" => $arTicket["CREATED_USER_ID"],
            "MODIFIED_USER_ID" => $arTicket["MODIFIED_USER_ID"],
            //"CREATED_GUEST_ID"=>$arTicket["CREATED_GUEST_ID"],
            "CATEGORY_ID" => $categoryId,
            "PRIORITY_ID" => $PRIORITY_ID > 0 ? $PRIORITY_ID : "",
            "RESPONSIBLE_USER_ID" => $arTicket["RESPONSIBLE_USER_ID"],
            "DATE_CLOSE" => new Type\DateTime($arTicket["DATE_CLOSE"]),
            "IS_CLOSE" => strlen($arTicket["DATE_CLOSE"]) > 0 ? "Y" : "N",
            "STATUS_ID" => $statusId,
            'FILES' => array(),
        );

        $skipMess = 0;
        $by = "s_id";
        $order = "asc";
        $obTicketMess = CTicket::GetMessageList($by, $order,
            array("TICKET_ID" => $arTicket["ID"], "TICKET_ID_EXACT_MATCH" => "Y"), $c, $a = "N");
        while ($arTicketMess = $obTicketMess->Fetch()) {
            $skipMess = $arTicketMess['ID'];
            $arTicketFields['MESSAGE'] = $arTicketMess['MESSAGE'];
            $arTicketFields['MESSAGE'] = str_replace(
                array('<B>', '</B>', '<QUOTE>', '</QUOTE>', '<CODE>', '</CODE>', '<I>', '</I>', '<U>', '</U>'),
                array('[B]', '[/B]', '[QUOTE]', '[/QUOTE]', '[CODE]', '[/CODE]', '[I]', '[/I]', '[U]', '[/U]'),
                $arTicketMess['MESSAGE']
            );
            $arTicketFields['LAST_MESSAGE_DATE'] = new Type\DateTime($arTicketMess['DATE_CREATE']);
            $rsFiles = CTicket::GetFileList($v1 = "s_id", $v2 = "asc",
                array("TICKET_ID" => $arTicket["ID"], 'MESSAGE_ID' => $arTicketMess['ID']));
            {
                while ($arFile = $rsFiles->Fetch()) {
                    $arTicketFields['FILES'][] = $arFile['ID'];
                }
            }
            break;
        }

        $result = Support\TicketTable::add($arTicketFields);
        $TICKET_ID = $result->getId();
        if ($result->isSuccess()) {
            $by = "s_id";
            $order = "asc";
            $obTicketMess = CTicket::GetMessageList($by, $order, array(
                "TICKET_ID" => $arTicket["ID"],
                "TICKET_ID_EXACT_MATCH" => "Y"
                /*,'IS_LOG'=>'N'*/,
                'IS_HIDDEN' => 'N'
            ), $c, $a = "N");
            while ($arTicketMess = $obTicketMess->Fetch()) {
                if ($arTicketMess["ID"] == $skipMess) {
                    continue;
                }

                $arTicketMessage = Array(
                    'DATE_CREATE' => new Type\DateTime($arTicketMess['DATE_CREATE']),
                    "CREATED_USER_ID" => $arTicketMess["CREATED_USER_ID"],
                    "MODIFIED_USER_ID" => $arTicketMess["MODIFIED_USER_ID"],
                    "TICKET_ID" => $TICKET_ID,
                    "MESSAGE" => $arTicketMess["MESSAGE"],
                    'FILES' => array(),
                );
                if ($arTicketMess['IS_LOG'] == 'Y') {
                    $arTicketMessage['MESSAGE'] = str_replace(
                        array('<li>'),
                        array('[*]'),
                        $arTicketMessage['MESSAGE']
                    );
                    $arTicketMessage['MESSAGE'] = '[LIST]' . $arTicketMessage['MESSAGE'] . '[/LIST]';
                } else {
                    $arTicketMessage['MESSAGE'] = str_replace(
                        array('<B>', '</B>', '<QUOTE>', '</QUOTE>', '<CODE>', '</CODE>', '<I>', '</I>', '<U>', '</U>'),
                        array('[B]', '[/B]', '[QUOTE]', '[/QUOTE]', '[CODE]', '[/CODE]', '[I]', '[/I]', '[U]', '[/U]'),
                        $arTicketMessage['MESSAGE']
                    );
                }
                $rsFiles = CTicket::GetFileList($v1 = "s_id", $v2 = "asc",
                    array("TICKET_ID" => $arTicket["ID"], 'MESSAGE_ID' => $arTicketMess['ID']));
                {
                    while ($arFile = $rsFiles->Fetch()) {
                        $arTicketMessage['FILES'][] = $arFile['ID'];
                    }
                }
                Support\TicketMessageTable::add($arTicketMessage);
            }

        }
        $i++;
    }
    ShowNote(GetMessage('ALTASIB_SUPPORT_IMPORTED') . ':' . $i);
}

$arStatuss = Array();
$obStatus = Support\StatusTable::getList(array('order' => array("SORT" => "ASC")));
while ($arStatus = $obStatus->Fetch()) {
    $arStatuss[$arStatus["ID"]] = $arStatus["NAME"];
}

$arAllOptions = Array(
    "MAIN" => Array(
        Array("SUPPORT_MAIL", GetMessage("ALTASIB_SUPPORT_OPTIONS_SUPPORT_MAIL") . ":", "", Array("text")),
        Array(
            "DEFAULT_STATUS",
            GetMessage("ALTASIB_SUPPORT_OPTIONS_DEFAULT_STATUS") . ":",
            "7",
            Array("selectbox", $arStatuss)
        ),
        Array("RE_STATUS", GetMessage("ALTASIB_SUPPORT_OPTIONS_RE_STATUS") . ":", "7", Array("selectbox", $arStatuss)),
        Array(
            "FINAL_STATUS",
            GetMessage("ALTASIB_SUPPORT_OPTIONS_FINAL_STATUS") . ":",
            "7",
            Array("selectbox", $arStatuss)
        ),
        Array(
            "SET_STATUS",
            GetMessage("ALTASIB_SUPPORT_OPTIONS_SET_STATUS") . ":",
            "7",
            Array("selectbox", array_merge(array('0' => '-'), $arStatuss))
        ),
        Array("AUTO_CLOSE", GetMessage("ALTASIB_SUPPORT_OPTIONS_AUTO_CLOSE") . ":", "7", Array("text")),
    ),
    "ALLOW_TAGS" => Array(
        Array("FORM_ALLOW_BIU", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_BIU") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_FONT", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_FONT") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_QUOTE", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_QUOTE") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_CODE", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_CODE") . ":", "Y", Array("checkbox")),
        Array(
            "FORM_ALLOW_ANCHOR",
            GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_ANCHOR") . ":",
            "Y",
            Array("checkbox")
        ),
        Array("FORM_ALLOW_IMG", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_IMG") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_TABLE", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_TABLE") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_LIST", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_LIST") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_NL2BR", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_NL2BR") . ":", "Y", Array("checkbox")),
        Array("FORM_ALLOW_VIDEO", GetMessage("ALTASIB_SUPPORT_OPTIONS_FORM_ALLOW_VIDEO") . ":", "Y", Array("checkbox")),
    ),
);
$arAllOptions['PATH'] = array();
$arAllOptions['PATH_GET_FILE'] = array();

$arSiteList = array();
$SubTabs = array();
$rsSites = CSite::GetList($by = "sort", $order = "desc");
while ($arSite = $rsSites->Fetch()) {
    $arSiteList[] = $arSite;
    $SubTabs[] = array(
        "DIV" => "opt_site_" . $arSite["ID"],
        "TAB" => "(" . htmlspecialcharsbx($arSite["ID"]) . ") " . htmlspecialcharsbx($arSite["NAME"]),
        'TITLE' => GetMessage('ALTASIB_SUPPORT_OPTIONS_SITE_SETTINGS') . ' ' . htmlspecialcharsbx($arSite["ID"])
    );
}

$arDefaultPathValues = array(
    'path_list' => '/support/',
    'path_detail' => '/support/ticket/#ID#/',
    'path_file' => '/support/ticket/#ID#/file/#FILE_HASH#/',
    'path_group_list' => '/workgroups/group/#group_id#/support/',
    'path_group_detail' => '/workgroups/group/#group_id#/support/ticket/#ID#/',
    'path_group_file' => '/workgroups/group/#group_id#/support/ticket/#ID#/file/#FILE_HASH#/',
);

if ($REQUEST_METHOD == "POST" && strlen($Update) > 0 && $RF_RIGHT == "W" && check_bitrix_sessid()) {

    foreach ($arAllOptions as $aOptGroup) {
        foreach ($aOptGroup as $option) {
            if (!is_array($option)) {
                continue;
            }
            $name = $option[0];
            $val = ${"option_" . $name};
            if ($option[3][0] == "checkbox" && $val != "Y") {
                $val = "N";
            }
            if ($option[3][0] == "multiselectbox") {
                $val = @implode(",", $val);
            }
            COption::SetOptionString($module_id, $name, $val, $option[1]);
        }
    }

    foreach ($arSiteList as $site) {
        foreach ($arDefaultPathValues as $key => $value) {
            if (isset($_POST[$key . "_" . $site["LID"]])) {
                COption::SetOptionString("altasib.support", $key, $_POST[$key . "_" . $site["LID"]], false,
                    $site["LID"]);
            }
        }
    }

    // setting rights groups
    COption::SetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $GROUP_DEFAULT_RIGHT, "Right for groups by default");
    reset($arGROUPS);
    while (list(, $value) = each($arGROUPS)) {
        $rt = ${"RIGHTS_" . $value["ID"]};
        if (strlen($rt) > 0 && $rt != "NOT_REF") {
            $APPLICATION->SetGroupRight($module_id, $value["ID"], $rt);
        } else {
            $APPLICATION->DelGroupRight($module_id, array($value["ID"]));
        }
    }

    if ($_REQUEST["back_url_settings"] <> "" && $_REQUEST["Apply"] == "") {
        echo '<script type="text/javascript">window.location="' . CUtil::addslashes($_REQUEST["back_url_settings"]) . '";</script>';
    }
}

function ShowParamsHTMLByArray($arParams)
{
    foreach ($arParams as $Option) {
        if (!is_array($Option)):
            ?>
            <tr class="heading">
                <td valign="top" colspan="2" align="center"><b><?= $Option ?></b></td>
            </tr>
            <?
        else:
            $val = COption::GetOptionString('altasib.support', $Option[0], $Option[2]);

            $type = $Option[3];
            $Option[0] = "option_" . $Option[0];
            ?>
            <tr>
                <td valign="top" width="60%"><?
                    if ($type[0] == "checkbox") {
                        echo "<label for='" . htmlspecialcharsBx($Option[0]) . "'>" . $Option[1] . "</label>";
                    } else {
                        echo $Option[1];
                    }
                    ?></td>
                <td valign="middle" width="40%"><?
                    if ($type[0] == "checkbox"):
                        ?><input type="checkbox" id="<? echo htmlspecialcharsBx($Option[0]) ?>"
                                 name="<? echo htmlspecialcharsBx($Option[0]) ?>" value="Y"<? if ($val == "Y") {
                        echo " checked";
                    } ?>><?
                    elseif ($type[0] == "text"):
                        ?><input type="text" size="<? echo $type[1] ?>" maxlength="255"
                                 value="<?= htmlspecialcharsBx($val) ?>"
                                 name="<? echo htmlspecialcharsBx($Option[0]) ?>"><?
                    elseif ($type[0] == "selectbox"):
                        $arr = $type[1];
                        $arr_keys = array_keys($arr);
                        ?>
                    <select name="<? echo htmlspecialcharsBx($Option[0]) ?>">
                        <?
                        for ($j = 0; $j < count($arr_keys); $j++):
                            ?>
                            <option
                                value="<? echo $arr_keys[$j] ?>"<? if ($val == $arr_keys[$j]) echo " selected" ?>><? echo htmlspecialcharsBx($arr[$arr_keys[$j]]) ?></option>
                            <?
                        endfor;
                        ?></select><?
                    elseif ($type[0] == "multiselectbox"):
                        $arr = $type[1];
                        $arr_keys = array_keys($arr);
                        $arr_val = explode(",", $val);
                        ?><select size="5" multiple name="<? echo htmlspecialcharsBx($Option[0]) ?>[]"><?
                        for ($j = 0; $j < count($arr_keys); $j++):
                            ?>
                            <option value="<? echo $arr_keys[$j] ?>"<? if (in_array($arr_keys[$j],
                            $arr_val)) echo " selected" ?>><? echo htmlspecialcharsBx($arr[$arr_keys[$j]]) ?></option><?
                        endfor;
                        ?></select><?
                    elseif ($type[0] == "textarea"):
                        ?><textarea rows="<? echo $type[1] ?>" cols="<? echo $type[2] ?>"
                                    name="<? echo htmlspecialcharsBx($Option[0]) ?>"><? echo htmlspecialcharsBx($val) ?></textarea><?
                    endif;
                    ?></td>
            </tr>
            <?
        endif;
    }
}

?>
<?
$subTabControl = new CAdminViewTabControl("subTabControl", $SubTabs);

$aTabs = array(
    array(
        "DIV" => "main",
        "TAB" => GetMessage("ALTASIB_SUPPORT_OPTIONS_MAIN"),
        "ICON" => "references_settings",
        "TITLE" => GetMessage("ALTASIB_SUPPORT_OPTIONS_MAIN_TITLE")
    ),
    array(
        "DIV" => "rights",
        "TAB" => GetMessage("MAIN_TAB_RIGHTS"),
        "ICON" => "references_settings",
        "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
    <form method="POST" name="opt_ir" id="opt_ir"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsBx($mid) ?>&amp;lang=<? echo LANG ?>">
        <? $tabControl->BeginNextTab(); ?>
        <? ShowParamsHTMLByArray($arAllOptions["MAIN"]); ?>
        <tr class="heading">
            <td colspan="2" valign="top" align="center"><? echo GetMessage("ALTASIB_SUPPORT_ALLOW_TAGS") ?></td>
        </tr>
        <? ShowParamsHTMLByArray($arAllOptions["ALLOW_TAGS"]); ?>

        <tr class="heading">
            <td colspan="2" valign="top" align="center"><? echo GetMessage("ALTASIB_SUPPORT_OPTIONS_PATHS") ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <? $subTabControl->Begin(); ?>
                <? foreach ($arSiteList as $site): ?>
                    <? $subTabControl->BeginNextTab(); ?>
                    <table width="75%" align="center">
                        <? foreach ($arDefaultPathValues as $key => $value):if ($site['DIR'] != '/') {
                            $value = $site['DIR'] . substr($value, 1);
                        } ?>
                            <tr>
                                <td align="right"><?= GetMessage("ALTASIB_SUPPORT_" . strtoupper($key)) ?>:</td>
                                <td><input type="text" size="40"
                                           value="<?= COption::GetOptionString("altasib.support", $key, $value,
                                               $site["LID"]) ?>"
                                           name="<?= $key ?>_<?= htmlspecialcharsbx($site["LID"]); ?>"></td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                <? endforeach; ?>
                <? $subTabControl->End(); ?>
            </td>
        </tr>
        <? $tabControl->BeginNextTab(); ?>
        <? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>
        <? $tabControl->Buttons(); ?>


        <input type="submit" <? if ($RF_RIGHT < "W") echo "disabled" ?> name="Update"
               value="<? echo GetMessage("MAIN_SAVE") ?>">
        <input type="reset" name="reset"
               onClick="document.getElementById('site_select_id').disabled=<? if (COption::GetOptionString($module_id,
                       "different_set", "N") != "Y"
               ) {
                   echo "true";
               } else {
                   echo "false";
               } ?>; SelectSite('<? echo htmlspecialcharsBx($siteList[0]["ID"]) ?>');"
               value="<? echo GetMessage("MAIN_RESET") ?>">
        <input type="hidden" name="Update" value="Y">
        <?= bitrix_sessid_post() ?>
        <? $tabControl->End(); ?>
    </form>
<? if (IsModuleInstalled("support")): ?>
    <a href="<?= $APPLICATION->GetCurPageParam("import=go"); ?>"><?= GetMessage('ALTASIB_SUPPORT_LOAD_OLD_SUPPORT_TICKET') ?></a>
<? endif; ?>