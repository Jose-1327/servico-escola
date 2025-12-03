<?php
session_start();
require_once 'conexao.php'; // Inclui o arquivo de conexão

// Função de segurança para limpar dados de entrada
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = conectar_bd();
    cadastrar_usuario_teste($conn); // Executa a criação do usuário de teste

    $usuario_digitado = sanitize($_POST['usuario']);
    $senha_digitada = sanitize($_POST['senha']);

    // 1. Prepara a consulta para evitar injeção de SQL
    $stmt = $conn->prepare("SELECT id, usuario, senha_hash, tipo_usuario FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_digitado);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // 2. Se o usuário foi encontrado, liga os resultados
        $stmt->bind_result($user_id, $username, $hashed_password, $user_type);
        $stmt->fetch();

        // 3. Verifica se a senha corresponde ao hash armazenado
        if (password_verify($senha_digitada, $hashed_password)) {
            
            // Sucesso no login: Cria as variáveis de sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $user_type; // Importante para controle de acesso

            // 4. Redireciona o usuário com base no tipo
            if ($user_type === 'Secretario' || $user_type === 'Admin') {
                header("Location: ../alunos.html"); // Redireciona para a tela de gestão de alunos
            } elseif ($user_type === 'Aluno') {
                header("Location: ../historico.html"); // Redireciona para a tela do aluno
            } else {
                header("Location: ../index.html?erro=acesso"); // Caso o tipo seja inválido
            }
            exit;

        } else {
            // Senha incorreta
            header("Location: ../index.html?erro=invalido");
            exit;
        }
    } else {
        // Usuário não encontrado
        header("Location: ../index.html?erro=invalido");
        exit;
    }

    $stmt->close();
    $conn->close();

} else {
    // Se a página for acessada diretamente sem POST, redireciona para o login
    header("Location: ../index.html");
    exit;
}
?>