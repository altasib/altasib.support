<?
$module = "altasib.support";
if(IsModuleInstalled($module))
{
    DeleteDirFilesEx("/bitrix/components/altasib/support");
    DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.detail");
    DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.form");
    DeleteDirFilesEx("/bitrix/components/altasib/support.ticket.list");
    $updater->CopyFiles("install/components", "components");
}
?>