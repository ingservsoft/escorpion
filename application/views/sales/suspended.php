<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
@media (min-width: 768px)
{
   .modal-dlg .modal-dialog
	{
		width: 750px !important;
	}
}
</style>
<div id="filtros" style="margin-bottom:1em">
<button id="alquiler" class="btn btn-info btn-xs" onclick="filtrartabla('alquiler')">Reservas</button>
<button id="tienda" class="btn btn-info btn-xs" onclick="filtrartabla('tienda')">Tienda</button>
<button id="otro" class="btn btn-info btn-xs" onclick="filtrartabla('otro')">Otros</button>&nbsp;&nbsp;&nbsp;
<button id="todos" class="btn btn-info btn-xs" onclick="filtrartabla('todos')">Ver Todo</button>
<label for="buscador">Buscar:</label> <input type="text" id="buscador" value=""/>
</div>
<table id="suspended_sales_table" class="table table-striped table-hover">
	<thead>
		<tr bgcolor="#CCC">
		    <th>&nbsp;</th>
			<th>&nbsp;</th>
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
			<th><?php echo $this->lang->line('sales_unsuspend_and_delete'); ?></th>
			<th><?php echo $this->lang->line('sales_print');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total=0;
		foreach($suspended_sales as $suspended_sale)
		{
			$sale_info = $this->Sale->get_info($suspended_sale['doc_id'])->row_array();
			if($suspended_sale['dinner_table_id']==1)
				$clasefiltro='tienda';
			elseif($suspended_sale['dinner_table_id']==2)
				$clasefiltro='alquiler';
			else
				$clasefiltro='otro';
			
			$totalabonos=$this->Sale->getTotPaymentsSale($suspended_sale['doc_id']);
		?>
			<tr class="<?php echo $clasefiltro;?>">
				<td><?php if(!$totalabonos) {?><input type="checkbox" name="option" value="<?php echo $suspended_sale['doc_id']."-".$suspended_sale['tipo']; ?>" /><?php } else echo "&nbsp;";?></td>
				<td><?php echo $suspended_sale['doc_id']; ?></td>
				<!--td><?php echo date($this->config->item('dateformat'), strtotime($suspended_sale['date_reservation'])); ?></td-->
				<td><?php echo date('Y-m-d h:i A', strtotime($suspended_sale['date_reservation'])); ?></td>
				<?php
				/*if($this->config->item('dinner_table_enable') == TRUE)
				{
				*/?>
					<!--td><?php echo $this->Dinner_table->get_name($suspended_sale['dinner_table_id']); ?></td-->
					<td><?php echo $suspended_sale['cancha']; ?></td>
				<?php
				/*}*/
				?>
				<td>
					<?php
					if(isset($suspended_sale['customer_id']))
					{
						$customer = $this->Customer->get_info($suspended_sale['customer_id']);
						echo $customer->first_name . ' ' . $customer->last_name;
					}
					else
					{
					?>
						&nbsp;
					<?php
					}
					?>
				</td>
				<td>
					<?php
					if(isset($suspended_sale['employee_id']))
					{
						$employee = $this->Employee->get_info($suspended_sale['employee_id']);
						echo $employee->first_name . ' ' . $employee->last_name;
					}
					else
					{
					?>
						&nbsp;
					<?php
					}
					?>
				</td>
				<td class="subtotal"><?php echo to_currency($sale_info['amount_due']); ?></td>
				<td class="subtotal">
				<?php echo to_currency($totalabonos);?></td>
				<td>
					<?php echo form_open('sales/unsuspend'); ?>
						<?php echo form_hidden('suspended_sale_id', $suspended_sale['sale_id']); ?>
						<input type="submit" name="submit" value="<?php echo $this->lang->line('sales_unsuspend'); ?>" id="submit" class="btn btn-primary btn-xs pull-right">
					<?php echo form_close(); ?>
				</td>
				<td>
					<a href="sales/receipt/<?php echo $suspended_sale['sale_id'];?>">
					<div class="btn btn-info btn-xs" ,="" id="show_print_button"><span class="glyphicon glyphicon-print"></span></div>
					</a>
				</td>
			</tr>
		<?php
			$total+=$sale_info['amount_due'];
		}
		?>
	</tbody>
    <tfoot>
    </tfoot>
</table>
<div id="resultado_total" style="font-weight:bold; text-align:right; padding-bottom:40px; margin-right:100px; margin-top:-20px">Total:&nbsp;<?php echo to_currency($total);?></div>
<div class='btn btn-sm btn-danger' id='cancel_sale_button' onclick="delete_sales()"><span class="glyphicon glyphicon-remove">&nbsp</span><?php echo $this->lang->line('sales_delete_reservation'); ?></div>


<script >

/*cambio carlos chaucanes*/
function delete_sales(){
	var options = document.querySelectorAll("input[name='option']:checked");
		if(options.length > 0){
			if(confirm("<?php echo $this->lang->line('sales_delete_confirm');?>")){
					var selected = [];
					options.forEach(element => {
						selected.push(element.value);
					});
					$.ajax({
					url: "<?php echo site_url('sales/group_cancel'); ?>",
					type: 'POST',
					data:{
						sales:JSON.stringify(selected)
					},
					success: function(response) {
						var res = parseInt(response);
						if(res>0){
							var button = document.getElementById('show_suspended_sales_button');
							var cerrar = document.getElementById('close');
							// Simula un clic en el botón
							cerrar.click();
							button.click();
						}
					},
					error: function(xhr, status, error) {
						// Manejar errores aquí
						console.log(JSON.stringify(status));
					}
				});
			}
		}
		else
		{
			alert("<?php echo $this->lang->line('sales_delete_message'); ?>");
		}
}

function select_reservations(){
	$options = document.querySelectorAll("input[name='option']");
	const btn = document.getElementById('selected_reservations');
	if(btn.checked){
		$options.forEach(element => {
			element.checked = true;
		});
	}
	else
	{
		$options.forEach(element => {
			element.checked = false;
		});
	}
}




function filtrartabla(filtro){
	/* carlos chaucanes eliminar en bloque */



	if(filtro=='alquiler')
	{
		$(".alquiler").show();
		$(".tienda").hide();
		$(".otro").hide();
	}
	if(filtro=='tienda')
	{
		$(".tienda").show();
		$(".alquiler").hide();
		$(".otro").hide();
	}
	if(filtro=='otro')
	{
		$(".otro").show();
		$(".alquiler").hide();
		$(".tienda").hide();
	}
	if(filtro=='todos')
	{
		$(".otro").show();
		$(".alquiler").show();
		$(".tienda").show();
	}
	calculaTotal();
}

jQuery("#buscador").keyup(function(){
    if( jQuery(this).val() != ""){
        jQuery("#suspended_sales_table tbody>tr").hide();
        jQuery("#suspended_sales_table td:contiene-palabra('" + jQuery(this).val() + "')").parent("tr").show();
		
    }
    else{
        jQuery("#suspended_sales_table tbody>tr").show();
    }
	calculaTotal();
});
 
jQuery.extend(jQuery.expr[":"], 
{
    "contiene-palabra": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || jQuery(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});

function calculaTotal(){
	var sum=0;
	var valor='';
	$('.subtotal').each(function() {
		if ($(this).is(':visible'))   
		{
			valor=$(this).html().replace(".", '');
			valor=valor.replace('$','').trim();
			valor=valor.replace('&nbsp;','');
	 		sum += parseFloat(valor, 10);  
		}
	}); 
	$('#resultado_total').html('Total:  ' + format(sum.toFixed(2)));
}
var format = function(num){
    var str = num.toString().replace("$", "").trim(), parts = false, output = [], i = 1, formatted = null;
    if(str.indexOf(".") > 0) {
        parts = str.split(".");
        str = parts[0];
    }
    str = str.split("").reverse();
    for(var j = 0, len = str.length; j < len; j++) {
        if(str[j] != ",") {
            output.push(str[j]);
            if(i%3 == 0 && j < (len - 1)) {
                output.push(".");
            }
            i++;
        }
    }
    formatted = output.reverse().join("");
    return("$" + formatted);
};


</script>