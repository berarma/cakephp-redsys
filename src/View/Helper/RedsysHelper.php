<?php
/**
 *
 * CakePHP Helper to interact with the Redsys TPV service
 *
 * Copyright (c) Bernat Arlandis
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
 * @copyright Copyright (c) Bernat Arlandis
 * @link http://bernatarlandis.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
namespace Berarma\Redsys\View\Helper;

use Cake\View\Helper;

class RedsysHelper extends Helper
{
    public $helpers = ['Form'];

    /**
     * Renders form
     *
     * @param array $options Form options
     * @return string Form
     */
    public function renderForm($options = [])
    {
        $options['url'] = $this->getView()->getRequest()->getData('Redsys.url');
        $options += [
            'templates' => []
        ];
        $options['templates'] += ['hiddenBlock' => ''];
        $output = $this->Form->create(null, $options);
        $output .= $this->Form->hidden('Redsys.parameters', ['name' => 'Ds_MerchantParameters']);
        $output .= $this->Form->hidden('Redsys.signature', ['name' => 'Ds_Signature']);
        $output .= $this->Form->hidden('Redsys.signatureVersion', ['name' => 'Ds_SignatureVersion']);
        $output .= $this->Form->end();
        return $output;
    }
}
