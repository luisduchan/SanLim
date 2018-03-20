<?php
$html = file_get_contents('http://tuoitre.vn/tin/nhip-song-tre/20170708/tiep-nhan-9-ti-dong-tiep-suc-den-truong-tan-sv-kho-khan/1348645.html'); //get the html returned from the following url

$pokemon_doc = new DOMDocument();

libxml_use_internal_errors(TRUE); //disable libxml errors

if(!empty($html)){ //if any html is actually returned

	$pokemon_doc->loadHTML($html);
	libxml_clear_errors(); //remove errors for yucky html
	
	$pokemon_xpath = new DOMXPath($pokemon_doc);

	//get all the h2's with an id
	$pokemon_row = $pokemon_xpath->query('//h2[@id]');

	if($pokemon_row->length > 0){
		foreach($pokemon_row as $row){
			echo $row->nodeValue . "<br/>";
		}
	}
}
?>