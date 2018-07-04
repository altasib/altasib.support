<?
use ALTASIB\Support;
use ALTASIB\SupportReport\Report;
use ALTASIB\SupportReport\Cost;
use ALTASIB\Support\TextParser;
use Bitrix\Main;

__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/template.php');
function getMessageSupport($arMessage,$arParams,$Ticket, $lastDate = '')
{
    global $APPLICATION;
    global $USER;
    ob_start();

    $CCTP = new TextParser();
    $CCTP->maxStringLen = 50;
    $CCTP->allow = Support\Tools::getAllowTags();
    $arMessage['MESSAGE'] = $CCTP->convertText($arMessage["MESSAGE"]);
    $arMessage['AUTHOR'] = array(
        "ID" => $arMessage['CREATED_USER_ID'],
        "NAME" => $arMessage['CREATED_USER_SHORT_NAME'],
        "URL" => str_replace(array('#USER_ID#','#SITE_DIR#'),array($arMessage['CREATED_USER_ID'],SITE_DIR),$arParams['PROFILE_PATH']),
        "AVATAR" => Support\User::getAvatar($arMessage['CREATED_USER_ID']),
    );
    $arMessage['POST_DATE'] = $arMessage["DATE_CREATE"]->toString();
	$arrPOST_DATE = explode(" ", $arMessage['POST_DATE']);
	$lastDate = explode(" ", $lastDate);
    $dataFile = Support\FileTable::getList(array('filter'=>array('TICKET_ID'=>$arParams['ID'],'MESSAGE_ID'=>$arMessage["ID"])));
    while($arFile = $dataFile->fetch())
    {
        if($arrFile = CFile::GetFileArray($arFile['FILE_ID']))
        {
            $arrFile['HASH'] = $arFile['HASH'];
            if($arrFile['HEIGHT']>0 && CFile::IsImage($arrFile['SRC']))
            {
                $arMessage["FILES_IMAGE"][] = $arrFile;
            }
            else
                $arMessage["FILES"][] = $arrFile;
        }        
    }    

    $hl = ($arParams['HIGHLIGHT_MESSAGE_ID']==$arMessage['ID']) ? ' style="background-color:cornsilk"' : '';
    $isFavorite = (isset($arMessage['FAVORITE']) && $arMessage['FAVORITE']=='Y');
	
?>

                            <div id="message<?=$arMessage['ID']?><?if($isFavorite):?>-fav<?endif;?>" class="altasib-support-message <?if($arMessage['IS_LOG']=='Y'):?>log-mess<?else:?> <?if($arMessage['CREATED_USER_ID'] == $Ticket['OWNER_USER_ID']):?>author-mess<?endif?><?endif?> <?if($arrPOST_DATE[0]!=$lastDate[0]):?>date-separate<?endif?><?if($isFavorite):?> post-fav<?endif;?>"<?=$hl?>>
                            	
								<a name="message<?=$arMessage['ID']?>"></a>
                            	<div class="feed-com-block-outer">
                            		<div class="feed-com-block<?if($arMessage['IS_LOG']=='Y'):?> log-message<?endif;?><?if($arMessage['IS_HIDDEN']=='Y'):?> hidden-message<?endif;?>">
											<?if(!$isFavorite && $arMessage['IS_LOG']!='Y'):?>
											<div class="ticket-fav fav-on" onclick="alSupDetail.messageToFav(<?=$arMessage['ID']?>)" title="<?=GetMessage('ALTASIB_SUPPORT_ADD_FAVORITE')?>"><i class="icon-star-empty"> </i><i class="icon-star"> </i></div>
											<?else:?>
											<div class="ticket-fav" onclick="alSupDetail.messageDelFav(<?=$arMessage['ID']?>)" title="<?=GetMessage('ALTASIB_SUPPORT_DEL_FAVORITE')?>"><i class="icon-star"> </i></div>
										<?endif;?>									
									
										<div class="feed-com-info">
											<?if($arMessage['IS_LOG']=='N'):?>
												<?if(strlen($arMessage['AUTHOR']['AVATAR'])==0):?>
												<div class="feed-com-avatar feed-com-avatar-N"><img src="/bitrix/images/1.gif" width="30" height="30"></div>
												<?else:?>
												<div class="feed-com-avatar feed-com-avatar-Y"><img src="<?=$arMessage['AUTHOR']['AVATAR'];?>" width="30" height="30"></div>
												<?endif;?>

												<?if(strlen($arMessage['AUTHOR']['URL'])==0):?>
													<span class="feed-com-name feed-author-name feed-author-name-1"><?=$arMessage['AUTHOR']['NAME']?></span>
												<?else:?>
													<a class="feed-com-name feed-author-name feed-author-name-1" href="<?=$arMessage['AUTHOR']['URL']?>"><?=$arMessage['AUTHOR']['NAME']?></a>
												<?endif;?>
											<?endif;?>
								
											<div class="feed-com-informers">
												<span class="feed-time"><?=$arMessage['POST_DATE']?></span>
												<?if($arMessage['IS_HIDDEN']=='Y'):?>
												<span class="is-hidden-text"><?=GetMessage('ALTASIB_SUPPORT_IS_HIDDEN');?></span>
												<?endif;?>
												<?if($arMessage['IS_LOG']=='Y'):?>
												<span class="is-log-text"><?=GetMessage('ALTASIB_SUPPORT_IS_LOG').' '.$arMessage['CREATED_USER_SHORT_NAME'];?></span>
												<?endif;?>
												
												<a name="mess<?=$arMessage['ID']?>"></a>                                            
											</div>
										</div>
                                        
                                       <a onclick="prompt('Link Message','http://<?=$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPageParam('message='.$arMessage['ID'],array('message'));?>#mess<?=$arMessage['ID']?>')" href="javascript:void(0)" class="ticketID_promt">#<?=$arMessage['ID']?></a>

                            			<div class="feed-com-text">
                            				<div class="feed-com-text-inner">
                            					<div class="feed-com-text-inner-inner">
													<div><?=$arMessage['MESSAGE']?></div>
                            					</div>
                            				</div>
                            			</div>
                                        <?if($arMessage['IS_LOG']=='N'):?>
                                        <div class="altasib-support-adm-act">
                                            <?if(($arMessage['pull_type']=='W' || (!isset($arMessage['pull_type']) && $arParams['ROLE']=='W'))):?>
											<div onclick="newTicketMessage(<?=$arMessage['ID']?>)" title="<?=GetMessage('ALTASIB_SUPPORT_EDIT_MOVE')?>"><i class="icon-signout"> </i></div>
                                            <?endif;?>
                                            <?if(
                                                ($arMessage['pull_type']=='W' || (!isset($arMessage['pull_type']) && $arParams['ROLE']=='W')) 
                                                || 
                                                (
                                                    ($arMessage['pull_type']=='E' || (!isset($arMessage['pull_type']) && $arParams['IS_SUPPORT_TEAM'])) 
                                                    && $arMessage['CREATED_USER_ID']==$USER->GetID()
                                                )
                                                ):?>
											<div onclick="editMessage(<?=$arMessage['ID']?>)" title="<?=GetMessage('ALTASIB_SUPPORT_EDIT_EDIT')?>"><i class="icon-edit"> </i></div>
                                            <?endif;?>
                                            <?if(($arMessage['pull_type']=='W' || (!isset($arMessage['pull_type']) && $arParams['ROLE']=='W'))):?>
											<div class="ticket-del" onclick="deleteMessage(<?=$arMessage['ID']?>)" title="<?=GetMessage('ALTASIB_SUPPORT_EDIT_DELETE')?>"><i class="icon-remove"> </i></div>
                                            <?endif;?>
                                        </div>
                                        <?endif;?>
                                        <?if(count($arMessage["FILES_IMAGE"])>0):?>
                                        <div class="feed-com-files">
                                        	<div class="feed-com-files-title"><?=GetMessage('ALTASIB_SUPPORT_EDIT_PHOTO')?>:</div>
                                        	<div class="feed-com-files-cont">									
                                            <?foreach($arMessage["FILES_IMAGE"] as $arFile):?>
                                                <span class="feed-com-files-photo" style="width:69px;height:69px;">
													<?$minImage = CFile::ResizeImageGet($arFile, Array("width" => 75, "height" => 50),BX_RESIZE_IMAGE_EXACT);?>
                                                    <?=CFile::ShowImage($minImage['src'], 9999, 50, 
                                                        'border=0 
                                                        data-bx-viewer="image" 
                                                        data-bx-title="'.$arFile['ORIGINAL_NAME'].'" 
                                                        data-bx-src="'.$arFile['SRC'].'" 
                                                        data-bx-download="'.$arFile['SRC'].'" 
                                                        data-bx-width="'.$arFile['WIDTH'].'" 
                                                        data-bx-height="'.$arFile['HEIGHT'].'"', 
                                                    "", false);?>
                                                </span>
                                            <?endforeach;?>
                                            </div>
                                        </div>
                                        <?endif;?>
                                        <?if(count($arMessage["FILES"])>0):?>
                                        <div class="feed-com-files">
                                        	<div class="feed-com-files-title"><?=GetMessage('ALTASIB_SUPPORT_EDIT_FILES')?>:</div>
                                        	<div class="feed-com-files-cont">
                                            <?foreach($arMessage["FILES"] as $arFile):?>
                            				<div class="feed-com-file-wrap">
                            					<span class="feed-con-file-icon feed-file-icon-file"></span>
                            					<span class="feed-com-file-name">
                                                    <?if(IsModuleInstalled('webdav') && in_array(GetFileExtension($arFile['FILE_NAME']),array('doc','docx','xls'))):?>
                                                      <?$dl = str_replace(array('#ID#','#FILE_HASH#'),array($arParams['ID'],$arFile['HASH']),$arParams['URL_GET_FILE']);?>
                                                      <?/*$dl=Bitrix\Disk\UrlManager::getUrlToShowAttachedFileByService($arFile['ID'],'gvdrive');*/?>
                                                      <a target="_blank" href="<?=$dl?>" data-bx-viewer="iframe" data-bx-title="<?=$arFile['ORIGINAL_NAME']?>" data-bx-src="<?=$dl?>?webdavView=Y" data-bx-download="<?=$dl?>?webdavView=Y"><?=$arFile['ORIGINAL_NAME']?></a>
                                                    <?else:?>
                            						  <a target="_blank" href="<?=str_replace(array('#ID#','#FILE_HASH#'),array($arParams['ID'],$arFile['HASH']),$arParams['URL_GET_FILE'])?>"><?=$arFile['ORIGINAL_NAME']?></a>
                                                    <?endif;?>
                                                    <small><?=CFile::FormatSize(intval($arFile['FILE_SIZE']), 0);?></small>
                                                </span>
                                            </div>
                                            <?endforeach;?>
                                            </div>
                                        </div>
                                        <?endif;?>
                            		</div>
                            	</div>
                            </div>
<?
$message = ob_get_clean();
return $message;
}
?>                            