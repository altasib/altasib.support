<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
$altasib_support_mobile = intval($_REQUEST['a']); // detect mobile device
if($arParams['ID']>0):
		$isOpen = true;
		$classSidebarBtn = 'sidebar-btn-close';
		$altasib_sidebar_float_block_hide = "";
		$altasib_sidebar_resizer_close = "";
		$altasib_sidebar_close = "";

		$sidebarUserOptions = CUserOptions::GetOption('support','right-sidebar');
		if($sidebarUserOptions['min'] === 'true')
		{
			$classSidebarBtn = 'sidebar-btn-close sidebar-btn-show';
			$altasib_sidebar_float_block_hide = "altasib-sidebar-float-block-hide";
			$altasib_sidebar_resizer_close = "altasib_sidebar_resizer-close";
			$altasib_sidebar_close = "altasib_sidebar-close";		
			$isOpen = false;
		}
		?>


		<script type="text/javascript">
		AltasibSupport.Bar.isOpen = <?=($isOpen ? 'true': 'false');?>;
		</script>
<?endif?>		
<div id="altasib-support-detail">
<?if (!$altasib_support_mobile):?>

	<table width="100%" border="0" id="altasib-support-main-table">
	<tr>
		<td valign="top">
			<div  id="support-detail-main">
				<?$APPLICATION->IncludeComponent("altasib:support.ticket.detail", "", $arParams,$component);?>
				<?$APPLICATION->IncludeComponent("altasib:support.ticket.form", "", $arParams,$component);?>
			</div>
		</td>
		<?if($arParams['ID']>0):?>
		<td id="altasib_sidebar_resizer" onMouseDown="AltasibSupport.Bar.Resizer(event);" class="<?=$altasib_sidebar_resizer_close?>">
			<div id="sidebar-btn" class="<?=$classSidebarBtn?>" style="display: none;" onclick="AltasibSupport.Bar.SidebarMin()"></div>
		</td>
		<td width="260" id="altasib_sidebar" valign="top" class="<?=$altasib_sidebar_close?>">
			<div id="altasib-sidebar-float-block" class="<?=$altasib_sidebar_float_block_hide?>">
				<div id="sidebar-block">
					<?$APPLICATION->IncludeComponent("altasib:support.ticket.detail.info", "", $arParams,$component);?>
				</div>
			</div>	
		</td>
		<?endif;?>
	</tr>
	</table>

<?else:?>	
	<a href="" id="altasib_supportToggleBar"> </a><a href="" id="altasib_supportFullScreen"> </a><br />
	<table width="100%" border="0" style="height: 100%">
	<tr>
		<td valign="top">
			<div  id="support-detail-main" style="overflow: hidden">
				<?$APPLICATION->IncludeComponent("altasib:support.ticket.detail", "", $arParams,$component);?>
				<?$APPLICATION->IncludeComponent("altasib:support.ticket.form", "", $arParams,$component);?>
			</div>
		</td>
		<?if($arParams['ID']>0):?>
		<td>
			<div id="sidebar-block-mobile">
				<?$APPLICATION->IncludeComponent("altasib:support.ticket.detail.info", "", $arParams,$component);?>
			</div>			
		</td>
		<?endif?>
	</tr>
	</table>	
<?endif;?>	
</div>