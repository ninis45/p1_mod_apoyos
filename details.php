<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Groups module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Groups
 */ 
class Module_Apoyos extends Module
{
	public $version = '1.0';

	public function info()
	{
		$info= array(
			'name' => array(
				'en' => 'Support',
				
				'es' => 'Apoyos',
				
			),
			'description' => array(
				'en' => 'Administration of Economic Support.',
				
				'es' => 'Administración de Apoyos Economicos',
				
			),
			'frontend' => false,
			'backend' => true,
			'menu' => 'admin',
            'roles' => array(
				'create', 'edit','delete'
			),
            'shortcuts' => array(
        			
                        array(
        					'name' => 'apoyos:report',
        					'uri' => 'admin/apoyos/report',
        					'class' => 'btn btn-success'
        				),
			),
			'uri' => 'admin/apoyos/{{ anio }}', 
            
        );

          /*  'sections'=>array(
                'apoyos'=>array(
                    'name'=>'apoyos:title',
                    'ng-if'=>'hide_shortcuts',
                    'uri' => 'admin/apoyos/{{ anio }}',
        		    'shortcuts' => array(
        				array(
        					'name' => 'apoyos:create',
        					'uri' => 'admin/apoyos/create/{{ anio }}',
        					'class' => 'btn btn-success'
        				),
                        array(
        					'name' => 'apoyos:report',
        					'uri' => 'admin/apoyos/report',
        					'class' => 'btn btn-primary'
        				),
        			)
                )
           )
		);*/
        
        /*if (function_exists('group_has_role'))
		{
			if(group_has_role('fondo', 'admin_fondo_partidas'))
			{
			    
				$info['sections']['partidas'] = array(
							'name' 	=> 'partidas:title',
							'uri' 	=> 'admin/fondo/partidas/{{ anio }}',
							'shortcuts' => array(
									'create' => array(
										'name' 	=> 'partidas:create',
										'uri' 	=> 'admin/fondo/partidas/create/{{ anio }}',
										'class' => 'btn btn-success'
									)
							)
				);
			}
		}*/
        
        
        return $info;
	}

	public function install()
	{
	    $this->dbforge->drop_table('apoyos');
        $this->dbforge->drop_table('apoyo_facturas');
		$tables = array(
			'apoyos'=>array(
    			'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
    			'id_director' => array('type' => 'INT', 'constraint' => 11,),
                'id_centro' => array('type' => 'INT', 'constraint' => 11,),
                
                'id_deposito' => array('type' => 'INT', 'constraint' => 11,),
                
    		  	'fecha_registro'  => array('type' => 'DATE','null'=>true),
                'created_on' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'updated_on' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                
                'estatus' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'observaciones' => array('type' => 'TEXT', 'null' => true,),
            
            ),

          'apoyo_facturas'=>array(
          	'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
          	'id_apoyo' => array('type' => 'INT', 'constraint' => 11,),
          	'total' => array('type' => 'DECIMAL','constraint' =>array(10,2), 'null' => true,),
          	'pdf' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
            'xml' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
            'xml_uuid' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
            'messages' => array('type' => 'TEXT', 'null' => true,),

          	)
			
		);
        
        if ( ! $this->install_tables($tables))
		{
			return false;
		}

        return true;
        
		

		
	}

	public function uninstall()
	{
	
        $this->dbforge->drop_table('apoyos');
        $this->dbforge->drop_table('apoyo_facturas');
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}
}

?>