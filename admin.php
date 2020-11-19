<?php

    use \Hcode\PageAdmin;
    use Hcode\Model\User;

    $app->get('/admin', function() {
		User::verifyLogin();	
		$page = new PageAdmin();
		$page->setTpl("index");

	});


?>