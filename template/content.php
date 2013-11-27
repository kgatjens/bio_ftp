<input type="text" class="span3" style="margin: 0 auto;" data-provide="typeahead" data-items="4" data-source="[
<?php
$i=0;

foreach ($bacteria->scanDir() as $key => $value) {
	$final = $bacteria->countDir() == $i ? "" : ",";		
	echo "'".$value."'".$final;
	$i++;
}
//'Bacillus_anthracis', 'Acinetobacter_baumannii', 'Bacillus_thuringiensis'

?>
]">
