<?php
// Inicia a sessão para acessar as variáveis de sessão existentes
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se for desejado destruir completamente a sessão, também é necessário
// destruir o cookie de sessão. Nota: Isto irá destruir a sessão,
// e não apenas os dados da sessão.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();

// Redireciona o usuário para a página de login
header("Location: ../index.html");
exit;
?>