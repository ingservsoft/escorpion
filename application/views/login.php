<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<base href="<?php echo base_url();?>" />
	<title><?php echo $this->config->item('company') . ' | QuieroCancha ' . $this->config->item('application_version')  . ' | ' .  $this->lang->line('login_login'); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="<?php echo 'dist/bootswatch/' . (empty($this->config->item('theme')) ? 'flatly' : $this->config->item('theme')) . '/bootstrap.min.css' ?>"/>
	<!-- start css template tags -->
	<link rel="stylesheet" type="text/css" href="css/login.css"/>
	<!-- end css template tags -->
	<script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>
	<script type="text/javascript" src="dist/opensourcepos.min.js?rel=ed81f5268b"></script>
    <style type="text/css">
	@media only screen and (max-width: 600px) {
		  body {  background-size:auto !important; background-repeat:no-repeat !important  }
		  #login  {  width:95%;  }
		  #terminosycondiciones{ bottom:15%}
		}
	#mensajecobro{
	    position:absolute;
	    top:40%;
	    left:50%;
	    margin-top:-211px;
	    margin-left:-242px;
	    z-index:9999;
	}
	</style>
</head>
<body style="background-image:url(<?php echo base_url();?>/images/fondologin.gif); background-size:cover">
	<div id="logo" align="center"><img src="<?php echo base_url();?>/images/logo.png" height="120px"></div>
	<div id="login">
		<?php echo form_open('login') ?>
			<div id="container">
				<div align="center" style="color:red"><?php echo validation_errors(); ?></div>
				<?php if (!$this->migration->is_latest()): ?>
				<div align="center" style="color:red"><?php echo $this->lang->line('common_migration_needed', $this->config->item('application_version')); ?></div>
				<?php endif; ?>
				<div id="login_form">
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
						<input class="form-control" placeholder="<?php echo $this->lang->line('login_username')?>" name="username" type="text" size=20 autofocus></input>
					</div>
					<div class="input-group">
						<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
						<input class="form-control" placeholder="<?php echo $this->lang->line('login_password')?>" name="password" type="password" size=20></input>
					</div>
					<?php
					if($this->config->item('gcaptcha_enable'))
					{
						echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
						echo '<div class="g-recaptcha" align="center" data-sitekey="' . $this->config->item('gcaptcha_site_key') . '"></div>';
					}
					?>
					<input class="btn btn-primary btn-block" type="submit" name="loginButton" value="<?php echo $this->lang->line('login_go')?>"/>
				</div>
			</div>
		<?php echo form_close(); ?>	
	</div>
    <div id="footerlogin">QuieroCancha® - <span style="font-size:small">Versión:<?php echo $this->config->item('application_version'); ?></span></div>
    <?php
    $dias_ultimopago=$this->Appconfig->dias_ultimopago();
    $infopago=array();
    $estadopago=0;
    foreach($this->Appconfig->get_all()->result() as $app_config)
    {
    	if($app_config->key=="company")
    		$nombreempresa=strip_tags($app_config->value);
    	if($app_config->key=="phone")
    		$nit=ltrim(strip_tags($app_config->value),"NIT: ");
    	if($app_config->key=="stock_alert_email")
    
    		$correo=$app_config->value;
    	if($app_config->key=="info_pago")
    		$infopago=explode("|",$app_config->value);
    	if($app_config->key=="estado_pago")
    		$estadopago=$app_config->value;
    }
    
    $imagenfondo="";
    if($dias_ultimopago>30)
    {
    	$this->Appconfig->desactiva_periodo();
    	$imagenfondo="periodovencido.png";
    }
    elseif($dias_ultimopago>=20)
    {
    	$venceen=31-$dias_ultimopago;
    	$imagenfondo="fondopagos.png";
    }
    
    if($imagenfondo!="")
    {
    	//Empezamos a llenar formularios de pago
    	$valormes=intval($infopago[2]);
    	$valorsem=intval($infopago[2])*(6-6*0.05);
    	$valoranio=intval($infopago[2])*(12-12*0.1);
    	
    	$referenciames="Mes-".$infopago[3]."-".time();
    	$integritywompimes=hash ("sha256", $referenciames.($valormes*100)."COPprod_integrity_mEEjAKKq2WsduFWicgAlViRDB93Vr4WW");
     
    	$referenciasem="Sem-".$infopago[3]."-".time();
        $integritywompisem=hash ("sha256", $referenciasem.($valorsem*100)."COPprod_integrity_mEEjAKKq2WsduFWicgAlViRDB93Vr4WW");
        
        $referenciaanio="Anio-".$infopago[3]."-".time();
        $integritywompianio=hash ("sha256", $referenciaanio.($valoranio*100)."COPprod_integrity_mEEjAKKq2WsduFWicgAlViRDB93Vr4WW");
        ?>
        <script>
    	function pagoMes(){
    	    var checkoutmes = new WidgetCheckout({
              currency: 'COP',
              amountInCents: <?php echo $valormes*100;?>,
              reference: '<?php echo $referenciames;?>',
              publicKey: 'pub_prod_hUtaJbwjSIDJLDQYioJd2qBU9T4d0uzI',
              signature: {integrity : '<?php echo $integritywompimes; ?>'},
            });
    	    checkoutmes.open(function (result) {
              var transaction = result.transaction;
              console.log("Transaction ID: ", transaction.id);
              console.log("Transaction object: ", transaction);
              location.reload();
            });
    	}
    	function pagoSem(){
    	    var checkoutsem = new WidgetCheckout({
              currency: 'COP',
              amountInCents: <?php echo $valorsem*100;?>,
              reference: '<?php echo $referenciasem;?>',
              publicKey: 'pub_prod_hUtaJbwjSIDJLDQYioJd2qBU9T4d0uzI',
              signature: {integrity : '<?php echo $integritywompisem; ?>'},
            });
    	    checkoutsem.open(function (result) {
              var transaction = result.transaction;
              console.log("Transaction ID: ", transaction.id);
              console.log("Transaction object: ", transaction);
              location.reload();
            });
    	}
    	function pagoAnio(){
    	    var checkoutanio = new WidgetCheckout({
              currency: 'COP',
              amountInCents: <?php echo $valoranio*100;?>,
              reference: '<?php echo $referenciaanio;?>',
              publicKey: 'pub_prod_hUtaJbwjSIDJLDQYioJd2qBU9T4d0uzI',
              signature: {integrity : '<?php echo $integritywompianio; ?>'},
            });
    	    checkoutanio.open(function (result) {
              var transaction = result.transaction;
              console.log("Transaction ID: ", transaction.id);
              console.log("Transaction object: ", transaction);
              location.reload();
            });
    	}
        </script>
        <div id="mensajecobro">
        <table id="tablacobros" width="485px" border="0" cellspacing="0" cellpadding="0" align="center" background="<?php echo base_url();?>images/<?php echo $imagenfondo;?>">
          <tr>
            <td width="87" height="37">&nbsp;</td>
            <td width="128">&nbsp;</td>
            <td width="127">&nbsp;</td>
            <td colspan="3" align="right"><img src="<?php echo base_url();?>images/close.png" width="32" height="32" alt="Cerrar" style="cursor:pointer" onclick="$('#mensajecobro').fadeOut(1000);" /></td>
          </tr>
          <tr>
            <td height="205" rowspan="3">&nbsp;</td>
            <td rowspan="3">&nbsp;</td>
            <td rowspan="3">&nbsp;</td>
            <td width="61" height="57">&nbsp;</td>
            <td width="67" rowspan="3">&nbsp;</td>
            <td width="14" rowspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td height="52" align="center" valign="middle" style="font-size:36px; font-family: Arial, Helvetica, sans-serif; color:#444"><?php echo $venceen; ?></td>
          </tr>
          <tr>
            <td height="95">&nbsp;</td>
          </tr>
          <tr>
            <td height="80">&nbsp;</td>
            <td style="cursor:pointer" onclick="pagoMes()">&nbsp;</td>
            <td style="cursor:pointer" onclick="pagoSem()"></td>
            <td colspan="2" style="cursor:pointer" onclick="pagoAnio()"></td>
            <td>&nbsp;</td>
          </tr>
          <tr style="font-family:Impact, Charcoal, sans-serif; color:#000; font-size:24px">
            <td height="37">&nbsp;</td>
            <td align="center" valign="middle" style="cursor:pointer" onclick="pagoMes();"><?php echo "$".number_format($valormes,0,",",".");?></td>
            <td align="center" valign="middle" style="cursor:pointer" onclick="pagoSem();"><?php echo "$".number_format($valorsem,0,",",".");?></td>
            <td colspan="2" align="center" valign="middle" style="cursor:pointer" onclick="pagoAnio();"><?php echo "$".number_format($valoranio,0,",",".");?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="63">&nbsp;</td>
            <td style="cursor:pointer" onclick="pagoMes();">&nbsp;</td>
            <td style="cursor:pointer" onclick="pagoSem();">&nbsp;</td>
            <td colspan="2" style="cursor:pointer" onclick="pagoAnio();">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        </div>
    	<script>
    	$(document).ready(function(){ 
    		$('#mensajecobro').fadeIn(1500);
    	});
    	</script>
    	<?php
    }
    ?>
</body>
</html>

