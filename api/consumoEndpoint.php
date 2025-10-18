<?php
function getApiData(string $url, string $bearerToken): ?array
{
    // Set up the HTTP headers for the request.
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer " . $bearerToken . "\r\n" .
                        "Accept: application/json\r\n"
        ]
    ];

    // Create a stream context with the specified options.
    $context = stream_context_create($options);

    // Make the request and get the response.
    $response = @file_get_contents($url, false, $context);

    // Check if the request failed.
    if ($response === false) {
        return null;
    }

    // Decode the JSON response into an associative array.
    $data = json_decode($response, true);

    // Check for JSON decoding errors.
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    return $data;
}

/*
<?php
include_once('api/consumoEndpoint.php');
// --- Example usage of the function ---
$endpoint = 'https://udp.coningenio.cl/api/v1/proyecto/';
$token = 'udp.2025';

$proyectos = getApiData($endpoint, $token);

if ($proyectos !== null) {
    echo '<div class="row">';
    foreach ($proyectos as $proyecto) {
        if($proyecto['activo']){
            $tarjeta = "
                <div class='col-4 g-4'>
                    <div class='card border-danger'>
                        <div class='card-header text-center bg-danger text-white'>
                            Proyecto Semestral
                        </div>
                        <div class='card-body'>
                            <h5 class='card-title text-center'>" . htmlspecialchars($proyecto['nombre']) . "</h5>
                            <hr>
                            " . htmlspecialchars($proyecto['descripcion']) . "
                            <hr>
                            <strong>Integrantes</strong>
                            " . htmlspecialchars($proyecto['integrantes']) . "
                        </div>
                        <div class='card-footer'>
                            <a href='".htmlspecialchars($proyecto['url'])."' target='_blank'><button class='btn btn-dark w-100'><i class='fa fa-link'></i> Acceso</button></a>
                        </div>
                    </div>
                </div>
            ";
            echo $tarjeta;
        }
    }
    echo '</div>';
} else {
    echo "Error: No se pudo obtener la información. Por favor, revisa la URL y el token.";
}

?>
*/

?>
