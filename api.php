<?php
$servername = "sql311.infinityfree.com";  // MySQL Hostname
$username   = "if0_39913839";             // MySQL Username
$password   = "6docQ8i1d";                // MySQL Password
$database   = "if0_39913839_106";         // MySQL Database Name

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $database);

if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "msg" => "Conexión fallida"]);
    exit;
}

// Leer datos JSON enviados desde Apps Script o Voiceflow
$data = json_decode(file_get_contents("php://input"), true);

// Sanitizar
$edad             = (int)$data["edad"];
$genero           = $conexion->real_escape_string($data["genero"]);
$region           = $conexion->real_escape_string($data["region"]);
$educacion        = $conexion->real_escape_string($data["educacion"]);
$trabajando       = $conexion->real_escape_string($data["trabajando"]);
$razon_desempleo  = $conexion->real_escape_string($data["razon_desempleo"]);
$busqueda_empleo  = $conexion->real_escape_string($data["busqueda_empleo"]);
$medio_busqueda   = $conexion->real_escape_string($data["medio_busqueda"]);
$movilidad        = $conexion->real_escape_string($data["movilidad"]);
$apoyo_preferido  = $conexion->real_escape_string($data["apoyo_preferido"]);
$source           = $conexion->real_escape_string($data["source"]);

// Insertar en la BD
$sql = "INSERT INTO desempleo_jovenes 
(edad, genero, region, educacion, trabajando, razon_desempleo, busqueda_empleo, medio_busqueda, movilidad, apoyo_preferido, source) 
VALUES ('$edad', '$genero', '$region', '$educacion', '$trabajando', '$razon_desempleo', '$busqueda_empleo', '$medio_busqueda', '$movilidad', '$apoyo_preferido', '$source')";

$response = [];

if ($conexion->query($sql) === TRUE) {
    // Generar recomendaciones personalizadas
    $recomendacion = "";

    if ($trabajando === "No" && $educacion === "Universitario") {
        $recomendacion .= "👉 Recomendamos prácticas preprofesionales, LinkedIn y ferias de empleo. ";
    }
    if (stripos($razon_desempleo, "experiencia") !== false || stripos($busqueda_empleo, "experiencia") !== false) {
        $recomendacion .= "👉 Voluntariados, pasantías y programas de capacitación gratuita. ";
    }
    if (strtolower($region) === "provincia" || strtolower($region) === "provincias") {
        $recomendacion .= "👉 Explora programas descentralizados como Jóvenes Productivos. ";
    }

    $response = [
        "status" => "ok",
        "edad" => $edad,
        "genero" => $genero,
        "region" => $region,
        "educacion" => $educacion,
        "trabajando" => $trabajando,
        "razon_desempleo" => $razon_desempleo,
        "busqueda_empleo" => $busqueda_empleo,
        "medio_busqueda" => $medio_busqueda,
        "movilidad" => $movilidad,
        "apoyo_preferido" => $apoyo_preferido,
        "source" => $source,
        "recomendacion" => trim($recomendacion)
    ];
} else {
    $response = ["status" => "error", "msg" => $conexion->error];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conexion->close();
?>
