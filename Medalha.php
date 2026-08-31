<?php
class Medalha {

    private $nome;
    private $descricao;
    private $objetivo;
    private $progresso;

    public function __construct(
        $nome,
        $descricao,
        $objetivo,
        $progresso
    ) {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->objetivo = $objetivo;
        $this->progresso = $progresso;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function calcularProgresso() {

        $porcentagem = ($this->progresso / $this->objetivo) * 100;

        return min($porcentagem, 100);
    }

    public function estaConquistada() {

        if ($this->progresso >= $this->objetivo) {
            return true;
        }

        return false;
    }
}
?>