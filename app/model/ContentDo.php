<?php
namespace Acd\Model;

class ContentKeyInvalidException extends \exception {}
class ContentDo
{
	private $id;
	private $idStructure;
	private $title;
	//private $data; /* Array key/value of variable fields */
	private $fields;
	private $parent; /* ContentDo Relation in complex structures */

	public function __construct() {
		$this->id = null;
		$this->idStructure = null;
		$this->fields = new FieldsDo();
		$this->parent = null;
	}
	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function setIdStructure($idStructure) {
		$this->idStructure = (string)$idStructure;
	}
	public function getIdStructure() {
		return $this->idStructure;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	public function addField($field) {
		$this->getFields()->add($field);
	}
	public function getFields() {
		return $this->fields;
	}
	private function setFields($fields) {
		$this->fields = $fields;
	}
	public function getFieldValue($fieldName) {
		try {
			return $this->getFields()->getValue($fieldName);
		} catch (KeyInvalidException $e) {
			return '';
		}
	}
	public function setFieldValue($fieldName, $value) {
		$this->getFields()->setValue($fieldName, $value);
	}
	public function setStructureRef($fieldName, $structureRef) {
		$this->getFields()->setStructureRef($fieldName, $structureRef);
	}	
	public function setData($keyData, $data = null) {
		echo "TODO";
		/* Setting full structure */
		if ($data === null) {
			// TODO errores si campo existe y tipo dato válido
			$this->data = $keyData;
		}
		/* Set key / value */
		else {
			$this->data[$keyData] = $data;
		}
	}
	public function getData($key = null) {
		$data = array();
		foreach ($this->getFields() as $field) {
			$data[$field->getName()] = $field->getValue();
		}

		return $data;
	}
	public function setParent($parent) {
		$this->parent = $parent; // ContentDO
	}
	public function getParent() {
		return $this->parent;
	}
	public function load($rawData, $idStructure = null) {
		$this->setId($rawData['id']);
		$this->setTitle($rawData['title']);
		$this->setIdStructure($idStructure);
		$fields = $this->getFields();
		if (is_array($rawData['data'])){
			foreach ($rawData['data'] as $key => $value) {
				unset($field);
				$field = new FieldDo();
				//d($value);
				$field->loadData($key, $value, true);
				$fields->add($field);
			}
			//d($fields);
		}
		//$this->setFields($fields);
	}
	public function tokenizeData() {
		$aFieldsData = array();
		// TODO usar fields
		$aFieldsData = $this->getData();
		return  array(
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'data' => $aFieldsData
		);
	}
}