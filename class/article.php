<?php
/**
*
*/
class Article {
  private $_id;
  private $_title;
  private $_author;
  private $_edition;
  private $_year;
  private $_editor;
  private $_code;

  public function __construct() {
    $this->_id = null;
    $this->_title = null;
    $this->_author = null;
    $this->_edition = null;
    $this->_year = null;
    $this->_editor = null;
    $this->_code = null;
  }

  public function getID(){
  	return $this->_id;
  }

  public function setID($id){
  	$this->_id = $id;
  }

  public function getTitle(){
  	return $this->_title;
  }

  public function setTitle($title){
  	$this->_title = $title;
  }

  public function getAuthor(){
  	return $this->_author;
  }

  public function setAuthor($author){
  	$this->_author = $author;
  }

  public function getEdition(){
  	return $this->_edition;
  }

  public function setEdition($edition){
  	$this->_edition = $edition;
  }

  public function getYear(){
  	return $this->_year;
  }

  public function setYear($year){
  	$this->_year = $year;
  }

  public function getEditor(){
  	return $this->_editor;
  }

  public function setEditor($editor){
  	$this->_editor = $editor;
  }

  public function getCode(){
  	return $this->_code;
  }

  public function setCode($code){
  	$this->_code = $code;
  }
}
?>
