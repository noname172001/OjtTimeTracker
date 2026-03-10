<?php

// create a function for var_dump ts purposes only - aldin
function dd($data)
{
    echo "<pre>";
    var_dump($data);
    echo "/<pre>";

    die();
}