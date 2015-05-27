<?php
namespace Etobi\Coreapi\Wrapper;

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

/**
 * ImportExportWrapper
 *
 * @package TYPO3
 * @subpackage tx_coreapi
 */
class ImportExportWrapper extends \TYPO3\CMS\Impexp\ImportExport {

	/**
	 * @return string
	 */
	public function getErrorLog() {
		$errors = array();
		if (count($this->errorLog)) {
			$cnt = 1;
			foreach ($this->errorLog AS $errorLog) {
				$info = explode(':', $errorLog);
				if (count($info) > 1) {
					$key = sprintf('%2d', $cnt) . ' impexp log: ' . trim(array_shift($info));
					$errors = array_merge($errors, array($key => trim(implode(':', $info))));
				} else {
					$errors = array_merge($errors, array(sprintf('%2d', $cnt) . ' impexp log: ' => $errorLog));
				}
				$cnt++;
			}
		}
		return $errors;
	}

	/**
	 * @return \Etobi\Coreapi\Wrapper\TceMainWrapper')
	 */
	public function getNewTCE() {
		$tce = GeneralUtility::makeInstance('Etobi\Coreapi\Wrapper\DataHandlerWrapper');
		$tce->stripslashes_values = 0;
		$tce->dontProcessTransformations = 1;
		$tce->enableLogging = $this->enableLogging;
		$tce->alternativeFileName = $this->alternativeFileName;
		$tce->alternativeFilePath = $this->alternativeFilePath;
		return $tce;
	}

}
