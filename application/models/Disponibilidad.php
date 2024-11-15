<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Disponibilidad class
 */

class Disponibilidad extends CI_Model
{
	/*
	Determines if a given Disponibilidad_id is an Disponibilidad
	*/
	public function exists($id_disponibilidad)
	{
		$this->db->from('disponibilidad');
		$this->db->where('id_disponibilidad', $id_disponibilidad);

		return ($this->db->get()->num_rows() == 1);
	}

	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('disponibilidad');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'referencia_articulo', 'asc', TRUE);
	}

	/*
	Searches disponibilidad
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'id_disponibilidad', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if ($count_only == TRUE) {
			$this->db->select('COUNT(id_disponibilidad) as count');
		}

		$this->db->from('disponibilidad');

		// get_found_rows case
		if ($count_only == TRUE) {
			return $this->db->get()->row()->count;
		}

		$this->db->order_by($sort, $order);

		if ($rows > 0) {
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	Gets information about a particular disponibilidad
	*/
	public function get_info($id_disponibilidad)
	{
		$this->db->from('disponibilidad');
		$this->db->where('id_disponibilidad', $id_disponibilidad);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			$disponibilidad_obj = new stdClass();
			foreach ($this->db->list_fields('disponibilidad') as $field) {
				$disponibilidad_obj->$field = '';
			}
			return $disponibilidad_obj;
		}
	}

	public function get_bydata($fechahora, $id_cancha)
	{
		$this->db->from('disponibilidad');
		$this->db->where('fechahora_inicio', $fechahora);
		$this->db->where('id_cancha', $id_cancha);
		$this->db->limit(1);
		$query = $this->db->get();
		$a = $query->row();
		$sql = $this->db->last_query();
		return $a;
	}

	//permite obtener la disponibilidad por el codigo de la venta
	public function getDisponibilidadBySaleId($sale_id)
	{
		$this->db->from('disponibilidad');
		$this->db->where('sale_id', $sale_id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row();
	}

	/*
	Inserts or updates an disponibilidad
	*/
	public function save(&$disponibilidad_data, $id_disponibilidad = FALSE)
	{
		if (!$id_disponibilidad == -1 || !$this->exists($id_disponibilidad)) {
			if ($this->db->insert('disponibilidad', $disponibilidad_data)) {
				$disponibilidad_data['id_disponibilidad'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('id_disponibilidad', $id_disponibilidad);

		return $this->db->update('disponibilidad', $disponibilidad_data);
	}

	public function delete($fechahora_inicio, $id_cancha)
	{
		return $this->db->query("delete from qc_disponibilidad WHERE fechahora_inicio = " . $fechahora_inicio . " AND id_cancha = " . $id_cancha . ";");
	}
	public function borradisptarifa($hora, $idcancha)
	{
		$sql = "delete from qc_disponibilidad WHERE fechahora_inicio LIKE '% " . $hora . "' AND id_cancha = " . $$idcancha . " AND estado='DISPONIBLE';";
		if ($this->db->query($sql))
			return TRUE;
		else
			return FALSE;
	}
	public function minmaxhoradisp($idcancha)
	{
		$this->db->select("MIN(DATE_FORMAT(fechahora_inicio, '%H')) AS minima, MAX(DATE_FORMAT(fechahora_inicio, '%H')) AS maxima");
		$this->db->from('disponibilidad');
		$this->db->where('id_cancha', $idcancha);
		$query = $this->db->get();
		return $query->row();
	}

	public function quitardiasanteriores()
	{
		//-----------------------
		$timestampActual = time();
		$timestampAntes = strtotime('-1 hour', $timestampActual);
		//-----------------------
		return $this->db->query("delete from qc_disponibilidad where fechahora_inicio<'" . date("Y-m-d H:00:00", $timestampAntes) . "' AND (estado='DISPONIBLE' OR estado='SOLICITADA');");
	}

	public function bloquear($idcancha, $fechahora)
	{
		return $this->db->query("UPDATE qc_disponibilidad SET estado='BLOQUEADA' WHERE id_cancha=" . $idcancha . " AND fechahora_inicio='" . $fechahora . "'");
	}
	public function desbloquear($idcancha, $fechahora)
	{
		return $this->db->query("UPDATE qc_disponibilidad SET estado='DISPONIBLE' WHERE id_cancha=" . $idcancha . " AND fechahora_inicio='" . $fechahora . "'");
	}

	public function actualizaestado($iddisponibilidad, $estado = "DISPONIBLE")
	{
		return $this->db->query("UPDATE qc_disponibilidad SET estado='" . $estado . "' WHERE id_disponibilidad=" . $iddisponibilidad . ";");
	}

	public function guardar_reserva($datos_reserva,$contract_id=-1)
	{
		/*### AQUI EMPIEZA A GUARDAR EN CADA TABLA LA VENTA SUSPENDIDA*/

		$sales_data = array(
			'sale_time' => date("Y-m-d H:i:s"),
			'customer_id' => $datos_reserva['customer_id'],
			'employee_id' => $datos_reserva['employee_id'],
			'comment' => '',
			'sale_status' => 1,
			'invoice_number' => NULL,
			'quote_number' => NULL,
			'work_order_number' => NULL,
			'dinner_table_id' => 2,
			'sale_type' => 0,

		);

		$sale_id = -1;

		// Ejecute estas consultas como una transacción, queremos asegurarnos de hacer todo o nada
		$this->db->trans_start();

		if ($this->db->insert('sales', $sales_data)) {
			$sale_id = $this->db->insert_id();
			$item_data = array(
				'sale_id' => $sale_id,
				'item_id' => $datos_reserva['item_id'],
				'line' => 1,
				'description' => date("Y-m-d H:i a", strtotime($datos_reserva['fechahora_reserva'])),
				'serialnumber' => '',
				'quantity_purchased' => 1,
				'discount' => 0,
				'discount_type' => 0,
				'item_cost_price' => 0,
				'item_unit_price' => $datos_reserva['tarifa_cancha'],
				'item_location' => 1,
				'print_option' => 0,
			);
			$this->db->insert('sales_items', $item_data);

			//AHORA ACTUALIZAMOS LA DISPONIBILIDAD
			$this->db->query("UPDATE qc_disponibilidad SET contract_id=" . $contract_id . " ,estado='RESERVADA', sale_id=" . $sale_id . " WHERE id_disponibilidad=" . $datos_reserva['id_disponibilidad'] . ";");
		}


		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return -1;
		}
		return $sale_id;
	}

	public function getContractId(){
		//obtenemos el id del contrato
		$contract_data = array("status"=>1);
		$this->db->insert('contracts',$contract_data);
		$contract_id = $this->db->insert_id();
		return $contract_id;
	}

	public function get_info_reserva_by_disponibilidad($id_disponibilidad)
	{
		$sql = "SELECT S.customer_id, D.fechahora_inicio, S.sale_id, CONCAT(P1.first_name,' ',P1.last_name) AS cliente, P1.phone_number AS telefono_cliente, CONCAT(P2.first_name,' ',P2.last_name) AS empleado,
		(SELECT IFNULL(SUM(SP.payment_amount),0) FROM " . $this->db->dbprefix('sales_payments') . " AS SP WHERE SP.sale_id=S.sale_id) AS abonado
		FROM " . $this->db->dbprefix('disponibilidad') . " AS D		 
		JOIN " . $this->db->dbprefix('sales') . " AS S ON D.sale_id=S.sale_id
		JOIN " . $this->db->dbprefix('people') . " AS P1 ON P1.person_id=S.customer_id
		JOIN " . $this->db->dbprefix('people') . " AS P2 ON P2.person_id=S.employee_id
		WHERE D.id_disponibilidad='" . $id_disponibilidad . "'";
		$query = $this->db->query($sql);
		return $query->result_array();
		//return $sql;
	}

	public function set_estado_disponibilidad_by_sale($sale_id, $estado)
	{
		$sql = "UPDATE qc_disponibilidad AS D	SET D.estado='" . $estado . "'	WHERE D.sale_id='" . $sale_id . "'";
		return $this->db->query($sql);
	}

	public function convertFecha($timestamp)
	{
		$dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
		$meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		// Obtener los componentes de la fecha
		$día = date('w', $timestamp); // Día de la semana como número (0 para domingo)
		$fecha_día = date('j', $timestamp); // Día del mes
		$mes = date('n', $timestamp); // Mes como número (1 para enero)
		$año = date('Y', $timestamp); // Año

		// Formatear la fecha en español
		$fecha = $dias[$día] . ', ' . $fecha_día . ' de ' . $meses[$mes - 1] . ' de ' . $año;

		echo $fecha; // Salida: martes, 3 de septiembre de 2024
	}

	public function convertDay($timestamp)
	{
		$daysOfWeek = [
			'Monday'    => 'Lunes',
			'Tuesday'   => 'Martes',
			'Wednesday' => 'Miércoles',
			'Thursday'  => 'Jueves',
			'Friday'    => 'Viernes',
			'Saturday'  => 'Sábado',
			'Sunday'    => 'Domingo'
		];

		// Texto original con días de la semana en inglés
		$text = 'I will meet you on Monday and Friday.';

		// Reemplazar los días de la semana
		$translatedText = str_replace(array_keys($daysOfWeek), array_values($daysOfWeek), $timestamp . "");

		echo $translatedText;
	}

	public function saveContract($person_id, $reservas)
	{
		$item_data = array(
			'person_id' => $person_id,
			'reservas' => json_encode($reservas),
			'status' => 1
		);
		$this->db->insert('contracts', $item_data);
	}

	function do_reserva($person_id, $id_disponibilidad, $contract_id)
	{
		$datos_disponibilidad = $this->get_info($id_disponibilidad);
		$referenciarticulo = $datos_disponibilidad->referencia_articulo;
		$idprimeraventa = 0;
		$reserva_data = array(
			'id_disponibilidad' => $datos_disponibilidad->id_disponibilidad,
			'customer_id' => $person_id,
			'employee_id' => $this->Employee->get_logged_in_employee_info()->person_id,
			'item_id' => $this->Item->get_info_by_id_or_number($referenciarticulo)->item_id,
			'fechahora_reserva' => $datos_disponibilidad->fechahora_inicio,
			'tarifa_cancha' => $datos_disponibilidad->tarifa_cancha,
			'contract_id' => $contract_id
		);
		$sale_suspend_id = $this->guardar_reserva($reserva_data,$contract_id);
		$idprimeraventa = $sale_suspend_id;

		if ($sale_suspend_id > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getNewDisponibilidad($fechahora_inicio, $id_cancha)
	{
		$fechaActual = new DateTime($fechahora_inicio);
		$fechaSiguienteWeek = clone $fechaActual;
		$fechaSiguienteWeek->modify('+1 week');
		$fecha_inicio_24 = $fechaSiguienteWeek->format('Y-m-d H:i:s');
		$fecha_inicio_12 = $fechaSiguienteWeek->format('Y-m-d h:i:s A');
		$id_cancha = $id_cancha;
		$disponibilidad_new = $this->Disponibilidad->get_bydata($fecha_inicio_24, $id_cancha);
		return array('disponibilidad_new' => $disponibilidad_new, 'fechahora_inicio' => $fecha_inicio_12);
	}
}
