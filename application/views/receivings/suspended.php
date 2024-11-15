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
<label for="buscador">Buscar:</label> <input type="text" id="buscador" value=""/>
</div>
<table id="suspended_receivings_table" class="table table-striped table-hover">
	<thead>
		<tr bgcolor="#CCC">
			<th>&nbsp;</th>
			<th><?php echo $this->lang->line('receivings_date'); ?></th>
			<th><?php echo $this->lang->line('receivings_supplier'); ?></th>
			<th><?php echo $this->lang->line('receivings_employee'); ?></th>
			<th><?php echo $this->lang->line('receivings_total'); ?></th>
			<th><?php echo $this->lang->line('receivings_unsuspend_and_delete'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total=0;
		foreach($suspended_receivings as $suspended_receiving)
		{
			//$receiving_info = $this->Receiving->get_info($suspended_receiving['receiving_id'])->row_array();
			$receiving_info = $this->Receiving->getTotalByReceivingId($suspended_receiving['receiving_id']);
		?>
			<tr>
				<td><?php echo $suspended_receiving['receiving_id']; ?></td>
				<td><?php echo date($this->config->item('dateformat'), strtotime($suspended_receiving['receiving_time'])); ?></td>				
				<td>
					<?php
					if(isset($suspended_receiving['supplier_id']))
					{
						$supplier = $this->Supplier->get_info($suspended_receiving['supplier_id']);
						echo $supplier->first_name . ' ' . $supplier->last_name;
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
					if(isset($suspended_receiving['employee_id']))
					{
						$employee = $this->Employee->get_info($suspended_receiving['employee_id']);
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
				<td class="subtotal"><?php echo to_currency($receiving_info['total']); ?></td>
				<td>
					<?php echo form_open('receivings/unsuspend'); ?>
						<?php echo form_hidden('suspended_receiving_id', $suspended_receiving['receiving_id']); ?>
						<input type="submit" name="submit" value="<?php echo $this->lang->line('receivings_unsuspend'); ?>" id="submit" class="btn btn-primary btn-xs pull-right">
					<?php echo form_close(); ?>
				</td>
			</tr>
		<?php
			$total+=$receiving_info['total'];
		}
		?>
	</tbody>
    <tfoot>
    </tfoot>
</table>
<div id="resultado_total" style="font-weight:bold; text-align:right; padding-bottom:40px; margin-right:100px; margin-top:-20px">Total:&nbsp;<?php echo to_currency($total);?></div>
<script>

jQuery("#buscador").keyup(function(){
    if( jQuery(this).val() != ""){
        jQuery("#suspended_receivings_table tbody>tr").hide();
        jQuery("#suspended_receivings_table td:contiene-palabra('" + jQuery(this).val() + "')").parent("tr").show();
		
    }
    else{
        jQuery("#suspended_receivings_table tbody>tr").show();
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
			valor=valor.replace('$','');
	 		sum += parseFloat(valor, 10);  
		}
	}); 
	$('#resultado_total').html('Total:  ' + format(sum.toFixed(2)));
}
var format = function(num){
    var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
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