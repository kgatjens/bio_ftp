<?php
//species, sequences, genes
include('main.php');
include('template/header.php');

//performe the search action
$data = $bacteria->bacteriaRead($_POST['bacteria']);

include('template/table.php');
include('template/footer2.php');

?>