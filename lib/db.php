<?php
/**
 * Data Base Library
 *
 * 
 * 
 */

/**
 * File to config the Data Base
 */
require_once 'config.php';

/**
 * Global connection variable
 *
 * @var mysqli
 */
$_connect = null;

/**
 * Retorna un recurso de conexión a la base de datos.
 *
 * Si la conexión ya fue hecha, la retorna, sino crea una.
 *
 * @return mysqli
 */
function getConnection() {
    global $_connect, $confiDB;

    return $_connect;
}

/**
 * Insert data in the specific table and return the ID, not validates duplicates
 *
 * @return int
 */
function insertData($table, $data) {
    

    //$conn = getConnection();

    $sql="INSERT INTO `".$table."`";
    $columns = "(";
        foreach ($data as $key => $value) {
            $columns = $columns."`".$key."`,";
        }
    
    $columns=substr($columns, 0,-1);
    $columns=$columns.")";
    $sql = $sql.$columns;

    $values = "VALUES (";
        foreach ($data as $key => $value) {
            $values = $values."'".$value."',";
        }

    $values=substr($values, 0,-1);
    $values=$values.")";
    $sql = $sql.$values.";";
    echo $sql;
    exit;
    

    $data = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($datos);
}


