<?php
/**
* 
*/
class Exemplaire {
  private $_id;
  private $_article;
  private $_titre;
  private $_dateAjout;
  private $_dateVente;
  private $_dateArgentRemis;
  private $_prix;
  
  public function __construct() {
    $this->_id = null;
    $this->_article = null;
    $this->_titre = null;
    $this->_dateAjout = null;
    $this->_dateVente = null;
    $this->_dateArgentRemis = null;
    $this->_prix = null;
  }
  
  public function getId() {
		return $this->_id;
	}
  
  public function setId($id) {
    $this->_id = $id;
  }
   
  public function getArticle() {
		return $this->_article;
	}

	public function setArticle($article) {
		$this->_article = $article;
	}

	public function getTitre(){
		return $this->_titre;
	}

	public function setTitre($titre){
		$this->_titre = $titre;
	}

	public function getDateAjout() {
		return $this->_dateAjout;
	}
	
	public function setDateAjout($date) {
  	$this->_dateAjout = $date;
	}
	
  public function getDateVente() {
		return $this->_dateVente;
	}
	
	public function setDateVente($date) {
  	$this->_dateVente = $date;
	}
	
	public function getDateArgentRemis() {
		return $this->_dateArgentRemis;
	}
	
	public function setDateArgentRemis($date) {
  	$this->_dateArgentRemis = $date;
	}

	public function setDate($date, $typeTransaction) {
  	if($typeTransaction == 1)
		  $this->setDateAjout($date);
		else if($typeTransaction == 4)
		  $this->setDateArgentRemis($date);
		else
		  $this->setDateVente($date);
	}

	public function getPrix() {
		return $this->_prix;
	}

	public function setPrix($prix) {
		$this->_prix = $prix;
	}
}
?>