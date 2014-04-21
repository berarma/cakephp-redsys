<?php
/**
 *
 * CakePHP Helper to interact with the Sermepa TPV service
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

class SermepaHelper extends AppHelper {

	public $helpers = array('Form');

	public function renderForm($options = array()) {
		$options['url'] = $this->request->params['sermepaUrl'];
		$data = $this->request->params['sermepaData'];
		$output = $this->Form->create($options);
		foreach ($data as $key => $value) {
			$output .= $this->Form->hidden($key, array('name' => $key, 'value' => $value));
		}
		$output .= $this->Form->end();
		return $output;
	}

}

