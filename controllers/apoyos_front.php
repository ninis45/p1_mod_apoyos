<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Apoyos_front extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
        $this->lang->load('apoyos');
        $this->load->library('files/files');
        $this->load->model(array('apoyo_m','depositos/deposito_m'));

        if($this->current_user == false)
        {
            $this->session->set_userdata('redirect_to', current_url());
            redirect('users/login');
        }

        $this->director = $this->db->where('user_id',$this->current_user->id)->get('directores')->row();

        $this->template->set('director',$this->director);

        $this->validation_rules = array(


                 array(
    				'field' => 'id_director',
    				'label' => 'Director',
    				'rules' => 'trim'
    				),
        );
    }
    public function index()
    {
         $depositos = $this->deposito_m->select('YEAR(fecha_deposito) AS anio')
                        ->where('id_director',$this->director->id)
                        ->group_by('YEAR(fecha_deposito)')->get_all();
         $this->template->title($this->module_details['name'])
                ->set('depositos',$depositos)
                ->build('index');
    }
    function load($anio=false)
    {
        $anio   = $anio ? $anio : date('Y');
        $status = $this->input->get('tab')?$this->input->get('tab'):'pendientes';
        $data = array(

            'Pendientes' => array(),
            'Recibidos'  => array(),
            'Validados'  => array()

         );

         $base_where = array(

            'YEAR(fecha_deposito)' => $anio,
            'depositos.tipo' => 'apoyo' ,
            'depositos.id_director' => $this->director->id
         );
         switch($status)
         {
            case 'pendientes':
                //$base_where['id'] = null;
                $base_where['apoyos.id IS NULL'] = null;
            break;
            case 'enviados':
                 $base_where['estatus IN(\'pendiente\',\'rechazado\')'] = NULL;
                 $base_where['apoyos.id_deposito IS NOT NULL'] = null;
            break;
            case 'validados':
                $base_where['estatus'] = 'validado';
            break;
         }
         $depositos = $this->db->select('*, depositos.id AS id_deposito,depositos.tipo AS tipo_pago,directores.id AS id_director, centros.nombre AS nombre_centro, directores.nombre AS nombre_director,apoyos.id AS id_apoyo')
             ->join('apoyos','apoyos.id_deposito=depositos.id','LEFT')
             ->join('directores','directores.id=depositos.id_director')
             ->join('centros','centros.id=directores.id_centro')
             ->order_by('concepto,fecha_deposito','DESC')
             ->where($base_where)
             //->or_where($base_where1)
             ->get('depositos')->result();

          $this->template->title($this->module_details['name'],sprintf(lang('apoyos:admin'),$anio))
                ->set('depositos',$depositos)
                ->set('anio',$anio)
                ->set('status',$status)
                ->build('index');

    }
    function details($id=0)
    {
         $deposito = $this->deposito_m->select('*,directores.nombre AS nombre_director, centros.nombre AS nombre_centro,apoyos.id AS id')
                        ->join('directores','directores.id=depositos.id_director')
                        ->join('centros','centros.id=directores.id_centro')
                        ->join('apoyos','apoyos.id_deposito=depositos.id')
                        ->where('depositos.id_director',$this->director->id)
                        ->get_by('apoyos.id',$id) OR show_404();

          list($y,$m,$y) = explode('-',$deposito->fecha_deposito);

         $deposito->facturas = $this->db->select('*,xml_uuid AS folio_uuid')->where('id_apoyo',$deposito->id)->get('apoyo_facturas')->result();
        $fichas = $this->db->select('*,no_operacion AS operacion')->where('id_apoyo',$deposito->id)->get('apoyo_depositos')->result();




        $this->template->title($this->module_details['name'])
                ->append_metadata('<script type="text/javascript"> var total_importe='.$deposito->importe.',depositos='.($fichas?json_encode($fichas):'[]').',facturas='.($deposito->facturas?json_encode($deposito->facturas):'[]').';</script>')
                ->append_js('module::front/form.js')
                ->set('anio',$y)
                ->set('facturas',$deposito->facturas)
                ->set('fichas',$fichas)
                ->set('deposito',$deposito)
                ->build('form');
    }
    function edit($id=0)
    {
        $deposito = $this->deposito_m->select('*,directores.nombre AS nombre_director, centros.nombre AS nombre_centro,apoyos.id AS id')
                        ->join('directores','directores.id=depositos.id_director')
                        ->join('centros','centros.id=directores.id_centro')
                        ->join('apoyos','apoyos.id_deposito=depositos.id','LEFT')
                        ->where('depositos.id_director',$this->director->id)
                        ->get_by('depositos.id',$id) OR show_404();

        if($deposito->id)
        {
            redirect('apoyos/detalles/'.$deposito->id);
        }

        list($y,$m,$y) = explode('-',$deposito->fecha_deposito);


        //$this->form_validation->set_rules($this->validation_rules['form']);

        if($_POST)

        {
             unset($_POST['btnAction']);
             //print_r($this->input->post());
             //exit();
             $id = $this->apoyo_m->save($id,$this->input->post());

             if($id)
             {
                 redirect('apoyos/detalles/'.$id);
             }

        }

        $deposito->facturas = $this->db->select('*,xml_uuid AS folio_uuid')->where('id_apoyo',$deposito->id)->get('apoyo_facturas')->result();
        //$depositos = $this->db->where('id_apoyo',$deposito->id)
        $fichas = $this->db->where('id_apoyo',$deposito->id)->get('apoyo_depositos')->result();
				

        $this->template->title($this->module_details['name'])
                ->append_metadata('<script type="text/javascript"> var total_importe='.$deposito->importe.', depositos='.($fichas?json_encode($fichas):'[]').', facturas='.($deposito->facturas?json_encode($deposito->facturas):'[]').', SITE_URL=\''.base_url().'\';</script>')
                ->append_js('module::front/form.js')
                ->set('anio',$y)
                ->set('deposito',$deposito)
                ->build('form');
    }

    function upload()
    {
        $this->load->library('facturas/factura');
        $this->load->model('files/file_folders_m');
        $result = array(

            'status'  => false,
            'message' => '',
            'data'    => array()
        );
        $folder = $this->file_folders_m->get_by_path('facturacion') OR show_error('No hay carpeta');

        $result_xml =   Files::upload($folder->id,false,'xml',false,false,false,'xml');
        $result_pdf =   Files::upload($folder->id,false,'pdf',false,false,false,'pdf');



        if($result_pdf['status'] && $result_xml['status'])
        {

             $valid_xml = Factura::ValidXML($result_xml['data']['id'],array('total'));
             $result['data']['pdf'] = $result_pdf;
             if($valid_xml['status'] && isset($valid_xml['data']['total']) && $valid_xml['data']['total'] > 0)
             {
                $result_xml['data']['total']      = $valid_xml['data']['total'];
                $result_xml['data']['folio_uuid'] = $valid_xml['data']['UUID'];
                $result['data']['xml'] = $result_xml;
                
                $result['status'] = true;
             }
             
             else{
                 Files::delete_file($result_pdf['data']['id']);
                 Files::delete_file($result_xml['data']['id']);
                 
                 if(is_array($valid_xml['messages']))
                 {
                    foreach($valid_xml['messages'] as $msg){
                        $result['message'] .= '<div class="alert alert-danger">'.$msg['message'].'</div>';
                    }
                 }
                 else
                    $result['message'] = '<div class="alert alert-danger">'.$valid_xml['message'].'</div>';
             }


             
        }
        else
        {
            if(!$result_xml['status'] && $result_xml['message'])
                $result['message'] = '<div class="alert alert-danger">'.$result_xml['message'].'</div>';

            if(!$result_pdf['status'] && $result_pdf['message'])
                $result['message'] .= '<div class="alert alert-danger">'.$result_pdf['message'].'</div>';
            if($result_pdf['status'])
            {
                Files::delete_file($result_pdf['data']['id']);
            }
            if($result_xml['status'])
            {
                Files::delete_file($result_xml['data']['id']);
            }
        }

        return $this->template->build_json($result);
    }

 }
 ?>
