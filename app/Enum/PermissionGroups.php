<?php

namespace App\Enum;

use App\Models\Permission;

enum PermissionGroups: string
{
        // System Billing
    case SYSTEM_BILLIN = 'system_billing';
        // Clients
    case CLIENTS = 'clients';

        // Dashboard
    case DASHBOARD = 'dashboard';

        // Dispatcher
    case DISPATCHER = 'dispatcher';

        // Customers
    case CUSTOMERS = 'customer';

        // Users
    case USERS = 'users';
        // Reports
    case REPORTS = 'reports';

        // Drivers
    case DRIVERS = 'drivers';

        // Driver
    case DRIVER = 'driver';

        // Operator Billings
    case OPERATOR_BILLINGS = 'operator_billings';

        // Orders
    case ORDERS = 'orders';

        // Road Assistance
    case ROAD_ASSISTANCE = 'road_assistance';

    case VEHICLES = 'vehicles';
    case LOCATIONS = 'locations';
    case INTEGRATIONS = 'Integrations';


    case SUPER_USER = 'super_user';
    // FINANCIAL_REPORTS
    case FINANCIAL_REPORTS = 'financial_reports';
    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_USER => 'Super user',
            self::SYSTEM_BILLIN => 'System Billing',
            self::CLIENTS => 'Clients',
            self::DASHBOARD => 'Dashboard',
            self::DISPATCHER => 'Dispatcher',
            self::CUSTOMERS => 'Customers',
            self::USERS => 'Users',
            self::REPORTS => 'Reports',
            self::DRIVERS => 'Drivers',
            self::DRIVER => 'Driver',
            self::OPERATOR_BILLINGS => 'Operator Billings',
            self::ORDERS => 'Orders',
            self::ROAD_ASSISTANCE => 'Road Assistance',
            self::VEHICLES => 'Vehicles',
            self::LOCATIONS => 'Locations',
            self::INTEGRATIONS => 'Integrations',
            self::FINANCIAL_REPORTS => 'FINANCIAL Reports',


                // Default
            default => ucfirst(str_replace('_', ' ', strtolower($this->value))),
        };
    }








    public function getPermissions()
    {
        return match ($this) {

            self::SUPER_USER => [Permissions::SUPER_USER,Permissions::ViewFinancialReportsAdmin ,Permissions::ClientsSalesReport],

            self::SYSTEM_BILLIN => [Permissions::VIEW_INVOICE, Permissions::VIEW_TRANSACTION, Permissions::DOWNLOAD_INVOICES, Permissions::PAY_INVOICES, Permissions::CLIENT_BILLING, Permissions::CREDIT_CARD_MANAGEMENT],
            self::CLIENTS => [Permissions::COPY_CLIENTS, Permissions::CONTROL_CLIENTS_SERVICE_PROVIDERS, Permissions::CONTROL_BRANCH_GROUPS, Permissions::CLIENTS_OR_BRANCHES_CAN_ASSIGNED_USER, Permissions::CONTROL_CLIENTS_SHOW_FEES_OPTION, Permissions::CONTROL_CLIENTS_SHOW_CANCEL_BUTTON, Permissions::CONTROL_CLIENTS_PARTIAL_PAY_OPTION, Permissions::CONTROL_CLIENTS_WALLET_OPTION, Permissions::CONTROL_CLIENTS_GROUPS, Permissions::CONTROL_CLIENTS, Permissions::CONTROL_CLIENTS_NOTIFICATIONS],
            self::DASHBOARD => [Permissions::SHOW_DASHBOARD],
            self::DISPATCHER => [Permissions::CAN_MAKE_CANCEL_REQUEST,Permissions::can_change_status_to_delivered_orders ,Permissions::CAN_ACCEPT_CANCEL_REQUEST, Permissions::CAN_UNASSIGN_ORDERS, Permissions::CAN_ASSIGN_ORDERS, Permissions::UPLOAD_ORDERS, Permissions::CONTROL_AREAS_ZONES, Permissions::CONTROL_CONSOLIDATED_ORDERS_SETTINGS, Permissions::ALLOW_DISPATCHING_CONSOLIDATED_ORDERS, Permissions::CAN_CONTROL_GET_DRIVER_SETTINGS, Permissions::CAN_GET_DRIVER, Permissions::BASIC_DISPATCHER_VIEW, Permissions::COMPACT_DISPATCHER_VIEW, Permissions::ADVANCED_DISPATCHER_VIEW],
            self::CUSTOMERS => [Permissions::CONTROL_ADDRESS_CONFIRMATION, Permissions::CONTROL_CUSTOMERS],
            self::USERS => [Permissions::CONTROL_USERS_TEMPLATES, Permissions::ASSIGN_USERS_CLIENT_BRANCH, Permissions::CONTROL_USERS_PRIVILEGES, Permissions::CONTROL_USERS],
            self::REPORTS => [Permissions::DISPATCHER_ASSIGN_REPORTS , Permissions::UTR_REPORTS , Permissions::ACCOUNTING_CLIENT_REPORTS, Permissions::OPERATORS_ACCEPTANCE_TIME_REPORTS , Permissions::ORDERS_REPORTS, Permissions::OPERATOR_REPORTS, Permissions::CLIENT_REPORTS , Permissions::CLIENT_CUSTOM_REPORTS, Permissions::VIEW_EXPORT_REPORTS],
            self::DRIVERS => [Permissions::CONTROL_FLEET, Permissions::CONTROL_DRIVERS_NOTIFICATIONS, Permissions::DRIVERS_ATTENDANCE, Permissions::CONTROL_DRIVERS_SHIFTS, Permissions::EXPORT_DRIVER_INACTIVE_DURATION, Permissions::EXPORT_DRIVERS_SCHEDULE, Permissions::EXPORT_DRIVERS_INFORMATION, Permissions::CONTROL_DRIVERS_AUTO_DISPATCH_OPTION, Permissions::CONTROL_DRIVERS_CROWD_OPTION, Permissions::CONTROL_DRIVERS_GROUPS, Permissions::CONTROL_DRIVERS,],
            self::DRIVER => [Permissions::AUTHORIZED_LOCATIONS],
            self::OPERATOR_BILLINGS =>  [Permissions::ENABLE_COD_BILLING, Permissions::CONTROL_DRIVERS_BILLING_TRANSACTIONS, Permissions::CONTROL_DRIVERS_BILLING_ACTIONS, Permissions::EXPORT_DRIVERS_BILLINGS, Permissions::VIEW_BILLINGS],
            self::ORDERS =>  [Permissions::previous_orders_basic_view,Permissions::EDIT_ORDERS, Permissions::ORDERS_BASIC_EXPORT, Permissions::ORDERS_BASIC_VIEW, Permissions::ORDERS_ADVANCED_EXPORT, Permissions::ORDERS_ADVANCE_VIEW],
            self::ROAD_ASSISTANCE =>  [Permissions::LOGISTICS_ACCESS, Permissions::ROS_TOWING_ACCESS, Permissions::REMOVE_POLICY, Permissions::EDIT_POLICY, Permissions::VIEW_POLICY, Permissions::VIEW_POLICIES, Permissions::UPLOAD_POLICIES, Permissions::ROAD_ASSISTANCE_SERVICE],
            self::VEHICLES => [Permissions::VIEW_VEHICLES, Permissions::CONTROLE_VEHICLES],
            self::LOCATIONS => [Permissions::VIEW_LOCATIONS, Permissions::CONTROLE_LOCATION],
            self::INTEGRATIONS => [Permissions::VIEW_INTEGRATION, Permissions::CONTROLE_INTEGRATION, Permissions::VIEW_FOODICS_CLIENTS],
            self::FINANCIAL_REPORTS => [Permissions::ViewFinancialReportsAdmin ,Permissions::ClientsSalesReport],

        };
    }
}
