<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name);?>/listartarifas/<?php echo $id_cancha?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'id_tarifa',
	});
});
</script>

<div id="title_bar" class="print_hide btn-toolbar">
	<!--button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/nuevatarifa/".$id_cancha); ?>'
			title='Nueva Tarifa'>
		<span class="glyphicon glyphicon-tags">&nbsp;</span>Nueva Tarifa
	</button-->
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp;</span><?php echo $this->lang->line("common_delete");?>
		</button>
	</div>
</div>
<div id="contenidotarifas">
<div style="width:20%; float:left; padding-right:15px">
<h3><?php echo $cancha_info->nombre_cancha;?></h3>
<img src="<?php echo $cancha_info->urlfoto_cancha;?>" width="100%" />
<h4>Tipo Cancha: <?php echo $cancha_info->tipo_cancha;?></h4>
</div>
<div id="table_holder" style="width:80%; float:left">
	<table id="table"></table>
</div>
</div>
<?php $this->load->view("partial/footer"); ?>
