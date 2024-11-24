<?php 
require_once "../modelos/Consultas.php";

$consulta = new Consultas();

switch ($_GET["op"]) {
    case 'comprasfecha':
        $fecha_inicio = $_REQUEST["fecha_inicio"];
        $fecha_fin = $_REQUEST["fecha_fin"];

        $rspta = $consulta->comprasfecha($fecha_inicio, $fecha_fin);
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fecha,
                "1" => $reg->usuario,
                "2" => $reg->proveedor,
                "3" => $reg->tipo_comprobante,
                "4" => $reg->serie_comprobante.' '.$reg->num_comprobante,
                "5" => $reg->total_compra,
                "6" => $reg->impuesto,
                "7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>'
            );
        }
        $results = array(
            "sEcho" => 1, // info para datatables
            "iTotalRecords" => count($data), // enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), // enviamos el total de registros a visualizar
            "aaData" => $data
        ); 
        echo json_encode($results);
        break;

    case 'ventasfechacliente':
        $fecha_inicio = $_REQUEST["fecha_inicio"];
        $fecha_fin = $_REQUEST["fecha_fin"];
        $idcliente = $_REQUEST["idcliente"];

        $rspta = $consulta->ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente);
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fecha,
                "1" => $reg->usuario,
                "2" => $reg->cliente,
                "3" => $reg->tipo_comprobante,
                "4" => $reg->serie_comprobante.' '.$reg->num_comprobante,
                "5" => $reg->total_venta,
                "6" => $reg->impuesto,
                "7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>'
            );
        }
        $results = array(
            "sEcho" => 1, // info para datatables
            "iTotalRecords" => count($data), // enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), // enviamos el total de registros a visualizar
            "aaData" => $data
        ); 
        echo json_encode($results);
        break;

    // Caso default para manejar valores inesperados
    default:
        // Se puede agregar un mensaje de error o un log para detectar si hay un valor inesperado en la variable 'op'
        echo json_encode(array("error" => "Operación no válida o no definida. Se recibió un valor desconocido en el parámetro 'op'."));
        break;
}
?>
