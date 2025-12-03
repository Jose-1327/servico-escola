<?php
// Define as credenciais de conexão com o banco de dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Mude para o seu usuário do MySQL
define('DB_PASSWORD', '');     // Mude para a sua senha do MySQL
define('DB_NAME', 'escola');   // O nome do banco de dados que criamos

/**
 * Abre e retorna uma nova conexão MySQLi.
 * @return mysqli O objeto de conexão.
 */
function conectar_bd() {
    // Tenta estabelecer a conexão
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        // Encerra o script e exibe o erro
        die("ERRO DE CONEXÃO COM O BANCO DE DADOS: " . $conn->connect_error);
    }

    // Define o charset para UTF-8 para evitar problemas de acentuação
    $conn->set_charset("utf8mb4");

    return $conn;
}

// Exemplo de uso (opcional, pode ser removido)
// $conn = conectar_bd();
// echo "Conexão bem-sucedida!";
// $conn->close();
?>