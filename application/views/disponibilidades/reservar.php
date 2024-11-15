<style>
	/*
	.has-error{
		color: red !important;
		width: 100% !important;
	}*/
</style>
<h4>Reservar cancha <?php echo $disponibilidad_info->nombre_cancha;?></h4>
<ul id="reserva_error_message_box" class="error_message_box"></ul>
<div class="form-group form-group-sm">
	<?php
	setlocale (LC_TIME, "es_CO");
	?>
	<!--div class="col-xs-3">Dia:</div><div class="col-xs-8"><?php echo utf8_encode(strftime("%A, %d de %B de %Y",strtotime($disponibilidad_info->fechahora_inicio)));?></div-->
	<div class="col-xs-3">Dia:</div><div class="col-xs-8"><?php echo  $this->Disponibilidad->convertFecha(strtotime($disponibilidad_info->fechahora_inicio));?></div>
</div>
<div class="form-group form-group-sm">
    <div class="col-xs-3">Hora:</div><div class="col-xs-8"><?php echo strftime("%I:%M %p",strtotime($disponibilidad_info->fechahora_inicio));?></div>
</div>
<h2>&nbsp;</h2>
<?php 
	echo form_open($controller_name."/guardar_reserva/", array('id'=>'reservar_cancha_form', 'class'=>'form-horizontal')); ?>
	<input type="hidden" id="id_disponibilidad" name="id_disponibilidad" value="<?php echo $disponibilidad_info->id_disponibilidad;?>" />
    <input type="hidden" id="item_number" name="item_number" value="<?php echo $disponibilidad_info->referencia_articulo;?>" />
	<div class="form-group" id="select_customer">
		<div class="col-xs-12">
		<?php echo form_input(array('name'=>'customer', 'id'=>'customer', 'class'=>'form-control input-sm', 'placeholder'=>$this->lang->line('sales_start_typing_customer_name')));?>
		<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("customers/view/-1/availability"); ?>'
				title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
			<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line('customers_new'); ?>
		</button>	
	</div>
	</div>
    <div class="form-group">
        <div class="col-xs-12"><span id="labelcliente"></span>
        <input type="text" id="person_id" name="person_id" value="" readonly="readonly" style="border:none; background:none; width:0.1%;" />
        </div>
        <div class="col-xs-10">
        <input type="text" id="person_name" name="person_name" value="" readonly="readonly" style="border:none; background:none; width:100%; font-size:1.3em" />
        </div>
    </div>
    <div class="form-group" id="select_customer">
		<div class="col-xs-12">
        <label for="porunmes">Reservar por un mes todos los <?php echo $this->Disponibilidad->convertDay(utf8_encode(strftime("%A a las %I:%M %p",strtotime($disponibilidad_info->fechahora_inicio))));?></label>
		<input name="porunmes" id="porunmes" type="checkbox" value="1" />
		</div>
	</div>

<?php echo form_close(); ?>



<script type="text/javascript">
$(document).ready(function()
{
	dialog_support.init("a.modal-dlg, button.modal-dlg");
	var clear_fields = function()
	{
		$(this).val('');
	};

	$('#customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});
	$("#customer").blur(function(event)
	{
		$(this).val("");
	});
	$( "#person_id" ).focus(function() {
	  $('#customer').focus();
	});
	$("#customer").autocomplete(
	{
		source: "<?php echo site_url("customers/suggest"); ?>",
		minChars: 0,
		delay: 10,
		select: function (a, ui) {
			$("#person_id").val(ui.item.value);
			$("#person_name").val(ui.item.label);
			$("#labelcliente").html("Cliente:");
			$(this).val("");
			/*AQUI ACCIONES CUANDO SELECCIONA*/
		}
	});
	$('#customer').keypress(function (e) {
		if(e.which == 13) {
			return false;
		}
	});
	
	$('#reservar_cancha_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: '#reserva_error_message_box',

		rules:
		{
			person_id: 'required'
		},

		messages:
		{
			person_id: "Debe seleccionar un cliente"
		}
	}, form_support.error));
	
});
</script>