<?php
include 'main.php';

$correlativo = $_GET['c'];

$sql_pdf = "SELECT p.fecha, o.nombre, p.responsable, p.cargo, o.ubicacion FROM prerecibo AS p INNER JOIN organizacion AS o ON p.id_organizacion = o.id WHERE correlativo = $correlativo;";
$result_pdf = mysqli_query($con, $sql_pdf);
$dato = mysqli_fetch_array($result_pdf);

$fecha = $dato['fecha'];
$org = $dato['nombre'];
$responsable = $dato['responsable'];
$cargo = $dato['cargo'];
$direccion = $dato['ubicacion'];



$sql_pdf2 = "SELECT m.descripcion, l.cantidad_kg, l.cantidad_p, l.total_kg, l.total_p FROM material_pre AS l INNER JOIN material AS m ON l.id_material = m.id WHERE l.correlativo = $correlativo;
";
$materiales = mysqli_query($con, $sql_pdf2);

$filasMateriales = "";


foreach ($materiales as $m_prereg) {

	$total_kg = $m_prereg['total_kg'];
	$total_p = $m_prereg['total_p'];





    $filasMateriales .= "
        <tr>
        	<td>".$m_prereg['descripcion']."</td>
            <td>".$m_prereg['cantidad_kg']."</td>
            <td>".$m_prereg['cantidad_p']."</td>
        </tr>
    ";
}


$sql_pdf3 = "SELECT o.nombre, o.ubicacion, r.responsable, r.cargo, r.fecha FROM recibo_final AS r INNER JOIN organizacion AS o ON o.id = r.id_organizacion INNER JOIN prerecibo AS p ON p.id = r.id_prerecibo WHERE r.correlativo = $correlativo AND r.estado = '1';";

$result_pdf3 = mysqli_query($con, $sql_pdf3);


$sql_pdf4 = "SELECT m.descripcion, l.cantidad_kg, l.cantidad_p, l.total_kg, l.total_p FROM material_refinal AS l INNER JOIN material AS m ON l.id_material = m.id WHERE l.correlativo = $correlativo;
";
$materiales2 = mysqli_query($con, $sql_pdf4);

$filasMateriales2 = "";


foreach ($materiales2 as $m_ref) {

	$total_kg2 = $m_ref['total_kg'];
	$total_p2 = $m_ref['total_p'];





    $filasMateriales2 .= "
        <tr>
        	<td>".$m_ref['descripcion']."</td>
            <td>".$m_ref['cantidad_kg']."</td>
            <td>".$m_ref['cantidad_p']."</td>
        </tr>
    ";
}





if (mysqli_num_rows($result_pdf3) > 0 || mysqli_num_rows($materiales2) > 0 ) {
	$dato_ref = mysqli_fetch_array($result_pdf3);

	$ref_org = $dato_ref['nombre'];
	$ref_responsable = $dato_ref['responsable'];
	$ref_cargo = $dato_ref['cargo'];
	$ref_direccion = $dato_ref['ubicacion'];
	$ref_fecha = $dato_ref['fecha'];


	$contenidoHTML3 = "

	    <div style='break-after: always;'>


		<img src='../img/ccs_recicla_logo.png' style='float: left;'>

		<img src='../img/logo.png' height='150px' style='float: right; margin-top: -3%;'>


		<h5 style='font-family: Arial, sans-serif;
		text-align:center;'>GOBIERNO DE CARACAS<br>
		CORPORACION CARACAS RECICLA, S.A.</h5>

		

		<table style='margin-top: 6%; margin-left: 50%;';
		margin-right: auto;'>	

			<tr>
				<td style='text-align: right; border: 0px; border-right: 1px solid #255E2B;'><i>FECHA:</i></td>
				<td style='text-align: right border: 1px; border-left: none;'>".$ref_fecha."</td>
			</tr>

			<tr>
				<td style='text-align: right; border: 0px; border-right: 1px solid #255E2B;'><i>N° DE RECIBO:</i></td>
				<td style='text-align: right; border: 1px; border-left: none'>".$correlativo."</td>
			</tr>
		</table>

		<h5 style='font-family: Arial, sans-serif;
		text-align:center; color: #255E2B;'>GERENCIA DE GESTION COMERCIAL</h5>

		<table align='center'>

			<tr>
				<td>COMERCIO O INSTITUCION</td>
				<td>".$ref_org."</td>
			</tr>

			<tr>
				<td>RESPONSABLE</td>
				<td>".$ref_responsable."</td>
			</tr>

			<tr>
				<td>CARGO</td>
				<td>".$ref_cargo."</td>
			</tr>

			<tr>
				<td>DIRECCION</td>
				<td>".$ref_direccion."</td>
			</tr>

		</table>

		<h5 style='font-family: Arial, sans-serif;
		text-align:center; color: #255E2B;'>DETALLES DEL MATERIAL RETIRADO</h5>

		<table align='center'>

			<tr>
				<td>TIPO DE MATERIAL RECIBIDO</td>
				<td>CANTIDAD KG</td>
				<td>PUNTOS</td>
			</tr>".$filasMateriales2."
			
			<tr>
	        	<td>TOTALES</td>
	        	<td>".$total_kg2."</td>
	        	<td>".$total_p2."</td>
	        </tr>


		</table>

	    <table style='width: 100%; margin-top: 40px; border-collapse: collapse;'>
	        <tr>
	            <td style='width: 30%; text-align: left; border: none; padding: 0;'>
	                <div style='
	                	font-family: Arial, sans-serif;
	                    border-bottom: 1px solid #255E2B; 
	                    width: 60%; 
	                    margin: 0 0 0 20%; /* Desactiva el centrado, alineando a la izquierda */
	                    height: 1px;
	                '></div>
	                <p style='
	                	font-family: Arial, sans-serif;
	                    color: #255E2B; 
	                    margin-top: 5px; 
	                    margin-bottom: 0; 
	                    font-size: 11pt;
	                    margin-left: 20%;
	                '>CORPORACION CCS RECICLA</p>
	            </td>
	            
	            <td style='width: 40%; text-align: left; border: none; padding: 0;'>
	                <div style='font-family: Arial, sans-serif;
	                    border-bottom: 1px solid #255E2B; 
	                    width: 60%; 
	                    margin: 0 0 0 20%; /* Desactiva el centrado, alineando a la izquierda */
	                    height: 1px;
	                '></div>
	                <p style='text-align: center; font-family: Arial, sans-serif;
	                    color: #255E2B; 
	                    margin-top: 5px; 
	                    margin-bottom: 0; 
	                    font-size: 11pt;
	                    margin-left: 5%;
	                '>ECO INNOVA</p>
	                </td>
	                
	           <td style='width: 30%; text-align: left; border: none; padding: 0;'>
	                <div style='font-family: Arial, sans-serif;
	                    border-bottom: 1px solid #255E2B; 
	                    width: 60%; 
	                    margin: 0 0 0 20%; /* Desactiva el centrado, alineando a la izquierda */
	                    height: 1px;
	                '></div>
	                <p style='font-family: Arial, sans-serif;
	                    color: #255E2B; 
	                    margin-top: 5px; 
	                    margin-bottom: 0; 
	                    font-size: 11pt;
	                    margin-left: 20%;
	                '>SUMINISTRADOR</p>
	            </td>
	        </tr>
	    </table>
	</div>";



	$contenidoHTML2 = $contenidoHTML3;

}else{
	$contenidoHTML2 = "";
}




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

		<tr>
			<td style='text-align: right; border: 0px; border-right: 1px solid #255E2B;'><i>FECHA:</i></td>
			<td style='text-align: right border: 1px; border-left: none;'>".$fecha."</td>
		</tr>

		<tr>
			<td style='text-align: right; border: 0px; border-right: 1px solid #255E2B;'><i>N° DE PRE-RECIBO:</i></td>
			<td style='text-align: right; border: 1px; border-left: none'>".$correlativo."</td>
		</tr>
	</table>

	<h5 style='font-family: Arial, sans-serif;
	text-align:center; color: #255E2B;'>GERENCIA DE GESTION COMERCIAL</h5>

	<table align='center'>

		<tr>
			<td>COMERCIO O INSTITUCION</td>
			<td>".$org."</td>
		</tr>

		<tr>
			<td>RESPONSABLE</td>
			<td>".$responsable."</td>
		</tr>

		<tr>
			<td>CARGO</td>
			<td>".$cargo."</td>
		</tr>

		<tr>
			<td>DIRECCION</td>
			<td>".$direccion."</td>
		</tr>

	</table>

	<h5 style='font-family: Arial, sans-serif;
	text-align:center; color: #255E2B;'>DETALLES DEL MATERIAL RETIRADO</h5>

	<table align='center'>

		<tr>
			<td>TIPO DE MATERIAL RECIBIDO</td>
			<td>CANTIDAD KG</td>
			<td>PUNTOS</td>
		</tr>".$filasMateriales."
		        <tr>
        	<td>TOTALES</td>
        	<td>".$total_kg."</td>
        	<td>".$total_p."</td>
        </tr>


	</table>

<div style='font-family: Arial, sans-serif; margin-top: 10px; color: #255E2B;'>
        <p style='font-weight: bold; margin-bottom: 2px;'>Consideraciones:</p>
        <ol class='consideraciones-ol'>
            <li>El peso plasmado en esta Pre-factura será actualizado luego que el material culmine su proceso de clasificación, pudiendo mermar su cantidad.</li>
            <li>Cuando el material sea HIERRO una persona autorizada por el Comité podrá acompañar el traslado hasta el almacén y verificar el peso.</li>
            <li>En el lapso de 15 días será entregado el recibo y quedará sin efecto este pre-recibo.</li>
            <li>El cálculo del puntaje está sujeto a cambios que serán debidamente notificados.</li>
            <li>Una vez entregado y trasladado el material a nuestros depósitos, pasa de inmediato al proceso de clasificación y no es posible devolverlo.</li>
        </ol>
    </div>

    <p style='margin-top: 10px; color:
    #255E2B; margin-bottom: 0;'>Leído y conforme firman,</p>

    <table style='width: 100%; margin-top: 40px; border-collapse: collapse;'>
        <tr>
            <td style='width: 50%; text-align: left; border: none; padding: 0;'>
                <div style='
                	font-family: Arial, sans-serif;
                    border-bottom: 1px solid #255E2B; 
                    width: 60%; 
                    margin: 0 0 0 20%; /* Desactiva el centrado, alineando a la izquierda */
                    height: 1px;
                '></div>
                <p style='
                	font-family: Arial, sans-serif;
                    color: #255E2B; 
                    margin-top: 5px; 
                    margin-bottom: 0; 
                    font-size: 11pt;
                    margin-left: 20%;
                '>CORPORACION CCS RECICLA</p>
            </td>
            
            <td style='width: 50%; text-align: left; border: none; padding: 0;'>
                <div style='font-family: Arial, sans-serif;
                    border-bottom: 1px solid #255E2B; 
                    width: 60%; 
                    margin: 0 0 0 20%; /* Desactiva el centrado, alineando a la izquierda */
                    height: 1px;
                '></div>
                <p style='font-family: Arial, sans-serif;
                    color: #255E2B; 
                    margin-top: 5px; 
                    margin-bottom: 0; 
                    font-size: 11pt;
                    margin-left: 20%;
                '>SUMINISTRADOR</p>
            </td>
        </tr>
    </table>
".$contenidoHTML2;



$html2pdf->writeHTML($contenidoHTML);
$html2pdf->output();

?>