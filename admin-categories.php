<?php

    use \Hcode\PageAdmin;
    use \Hcode\Model\User;
    use \Hcode\Model\Category;
    use \Hcode\Model\Product;
    
    $app->get("/admin/categories", function() {
		User::verifyLogin();
		//$categories = Category::listAll();

		$search = (isset($_GET['search'])) ? $_GET['search'] : '';
		
		$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
		
		if ($search === '') {
			$pagination = Category::getPage($page);
		} else {
			$pagination = Category::getPageSearch($search, $page);
		}

		$pages = [];
		for($x = 1; $x <= $pagination['pages']; $x++){
			array_push($pages, [
				'href'=>'/admin/users?'.http_build_query([
					'page'=>$x,
					'search'=>$search
				]),
				'text'=>$x
			]);
		}

		$page = new PageAdmin();
		$page->setTpl("categories", [
			"categories"=>$pagination['data'],
			"search"=>$search,
			"pages"=>$pages
		]);
	});

	$app->get("/admin/categories/create", function() {
		User::verifyLogin();
		$page = new PageAdmin();
		$page->setTpl("categories-create");
	});

	$app->post("/admin/categories/create", function() {
		User::verifyLogin();
		$category = new Category();
		$category->setData($_POST);
		$category->save();
		$page = new PageAdmin();
		header("Location: /admin/categories");
		exit;
	});

	$app->get("/admin/categories/:idcategory/delete", function($idcategory) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);
		$category->delete();
		header("Location: /admin/categories");
		exit;
	});

	$app->get("/admin/categories/:idcategory", function($idcategory) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);
		$page = new PageAdmin();
		$page->setTpl("categories-update", [
			'category'=>$category->getValues()
		]);
	});

	$app->post("/admin/categories/:idcategory", function($idcategory) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);
		$category->setData($_POST);
		$category->save();
		header("Location: /admin/categories");
		exit;
	});

	$app->get('/admin/categories/:idcategory/products', function($idcategory) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);

		$page = new PageAdmin();
		$page->setTpl("categories-products", [
			'category'=>$category->getValues(),
			'productsRelated'=>$category->getProducts(),
			'productsNotRelated'=>$category->getProducts(false)
		]);
	});

	$app->get('/admin/categories/:idcategory/products/:idproduct/add', function($idcategory, $idproduct) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);

		$product = new Product();
		$product->get((int)$idproduct);

		$category->addProduct($product);

		header("Location: /admin/categories/$idcategory/products");
		exit;
	});

	$app->get('/admin/categories/:idcategory/products/:idproduct/remove', function($idcategory, $idproduct) {
		User::verifyLogin();
		$category = new Category();
		$category->get((int)$idcategory);

		$product = new Product();
		$product->get((int)$idproduct);
		
		$category->removeProduct($product);

		header("Location: /admin/categories/$idcategory/products");
		exit;
	});

?>