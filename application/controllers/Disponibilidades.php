<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Disponibilidades extends Secure_Controller
{
	public $diainicio;
	public $diasatraz;
	public $seleccionadas;
	
	public function __construct()
	{
		parent::__construct('disponibilidades');
	}

	public function index()
	{		
		//primero actualizamos la tabla disponibilidad y sus tarifas
		$this->creayactualiza_disponibilidad();
		
		//recogemos variables que vamos a necesitar
		$this->diainicio=isset($_GET['diainicio'])?$_GET['diainicio']:date("Y-m-d");
		
		$data = array();
		$canchas=$this->Cancha->get_all(1000,0,'nombre_cancha','asc')->result();
		
		foreach($canchas as $cancha)
		{
			$cancha->tarifas=$this->Cancha->get_tarifas_cancha($cancha->id_cancha);
			$data['canchas'][]=$cancha;		
		}
		
		$data['diainicio']=$this->diainicio;
		$data['mestitulo']=$this->mestitulo();
		$data['cabecera_dias']=$this->cabecera_dias();
		$data['tab'] = isset($_GET['tab'])?$_GET['tab']:4;
		
		$this->load->view('disponibilidades/manage', $data);
	}
	
	public function creayactualiza_disponibilidad()
	{
		//aqui vamos a actualizar y crear disponibilidades hasta un dos despues de la fecha actual
		$fechaactual = time();
		$fechalimite = strtotime(date("Y-m-d")."+ 2 month");
		$tarifas=$this->Cancha->get_tarifas_cancha(); //busca todas las tarifas		
		
		foreach($tarifas as $tarifa)
		{
			$id_cancha=$tarifa['id_cancha'];
			for($dia=$fechaactual;$dia<=$fechalimite;$dia+=86400)
			{		  
			   $fechahora_inicio = date("Y-m-d ".$tarifa['hora'], $dia);
			   $disponibilidad_data=$this->Disponibilidad->get_bydata(date("Y-m-d ".$tarifa['hora'], $dia),$id_cancha);
				
				//sacamos el valor de la tarifa de acuerdo al dia de la semana
				switch(date("N",$dia)) //1 (para lunes) hasta 7 (para domingo)
				{
				   case 1:
					$valtarifa = $tarifa['lunes'];
					break;
				   case 2:
					$valtarifa = $tarifa['martes'];
					break;
				   case 3:
					$valtarifa = $tarifa['miercoles'];
					break;
				   case 4:
					$valtarifa = $tarifa['jueves'];
					break;
				   case 5:
					$valtarifa = $tarifa['viernes'];
					break;
				   case 6:
					$valtarifa = $tarifa['sabado'];
					break;
				   case 7:
					$valtarifa = $tarifa['domingo'];
					break;
				}
			   if(!$disponibilidad_data) //Si no existe la creamos nueva en la tabla
			   {
				   $can0=$this->Cancha->get_info($tarifa['id_cancha']);
				   $disponibilidad_data = array(
						'fechahora_inicio' => date("Y-m-d", $dia).' '.$tarifa['hora'],
						'tarifa_cancha' => $valtarifa,
						'id_cancha' => $tarifa['id_cancha'],
						'referencia_articulo' => $can0->referencia_articulo,
						'estado' => "DISPONIBLE"
					);
					$this->Disponibilidad->save($disponibilidad_data,-1);											   
			   }
			   elseif($disponibilidad_data->estado=="DISPONIBLE" && $disponibilidad_data->tarifa_cancha!=$valtarifa) //si esta registrada en la tabla y cambió la tarifa actualizamos la tarifa
			   {
				   $disponibilidad_data->tarifa_cancha=$valtarifa;
				   $this->Disponibilidad->save($disponibilidad_data,$disponibilidad_data->id_disponibilidad);
			   }		   
			}
		}
		$this->Disponibilidad->quitardiasanteriores();
	}
	
	public function mestitulo()
	{
	   $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	   return $meses[date("m",strtotime($this->diainicio))-1].' de '.date("Y",strtotime($this->diainicio));
	}
	

	public function listar_canchas()
	{
	  return $this->cancha->listar();
	}
	public function cabecera_dias()
	{
		$cancha = isset($_GET['tab'])?$_GET['tab']:1;
	   
	   $cabeceras=array();
	   $dias=array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
	   $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
	   for($i=0;$i<7;$i++)
	   {
		   $sumadia=date("Y-m-d",strtotime($this->diainicio."+ ".$i." days"));	
		   $cabeceras[]=array(
				"dia"	=>	date("Y-m-d",strtotime($sumadia)),
				"label"	=>	$dias[date("w",strtotime($sumadia))].'<br />'.date("d",strtotime($sumadia)).' '.$meses[date("n",strtotime($sumadia))-1]."<br/><input type='checkbox'  value='".$sumadia."' onchange='seleccionar_dia(this)' />",
				"diasem" => $dias[date("w",strtotime($sumadia))],
				);
	   }
	   
	   return $cabeceras;
	}
	public function cargahoras($idcancha)
	{
		$horas=array();
		$data=$this->Disponibilidad->minmaxhoradisp($idcancha);

		if($data->minima)
		{
			for($i=(double)$data->minima;$i<=(double)$data->maxima;$i++)
			{
				$horas[]=$i.':00';
			}
		}

		return $horas;
	}
	public function filasdisponibilidad($id_cancha,$diainicio)
	{
		$fila='';
	   $horas=$this->cargahoras($id_cancha);
	   foreach($horas as $hora)
	   {
		   $fila.=$hora=='12:00'?'<tr><td colspan="8">&nbsp;</td></tr>':'';
		   $fila.='
		   <tr id="hora'.date("H",strtotime($hora)).'"><th class="hora">'.date("h:i A",strtotime($hora)).'</th>';
		   $fechainicio = strtotime($diainicio);
		   $fechalimite = strtotime($diainicio."+ 6 days");
		   $dias=$this->cabecera_dias();
		   
		   for($dia=$fechainicio;$dia<=$fechalimite;$dia+=86400)
		   {
			   
				$disp=$this->Disponibilidad->get_bydata(date("Y-m-d",$dia).' '.$hora,$id_cancha);
				//$fila.='<td>'.$disp.'</td>';
				if($disp)
				{			
					//carlos chaucanes		
					if($disp->estado=='DISPONIBLE' && $disp->tarifa_cancha != 0)
						$fila.='
						
						<td class="text-center disponible">
						<input type="checkbox" name="seleccionadas" class="fecha_'.explode(" ",$disp->fechahora_inicio)[0].'" value="'.$disp->id_disponibilidad.'"/><br/>
						DISPONIBLE<br />'.to_currency($disp->tarifa_cancha).'<br />
						<button class="btn btn-warning btn-xs modal-dlg" data-btn-submit="Reservar" data-href="'.site_url("disponibilidades/reservar/".$disp->id_disponibilidad).'" title="Reservar"><span class="glyphicon glyphicon-check"></span>&nbsp;Reservar</button>						
						
						</td>';
					elseif($disp->estado=='SOLICITADA')
						$fila.='<td class="text-center solicitada">
						SOLICITADA<br />
						<a href="javascript:aprobar(\''.$disp->fechahora_inicio.'\','.$id_cancha.')" title="Aprobar" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-check"></span></a>
						<a href="javascript:detalles('.$disp->id_disponibilidad.',\'solicitada\')" title="Ver Detalles" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>						
						</td>';
					elseif($disp->estado=='RESERVADA')
					{
						##consultamos datos de la venta suspendida o reserva (sale)
						$sale_data=$this->Disponibilidad->get_info_reserva_by_disponibilidad($disp->id_disponibilidad);
						$customer_id = $sale_data[0]['customer_id'];
						$sale_id = $sale_data[0]['sale_id'];
						$abono = $sale_data[0]['abonado'];
						$html = '<div style="'.($sale_data[0]['abonado']>0?'background-color:#FF9;':'').'margin-top:3px">Abono: '.to_currency($sale_data[0]['abonado']).'</div>';
						if($sale_data[0]['abonado'] > 0){
							$html = '<br><a class=" btn-sm modal-dlg" id="show_suspended_sales_butto" href="'.site_url("Sales/suspendedByCustomer/$customer_id/$sale_id/$abono/mover_abono").'"><span style="'.($sale_data[0]['abonado']>0?'background-color:#FF9;':'').'margin-top:3px">Abono: '.to_currency($sale_data[0]['abonado']).'</span></a>';
						}
						$fila.='<td class="text-center reservada">
						<b>'.$sale_data[0]['cliente'].'</b><br /><span class="glyphicon glyphicon-phone">'.$sale_data[0]['telefono_cliente'].'</span><br />'.to_currency($disp->tarifa_cancha).'<br />
						<a href="javascript:facturar('.$sale_data[0]['sale_id'].')" title="Facturar" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-credit-card"></span>&nbsp;&nbsp;Ver/Facturar</a>						
						'.$html.'
						</td>';						
					}
					elseif($disp->estado=='FACTURADA')
					{
						$sale_data=$this->Disponibilidad->get_info_reserva_by_disponibilidad($disp->id_disponibilidad);
						$fila.='<td class="text-center facturada">
						FACTURADA<br /><b>'.$sale_data[0]['cliente'].'</b><br />'.to_currency($disp->tarifa_cancha).'<br />
						<a href="'.site_url().'sales/receipt/'.$sale_data[0]['sale_id'].'" title="Ver Detalles" class="btn btn-primary"><span class="glyphicon glyphicon-eye-open"></span></a>
						</td>';
					}
					elseif($disp->estado=='BLOQUEADA')
						$fila.='<td class="text-center bloqueada">&nbsp;<br />
						<div>BLOQUEADA<br />
						<a href="javascript:desbloquear(\''.$disp->fechahora_inicio.'\','.$id_cancha.')" title="Desbloquear" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-folder-open"></span></a>
						</div></td>';


				}
				elseif(date($dia.' '.$hora)<date("Y-m-d H:00"))
				{
					$filadata["estado"]="diapasado";
					$fila.='<td class="text-center diapasado"></td>';
				}
				else
				{
					$fila.='<td class="text-center"><a href="" class="small bloqueada">N.D</a></td>';
					$filadata["estado"]="N.D.";
				}
		   }
			$fila.='</tr>';
	   }
	   
	   if($fila!='')
		echo $fila;
	   else
		echo '<tr><td colspan="8" class="text-center">No ha creado tarifas para esta cancha<br /><a href="index.php?page=gestion_cancha&id='.$id_cancha.'">Crear Tarifas</a></td></tr>';
	
	}

	public function seleccionarCliente(){
		/*$disponibilidades = $this->input->get("disponibilidades");
		$disponibles = json_decode($disponibilidades);*/
		$data = array();
		//$data['cantidad'] = count($disponibles); 
		$this->load->view("disponibilidades/seleccionarcliente", $data);
	}


	public function reservarVarias(){
		$disponibilidades = $this->input->post("disponibilidades");
		$disponibles = json_decode($disponibilidades);
		$cont = 0;
		$hora = "06";
		$fechaHora = date('Y-m-d');
		foreach($disponibles as $dis_id){
			$datos_disponibilidad=$this->Disponibilidad->get_info($dis_id);
			$reserva_data = array(
				'id_disponibilidad' => $datos_disponibilidad->id_disponibilidad,
				'customer_id' => $this->input->post('person_id'),
				'employee_id' => $this->Employee->get_logged_in_employee_info()->person_id,
				'item_id' =>$this->Item->get_info_by_id_or_number($referenciarticulo)->item_id,
				'fechahora_reserva' =>$datos_disponibilidad->fechahora_inicio,
				'tarifa_cancha' =>$datos_disponibilidad->tarifa_cancha,
				'iscontract'=>0
			);

			$sale_suspend_id=$this->Disponibilidad->guardar_reserva($reserva_data);
			if($sale_suspend_id){
				$cont++;
				if($cont==1){
					$timestamp = strtotime($datos_disponibilidad->fechahora_inicio);
					$hora = date('H', $timestamp);
					$diainicio = $datos_disponibilidad->fechahora_inicio;
				}
			}
		}
		
		echo json_encode(array('cantidad_reservadas'=>$cont,'hora'=>$hora,'diainicio'=>$diainicio,'cancha_id'=>$datos_disponibilidad->id_cancha));
		
	}

	public function reservar($id_disponibilidad)
	{
		$data = array();

		$disponibilidad_info = $this->Disponibilidad->get_info($id_disponibilidad);
		
		foreach(get_object_vars($disponibilidad_info) as $property => $value)
		{
			$disponibilidad_info->$property = $this->xss_clean($value);
		}
		//buscamos nombre de canchas segun articulos
		$disponibilidad_info->nombre_cancha=$this->Cancha->get_info_by_referencia($this->xss_clean($disponibilidad_info->referencia_articulo))->nombre_cancha;
		//echo json_encode($disponibilidad_info);
		$data['disponibilidad_info'] = $disponibilidad_info;

		$this->load->view("disponibilidades/reservar", $data);
	}


	
	public function guardar_reserva()
	{
		$datos_disponibilidad=$this->Disponibilidad->get_info($this->input->post('id_disponibilidad'));
		$porunmes=$this->input->post('porunmes');		
		$referenciarticulo=$datos_disponibilidad->referencia_articulo;
		$idprimeraventa=0;
		if($porunmes)
		{
			$reserva_data = array(				
				'customer_id' => $this->input->post('person_id'),
				'employee_id' => $this->Employee->get_logged_in_employee_info()->person_id,
				'item_id' =>$this->Item->get_info_by_id_or_number($referenciarticulo)->item_id,				
				'tarifa_cancha' =>$datos_disponibilidad->tarifa_cancha,
				'iscontract'=>1
			);

			$fecha_inicial = new DateTime($datos_disponibilidad->fechahora_inicio);
			$ffin=date("Y-m-d H:i:s",strtotime($datos_disponibilidad->fechahora_inicio."+ 28 days"))."";
			$fecha_final = new DateTime($ffin);
			// Necesitamos modificar la fecha final en 1 día para que aparezca en el bucle
			$fecha_final = $fecha_final ->modify('+0 day');

			$intervalo = DateInterval::createFromDateString('7 day');
			$periodo = new DatePeriod($fecha_inicial , $intervalo, $fecha_final);
			$id_cancha=$datos_disponibilidad->id_cancha;

			$reservas = array();
			$contract_id = $this->Disponibilidad->getContractId();
			foreach ($periodo as $dt) {
				$datos_disponibilidad=$this->Disponibilidad->get_bydata($dt->format("Y-m-d H:00:00"),$id_cancha); // consultamos la disponibilidad
				$reserva_data['id_disponibilidad'] = $datos_disponibilidad->id_disponibilidad;
				$reserva_data['fechahora_reserva'] = $dt->format("Y-m-d H:i:s");
				//guardamos las fechas de las reservas que haran parte del contrato
				array_push($reservas,$dt->format("Y-m-d H:i:s"));
				

				if($datos_disponibilidad->estado=='DISPONIBLE') //solo guardamos la reserva si está disponible
				{
					$sale_suspend_id=$this->Disponibilidad->guardar_reserva($reserva_data,$contract_id);
					if(!$idprimeraventa)
						$idprimeraventa=$sale_suspend_id; //guardamos el primer id de la venta de reserva inicial
				}				
			}
			//guardamos un contrato para en un futuro renovarlo(CarlosCh)
			//$fi = $fecha_inicial->format("Y-m-d H:i:s");
			//$ff = $dt->format("Y-m-d H:i:s");
			//$this->Disponibilidad->saveContract($this->input->post('person_id'),$reservas);
		}
		else
		{
			$reserva_data = array(
				'id_disponibilidad' => $datos_disponibilidad->id_disponibilidad,
				'customer_id' => $this->input->post('person_id'),
				'employee_id' => $this->Employee->get_logged_in_employee_info()->person_id,
				'item_id' =>$this->Item->get_info_by_id_or_number($referenciarticulo)->item_id,
				'fechahora_reserva' =>$datos_disponibilidad->fechahora_inicio,
				'tarifa_cancha' =>$datos_disponibilidad->tarifa_cancha,
				'iscontract'=>0
			);
			$sale_suspend_id=$this->Disponibilidad->guardar_reserva($reserva_data);
			$idprimeraventa=$sale_suspend_id;
		}



		if($sale_suspend_id>0)
		{
			##CUANDO GUARDE CORRECTAMENTE LLAMAMOS A DESUSPENDER LA VENTA para que complete lo del pago			
			echo json_encode(array('success' => TRUE, 'message' => 'Se registró correctamente la reserva', 'id' => $idprimeraventa));
			
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => 'Hubo un error al registrar la reserva', 'id' => -1));
		}
	}




}
?>
