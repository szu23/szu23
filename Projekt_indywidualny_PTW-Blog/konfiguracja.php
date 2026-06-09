<?php
$polaczenie = new mysqli('localhost', 'root', '', 'blog');

if($polaczenie->connect_error){
    die("ERROR: Could not connect. " . $polaczenie->connect_error);
}
