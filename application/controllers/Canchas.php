<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Canchas extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('canchas');
		$this->load->helper('url');
		$this->load->library('upload');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_canchas_manage_table_headers());
		$this->load->view('canchas/manage', $data);
	}
	
	/*
	Returns canchas table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search   = $this->input->get('search');
		$limit    = $this->input->get('limit');
		$offset   = $this->input->get('offset');
		$sort     = $this->input->get('sort');
		$order    = $this->input->get('order');
		
		$canchas = $this->Cancha->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Cancha->get_found_rows($search);
		$data_rows = array();
		foreach($canchas->result() as $cancha)
		{
			$data_rows[] = $this->xss_clean(get_cancha_data_row($cancha));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view($id_cancha = -1)
	{
		$data = array();

		$canchas_info = $this->Cancha->get_info($id_cancha);

		foreach(get_object_vars($canchas_info) as $property => $value)
		{
			$canchas_info->$property = $this->xss_clean($value);
		}
		$data['canchas_info'] = $canchas_info;
		//----------------------------------------
		$tipos = $this->Cancha->get_tipo_canchas();
        // Procesar los datos para el form_dropdown
        $data['tiposcancha'] = array();
        foreach ($tipos as $tipo) {
            $data['tiposcancha'][$tipo['tipocancha']] = $tipo['tipocancha']; // 'id' y 'nombre' son columnas de la tabla
        }
		//----------------------------------------

		$this->load->view("canchas/form", $data);
	}

	public function get_row($row_id)
	{
		$canchas_info = $this->Cancha->get_info($row_id);
		$data_row = $this->xss_clean(get_cancha_data_row($canchas_info));

		echo json_encode($data_row);
	}

	public function save($id_cancha = -1)
	{
		$config['upload_path']   = './images/canchas/';
		$config['allowed_types'] = 'gif|jpg|pdf';
		$config['max_size']      = 2048;
		$config['encrypt_name']  = TRUE;
        $this->upload->initialize($config);
        $res = $this->upload->do_upload('urlfoto_cancha');
		
		$data = $this->upload->data();

		if($res){
			$cancha_data = array(
				'nombre_cancha' => $this->input->post('nombre_cancha'),
				'tipo_cancha' => $this->input->post('tipo_cancha'),
				'descripcion_cancha' => $this->input->post('descripcion_cancha'),
				'urlfoto_cancha' =>isset($data['file_name'])?'images/canchas/'.$data['file_name']:'Noaplica',
				'referencia_articulo' => $this->input->post('referencia_articulo')
			);
		}
		else{
			$cancha_data = array(
				'nombre_cancha' => $this->input->post('nombre_cancha'),
				'tipo_cancha' => $this->input->post('tipo_cancha'),
				'descripcion_cancha' => $this->input->post('descripcion_cancha'),
				'referencia_articulo' => $this->input->post('referencia_articulo')
			);
		}


		if($this->Cancha->save($cancha_data, $id_cancha))
		{
			$cancha_data = $this->xss_clean($cancha_data);

			//New id_cancha
			if($id_cancha == -1)
			{
				//-----------------------------
				$item_data = array(
					'name' => "Alquiler de ".$this->input->post('nombre_cancha'),
					'description' => '',
					'category' => 'CANCHAS',
					'item_type' => 0,
					'stock_type' => 1,
					'item_number' => $this->input->post('referencia_articulo'),
					'is_serialized'=>0,
					'allow_alt_description'=>0
				);
				$this->Item->save($item_data);
				//-----------------------------
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('canchas_successful_adding'), 'id' => $cancha_data['id_cancha']));
			}
			else // Existing Cancha
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('canchas_successful_updating'), 'id' => $id_cancha));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('canchas_error_adding_updating'), 'id' => -1));
		}
		
	


	}



	

	public function delete()
	{
		$canchas_to_delete = $this->input->post('ids');

		if($this->Cancha->delete_list($canchas_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('canchas_successful_deleted') . ' ' . count($canchas_to_delete) . ' ' . $this->lang->line('canchas_one_or_multiple'), 'ids' => $canchas_to_delete));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('canchas_cannot_be_deleted'), 'ids' => $canchas_to_delete));
		}
	}
	
	//funciones para las tarifas
	public function vertarifas($id_cancha = -1)
	{
		$data = array();

		$cancha_info = $this->Cancha->get_info($id_cancha);

		foreach(get_object_vars($cancha_info) as $property => $value)
		{
			$cancha_info->$property = $this->xss_clean($value);
		}
		$data['table_headers'] = $this->xss_clean(get_tarifas_manage_table_headers());
		$data['cancha_info'] = $cancha_info;		
		$data['id_cancha']=$id_cancha;		
		$this->load->view("canchas/tarifas", $data);
	}
	public function listartarifas($id_cancha)
	{
		$search   = $this->input->get('search');
		$limit    = $this->input->get('limit');
		$offset   = $this->input->get('offset');
		$sort     = $this->input->get('sort');
		$order    = $this->input->get('order');
		
		$tarifas = $this->Cancha->search_tarifas($id_cancha,$search, $limit, $offset, $sort, $order);
		$total_rows = $this->Cancha->get_found_rows_tarifas($id_cancha,$search);
		$data_rows = array();
		foreach($tarifas->result() as $tarifa)
		{
			$data_rows[] = $this->xss_clean(get_tarifa_data_row($tarifa));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
		
	}
	
	public function nuevatarifa($id_cancha = -1)
	{
		$data = array();

		$cancha_info = $this->Cancha->get_info($id_cancha);

		foreach(get_object_vars($cancha_info) as $property => $value)
		{
			$cancha_info->$property = $this->xss_clean($value);
		}
		
		$data['cancha_info'] = $cancha_info;		
		$this->load->view("canchas/nuevatarifa", $data);
	}
	public function guardartarifa()
	{
		$selected_hours = $this->input->post('hours');
		$selected_days = $this->input->post('dias');
		$value_fee = $this->input->post("valor_tarifa");
		$canchas = $this->input->post("canchas");

		if(count($canchas)>0){
			if(count($selected_days)>0){
				if(count($selected_hours)>0){
					if(filter_var($value_fee,FILTER_VALIDATE_INT)!=false && intval($value_fee) > 0){
						$succes = $this->Cancha->guardar_tarifas($canchas,$selected_hours,$selected_days,$value_fee);
						if($succes)
						{			
							echo  json_encode(array('success' => TRUE, 'message' => "Se registró satisfactoriamente la tarifa"));
						}
						else // Existing Cancha
						{
							echo  json_encode(array('success' => FALSE, 'message' => "Error al registrar/actualizar la tarifa"));
						}
					}
					else{
						echo  json_encode(array('success' => FALSE, 'message' => "valor inválido"));
					}
				}
				else
				{
					echo  json_encode(array('success' => FALSE, 'message' => "Se debe seleccionar almenos una hora"));
				}
			}
			else
			{
				echo  json_encode(array('success' => FALSE, 'message' => "Se debe seleccionar almenos un día de la semana"));
			}
		}
		else
		{
			echo  json_encode(array('success' => FALSE, 'message' => "Se debe seleccionar almenos una cancha"));
		}

		//$data['table_headers'] = $this->xss_clean(get_canchas_manage_table_headers());
		//$this->load->view('canchas/manage', $data);
	}
		
}
?>
