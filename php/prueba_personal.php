<?php
include 'main.php';

$sql_pdf = "SELECT p.nombre, p.apellido, p.cedula, p.fecha_nac, g.descripcion FROM personal AS p INNER JOIN gerencia AS g ON ";
$result_pdf = mysqli_query($con, $sql_pdf);
$dato = mysqli_fetch_array($result_pdf);

$nombre = $dato['nombre'];
$apellido = $dato['apellido'];
$cedula = $dato['cedula'];
$fecha_nac = $dato['fecha_nac'];
$descripcion = $dato['descripcion'];

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

	

	<table style='margin-top: 6%; margin-left: 50%;';
	margin-right: auto;'>	

	</table>

	<table align='center'>

		<tr>
			<td>NOMBRE</td>
			<td>".$nombre."</td>
		</tr>

		<tr>
			<td>APELLIDO</td>
			<td>".$apellido."</td>
		</tr>

		<tr>
			<td>CEDULA</td>
			<td>".$cedula."</td>
		</tr>

		<tr>
			<td>FECHA DE NACIMIENTO</td>
			<td>".$fecha_nac."</td>
		</tr>
		
		<tr>
			<td>GERENCIA</td>
			<td>".$descripcion."</td>
		</tr>

		
	</table>";
$html2pdf->writeHTML($contenidoHTML);
$html2pdf->output();
?>