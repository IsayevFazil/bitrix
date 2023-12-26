<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("my");
?>

<?
CModule::IncludeModule("iblock");
$el = new CIBlockElement;


function parseDescription($url)
{
	$html = file_get_contents($url);

	// Создаем объект DOMDocument
	$dom = new DOMDocument;

	// Загружаем HTML-код
	@$dom->loadHTML($html);

	// Создаем объект DOMXPath
	$xpath = new DOMXPath($dom);

	// Ищем мета-тег с именем "description"
	$metaDescription = $xpath->query('//meta[@name="description"]/@content');

	// Если мета-тег найден, выводим его содержимое
	if ($metaDescription->length > 0) {
		return $metaDescription[0]->nodeValue;
	} else {
		return 'Мета-тег "description" не найден на странице ' . $url;
	}
}

// Пример использования
$file_path_urls = 'desc.txt';

// Чтение строк из файла в массив
$urls = file($file_path_urls, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($urls as $url) {
	$description = parseDescription($url);
	$metaDesc[] = $description;
}
echo "<pre>";
print_r($metaDesc);
echo "</pre>";


$elementsCode = 'elements_code.txt';

// Чтение строк из файла в массив
$arElementsCode = file($elementsCode, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($arElementsCode as $key => $code){
	$arSelect = Array("ID", "IBLOCK_ID", "CODE", "NAME");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
	$arFilter = Array("IBLOCK_ID"=> 64, "CODE" => $code);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
	while($ob = $res->GetNextElement()){
		$arFields = $ob->GetFields();
		echo "<pre>";
		print_r($arFields);
		echo "</pre>";
		$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates(64,$arFields["ID"]); //для элемента

		if (strpos($metaDesc[$key], "Москву") !== false) {
			$newStr = str_replace("Москву", "#REG_PADEZH_ROD#", $metaDesc[$key]);
		}elseif (strpos($metaDesc[$key], "Москве") !== false){
			$newStr = str_replace("Москве", "#REG_PADEZH_DAT#", $metaDesc[$key]);
		}elseif (strpos($metaDesc[$key], "Москву") !== false){
			$newStr = str_replace("Москву", "#REG_PADEZH_VIN#", $metaDesc[$key]);
		}elseif (strpos($metaDesc[$key], "Москвой") !== false){
			$newStr = str_replace("Москвой", "#REG_PADEZH_TVOR#", $metaDesc[$key]);
		}elseif (strpos($metaDesc[$key], "Москве") !== false){
			$newStr = str_replace("Москве", "#REG_PADEZH_PRED#", $metaDesc[$key]);
		}else{
			$newStr = $metaDesc[$key];
		}
		$arNewTemplates = array('ELEMENT_META_DESCRIPTION'=>$newStr);
		$ipropTemplates->set($arNewTemplates);
	}

} ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>