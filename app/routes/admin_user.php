<?php

// ADMIN - CRUD for users table
$app->get('/admin/user/:op(/:id)', function($op, $id = 0) use ($app) {
    
    // FOR PROJECTS WITH MANY ACCESS LEVELS
//    if (($_SESSION['eshopuser']) || ($_SESSION['role'] != 'admin')) {
//       $app->render('forbidden.html.twig');
//       return;
//    } 
    
    if ($op == 'edit') {
        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $id);
        if (!$user) {
            echo 'User not found';
            return;
        }
        $app->render("admin_user_add.html.twig", array(
            'v' => $user, 'operation' => 'Update',
            "eshopuser" => $_SESSION['eshopuser']
        ));
    } else {
        $app->render("admin_user_add.html.twig", array(
            'operation' => 'Add',
            "eshopuser" => $_SESSION['eshopuser']
        ));
    }
})->conditions(array(
    'op' => '(add|edit)',
    'id' => '[0-9]+'));

$app->post('/admin/user/:op(/:id)', function($op, $id = 0) use ($app) {
    
    if (!$_SESSION['eshopuser']) {
        $app->render('forbidden.html.twig');
        return;
    }
    $name = $app->request()->post('name');
    $fname = $app->request()->post('fname');
    $lname = $app->request()->post('lname');
    $email = $app->request()->post('email');
    $phone = $app->request()->post('phone');
    $city = $app->request()->post('city');
    $addressLine1 = $app->request()->post('addressLine1');
    $addressLine2 = $app->request()->post('addressLine2');
    $code = $app->request()->post('code');
    $state = $app->request()->post('state');
    $code = $app->request()->post('code');
    $role = $app->request()->post('role');
    $pass1 = $app->request->post('pass1');
    $today = date("Y-m-d");
    //$last_login = $today;
    $errorList = array();
    $valueList = array('name' => $name);
    if (strlen($name) < 2 || strlen($name) > 200) {
        array_push($errorList, "Username name must be 2-100 characters long");
    } else {
        $userList = DB::queryFirstRow("SELECT * FROM users WHERE name=%s", $name);
        if ($userList) {
            array_push($errorList, "Username already in use");
        }
    }
    
    if ($errorList) {
        $app->render("admin_user_add.html.twig", ["errorList" => $errorList,
            'v' => $valueList
        ]);
    } else {
        if ($op == 'edit') {
            // unlink('') OLD file - requires select
            DB::update('users', array(
                 'name' => $name, 
            "fname" => $fname,
            "lname" => $lname,
            "email" => $email,
            "phone" => $phone,
            'password' => password_hash($pass1, CRYPT_BLOWFISH),
            "city" => $city,
            "addressLine1" => $addressLine1,
            "addressLine2" => $addressLine2,
            "code" => $code,
            "state" => $state,
                    ), "id=%i", $id);
        } else {
            DB::insert('users', array(
                 'name' => $name, 
            "fname" => $fname,
            "lname" => $lname,
            "email" => $email,
            "phone" => $phone,
            'password' => password_hash($pass1, CRYPT_BLOWFISH),
            "city" => $city,
            "addressLine1" => $addressLine1,
            "addressLine2" => $addressLine2,
            "code" => $code,
            "state" => $state,
            ));
            $id = DB::insertId();
        }
        $app->render("add_success.html.twig", array(
            "name" => $name,
            "userID" => $userID,
            "address" => $address,
            "postalCode" => $postalCode,
            "email" => $email,
            "eshopuser" => $_SESSION['eshopuser']
        ));
    }
})->conditions(array(
    'op' => '(add|edit)',
    'id' => '[0-9]+'));

// HOMEWORK: implement a table of existing users with links for editing
$app->get('/admin/user/list', function() use ($app) {
    $userList = DB::query("SELECT * FROM users");
    $app->render("admin_user_list.html.twig", array(
        'userList' => $userList,
        "eshopuser" => $_SESSION['eshopuser']    
    ));
});

$app->get('/admin/user/delete/:id', function($id) use ($app) {
    $user = DB::queryFirstRow('SELECT * FROM users WHERE id=%i', $id);
    $app->render('admin_user_delete.html.twig', array(
        'o' => $user,
        "eshopuser" => $_SESSION['eshopuser']
    ));
})->VIA('GET', 'POST');