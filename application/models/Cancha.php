<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cancha class
 */

class Cancha extends CI_Model
{
	/*
	Determines if a given Cancha_id is an Cancha
	*/
	public function exists($id_cancha)
	{
		$this->db->from('canchas');
		$this->db->where('id_cancha', $id_cancha);
		
		return ($this->db->get()->num_rows() == 1);
	}

	public function get_all($limit = 10000, $offset = 0, $sort = 'id_cancha', $order = 'asc')
	{
		$this->db->from('canchas');
		$this->db->where('estado', 1);
		$this->db->order_by($sort, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	public function get_canchas(){
		$this->db->from('canchas');
		$this->db->where('estado', 1);
		$res = $this->db->get()->result();
		return $res;
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'nombre_cancha', 'asc', TRUE);
	}

	/*
	Searches canchas
	*/
	public function search($search='', $rows = 0, $limit_from = 0, $sort = 'id_cancha', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id_cancha) as count');
		}

		$this->db->from('canchas');
		$this->db->where('estado', 1);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	Gets information about a particular cancha
	*/
	public function get_info($id_cancha)
	{	
		$this->db->from('canchas');
		$this->db->where('id_cancha', $id_cancha);		
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			$cancha_obj = new stdClass();
			foreach($this->db->list_fields('canchas') as $field)
			{
				$cancha_obj->$field = '';
			}
			return $cancha_obj;
		}
	}
	public function get_info_by_referencia($referencia)
	{	
		$this->db->from('canchas');
		$this->db->where('referencia_articulo', $referencia);		
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			$cancha_obj = new stdClass();
			foreach($this->db->list_fields('canchas') as $field)
			{
				$cancha_obj->$field = '';
			}
			return $cancha_obj;
		}
	}
	
	/*
	Gets information about multiple employees
	*/
	public function get_multiple_info($id_canchas)
	{
		$this->db->from('canchas');
		$this->db->where_in('id_cancha', $id_canchas);
		return $this->db->get();
	}

	/*
	Inserts or updates an cancha
	*/
	public function save(&$cancha_data, $id_cancha)
	{
		if($id_cancha == -1 || !$this->exists($id_cancha))
		{
			if($this->db->insert('canchas', $cancha_data))
			{
				$cancha_data['id_cancha'] = $this->db->insert_id();

				return TRUE;
			}
			return FALSE;
		}

		$this->db->where('id_cancha', $id_cancha);

		return $this->db->update('canchas', $cancha_data);
	}

	/*
	Deletes a list of canchas
	*/
	public function delete_list($id_canchas)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing

		$this->db->trans_start();
			$this->db->where_in('id_cancha', $id_canchas);
			$success = $this->db->update('canchas', array('estado'=>0));
		$this->db->trans_complete();

		return $success;
	}
	
	## cambio MARIO LARRAÑAGA funciones para tarifas
	public function guardartarifa($tarifa_data, $id_tarifa=FALSE)
	{
		if(!$id_tarifa == -1 || !$this->exists($id_tarifa))
		{
			if($this->db->insert('cancha', $tarifa_data))
			{
				$tarifa_data['id_tarifa'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('id_tarifa', $id_tarifa);

		return $this->db->update('cancha', $tarifa_data);
	}

	public function guardar_tarifas($canchas,$selected_hours,$selected_days,$value_fee){
		$this->db->trans_start();
		$success = false;
		foreach($canchas as $cancha){
			foreach($selected_hours as $hora){
				$this->db->from('tarifascancha');
				$this->db->where('id_cancha',$cancha);
				$this->db->where('hora',$hora.":00");
				$result = $this->db->get()->result()[0];
				foreach($selected_days as $day){
					if($this->existTarifa($cancha,$hora)){
						$tarifa_data = array(
							$day=>$value_fee
						);
						$this->db->where('hora',$hora.":00");
						$this->db->where('id_cancha',$cancha);
						$this->db->update('tarifascancha',$tarifa_data);
					}
					else
					{
						$tarifa_data = array(
							'id_cancha' => $cancha,
							'hora' => $hora . ":00",
							$day=>$value_fee
						);
						// Insertar el nuevo registro
						$this->db->insert('tarifascancha', $tarifa_data);
					}


				}
			}
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            $success = false;
        } else {
            $success = true;
        }
		return $success;
	}
	
	function existTarifa($cancha,$hora)
	{
		$this->db->from('tarifascancha');
    	$this->db->where('id_cancha', $cancha);
    	$this->db->where('hora', $hora . ":00");
    	$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return true;
		}
		else
		{
			return false;
		}
	}


	public function search_tarifas($id_cancha=-1,$search='', $rows = 0, $limit_from = 0, $sort = 'id_cancha, hora', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id_tarifa) as count');
		}

		$this->db->from('tarifascancha');
		if($id_cancha>0)
			$this->db->where('id_cancha', $id_cancha);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}
		
		return $this->db->get();

	}
	
	public function get_found_rows_tarifas($id_cancha,$search)
	{
		return $this->search_tarifas($id_cancha,$search, 0, 0, 'hora', 'asc', TRUE);
	}
	
	public function get_tarifas_cancha($id_cancha='')
	{
		$this->db->from('tarifascancha');
		if($id_cancha!='')
			$this->db->where('id_cancha', $id_cancha);
		return $this->db->get()->result_array();
	}

	public function get_tipo_canchas(){
		$this->db->from('tipocanchas');
		return $this->db->get()->result_array();
	}
}
?>
