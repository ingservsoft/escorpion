<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Inventory_kardex extends Report
{
	public function getDataColumns()
	{
		return array(array('item_codigolocal' => $this->lang->line('reports_item_codigolocal')),
					array('item_name' => $this->lang->line('reports_item_name')),
					array('item_number' => $this->lang->line('reports_item_number')),
					array('stock_ini' => $this->lang->line('reports_stock_ini')),
					array('entradas' => $this->lang->line('reports_entradas')),
					array('salidas' => $this->lang->line('reports_salidas')),
					array('ajuste' => $this->lang->line('reports_ajuste')),
					array('stock_fin' => $this->lang->line('reports_stock_fin')));
	}

	public function getData(array $inputs)
	{
		$inputs['start_date']=strpos(" ",$inputs['start_date'])===false?$inputs['start_date'].' 00:00:00':$inputs['start_date'];
		$inputs['end_date']=strpos(" ",$inputs['end_date'])===false?$inputs['end_date'].' 23:59:59':$inputs['end_date'];
		
		$query = $this->db->query("SELECT I.item_codigolocal, I.name, I.item_number, I.category, IQ.quantity AS stockactual,
		(SELECT SUM(INV.trans_inventory) FROM qc_inventory AS INV WHERE INV.trans_items=I.item_id AND INV.trans_date<".$this->db->escape(rawurldecode($inputs['start_date'])).") AS stock_ini,
		(SELECT SUM(IV.trans_inventory) FROM qc_inventory AS IV WHERE IV.trans_items=I.item_id AND IV.trans_comment LIKE 'RECV%' AND IV.trans_date BETWEEN " . $this->db->escape(rawurldecode($inputs['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($inputs['end_date'])).") AS entradas,
		(SELECT ABS(SUM(IV2.trans_inventory)) FROM qc_inventory AS IV2 WHERE IV2.trans_items=I.item_id AND IV2.trans_comment LIKE 'POS%' AND IV2.trans_date BETWEEN " . $this->db->escape(rawurldecode($inputs['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($inputs['end_date'])).") AS salidas,
		(SELECT SUM(IV3.trans_inventory) FROM qc_inventory AS IV3 WHERE IV3.trans_items=I.item_id AND IV3.trans_comment NOT LIKE 'POS%' AND IV3.trans_comment NOT LIKE 'RECV%' AND IV3.trans_date BETWEEN " . $this->db->escape(rawurldecode($inputs['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($inputs['end_date'])).") AS ajuste
		FROM qc_items AS I
		JOIN qc_item_quantities AS IQ ON IQ.item_id=I.item_id
		WHERE I.stock_type=0
		GROUP BY I.item_id
		ORDER BY I.name ASC");
		return $query->result_array();
	}

	/**
	 * calculates the total value of the given inventory summary by summing all sub_total_values (see Inventory_summary::getData())
	 *
	 * @param array $inputs expects the reports-data-array which Inventory_summary::getData() returns
	 * @return array
	 */
	public function getSummaryData(array $inputs)
	{
		$return = array('total_inventory_value' => 0, 'total_quantity' => 0, 'total_low_sell_quantity' => 0, 'total_retail' => 0);

		foreach($inputs as $input)
		{
			$return['total_inventory_value'] += $input['sub_total_value'];
			$return['total_quantity'] += $input['quantity'];
			$return['total_low_sell_quantity'] += $input['low_sell_quantity'];
			$return['total_retail'] += $input['unit_price'] * $input['quantity'];
		}

		return $return;
	}

	/**
	 * returns the array for the dropdown-element item-count in the form for the inventory summary-report
	 *
	 * @return array
	 */
	public function getItemCountDropdownArray()
	{
		return array('all' => $this->lang->line('reports_all'),
					'zero_and_less' => $this->lang->line('reports_zero_and_less'),
					'more_than_zero' => $this->lang->line('reports_more_than_zero'));
	}
}
?>
