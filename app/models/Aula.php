<?php
require_once 'Turmas.php';
require_once 'Atividade.php';

class Aula {
    private $id;
    private $titulo;

    private Turma $turma;
    private $atividades = [];

    public function __construct($id,$titulo,$turma){
        $this->id = $id;
        $this->titulo = $titulo;
        $this->turma = $turma;
        $turma->adicionarAula($this);
    }

    public function adicionarAtividade(Atividade $atividade){
        $this->atividades[] = $atividade;       
    }
}


?>