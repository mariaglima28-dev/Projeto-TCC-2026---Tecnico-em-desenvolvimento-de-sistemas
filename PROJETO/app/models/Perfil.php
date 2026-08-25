<?php
require_once 'Aluno.php';
require_once 'Metas.php';

class Perfil {
    private $id;
    private Aluno $aluno;
    private $dificuldades;
    private $preferencias;
    private $nivel; // seria o nivel da Aprendizagem de cada aluno
    private $conquistas=[];
    private $foto;

    public function __construct($id, Aluno $aluno, $dificuldades,$preferencias,$nivel, $conquistas, $foto){
        $this->id = $id;
        $this->aluno = $aluno;
        $this->dificuldades = $dificuldades;
        $this->preferencias = $preferencias;
        $this->nivel = $nivel;
        $this->conquistas = $conquistas;
        $this->foto = $foto;
    }

    public function Todos(){
        return [
            $this->id,
            $this->aluno,
            $this->dificuldades,
            $this->preferencias,
            $this->nivel,
            $this->conquistas
        ];
    }

    public function getDificuldades(){
        return $this->dificuldades;
    }

    public function getPreferencias(){
        return $this->preferencias;
    }

    public function getNivel(){
        return $this->nivel;
    }

    public function getConquistas(){
        return $this->conquistas;
    }

    public function getFoto(){
        return $this->foto;
    }
}



?>