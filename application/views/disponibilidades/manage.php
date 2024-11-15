<?php $this->load->view("partial/header"); ?>
<style type="text/css">

/* CSS Document */
.bloqueada {
	background-color:#FDD;
}
.disponible {
	background-color:#DFD;
}
.solicitada{
	background-color:#FFC;
}
.reservada{
	background-color:#FC7;
}
.facturada{
	background-color:#ABF;
}
</style>
<ul class="nav nav-tabs" data-tabs="tabs">
	<?php
	
	$i=0;
	foreach($canchas as $cancha)
	{
    ?>
	<li <?php if($cancha->id_cancha==$tab) echo ' class="active"';?> role="presentation">
		<a data-toggle="tab" href="#cancha-<?php echo $cancha->id_cancha;?>"  class="cancha-<?php echo $cancha->id_cancha;?>"  title="Configuración de <?php echo $cancha->nombre_cancha;?>"><?php echo $cancha->nombre_cancha;?></a>
	</li>
    <?php	
		$i++;
	}
	?>
</ul>

<div class="tab-content">
  <?php
	$i=0;
	foreach($canchas as $cancha)
	{
  ?>
    <div class="tab-pane <?php if($cancha->id_cancha==$tab) echo 'active';?>" id="cancha-<?php echo $cancha->id_cancha;?>">  
      	<h1 class="titcancha" id="<?php echo $cancha->id_cancha;?>"><?php echo $cancha->nombre_cancha;?></h1>
          <table align="center" style="margin-top:8px; margin-bottom:5px;">
          <tr><td align="left">
            <span class="btn-group">
              <a class="btn btn-default" href="<?php echo $controller_name."?diainicio=".date("Y-m-d",strtotime($diainicio."- 7 days"))."&tab=".$cancha->id_cancha;?>" title="Dias atráz"  style="font-size:large">
                 <span class="glyphicon glyphicon-backward"></span>
              </a>                 
            </span>
          </td><td align="center">
              <a class="btn btn-default" href="#" style="font-size:large"><?php echo $mestitulo;?></a>
          <td align="right">
            <span class="btn-group">
			
              <a class="btn btn-default" href="<?php echo $controller_name."?diainicio=".date("Y-m-d",strtotime($diainicio."+ 7 days"))."&tab=".$cancha->id_cancha;?>" title="Dias adelantedd"  style="font-size:large">
                 <span class="glyphicon glyphicon-forward"></span>
              </a>                 
            </span>
			Ir a la Fecha
			<input type="date" id="go_<?php echo $cancha->id_cancha;?>" name="go_<?php echo $cancha->id_cancha;?>" onchange="go_date(<?php echo $cancha->id_cancha;?>)"  value="<?php echo date("Y-m-d",strtotime($diainicio)); ?>"/>
          </td>
		  
		</tr></table>        
      	  <div class="table-responsive">                    
            <table class="table table-hover table-striped table-bordered">
              <thead>
              	<tr>
                	<th class="text-center" width="80px">Hora</th>
                    <?php
					foreach($cabecera_dias as $dia)
					{
					?>
                	<th class="text-center" width="13.5%"><?php echo $dia['label'];?></th>
                  	<?php
					}
					?>
                </tr>
              </thead>
              <tbody id="contenido_<?php echo $cancha->id_cancha;?>">
				
              </tbody>
            </table>
          </div>
    </div>
  <?php
  $i++;
	}
	
  ?>

</div>
<?php
	echo form_open('sales/unsuspend',array('id' => 'unsuspend_reserva', 'enctype' => 'multipart/form-data'));
	echo form_hidden('suspended_sale_id', '');
	echo form_close();
?>  
<script type="text/javascript">
$( document ).ready(function() {
	const tab = document.querySelector('.cancha-'+<?php echo $tab; ?>);
	console.log(tab);
	tab.click();
	cargarDisponibilidad();	
	dialog_support.init("a.modal-dlg, button.modal-dlg");
	table_support.handle_submit = function(resource, response, stay_open)
	{
		$.notify(response.message, { type: response.success ? 'success' : 'danger'} );
		cargarDisponibilidad();
		$('input[name ="suspended_sale_id"]').val(response.id);
		$("#unsuspend_reserva").submit();		
	}
});

function reservar(iddisponibilidad)
{
	var url="<?php echo site_url().$controller_name.'/reservar/';?>"+iddisponibilidad;
	
}
function facturar(sale_id,id_cancha)
{
	$('input[name ="suspended_sale_id"]').val(sale_id);
	$("#unsuspend_reserva").submit();
}

function bloquear(fechahora,idcancha)
{
	
}
function desbloquear(fechahora,idcancha)
{
	
}
function detalles(codigo)
{
	var url='{$fsc->url()}&codigo='+codigo;
	$("#detalle").load(url);
	$('#modal_detalles').modal('show');
}

function cargarDisponibilidad($diainicio){
	var url="";
	$("h1.titcancha").each(function(){
		
		$("#contenido_"+$(this).attr('id')).html("");
		url="<?php echo site_url().$controller_name.'/filasdisponibilidad/';?>"+$(this).attr('id')+"/<?php echo $diainicio;?>";
		var contenido="";
		$.ajax({ type: "GET",   
				 url: url,   
				 async: false,
				 success : function(text)
				 {
					 
					 contenido=text;
				 }
		});
		$("#contenido_"+$(this).attr('id')).append(contenido);	
	});
}
//funcion para ir a fecha seleccionada
function go_date(id_cancha){
	var selectedDate = document.getElementById("go_"+id_cancha).value;
	//alert(selectedDate);
	var url = "<?php echo $controller_name;?>";
	url +="?diainicio="+selectedDate;
	url +="&tab="+id_cancha;
	window.location.href = url;
}
</script>

<?php $this->load->view("partial/footer"); ?>
