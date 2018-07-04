<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$name_x = CUtil::JSEscape($arParams['NAME']);
$arParams['INPUT_NAME'] = CUtil::JSEscape($arParams['INPUT_NAME']);
?>
<script type="text/javascript">
<?if($arParams['INPUT_NAME'] && !$arParams['ONSELECT']):?>
	function OnSelect_<?=$name_x?>(value){
		document.getElementById('<?=$arParams['INPUT_NAME']?>').value = value;
	}
	<?
	$arParams['ONSELECT'] = 'OnSelect_'.$name_x;
endif;?>

var <?=$name_x?> = new JCTreeSelectControl({
	'AJAX_PAGE' : '<?echo CUtil::JSEscape($this->GetFolder()."/ajax.php")?>',
	'AJAX_PARAMS' : <?echo CUtil::PhpToJsObject(array(
		"ALTASIB_AJAX_CALL" => 'Y',
		"TICKET_ID" => (int)$arParams["TICKET_ID"],
	))?>,
	'MULTIPLE' : <?echo $arParams['MULTIPLE'] == 'Y' ? 'true' : 'false'?>,
	'GET_FULL_INFO': <?echo $arParams['GET_FULL_INFO'] == 'Y' ? 'true' : 'false'?>,
	'ONSELECT' : function(v){<?echo $arParams['ONSELECT']?>(v)},
	'START_TEXT' : '<?echo CUtil::JSEscape($arParams["START_TEXT"])?>',
	'NO_SEARCH_RESULT_TEXT' : '<?echo CUtil::JSEscape($arParams["NO_SEARCH_RESULT_TEXT"])?>',
	'INPUT_NAME' : '<?echo CUtil::JSEscape($arParams["INPUT_NAME"])?>'
});
</script>