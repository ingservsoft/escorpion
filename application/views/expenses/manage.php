<?php $this->load->view("partial/header");?>

<script type="text/javascript">
$(document).ready(function()
{
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e) {
		table_support.refresh();
	});
	
	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>

	$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
		table_support.refresh();
	});

	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>


	table_support.init({
		resource: '<?php echo site_url($controller_name);?>',
		headers: <?php echo $table_headers; ?>,
		sortable: false,
		pageSize: -1,
		uniqueId: 'expense_id',
		onLoadSuccess: function(response) {
			console.log('<?php echo site_url($controller_name)?>');
			if($("#table tbody tr").length > 1) {
				//$("#table tbody tr:last td:first").html(""); //quitamos el ultimo checkbox
				$("#table tbody tr:first td:first").html(""); //quitamos el checkbox del saldo que viene
				<?php
				if(!$this->Employee->has_grant('sales_delete', $this->session->userdata('person_id')))
				{
				?>
					//desactivamos los checkbox y los editar
					$("#table tr").find('td:eq(0)').each(function () {
						$(this).html("");
					});
					$("#table tr").find('td:eq(14)').each(function () {
						$(this).html("");
					});
				<?php
				}
				?>
			}
		},
		queryParams: function() {
			return $.extend(arguments[0], {
				start_date: start_date,
				end_date: end_date,
				filters: $("#filters").val() || [""]
			});
		}
	});
	
});
</script>

<style>
	/*Ocultamos la paginación al final y la busqueda*/
	.fixed-table-pagination{display: none !important;}
	.search{display: none !important;}
	
</style>

<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>false, 'selected_printer'=>'takings_printer'));?>

<div id="title_bar" class="print_hide btn-toolbar">
	<button onclick="javascript:printdoc()" class='btn btn-info btn-sm pull-right'>
		<span class="glyphicon glyphicon-print">&nbsp;</span><?php echo $this->lang->line('common_print'); ?>
	</button>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new'); ?>'>
		<span class="glyphicon glyphicon-tags">&nbsp</span><?php echo $this->lang->line($controller_name . '_new'); ?>
	</button>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>

		<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		<?php //echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'class'=>'selectpicker show-menu-arrow', 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
	</div>
</div>

<div id="table_holder" data-pagination="false">
	<table id="table"></table>
</div>

<div id="payment_summary">
</div>

<?php $this->load->view("partial/footer"); ?>
