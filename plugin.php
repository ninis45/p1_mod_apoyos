<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Search Plugin
 *
 * Use the search plugin to display search forms and content
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Search\Plugins
 */
class Plugin_Galeria extends Plugin
{
	public $version = '1.0.0';

	public $name = array(
		'en' => 'Search',
            'fa' => 'جستجو',
	);

	public $description = array(
		'en' => 'Create a search form and display search results.',
        'fa' => 'ایجاد فرم جستجو و نمایش نتایج',
	);
    public function __construct()
	{
		$this->load->model(array(
			'files/file_m',
			'files/file_folders_m'
		));
	}
    
    public function listing()
    {
        
        
        $f= $this->input->get('f');
        $folder = $this->attribute('folder','galeria');
        
       
        if(empty($f))
        {
            $folder = $this->file_folders_m->get_by_path($folder);
        }
        else
        {
            $folder = $this->file_folders_m->get_by_path($f);
        }
        
        
        if(!$folder) return false;
        
        $files = $this->db->where('folder_id',$folder->id)
                    ->where('type','i')
                    ->get('files')
                    ->result();
        
       
        
        
        return $files;
    }
}