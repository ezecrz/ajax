<?php 
require_once "../modelos/Permiso.php";

$categoria = new Permiso();

switch ($_GET["op"]) {
    case 'listar':
        $rspta = $categoria->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->nombre
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

    // Agrega la cláusula default para manejar los casos no definidos
    default:
        // Acción predeterminada en caso de que no se pase un valor válido en $_GET["op"]
        echo json_encode(array("error" => "Opción no válida"));
        break;
}
?>
