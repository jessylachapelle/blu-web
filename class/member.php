<?php
/**
*
*/
class Membre {
  private $_no;
  private $_prenom;
  private $_nom;
  private $_inscription;
  private $_derniereActivite;
  private $_noCivic;
  private $_address;
  private $_app;
  private $_ville;
  private $_province;
  private $_codePostal;
  private $_courriel;
  private $_telephone;

  public function __construct() {
    $this->_no = null;
    $this->_prenom = null;
    $this->_nom = null;
    $this->_inscription = null;
    $this->_derniereActivite = null;
    $this->_noCivic = null;
    $this->_address = null;
    $this->_ville = null;
    $this->_province = null;
    $this->_codePostal = null;
    $this->_courriel = null;
    $this->_telephone = null;
  }

  public function getNo(){
    return $this->_no;
  }

  public function setNo($no){
    $this->_no = $no;
  }

  public function getPrenom(){
    return $this->_prenom;
  }

  public function setPrenom($prenom){
    $this->_prenom = $prenom;
  }

  public function getNom(){
    return $this->_nom;
  }

  public function setNom($nom){
    $this->_nom = $nom;
  }

  public function getInscription() {
    return $this->_inscription;
  }

  public function setInscription($inscription) {
    $this->_inscription = $inscription;
  }

  public function getDerniereActivite() {
    return $this->_derniereActivite;
  }

  public function setDerniereActivite($derniereActivite) {
    $this->_derniereActivite = $derniereActivite;
  }

  public function getDateDesactivation() {
    $date = new DateTime($this->getDerniereActivite());
    $date->add(new DateInterval('P1Y'));
    return $date->format('Y-m-d');
  }

  public function getNoCivic(){
    return $this->_noCivic;
  }

  public function setNoCivic($noCivic){
    $this->_noCivic = $noCivic;
  }

  public function getAddress(){
    return $this->_address;
  }

  public function setAddress($address){
    $this->_address = $address;
  }

  public function getApp() {
    return $this->_app;
  }

  public function setApp($app) {
    $this->_app = $app;
  }

  public function getVille(){
    return $this->_ville;
  }

  public function setVille($ville){
    $this->_ville = $ville;
  }

  public function getProvince(){
    return $this->_province;
  }

  public function setProvince($province){
    $this->_province = $province;
  }

  public function getCodePostal(){
    return $this->_codePostal;
  }

  public function setCodePostal($codePostal){
    $this->_codePostal = $codePostal;
  }

  public function getCourriel(){
    return $this->_courriel;
  }

  public function setCourriel($courriel){
    $this->_courriel = $courriel;
  }

  public function getTelephone(){
    return $this->_telephone;
  }

  public function setTelephone($telephone){
    $this->_telephone = $telephone;
  }

  public function isActive() {
    return $this->getDateDesactivation() >= date('Y-m-d');
  }
}
?>
