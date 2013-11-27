<?php

/*
*
*	Class Bacteria
*	Read data from the NCBI open FTP, and save it locally. 
*	Author: Kenneth Brenes	
*	Organization: UCR
*	https://github.com/kgatjens/bio_ftp.git
*
*	git push -u origin 
*
*
*/

define("SERVER_PATH","ftp://ftp.ncbi.nlm.nih.gov/genomes/Bacteria/");
define("GENES_READS","10");
define("FILE_NAME","NC_008598.ptt"); // Temporal
define("FILE_EXTENSION","ptt");


class Bacteria{

	public $bacteria_name;
	public $file_location;

	function __construct($bacteria_name) {
       $this->bacteria_name = $bacteria_name;
       $this->file_location = SERVER_PATH.$this->bacteria_name."/".FILE_NAME;
   	}

	/*
	* Read FTP specific file from a location
	* return 
	*/
	function readFtp(){
		$data = file($this->file_location);
		unset($data[0]);
		unset($data[1]);
		unset($data[2]);

		$cleanRow = array();
		for($i=3;$i<13;$i++){ // test - limit just for ten
			$cleanRow[] = preg_split ("/\s+/", $data[$i]);
		}
		return $cleanRow;
	}

	/*
   	* Build the property array for Gene Table
   	* return 
	*/
   function geneCreation($cleanRow = array()){
		$gene = array();
		$product = "";
		$index=0;
		foreach ($cleanRow as $key => $value) {
			$totalValues = count($value);
			$gene[$index]['location'] 		= $value[0];
			$gene[$index]['strand'] 		= $value[1];
			$gene[$index]['length'] 		= $value[2];
			$gene[$index]['pid'] 			= $value[3];
			$gene[$index]['gene_name'] 		= $value[4];
			$gene[$index]['synonym_code'] 	= $value[5];
			$gene[$index]['cog'] = $value[7];
			for($i = 8; $i<$totalValues;$i++){
				$product = $product." ".$value[$i];	
			}
			$gene[$index]['product'] = $product;
			$product = "";
			$index++;
		}

		return $gene;
   }

   /*
   * Read folder from path
   * return 
   */
   function scanDir($path = SERVER_PATH){
      $folders = scandir($path);
      return $folders;
   }

   /*
   * Read folder from path
   * return 
   */
   function countDir($path = SERVER_PATH){
      $foldersQuantity = count(scandir($path));
      return $foldersQuantity;
   }

	/*
	* 		- HELPER
	*  
	$this->show($value);
	*/
	function show($array=array()){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		exit;
	}



}

$bacteria = new Bacteria("Bacillus_thuringiensis_Al_Hakam_uid58795");
//$bacteria->geneCreation($bacteria->readFtp());
//$bacteria->show($bacteria->scanDir());
//echo $bacteria->countDir();

//exit;


?>