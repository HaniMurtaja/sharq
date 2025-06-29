<?php

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\ClientsUpdatedController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Exports\OrdersExport;
use App\Http\Controllers\Admin\DispatcherController;
use App\Http\Controllers\Admin\FoodicsClientsController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\OnlineOrdersController;
use App\Http\Controllers\Admin\MapsController;
use App\Http\Controllers\Admin\ReasonsController;
use App\Http\Controllers\Admin\ReportNewController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientsGroupNewController;


use App\Http\Controllers\Admin\ZoneNewController;

use App\Http\Controllers\Api\FoodicsOrderController;
use App\Http\Controllers\Admin\ExportController;
use Maatwebsite\Excel\Facades\Excel;

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/livewire/update', $handle)->name('update-livewire');
});



Route::prefix('admin')->group(function () {
    Route::get('test_fathy', [AuthController::class, 'test_fathy']);
    Route::get('login', [AuthController::class, 'showLogin'])->name('show-login');
    Route::post('login-save', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth')->group(function () {
        require_once __DIR__ . '/settings.php';


        Route::post('/delete-driver-order', [OnlineOrdersController::class, 'deleteDriverOrder']);



        // sidebar routes

        Route::get('operators', [HomeController::class, 'operators'])->name('operators');
        //Route::get('clients', [HomeController::class, 'clients'])->name('clients');
        Route::get('users', [HomeController::class, 'users'])->name('users');
        Route::get('vehicles', [HomeController::class, 'vehicles'])->name('vehicles');

        Route::get('messages', [HomeController::class, 'messages'])->name('messages');

        Route::get('locations', [HomeController::class, 'locations'])->name('locations');


        //pages routes

        Route::get('shifts', [OperatorController::class, 'shiftsList'])->name('shifts');
        Route::get('update-shifts/{id?}', [OperatorController::class, 'updateShift'])->name('update-shifts');
        Route::post('edit-shift/{id}', [OperatorController::class, 'editShift'])->name('edit-shift');
        Route::post('save-shift/{id?}', [OperatorController::class, 'saveShift'])->name('save-shift');
        Route::delete('delete-shift/{id}', [OperatorController::class, 'deleteShift'])->name('delete-shift');



        Route::get('groups', [OperatorController::class, 'groupsList'])->name('groups');
        Route::post('save-group', [OperatorController::class, 'saveGroup'])->name('save-group');
        Route::get('update-group/{id?}', [OperatorController::class, 'updateGroup'])->name('update-group');
        Route::post('edit-group/{id}', [OperatorController::class, 'editGroup'])->name('edit-group');

        Route::delete('delete-group/{id}', [OperatorController::class, 'deleteGroup'])->name('delete-group');


        Route::get('operators-list', [OperatorController::class, 'operatorsList'])->name('operators-list');
        Route::post('save-operator', [OperatorController::class, 'saveOperator'])->name('save-operator');
        Route::get('update-operator/{id}', [OperatorController::class, 'updateOperator'])->name('update-operator');
        Route::post('edit-operator/{id}', [OperatorController::class, 'editOperator'])->name('edit-operator');
        Route::delete('delete-operator/{id}', [OperatorController::class, 'deleteOperator'])->name('delete-operator');

        Route::post('change-operator-verification_status', [OperatorController::class, 'changeOperatorVerificationStatus'])->name('changeOperatorVerificationStatus');


         Route::get('get-verification-data/{id}', [OperatorController::class, 'getVerificationData'])->name('getVerificationData');



        Route::get('vehicle-list', [VehicleController::class, 'vehicleList'])->name('vehicle-list');
        Route::post('save-vehicles', [VehicleController::class, 'save'])->name('save-vehicles');
        Route::get('update-vehicle/{id}', [VehicleController::class, 'update'])->name('update-vehicle');
        Route::post('edit-vehicle/{id}', [VehicleController::class, 'edit'])->name('edit-vehicle');
        Route::delete('delete-vehicle/{id}', [VehicleController::class, 'delete'])->name('delete-vehicle');


        Route::get('branches-list', [ClientsController::class, 'branchesList'])->name('branches-list');
        Route::post('save-branch', [ClientsController::class, 'saveBranch'])->name('save-branch');
        Route::get('edit-branch/{id}/edit', [ClientsController::class, 'editBranch'])->name('edit-branch');
        Route::post('update-branch/{id}', [ClientsController::class, 'updateBranch'])->name('update-branch');
        Route::delete('delete-branch/{id}', [ClientsController::class, 'deleteBranch'])->name('delete-branch');
        Route::get('branch/{id}', [ClientsController::class, 'getBranch'])->name('get-branch');




        Route::get('clients-group-list', [ClientsController::class, 'clientsGroupList'])->name('clients-group-list');
        Route::post('save-clients-group', [ClientsController::class, 'saveClientsGroup'])->name('save-clients-group');
        Route::get('edit-clients-group/{id}/edit', [ClientsController::class, 'editClientsGroup'])->name('edit-clients-group');
        Route::put('update-clients-group/{id}', [ClientsController::class, 'updateClientsGroup'])->name('update-clients-group');
        Route::delete('delete-clients-group/{id}', [ClientsController::class, 'deleteClientsGroup'])->name('delete-clients-group');


        Route::get('zone-list', [ClientsController::class, 'zoneList'])->name('zone-list');
        Route::post('save-zone', [ClientsController::class, 'saveZone'])->name('save-zone');
        Route::get('edit-zone/{id}/edit', [ClientsController::class, 'editZone'])->name('edit-zone');
        Route::put('update-zone/{id}', [ClientsController::class, 'updateZone'])->name('update-zone');
        Route::delete('delete-zone/{id}', [ClientsController::class, 'deleteZone'])->name('delete-zone');


        Route::get('client-list', [ClientsController::class, 'clientList'])->name('client-list');
        Route::post('save-client', [ClientsController::class, 'saveClient'])->name('save-client');
        Route::get('edit-client/{id}/edit', [ClientsController::class, 'editClient'])->name('edit-client');
        Route::get('client-show/{id}', [ClientsController::class, 'clientShow'])->name('client-show');
        Route::put('update-client/{id}', [ClientsController::class, 'updateClient'])->name('update-client');
        Route::delete('delete-client/{id}', [ClientsController::class, 'deleteClient'])->name('delete-client');

        Route::post('save-client-branch', [ClientsController::class, 'saveClientBranch'])->name('save-client-branch');
        Route::get('get-client-branches', [ClientsController::class, 'getBranches'])->name('get-client-branches');

        Route::get('get-client-orders', [ClientsController::class, 'getOrders'])->name('get-client-orders');


        Route::post('charge-client-wallet', [ClientsController::class, 'chargeWallet'])->name('charge-client-wallet');

        Route::get('change-client-branch-status', [ClientsController::class, 'changeClientBranchStatus'])->name('change-client-branch-status');
        Route::post('change-client-branch-auto-dispatch', [ClientsController::class, 'changeClientBranchAutoDispatch'])->name('change-client-branch-auto-dispatch');


        Route::post('save-client-user', [ClientsController::class, 'saveClientUser'])->name('save-client-user');
        Route::get('get-client-users', [ClientsController::class, 'getUsers'])->name('get-client-users');

        Route::post('save-client-exist-user', [ClientsController::class, 'saveClientExistUser'])->name('save-client-exist-user');

        Route::post('update-client-user', [ClientsController::class, 'updateClientExistUser'])->name('update-client-user');
        Route::post('delete-client-user', [ClientsController::class, 'deleteClientExistUser'])->name('delete-client-user');
        Route::post('change-client-user-status', [ClientsController::class, 'changeClientUserStatus'])->name('change-client-user-status');
        Route::get('/proxy/distance-matrix', [ClientsController::class, 'distanceMatrix'])->name('distance-matrix');

        Route::get('getBranchOfMaster', [ClientsController::class, 'getBranchOfMaster'])->name('getBranchOfMaster');
        Route::get('maps', [MapsController::class, 'Maps'])->name('getMaps');
        Route::get('/api/map-data', [MapsController::class, 'getMapData'])->name('getMapDataNew');;
        Route::get('geoapify', [MapsController::class, 'geoapify'])->name('geoapify');
        Route::get('google', [MapsController::class, 'google'])->name('google');
        Route::get('googlemap', [MapsController::class, 'googlemap'])->name('googlemap');


        Route::get('user-list', [UserController::class, 'usertList'])->name('user-list');
        Route::post('save-user', [UserController::class, 'saveUser'])->name('save-user');
        Route::get('edit-user/{id}/edit', [UserController::class, 'editUser'])->name('edit-user');
        Route::put('update-user/{id}', [UserController::class, 'updateUser'])->name('update-user');
        Route::delete('delete-user/{id}', [UserController::class, 'deleteUser'])->name('delete-user');


        Route::get('export-users-template', [UserController::class, 'exportUserTemplate'])->name('exportUserTemplate');


        Route::get('template-list', [UserController::class, 'templateList'])->name('template-list');
        Route::post('save-template', [UserController::class, 'saveTemplate'])->name('save-template');
        Route::get('edit-template/{id}/edit', [UserController::class, 'editTemplate'])->name('edit-template');
        Route::put('update-template/{id}', [UserController::class, 'updateTemplate'])->name('update-template');
        Route::delete('delete-template/{id}', [UserController::class, 'deleteTemplate'])->name('delete-template');




        Route::get('country-list', [LocationController::class, 'countryList'])->name('country-list');
        Route::post('save-country', [LocationController::class, 'saveCountry'])->name('save-country');
        Route::get('edit-country/{id}/edit', [LocationController::class, 'editCountry'])->name('edit-country');
        Route::put('update-country/{id}', [LocationController::class, 'updateCountry'])->name('update-country');
        Route::delete('delete-country/{id}', [LocationController::class, 'deleteCountry'])->name('delete-country');


        Route::get('city-list', [LocationController::class, 'cityList'])->name('city-list');
        Route::post('save-city', [LocationController::class, 'saveCity'])->name('save-city');
        Route::get('edit-city/{id}/edit', [LocationController::class, 'editCity'])->name('edit-city');
        Route::put('update-city/{id}', [LocationController::class, 'updateCity'])->name('update-city');
        Route::delete('delete-city/{id}', [LocationController::class, 'deleteCity'])->name('delete-city');


        Route::get('area-list', [LocationController::class, 'areaList'])->name('area-list');
        Route::post('save-area', [LocationController::class, 'saveArea'])->name('save-area');
        Route::get('edit-area/{id}/edit', [LocationController::class, 'editArea'])->name('edit-area');
        Route::put('update-area/{id}', [LocationController::class, 'updateArea'])->name('update-area');
        Route::delete('delete-area/{id}', [LocationController::class, 'deleteArea'])->name('delete-area');



        Route::get('city-areas', [LocationController::class, 'cityAreas'])->name('city-areas');






        Route::post('get-calculation-method', [ClientsController::class, 'getCalculationMethod'])->name('get-calculation-method');






        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('get-charts', [DashboardController::class, 'getCharts'])->name('get-charts');
        Route::get('get-charts-new', [DashboardController::class, 'getChartsNew'])->name('get-charts-new');


        // Route::get('/', [HomeController::class, 'index'])->name('index');
// 
        
        Route::get('/', [MapsController::class, 'geoapify'])->name('index');



        Route::get('/select-user', [HomeController::class, 'select_user'])->name('select_user');
        Route::get('/search_order', [HomeController::class, 'search_order'])->name('search_order');

        Route::get('reports', [HomeController::class, 'reports'])->name('reports');
        Route::get('reports/driver-reports', [HomeController::class, 'driver_reports'])->name('reports.driver-reports');


        Route::get('billings', [ReportController::class, 'billings'])->name('reports.billings');
        Route::get('get-billings-report', [ReportController::class, 'getBillingsData'])->name('get-billings-report');
        Route::get('get-cod-billings-report', [ReportController::class, 'getCodBillingsData'])->name('get-cod-billings-report');

        Route::get('operators/operator-reports', [ReportController::class, 'operatorReports'])->name('operators.operator-reports');

        Route::get('operators/get-operator-reports', [ReportController::class, 'getoperatorReports'])->name('operators.get-operator-reports');
        Route::post('save-operator-reports-history', [ReportController::class, 'saveOperatorReportHistory'])->name('save-operator-reports-history');

        Route::get('export-operators/{id}', [ReportController::class, 'exportOperators'])->name('export-operators');
        Route::get('export-vehicles/{id}', [ReportController::class, 'exportVehicle'])->name('export-vehicle');


        Route::get('report-citys', [ReportController::class, 'reportCitys'])->name('reports.reportCitys');

        Route::get('settings', [HomeController::class, 'settings'])->name('settings');

        Route::get('logout', [HomeController::class, 'logout'])->name('logout');

        Route::get('orders', [HomeController::class, 'orders'])->name('orders');



        Route::post('change-operator-status', [OperatorController::class, 'changeStatus'])->name('change-operator-status');


        Route::get('getClientLogData', [ClientsUpdatedController::class, 'getClientLogData'])->name('getClientLogData');




        //pages routes




        Route::get('city-areas', [LocationController::class, 'cityAreas'])->name('city-areas');




        Route::get('increment-step', [OrderController::class, 'incrementStep'])->name('increment-step');
        Route::get('decrement-step', [OrderController::class, 'decrementStep'])->name('decrement-step');
        Route::get('save-order', [OrderController::class, 'save'])->name('save-order');

        Route::get('client-branches', [OrderController::class, 'clientBranches'])->name('client-branches');
        Route::post('reset-step', [OrderController::class, 'resetStep'])->name('reset-step');




        Route::get('order-list', [ReportController::class, 'orderList'])->name('order-list');

        Route::post('save-history', [ReportController::class, 'saveHistory'])->name('save-history');
        Route::get('export-orders/{id}', [ReportController::class, 'exportOrders'])->name('export-orders');
        Route::get('history-list', [ReportController::class, 'historyList'])->name('history-list');


        //orders
        Route::get('orders-data-table', [OrderController::class, 'orderList'])->name('orders-data-table');
        Route::get('orders-dashboard', [OrderDashboardController::class, 'index'])->name('OrderDashboard');

        Route::get('UnifonicResponse', [OrderDashboardController::class, 'UnifonicResponse'])->name('UnifonicResponse');



        Route::get('dashboard-new', [OrderDashboardController::class, 'dashboard'])->name('OrderDashboard.dashboard');
        Route::get('/orders-chart-data', [OrderDashboardController::class, 'ordersChartData'])->name('OrderDashboard.ordersChartData');
        Route::get('/orders-per-city-chart-data', [OrderDashboardController::class, 'OrdersPerCityChartData'])->name('OrderDashboard.OrdersPerCityChartData');
        Route::get('/orders-per-clients', [OrderDashboardController::class, 'OrdersPerClientstData'])->name('OrderDashboard.OrdersPerClientstData');
        Route::get('/orders-per-clients-export', [OrderDashboardController::class, 'OrdersPerClientstDataExport'])->name('OrderDashboard.OrdersPerClientstDataExport');

        Route::get('/orders-per-client-chart-data', [OrderDashboardController::class, 'OrdersPerClientChartData'])->name('OrderDashboard.OrdersPerClientChartData');



        Route::get('/orders-per-city', [OrderDashboardController::class, 'OrdersPerCityData'])->name('OrderDashboard.OrdersPerCityData');

        Route::get('/orders-per-client', [OrderDashboardController::class, 'OrdersPerClientData'])->name('OrderDashboard.OrdersPerClientData');




        Route::post('save-firebase-token', [AuthController::class, 'saveFirebaseToken'])->name('save-firebase-token');

        Route::get('/notifications', [HomeController::class, 'fetchNotifications'])->name('fetch-notifications');
        Route::post('/mark-as-read', [HomeController::class, 'markAsRead'])->name('mark-as-read');


        Route::get('driver-reports', [ReportController::class, 'driverReports'])->name('driver-reports');

        Route::get('get-reports-list', [ReportController::class, 'getReportsList'])->name('get-reports-list');



        Route::get('brand-reports', [ReportController::class, 'brandReports'])->name('brand-reports');

        Route::get('get-brands-list', [ReportController::class, 'getBrandsList'])->name('get-brands-list');




        Route::get('client-reports', [ReportController::class, 'clientReports'])->name('reports.clientReports');

        Route::get('get-clients-list', [ReportController::class, 'getClientsList'])->name('get-clients-list');

        Route::get('export-clients-data', [ReportController::class, 'exportClientsData'])->name('export-clients-data');



        Route::get('export-operator-assign-report-data', [ReportController::class, 'exportOperatorsAssignReportData'])->name('exportOperatorsAssignReportData');

        Route::get('export-dispatchers-assign-report-data', [ReportNewController::class, 'exportDispatchersAssignReportData'])->name('exportDispatchersAssignReportData');


        Route::get('driver-status-list', [ReportController::class, 'driverStatusList'])->name('driver-status-list');
        Route::get('driver-order-list', [ReportController::class, 'getDriverOrdersData'])->name('driver-order-list');



        Route::post('client-cancel-order', [OrderController::class, 'clientCancelOrder'])->name('client-cancel-order');


        Route::post('/resolve-url', [LocationController::class, 'resolveUrl'])->name('resolve-url');


        Route::post('save-report-template', [ReportController::class, 'saveReportTemplate'])->name('save-report-template');
        Route::get('edit-report-template/{id}/edit', [ReportController::class, 'editReportTemplate'])->name('edit-report-template');

        Route::delete('delete-report-template/{id}', [ReportController::class, 'deleteReportTemplate'])->name('delete-report-template');

        Route::get('get-template-report-data-table', [ReportController::class, 'getTemplateReportData'])->name('get-template-report-data-table');


        Route::get('get-template-name', [ReportController::class, 'getTemplateName'])->name('get-template-name');

        Route::get('export-order-data-table', [ReportController::class, 'exportOrdersDataTable'])->name('export-order-data-table');
        Route::post('export-all-order-data-table', [ReportController::class, 'exportAllOrdersDataTable'])->name('export-all-order-data-table');

        Route::get('system-closed', [HomeController::class, 'systemClosed'])->name('system-closed');

        Route::get('dispatcherAssignReport', [ReportNewController::class, 'dispatcherAssignReport'])->name('report.dispatcherAssignReport');
        Route::get('dispatcherAssignReportShowBy', [ReportNewController::class, 'dispatcherAssignReportShowBy'])->name('report.dispatcherAssignReportShowBy');
        Route::get('clientsSalesreports', [ReportNewController::class, 'clientsSalesreport'])->name('report.clientsSalesreport');
        Route::get('getClientsSalesReportData', [ReportNewController::class, 'getClientsSalesReportData'])->name('report.getClientsSalesReportData');
        Route::get('getClientsSalesReportDataPerCity', [ReportNewController::class, 'getClientsSalesReportDataPerCity'])->name('report.getClientsSalesReportDataPerCity');




        Route::get('citiesSalesreports', [ReportNewController::class, 'citiesSalesreport'])->name('report.citiesSalesreport');
        Route::get('getCitiesSalesReportData', [ReportNewController::class, 'getCitiesSalesReportData'])->name('report.getCitiesSalesReportData');
        Route::get('getCitiesSalesReportDataPerclient', [ReportNewController::class, 'getCitiesSalesReportDataPerclient'])->name('report.getCitiesSalesReportDataPerclient');






        Route::get('operatorAssignReport', [ReportController::class, 'operatorAssignReport'])->name('report.operatorassignReport');
        Route::get('getOperatorAssignReportData', [ReportController::class, 'getOperatorAssignReportData'])->name('report.getOperatorAssignReportData');

        Route::get('getOperatorOrderSummaryData', [ReportController::class, 'getOperatorOrderSummaryData'])->name('report.getOperatorOrderSummaryData');



        Route::get('integrations', [HomeController::class, 'integrations'])->name('integrations');

        Route::get('integration-list', [IntegrationController::class, 'integrationList'])->name('integration-list');
        Route::post('save-integrations', [IntegrationController::class, 'save'])->name('save-integration');
        Route::get('update-integration/{id}', [IntegrationController::class, 'update'])->name('update-integration');
        Route::post('edit-integration/{id}', [IntegrationController::class, 'edit'])->name('edit-integration');
        Route::delete('delete-integration/{id}', [IntegrationController::class, 'delete'])->name('delete-integration');



        Route::get('webhook-list', [IntegrationController::class, 'webhookList'])->name('webhook-list');
        Route::post('save-webhooks', [IntegrationController::class, 'saveWebhook'])->name('save-webhook');
        Route::get('update-webhook/{id}', [IntegrationController::class, 'updateWebhook'])->name('update-webhook');
        Route::post('edit-webhook/{id}', [IntegrationController::class, 'editWebhook'])->name('edit-webhook');
        Route::delete('delete-webhook/{id}', [IntegrationController::class, 'deleteWebhook'])->name('delete-webhook');






        Route::get('get-order-summery-data', [DispatcherController::class, 'getOrderSummeryData'])->name('getOrderSummeryData');


        Route::get('assign-driver-modal', [DispatcherController::class, 'getAssignData'])->name('assign-driver-modal');
        // Route::post('assign-driver', [DispatcherController::class, 'assignDriver'])->name('assign-driver');
        Route::get('assign-driver', [DispatcherController::class, 'assignDriver'])->name('assign-driver');


        Route::get('get-order-history', [DispatcherController::class, 'getOrderHistory'])->name('get-order-history');
        Route::get('get-driver-orders', [DispatcherController::class, 'getDriverOrders'])->name('get-driver-orders');

        Route::get('get-statistics', [DispatcherController::class, 'getStatistics'])->name('get-statistics');
        Route::get('get-orders-data', [DispatcherController::class, 'GetOrdersData'])->name('GetOrdersData');


        Route::get('get-order-popup', [DispatcherController::class, 'getOrderPopup'])->name('get-order-popup');
        Route::get('get-order-popup-newmap', [MapsController::class, 'getOrderPopupNewMap'])->name('get-order-popup-newmap');

        Route::get('get-search-order-data', [DispatcherController::class, 'GetSearchOrdersData'])->name('GetSearchOrdersData');
        Route::get('get-search-order-data-delivered', [DispatcherController::class, 'GetSearchOrdersDataDELIVERED'])->name('GetSearchOrdersDataDELIVERED');


        Route::get('accept-cancel-request', [DispatcherController::class, 'acceptCancelRequest'])->name('acceptCancelRequest');


        Route::get('get-drivers-data', [DispatcherController::class, 'GetDriversData'])->name('GetDriversData');


        Route::get('get-driver-popup', [DispatcherController::class, 'getDriverPopup'])->name('get-driver-popup');


        Route::get('get-map-data', [DispatcherController::class, 'getMapData'])->name('getMapData');


        Route::get('get-clients-orders-data', [DashboardController::class, 'getClientsOrdersData'])->name('get-clients-orders-data');

        Route::get('get-dashboard-drivers-data', [DashboardController::class, 'getDashboardDriversData'])->name('get-dashboard-drivers-data');


        Route::get('get-driver-locations-log', [OperatorController::class, 'getDriverLocationsLog'])->name('get-driver-locations-log');


        Route::post('/upload-branches', [ClientsController::class, 'uploadBranches'])->name('upload-branches');


        Route::get('online_orders', [OnlineOrdersController::class, 'online_orders'])->name('online_orders');
        Route::get('get-online-orders', [OnlineOrdersController::class, 'getDriversWithOrders'])->name('get-online-orders');


        Route::get('order_status/{order_id?}/{status?}', [DispatcherController::class, 'order_status_change'])->name('order_status_change');


        Route::get('edit-client-branch', [ClientsController::class, 'editClientBrnch'])->name('edit-client-branch');
        Route::get('unassign-driver', [DispatcherController::class, 'UnassignDriver'])->name('UnassignDriver');
        Route::get('ChangeStatusToDelivered', [DispatcherController::class, 'ChangeStatusToDelivered'])->name('ChangeStatusToDelivered');





        Route::get('change-client-active', [ClientsController::class, 'changeClientActive'])->name('changeClientActive');





        Route::get('cancel-reasons', [ReasonsController::class, 'cancelReasons'])->name('cancelReasons');

        Route::get('reason-list', [ReasonsController::class, 'reasonList'])->name('reason-list');
        Route::post('save-reason', [ReasonsController::class, 'saveReason'])->name('save-reason');
        Route::get('edit-reason/{id}/edit', [ReasonsController::class, 'editReason'])->name('edit-reason');
        Route::put('update-reason/{id}', [ReasonsController::class, 'updateReason'])->name('update-reason');
        Route::delete('delete-reason/{id}', [ReasonsController::class, 'deleteReason'])->name('delete-reason');


        Route::get('foodics-clients', [FoodicsClientsController::class, 'index'])->name('foodicsClients');


        Route::get('get-foodics-clients-data', [FoodicsClientsController::class, 'getClientsData'])->name('getFoodicsClientsData');

        Route::get('call-client-foodics-api', [FoodicsClientsController::class, 'revokeClientFoodicsToken'])->name('callClientFoodicsAPI');


        Route::get('get-operators-acceptance-rate-less-2', [DispatcherController::class, 'getOperatorsWithAvgPendingMoreThanTwoMinutes'])->name('getOperatorsAcceptanceRateLessTwo');
        Route::get('get-operators-acceptance-rate-more-2', [DispatcherController::class, 'getOperatorsAcceptanceRateMoreTwo'])->name('getOperatorsAcceptanceRateMoreTwo');

        Route::get('get-operators-acceptance-rate-detailes', [DispatcherController::class, 'getOperatorsAcceptanceRateDetailes'])->name('getOperatorsAcceptanceRateDetailes');

        Route::get('get-operators-pending-rate-detailes', [DispatcherController::class, 'getOperatorsPendingRateDetailes'])->name('getOperatorsPendingRateDetailes');


        Route::get('get-per-city-branches-detailes', [ReportNewController::class, 'getPerCityBranchesDetailes'])->name('getPerCityBranchesDetailes');

        Route::group(['prefix' => 'clientupdated'], function () {


            Route::get('/', [ClientsUpdatedController::class, 'index'])->name('clientupdated');
            Route::get('/create', [ClientsUpdatedController::class, 'create'])->name('clientupdated.create');
            Route::post('/store', [ClientsUpdatedController::class, 'store'])->name('clientupdated.store');
            Route::get('/edit/{id}', [ClientsUpdatedController::class, 'edit'])->name('clientupdated.edit');
            Route::post('/update/{id}', [ClientsUpdatedController::class, 'update'])->name('clientupdated.update');
            Route::delete('/destroy/{id}', [ClientsUpdatedController::class, 'destroy'])->name('clientupdated.destroy');

            Route::get('/view/{id}', [ClientsUpdatedController::class, 'view'])->name('clientupdated.view');

            Route::get('/change-client-status', [ClientsUpdatedController::class, 'changeClientStatus'])->name('clientupdated.changeClientStatus');



            Route::get('edit-client-branch', [ClientsUpdatedController::class, 'editClientBrnch'])->name('clientupdated.editClientBrnch');

            Route::post('/save-client-branch', [ClientsUpdatedController::class, 'saveClientBranch'])->name('clientupdated.saveClientBranch');
            Route::get('/get-client-branches', [ClientsUpdatedController::class, 'getClientBranches'])->name('clientupdated.getClientBranches');
            Route::get('/get-client-orders', [ClientsUpdatedController::class, 'getOrders'])->name('clientupdated.getOrders');

            Route::get('/get-users-search', [ClientsUpdatedController::class, 'getUsersSearch'])->name('clientupdated.getUsersSearch');


            Route::get('/change-client-branch-status', [ClientsUpdatedController::class, 'changeClientBranchStatus'])->name('clientupdated.changeClientBranchStatus');
            Route::get('/change-client-branch-auto-dispatch', [ClientsUpdatedController::class, 'changeClientBranchAutoDispatch'])->name('clientupdated.changeClientBranchAutoDispatch');


            Route::post('/upload-branches', [ClientsUpdatedController::class, 'uploadBranches'])->name('clientupdated.uploadBranches');



            Route::post('/save-client-user', [ClientsUpdatedController::class, 'saveClientUser'])->name('clientupdated.saveClientUser');
            Route::get('/get-client-users', [ClientsUpdatedController::class, 'getUsers'])->name('clientupdated.getUsers');

            Route::post('/save-client-exist-user', [ClientsUpdatedController::class, 'saveClientExistUser'])->name('clientupdated.saveClientExistUser');

            Route::get('/edit-client-user', [ClientsUpdatedController::class, 'editClientExistUser'])->name('clientupdated.editClientExistUser');

            Route::post('/update-client-user', [ClientsUpdatedController::class, 'updateClientExistUser'])->name('clientupdated.updateClientExistUser');
            Route::post('/delete-client-user', [ClientsUpdatedController::class, 'deleteClientExistUser'])->name('clientupdated.deleteClientExistUser');
            Route::get('/change-client-user-status', [ClientsUpdatedController::class, 'changeClientUserStatus'])->name('clientupdated.changeClientUserStatus');
        });


        Route::group(['prefix' => 'branchnew'], function () {
            Route::get('/', [BranchController::class, 'index'])->name('branchnew');
            Route::get('/create', [BranchController::class, 'create'])->name('branchnew.create');
            Route::post('/store', [BranchController::class, 'store'])->name('branchnew.store');
            Route::get('/edit/{id}', [BranchController::class, 'edit'])->name('branchnew.edit');
            Route::post('/update/{id}', [BranchController::class, 'update'])->name('branchnew.update');
            Route::delete('/destroy/{id}', [BranchController::class, 'destroy'])->name('branchnew.destroy');
        });
        Route::group(['prefix' => 'ClientsGroupNew'], function () {
            Route::get('/', [ClientsGroupNewController::class, 'index'])->name('ClientsGroupNew');
            Route::get('/create', [ClientsGroupNewController::class, 'create'])->name('ClientsGroupNew.create');
            Route::post('/store', [ClientsGroupNewController::class, 'store'])->name('ClientsGroupNew.store');
            Route::get('/edit/{id}', [ClientsGroupNewController::class, 'edit'])->name('ClientsGroupNew.edit');
            Route::post('/update/{id}', [ClientsGroupNewController::class, 'update'])->name('ClientsGroupNew.update');
            Route::delete('/destroy/{id}', [ClientsGroupNewController::class, 'destroy'])->name('ClientsGroupNew.destroy');
        });

        Route::group(['prefix' => 'zoneNew'], function () {
            Route::get('/', [ZoneNewController::class, 'index'])->name('ZoneNew');
            Route::get('/create', [ZoneNewController::class, 'create'])->name('ZoneNew.create');
            Route::post('/store', [ZoneNewController::class, 'store'])->name('ZoneNew.store');
            Route::get('/edit/{id}', [ZoneNewController::class, 'edit'])->name('ZoneNew.edit');
            Route::post('/update/{id}', [ZoneNewController::class, 'update'])->name('ZoneNew.update');
            Route::delete('/destroy/{id}', [ZoneNewController::class, 'destroy'])->name('ZoneNew.destroy');
        });

        Route::group(['prefix' => 'export'], function () {
            Route::get('/orders', [ExportController::class, 'GetOrders'])->name('export.GetOrders');
            Route::get('/getData', [ExportController::class, 'getData'])->name('export.getData');
            Route::get('/run-export-orders', function () {Artisan::call('orders:export');return 'Job Done';});
        });
    });
});
