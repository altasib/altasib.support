<?
$module = "altasib.support";
if(IsModuleInstalled($module))
{
    DeleteDirFilesEx("/local/components/altasib/support");
    DeleteDirFilesEx("/local/components/altasib/support.ticket.detail");
    DeleteDirFilesEx("/local/components/altasib/support.ticket.form");
    DeleteDirFilesEx("/local/components/altasib/support.ticket.list");
    DeleteDirFilesEx("/local/components/altasib/support.desktop");
    DeleteDirFilesEx("/local/components/altasib/support.ticket.detail.info");
    $updater->CopyFiles("install/components", "components");
}
?>