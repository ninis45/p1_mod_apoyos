<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller {
	protected $section='apoyos';
	protected $error=array();
	protected $file_data=false;
	public function __construct()
	{
		parent::__construct();
       //  $this->load->helper('fondo');
       // $this->load->config('apoyos');
        $this->lang->load(array('apoyos','calendar'));
        $this->load->model(array('apoyos_m',
                    'centros/centro_m',
                    'depositos/deposito_m',
                    'files/file_folders_m'
        ));

       
         
         $this->config->load('files/files');
         $this->lang->load('files/files');
       
         $this->load->library(array('files/files','facturas/factura'));
         $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
       // $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;



        $this->validation_rules = array(
			'form'=>array(
    			
                 array(
    				'field' => 'id_director',
    				'label' => 'Director',
    				'rules' => 'trim'
    				),
                array(
                    'field' => 'id_centro',
                    'label' => 'Centro',
                    'rules' => 'trim'
                    ),
                array(
    				'field' => 'id_importe',
    				'label' => 'Importe',
    				'rules' => 'trim'
    				),
                
                'status'=>array(
    				'field' => 'status',
    				'label' => 'Estado',
    				'rules' => 'trim'
    				),
                array(
    				'field' => 'xml_uuid',
    				'label' => 'UUID',
    				'rules' => 'trim'
    				),
                 array(
    				'field' => 'pdf',
    				'label' => 'PDF',
    				'rules' => 'trim'
    				),
                 array(
    				'field' => 'xml',
    				'label' => 'XML',
    				'rules' => 'trim'
    				)
            ),
            'report' => array(
            
                 array(
    				'field' => 'fecha_ini',
    				'label' => 'Inicio',
    				'rules' => 'trim|required'
    				),
                
                array(
    				'field' => 'fecha_fin',
    				'label' => 'Fin',
    				'rules' => 'trim|required'
    				),
            )
        
        
		);

        
    }
    
    
    private function  _valid_xml()
    {
        
       
        /*if(!$_FILES['xml_file']['name'])
        {
            return true;
            
            
        }
        
        $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
        $result = array();
        
        if(!$folder)
        {
            show_error(lang('fondo:error_folder'));    
        }
        
        $result = Files::upload($folder->id,false,'xml_file');*/
        
        libxml_use_internal_errors(true);
        
        $xml = new DOMDocument();
        $xsl = new DOMDocument();
        
        $proc = new XSLTProcessor;
        
       
        $data = array();
        
        $file = file_get_contents($this->_path.'/'.$result['data']['filename']);
        
        
        //print_r($file);
        $xml->load($this->_path.'/'.$result['data']['filename']);
        //$xml->loadXML($file);
        
       
       
        
        $elements     = $xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', '*');
        $complementos = $xml->getElementsByTagNameNS('http://www.sat.gob.mx/TimbreFiscalDigital','*');
        $addenda      = $xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3','Addenda');
        
        
        foreach($addenda as $element)
        {
            
            
            $xml->documentElement->removeChild($element);
        }
       
        
        
        
      
        foreach($elements as $element)
        {
            
            //print_r($element);
            $element->getAttribute('version') AND $data['version']     = $element->getAttribute('version');
            $element->getAttribute('sello') AND $data['sello']  = $element->getAttribute('sello');
            $element->getAttribute('certificado') AND $data['cert'] = $element->getAttribute('certificado');
            
           
            
        }
        //echo $xml->saveXML($xml);
        switch($data['version'])
        {
            case '3.0':
            
                $validate = $xml->schemaValidate($this->_path.'/facturacion/cfdv3.xsd');
                $xsl->load($this->_path.'/facturacion/cadenaoriginal_3_0.xslt');
            break;
            case '3.2':
                $validate = $xml->schemaValidate($this->_path.'/facturacion/cfdv32.xsd');
                $xsl->load($this->_path.'/facturacion/cadenaoriginal_3_2.xslt');
            break;
            default:
                $validate = false;
            break;
        }
        //$errors = libxml_get_errors();
        
        //echo $this->_path.'/facturacion/cadenaoriginal_3_2.xslt';
        //print_r($errors);
       
        
        if(!$validate)
        {
          
            $this->form_validation->set_message('_valid_xml', lang('fondo:error_xml'));
            Files::delete_file($result['data']['id']);
			return false;
        }
        
        foreach($complementos as $element)
        {
            
            $element->getAttribute('UUID') AND $data['UUID']     = $element->getAttribute('UUID');
            
            
           
            
        }
        
        $data['validacion'][] = array(1,'Estructura del XML válida');
        
       
        if(!$data['UUID'])
        {
            
            $this->form_validation->set_message('_valid_xml', lang('fondo:error_timbrado'));
            Files::delete_file($result['data']['id']);
			return false;
            //$data['validacion'][] = array(0,lang('fondo:error_timbrado'));
        }
        elseif($match = $this->db->select('*,centros.nombre AS nombre,fondo.id AS id')   
                ->where_not_in('fondo.id',$this->input->post('id'))    
                ->join('centros','centros.id=fondo.id_centro')
                ->where('xml_uuid',$data['UUID'])->get('fondo')->row())
        {
            $this->form_validation->set_message('_valid_xml', sprintf(lang('fondo:error_uuid'),$match->nombre,$match->id));
            Files::delete_file($result['data']['id']);
			return false;
            
            //$data['validacion'][] = array(0,sprintf(lang('fondo:error_uuid'),$match->nombre));
        }
        
        $proc->importStyleSheet($xsl); 
        $cadena = $proc->transformToXML($xml);
        
       
        
       
       
        if(!$cadena)
        {
            $this->form_validation->set_message('_valid_xml', lang('fondo:error_proc'));
            Files::delete_file($result['data']['id']);
			return false;
            
            //$data['validacion'][] = array(0,lang('fondo:error_cadena'));
        }
        
        
        
        $pem = (sizeof($data['cert'])<=1) ? $data['cert'] : $data['cert'][0];
       
        $pem = preg_replace("[\n|\r|\n\r]", '', $pem);
        $pem = preg_replace('/\s\s+/', '', $pem); 
        $cert = "-----BEGIN CERTIFICATE-----\n".chunk_split($pem,64)."-----END CERTIFICATE-----\n";
        
        
        $pubkeyid = openssl_get_publickey(openssl_x509_read($cert));
        
        if(!$pubkeyid)
        {
            $this->form_validation->set_message('_valid_xml', lang('fondo:error_cert'));
            Files::delete_file($result['data']['id']);
			return false;
            
             //$data['validacion'][] = array(0,lang('fondo:error_cert'));
        }
        
        
        
        
        
        
        
        $sello = openssl_verify($cadena, 
                     base64_decode($data['sello']), 
                     $pubkeyid, 
                     OPENSSL_ALGO_SHA1);
                     
        if(!$sello)
        {
            $this->form_validation->set_message('_valid_xml', lang('fondo:error_sello'));
            Files::delete_file($result['data']['id']);
			return false;
             //$data['validacion'][] = array(0,lang('fondo:error_sello'));
        }
        
        
        unset($data['sello'],$data['cert']);
        
        //$_POST['xml_validacion'] = json_encode($data['validacion']);        
        $_POST['xml']            = $result['data']['id'];        
        $_POST['xml_uuid']       = $data['UUID'];
        
        return true;
       
    }
    function index()
    {
        
     $min_fecha = $this->deposito_m->select_min('fecha_deposito')->group_by('fecha_deposito')->get_all();
     list($year,$month,$day) = explode('-',$min_fecha[0]->fecha_deposito);
        
         
         
        $this->template->title($this->module_details['name'])
            ->set('min_year',$year)
            ->build('admin/init');



    }


    //}
    function load($anio='')
    {
         $data = array(
         
            'Pendientes' => array(),
            'Recibidos'  => array(),
            'Validados'  => array()
         );
         
         $f_centro = $this->input->get('f_centro');
         
         $base_where = array(
         
            'YEAR(fecha_deposito)' => $anio,
            'depositos.tipo' => 'apoyo' 
         );
         
         if($f_centro)
         {
            $base_where['centros.id'] = $f_centro;
         }
         
         $depositos_bd= $this->db->select('*, depositos.id AS id_deposito,depositos.tipo AS tipo_pago,directores.id AS id_director, centros.nombre AS nombre_centro, directores.nombre AS nombre_director,apoyos.id AS id_apoyo')
             ->join('apoyos','apoyos.id_deposito=depositos.id','left')
             ->join('directores','directores.id=depositos.id_director')
             ->join('centros','centros.id=directores.id_centro')
             ->order_by('concepto,fecha_deposito','DESC')
             ->where($base_where)
             //->or_where($base_where1)
             ->get('depositos')->result();
         
         foreach($depositos_bd as $deposito)
         {
            list($year,$month,$day) = explode('-',$deposito->fecha_deposito);
            
            if(!$deposito->id_apoyo)
            {
                $data['Pendientes'][$deposito->concepto][]= $deposito;
            }
            else
            {
                $comprobado = $this->db->select('SUM(total) AS total')->where('id_apoyo',$deposito->id_apoyo)->get('apoyo_facturas')->row();
                
                if($comprobado)
                {
                    $deposito->comprobado = $comprobado->total;
                }
                if($deposito->estatus == 'Validado')
                {
                    $data['Validados'][$deposito->concepto][]= $deposito;
                }
                else
                {
                    $data['Recibidos'][$deposito->concepto][]= $deposito;
                }
                
            }
         }
         
         

        $centros = $this->db->get('centros')->result();
        
        
        $this->template->title($this->module_details['name'])
            ->append_js('module::apoyo.controller.js')
            ->append_metadata('<script type="text/javascript"> var depositos='.json_encode($data).'</script>')
            
            //->set('items',$items)
            ->set('anio',$anio)
            //->set('importe',$importe)
            //->set('total_rows',$total_rows)
            ->set('data',$data)
            ->set('centros',array_for_select($centros,'id','nombre'))
            
            ->build('admin/index');


    }


    function create($anio='', $id='')
    {
        role_or_die('apoyos','create');
        $apoyo = new StdClass();

             
        $deposito = $this->deposito_m->select('*,directores.nombre AS nombre_director, centros.nombre AS nombre_centro')
                        ->join('directores','directores.id=depositos.id_director')
                        ->join('centros','centros.id=directores.id_centro')
                        ->get_by('depositos.id',$id) OR redirect('admin/apoyos/'.$anio);
        // regresa registro con el id especifico del deposito//
        $base_where = array('depositos.id'=>$id);
        if(!$apoyo = $this->deposito_m->select('*,depositos.id AS id_deposito, directores.nombre AS nombre_director, centros.nombre AS nombre_centro')
                    ->join('directores','directores.id=depositos.id_director')
                    ->join('centros','centros.id=directores.id_centro')
                    ->get_by($base_where) )
        {
            $this->session->set_flashdata('error',lang('global:not_found_edit'));
            redirect('admin/apoyos/'.$anio);
        }
       

        $datos = $this->db->where('id',$apoyo->id_director)->get('directores')->row();
         
        
        $this->form_validation->set_rules($this->validation_rules['form']);
       
        if ($this->form_validation->run())
        {
             unset($_POST['btnAction']);
            
            
            if($id = $this->apoyos_m->create($this->input->post()))
            {
                
                $this->session->set_flashdata('success',sprintf(lang('apoyos:save_success'),$this->input->post('concepto')));
                
            }
            else
            {
                $this->session->set_flashdata('error',lang('global:save_error'));
                
            }
            redirect('admin/apoyos/edit/'.$id);
        }

        foreach ($this->validation_rules['form'] as $rule)
        {
               $apoyo->{$rule['field']} = $this->input->post($rule['field']);
        }
        


   

        $this->template->title($this->module_details['name'])
            ->enable_parser(false)
            ->set('datos',$datos)
            ->set('apoyo',$apoyo)
            ->set('deposito',$deposito)
            ->set('anio',$anio)
            
            //->append_js('module::apoyos.controller.js')
            ->build('admin/form');
        
    }


    function edit($id=0)
    {
        

        role_or_die($this->section, 'edit');
        $files = array();
        $apoyo = $this->apoyos_m
                            ->get_by('apoyos.id',$id) OR redirect('admin/apoyos');
                            
        if($apoyo->estatus == 'Validado')
        {
            $this->session->set_flashdata('error',lang('apoyos:error_estatus'));
            redirect('admin/apoyos');
        }
        $this->validation_rules['form']['status'] .= '|required';
        $this->form_validation->set_rules($this->validation_rules['form']);
       
        if ($this->form_validation->run())
        {
             unset($_POST['btnAction']);
            
            $data = array(
            
                'estatus'       => $this->input->post('estatus'),
                'observaciones' => $this->input->post('observaciones'),
                'updated_on' => now()
            );
            if($this->apoyos_m->update($id,$data))
            {
                
                $this->session->set_flashdata('success',lang('global:save_success'));
                
            }
            else
            {
                $this->session->set_flashdata('error',lang('global:save_error'));
                
            }
            redirect('admin/apoyos/edit/'.$id);
        }
        $deposito =  $this->deposito_m->select('*,centros.nombre AS nombre_centro,directores.nombre AS nombre_director')
                        ->join('directores','directores.id=depositos.id_director')
                        ->join('centros','centros.id=directores.id_centro')
                        ->get_by('depositos.id',$apoyo->id_deposito);
       

        $facturas = $this->db->where('id_apoyo',$id)
                                ->get('apoyo_facturas')->result();
                                
        if($facturas)
        {
            foreach($facturas as $factura)
            {
                $files[] = array(
                    'id'    => $factura->id,
                    'pdf'   => $factura->pdf,
                    'xml'   => $factura->xml,
                    'total' => $factura->total,
                    'messages' => json_decode($factura->messages)
                );
            }
            
        }
        
        list($year,$month,$day) = explode('-',$deposito->fecha_deposito);
        
        $this->template->title($this->module_details['name'])
              ->append_js('module::apoyo.controller.js')
              ->append_metadata('<script type="text/javascript">var id=\''.$id.'\',files='.json_encode($files).';</script>')
              ->set('anio',$year)
              ->set('apoyo',$apoyo)
              ->set('deposito',$deposito)
          

            
            ->build('admin/form');


 
        
    }
    function details($id=0)
    {
         $files = array();
         $apoyo = $this->apoyos_m
                            ->get_by('apoyos.id',$id) OR redirect('admin/apoyos');
           
          
         $deposito =  $this->deposito_m->select('*,centros.nombre AS nombre_centro,directores.nombre AS nombre_director')
                        ->join('directores','directores.id=depositos.id_director')
                        ->join('centros','centros.id=directores.id_centro')
                        ->get_by('depositos.id',$apoyo->id_deposito);
       

        $facturas = $this->db->where('id_apoyo',$id)
                                ->get('apoyo_facturas')->result();
                                
        if($facturas)
        {
            foreach($facturas as $factura)
            {
                $files[] = array(
                    'id'    => $factura->id,
                    'pdf'   => $factura->pdf,
                    'xml'   => $factura->xml,
                    'total' => $factura->total,
                    'messages' => json_decode($factura->messages)
                );
            }
            
        }
        list($year,$month,$day) = explode('-',$deposito->fecha_deposito);                 
         $this->template->title($this->module_details['name'])
              ->append_js('module::apoyo.controller.js')
              ->append_metadata('<script type="text/javascript">var id=\''.$id.'\',files='.json_encode($files).';</script>')
              ->set('anio',$year)
              ->set('apoyo',$apoyo)
              ->set('deposito',$deposito)
           

            
            ->build('admin/form');
    }
    function upload()
    {
        $result = array(
        
            'status'  => true,
            'message' => '',
            'data'    => false
        );
        $input = $this->input->post();
        
        $folder = $this->file_folders_m->get_by_path('facturacion');
        
        if($folder)
        {
            $result = Files::upload($folder->id,$input['name'],'file',false,false,false,'pdf|xml');
            
            if($result['status'])
            {
                $data = array(
                    'id_apoyo' => $input['id'],
                    //'id_factura' => $input['id_factura']
                    //{$input->type}=> $result['data']['id'],
                    
                );
                
                if($result['data']['extension']=='.xml')
                {
                    $valid_xml = Factura::ValidXML($result['data']['id'],array('total'));
                    
                    if($valid_xml['status'])
                    {
                        $result['message'] = $valid_xml['messages'];
                        $data['xml_uuid']  = $valid_xml['data']['UUID'];
                        $data['total']     = $valid_xml['data']['total'];
                        $result['data']['total'] = $valid_xml['data']['total'];
                    }
                    
                    $data['xml']      = $result['data']['id'];
                    $data['messages'] = json_encode($valid_xml['messages']);
                    
                    
                }
                else
                {
                    $data['pdf'] = $result['data']['id'];
                }
                
                if(!$input['id_factura'])
                {
                    $this->db->set($data)->insert('apoyo_facturas');
                    $result['data']['id_factura'] = $this->db->insert_id();
                }
                else
                {
                    $this->db->where('id',$input['id_factura'])->set($data)->update('apoyo_facturas');
                }
                
                
            }
            
        }
        else
        {
            $result['message'] = lang('files:no_folders_wysiwyg');
            $result['status']  = false;
        }
        
        echo  json_encode($result);
        exit();
    }
    
    function download($anio='')
    {
        $this->load->helper('fondo/fondo');
        $base_where = array(
        
            
        );
        
        
        $centro      = $this->input->get('centro');
        $concepto    = $this->input->get('concepto');
        $fecha_ini   = $this->input->get('fecha_ini');
        $fecha_fin   = $this->input->get('fecha_fin');
        
        
        if($anio)
        {
            $base_where['YEAR(fecha_deposito)'] = $anio;
        }
        if($centro)
        {
            $base_where['centros.id'] = $centro;
        }
        
        if($concepto)
        {
            $base_where['depositos.concepto'] = urldecode($concepto);
        }
        if($fecha_ini && $fecha_fin)
        {
            $base_where['fecha_deposito BETWEEN \''.$fecha_ini.'\' AND \''.$fecha_fin.'\' '] = null;
        }
        
        
        
        $apoyos_bd = $this->db->select('*, depositos.id AS id_deposito,depositos.tipo AS tipo_pago,directores.id AS id_director, centros.nombre AS nombre_centro, directores.nombre AS nombre_director,apoyos.id AS id_apoyo')
             ->join('apoyos','apoyos.id_deposito=depositos.id')
             ->join('directores','directores.id=depositos.id_director')
             ->join('centros','centros.id=directores.id_centro')
             ->order_by('concepto,fecha_deposito','DESC')
             ->where($base_where)
             //->or_where($base_where1)
             ->get('depositos')->result();
             
        foreach($apoyos_bd as &$apoyo)
        {
            $comprobado = $this->db->select('SUM(total) AS suma')->where('id_apoyo',$apoyo->id_apoyo)
                                ->get('apoyo_facturas')->row();
                                
            $apoyo->comprobado = ($comprobado)?($comprobado->suma>$apoyo->importe?$apoyo->importe:$comprobado->suma):0;
            //$apoyo->comprobado = ($comprobado)?$comprobado->suma:0;
        }
       
             
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
      
        
        
        $this->load->library('Factory');
        
        $this->excel = factory::getTemplate('apoyos.xlsx');
        
        
        $this->excel->getProperties()->setCreator("Colegio de Bachilleres del Estado de Campeche")
							 ->setLastModifiedBy("Colegio de Bachilleres del Estado de Campeche")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
                             
        $inc = 0;
        $extra = 3;
        foreach($apoyos_bd as $row)
        {
                $this->excel->getActiveSheet()->insertNewRowBefore($inc+$extra,1);
                $this->excel->getActiveSheet()->setCellValue('A'.($inc+$extra),($row->tipo=='Plantel'?'P':'CE').pref_centro($row->clave,''));
                $this->excel->getActiveSheet()->setCellValue('B'.($inc+$extra), $row->nombre_centro);
                $this->excel->getActiveSheet()->setCellValue('C'.($inc+$extra), $row->nombre_director);
                $this->excel->getActiveSheet()->setCellValue('D'.($inc+$extra), format_date_calendar($row->fecha_deposito));
                $this->excel->getActiveSheet()->setCellValue('E'.($inc+$extra), $row->banco);
                $this->excel->getActiveSheet()->setCellValue('F'.($inc+$extra), ' '.$row->no_tarjeta);
                $this->excel->getActiveSheet()->setCellValue('G'.($inc+$extra), number_format($row->importe,2,'.',''));
                $this->excel->getActiveSheet()->setCellValue('H'.($inc+$extra), number_format($row->comprobado,2,'.',''));
                
                $this->excel->getActiveSheet()->setCellValue('I'.($inc+$extra), number_format($row->importe-$row->comprobado,2,'.',''));
                //$this->excel->getActiveSheet()->setCellValue('H'.($inc+$extra), $row['tipo']=='fondo'?'Fondo Revolvente':'Apoyo');
                $this->excel->getActiveSheet()->setCellValue('J'.($inc+$extra), $row->concepto);
                
                $inc++;
        }
        
        $this->excel->getActiveSheet()->removeRow(2,1);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Apoyos_'.now().'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    function report()
    {
        $base_where = array();
        $status     = false;
        $data       = array();
        $fecha_ini  = $this->input->post('fecha_ini');
        $fecha_fin  = $this->input->post('fecha_fin');
        
        if($fecha_ini && $fecha_fin)
        {
            $base_where['fecha_deposito BETWEEN \''.$fecha_ini.'\' AND \''.$fecha_fin.'\' '] = null;
        }
        
       
        $this->form_validation->set_rules($this->validation_rules['report']);
        if ($this->form_validation->run())
        {
        
            $status = true;
            $apoyos_bd = $this->db->select('*, depositos.id AS id_deposito,depositos.tipo AS tipo_pago,directores.id AS id_director, centros.nombre AS nombre_centro, directores.nombre AS nombre_director,apoyos.id AS id_apoyo')
             
             ->join('apoyos','apoyos.id_deposito=depositos.id')
             ->join('directores','directores.id=depositos.id_director')
             ->join('centros','centros.id=directores.id_centro')
             ->order_by('concepto,fecha_deposito','DESC')
             ->where($base_where)            
             ->get('depositos')->result();
             
             foreach($apoyos_bd as $apoyo)
             {
                 $comprobado = $this->db->select('SUM(total) AS suma')->where('id_apoyo',$apoyo->id_apoyo)
                                    ->get('apoyo_facturas')->row();
                                    
                
                $apoyo->comprobado = ($comprobado)?($comprobado->suma>$apoyo->importe?$apoyo->importe:$comprobado->suma):0;
                $data[$apoyo->concepto][]=$apoyo;
            }
        }
        
        
        $centros = $this->db->get('centros')->result();
        $this->template->title($this->module_details['name'],lang('apoyos:report'))
                        ->append_js('module::apoyo.controller.js')
                        ->set('centros',array_for_select($centros,'id','nombre'))
                        ->set('data',$data)
                        ->set('status',$status)
                        ->build('admin/report');
    }
    function delete($anio,$id)
    {
        $apoyo = $this->apoyos_m->get($id);
        
        if($apoyo)
        {
            $facturas = $this->db->where('id_apoyo',$apoyo->id)->get('apoyo_facturas')->result();
            
            if($facturas)
            {
                foreach($facturas as $factura)
                {
                   /*$factura->xml AND Files::delete_file($factura->xml);
                   $factura->pdf AND Files::delete_file($factura->pdf);
                   
                   $this->db->where('id',$factura->id)
                                ->delete('apoyo_facturas');*/
                                
                   $this->remove_factura($factura->id);
                }
                
            }
            $this->apoyos_m->delete($apoyo->id);
            
            $this->session->set_flashdata('success',lang('apoyos:delete_success'));
        }
        else
        {
            $this->session->set_flashdata('error',lang('global:delete_error'));
        }
        redirect('admin/apoyos/'.$anio);
    }
    function remove_factura($id='')
    {
        
         $id = $id?$id:$this->input->get('id_factura');
         $factura = $this->db->where('id',$id)
                        ->get('apoyo_facturas')->row();
         $response = array(
         
            'status'  => false,
            'message' => ''
         );               
                        
        if($factura)
        {
            $factura->xml AND Files::delete_file($factura->xml);
            $factura->pdf AND Files::delete_file($factura->pdf);
            
            $this->db->where('id',$this->input->get('id_factura'))->delete('apoyo_facturas');
            $response['status']  = true;
            $response['message'] = lang('global:delete_success'); 
        }
        if( $this->input->is_ajax_request())
        {
            echo  json_encode($response);
            exit();
        }
        
    }


   

       
 }


 ?>