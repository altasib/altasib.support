<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use ALTASIB\Support;
use Bitrix\Main;

if (!isset($arParams["CACHE_TIME"])) {
    $arParams["CACHE_TIME"] = 3600;
}

if (!Main\Loader::includeModule("altasib.support")) {
    ShowError(GetMessage("ALTASIB_SUPPORT_MODULE_NOT_INSTALL"));
    return;
}
CPageOption::SetOptionString("main", "nav_page_in_session", "N");
$arParams["NUM_TICKETS"] = (int)$arParams["NUM_TICKETS"] == 0 ? 10 : (int)$arParams["NUM_TICKETS"];
$arParams["CREATE_URL"] = htmlspecialchars(CComponentEngine::MakePathFromTemplate($arParams["URL_DETAIL"],
    Array("ID" => "0", "TICKET_ID" => 0, "CODE" => 0, 'group_id' => $arParams['GROUP_ID'])));
$arParams["SHOW_FILTER"] = $arParams["SHOW_FILTER"] == "Y" ? true : false;
$arParams['GRID_ID'] = 'altasib_support_list';
$arParams["GROUP_ID"] = (int)$arParams["GROUP_ID"];

if (!($arParams['Right'] instanceof ALTASIB\Support\Rights)) {
    $arParams['Right'] = new ALTASIB\Support\Rights($USER->getId());
}

$arParams["ROLE"] = $Role = $arParams['Right']->getRole();
if ($arParams['Right']->getRole() == 'D') {
    $APPLICATION->AuthForm('');
}

$arParams["HAS_CREATE"] = $arParams['SUPPORT_TEAM'] = $arParams['Right']->isSupportTeam();

$arResult = Array();
$gridFilter = array();
$f = Support\TicketTable::getUserRoleFilter(false);
$roleFilter = $f['roleFilter'];
$filter = $f['filter'];
if ($f['HAS_CREATE']) {
    $arParams["HAS_CREATE"] = true;
}

$grid_options = new CGridOptions($arParams["GRID_ID"]);
$aNav = $grid_options->GetNavParams(array("nPageSize" => $arParams["NUM_TICKETS"]));
$arParams['TICKET_MAP'] = Support\TicketTable::getMap();

$arParams['GRID']['HEADER'] = array(
    array(
        "id" => "ID",
        "name" => $arParams['TICKET_MAP']['ID']['title'],
        "sort" => "ID",
        "default" => true,
        "editable" => false
    ),
    array(
        "id" => "DATE_CREATE",
        'type' => 'date',
        "name" => $arParams['TICKET_MAP']['DATE_CREATE']['title'],
        "sort" => "DATE_CREATE",
        "default" => false,
        "editable" => false,
        'align' => 'center'
    ),
    array(
        "id" => "TIMESTAMP",
        'type' => 'date',
        "name" => $arParams['TICKET_MAP']['TIMESTAMP']['title'],
        "sort" => "TIMESTAMP",
        "default" => false,
        "editable" => false,
        'align' => 'center'
    ),
    array(
        "id" => "IS_CLOSE",
        'type' => 'checkbox',
        "name" => $arParams['TICKET_MAP']['IS_CLOSE']['title'],
        "sort" => "IS_CLOSE",
        "default" => false,
        "editable" => false,
        'align' => 'center'
    ),
    array(
        "id" => "DATE_CLOSE",
        'type' => 'date',
        "name" => $arParams['TICKET_MAP']['DATE_CLOSE']['title'],
        "sort" => "DATE_CLOSE",
        "default" => false,
        "editable" => false,
        'align' => 'center'
    ),
    array(
        "id" => "PRIORITY",
        "name" => $arParams['TICKET_MAP']['PRIORITY_ID']['title'],
        "sort" => "PRIORITY_ID",
        "default" => false,
        "editable" => false
    ),
    array(
        "id" => "STATUS",
        "name" => $arParams['TICKET_MAP']['STATUS_ID']['title'],
        "sort" => "STATUS_ID",
        "default" => true,
        "editable" => false
    ),
    array(
        "id" => "TITLE",
        "name" => $arParams['TICKET_MAP']['TITLE']['title'],
        "sort" => "TITLE",
        "default" => true,
        "editable" => false
    ),
    array(
        "id" => "CATEGORY",
        "name" => $arParams['TICKET_MAP']['CATEGORY_ID']['title'],
        "sort" => "CATEGORY_ID",
        "default" => true,
        "editable" => false
    ),
    array(
        "id" => "STATUS",
        "name" => $arParams['TICKET_MAP']['STATUS_ID']['title'],
        "sort" => "STATUS_ID",
        "default" => true,
        "editable" => false
    ),
    array(
        "id" => "LAST_MESSAGE_DATE",
        'type' => 'date',
        "name" => $arParams['TICKET_MAP']['LAST_MESSAGE_DATE']['title'],
        "sort" => "LAST_MESSAGE_DATE",
        "default" => true,
        "editable" => false,
        'align' => 'center'
    ),
    array(
        "id" => "MESSAGE_CNT",
        "name" => $arParams['TICKET_MAP']['MESSAGE_CNT']['title'],
        "sort" => "MESSAGE_CNT",
        "default" => true,
        "editable" => false,
        'align' => 'center'
    ),
);
if (IsModuleInstalled('socialnetwork')) {
    if ($arParams['GROUP_ID'] == 0) {
        $arParams['GRID']['HEADER'][] = array(
            "id" => "GROUP_ID",
            "name" => GetMessage('ALTASIB_STL_T_LIST_GROUP_ID'),
            "sort" => "GROUP_ID",
            "default" => true,
            "editable" => false
        );
    }
}
$arParams['GRID']['HEADER'][] = array(
    "id" => "CREATED_USER",
    "name" => $arParams['TICKET_MAP']['CREATED_USER_ID']['title'],
    "sort" => "CREATED_USER_ID",
    "default" => false,
    "editable" => false
);
$arParams['GRID']['HEADER'][] = array(
    "id" => "LAST_MESSAGE_USER",
    "name" => $arParams['TICKET_MAP']['LAST_MESSAGE_USER_ID']['title'],
    "sort" => "LAST_MESSAGE_USER_ID",
    "default" => false,
    "editable" => false
);
if ($arParams['SUPPORT_TEAM']) {
    $arParams['GRID']['HEADER'][] = array(
        "id" => "MODIFIED_USER",
        "name" => $arParams['TICKET_MAP']['MODIFIED_USER_ID']['title'],
        "sort" => "MODIFIED_USER_ID",
        "default" => false,
        "editable" => false
    );
    $arParams['GRID']['HEADER'][] = array(
        "id" => "OWNER",
        "name" => $arParams['TICKET_MAP']['OWNER_USER_ID']['title'],
        "sort" => "OWNER_USER_ID",
        "default" => true,
        "editable" => false
    );
    $arParams['GRID']['HEADER'][] = array(
        "id" => "RESPONSIBLE",
        "name" => $arParams['TICKET_MAP']['RESPONSIBLE_USER_ID']['title'],
        "sort" => "RESPONSIBLE_USER_ID",
        "default" => true,
        "editable" => false
    );
    $arParams['GRID']['HEADER'][] = array(
        "id" => "SLA",
        "name" => $arParams['TICKET_MAP']['SLA_ID']['title'],
        "sort" => "SLA_ID",
        "default" => false,
        "editable" => false
    );
    $arParams['GRID']['HEADER'][] = array(
        "id" => "IP",
        "name" => $arParams['TICKET_MAP']['IP']['title'],
        "default" => false
    );
}
$setLogic = false;
if ($arParams["SHOW_FILTER"]) {
    $arResult['CATEGORY'] = array();
    $filterCategory = array('0' => '-');
    $dataCategory = Support\CategoryTable::getList();
    while ($category = $dataCategory->fetch()) {
        $arResult['CATEGORY'][$category["ID"]] = $category;
        $filterCategory[$category['ID']] = $category['NAME'];
    }

    $arResult['STATUS'] = array();
    $filterStatus = array('0' => '-');
    $dataStatus = Support\StatusTable::getList();
    while ($status = $dataStatus->fetch()) {
        $arResult['STATUS'][$status["ID"]] = $status;
        $filterStatus[$status['ID']] = $status['NAME'];
    }

    $isOpenFilter = array(
        'ALL' => GetMessage("MAIN_ALL"),
        'N' => GetMessage("MAIN_NO"),
        'Y' => GetMessage("MAIN_YES"),
    );

    $arResult['GRID_FILTER'] = array(
        array('id' => 'ID', 'name' => 'ID', 'default' => true),
        array(
            'id' => 'IS_CLOSE',
            'name' => $arParams['TICKET_MAP']['IS_CLOSE']['title'],
            'type' => 'list',
            'items' => $isOpenFilter,
            'filtered' => (!isset($_REQUEST['IS_CLOSE'])),
            'filter_value' => 'N'
        ),
        array('id' => 'TITLE', 'name' => $arParams['TICKET_MAP']['TITLE']['title'], 'default' => true),
        array('id' => 'MESSAGE_EX', 'name' => GetMessage('ALTASIB_SUPPORT_FILTER_MESSAGE')),
        array(
            'id' => 'CATEGORY_ID',
            'name' => $arParams['TICKET_MAP']['CATEGORY_ID']['title'],
            'type' => 'list',
            'items' => $filterCategory
        ),
        array(
            'id' => 'STATUS_ID',
            'name' => $arParams['TICKET_MAP']['STATUS_ID']['title'],
            'type' => 'list',
            'items' => $filterStatus
        ),
        array(
            'id' => 'NO_REPLY',
            'name' => GetMessage('ALTASIB_SUPPORT_FILTER_NO_REPLY'),
            'type' => 'checkbox',
            'value' => 'Y'
        ),
        array(
            'id' => 'TIMESTAMP',
            'type' => 'date',
            "name" => $arParams['TICKET_MAP']['TIMESTAMP']['title'],
            'default' => false
        ),
    );
    if (\Bitrix\Main\Loader::includeModule('socialnetwork')) {
        if ($arParams['GROUP_ID'] == 0) {
            $groupList = array('' => '-');
            $groupListfilter = array(
                "USER_ID" => $USER->GetID(),
                "GROUP_ACTIVE" => "Y"
            );
            if ($arParams['SUPPORT_ROLE'] == 'C') {
                $groupListfilter["<=ROLE"] = SONET_ROLES_MODERATOR;
            }

            if ($arParams['SUPPORT_ROLE'] == 'E') {
                $groupListfilter["<=ROLE"] = SONET_ROLES_USER;
            }

            $arResult["MY_GROUPS"] = array();
            $rsGroups = CSocNetUserToGroup::GetList(
                array("GROUP_NAME" => "ASC"),
                $groupListfilter,
                false,
                false,
                array("ID", "GROUP_ID", "GROUP_NAME")
            );
            while ($arGroup = $rsGroups->Fetch()) {
                $groupList[$arGroup['GROUP_ID']] = $arGroup['GROUP_NAME'];
            }
            $arResult['GRID_FILTER'][] = array(
                'id' => 'GROUP_ID',
                'name' => GetMessage('ALTASIB_STL_T_LIST_GROUP_ID'),
                'type' => 'list',
                'items' => $groupList
            );
        }
    }

    if ($arParams['SUPPORT_TEAM']) {
        $arResult['GRID_FILTER'][] = array(
            'id' => 'OWNER_USER_ID',
            'name' => $arParams['TICKET_MAP']['OWNER_USER_ID']['title']
        );
    }

    if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::ADMIN) {
        $supportTeam = Support\Tools::getSupportTeam();
        $supportTeam[0] = '-';
        array_reverse($supportTeam, true);
        $arResult['GRID_FILTER'][] = array(
            'id' => 'RESPONSIBLE_USER_ID',
            'name' => $arParams['TICKET_MAP']['RESPONSIBLE_USER_ID']['title'],
            'type' => 'list',
            'items' => $supportTeam
        );
    }

    $gridFilter = $grid_options->GetFilter($arResult["GRID_FILTER"]);
    foreach ($gridFilter as $key => $value) {
        if (substr($key, -8) == "_datesel") {
            unset($gridFilter[$key]);
            continue;
        }

        if (substr($key, -5) == "_from") {
            $new_key = '>=' . substr($key, 0, -5);
            unset($gridFilter[$key]);
            $value = $value . ' 00:00:00';
        } elseif (substr($key, -3) == "_to") {
            $new_key = '<=' . substr($key, 0, -3);
            $value = $value . ' 23:59:59';
            unset($gridFilter[$key]);
        } else {
            $new_key = $key;
        }

        $gridFilter[$new_key] = $value;
    }

    if (isset($gridFilter['NO_REPLY'])) {
        if ($gridFilter['NO_REPLY'] == 'Y') {
            if ($arParams['IS_SUPPORT_TEAM']) {
                $gridFilter['LAST_MESSAGE_BY_SUPPORT'] = 'N';
            } else {
                $gridFilter['LAST_MESSAGE_BY_SUPPORT'] = 'Y';
            }
        }
        unset($gridFilter['NO_REPLY']);
    }

    if ($gridFilter['IS_CLOSE'] == 'ALL') {
        unset($gridFilter['IS_CLOSE']);
    }

    if ($gridFilter['STATUS_ID'] == 0) {
        unset($gridFilter['STATUS_ID']);
    }

    if ($gridFilter['CATEGORY_ID'] == 0) {
        unset($gridFilter['CATEGORY_ID']);
    }

    if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::ADMIN && $gridFilter['RESPONSIBLE_USER_ID'] == 0) {
        unset($gridFilter['RESPONSIBLE_USER_ID']);
    }

    if (!empty($gridFilter['TITLE'])) {
        $gridFilter['TITLE'] = '%' . $gridFilter['TITLE'] . '%';
    }

    if (!empty($gridFilter['MESSAGE_EX'])) {
        $messFilter = array(
            'MESSAGE' => '%' . $gridFilter['MESSAGE_EX'] . '%'
        );
        if (isset($gridFilter['IS_CLOSE'])) {
            $messFilter['TICKET.IS_CLOSE'] = $gridFilter['IS_CLOSE'];
        }

        $obTidMessage = Support\TicketMessageTable::getList(array(
            'filter' => $messFilter,
            'select' => array('TICKET_ID'),
            'group' => array('TICKET_ID'),
        ));
        $dataTidMessage = array();
        while ($TidMessage = $obTidMessage->fetch()) {
            $dataTidMessage[] = $TidMessage['TICKET_ID'];
        }

        $gridFilter[] = array(
            'LOGIC' => 'OR',
            array('=ID' => $dataTidMessage),
            array('MESSAGE' => '%' . $gridFilter['MESSAGE_EX'] . '%')
        );
        unset($gridFilter['MESSAGE_EX']);
    }

    if (isset($gridFilter['OWNER_USER_ID']) && $arParams['SUPPORT_TEAM']) {
        if (strlen($gridFilter['OWNER_USER_ID']) > 0) {
            $gridFilter[] = array(
                'LOGIC' => 'OR',
                array('=OWNER_USER_ID' => $gridFilter['OWNER_USER_ID']),
                array('%OWNER_USER.LOGIN' => $gridFilter['OWNER_USER_ID']),
                array('%OWNER_USER.EMAIL' => $gridFilter['OWNER_USER_ID']),
                array('%OWNER_USER.NAME' => $gridFilter['OWNER_USER_ID']),
                array('%OWNER_USER.LAST_NAME' => $gridFilter['OWNER_USER_ID']),
            );
        }
        unset($gridFilter['OWNER_USER_ID']);
    } else {
        unset($gridFilter['OWNER_USER_ID']);
    }
}

$request = Main\Context::getCurrent()->getRequest();

if (!empty($request["clear_filter"]) && $arParams['SHOW_FILTER']) {
    LocalRedirect($APPLICATION->GetCurPageParam('',
        array('setFilter', 'STATUS_ID', 'CLOSE', 'TITLE', 'TICKET_MESSAGE', 'MESSAGE', 'CATEGORY_ID', 'clear_filter')));
}

if (\Bitrix\Main\ModuleManager::isModuleInstalled('intranet') && $arParams['SHOW_MENU_COUNTER'] == 'Y') {
    $cntFilter = array_merge($filter,
        array('IS_CLOSE' => 'N', 'LAST_MESSAGE_BY_SUPPORT' => ($arParams['IS_SUPPORT_TEAM'] ? 'N' : 'Y')));
    $countQuery = new Bitrix\Main\Entity\Query(Support\TicketTable::getEntity());
    $countQuery
        ->registerRuntimeField("CNT", array(
                "data_type" => "integer",
                "expression" => array("COUNT(1)")
            )
        )
        ->setSelect(array("CNT"))
        ->setFilter($cntFilter);
    $totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
    $_SESSION["cnt_altasib_support"] = intval($totalCount['CNT']);
    unset($countQuery);
    unset($totalCount);
}
$filter = array_merge($filter, $gridFilter);
if ($arParams["GROUP_ID"] > 0) {
    $filter['GROUP_ID'] = $arParams["GROUP_ID"];
}

$limit = (is_set($_GET['SIZEN_1']) && $_GET['SIZEN_1'] > 0) ? $_GET['SIZEN_1'] : $arParams["NUM_TICKETS"];//$aNav['nPageSize'];
$page = is_set($_GET['PAGEN_1']) && $_GET['PAGEN_1'] > 0 ? $_GET['PAGEN_1'] : 1;

if ($arParams['SUPPORT_TEAM']) {
    $order = array('IS_OVERDUE' => 'DESC', 'LAST_MESSAGE_BY_SUPPORT' => 'ASC', 'LAST_MESSAGE_DATE');
} else {
    $order = array('IS_CLOSE' => 'ASC', 'DATE_CREATE' => 'DESC', 'LAST_MESSAGE_DATE' => 'DESC');
}

$aSort = $grid_options->GetSorting(array("sort" => $order, "vars" => array("by" => "by", "order" => "order")));
$nav = $grid_options->GetNavParams(array('nPageSize' => $limit));
$limit = $nav['nPageSize'];

if (count($aSort['sort']) == 1) {
    $aSortArg = each($aSort["sort"]);
    $order = array($aSortArg['key'] => $aSortArg['value']);
}
$arResult["SORT"] = $aSort["sort"];
$arResult["SORT_VARS"] = $aSort["vars"];

if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::SUPPORT) {
    $memberFilter = array('USER_ID' => $USER->GetID());

    foreach ($gridFilter as $k => $v) {
        if (!is_numeric($k)) {
            $prefix = substr($k, 0, 2);
            $prefix2 = substr($k, 0, 1);
            if ($prefix == '>=' || $prefix == '<=') {
                $memberFilter[$prefix . 'TICKET.' . substr($k, 2)] = $v;
            } elseif ($prefix2 == '%' || $prefix2 == '=') {
                $memberFilter[$prefix2 . 'TICKET.' . substr($k, 1)] = $v;
            } else {
                $memberFilter['TICKET.' . $k] = $v;
            }
        } else {
            $mV = $v;
            foreach ($mV as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $fk => $fv) {
                        unset($mV[$k][$fk]);
                        $mPrefix = substr($fk, 0, 2);
                        $mPrefix2 = substr($fk, 0, 1);
                        if ($mPrefix == '>=' || $mPrefix == '<=' || $mPrefix == '=') {
                            $mV[$k][$mPrefix . 'TICKET.' . substr($fk, 2)] = $fv;
                        } elseif ($mPrefix2 == '%' || $mPrefix2 == '=') {
                            $mV[$k][$mPrefix2 . 'TICKET.' . substr($fk, 1)] = $fv;
                        } else {
                            $mV[$k]['TICKET.' . $fk] = $fv;
                        }
                    }
                }
            }
            $memberFilter[] = $mV;
        }
    }

    $memberFilterTicketId = array();
    $dataMember = Support\TicketMemberTable::getList(array(
        'select' => array('TICKET_ID'),
        'filter' => $memberFilter
    ))->fetchAll();
    foreach ($dataMember as $member) {
        $memberFilterTicketId['ID'][] = $member['TICKET_ID'];
    }
}
if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::SUPPORT && count($roleFilter) > 0) {
    $sFilter = $filter;
    unset($sFilter['RESPONSIBLE_USER_ID']);
    $s2F = array_merge($roleFilter, $sFilter);
    $filter = array('LOGIC' => 'OR', $filter, $s2F);
}
if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::SUPPORT && count($memberFilterTicketId) > 0) {
    if (count($roleFilter) > 0) {
        $filter[] = $memberFilterTicketId;
    } else {
        $filter = array('LOGIC' => 'OR', $filter, $memberFilterTicketId);
    }
}

if ($arParams['Right']->getRole() == ALTASIB\Support\Rights::CUSTOMER && count($roleFilter) > 0) {
    $sFilter = $filter;
    unset($sFilter['OWNER_USER_ID']);
    $s2F = array_merge($roleFilter, $sFilter);
    $filter = array('LOGIC' => 'OR', $filter, $s2F);
}

$parametrs = array(
    'order' => $order,
    'filter' => $filter,
    'select' => array(
        '*',
        'CATEGORY_NAME' => 'CATEGORY.NAME',
        'STATUS_NAME' => 'STATUS.NAME',
        'OWNER_USER',
        'CREATED_USER',
        'MODIFIED_USER',
        'MESSAGE_CNT',
        'CREATED_USER_NAME' => 'CREATED_USER.NAME',
        'CREATED_USER_LOGIN' => 'CREATED_USER.LOGIN',
        'CREATED_USER_EMAIL' => 'CREATED_USER.EMAIL',
        'CREATED_USER_SHORT_NAME' => 'CREATED_USER.SHORT_NAME',

        'MODIFIED_USER_NAME' => 'MODIFIED_USER.NAME',
        'MODIFIED_USER_LOGIN' => 'MODIFIED_USER.LOGIN',
        'MODIFIED_USER_EMAIL' => 'MODIFIED_USER.EMAIL',
        'MODIFIED_USER_SHORT_NAME' => 'MODIFIED_USER.SHORT_NAME',

        'LAST_MESSAGE_USER_NAME' => 'LAST_MESSAGE_USER.NAME',
        'LAST_MESSAGE_USER_LOGIN' => 'LAST_MESSAGE_USER.LOGIN',
        'LAST_MESSAGE_USER_EMAIL' => 'LAST_MESSAGE_USER.EMAIL',
        'LAST_MESSAGE_USER_SHORT_NAME' => 'LAST_MESSAGE_USER.SHORT_NAME',

        'OWNER_USER_NAME' => 'OWNER_USER.NAME',
        'OWNER_USER_LOGIN' => 'OWNER_USER.LOGIN',
        'OWNER_USER_EMAIL' => 'OWNER_USER.EMAIL',
        'OWNER_USER_SHORT_NAME' => 'OWNER_USER.SHORT_NAME',

        'RESPONSIBLE_USER_NAME' => 'RESPONSIBLE_USER.NAME',
        'RESPONSIBLE_USER_LOGIN' => 'RESPONSIBLE_USER.LOGIN',
        'RESPONSIBLE_USER_EMAIL' => 'RESPONSIBLE_USER.EMAIL',
        'RESPONSIBLE_USER_SHORT_NAME' => 'RESPONSIBLE_USER.SHORT_NAME',

        'SLA_NAME' => 'SLA.NAME',
    ),
    'limit' => $limit,
    'offset' => ($page - 1) * $limit
);

if (IsModuleInstalled('socialnetwork')) {
    $parametrs['select']['GROUP_NAME'] = 'GROUP.NAME';
    $parametrs['select']['GROUP_OWNER_ID'] = 'GROUP.OWNER_ID';
}

$data = Support\TicketTable::getList($parametrs);
$result = new CDBResult($data);
$result->NavStart($limit);
while ($arTicket = $result->fetch()) {
    $arTicket['COLOR'] = Support\TicketTable::LAMP_GRAY;
    if ($arTicket['LAST_MESSAGE_BY_SUPPORT'] == 'Y') {
        if ($arParams['IS_SUPPORT_TEAM']) {
            $arTicket['COLOR'] = Support\TicketTable::LAMP_GREEN;
        } else {
            $arTicket['COLOR'] = Support\TicketTable::LAMP_RED;
        }
    } else {
        if ($arParams['IS_SUPPORT_TEAM']) {
            $arTicket['COLOR'] = Support\TicketTable::LAMP_RED;
        } else {
            $arTicket['COLOR'] = Support\TicketTable::LAMP_GREEN;
        }
    }

    if ($arTicket['IS_DEFERRED'] == 'Y') {
        $arTicket['COLOR'] = Support\TicketTable::LAMP_BROWN;
    }

    if ($arTicket['IS_CLOSE'] == 'Y') {
        $arTicket['COLOR'] = Support\TicketTable::LAMP_GRAY;
    }

    $arTicket['GROUP_ID'] = $arTicket['GROUP_NAME'];

    $arTicket["URL_DETAIL"] = str_replace(Array("#ID#", "#TICKET_ID#", "#CODE#", '#group_id#'),
        Array($arTicket["ID"], $arTicket["ID"], $arTicket["CODE"], $arParams['GROUP_ID']), $arParams["URL_DETAIL"]);
    $arResult["TICKET"][] = $arTicket;
}

$countQuery = new Bitrix\Main\Entity\Query(Support\TicketTable::getEntity());
$countQuery
    ->registerRuntimeField("CNT", array(
            "data_type" => "integer",
            "expression" => array("COUNT(1)")
        )
    )
    ->setSelect(array("CNT"))
    ->setFilter($filter);
$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
$totalCount = intval($totalCount['CNT']);
$totalPage = ceil($totalCount / $limit);
$result->NavRecordCount = $totalCount;
$result->NavPageCount = $totalPage;
$result->NavPageNomer = $page;
$arResult["NAV_STRING"] = $result->GetPageNavString('', 'visual');
$arResult["NAV_PARAMS"] = $result->GetNavParams();
$arResult["NAV_NUM"] = $result->NavNum;
$result->bShowAll = false;
$arResult["NAV_OBJECT"] = $result;

$this->IncludeComponentTemplate();
$APPLICATION->SetTitle(GetMessage('ALTASIB_SUPPORT_PAGE_NAME'));
?>