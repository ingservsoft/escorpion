<style>
	/*
	.has-error{
		color: red !important;
		width: 100% !important;
	}*/
</style>
<ul id="reserva_error_message_box" class="error_message_box"></ul>

<h2>Seleccionar Cliente</h2>
<?php 
	echo form_open($controller_name."/reservaVarias/", array('id'=>'reservar_cancha_form', 'class'=>'form-horizontal')); ?>
	<div class="form-group" id="select_customer">
		<div class="col-xs-12">
		<?php echo form_input(array('name'=>'customer', 'id'=>'customer', 'class'=>'form-control input-sm', 'placeholder'=>$this->lang->line('sales_start_typing_customer_name')));?>
		<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("customers/view/-1/availability"); ?>'
				title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
			<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line('customers_new'); ?>
		</button>	
	</div>
	<div>
		<!--h6><b>Cantidad de reservas seleccionadas:</b> <?php echo $cantidad ?></h6-->
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

	<div class="form-group">
        <div class="col-xs-12"><span id="labelcliente"></span>
        <input type="button" class="btn btn-sm btn-success" value="Reservar" onclick="reservarDisponibilidades()"/>
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