<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Apoyos extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
    }
    public function index()
    {
         $this->template->title($this->module_details['name'])
                ->build('index');
    }
 }
 ?>