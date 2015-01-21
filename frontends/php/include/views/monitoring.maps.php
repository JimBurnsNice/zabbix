<?php
/*
** Zabbix
** Copyright (C) 2001-2015 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
**/


$mapWidget = new CWidget('hat_maps');
$mapWidget->setTitle(_('Maps'));

$mapTable = new CTable(_('No maps found.'), 'map map-container');
$mapTable->setAttribute('style', 'margin-top: 4px;');

$icon = $fsIcon = null;

if (!empty($this->data['maps'])) {
	$mapComboBox = new CComboBox('sysmapid', getRequest('sysmapid', 0), 'submit()');
	foreach ($this->data['maps'] as $sysmapId => $map) {
		$mapComboBox->addItem($sysmapId, $map['name']);
	}

	$headerMapForm = new CForm('get');
	$headerMapForm->cleanItems();
	$controls = new CList();
	$headerMapForm->addVar('fullscreen', $this->data['fullscreen']);
	$controls->addItem(array(_('Maps').SPACE, $mapComboBox));

	$controls->addItem(array(_('Minimum severity').SPACE, $this->data['pageFilter']->getSeveritiesMinCB()));
	// get map parent maps
	$parentMaps = array();
	foreach (getParentMaps($this->data['sysmapid']) as $parent) {
		// check for permissions
		if (isset($this->data['maps'][$parent['sysmapid']])) {
			$parentMaps[] = SPACE.SPACE;
			$parentMaps[] = new CLink($parent['name'], 'maps.php?sysmapid='.$parent['sysmapid'].'&fullscreen='.$this->data['fullscreen'].'&severity_min='.$this->data['severity_min']);
		}
	}
	if (!empty($parentMaps)) {
		array_unshift($parentMaps, _('Upper level maps').':');
		$controls->addItem($parentMaps);
	}

	$actionMap = getActionMapBySysmap($this->data['map'], array('severity_min' => $this->data['severity_min']));

	$mapTable->addRow($actionMap);

	$imgMap = new CImg('map.php?sysmapid='.$this->data['sysmapid'].'&severity_min='.$this->data['severity_min']);
	$imgMap->setMap($actionMap->getName());
	$mapTable->addRow($imgMap);

	$icon = get_icon('favourite', array(
		'fav' => 'web.favorite.sysmapids',
		'elname' => 'sysmapid',
		'elid' => $this->data['sysmapid']
	));
	$fsIcon = get_icon('fullscreen', array('fullscreen' => $this->data['fullscreen']));

	$controls->addItem($icon);
	$controls->addItem($fsIcon);

	$headerMapForm->addItem($controls);
	$mapWidget->setControls($headerMapForm);
}

$mapWidget->addItem($mapTable);

return $mapWidget;
