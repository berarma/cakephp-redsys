<?php
/**
 *
 * CakePHP Helper to interact with the Redsys TPV service
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

class RedsysHelper extends AppHelper {

	public $helpers = array('Form');

/**
 * Renders form
 *
 * @param array $options Form options
 * @return string Form
 */
	public function renderForm($options = array()) {
		$options['url'] = $this->request->params['RedsysUrl'];
		$output = $this->Form->create($options);
		$output = preg_replace('/<div.*/', '', $output);
		$output .= $this->Form->hidden('Ds_MerchantParameters', array('name' => 'Ds_MerchantParameters', 'value' => $this->request->params['RedsysParameters']));
		$output .= $this->Form->hidden('Ds_Signature', array('name' => 'Ds_Signature', 'value' => $this->request->params['RedsysSignature']));
		$output .= $this->Form->hidden('Ds_SignatureVersion', array('name' => 'Ds_SignatureVersion','value' => $this->request->params['RedsysSignatureVersion']));
		$output .= $this->Form->end();
		return $output;
	}
}

