<?php
namespace App\Enum;


enum Permissions: string
{
    case SUPER_USER = 'super_user';
    // System Billing
    case VIEW_INVOICE = 'view_invoice';
    case VIEW_TRANSACTION = 'view_transaction';
    case DOWNLOAD_INVOICES = 'download_invoices';
    case PAY_INVOICES = 'pay_invoices';
    case CLIENT_BILLING = 'client_billing';
    case CREDIT_CARD_MANAGEMENT = 'credit_card_management';

    // Clients
    case CONTROL_CLIENTS_NOTIFICATIONS = 'control_clients_notifications';
    case CONTROL_CLIENTS = 'control_clients';
    case CONTROL_CLIENTS_GROUPS = 'control_clients_groups';
    case CONTROL_CLIENTS_WALLET_OPTION = 'control_clients_wallet_option';
    case CONTROL_CLIENTS_PARTIAL_PAY_OPTION = 'control_clients_partial_pay_option';
    case CONTROL_CLIENTS_SHOW_CANCEL_BUTTON = 'control_clients_show_cancel_button';
    case CONTROL_CLIENTS_SHOW_FEES_OPTION = 'control_clients_show_fees_option';
    case CLIENTS_OR_BRANCHES_CAN_ASSIGNED_USER = 'clients_or_branches_can_assigned_user';
    case CONTROL_BRANCH_GROUPS = 'control_branch_groups';
    case CONTROL_CLIENTS_SERVICE_PROVIDERS = 'control_clients_service_providers';
    case COPY_CLIENTS = 'copy_clients';

    // Dashboard
    case SHOW_DASHBOARD = 'show_dashboard';

    // Dispatcher
    case ADVANCED_DISPATCHER_VIEW = 'advanced_dispatcher_view';
    case COMPACT_DISPATCHER_VIEW = 'compact_dispatcher_view';
    case BASIC_DISPATCHER_VIEW = 'basic_dispatcher_view';
    case CAN_GET_DRIVER = 'can_get_driver';
    case CAN_CONTROL_GET_DRIVER_SETTINGS = 'can_control_get_driver_settings';
    case ALLOW_DISPATCHING_CONSOLIDATED_ORDERS = 'allow_dispatching_consolidated_orders';
    case CONTROL_CONSOLIDATED_ORDERS_SETTINGS = 'control_consolidated_orders_settings';
    case CONTROL_AREAS_ZONES = 'control_areas_zones';
    case UPLOAD_ORDERS = 'upload_orders';
    case CAN_ASSIGN_ORDERS = 'can_assign_orders';
    case CAN_UNASSIGN_ORDERS = 'can_unassign_orders';
    case CAN_ACCEPT_CANCEL_REQUEST = 'can_accept_cancel_request';


    case CAN_MAKE_CANCEL_REQUEST = 'can_make_cancel_request';

    // Customers
    case CONTROL_CUSTOMERS = 'control_customers';
    case CONTROL_ADDRESS_CONFIRMATION = 'control_address_confirmation';

    // Users
    case CONTROL_USERS = 'control_users';
    case CONTROL_USERS_PRIVILEGES = 'control_users_privileges';
    case ASSIGN_USERS_CLIENT_BRANCH = 'assign_users_client_branch';
    case CONTROL_USERS_TEMPLATES = 'control_users_templates';

    // Reports
    case VIEW_EXPORT_REPORTS = 'view_export_reports';
    case CLIENT_CUSTOM_REPORTS = 'client_custom_reports';

    case ORDERS_REPORTS = 'orders_report';
    case OPERATOR_REPORTS = 'operators_reports';
    case CLIENT_REPORTS = 'client_reports';
    case UTR_REPORTS = 'utr_reports';
    case ACCOUNTING_CLIENT_REPORTS = 'accounting_client_reports';
    case OPERATORS_ACCEPTANCE_TIME_REPORTS = 'operators_acceptance_time_reports';
    case DISPATCHER_ASSIGN_REPORTS = 'dispatcher_assign_reports';

    // Reports Financial accounts
    case ViewFinancialReportsAdmin = 'view_financial_reports_admin';
    case ClientsSalesReport = 'clientsSalesreport';
    // Drivers
    case CONTROL_DRIVERS = 'control_drivers';
    case CONTROL_DRIVERS_GROUPS = 'control_drivers_groups';
    case CONTROL_DRIVERS_CROWD_OPTION = 'control_drivers_crowd_option';
    case CONTROL_DRIVERS_AUTO_DISPATCH_OPTION = 'control_drivers_auto_dispatch_option';
    case EXPORT_DRIVERS_INFORMATION = 'export_drivers_information';
    case EXPORT_DRIVERS_SCHEDULE = 'export_drivers_schedule';
    case EXPORT_DRIVER_INACTIVE_DURATION = 'export_driver_inactive_duration';
    case CONTROL_DRIVERS_SHIFTS = 'control_drivers_shifts';
    case DRIVERS_ATTENDANCE = 'drivers_attendance';
    case CONTROL_DRIVERS_NOTIFICATIONS = 'control_drivers_notifications';
    case CONTROL_FLEET = 'control_fleet';

    // Driver
    case AUTHORIZED_LOCATIONS = 'authorized_locations';

    // Operator Billings
    case VIEW_BILLINGS = 'view_billings';
    case EXPORT_DRIVERS_BILLINGS = 'export_drivers_billings';
    case CONTROL_DRIVERS_BILLING_ACTIONS = 'control_drivers_billing_actions';
    case CONTROL_DRIVERS_BILLING_TRANSACTIONS = 'control_drivers_billing_transactions';
    case ENABLE_COD_BILLING = 'enable_COD_billing';

    // Orders
    case ORDERS_ADVANCE_VIEW = 'orders_advance_view';
    case ORDERS_ADVANCED_EXPORT = 'orders_advanced_export';
    case ORDERS_BASIC_VIEW = 'orders_basic_view';
    case ORDERS_BASIC_EXPORT = 'orders_basic_export';
    case EDIT_ORDERS = 'edit_orders';
    case previous_orders_basic_view = 'previous_orders_basic_view';

    // Road Assistance
    case ROAD_ASSISTANCE_SERVICE = 'road_assistance_service';
    case UPLOAD_POLICIES = 'upload_policies';
    case VIEW_POLICIES = 'view_policies';
    case VIEW_POLICY = 'view_policy';
    case EDIT_POLICY = 'edit_policy';
    case REMOVE_POLICY = 'remove_policy';
    case ROS_TOWING_ACCESS = 'ROS_towing_access';
    case LOGISTICS_ACCESS = 'logistics_access';

    //vehicles

    case VIEW_VEHICLES = 'view_vehicles';
    case CONTROLE_VEHICLES = "controle_vehicle";
    case can_change_status_to_delivered_orders = "can_change_status_to_delivered_orders";

    // locations
    case VIEW_LOCATIONS = 'view_location';
    case CONTROLE_LOCATION = 'controle_location';

    //integrations

    case VIEW_INTEGRATION = 'view_integration';
    case CONTROLE_INTEGRATION = 'controle_integration';


    case  VIEW_FOODICS_CLIENTS = 'view_foodics_clients';

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_USER => 'Super user',
            // System Billing
            self::VIEW_INVOICE => 'View Invoice',
            self::VIEW_TRANSACTION => 'View Transaction',
            self::DOWNLOAD_INVOICES => 'Download Invoices',
            self::PAY_INVOICES => 'Pay Invoices',
            self::CLIENT_BILLING => 'Client Billing',
            self::CREDIT_CARD_MANAGEMENT => 'Credit Card Management',

            // Clients
            self::CONTROL_CLIENTS_NOTIFICATIONS => 'Control Clients Notifications',
            self::CONTROL_CLIENTS => 'Control Clients',
            self::CONTROL_CLIENTS_GROUPS => 'Control Clients Groups',
            self::CONTROL_CLIENTS_WALLET_OPTION => 'Control Clients Wallet Option',
            self::CONTROL_CLIENTS_PARTIAL_PAY_OPTION => 'Control Clients Partial Pay Option',
            self::CONTROL_CLIENTS_SHOW_CANCEL_BUTTON => 'Control Clients Show Cancel Button',
            self::CONTROL_CLIENTS_SHOW_FEES_OPTION => 'Control Clients Show Fees Option',
            self::CLIENTS_OR_BRANCHES_CAN_ASSIGNED_USER => 'Clients or Branches Can Assigned to User',
            self::CONTROL_BRANCH_GROUPS => 'Control Branch Groups',
            self::CONTROL_CLIENTS_SERVICE_PROVIDERS => 'Control Clients Service Providers',
            self::COPY_CLIENTS => 'Copy Clients',

            // Dashboard
            self::SHOW_DASHBOARD => 'Show Dashboard',

            // Dispatcher
            self::ADVANCED_DISPATCHER_VIEW => 'Advanced Dispatcher View',
            self::COMPACT_DISPATCHER_VIEW => 'Compact Dispatcher View',
            self::BASIC_DISPATCHER_VIEW => 'Basic Dispatcher View',
            self::CAN_GET_DRIVER => 'Can Get Driver',
            self::CAN_CONTROL_GET_DRIVER_SETTINGS => 'Can Control Get Driver Settings',
            self::ALLOW_DISPATCHING_CONSOLIDATED_ORDERS => 'Allow Dispatching Consolidated Orders',
            self::CONTROL_CONSOLIDATED_ORDERS_SETTINGS => 'Control Consolidated Orders Settings',
            self::CONTROL_AREAS_ZONES => 'Control Areas Zones',
            self::UPLOAD_ORDERS => 'Upload Orders',
            self::CAN_ASSIGN_ORDERS => 'Can Assign Orders',
            self::CAN_UNASSIGN_ORDERS => 'Can Unassign Orders',
            self::CAN_ACCEPT_CANCEL_REQUEST => 'Can Accept Cancel Request',
            self::CAN_MAKE_CANCEL_REQUEST => 'Can make cancel request',
            self::can_change_status_to_delivered_orders => 'Can Change Status To Delivered Orders',

            // Customers
            self::CONTROL_CUSTOMERS => 'Control Customers',
            self::CONTROL_ADDRESS_CONFIRMATION => 'Control Address Confirmation',

            // Users
            self::CONTROL_USERS => 'Control Users',
            self::CONTROL_USERS_PRIVILEGES => 'Control Users Privileges',
            self::ASSIGN_USERS_CLIENT_BRANCH => 'Assign Users to Client/Branch',
            self::CONTROL_USERS_TEMPLATES => 'Control Users Templates',

            // Reports
            self::VIEW_EXPORT_REPORTS => 'View & Export Reports',
            self::CLIENT_CUSTOM_REPORTS => 'Client Custom Reports',



            self::ORDERS_REPORTS => 'Orders Reports',
            self::OPERATOR_REPORTS => 'Operators Reports',
            self::CLIENT_REPORTS => 'Client Reports',
            self::UTR_REPORTS => 'URT Reports',
            self::ACCOUNTING_CLIENT_REPORTS => 'Accounting Client Reports',
            self::OPERATORS_ACCEPTANCE_TIME_REPORTS => 'Operators Acceptance Time Reports',
            self::DISPATCHER_ASSIGN_REPORTS => 'Dispatcher Assign Reports',





            // Drivers
            self::CONTROL_DRIVERS => 'Control Drivers',
            self::CONTROL_DRIVERS_GROUPS => 'Control Drivers Groups',
            self::CONTROL_DRIVERS_CROWD_OPTION => 'Control Drivers Crowd Option',
            self::CONTROL_DRIVERS_AUTO_DISPATCH_OPTION => 'Control Drivers Auto Dispatch Option',
            self::EXPORT_DRIVERS_INFORMATION => 'Export Drivers Information',
            self::EXPORT_DRIVERS_SCHEDULE => 'Export Drivers Schedule',
            self::EXPORT_DRIVER_INACTIVE_DURATION => 'Export Driver Inactive Duration',
            self::CONTROL_DRIVERS_SHIFTS => 'Control Drivers Shifts',
            self::DRIVERS_ATTENDANCE => 'Drivers Attendance',
            self::CONTROL_DRIVERS_NOTIFICATIONS => 'Control Drivers Notifications',
            self::CONTROL_FLEET => 'Control Fleet',

            // Driver
            self::AUTHORIZED_LOCATIONS => 'Authorized Locations',

            // Operator Billings
            self::VIEW_BILLINGS => 'View Billings',
            self::EXPORT_DRIVERS_BILLINGS => 'Export Drivers Billings',
            self::CONTROL_DRIVERS_BILLING_ACTIONS => 'Control Drivers Billing Actions',
            self::CONTROL_DRIVERS_BILLING_TRANSACTIONS => 'Control Drivers Billing Transactions',
            self::ENABLE_COD_BILLING => 'Enable COD Billing',

            // Orders
            self::ORDERS_ADVANCE_VIEW => 'Orders Advance View',
            self::ORDERS_ADVANCED_EXPORT => 'Orders Advanced Export',
            self::ORDERS_BASIC_VIEW => 'Orders Basic View',
            self::ORDERS_BASIC_EXPORT => 'Orders Basic Export',
            self::EDIT_ORDERS => 'Edit Orders',

            // Road Assistance
            self::ROAD_ASSISTANCE_SERVICE => 'Road Assistance Service',
            self::UPLOAD_POLICIES => 'Upload Policies',
            self::VIEW_POLICIES => 'View Policies',
            self::VIEW_POLICY => 'View Policy',
            self::EDIT_POLICY => 'Edit Policy',
            self::REMOVE_POLICY => 'Remove Policy',
            self::ROS_TOWING_ACCESS => 'ROS Towing Access',
            self::LOGISTICS_ACCESS => 'Logistics Access',

            //vehicles

            self::VIEW_VEHICLES => 'View Vehicles',
            self::CONTROLE_VEHICLES => 'Controle Vehicle',
            self::CONTROLE_LOCATION => 'Controle Location',
            self::CONTROLE_INTEGRATION => 'Controle Integration',

            self::VIEW_FOODICS_CLIENTS => 'View Foodics Clients',


            //locations
            self::VIEW_LOCATIONS => 'View Location',

            //integration

            self::VIEW_INTEGRATION => 'View Integration',
            // Reports Financial accounts
            self::ViewFinancialReportsAdmin => 'View Financial Reports Admin',
            self::ClientsSalesReport => 'Clients Sales Report',
            // Default
            default => ucfirst(str_replace('_', ' ', strtolower($this->value))),
        };
    }

}
