

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

	function __construct($bacteria_name) {
       echo $this->bacteria_name = $bacteria_name;
   	}

   	/*
   	* Read FTP specific file from a location
   	* return 
	*/
	function readFtp(){

	}



}

$bacteria = new Bacteria("Bacillus_thuringiensis_Al_Hakam_uid58795");


?>