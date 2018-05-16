<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

global $USER,$APPLICATION;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/'.basename(__FILE__));

$Role = $APPLICATION->GetUserRight("altasib.support");
if($Role!='W')
    die();

if (!CModule::IncludeModule('altasib.support') || !CModule::IncludeModule('altasib.supportreport'))
{
	ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
	die();
}
CUtil::JSPostUnescape();

$TICKET_ID = intval($_REQUEST["TICKET_ID"]);
$obTicket = \ALTASIB\Support\TicketTable::getList(array('filter'=>array('ID'=>$TICKET_ID),'select' => array('ID','OWNER_USER_ID')));
if(!$arTicket = $obTicket->Fetch())
    die();
    
if ($_REQUEST['MODE'] == 'section')
{
	$arResult = array();
    $crmInfo = \ALTASIB\SupportReport\Crm::getInfoByContact($arTicket['OWNER_USER_ID']);
    foreach($crmInfo['DEAL_LIST'] as $deal)
    {
    		$arResult[] = array(
    			"ID" => $deal["ID"],
    			"NAME" => $deal["TITLE"],
    			"SECTION_ID" => $deal["COMPANY_ID"],
                "CONTENT" => '<span class="mts-name">'.$deal["TITLE"].'</span>',
    		);    
    }

	$APPLICATION->RestartBuffer();
	Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
	echo CUtil::PhpToJsObject(array("SECTION_ID" => 0, "arElements" => $arResult));
	die();
}
elseif ($_REQUEST['MODE'] == 'search')
{
	$arResult = array();
    $crmInfo = \ALTASIB\SupportReport\Crm::getInfoByContact($arTicket['OWNER_USER_ID']);
    foreach($crmInfo['DEAL_LIST'] as $deal)
    {
        if(stristr($deal["TITLE"],$_REQUEST['search']))
    		$arResult[] = array(
    			"ID" => $deal["ID"],
    			"NAME" => $deal["TITLE"],
    			"SECTION_ID" => $deal["COMPANY_ID"],
    		);    
    }
	$APPLICATION->RestartBuffer();
	Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
	echo CUtil::PhpToJsObject($arResult);
	die();
}
else
{
$win_id = preg_replace("/[^a-z0-9_\\[\\]:.,_-]/i", "", $_REQUEST["win_id"]);
$arValues = array();

if(isset($_GET['value']))
{
	$arValues = array();
	foreach(explode(',', $_GET['value']) as $value)
	{
		$value = intval($value);
		if($value > 0)
			$arValues[$value] = $value;
	}
}
?>
<?/*
<div class="title">
<table cellspacing="0" width="100%">
	<tr>
		<td width="100%" class="title-text" onmousedown="jsFloatDiv.StartDrag(arguments[0], document.getElementById('<?echo $win_id?>'));">&nbsp;</td>
		<td width="0%"><a class="close" href="javascript:document.getElementById('<?echo $win_id?>').__object.CloseDialog();" title="<?=GetMessage("CT_BMTS_WINDOW_CLOSE")?>"></a></td>
	</tr>
</table>
</div>
*/?>
<script>
var current_selected = <?echo CUtil::PhpToJsObject(array_values($arValues))?>;
</script>
<div class="content" id="_f_popup_content" style="padding: 0px;">
<input id="bx_emp_search_control" type="text" style="width: 99%" value="" autocomplete="off" />

<script>
document.getElementById('<?echo $win_id?>').__object.InitControl('bx_emp_search_control');
</script>

<div id="mts_search_layout">
<?
	echo '<div style="display:none" id="mts_section_0">';
	echo '<div class="mts-section-name mts-closed"></div>';
	echo '</div>';

	echo '<div style="display: none" id="bx_children_0">';
	echo '<div class="mts-list" id="mts_elements_0"><i>'.GetMessage('ALTASIB_DEAL_WAIT').'</i></div>';
	echo '</div>';
?>
<script>
	document.getElementById('<?echo $win_id?>').__object.LoadSection('0', true);
</script>
</div>
</div>
<div class="buttons">
	<input type="button" id="submitbtn" value="<?echo GetMessage('ALTASIB_DEAL_CHANGE')?>" onclick="document.getElementById('<?echo $win_id?>').__object.ElementSet();" />
	<input type="button" value="<?echo GetMessage('MAIN_CLOSE')?>" onclick="document.getElementById('<?echo $win_id?>').__object.CloseDialog();" />
</div>
<?
}
?>