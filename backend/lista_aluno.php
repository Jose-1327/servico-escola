<?php
include 'conexao.php';

$sql = "SELECT * FROM alunos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Idade</th><th>Turma</th></tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["nome"] . "</td>";
        echo "<td>" . $row["idade"] . "</td>";
        echo "<td>" . $row["turma"] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Nenhum aluno cadastrado.";
}

$conn->close();
?>
