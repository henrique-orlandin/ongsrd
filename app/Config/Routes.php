<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
service('auth')->routes($routes);

$routes->get('/', 'HomeController::home');
$routes->get('/quem-somos', 'AboutController::page');
$routes->get('/como-ajudar', 'HomeController::howToHelp');
$routes->get('/finais-felizes', 'HomeController::happyEndings');
$routes->get('/contato', 'ContactController::index');
$routes->post('/contato', 'ContactController::submit');
$routes->get('/noticias', 'NewsController::list');
$routes->get('/noticias/(:segment)', 'NewsController::details/$1');
$routes->get('/adotar', 'PetController::list');
$routes->get('/adotar/(:segment)', 'PetController::details/$1');
$routes->get('uploads/(:segment)/(:segment)', 'MediaController::show/$1/$2');
$routes->head('uploads/(:segment)/(:segment)', 'MediaController::show/$1/$2');

$routes->get('cms', static function () {
	if (auth()->loggedIn()) {
		return redirect()->to('/cms/dashboard');
	}

	return redirect()->to('/cms/login');
});

$routes->group('cms', ['filter' => ['session', 'group:admin,super_admin']], static function ($routes) {
	$routes->get('dashboard', 'Admin\\DashboardController::index');

	$routes->get('settings/maintenance', 'Admin\\MaintenanceController::edit', ['filter' => 'group:super_admin']);
	$routes->post('settings/maintenance', 'Admin\\MaintenanceController::update', ['filter' => 'group:super_admin']);

	$routes->get('users', 'Admin\\UsersController::index', ['filter' => 'group:super_admin']);
	$routes->get('users/new', 'Admin\\UsersController::new', ['filter' => 'group:super_admin']);
	$routes->post('users/create', 'Admin\\UsersController::create', ['filter' => 'group:super_admin']);
	$routes->get('users/edit/(:num)', 'Admin\\UsersController::edit/$1', ['filter' => 'group:super_admin']);
	$routes->post('users/update/(:num)', 'Admin\\UsersController::update/$1', ['filter' => 'group:super_admin']);
	$routes->post('users/delete/(:num)', 'Admin\\UsersController::delete/$1', ['filter' => 'group:super_admin']);

	$routes->get('about', 'Admin\\AboutController::index');
	$routes->post('about/update', 'Admin\\AboutController::update');

	$routes->get('pets', 'Admin\\PetsController::index');
	$routes->get('pets/new', 'Admin\\PetsController::new');
	$routes->post('pets/create', 'Admin\\PetsController::create');
	$routes->get('pets/edit/(:num)', 'Admin\\PetsController::edit/$1');
	$routes->post('pets/update/(:num)', 'Admin\\PetsController::update/$1');
	$routes->post('pets/delete/(:num)', 'Admin\\PetsController::delete/$1');
	$routes->post('pets/(:num)/images/(:num)/delete', 'Admin\\PetsController::deleteImage/$1/$2');
	$routes->post('pets/(:num)/images/(:num)/thumbnail', 'Admin\\PetsController::setThumbnail/$1/$2');

	$routes->get('how-to-help', 'Admin\\HowToHelpController::index');
	$routes->post('how-to-help/update', 'Admin\\HowToHelpController::update');

	$routes->get('happy-endings', 'Admin\\HappyEndingsController::index');
	$routes->get('happy-endings/new', 'Admin\\HappyEndingsController::new');
	$routes->post('happy-endings/create', 'Admin\\HappyEndingsController::create');
	$routes->get('happy-endings/edit/(:num)', 'Admin\\HappyEndingsController::edit/$1');
	$routes->post('happy-endings/update/(:num)', 'Admin\\HappyEndingsController::update/$1');
	$routes->post('happy-endings/delete/(:num)', 'Admin\\HappyEndingsController::delete/$1');

	$routes->get('contact-messages', 'Admin\\ContactMessagesController::index');
	$routes->get('contact-messages/(:num)', 'Admin\\ContactMessagesController::show/$1');

	$routes->get('home-page', 'Admin\\HomePageController::edit');
	$routes->post('home-page/update', 'Admin\\HomePageController::update');

	$routes->get('partners', 'Admin\\PartnersController::index');
	$routes->get('partners/new', 'Admin\\PartnersController::new');
	$routes->post('partners/create', 'Admin\\PartnersController::create');
	$routes->get('partners/edit/(:num)', 'Admin\\PartnersController::edit/$1');
	$routes->post('partners/update/(:num)', 'Admin\\PartnersController::update/$1');
	$routes->post('partners/delete/(:num)', 'Admin\\PartnersController::delete/$1');

	$routes->get('banners', 'Admin\\BannersController::index');
	$routes->get('banners/new', 'Admin\\BannersController::new');
	$routes->post('banners/create', 'Admin\\BannersController::create');
	$routes->get('banners/edit/(:num)', 'Admin\\BannersController::edit/$1');
	$routes->post('banners/update/(:num)', 'Admin\\BannersController::update/$1');
	$routes->post('banners/delete/(:num)', 'Admin\\BannersController::delete/$1');

	$routes->get('noticias', 'Admin\\NewsController::index');
	$routes->get('noticias/new', 'Admin\\NewsController::new');
	$routes->post('noticias/create', 'Admin\\NewsController::create');
	$routes->get('noticias/edit/(:num)', 'Admin\\NewsController::edit/$1');
	$routes->post('noticias/update/(:num)', 'Admin\\NewsController::update/$1');
	$routes->post('noticias/delete/(:num)', 'Admin\\NewsController::delete/$1');
	$routes->post('noticias/(:num)/images/(:num)/delete', 'Admin\\NewsController::deleteImage/$1/$2');
	$routes->post('noticias/(:num)/images/(:num)/main', 'Admin\\NewsController::setMainImage/$1/$2');
});
