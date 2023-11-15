<?php

session_start();
require_once 'conexao.php';

$usuario = trim($_POST['usuario'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if(empty($usuario) || empty($senha)){
    header('location: index.php');
}

$stmt = $bd->prepare("  SELECT *
                        FROM tb_usuario
                        WHERE  usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$val = $stmt->fetch(PDO::FETCH_ASSOC);

if( password_verify($senha, $val['senha']) ){
    $_SESSION['id_usuario'] = $val['id_usuario'];
    $_SESSION['id_nucleo'] = $val['id_nucleo'];
    header('location: index.php');
    
}