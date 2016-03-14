<?php
/**
* 
*/
class Article {
  private $_id;
  private $_titre;
  private $_auteur;
  private $_edition;
  private $_annee;
  private $_editeur;
  private $_code;

  public function __construct() {
    $this->_id = null;
    $this->_titre = null;
    $this->_auteur = null;
    $this->_edition = null;
    $this->_annee = null;
    $this->_editeur = null;
    $this->_code = null;
  }

  public function getID(){
  	return $this->_id;
  }

  public function setID($id){
  	$this->_id = $id;
  }

  public function getTitre(){
  	return $this->_titre;
  }

  public function setTitre($titre){
  	$this->_titre = $titre;
  }

  public function getAuteur(){
  	return $this->_auteur;
  }

  public function setAuteur($auteur){
  	$this->_auteur = $auteur;
  }

  public function getEdition(){
  	return $this->_edition;
  }

  public function setEdition($edition){
  	$this->_edition = $edition;
  }

  public function getAnnee(){
  	return $this->_annee;
  }

  public function setAnnee($annee){
  	$this->_annee = $annee;
  }

  public function getEditeur(){
  	return $this->_editeur;
  }

  public function setEditeur($editeur){
  	$this->_editeur = $editeur;
  }

  public function getCode(){
  	return $this->_code;
  }

  public function setCode($code){
  	$this->_code = $code;
  }
}

?>