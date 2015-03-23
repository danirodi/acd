<?php
namespace Acd;

require ('../autoload.php');
session_start();
$action =$_GET['a'];
@$id = $_GET['id'];
@$idStructureTypeSearch = $_GET['idt'];
@$titleSearch = $_GET['s'];
$idParent = $_GET['idp'];
$idStructureTypeParent = $_GET['idtp'];
$idField = $_GET['f'];
@$positionInField = $_GET['p'];
if (!Model\Auth::isLoged()) {
	$action = 'login';
}

switch ($action) {
	case 'login':
		header('Location: index.php');
		return;
		break;
	case 'select_type': 
	case 'search': 
		$structures = new Model\StructuresDo();
		$structures->loadFromFile(conf::$DATA_PATH);
		$headerMenuOu = new View\HeaderMenu();
		$headerMenuOu->setType('menu');

		$toolsOu = new View\Tools();
		$toolsOu->setLogin($_SESSION['login']);
		$toolsOu->setRol($_SESSION['rol']);

		$contentOu = new View\ContentEditSearch();
		//$contentOu->setActionType('index');
		$contentOu->setId($idParent);
		$contentOu->setType($idStructureTypeParent);
		$contentOu->setIdField($idField);
		$contentOu->setPositionInField($positionInField);
		$contentOu->setStructures($structures);
		$contentOu->setTitleSeach($titleSearch);
		$contentOu->setStructureTypeSeach($idStructureTypeSearch);

		if ($action === 'search') {
			$contentLoader = new Model\ContentLoader();
			$contentLoader->setId($idStructureTypeSearch);
			$whereCondition = [];
			if($titleSearch) {
				$whereCondition['title'] = $titleSearch;
			}
			if($idStructureTypeSearch) {
				$whereCondition['idStructure'] = $idStructureTypeSearch;
			}
			$matchContents = $contentLoader->loadContent('editorSearch', $whereCondition);
			//d($matchContents);
			$contentOu->setResultSearch($matchContents);
		}


		$skeletonOu = new View\BaseSkeleton();
		$skeletonOu->setBodyClass('indexContent');
		$skeletonOu->setHeadTitle('Manage content type');
		$skeletonOu->setHeaderMenu($headerMenuOu->render());
		$skeletonOu->setTools($toolsOu->render());
		break;

	default:
		dd("Error 404");
}

$skeletonOu->setContent($contentOu->render());

header("Content-Type: text/html");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo $skeletonOu->render();
