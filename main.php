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
*http://www.w3resource.com/twitter-bootstrap/example-typehead.html
*/

require_once('lib/db.php');


define("SERVER_PATH","ftp://ftp.ncbi.nlm.nih.gov/genomes/Bacteria/");
define("GENES_READS","10");
define("FILE_NAME","NC_008598.ptt"); // Temporal
define("FILE_EXTENSION","ptt");


class Bacteria{

	public $bacteria_name;
	public $file_location;

	public $sequense;
	public $genes;
	public $species;

	function __construct($bacteria_name) {
       $this->bacteria_name = $bacteria_name;
       $this->file_location = SERVER_PATH.$this->bacteria_name."/".FILE_NAME;
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
   	* Read from a specific bacteria FTP folder, and extract data
	*
	*	Files to be parse: 
	*	.GeneMark-2.5m
	*	.ptt
	*	.GeneMarkHMM-2.6r
	*
   	* return 
	*/
   function bacteriaRead($bacteria_name = ""){
   		$bacteria_files = $this->scandir(SERVER_PATH."/".$bacteria_name);
   		
   		$gbsFile = $bacteria_files[10]; // Get the *.gbs file to get the final SpeciesNo
   		
   		$sequense = $this->addSequense($bacteria_name."/".$bacteria_files[0], $bacteria_name."/".$gbsFile);
   		
   		$this->parsePpt($bacteria_name."/".$bacteria_files[12]);

   		$this->addGene($bacteria_name."/".$bacteria_files[1],$bacteria_name."/".$bacteria_files[11], $sequense);

   		$this->show($bacteria_files);
   }


    /*
   	* Parse GeneMark-2.5m
	* @geneMark : geneMark current file name, 
	* @aspFile 	: asp current file name	 
   	* return 
	*/
   function addSequense($geneMark = "", $gbsFile = ""){
   		//echo SERVER_PATH.$asnFile;
   		$data = file(SERVER_PATH.$geneMark);

   		$gbs = file(SERVER_PATH.$gbsFile);
		$needle = 'strain=';
		$reg = '/' . $needle . '/';
		$SpeciesNo = preg_grep($reg, $gbs);

		//$this->show($gbs);//debug

   		foreach ($SpeciesNo as $key => $value) {//getting the SpeciesNo
		   	$SpeciesNo = str_replace('/strain="','',$value);
		   	$SpeciesNo = str_replace('"','',$SpeciesNo);
		   	$SpeciesNo = str_replace(' ','',$SpeciesNo);
   		}

   		$SpeciesNo = strlen($SpeciesNo) > 10 ? "" : $SpeciesNo;// validation, to be sure is the right line with right info

		$sequense['SpeciesNo'] 		= $SpeciesNo;
   		$sequense['SequenceNo'] 	= '';
		
		$sequense['SequenceID'] 	= str_replace('Sequence file: ','',$data[3]);
		$sequense['SequenceID'] 	= str_replace('.fna','',$sequense['SequenceID']);

   		$sequense['SequenceDesc'] 	= str_replace('Sequence: ','',$data[2]);
   		$sequense['SequenceLength'] = str_replace('Sequence length: ','',$data[4]);

   		$sequense['SequenceNo'] = insertData('Sequences',$sequense);//return ID

   		$this->show($sequense);

   		return $sequense;

   		//$this->show($sequense);
   }

   /*
   	* Parse GeneMarkHMM-2.6r and the *.ppt
	*
   	* return 
	*/
   function addGene($geneMarkHMM = "", $pptFile = "", $sequense = array()){
   		
   		$geneMarkH = file(SERVER_PATH.$geneMarkHMM);

   		//$data = $this->show($geneMarkH);

		for($i=9;$i<9+GENES_READS;$i++){//clean the array

			$geneMarkH[$i] = $this->clean($geneMarkH[$i]);			
			$genesData[] = explode(" ", $geneMarkH[$i]);
		}   

		//$this->show($genesData);

		foreach ($genesData as $key => $value) {
			$gene[$key]['GeneStrand'] = $value[2];
   			$gene[$key]['GeneStart']  = $value[3];
   			$gene[$key]['GeneEnd']    = $value[4];
   			$gene[$key]['GeneLength'] = $value[5];
		}		

		$genePpt = file(SERVER_PATH.$pptFile);

		for($i=3;$i<3+GENES_READS;$i++){//clean the array

			$genePpt[$i] = $this->changeBlanks($genePpt[$i]);			
			$genesData2[] = explode("*", $genePpt[$i]);
		}  


		//Adding extra fields
   		$gene['GeneName'] 		= $genesData2[4];
   		$gene['GeneSynonym'] 	= $genesData2[4];
   		$gene['GeneCode'] 		= "";
   		$gene['GeneCOG'] 		= $genesData2[7];
   		$gene['GeneProduct'] 	= $genesData2[8];

   		//$this->show($sequense);

   		$gene['SpeciesNo'] 	= $sequense['SpeciesNo'];
   		$gene['SequenceNo'] = $sequense['SequenceNo'];
   		
   		$gene['GenePID'] = "";//*
   		$gene['id'] = "";//*
   		$gene['GeneNo'] = "";//*
   		$gene['GeneGC'] = "";//*
   		
   		$gene['GeneKey'] = "";

   }

   /*
	* Read FTP specific file from a location
	* return 
	*/
	function parsePPT($ppt = ""){
		$data = file(SERVER_PATH.$ppt);
		unset($data[0]);
		unset($data[1]);
		unset($data[2]);

		$cleanRow = array();
		for($i=3;$i<13;$i++){ // test - limit just for ten
			$cleanRow[] = preg_split ("/\s+/", $data[$i]);
		}

		$this->show($cleanRow);

		return $cleanRow;
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
   * Delete spaces
   * return 
   */
   function clean($array = array()){
		$array = str_replace('           ',' ',$array);
		$array = str_replace('          ',' ',$array);
		$array = str_replace('         ',' ',$array);
		$array = str_replace('        ',' ',$array);
		$array = str_replace('       ',' ',$array);
		$array = str_replace('      ',' ',$array);
		$array = str_replace('     ',' ',$array);
		$array = str_replace('    ',' ',$array);
		$array = str_replace('   ',' ',$array);
      return $array;
   }

   /*
   * Delete spaces
   * return 
   */
   function changeBlanks($array = array()){
		$array = str_replace('           ','*',$array);
		$array = str_replace('          ','*',$array);
		$array = str_replace('         ','*',$array);
		$array = str_replace('        ','*',$array);
		$array = str_replace('       ','*',$array);
		$array = str_replace('      ','*',$array);
		$array = str_replace('     ','*',$array);
		$array = str_replace('    ','*',$array);
		$array = str_replace('   ','*',$array);
		$array = str_replace('	','*',$array);
      return $array;
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