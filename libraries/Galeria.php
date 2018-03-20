<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * PyroCMS File library. 
 *
 * This handles all file manipulation 
 * both locally and in the cloud
 * 
 * @author		Jerel Unruh - PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Files\Libraries
 */
class Galeria
{
    public		static $providers;
	public 		static $path;
	public		static $max_size_possible;
	public		static $max_size_allowed;
	protected	static $_cache_path;
	protected 	static $_ext;
	protected	static $_type = '';
	protected	static $_filename = null;
	protected	static $_mimetype;
    
    public function __construct()
	{
		ci()->load->config('files/files');

		self::$path = config_item('files:path');
		self::$_cache_path = config_item('cache_dir').'cloud_cache/';

		if ($providers = Settings::get('files_enabled_providers'))
		{
			self::$providers = explode(',', $providers);

			// make 'local' mandatory. We search for the value because of backwards compatibility
			if ( ! in_array('local', self::$providers))
			{
				array_unshift(self::$providers, 'local');
			}
		}
		else
		{
			self::$providers = array('local');
		}

		// work out the most restrictive ini setting
		$post_max = str_replace('M', '', ini_get('post_max_size'));
		$file_max = str_replace('M', '', ini_get('upload_max_filesize'));
     
       
        
		// set the largest size the server can handle and the largest the admin set
		self::$max_size_possible = ($file_max > $post_max ? $post_max : $file_max) * 1048576; // convert to bytes
		self::$max_size_allowed = Settings::get('files_upload_limit') * 1048576; // convert this to bytes also

		set_exception_handler(array($this, 'exception_handler'));
		set_error_handler(array($this, 'error_handler'));

		ci()->load->model('files/file_m');
		ci()->load->model('files/file_folders_m');
		ci()->load->spark('cloudmanic-storage/1.0.4');
	}

    public static function allowed_actions()
	{
		$allowed_actions = array();

		foreach (ci()->module_m->roles('galeria') as $value)
		{
			// build a simplified permission list for use in this module
			if (isset(ci()->permissions['galeria']) and array_key_exists($value, ci()->permissions['galeria']) or ci()->current_user->group == 'admin')
			{
				$allowed_actions[] = $value;
			}
		}

		return $allowed_actions;
	}
    public static function folder_tree($id_parent)
    {
        $folders = array();
		$folder_array = array();

		ci()->db->select('id, parent_id, slug, name')->where(array('hidden'=>0,'parent_id'=>$id_parent))->order_by('sort');
		$all_folders = ci()->file_folders_m->get_all();
       
		// we must reindex the array first
		foreach ($all_folders as $row)
		{
			$folders[$row->id] = (array)$row;
		}

		unset($tree);
        
		// build a multidimensional array of parent > children
		foreach ($folders as $row)
		{
		  
            //$childrens = Descargas::folder_tree($row['id']);
            
			/*if (array_key_exists($row['parent_id'], $folders))
			{
				// add this folder to the children array of the parent folder
				$folders[$row['parent_id']]['children'][] =& $folders[$row['id']];
			}*/

			// this is a root folder
			if ($row['parent_id'] == $id_parent)
			{
				$folder_array[] =& $folders[$row['id']];
			}
            
            /*if($childrens)
            {
                $folder_array[$row['parent_id']]['children'] = $childrens;
            }*/
		}
		return $folder_array;
    }
}