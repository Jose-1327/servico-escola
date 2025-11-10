<?php
include 'conexao.php';

$nome = $_POST['nome'];
$idade = $_POST['idade'];
$turma = $_POST['turma'];

$sql = "INSERT INTO alunos (nome, idade, turma) VALUES ('$nome', '$idade', '$turma')";

if ($conn->query($sql) === TRUE) {
    echo "Aluno cadastrado com sucesso!";
} else {
    echo "Erro: " . $conn->error;
}

$conn->close();
?>
