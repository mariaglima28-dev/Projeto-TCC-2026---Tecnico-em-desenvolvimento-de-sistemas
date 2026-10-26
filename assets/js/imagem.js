
const inputFoto = document.getElementById('foto');
const previewFoto = document.getElementById('previewFoto');
const mensagemFoto = document.getElementById('mensagemFoto');

inputFoto.addEventListener('change', async function () {
    const arquivo = this.files[0];

    if (!arquivo) return;

    const tiposPermitidos = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!tiposPermitidos.includes(arquivo.type)) {
        mensagemFoto.textContent = 'Escolha uma imagem JPG, PNG ou WEBP.';
        this.value = '';
        return;
    }

    if (arquivo.size > 2 * 1024 * 1024) {
        mensagemFoto.textContent = 'A imagem deve ter no máximo 2 MB.';
        this.value = '';
        return;
    }

    const fotoAnterior = previewFoto.src;
    const urlTemporaria = URL.createObjectURL(arquivo);

    previewFoto.src = urlTemporaria;
    mensagemFoto.textContent = 'Enviando foto...';

    const dados = new FormData();
    dados.append('foto', arquivo);

    try {
        // Enviar para o PHP que salva a imagem no banco
        const resposta = await fetch('uploadFoto.php', {
            method: 'POST',
            body: dados,
            credentials: 'same-origin'
        });

        // Ler a resposta uma única vez
        const texto = await resposta.text();

        let resultado;

        try {
            resultado = JSON.parse(texto);
        } catch {
            console.error('Resposta recebida do PHP:', texto);
            throw new Error(
                'O servidor não retornou JSON válido. Verifique o uploadFoto.php.'
            );
        }

        if (!resposta.ok || !resultado.sucesso) {
            throw new Error(
                resultado.mensagem || 'Não foi possível atualizar a foto.'
            );
        }

        // Exibir a imagem retornada pelo PHP
        previewFoto.src = resultado.foto;
        mensagemFoto.textContent = resultado.mensagem;

    } catch (erro) {
        previewFoto.src = fotoAnterior;

        mensagemFoto.textContent =
            erro.message || 'Erro ao enviar a foto.';

        console.error('Erro no upload:', erro);

    } finally {
        URL.revokeObjectURL(urlTemporaria);
        this.value = '';
    }
});
