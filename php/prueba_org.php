<?php
include 'main.php';

$sql_pdf = "SELECT o.nombre, o.ubicacion, o.nombre_responsable, o.cedula_responsable, o.telefono_responsable, o.fecha_registro FROM organizacion AS o ";
$result_pdf = mysqli_query($con, $sql_pdf);
$dato = mysqli_fetch_array($result_pdf);

$org = $dato['nombre'];
$responsable = $dato['nombre_responsable'];
$direccion = $dato['ubicacion'];
$cedula = $dato['cedula_responsable'];
$telefono = $dato['telefono_responsable'];
$fecha = $dato['fecha_registro'];

require __DIR__.'/../vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf();

$contenidoHTML = "



<style>

img{
	width: 150px;
	height: 100px;
}

table{
	border-collapse: collapse; 
	width: 100%;
	color: #255E2B;
	font-family: Arial, sans-serif;
}

td{
	border: 1px solid #255E2B; 
	padding: 8px;
	font-family: Arial, sans-serif;	
}

</style>


	<img src='../img/ccs_recicla_logo.png' style='float: left;'>

	<img src='../img/logo.png' height='150px' style='float: right; margin-top: -3%;'>


	<h5 style='font-family: Arial, sans-serif;
	text-align:center;'>GOBIERNO DE CARACAS<br>
	CORPORACION CARACAS RECICLA, S.A.</h5>


	<h5 style='font-family: Arial, sans-serif;
	text-align:center; color: #255E2B;'>DATOS DE ORGANIZACION</h5>

	
	<table align = 'center'>

		<tr>
			<td>ORGANIZACION</td>
			<td>".$org."</td>
		</tr>

		<tr>
			<td>RESPONSABLE</td>
			<td>".$responsable."</td>
		</tr>

		<tr>
			<td>DIRECCION</td>
			<td>".$direccion."</td>
		</tr>

		<tr>
			<td>CEDULA DEL RESPONSABLE</td>
			<td>".$cedula."</td>
		</tr>
		
		<tr>
			<td>TELEFONO DEL RESPONSABLE</td>
			<td>".$telefono."</td>
		</tr>
		
		<tr>
			<td>FECHA DE REGISTRO</td>
			<td>".$fecha."</td>
		</tr>

	</table>";
$html2pdf->writeHTML($contenidoHTML);
$html2pdf->output();
?>