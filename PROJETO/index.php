
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Acesso Profissional</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://google.com" async defer></script>
</head>
<body>

    <img src="assets/img/Qualificação TCC.svg" class="bg-canvas">
    <main class="main-workspace">
        <div class="auth-card">
            <div class="card-header">
                <h2>Bem-vindo(a)</h2>
                <p>Insira suas credenciais para acessar o sistema.</p>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert-error">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            <div id="g_id_onload" data-client_id="949242625796-3tqep1q9beie1cn0ivjd32i7o7isb6kn.apps.googleusercontent.com" data-callback="handleCredentialResponse"></div>
            <div class="g_id_signin" data-type="standard"></div>
        </div>
    </main>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="assets/js/script.js"></script>
</body>
</html>