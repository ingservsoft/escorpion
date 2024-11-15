<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
	@media (min-width: 768px) {
		.modal-dlg .modal-dialog {
			width: 750px !important;
		}
	}
</style>
<?php
if ($accion == "completar_venta") {
	$variable_monto = "Cambio";
} else {
	$variable_monto = "Abono";
}
?>



<h5 style="text-align: center;">¿ Desea Trasladar el <b><?php echo $variable_monto; ?></b> de <b><?php echo to_currency($abono); ?> </b>a una nueva reserva ?</h5>
<table id="suspended_sales_table" class="table table-striped table-hover">
	<thead>
		<tr bgcolor="#CCC">
			<th></th>
			<th>ID</th>
			<th><?php echo $this->lang->line('sales_date'); ?></th>
			<?php
			/*if($this->config->item('dinner_table_enable') == TRUE)
			{*/
			?>
			<!--th><?php echo $this->lang->line('sales_table'); ?></th-->
			<th><?php echo $this->lang->line('sales_cancha'); ?></th>
			<?php
			/*}*/
			?>
			<th><?php echo $this->lang->line('sales_customer'); ?></th>
			<th><?php echo $this->lang->line('sales_employee'); ?></th>
			<th><?php echo $this->lang->line('sales_total'); ?></th>
			<th>Abonos</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total = 0;
		$id_customer = 0;
		foreach ($suspended_sales as $suspended_sale) {
			$sale_info = $this->Sale->get_info($suspended_sale['doc_id'])->row_array();
			if ($suspended_sale['dinner_table_id'] == 1)
				$clasefiltro = 'tienda';
			elseif ($suspended_sale['dinner_table_id'] == 2)
				$clasefiltro = 'alquiler';
			else
				$clasefiltro = 'otro';
		?>
			<tr class="<?php echo $clasefiltro; ?>">
				<td><input type="radio" name="option" value="<?php echo $suspended_sale['doc_id']; ?>" /></td>
				<td><?php echo $suspended_sale['doc_id']; ?></td>
				<!--td><?php echo date($this->config->item('dateformat'), strtotime($suspended_sale['date_reservation'])); ?></td-->
				<td><?php echo date('Y-m-d h:i A', strtotime($suspended_sale['date_reservation'])); ?></td>
				<?php
				/*if($this->config->item('dinner_table_enable') == TRUE)
				{
				*/ ?>
				<!--td><?php echo $this->Dinner_table->get_name($suspended_sale['dinner_table_id']); ?></td-->
				<td><?php echo $suspended_sale['cancha']; ?></td>
				<?php
				/*}*/
				?>
				<td>
					<?php
					if (isset($suspended_sale['customer_id'])) {
						$id_customer = $suspended_sale['customer_id'];
						$customer = $this->Customer->get_info($suspended_sale['customer_id']);
						echo $customer->first_name . ' ' . $customer->last_name;
					} else {
					?>
						&nbsp;
					<?php
					}
					?>
				</td>
				<td>
					<?php
					if (isset($suspended_sale['employee_id'])) {
						$employee = $this->Employee->get_info($suspended_sale['employee_id']);
						echo $employee->first_name . ' ' . $employee->last_name;
					} else {
					?>
						&nbsp;
					<?php
					}
					?>
				</td>
				<td class="subtotal"><?php echo to_currency($sale_info['amount_due']); ?></td>
				<td class="subtotal">
					<?php echo to_currency($this->Sale->getTotPaymentsSale($suspended_sale['doc_id'])); ?></td>
			</tr>
		<?php
			$total += $sale_info['amount_due'];
		}
		?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
<?php
if (isset($last_reserva_contratada)) {
	$id_cancha = $last_reserva_contratada['id_cancha'];
	$disponibilidad  = $this->Disponibilidad->getNewDisponibilidad($last_reserva_contratada['fechahora_inicio'], $id_cancha);
    if($accion == 'completar_venta'){
	if($disponibilidad['disponibilidad_new']->estado == 'DISPONIBLE'){
		?>
				<input type="checkbox" id="renew" name="renew" value="1" /> ¿Desea reservar una semana mas en la fecha <?php echo $disponibilidad['fechahora_inicio'];   ?> ?
		<?php
	}
	else{
		 echo "La Próxima fecha ".$disponibilidad['fechahora_inicio']." no esta disponible";
		 echo "<input type='checkbox' id='renew' name='renew' value='0' style='visibility:hidden'/>";
	}
}
else
{
	echo "<input type='checkbox' id='renew' name='renew' value='0' style='visibility:hidden'/>";
}

?>


<?php
}
?>
<div id="resultado_total" style="font-weight:bold; text-align:right; padding-bottom:40px; margin-right:100px; margin-top:-20px">Total:&nbsp;<?php echo to_currency($total); ?></div>
<div class='btn btn-sm btn-info' onclick="move_abono()"><span class="glyphicon glyphicon-send">&nbsp</span>Si, Trasladar <?php echo $variable_monto; ?></div>
<?php
if ($accion == "completar_venta") {
?>
	<div class='btn btn-sm btn-warning' onclick="do_reserva()"><span class="glyphicon glyphicon-retweet">&nbsp</span>No, Solo efectuar cambio</div>
<?php
}
?>

<script>

function do_reserva() {
	var checkRenew = document.getElementById('renew');
		if (checkRenew.checked){
			var accion = '<?php echo $accion; ?>';
			$.ajax({
				url: "<?php echo site_url('sales/renewReserva'); ?>",
				type: 'POST',
				data: {
					disponibilidad_new: '<?php echo $disponibilidad['disponibilidad_new']->id_disponibilidad; ?>',
					person_id: '<?php echo $id_customer; ?>',
					sale_id:'<?php echo $sale_old;?>'
				},
				success: function(response) {
					console.log(response);
					completar_venta();

				},
				error: function(xhr, status, error) {
					// Manejar errores aquí
					console.log(JSON.stringify(status));
				}
			});
		}
		else
		{
			completar_venta();
		}

	}

	/*cambio carlos chaucanes*/
	function move_abono() {
		var accion = '<?php echo $accion; ?>';
		var option = document.querySelector("input[name='option']:checked");
		var checkRenew = document.getElementById('renew');
		var new_disponibilidad = 0;
		if (checkRenew.checked) {
			new_disponibilidad = '<?php echo $disponibilidad['disponibilidad_new']->id_disponibilidad; ?>';
		}
		if (option) {
			if (confirm("<?php echo $this->lang->line('sales_move_confirm'); ?>")) {
				$.ajax({
					url: "<?php echo site_url('sales/move_abono'); ?>",
					type: 'POST',
					data: {
						sale_old: <?php echo $sale_old; ?>,
						sale_new: option.value,
						abono: <?php echo $abono; ?>,
						accion: accion,
						customer_id: <?php echo $id_customer; ?>,
						estado_sale: <?php echo $estado_sale; ?>,
						new_disponibilidad: new_disponibilidad
					},
					success: function(response) {
						var res = parseInt(response);
						if (res > 0) {
							if (accion == 'cancelar_venta') {
								cancelar_venta();
							} else if (accion == 'completar_venta') {
								completar_venta();
							} else {
								var cerrar = document.getElementById('close');
								cerrar.click();
								window.location.reload();
							}

						}
					},
					error: function(xhr, status, error) {
						// Manejar errores aquí
						console.log(JSON.stringify(status));
					}
				});
			}
		} else {
			alert("<?php echo $this->lang->line('sales_abono_translate_message'); ?>");
		}


	}

	function completar_venta() {
		$('#buttons_form').attr('action', "<?php echo site_url($controller_name . "/complete"); ?>");
		$('#buttons_form').submit();
	}

	function cancelar_venta() {
		$('#buttons_form').attr('action', "<?php echo site_url("sales/cancel"); ?>");
		$('#buttons_form').submit();
	}



	jQuery("#buscador").keyup(function() {
		if (jQuery(this).val() != "") {
			jQuery("#suspended_sales_table tbody>tr").hide();
			jQuery("#suspended_sales_table td:contiene-palabra('" + jQuery(this).val() + "')").parent("tr").show();

		} else {
			jQuery("#suspended_sales_table tbody>tr").show();
		}
		calculaTotal();
	});

	jQuery.extend(jQuery.expr[":"], {
		"contiene-palabra": function(elem, i, match, array) {
			return (elem.textContent || elem.innerText || jQuery(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});

	function calculaTotal() {
		var sum = 0;
		var valor = '';
		$('.subtotal').each(function() {
			if ($(this).is(':visible')) {
				valor = $(this).html().replace(".", '');
				valor = valor.replace('$', '').trim();
				valor = valor.replace('&nbsp;', '');
				sum += parseFloat(valor, 10);
			}
		});
		$('#resultado_total').html('Total:  ' + format(sum.toFixed(2)));
	}
	var format = function(num) {
		var str = num.toString().replace("$", "").trim(),
			parts = false,
			output = [],
			i = 1,
			formatted = null;
		if (str.indexOf(".") > 0) {
			parts = str.split(".");
			str = parts[0];
		}
		str = str.split("").reverse();
		for (var j = 0, len = str.length; j < len; j++) {
			if (str[j] != ",") {
				output.push(str[j]);
				if (i % 3 == 0 && j < (len - 1)) {
					output.push(".");
				}
				i++;
			}
		}
		formatted = output.reverse().join("");
		return ("$" + formatted);
	};
</script>