<?php
    
    use \Hcode\PageAdmin;
    use \Hcode\Model\User;
    use \Hcode\Model\Order;
    use \Hcode\Model\OrderStatus;

    $app->get('/admin/orders/:idorder/status', function($idorder) {
        User::verifyLogin();

        $order = new Order();
        $order->get((int)$idorder);

        $page = new PageAdmin();
        $page->setTpl("order-status", [
            "order"=>$order->getValues(),
            "status"=>OrderStatus::listAll(),
            "msgSuccess"=>Order::getMsgSuccess(),
            "msgError"=>Order::getMsgError()
        ]);
        
    });

    $app->post('/admin/orders/:idorder/status', function($idorder) {
        User::verifyLogin();

        if(!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0) {
            Order::setMsgError("informe o Status actual");
            header("Location: /admin/orders/$idorder/status");
            exit;
        }

        $order = new Order();
        $order->get((int)$idorder);
        $order->setidstatus((int)$_POST['idstatus']);
        $order->save();

        Order::setMsgSuccess("Status do pedido actualizado!");

        header("Location: /admin/orders/$idorder/status");
        exit;
    });

    $app->get('/admin/orders/:idorder/delete', function($idorder) {
        User::verifyLogin();
        
        $order = new Order();
        $order->get((int)$idorder);
        $order->delete();

        header("location: /admin/orders");
        exit;
    });

    $app->get('/admin/orders/:idorder', function($idorder) {
        User::verifyLogin();
        
        $order = new Order();
        $order->get((int)$idorder);

        $cart = $order->getCart();
        
        $page = new PageAdmin();
        $page->setTpl("order", [
            "order"=>$order->getValues(),
            "cart"=>$cart->getValues(),
            'produts'=>$cart->getProducts()
        ]);

    });

    $app->get('/admin/orders', function() {
        User::verifyLogin();

        $search = (isset($_GET['search'])) ? $_GET['search'] : '';
		
		$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
		
		if ($search === '') {
			$pagination = Order::getPage($page);
		} else {
			$pagination = Order::getPageSearch($search, $page);
        }
        
        //var_dump($pagination); exit;

		$pages = [];
		for($x = 1; $x <= $pagination['pages']; $x++){
			array_push($pages, [
				'href'=>'/admin/orders?'.http_build_query([
					'page'=>$x,
					'search'=>$search
				]),
				'text'=>$x
			]);
		}

        $page = new PageAdmin();
        $page->setTpl("orders", [
            "orders"=>$pagination['data'],
			"search"=>$search,
			"pages"=>$pages
        ]);
    });
?>