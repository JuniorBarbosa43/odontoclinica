<?php
// controllers/lembrete_controller.php
session_start();
require_once '../core/db_connection.php';
require_once '../core/session_check.php';

// Pega o ID da consulta pela URL
$id_consulta = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id_consulta) {
    header("Location: ../views/agenda.php?status=error");
    exit();
}

// Busca todas as informações necessárias da consulta, do paciente e do dentista
$stmt = $conn->prepare(
    "SELECT 
        c.data_consulta, c.procedimento,
        p.nome as nome_paciente, p.email as email_paciente,
        d.nome as nome_dentista
     FROM consultas c
     JOIN pacientes p ON c.id_paciente = p.id
     JOIN dentistas d ON c.id_dentista = d.id
     WHERE c.id = ?"
);
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$consulta = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$consulta) {
    header("Location: ../views/agenda.php?status=notfound");
    exit();
}

// Formatação dos dados para o e-mail
$data_formatada = (new DateTime($consulta['data_consulta']))->format('d/m/Y');
$hora_formatada = (new DateTime($consulta['data_consulta']))->format('H:i');
$nome_paciente = $consulta['nome_paciente'];
$email_paciente = $consulta['email_paciente'];
$nome_dentista = $consulta['nome_dentista'];
$procedimento = $consulta['procedimento'];

// Montagem do E-mail
$para = $email_paciente;
$assunto = "Lembrete de Consulta Odontológica";

// Corpo do e-mail em formato HTML
$mensagem = "
<html>
<head>
  <title>Lembrete de Consulta</title>
</head>
<body style='font-family: Arial, sans-serif;'>
  <h2>Olá, " . htmlspecialchars($nome_paciente) . "!</h2>
  <p>Este é um lembrete amigável sobre sua próxima consulta na OdontoClínica.</p>
  <table cellpadding='10' style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;'>
    <tr style='background-color: #f2f2f2;'>
      <th style='text-align: left; border: 1px solid #ddd;'>Data</th>
      <td style='border: 1px solid #ddd;'>" . $data_formatada . "</td>
    </tr>
    <tr>
      <th style='text-align: left; border: 1px solid #ddd;'>Hora</th>
      <td style='border: 1px solid #ddd;'>" . $hora_formatada . "</td>
    </tr>
    <tr style='background-color: #f2f2f2;'>
      <th style='text-align: left; border: 1px solid #ddd;'>Profissional</th>
      <td style='border: 1px solid #ddd;'>" . htmlspecialchars($nome_dentista) . "</td>
    </tr>
    <tr>
      <th style='text-align: left; border: 1px solid #ddd;'>Procedimento</th>
      <td style='border: 1px solid #ddd;'>" . htmlspecialchars($procedimento) . "</td>
    </tr>
  </table>
  <p>Por favor, chegue com alguns minutos de antecedência. Se precisar reagendar, entre em contato conosco.</p>
  <p>Atenciosamente,<br>Equipe OdontoClínica</p>
</body>
</html>
";

// Cabeçalhos para enviar e-mail em formato HTML
$cabecalhos = "MIME-Version: 1.0" . "\r\n";
$cabecalhos .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$cabecalhos .= 'From: <nao-responda@odontoclinica.com>' . "\r\n"; // E-mail do remetente

// Tenta enviar o e-mail
if (mail($para, $assunto, $mensagem, $cabecalhos)) {
    // Se o envio for bem-sucedido (no servidor de produção)
    header("Location: ../views/agenda.php?status=reminder_sent");
} else {
    // Se falhar (provável no XAMPP)
    header("Location: ../views/agenda.php?status=reminder_failed");
}
?>