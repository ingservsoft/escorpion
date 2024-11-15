<style>
	#tabla_horario{
		width: 100%;
	}

	.center_content{
		text-align: center;
	}
</style>
<h3 class="center_content"><?php echo $cancha_info->nombre_cancha;?></h3>

<!--div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div-->
<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('canchas/guardartarifa', array('id'=>'tarifas_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_basic_info">
		<table id="tabla_horario">
			<tr>
				<td>
					<b>Seleccionar Cancha</b>
					<br/><br/>	
					<?php
						$canchas = $this->Cancha->get_canchas();
						foreach($canchas as $cancha){
							echo '<label>';
							echo '<input type="checkbox" name="canchas[]" value="' . $cancha->id_cancha . '"> ' . $cancha->nombre_cancha;
							echo '</label><br>';
						}
						
					?>
				</td>
				<td>
					<b>Seleccionar Dia</b>
					<br/><br/>
					<fieldset>
						<label><input type="checkbox" name="dias[]" value="lunes"> Lunes</label><br>
						<label><input type="checkbox" name="dias[]" value="martes"> Martes</label><br>
						<label><input type="checkbox" name="dias[]" value="miercoles"> Miércoles</label><br>
						<label><input type="checkbox" name="dias[]" value="jueves"> Jueves</label><br>
						<label><input type="checkbox" name="dias[]" value="viernes"> Viernes</label><br>
						<label><input type="checkbox" name="dias[]" value="sabado"> Sábado</label><br>
						<label><input type="checkbox" name="dias[]" value="domingo"> Domingo</label><br>
					</fieldset>	
				</td>
				<td>
				<b>Seleccionar Hora</b>
				<br/><br/>
				<?php
					$startHour = 6;
					$endHour = 23;
					// Generar los checkboxes
					for ($hour = $startHour; $hour <= $endHour; $hour++){
						$hourFormatted = $hour > 12 ? ($hour - 12) . ':00 PM' : $hour . ':00 AM';
						$hour24 = $hour < 10 ? "0$hour:00" : "$hour:00";
						echo '<label>';
						echo '<input type="checkbox" name="hours[]" value="' . $hour24 . '"> ' . $hourFormatted;
						echo '</label><br>';
					}
        		?> 
				</td>
				 
			</tr>
		</table>
		<div class="center_content">
			<b>Valor Tarifa</b></br>
			<input type="number" id="valor_tarifa" name="valor_tarifa"/>
		</div>
		<!--div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_info'), 'canchas_info', array('class'=>'control-label col-xs-3')); ?>
			<?php echo form_label(!empty($canchas_info->id_cancha) ? $this->lang->line('canchas_id') . ' ' . $canchas_info->id_cancha : '', 'id_cancha', array('class'=>'control-label col-xs-8', 'style'=>'text-align:left')); ?>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label("Tipo de Cancha", 'tipo_cancha', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_dropdown('tipo_cancha', $tiposcancha, $canchas_info->tipo_cancha, 'id="tipo_cancha" class="form-control"');?>
			</div>
		</div-->
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
$(document).ready(function() {

	$('#tarifas_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					var resp = JSON.parse(JSON.stringify(response));
					if(resp.success){
						dialog_support.hide();
					}
					
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		},
		errorLabelContainer: '#error_message_box',
		rules:
		{
			valor_tarifa:
			{
				required: true,
				minlength: 1
			}
		},
		messages: 
		{
			valor_tarifa: "Digite un valor de tarifa"
		}
	}, form_support.error));
})
</script>



