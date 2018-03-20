<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Apoyos_m extends MY_Model {

	public function __construct()
	{		
		parent::__construct();
		
		/**
		 * If the sample module's table was named "samples"
		 * then MY_Model would find it automatically. Since
		 * I named it "sample" then we just set the name here.
		 */
		$this->_table = 'apoyos';

	}
	
	//create a new item
	public function create($input)
	{


	    $data = array(
            'id_centro'      => $input['id_centro'],
            'id_director'    => $input['id_director'],
            'id_deposito'    => $input['id_deposito'],
            'fecha_registro' => date('Y-m-d'),
            'estatus'         => 'Pendiente',
            'created_on' => now()
            
            
        );
        
       
        return $this->insert($data);
    }

    public function edit($id,$input)
    {
        $data = array(
 
            'fecha_registro'    => $input['fecha_registro'],
            'estatus'            => $input['estatus'],
            'xml'               => isset($input['xml'])?$input['xml']:NULL,
            'pdf'               => isset($input['pdf'])?$input['pdf']:NULL,
            'xml_uuid'          => $input['xml_uuid']?$input['xml_uuid']:NULL,


        );
        
        
        return $this->db->where('id',$id)
                ->set($data)
                ->update($this->_table);
    }

    
    
    /*function get_reporte($base_where=array())
    {
        
        $result = $this->db->select('*,centros.nombre AS nombre_centro,directores.nombre AS nombre_director,depositos.tipo AS tipo,depositos.banco AS banco,depositos.no_tarjeta AS no_tarjeta')
                    ->order_by('ordering_count,fecha_deposito')
                    ->where($base_where)
                    ->join('centros','centros.id=depositos.id_centro')
                    ->join('directores','directores.id=depositos.id_director')
                    ->get($this->_table)
                    ->result();
                    
        return $result;
    }*/



    
 }
 ?>