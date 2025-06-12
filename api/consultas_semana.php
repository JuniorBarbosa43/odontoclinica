<?php
// api/consultas_semana.php
header('Content-Type: application/json');
session_start();
require_once '../core/db_connection.php';

// Garante que apenas usuários logados acessem
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit();
}

$id_dentista = $_SESSION['user_id'];

// Pega a data de início da semana via GET, com validação
$startDateStr = $_GET['start_date'] ?? date('Y-m-d');
$startDate = DateTime::createFromFormat('Y-m-d', $startDateStr);

if (!$startDate) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Formato de data inválido. Use AAAA-MM-DD.']);
    exit();
}

// Calcula a data de fim da semana (6 dias depois)
$endDate = clone $startDate;
$endDate->modify('+6 days');

// Prepara a query para buscar consultas no intervalo da semana
$stmt = $conn->prepare(
    "SELECT c.id, c.data_consulta, c.procedimento, p.nome as nome_paciente 
     FROM consultas c
     JOIN pacientes p ON c.id_paciente = p.id
     WHERE c.id_dentista = ? AND c.data_consulta BETWEEN ? AND ?"
);

$startDateFormatted = $startDate->format('Y-m-d 00:00:00');
$endDateFormatted = $endDate->format('Y-m-d 23:59:59');

$stmt->bind_param("iss", $id_dentista, $startDateFormatted, $endDateFormatted);
$stmt->execute();
$result = $stmt->get_result();

$consultas = [];
while ($row = $result->fetch_assoc()) {
    $consultas[] = $row;
}

$stmt->close();
$conn->close();

// Retorna os dados em formato JSON
echo json_encode($consultas);
?>