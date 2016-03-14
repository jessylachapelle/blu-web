<?php
/**
* 
*/
class Telephone {
  private $_id;
  private $_numero;
  private $_note;
  
  public function __construct() {
    $this->_id = null;
    $this->_numero = null;
    $this->_note = null;
  }
  
  public function getId(){
    return $this->_id;
  }

  public function setId($id){
    $this->_id = $id;
  }
  
  public function getNumero(){
    return $this->_numero;
  }

  public function setNumero($numero){
    $this->_numero = $numero;
  }

  public function getNote(){
    return $this->_note;
  }

  public function setNote($note){
    $this->_note = $note;
  }
}
?>