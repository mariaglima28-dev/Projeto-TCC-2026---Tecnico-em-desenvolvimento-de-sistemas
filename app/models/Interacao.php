<?php 
require_once 'Aluno.php';
require_once 'Professor.php';
require_once 'Turmas.php';


class Interacao {
    private $id;
    private $tipoUser;
    private Turma $turma;
    private $pergunta;
    private $resposta;

    public function __construct($id,  $tipoUser, Turma $turma, $pergunta) {
        $this->id = $id;
        $this->tipoUser = $tipoUser;
        $this->turma = $turma;
        $this->pergunta = $pergunta;
        $this->resposta = null; // Inicialmente a resposta não existe
    }

    public function reponder($responta) {
        $this->resposta = $responta;
    }
    
    public function getPergunta(){
       return $this->pergunta;
    }

    public function getResposta(){
        return $this->resposta;
    }

    public function getAluno(){
        return $this->aluno;
    }
}




?>