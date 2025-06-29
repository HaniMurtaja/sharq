<?php

namespace App\Enum;



enum UserRole: int  {
    case ADMIN = 1;
    case CLIENT = 2;
    case OPERATOR= 3;
    case DISPATCHER= 4;
    case BRANCH = 5;
    case REPORTS = 6;
    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
        // ["deposit" => "Deposit", "withdraw" => "Withdraw"]
    }


    public function getLabel(): ?string {
        return match($this) {
            
            self::ADMIN => 'Admin',
            self::CLIENT => 'Client',
            self::OPERATOR => 'Operator',
            self::DISPATCHER => 'Dispatcher',
            self::BRANCH => 'Branch',
            self::REPORTS => 'Reports',

        };
    }



    public function GetALl()
    {
        return[
            'ADMIN' => 1,
            'DISPATCHER' => 'Dispatcher',
        ];
    }


}
