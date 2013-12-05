<?php
//species, sequences, genes
//require_once '../lib/base_datos.php';

include('main.php');
$bacteria->bacteriaRead($_POST['bacteria']);

include('template/footer.php');

?>