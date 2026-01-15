<?php
$conn = mysqli_connect("localhost", "root", "", "proyecto_imc");
if (!$conn) { die("Error de conexión: " . mysqli_connect_error()); }

$nombre = $edad = $direccion = $peso = $talla = "";
$edit_id = null;

if (isset($_POST['guardar'])) {
    $nombre = trim($_POST['nombre']);
    $edad = trim($_POST['edad']);
    $direccion = trim($_POST['direccion']);
    $peso = trim($_POST['peso']);
    $talla = trim($_POST['talla']);

    if (!empty($nombre) && !empty($edad) && !empty($direccion) && !empty($peso) && !empty($talla)) {
        if (!empty($_POST['edit_id'])) {
            $id = intval($_POST['edit_id']);
            mysqli_query($conn, "UPDATE usuario SET nombre='$nombre', edad='$edad', direccion='$direccion', peso='$peso', talla='$talla' WHERE id=$id");
        } else {
            mysqli_query($conn, "INSERT INTO usuario (nombre, edad, direccion, peso, talla) VALUES ('$nombre','$edad','$direccion','$peso','$talla')");
        }
        header("Location: index.php"); exit;
    }
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    mysqli_query($conn, "DELETE FROM usuario WHERE id=$id");
    header("Location: index.php"); exit;
}

if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = mysqli_query($conn, "SELECT * FROM usuario WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_id = $row['id'];
        $nombre = $row['nombre'];
        $edad = $row['edad'];
        $direccion = $row['direccion'];
        $peso = $row['peso'];
        $talla = $row['talla'];
    }
}

$result = mysqli_query($conn, "SELECT * FROM usuario ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro IMC</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>

<h2><?php echo $edit_id ? "Editar Usuario" : "Registrar Usuario"; ?></h2>

<form method="post" class="formulario">
<input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

<label>Nombre:</label>
<input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

<label>Edad:</label>
<input type="text" name="edad" value="<?php echo htmlspecialchars($edad); ?>" required>

<label>Dirección:</label>
<input type="text" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>" required>

<label>Peso (kg):</label>
<input type="text" name="peso" value="<?php echo htmlspecialchars($peso); ?>" required>

<label>Talla (m):</label>
<input type="text" name="talla" value="<?php echo htmlspecialchars($talla); ?>" required>

<button type="submit" name="guardar">Guardar</button>
</form>

<table>
<tr>
<th>ID</th><th>Nombre</th><th>Edad</th><th>Dirección</th><th>Peso</th><th>Talla</th>
<th>IMC</th><th>Estado</th><th>Imagen</th><th>Recomendación</th><th>Institución</th><th>Acciones</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { 
$peso_val = floatval($row['peso']);
$talla_val = floatval($row['talla']);
$imc = ($talla_val>0)?round($peso_val/($talla_val*$talla_val),2):0;

if ($imc < 18.5) {
    $clas="bajo"; $rec="Aumentar alimentos nutritivos, comer 5 veces al día y acudir a un nutriólogo.";
    $inst="https://www.gob.mx/salud"; $img="bajo.png";
} elseif ($imc < 25) {
    $clas="normal"; $rec="Mantener dieta balanceada, beber agua y hacer ejercicio 3 veces por semana.";
    $inst="https://www.gob.mx/salud"; $img="normal.png";
} elseif ($imc < 30) {
    $clas="sobrepeso"; $rec="Reducir grasas, caminar diario y acudir al IMSS.";
    $inst="https://www.imss.gob.mx"; $img="sobrepeso.png";
} else {
    $clas="obesidad"; $rec="Consultar médico y seguir plan alimenticio.";
    $inst="https://www.gob.mx/salud/insabi"; $img="obesidad.png";
}
?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo htmlspecialchars($row['nombre']); ?></td>
<td><?php echo $row['edad']; ?></td>
<td><?php echo htmlspecialchars($row['direccion']); ?></td>
<td><?php echo $row['peso']; ?></td>
<td><?php echo $row['talla']; ?></td>
<td><?php echo $imc; ?></td>
<td class="<?php echo $clas; ?>"><?php echo ucfirst($clas); ?></td>
<td><img src="<?php echo $img; ?>" width="60"></td>
<td><?php echo $rec; ?></td>
<td><a href="<?php echo $inst; ?>" target="_blank">Ver institución</a></td>
<td>
<a href="index.php?editar=<?php echo $row['id']; ?>">Editar</a>
<a href="index.php?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
</td>
</tr>
<?php } ?>
</table>
</body>
</html>