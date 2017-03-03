<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("DisableEventsCheck", true);
define('BX_SECURITY_SESSION_READONLY', true);

use ALTASIB\Support;
if(CModule::IncludeModule("altasib.support") && strlen($arParams['FILE_HASH']) > 0)
{
	$forb = false;
	$dataFile = Support\FileTable::getList(array('filter'=>array('TICKET_ID'=>$arParams['ID'],'HASH'=>$arParams['FILE_HASH'])));
	if ($arResult = $dataFile->Fetch())
	{
			set_time_limit(0);
            $arFile = CFile::GetFileArray($arResult['FILE_ID']);
			$pathFile = $_SERVER["DOCUMENT_ROOT"]."/upload/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
			//$arFile["ORIGINAL_NAME"] = substr($arFile["ORIGINAL_NAME"],0,-1);
			$ar = pathinfo($pathFile);
			$ext = $ar["extension"];

            if($_REQUEST['webdavView']=='Y' && $_POST['json']==1)
            {
                $APPLICATION->RestartBuffer();
                CModule::IncludeModule('webdav');
                $p = "/upload/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
            	$hash = CWebDavExtLinks::getHashLink(array(
            			), array(
            				'PASSWORD' => '',
            				'LIFETIME_NUMBER' => CWebDavExtLinks::LIFETIME_TYPE_AUTO,
            				'LIFETIME_TYPE' => 'minute',
            				'URL' =>$p,
            				//'BASE_URL' => $p,
            				'SINGLE_SESSION' => false,
            				//'LINK_TYPE' => CWebDavExtLinks::LINK_TYPE_AUTO,
            				'VERSION_ID' => '1',
            				'FILE_ID' => $arResult['FILE_ID'],
            				'ELEMENT_ID' => '',
                            
            			), null);
            
            //var_dump(Bitrix\Disk\UrlManager::getUrlToShowAttachedFileByService($arResult['FILE_ID'],'gvdrive'));
            //die();
            		CWebDavTools::sendJsonResponse(array(
            			'file' => $hash,
            			'viewerUrl' => CWebDavExtLinks::$urlGoogleViewer . urlencode($hash) . '?LoadFile=1&embedded=true',
                        //'neededDelete'=>'false',
                        //'neededCheckView'=>'true',
                        //'status'=>'success'
            		));
                    
                    //echo '{"id":"3dc5d329e4b654a07c8417fad20419d4","viewUrl":"https:\/\/drive.google.com\/viewerng\/viewer?embedded=true\u0026url=https%3A%2F%2Fcp.altasib.ru%2Fdocs%2Fpub%2F3dc5d329e4b654a07c8417fad20419d4%2Fdownload%2F%3F%26","neededDelete":false,"neededCheckView":true,"status":"success"}';
                
                
                //getDataForViewFile
                //$dc = new Bitrix\Disk\Document\DocumentController;
                //$dc->processActionShow();
                die(); 
            }            
			switch(strtolower($ext))
			{
    			case "xla":
    			case "xlb":
    			case "xlc":
    			case "xll":
    			case "xlm":
    			case "xls":
    			case "xlsx":
    			case "xlt":
    			case "xlw":
    			case "dbf":
    			case "csv":
    				CFile::ViewByUser($arFile, array("content_type" => "application/vnd.ms-excel"));
    				break;
    			case "doc":
    			case "docx":
    			case "dot":
    			case "rtf":
    				CFile::ViewByUser($arFile, array("content_type" => "application/msword"));
    				break;
    			case 'rar':
    				CFile::ViewByUser($arFile, array("content_type" => "application/x-rar-compressed"));
    				break;
    			case 'zip':
    				CFile::ViewByUser($arFile, array("content_type" => "application/zip"));
    				break;
    			default:
    				CFile::ViewByUser($arFile, array("force_download" => true));
    				break;
		  }
	}
}

if($forb)
	$APPLICATION->AuthForm();
else
	echo ShowError(GetMessage("SUP_ERROR_ATTACH_NOT_FOUND"));   
?>