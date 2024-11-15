<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Summary_report.php");

class Summary_dinnertable extends Summary_report
{
	protected function _get_data_columns()
	{
		$this->db->select("name");
		$this->db->from('dinner_tables');
		$this->db->where('deleted', 0);
		$dinner_tables = $this->db->get()->result_array();
		foreach($dinner_tables as $dinnertable)
		{
			
		}
		return array(
			array('dinner_table' => $this->lang->line('reports_dinnertables')),			
			array('efectivo' => "Efectivo"),
			array('tarjeta_debito' => "Tarjeta Débito"),
			array('tarjeta_credito' => "Tarjeta Crédito"),
			array('credito' => "A Crédito"),
			array('tarjeta_regalo' => "Tarjeta de Regalo"),
			array('total_venta' => "Totales"),
			);
	}

	public function getData(array $inputs)
	{
		$where = '';

		if(empty($this->config->item('date_or_time_format')))
		{
			$where .= 'DATE(sale_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where .= 'sale_time BETWEEN ' . $this->db->escape(rawurldecode($inputs['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($inputs['end_date']));
		}
		
		$sql="SELECT DT.name as dinner_table, 
		(SELECT SUM(SP.`payment_amount`) FROM qc_sales_payments AS SP, qc_sales AS S1 WHERE S1.sale_id=SP.sale_id AND S1.sale_status=".COMPLETED." AND S1.dinner_table_id=DT.dinner_table_id AND ".$where." AND SP.payment_type='Efectivo') AS efectivo,
		(SELECT SUM(SP.`payment_amount`) FROM qc_sales_payments AS SP, qc_sales AS S1 WHERE S1.sale_id=SP.sale_id AND S1.sale_status=".COMPLETED." AND S1.dinner_table_id=DT.dinner_table_id AND ".$where." AND SP.payment_type='Tarjeta de Débito') AS tarjeta_debito,
		(SELECT SUM(SP.`payment_amount`) FROM qc_sales_payments AS SP, qc_sales AS S1 WHERE S1.sale_id=SP.sale_id AND S1.sale_status=".COMPLETED." AND S1.dinner_table_id=DT.dinner_table_id AND ".$where." AND SP.payment_type='Tarjeta de Crédito') AS tarjeta_credito,
		(SELECT SUM(SP.`payment_amount`) FROM qc_sales_payments AS SP, qc_sales AS S1 WHERE S1.sale_id=SP.sale_id AND S1.sale_status=".COMPLETED." AND S1.dinner_table_id=DT.dinner_table_id AND ".$where." AND SP.payment_type='A Crédito') AS credito,
		(SELECT SUM(SP.`payment_amount`) FROM qc_sales_payments AS SP, qc_sales AS S1 WHERE S1.sale_id=SP.sale_id AND S1.sale_status=".COMPLETED." AND S1.dinner_table_id=DT.dinner_table_id AND ".$where." AND SP.payment_type='Tarjeta de Regalo') AS tarjeta_regalo
		FROM `qc_dinner_tables` AS DT 
		WHERE DT.deleted=0";
		
		$resultados=$this->db->query($sql);
		
		return $resultados->result_array();
	}

	
}
?>