<?php
//species, sequences, genes
//require_once '../lib/base_datos.php';

include('main.php');
include('template/header.php');

//performe the search action
$data = $bacteria->bacteriaRead($_POST['bacteria']);

include('template/table.php');

include('template/footer.php');

?>