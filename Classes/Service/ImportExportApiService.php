<?php
namespace Etobi\Coreapi\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Achim Fritz <af@lightwerk.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Impexp API service
 *
 * @package TYPO3
 * @subpackage tx_coreapi
 */
class ImportExportApiService {

	/**
	 * @return array<string>
	 */
	public function help() {
		$messages = array('impexp:import' => 'pid fileName update=FALSE');
		return $messages;
	}

	/**
	 * @param integer $pid 
	 * @param string $fileName 
	 * @param boolean $update 
	 * @return array<string>
	 */
	public function import($pid, $fileName, $update = FALSE) {
		$messages = array();
		if (file_exists($fileName) === FALSE) {
			throw new ImportExportApiServiceException('file not exists ' . $fileName, 1373142899);
		}
		$impexpWrapper = GeneralUtility::makeInstance('Etobi\Coreapi\Wrapper\ImportExportWrapper');
		$impexpWrapper->init(0, 'import');
		$impexpWrapper->update = $update;
		$impexpWrapper->enableLogging = TRUE;
		$impexpWrapper->global_ignore_pid = TRUE;
		$impexpWrapper->force_all_UIDS = FALSE;
		$impexpWrapper->showDiff = TRUE;
		$impexpWrapper->allowPHPScripts = FALSE;
		$impexpWrapper->softrefInputValues = FALSE;
		try {
			$impexpWrapper->loadFile($fileName, 1);
		} catch (Exception $e) {
			throw new ImportExportApiServiceException('tx_impexp exception ' . $e->getMessage(), 1373142902);
		}

		// Check extension dependencies:
		if (is_array($impexpWrapper->dat['header']['extensionDependencies'])) {
			foreach($impexpWrapper->dat['header']['extensionDependencies'] as $extKey) {
				if (ExtensionManagementUtility::isLoaded($extKey) === FALSE) {
					throw new ImportExportApiServiceException('extension not installed ' . $extKey, 1373142903);
				}
			}
		}
		try {
			$impexpWrapper->importData((int)$pid);
		} catch (Exception $e) {
			throw new ImportExportApiServiceException('tx_impexp exception ' . $e->getMessage(), 1373142904);
		}
		$messages = array_merge($messages, $impexpWrapper->getErrorLog());
		$messages = array_merge($messages, array('FINISH' => 'importing ' . $fileName . ' on pid=' . $pid));
		return $messages;
	}
}
