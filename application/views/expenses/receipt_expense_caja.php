<?php
$mov=json_decode($movimiento);
?>
<style>
	#signatures{
		width: 100%;
		display: flex;
		flex-direction: row;
		text-align: center;
		align-items: center;
	}
	#signatures > div{
		margin-top: 60px;
		width: 40%;
		border-top: 1px solid black;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
	}
	#receipt_marco{
		width: 310px;
		border: 1px solid gray;
		padding: 0;
		margin: 0;
	}

	#container_receipt{
		display: flex;
		flex-direction:row ;
		width: 100%;
		justify-content: center;
		margin-top: 20px;
	}

	#buttons{
		margin-top: 10px;
		display: flex;
		flex-direction: row;
		justify-content: center;
	}
	#sale_receipt,#company_name{
		text-align: center;
	}
@media print {
  body * {
    visibility: hidden; /* Oculta todo el contenido de la página */
  }
	#receipt_marco * {
		visibility: visible; /* Muestra solo el contenido del contenedor especificado */
		padding: 0;
		margin: 0;
	}

  }

  .modal-footer{
	display: none !important;
  }
</style>
<?php $this->load->view("partial/header"); ?>
<?php
	if (isset($error_message))
	{
		echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
		exit;
	}

?>

<div class="print_hide" id="control_buttons" style="text-align:right"></div>
<div id="container_receipt">
	<div id="receipt_marco">		
		<?php
		if ($this->config->item('company_logo') != '') 
		{ 
		?>
			<div id="company_name"><img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" /></div>
		<?php
		}
		?>

		<?php
		if ($this->config->item('receipt_show_company_name') && $this->config->item('company_logo') == '') 
		{ 
		?>
			<div id="company_name"><?php echo $this->config->item('company'); ?></div>
		<?php
		}
		
		?>

		<div id="sale_receipt"><?php echo "<b>".$this->lang->line('receivings_voucher')." ".$mov->tipo_movimiento." No. </b>".$mov->expense_id?></div>
		<div id="sale_time"><?php echo $transaction_time ?></div>
	
		<?php
		if(isset($supplier))
		{
		?>
			<div id="customer"><?php echo $this->lang->line('suppliers_supplier').": ".$supplier; ?></div>
		<?php
		}
		?>
		<?php 
			$third = "";
			if($mov->tipo_movimiento == 'Ingreso'){
				$third = $this->lang->line('receivings_received');
			}
			else
			{
				$third = $this->lang->line('receivings_delivered');
			}
		?>
		<div><?php echo "<b>".$third.":</b> ".$mov->supplier_name; ?></div>

		<div ><?php echo "<b>".$this->lang->line('receivings_balance').":</b> $".to_quantity_decimals($mov->amount); ?></div>

		<div ><?php echo "<b>".$this->lang->line('receivings_concept').":</b> ".$mov->description?></div>
		<div id="signatures">
			<div id="delivered">
				<?php echo $this->lang->line('receivings_deliver')?>
			</div>
			<div id="received">
				<?php echo $this->lang->line('receivings_receive')?>
			</div>
		</div>		
	</div>
</div>
<div id="buttons">
	<a href="javascript:imprimirContenido();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
</div>
<?php $this->load->view("partial/footer"); ?>
<script>
	        function imprimirContenido() {
			var divContent = document.getElementById('receipt_marco').innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = divContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
</script>