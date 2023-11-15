<?php


require_once 'conexao.php';


$id_financa = $_POST['id_financa'];
// Consulta SQL para buscar informações do usuário com base no ID
$sql = "SELECT * FROM tb_financas f
LEFT JOIN tb_usuario u ON f.id_usuario = u.id_usuario
LEFT JOIN tb_cursistas c ON f.id_cursista = c.id_cursista WHERE id_financa = :id_financa";
$stmt = $bd->prepare($sql);
$stmt->bindParam(':id_financa', $id_financa, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($result);
