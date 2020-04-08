<?php

use Bitrix\Main\Config\Option,
	Bitrix\Main\Localization\Loc;

$module_id = 'dmbgeo.neworderstatus';
$module_path = str_ireplace($_SERVER["DOCUMENT_ROOT"], '', __DIR__) . $module_id . '/';
CModule::IncludeModule('main');
CModule::IncludeModule($module_id);
CModule::IncludeModule('sale');
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);
if ($APPLICATION->GetGroupRight($module_id) < "S") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}
$db_dtype = \CSaleStatus::GetList();

$saleStatuses = array();
while ($ar_dtype = $db_dtype->Fetch()) {
	$saleStatuses[$ar_dtype['ID']] = $ar_dtype['NAME'];
}


$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$SITES = \NewOrderStatus::getSites();

foreach ($SITES as $SITE) {
	$aTabs[] = array(
		'DIV' => $SITE['LID'],
		'TAB' => $SITE['NAME'],
		'OPTIONS' => array(
			array('OPTION_STATUS_' . $SITE['LID'], Loc::getMessage('OPTION_STATUS'), 'Y', array('checkbox', 1))
		),
	);
	$params[] = 'OPTION_STATUS_' . $SITE['LID'];
	$params[] = 'OPTION_ORDER_STATUS_' . $SITE['LID'];
	$params[] = 'OPTION_ORDER_FILTER_DELIVERY_' . $SITE['LID'];
	$params[] = 'OPTION_ORDER_FILTER_PAY_' . $SITE['LID'];
}

if ($request->isPost() && $request['Apply'] && check_bitrix_sessid()) {

	foreach ($params as $param) {
		if (array_key_exists($param, $_POST) === true) {

			Option::set($module_id, $param, is_array($_POST[$param]) ? implode(",", $_POST[$param]) : $_POST[$param]);
		} else {
			Option::set($module_id, $param, "N");
		}
	}
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>

<form method='post' action='<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>' name='DMBGEO_settings'>

	<? $n = count($aTabs); ?>
	<? foreach ($aTabs as $key => $aTab) :
		if ($aTab['OPTIONS']) : ?>
			<? $tabControl->BeginNextTab(); ?>
			<? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>
			<tr>
				<?
				$OPTION_ORDER_STATUS = COption::GetOptionString($module_id, 'OPTION_ORDER_STATUS_' . $aTab['DIV']);
				?>
				<td class="adm-detail-valign-top adm-detail-content-cell-l" width="50%"><? echo Loc::getMessage("OPTION_ORDER_STATUS"); ?><a name="opt_OPTION_ORDER_STATUS_<?= $aTab['DIV']; ?>"></a></td>
				<td width="50%" class="adm-detail-content-cell-r">
					<select size="1" id='OPTION_ORDER_STATUS_<?= $aTab['DIV']; ?>' name="OPTION_ORDER_STATUS_<?= $aTab['DIV']; ?>">
						<? foreach ($saleStatuses as $key => $status) : ?>
							<?
							$option = '';
							$option .= '<option value="' . $key . '"';
							if ($key == $OPTION_ORDER_STATUS) {
								$option .= ' selected="selected" ';
							}
							$option .= '>';
							$option .= $status;
							$option .= '</option>';
							?>
							<? echo $option; ?>
						<? endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<?
				$OPTION_ORDER_FILTER_DELIVERY = COption::GetOptionString($module_id, 'OPTION_ORDER_FILTER_DELIVERY_' . $aTab['DIV']);
				$OPTION_ORDER_FILTER_DELIVERY = explode(',', $OPTION_ORDER_FILTER_DELIVERY);
				?>
				<td class="adm-detail-valign-top adm-detail-content-cell-l" width="50%"><? echo Loc::getMessage("OPTION_ORDER_FILTER_DELIVERY"); ?><a name="opt_OPTION_ORDER_FILTER_DELIVERY_<?= $aTab['DIV']; ?>[]"></a></td>
				<td width="50%" class="adm-detail-content-cell-r">
					<select size="5" multiple id='OPTION_ORDER_FILTER_DELIVERY_<?= $aTab['DIV']; ?>' name="OPTION_ORDER_FILTER_DELIVERY_<?= $aTab['DIV']; ?>[]">


						<? $db_dtype = \CSaleDelivery::GetList(
							array(),
							array(
								"ACTIVE" => "Y"
							)
						);
						?>

						<? while ($ar_dtype = $db_dtype->Fetch()) : ?>
							<?
							$option = '';
							$option .= '<option value="' . $ar_dtype['ID'] . '"';
							if (in_array($ar_dtype['ID'], $OPTION_ORDER_FILTER_DELIVERY)) {
								$option .= ' selected="selected" ';
							}
							$option .= '>';
							$option .= $ar_dtype['NAME'];
							$option .= '</option>';
							?>
							<? echo $option; ?>
						<? endwhile; ?>

					</select>
				</td>
			</tr>
			<tr>
				<?
				$OPTION_ORDER_FILTER_PAY = COption::GetOptionString($module_id, 'OPTION_ORDER_FILTER_PAY_' . $aTab['DIV']);
				$OPTION_ORDER_FILTER_PAY = explode(',', $OPTION_ORDER_FILTER_PAY);
				?>
				<td class="adm-detail-valign-top adm-detail-content-cell-l" width="50%"><? echo Loc::getMessage("OPTION_ORDER_FILTER_PAY"); ?><a name="opt_OPTION_ORDER_FILTER_PAY_<?= $aTab['DIV']; ?>[]"></a></td>
				<td width="50%" class="adm-detail-content-cell-r">
					<select size="5" multiple id='OPTION_ORDER_FILTER_PAY_<?= $aTab['DIV']; ?>' name="OPTION_ORDER_FILTER_PAY_<?= $aTab['DIV']; ?>[]">


						<? $db_dtype = \CSalePaySystem::GetList(
							array(),
							array(
								"ACTIVE" => "Y"
							)
						);
						?>

						<? while ($ar_dtype = $db_dtype->Fetch()) : ?>
							<?
							$option = '';
							$option .= '<option value="' . $ar_dtype['ID'] . '"';
							if (in_array($ar_dtype['ID'], $OPTION_ORDER_FILTER_PAY)) {
								$option .= ' selected="selected" ';
							}
							$option .= '>';
							$option .= $ar_dtype['NAME'];
							$option .= '</option>';
							?>
							<? echo $option; ?>
						<? endwhile; ?>

					</select>
				</td>
			</tr>
		<? endif ?>
	<? endforeach; ?>
	<?

	$tabControl->Buttons(); ?>

	<input type="submit" name="Apply" value="<? echo GetMessage('MAIN_SAVE') ?>">
	<input type="reset" name="reset" value="<? echo GetMessage('MAIN_RESET') ?>">
	<?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>