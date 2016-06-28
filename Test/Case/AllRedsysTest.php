<?php
/**
 *
 * Redsys Plugin Test Suite
 *
 * Copyright 2016 Bernat Arlandis
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
 * @copyright Copyright 2016 Bernat Arlandis
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

class AllRedsysPluginTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Redsys Plugin tests');
		$suite->addTestDirectoryRecursive(App::pluginPath('Redsys') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}
}

