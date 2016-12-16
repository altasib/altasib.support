<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Техподдержка");
?>
<?$APPLICATION->IncludeComponent(
	"altasib:support", 
	".default", 
	array(
		"SHOW_FILTER" => "Y",
		"PROFILE_PATH" => "",
		"NUM_TICKETS" => "10",
		"TICKET_LIST_URL" => "/support/",
		"ID" => $_REQUEST["ID"],
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/support/",
		"SEF_URL_TEMPLATES" => array(
			"ticket_list" => "",
			"ticket_detail" => "ticket/#ID#/",
			"get_file" => "ticket/#ID#/file/#FILE_HASH#/",
			"desktop" => "desktop/",
		)
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>