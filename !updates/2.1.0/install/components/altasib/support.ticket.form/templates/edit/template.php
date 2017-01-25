<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["HAVE_ANSWER"] || ($arParams['ID']==0 && $arParams["HAVE_CREATE"])):?>
<br style="clear:both;"/>
<?if (!empty($arResult["ERRORS"])):?>
        <?=ShowError(implode("<br />", $arResult["ERRORS"]));?>
<br style="clear:both;"/>
<?endif?>
<span id="errors" style="display: none; color: red;"></span>
<form name="ticket_edit" id="ticket_edit" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="TICKET_ID" id="TICKET_ID" value="<?=$arParams["ID"];?>" />
<input type="hidden" value="EDIT" name="ACTION">
<input type="hidden" name="SUPPORT_AJAX" value="Y" />
<input type="hidden" value="<?=$arParams['edit_support_message_id']?>" name="edit_support_message_id">
        <?=bitrix_sessid_post()?>
        <table class="ticketn" width="100%">
                <?if($arParams["ID"]>0 && $arParams["REPLY_ON"]=="Y" && !$arResult["IS_CLOSE"]):?>
                <tbody>
                                <tr>
                                        <td colspan="2">
                                                <?ALTASIB\Support\Tools::ShowLHE("MESSAGE_e",$arResult["MESSAGE"]["MESSAGE"],"MESSAGE_e");?>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan="2">
                                            <?
                                            $APPLICATION->IncludeComponent("bitrix:main.file.input","",Array(
                                                "ALLOW_UPLOAD"=>"F",
                                                "ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
                                                //"MAX_FILE_SIZE" => $arParams["UPLOAD_FILE_SIZE"],
                                                "INPUT_NAME"=>"FILES",
                                                "INPUT_NAME_UNSAVED"=>"FILES_TMP",
                                                'INPUT_VALUE'=>$arResult['FILES_EDIT_VALUE'],
                                                "MULTIPLE"=>"Y",
                                                "MODULE_ID"=>"altasib.support",
                                                )
                                            );
                                            ?>

                                        </td>
                                </tr>

                </tbody>
                <?endif;?>
        </table>
<?endif;?>