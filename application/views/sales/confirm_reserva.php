<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
	@media (min-width: 768px) {
		.modal-dlg .modal-dialog {
			width: 750px !important;
		}
	}
</style>
<?php
if (isset($last_reserva_contratada)) {
	$id_cancha = $last_reserva_contratada['id_cancha'];
	$disponibilidad  = $this->Disponibilidad->getNewDisponibilidad($last_reserva_contratada['fechahora_inicio'], $id_cancha);
	if($accion == 'completar_venta'){
		if($disponibilidad['disponibilidad_new']->estado == 'DISPONIBLE'){
?>
	 	¿Desea reservar una semana mas en la fecha  <?php echo $disponibilidad['fechahora_inicio'];   ?> ?
<?php
		}
		else
		{
			echo "La Próxima fecha ".$disponibilidad['fechahora_inicio']." no esta disponible";
		}
	}
}
?>

<?php
$disp = $this->Disponibilidad->get_bydata(date("Y-m-d", $dia) . ' ' . $hora, $id_cancha);
if ($accion == "completar_venta") {
	if($disponibilidad['disponibilidad_new']->estado == 'DISPONIBLE'){
?>
<div class='btn btn-sm btn-info' onclick="do_reserva()"><span class="glyphicon glyphicon-send">&nbsp</span>Si<?php echo $variable_monto; ?></div>
<div class='btn btn-sm btn-warning' onclick="completar_venta()"><span class="glyphicon glyphicon-retweet">&nbsp</span>No</div>
<?php
   }
   else
   {
	?>
	<div class='btn btn-sm btn-warning' onclick="completar_venta()"><span class="glyphicon glyphicon-retweet">&nbsp</span>Completar Venta</div>
	<?php
   }
}
?>


<script>
	/*cambio carlos chaucanes*/
	function do_reserva() {
		var accion = '<?php echo $accion; ?>';
		$.ajax({
			url: "<?php echo site_url('sales/renewReserva'); ?>",
			type: 'POST',
			data: {
				disponibilidad_new: '<?php echo $disponibilidad['disponibilidad_new']->id_disponibilidad; ?>',
				person_id: '<?php echo $customer_id; ?>',
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

	function completar_venta() {
		$('#buttons_form').attr('action', "<?php echo site_url($controller_name . "/complete"); ?>");
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