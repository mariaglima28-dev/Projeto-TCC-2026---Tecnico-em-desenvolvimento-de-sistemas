<?php
require_once 'Aluno.php';
require_once 'Perfil.php';

class Meta{
    private $id;
    private $descricao;
    private $nome;
    private $pontuacaoMin; //seria o valor minimo para atingir a meta
    private Aluno $aluno;
    private $data;
    private $conquistas = [];

    public function __construct($id, $descricao, $nome, $pontuacaoMin, Aluno $aluno, $data){
        $this->id = $id;
        $this->descricao = $descricao;
        $this->nome = $nome;
        $this->pontuacaoMin;
        $this->aluno = $aluno;
        $this->data = $data;
    }

    public function Todos(){
        return [
            $this->id,
            $this->descricao,
            $this->nome,
            $this->pontuacaoMin,
            $this->aluno,
            $this->data
        ];
    }

    public function getTodos(){
        return $this->Todos();
    }

    public function Setters($descricao, $nome, $pontuacaoMin, $aluno, $data){
        return [
            $this->descricao = $descricao, 
            $this->nome = $nome,
            $this->pontuacaoMin = $pontuacaoMin,
            $this->aluno = $aluno,
            $this->data = $data
        ];
    }


    public function adicionarConquista(Metas $meta){
        $this->conquistas[] = $meta;
    }
    
}

$meta1 = new Metas(1, "Estudar para a prova", "Meta para estudo", 80, $aluno, "2026-06-22");

echo "<pre>";
print_r($meta1->getTodos());
echo "</pre>";


?>