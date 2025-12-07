<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// ========== PUBLIC ROUTES (No Auth Required) ========== //    

$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('knowledge-base', 'Home::knowledgeBase', ['as' => 'kb']);
$routes->get('knowledge-base/(:num)', 'Home::knowledgeBaseDetail/$1', ['as' => 'kb.detail']);
$routes->get('knowledge-base/search', 'Home::knowledgeBaseSearch', ['as' => 'kb.search']);

// ========== AUTH ROUTES ========== //

$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], function ($routes) {
    $routes->get('login', 'LoginController::index', ['as' => 'login']);
    $routes->post('login', 'LoginController::authenticate', ['as' => 'login.authenticate']);
    $routes->get('register', 'RegisterController::index', ['as' => 'register']);
    $routes->post('register', 'RegisterController::create', ['as' => 'register.create']);
    $routes->get('logout', 'LoginController::logout', ['as' => 'logout']);
});

// ========== USER ROUTES (Auth Required, Role: user) ========== //

$routes->group('user', ['namespace' => 'App\Controllers\User', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'user.dashboard']);

    // Complaints
    $routes->get('complaints', 'ComplaintController::index', ['as' => 'user.complaints']);
    $routes->get('complaints/create', 'ComplaintController::create', ['as' => 'user.complaints.create']);
    $routes->post('complaints/store', 'ComplaintController::store', ['as' => 'user.complaints.store']);
    $routes->get('complaints/(:num)', 'ComplaintController::show/$1', ['as' => 'user.complaints.show']);
    $routes->get('complaints/(:num)/edit', 'ComplaintController::edit/$1', ['as' => 'user.complaints.edit']);
    $routes->post('complaints/(:num)/update', 'ComplaintController::update/$1', ['as' => 'user.complaints.update']);
    $routes->delete('complaints/(:num)', 'ComplaintController::delete/$1', ['as' => 'user.complaints.delete']);

    // Chat
    $routes->get('complaints/(:num)/chat', 'ChatController::index/$1', ['as' => 'user.chat']);
    $routes->post('complaints/(:num)/chat/send', 'ChatController::send/$1', ['as' => 'user.chat.send']);
    $routes->get('complaints/(:num)/chat/fetch', 'ChatController::fetch/$1', ['as' => 'user.chat.fetch']);

    // Feedback
    $routes->get('complaints/(:num)/feedback', 'FeedbackController::create/$1', ['as' => 'user.feedback.create']);
    $routes->post('complaints/(:num)/feedback', 'FeedbackController::store/$1', ['as' => 'user.feedback.store']);

    // Notifications
    $routes->get('notifications', 'NotificationController::index', ['as' => 'user.notifications']);
    $routes->post('notifications/(:num)/read', 'NotificationController::markAsRead/$1', ['as' => 'user.notifications.read']);
    $routes->post('notifications/read-all', 'NotificationController::markAllAsRead', ['as' => 'user.notifications.readAll']);
});

// ========== ADMIN ROUTES (Auth Required, Role: admin|superadmin) ========== //

$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin,superadmin'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);

    // Complaints Management
    $routes->get('complaints', 'ComplaintController::index', ['as' => 'admin.complaints']);
    $routes->get('complaints/(:num)', 'ComplaintController::show/$1', ['as' => 'admin.complaints.show']);
    $routes->post('complaints/(:num)/assign', 'ComplaintController::assign/$1', ['as' => 'admin.complaints.assign']);
    $routes->post('complaints/(:num)/status', 'ComplaintController::changeStatus/$1', ['as' => 'admin.complaints.status']);
    $routes->post('complaints/(:num)/priority', 'ComplaintController::changePriority/$1', ['as' => 'admin.complaints.priority']);

    // Chat (Admin Side)
    $routes->get('complaints/(:num)/chat', 'ChatController::index/$1', ['as' => 'admin.chat']);
    $routes->post('complaints/(:num)/chat/send', 'ChatController::send/$1', ['as' => 'admin.chat.send']);
    $routes->get('complaints/(:num)/chat/fetch', 'ChatController::fetch/$1', ['as' => 'admin.chat.fetch']);

    // Analytics
    $routes->get('analytics', 'AnalyticsController::index', ['as' => 'admin.analytics']);
    $routes->get('analytics/export', 'AnalyticsController::export', ['as' => 'admin.analytics.export']);

    // Export Complaints
    $routes->get('complaints/(:num)/export-pdf', 'ComplaintController::exportPdf/$1', ['as' => 'admin.complaints.pdf']);
    $routes->get('complaints/export-excel', 'ComplaintController::exportExcel', ['as' => 'admin.complaints.excel']);

    // Knowledge Base Management
    $routes->get('knowledge-base', 'KnowledgeBaseController::index', ['as' => 'admin.kb']);
    $routes->get('knowledge-base/create', 'KnowledgeBaseController::create', ['as' => 'admin.kb.create']);
    $routes->post('knowledge-base/store', 'KnowledgeBaseController::store', ['as' => 'admin.kb.store']);
    $routes->get('knowledge-base/(:num)/edit', 'KnowledgeBaseController::edit/$1', ['as' => 'admin.kb.edit']);
    $routes->post('knowledge-base/(:num)/update', 'KnowledgeBaseController::update/$1', ['as' => 'admin.kb.update']);
    $routes->delete('knowledge-base/(:num)', 'KnowledgeBaseController::delete/$1', ['as' => 'admin.kb.delete']);
});

// ========== SUPERADMIN ROUTES (Auth Required, Role: superadmin only) ========== //

$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'role:superadmin'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'superadmin.dashboard']);

    // User Management
    $routes->get('users', 'UserManagementController::index', ['as' => 'superadmin.users']);
    $routes->get('users/create', 'UserManagementController::create', ['as' => 'superadmin.users.create']);
    $routes->post('users/store', 'UserManagementController::store', ['as' => 'superadmin.users.store']);
    $routes->get('users/(:num)/edit', 'UserManagementController::edit/$1', ['as' => 'superadmin.users.edit']);
    $routes->post('users/(:num)/update', 'UserManagementController::update/$1', ['as' => 'superadmin.users.update']);
    $routes->delete('users/(:num)', 'UserManagementController::delete/$1', ['as' => 'superadmin.users.delete']);
    $routes->post('users/(:num)/toggle-active', 'UserManagementController::toggleActive/$1', ['as' => 'superadmin.users.toggle']);

    // Application Management
    $routes->get('applications', 'ApplicationManagementController::index', ['as' => 'superadmin.applications']);
    $routes->get('applications/create', 'ApplicationManagementController::create', ['as' => 'superadmin.applications.create']);
    $routes->post('applications/store', 'ApplicationManagementController::store', ['as' => 'superadmin.applications.store']);
    $routes->get('applications/(:num)/edit', 'ApplicationManagementController::edit/$1', ['as' => 'superadmin.applications.edit']);
    $routes->post('applications/(:num)/update', 'ApplicationManagementController::update/$1', ['as' => 'superadmin.applications.update']);
    $routes->delete('applications/(:num)', 'ApplicationManagementController::delete/$1', ['as' => 'superadmin.applications.delete']);

    // Category Management
    $routes->get('categories', 'CategoryManagementController::index', ['as' => 'superadmin.categories']);
    $routes->get('categories/create', 'CategoryManagementController::create', ['as' => 'superadmin.categories.create']);
    $routes->post('categories/store', 'CategoryManagementController::store', ['as' => 'superadmin.categories.store']);
    $routes->get('categories/(:num)/edit', 'CategoryManagementController::edit/$1', ['as' => 'superadmin.categories.edit']);
    $routes->post('categories/(:num)/update', 'CategoryManagementController::update/$1', ['as' => 'superadmin.categories.update']);
    $routes->delete('categories/(:num)', 'CategoryManagementController::delete/$1', ['as' => 'superadmin.categories.delete']);

    // System Analytics
    $routes->get('analytics', 'SystemAnalyticsController::index', ['as' => 'superadmin.analytics']);
    $routes->get('analytics/export', 'SystemAnalyticsController::export', ['as' => 'superadmin.analytics.export']);
});
