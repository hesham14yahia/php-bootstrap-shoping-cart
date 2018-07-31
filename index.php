<?php
    require('config/db.php');

    $query = 'SELECT * FROM products ORDER BY id ASC';
    $result = mysqli_query($connect, $query);

    session_start();
    $product_ids = array();
    //session_destroy();

    // Check if the card has been submitted
    if(filter_input(INPUT_POST, 'add_to_cart')) {
        if(isset($_SESSION['shopping_cart'])) {
            
            // Keep track of how many products in the shopping cart
            $count = count($_SESSION['shopping_cart']);

            // Create sequantial array for matching array keys to products id's
            $product_ids = array_column($_SESSION['shopping_cart'], 'id');

            if(!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
                $_SESSION['shopping_cart'][$count] = array (
                    'id' => filter_input(INPUT_GET, 'id'),
                    'name' => filter_input(INPUT_POST, 'name'),
                    'price' => filter_input(INPUT_POST, 'price'),
                    'quantity' => filter_input(INPUT_POST, 'quantity')
                );
            } else {
                // Product already exists, increase quantity
                // Match array key ti id of the product being add to cart
                for($i = 0; $i < count($product_ids); $i++){
                    if($product_ids[$i] == filter_input(INPUT_GET, 'id')) {
                        // Add item quantity to the existing product in the array
                        $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                    }
                } 
            }

        } else {
            // if cart doesn't exist, create frist product with array key 0
            // create array using submitted form data, start from key 0 and fill it with values
            $_SESSION['shopping_cart'][0] = array (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
            );
        }
    }

    if(filter_input(INPUT_GET, 'action') == 'delete') {
        // loop throught all products in the shopping cart until it matches with GET id variable
        foreach($_SESSION['shopping_cart'] as $key => $product){
            if($product['id'] == filter_input(INPUT_GET, 'id')) {
                //remove product from the shopping cart when it matches with the GET id
                unset($_SESSION['shopping_cart'][$key]);
            }
        }
        // reset session array keys so they match with $product_ids numeric array
        $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Shopping</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css" />
    </head>
    <body>
        <div class="container">
            <?php 
                if($result):
                    if(mysqli_num_rows($result) > 0):
                        while($product = mysqli_fetch_assoc($result)):
                            ?>
                                <div class="col-md-3 col-sm-4">
                                    <form method="POST" action="index.php?action=add&id=<?php echo $product['id']; ?>">
                                        <div class="products form-group">
                                            <img src="<?php echo $product['image']; ?>" class="img-responsive">
                                            <h4><?php echo $product['name']; ?></h4>
                                            <h4><?php echo $product['price']; ?></h4>
                                            <input class="form-control" type="text" name="quantity" value="1">
                                            <input type="hidden" name="name" value="<?php echo $product['name'] ?>">
                                            <input type="hidden" name="price" value="<?php echo $product['price'] ?>">
                                            <input type="submit" name="add_to_cart" class="btn btn-info" value="Add to cart">
                                        </div>
                                    </form>
                                </div>
                            <?php 
                        endwhile;
                    endif;
                endif;
            ?>
            <div class="clearfix"></div>
            <div class="table-responsive">
                <table class="table">
                    <tr class="colspan"><h3>Order Details</h3></tr>
                    <tr>
                        <th width="40%">Product Name</th>
                        <th width="15%">Quantity</th>
                        <th width="20%">Price</th>
                        <th width="20%">Total</th>
                        <th width="5%">Action</th>
                    </tr>
                    <?php
                        if(!empty($_SESSION['shopping_cart'])):
                            $total = 0;
                            foreach($_SESSION['shopping_cart'] as $key => $product):
                    ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
                        <td>
                            <a class="btn btn-danger" href="index.php?action=delete&id=<?php echo $product['id']; ?>">Remove</a>
                        </td>
                    </tr>
                    <?php
                                $total = $total + ($product['quantity'] * $product['price']);
                            endforeach;
                    ?>
                    <tr>
                        <td colspan="3" align="right">Total</td>  
                        <td align="right">$ <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <!-- Show checkout button only if the shopping cart is not empty -->
                        <td colspan="5">
                            <?php
                                if(isset($_SESSION['shopping_cart'])):
                                    if(count($_SESSION['shopping_cart'])):
                            ?>
                                        <a href="#" class="button">Checkout</a>
                            <?php
                                    endif;
                                endif;
                            ?>
                        </td>
                    </tr>
                    <?php

                        endif;
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
