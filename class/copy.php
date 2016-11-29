<?php
/**
*
*/
class Exemplaire {
  private $_id;
  private $_article;
  private $_title;
  private $_dateAdded;
  private $_dateSold;
  private $_datePaid;
  private $_price;

  public function __construct() {
    $this->_id = null;
    $this->_article = null;
    $this->_title = null;
    $this->_dateAdded = null;
    $this->_dateSold = null;
    $this->_datePaid = null;
    $this->_price = null;
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

	public function getTitle(){
		return $this->_title;
	}

	public function setTitle($title){
		$this->_title = $title;
	}

	public function getDateAdded() {
		return $this->_dateAdded;
	}

	public function setDateAdded($date) {
  	$this->_dateAdded = $date;
	}

  public function getDateSold() {
		return $this->_dateSold;
	}

	public function setDateSold($date) {
  	$this->_dateSold = $date;
	}

	public function getDatePaid() {
		return $this->_datePaid;
	}

	public function setDatePaid($date) {
  	$this->_datePaid = $date;
	}

	public function setDate($date, $transactionType) {
  	if($transactionType == 1)
		  $this->setDateAdded($date);
		else if($transactionType == 4)
		  $this->setDatePaid($date);
		else
		  $this->setDateSold($date);
	}

	public function getPrice() {
		return $this->_price;
	}

	public function setPrice($price) {
		$this->_price = $price;
	}
}
?>
