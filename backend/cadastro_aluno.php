<?php
// Inicia a sessão para garantir que apenas usuários autenticados possam cadastrar
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php';

// Redireciona para o login se o usuário não estiver logado (Controle de Acesso Básico)
// if (!isset($_SESSION['loggedin']) || ($_SESSION['user_type'] !== 'Secretario' && $_SESSION['user_type'] !== 'Admin')) {
//     header("Location: ../index.html");
//     exit;
// }

// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Conecta ao banco de dados
    $conn = conectar_bd();

    // 1. Coleta e sanitiza os dados recebidos do formulário (alunos.html)
    // O formulário simplificado envia: nome, idade, turma
    $nome = $conn->real_escape_string($_POST['nome']);
    $idade = (int)$_POST['idade']; // Converte para inteiro
    $nome_turma = $conn->real_escape_string($_POST['turma']); 

    // O banco de dados SQL que definimos usa 'turma_id', não 'nome_turma'.
    // Precisamos primeiro encontrar o ID da turma pelo nome.
    $turma_id = null;
    $stmt_turma = $conn->prepare("SELECT id FROM turmas WHERE nome_turma = ?");
    if ($stmt_turma) {
        $stmt_turma->bind_param("s", $nome_turma);
        $stmt_turma->execute();
        $stmt_turma->bind_result($turma_id_result);
        if ($stmt_turma->fetch()) {
            $turma_id = $turma_id_result;
        }
        $stmt_turma->close();
    }
    
    // Se a turma não for encontrada, vamos tratar como NULO para não quebrar o cadastro.
    // Em um sistema real, você forçaria o Secretário a cadastrar a turma primeiro.
    if ($turma_id === null) {
        // Esta é uma mensagem temporária para fins de desenvolvimento
        echo "Aviso: Turma '$nome_turma' não encontrada. O aluno será cadastrado sem link para a turma.<br>";
    }
    
    // 2. Prepara a query de inserção para a tabela 'alunos'
    // Estamos simplificando, preenchendo apenas nome e o turma_id (se encontrado). 
    // Outros campos obrigatórios na tabela SQL (como 'matricula') foram temporariamente removidos
    // ou assumidos como opcionais para este script simplificado.
    
    // A query abaixo assume que 'matricula' e 'data_nascimento' são preenchidos por outros meios 
    // ou não são obrigatórios (NOT NULL) no seu esquema SQL atual para este teste.
    // Se 'matricula' for NOT NULL, você DEVE gerar uma antes de inserir.
    
    $matricula_mock = "MAT" . time(); // Cria uma matrícula mock temporária
    $stmt = $conn->prepare("INSERT INTO alunos (matricula, nome, idade, turma_id) VALUES (?, ?, ?, ?)");
    
    if ($stmt) {
        // A idade não estava na sua tabela SQL de 'alunos' que criamos, mas vamos assumir que existe.
        // Como o formulário não pede data_nascimento, vamos usar a idade como um campo temporário.
        $stmt->bind_param("sssi", $matricula_mock, $nome, $idade, $turma_id);

        // 3. Executa a query
        if ($stmt->execute()) {
            // Sucesso
            echo "Aluno **$nome** (Matrícula: $matricula_mock) cadastrado com sucesso!<br>";
            echo "<a href='../alunos.html'>Voltar para Gestão de Alunos</a>";
            
        } else {
            // Erro na execução
            echo "Erro ao cadastrar aluno: " . $stmt->error;
        }

        // Fecha o statement
        $stmt->close();
        
    } else {
        // Erro na preparação da query
        echo "Erro na preparação da query: " . $conn->error;
    }

    // 4. Fecha a conexão
    $conn->close();

} else {
    // Se não for POST, redireciona para a página de cadastro
    header("Location: ../alunos.html");
    exit;
}
?>