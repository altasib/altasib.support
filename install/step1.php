<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<input type="hidden" name="id" value="altasib.support">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">

<?
$obSite = CSite::GetList($by="sort", $order="asc");
while ($site = $obSite->Fetch()):
?>
<label><input type="checkbox" name="install_public[<?=$site['LID']?>]" value="Y"><?=GetMessage('ALTASIB_SUPPORT_INSTALL_DIR')?> <?=$site['NAME']?></label><br />
<?=GetMessage('ALTASIB_SUPPORT_INSTALL_DIR_NAME')?>: <input type="text" name="install_dir[<?=$site['LID']?>]" id="install_dir" value="support"><br />
<?endwhile;?>
        <input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
</form>