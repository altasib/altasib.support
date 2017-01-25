<?
$module = "altasib.support";
if(IsModuleInstalled($module))
{
    $updater->CopyFiles("install/components", "components");
}
?>