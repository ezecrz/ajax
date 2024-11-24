<?php
require_once "../modelos/Articulo.php";
require_once "../modelos/Categoria.php";  // Añadido para el select de categoría

$articulo = new Articulo();
$categoria = new Categoria();

// Función de limpieza para evitar XSS e inyecciones
function limpiarCadena($cadena) {
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    $cadena = htmlspecialchars($cadena);
    return $cadena;
}

$idarticulo = isset($_POST["idarticulo"]) ? limpiarCadena($_POST["idarticulo"]) : "";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        // Validación de la imagen
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $imagen = $_POST["imagenactual"];
        } else {
            $ext = explode(".", $_FILES["imagen"]["name"]);
            $ext = strtolower(end($ext)); // Convertir a minúsculas para comparar de forma segura
            $allowed_types = ['jpg', 'jpeg', 'png'];  // Tipos de archivo permitidos

            // Verificar el tipo de archivo
            if (in_array($ext, $allowed_types) && ($_FILES['imagen']['size'] <= 2 * 1024 * 1024)) { // Máximo 2 MB
                $imagen = round(microtime(true)) . '.' . $ext;
                $upload_path = "../files/articulos/" . $imagen;
                if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $upload_path)) {
                    echo "Error al subir la imagen.";
                    exit;
                }
            } else {
                echo "El archivo no es válido o es demasiado grande.";
                exit;
            }
        }

        // Insertar o actualizar el artículo
        if (empty($idarticulo)) {
            $rspta = $articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
        } else {
            $rspta = $articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
        }
        break;

    case 'desactivar':
        // Desactivar el artículo
        $rspta = $articulo->desactivar($idarticulo);
        echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
        break;

    case 'activar':
        // Activar el artículo
        $rspta = $articulo->activar($idarticulo);
        echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
        break;

    case 'mostrar':
        // Mostrar los detalles de un artículo
        $rspta = $articulo->mostrar($idarticulo);
        echo json_encode($rspta);
        break;

    case 'listar':
        // Listar artículos
        $rspta = $articulo->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->condicion) ?
                    '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idarticulo . ')"><i class="fa fa-pencil"></i></button>' . ' ' .
                    '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idarticulo . ')"><i class="fa fa-close"></i></button>' :
                    '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idarticulo . ')"><i class="fa fa-pencil"></i></button>' . ' ' .
                    '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idarticulo . ')"><i class="fa fa-check"></i></button>',
                "1" => $reg->nombre,
                "2" => $reg->categoria,
                "3" => $reg->codigo,
                "4" => $reg->stock,
                "5" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>",
                "6" => $reg->descripcion,
                "7" => ($reg->condicion) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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

    case 'selectCategoria':
        // Cargar las categorías
        $rspta = $categoria->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->idcategoria . '">' . $reg->nombre . '</option>';
        }
        break;

    // Caso por defecto para manejar operaciones no válidas
    default:
        echo "Operación no válida.";
        http_response_code(400); // Opcional: devolver un código de error HTTP 400 (Bad Request)
        break;
}
?>
