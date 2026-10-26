<?php
require_once 'Aulas.php';
require_once 'Desempenho.php';

class Atividade {
    private $id;
    private $enunciado;
    private $data;
    private Aulas $aula;
    private $desempenhos = [];
    private $db;

    public function __construct($id, $enunciado, $data, Aulas $aula, $db){
        $this->id = $id;
        $this->enunciado = $enunciado;
        $this->data = $data;
        $this->aula = $aula;
        $aula->adicionarAtividade($this);
        $this->db = $db;
    }

     public function listar(){
        $sql = "SELECT * FROM atividades ORDER BY data_postagem DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarDesempenho(Desempenho $desempenho){
        $this->desempenhos[] = $desempenho;
    }

    public function getDesempenhos(){
        return $this->desempenhos;
    }

    public function getData(){
        return $this->data;
    }

    public function getAtividade(){
        return $this->atividades[] = $atividade;   
    }
}


?>