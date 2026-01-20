<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ========== AUTHENTICATION ==========
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';
$route['auth/select_tenant'] = 'auth/select_tenant';
$route['auth/(:any)'] = 'auth/$1';

// ========== ADMIN SYSTEM ==========
$route['admin'] = 'admin/admin/index';
$route['admin/dashboard'] = 'admin/admin/dashboard';

// Tenants
$route['admin/tenants'] = 'admin/admin/tenants';
$route['admin/tenants/create'] = 'admin/admin/create_tenant';
$route['admin/create_tenant'] = 'admin/admin/create_tenant';
$route['admin/tenants/edit/(:num)'] = 'admin/admin/edit_tenant/$1';
$route['admin/edit_tenant/(:num)'] = 'admin/admin/edit_tenant/$1';
$route['admin/tenants/delete/(:num)'] = 'admin/admin/delete_tenant/$1';
$route['admin/delete_tenant/(:num)'] = 'admin/admin/delete_tenant/$1';
$route['admin/tenants/users/(:num)'] = 'admin/admin/tenant_users/$1';
$route['admin/tenant_users/(:num)'] = 'admin/admin/tenant_users/$1';

// Users
$route['admin/users'] = 'admin/admin/users';
$route['admin/users/create'] = 'admin/admin/create_user';
$route['admin/create_user'] = 'admin/admin/create_user';
$route['admin/users/edit/(:num)'] = 'admin/admin/edit_user/$1';
$route['admin/edit_user/(:num)'] = 'admin/admin/edit_user/$1';
$route['admin/users/delete/(:num)'] = 'admin/admin/delete_user/$1';
$route['admin/delete_user/(:num)'] = 'admin/admin/delete_user/$1';

// ✅ System Tools
$route['admin/settings'] = 'admin/admin/settings';
$route['admin/backup'] = 'admin/admin/backup';
$route['admin/logs'] = 'admin/admin/logs';

// Generic admin route (LAST)
$route['admin/(:any)'] = 'admin/admin/$1';

// ========== DASHBOARD ==========
$route['dashboard'] = 'dashboard/index';

// ========== PRODUCTS ==========
$route['products'] = 'products/index';
$route['products/create'] = 'products/create';
$route['products/update/(:num)'] = 'products/update/$1';
$route['products/delete/(:num)'] = 'products/delete/$1';
$route['products/(:any)'] = 'products/$1';

// ========== BRANDS ==========
$route['brands'] = 'brands/index';
$route['brands/create'] = 'brands/create';
$route['brands/update/(:num)'] = 'brands/update/$1';
$route['brands/delete/(:num)'] = 'brands/delete/$1';
$route['brands/(:any)'] = 'brands/$1';

// ========== CATEGORIES ==========
$route['category'] = 'category/index';
$route['category/create'] = 'category/create';
$route['category/update/(:num)'] = 'category/update/$1';
$route['category/delete/(:num)'] = 'category/delete/$1';
$route['category/(:any)'] = 'category/$1';
$route['categories'] = 'category/index';
$route['categories/(:any)'] = 'category/$1';

// ========== STOCK ==========
$route['stock'] = 'stock/index';
$route['stock/create'] = 'stock/create';
$route['stock/update/(:num)'] = 'stock/update/$1';
$route['stock/(:any)'] = 'stock/$1';

// ========== ORDERS ==========
$route['orders'] = 'orders/index';
$route['orders/fetchOrdersData'] = 'orders/fetchOrdersData';
$route['orders/fetchOrdersByStatus/(:any)'] = 'orders/fetchOrdersByStatus/$1';
$route['orders/create'] = 'orders/create';
$route['orders/update/(:num)'] = 'orders/update/$1';
$route['orders/delete/(:num)'] = 'orders/delete/$1';
$route['orders/generateInvoicePDF/(:num)'] = 'orders/generateInvoicePDF/$1';
$route['orders/(:any)'] = 'orders/$1';

// ========== CUSTOMERS ==========
$route['customers'] = 'customers/index';
$route['customers/create'] = 'customers/create';
$route['customers/update/(:num)'] = 'customers/update/$1';
$route['customers/delete/(:num)'] = 'customers/delete/$1';
$route['customers/(:any)'] = 'customers/$1';

// ========== SUPPLIERS ==========
$route['suppliers'] = 'suppliers/index';
$route['suppliers/create'] = 'suppliers/create';
$route['suppliers/update/(:num)'] = 'suppliers/update/$1';
$route['suppliers/delete/(:num)'] = 'suppliers/delete/$1';
$route['suppliers/(:any)'] = 'suppliers/$1';

// ========== PURCHASES ==========
$route['purchases'] = 'purchases/index';
$route['purchases/create'] = 'purchases/create';
$route['purchases/update/(:num)'] = 'purchases/update/$1';
$route['purchases/delete/(:num)'] = 'purchases/delete/$1';
$route['purchases/(:any)'] = 'purchases/$1';

// ========== REPORTS ==========
$route['reports'] = 'reports/index';
$route['reports/stock'] = 'reports/stock';
$route['reports/sales'] = 'reports/sales';
$route['reports/(:any)'] = 'reports/$1';

// ========== USERS ==========
$route['users'] = 'users/index';
$route['users/create'] = 'users/create';
$route['users/update/(:num)'] = 'users/update/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['users/(:any)'] = 'users/$1';

// ========== GROUPS ==========
$route['groups'] = 'groups/index';
$route['groups/create'] = 'groups/create';
$route['groups/update/(:num)'] = 'groups/update/$1';
$route['groups/delete/(:num)'] = 'groups/delete/$1';
$route['groups/(:any)'] = 'groups/$1';

// ========== COMPANY ==========
$route['company'] = 'company/index';
$route['company/update'] = 'company/update';
$route['company/(:any)'] = 'company/$1';

// ========== STORES ==========
$route['stores'] = 'stores/index';
$route['stores/create'] = 'stores/create';
$route['stores/update/(:num)'] = 'stores/update/$1';
$route['stores/delete/(:num)'] = 'stores/delete/$1';
$route['stores/(:any)'] = 'stores/$1';
// ==========================================
// PRE-ORDERS ROUTES
// ==========================================
$route['preorders'] = 'preorders/index';
$route['preorders/view/(:num)'] = 'preorders/view/$1';
$route['preorders/update_status/(:num)'] = 'preorders/update_status/$1';
$route['preorders/delete/(:num)'] = 'preorders/delete/$1';
// ==========================================
// API ROUTES (MOBILE APP)
// ==========================================
$route['api/login'] = 'api/login';
$route['api/logout'] = 'api/logout';
$route['api/products'] = 'api/products';
$route['api/product/(:num)'] = 'api/product/$1';
$route['api/calculate_price'] = 'api/calculate_price';
$route['api/create_pre_order'] = 'api/create_pre_order';
$route['api/pre_orders'] = 'api/pre_orders';
$route['api/preorders'] = 'api/preorders';
$route['api/preorder/(:num)'] = 'api/preorder/$1';
$route['api/stats'] = 'api/stats';