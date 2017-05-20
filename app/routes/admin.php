<?php

$app->get('/admin_panel', function() use ($app) {
    $app->render('admin_panel.html.twig', array(
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

$app->get('/admin_product', function() use ($app) {
    $productList = DB::query("SELECT * FROM products");
    $app->render('admin_product.html.twig', array(
        'productList' => $productList,
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

$app->map('/admin_user', function() use ($app) {
    $userList = DB::query("SELECT * FROM users");
    $app->render("admin_user.html.twig", array(
        'userList' => $userList,
        "eshopuser" => $_SESSION['eshopuser']
    ));
})->via('GET', 'POST');

//$app->post('/admin_user', function() use ($app) {
//    $userList =  DB::query("SELECT * FROM users");
//    $app->render("admin_user.html.twig", array(
//        'userList' => $userList,
//        "eshopuser" => $_SESSION['eshopuser']
//    ));
//});


$app->get('/admin_category', function() use ($app) {
    $categoryList = DB::query("SELECT * FROM categories");
    $app->render("admin_category.html.twig", array(
        'categoryList' => $categoryList,
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

$app->get('/admin_order', function() use ($app) {
    $orderList = DB::query("SELECT * FROM orders");
    $app->render("admin_order.html.twig", array(
        'orderList' => $orderList,
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

$app->get('/admin_product_add', function() use ($app) {
    $app->render("admin_product_add.html.twig", array(
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

$app->post('/admin_product_add', function() use ($app) {
    if (!$_SESSION['eshopuser']) {
        $app->render('forbidden.html.twig');
        return;
    }
//    print_r($_POST);
    $title = $app->request()->post('title');
    $name = $app->request()->post('name');
    $catID = $app->request()->post('catID');
    $modelName = $app->request()->post('modelName');
    $modelNo = $app->request()->post('modelNo');
    $desc1 = $app->request()->post('desc1');
    //$desc2 = $app->request()->post('desc2');
    //$desc3 = $app->request()->post('desc3');
    $price = $app->request()->post('price');
    $stock = $app->request()->post('stock');
    $discount = $app->request()->post('discount');
    $today = date("Y-m-d");
    $postDate = $today;
    $image = isset($_FILES['image']) ? $_FILES['image'] : array();
    $errorList = array();
    /*if (strlen($name) < 2 || strlen($name) > 100) {
        array_push($errorList, "Name must be 2-100 characters long");
    }*/
   
    if (empty($price) || $price < 0 || $price > 99999999) {
        array_push($errorList, "Price must be between 0 and 99999999");
    }
    if ($image) {
        $imageInfo = getimagesize($image["tmp_name"]);
        if (!$imageInfo) {
            array_push($errorList, "File does not look like an valid image");
        } else {
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            if ($width > 300 || $height > 300) {
                array_push($errorList, "Image must at most 300 by 300 pixels");
            }
        }
    }
    
    $valueList = array('title' => $title);
    
    if (strlen($title) < 2 || strlen($title) > 200) {
        array_push($errorList, "Task name must be 2-100 characters long");
    } else {
        $productList = DB::queryFirstRow("SELECT * FROM products WHERE title=%s", $title);
        if ($productList) {
            array_push($errorList, "Product title already in use");
        }
    }
        
//    print_r($_SESSION['todouser']);
    if ($errorList) {
        $app->render("admin_product_add.html.twig", ["errorList" => $errorList,
            'v' => $valueList
        ]);
    } else {
        
         
        $imageBinaryData = file_get_contents($image['tmp_name']);
        $mimeType = mime_content_type($image['tmp_name']);
        
        DB::insert('products', array(
            "title" => $title,
            "catID" => $catID,
            "modelName" => $modelName,
            "modelNo" => $modelNo,
            "desc1" => $desc1,
            "price" => $price,
            "stock" => $stock,
            "discount" => $discount,
            "postDate" => $today,
            'imageData1' => $imageBinaryData,
            'imageMimeType1' => $mimeType
        ));
        $app->render("add_success.html.twig", array(
            "title" => $title,
            "catID" => $catID,
            "modelName" => $modelName,
            "price" => $price,
            "stock" => $stock
        ));
    }
});

//Admin_Panel->Manage Users->Add users

$app->get('/admin_user_add', function() use ($app) {
    $app->render("admin_user_add.html.twig", array(
        "eshopuser" => $_SESSION['eshopuser']
    ));
});

