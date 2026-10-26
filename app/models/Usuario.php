<?php
//No caso o Usuario é o Gestor/Diretor da escola
class Usuario{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $dataNasc;
    private $tipoUser;
    private $instituicao;

    public function __construct($nome,$email,$senha,$cpf,$dataNasc,$tipoUser, $instituicao) {
        $this->id = rand(1, 1000);
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cpf = $cpf;
        $this->dataNasc = $dataNasc;
        $this->tipoUser = $tipoUser;
        $this->instituicao = $instituicao;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getSenha(){
        return $this->senha;
    }

    public function getCpf(){
        return $this->cpf;
    }

    public function dataNasc(){
        return $this->dataNasc;
    }

    public function getInstituicao(){
        return $this->instituicao;
    }

    public function setNome($nome){
        $this->nome = $nome;
    }

    public function setEmail($email){
        $this->email = $email;
    }
    
    public function setCpf($cpf){
        $this->cpf = $cpf;
    }
}

?>