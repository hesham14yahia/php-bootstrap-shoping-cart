<?php

    $connect = mysqli_connect('localhost', 'root', '5556276', 'cart');

    // Check for errors
    if(mysqli_connect_errno()) {
        // Conntection Falied
        echo 'Failed to connection to mysql '.mysqli_connect_errno();
    }