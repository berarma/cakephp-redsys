<?php
/**
 *
 * Sermepa Plugin Test Suite
 *
 * Copyright 2014 Bernat Arlandis i Mañó
 *
 * This package is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This package is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright Copyright 2014 Bernat Arlandis i Mañó
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

class AllSermepaPluginTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Sermepa Plugin tests');
		$suite->addTestDirectoryRecursive(App::pluginPath('Sermepa') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}

