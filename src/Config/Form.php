<?php
namespace Horde\Gollem\Config;
use Horde_Form;
use Horde_Config_Form;
use Horde;
class Form extends Horde_Config_Form
{    
    public function __construct($vars, $app = 'gollem')
    {
        $this->addVariable(_('Backend Administration'), 'add_backend', 'link', true, false, _('Add a gollem files backend'), [[
            'url' => Horde::url($GLOBALS['registry']->get('webroot', $app) . '/admin/backend/new', true),
            'text' => _('Add Backend'),
            'title' => 'plus'
        ]]
        );
        parent::__construct($vars, $app);
    }
}