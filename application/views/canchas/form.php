<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<?php json_encode($canchas_info); ?>
<?php echo form_open_multipart($controller_name . '/save/' . $canchas_info->id_cancha, array('id'=>'canchas_edit_form', 'class'=>'form-horizontal')); ?>
<!--?php echo form_open('canchas/save/', array('id'=>'canchas_edit_form', 'class'=>'form-horizontal')); ?-->
	<fieldset id="item_basic_info"> 
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_info'), 'canchas_info', array('class'=>'control-label col-xs-3')); ?>
			<?php echo form_label(!empty($canchas_info->id_cancha) ? $this->lang->line('canchas_id') . ' ' . $canchas_info->id_cancha : '', 'id_cancha', array('class'=>'control-label col-xs-8', 'style'=>'text-align:left')); ?>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_nombre'), 'nombre_cancha', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_input(array(
					'name'=>'nombre_cancha',
					'id'=>'nombre_cancha',
					'class'=>'form-control input-sm',
					'value'=>$canchas_info->nombre_cancha,
					'required'=>'true'
				),
					
					);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label("Tipo de Cancha", 'tipo_cancha', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_dropdown('tipo_cancha', $tiposcancha, $canchas_info->tipo_cancha, 'id="tipo_cancha" class="form-control"');?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_description'), 'descripcion_cancha', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_textarea(array(
					'name'=>'descripcion_cancha',
					'id'=>'descripcion_cancha',
					'class'=>'form-control input-sm',
					'value'=>$canchas_info->descripcion_cancha,
					'required'=>'true')
					);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_foto'), 'urlfoto_cancha', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_upload('urlfoto_cancha');?>
			</div>
			<div class='col-xs-6'>
				<img src="<?php echo base_url($canchas_info->urlfoto_cancha); ?>" alt="" style="max-width: 100px; max-height: 100px;" />
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('canchas_referencia'), 'referencia_articulo', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_input(array(
					'name'=>'referencia_articulo',
					'id'=>'referencia_articulo',
					'class'=>'form-control input-sm',
					'value'=>$canchas_info->referencia_articulo,
					'required'=>'true')
					);?>
			</div>
		</div>


	</fieldset>
<?php echo form_close(); ?>


<script type="text/javascript">
$(document).ready(function() {

	$('#canchas_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					console.log(JSON.stringify(response));
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		},
		errorLabelContainer: '#error_message_box',
		rules:
		{
			nombre_cancha:
			{
				required: true,
				minlength: 1
			},
			tipo_cancha:
			{
				required: true,
			},
			descripcion_cancha:
			{
				required: true
			},
			referencia_articulo:
			{
				required: true
			}
		},
		messages: 
		{
			nombre_cancha: "Es obligatorio digitar el nombre de la cancha",
			tipo_cancha: "Es obligatorio digitar el tipo de la cancha",
			descripcion_cancha: "Es obligatorio digitar la descripcion de la cancha",
			referencia_articulo: "Es obligatorio ingresar la referencia del articulo"
		}
	}, form_support.error));
})
</script>