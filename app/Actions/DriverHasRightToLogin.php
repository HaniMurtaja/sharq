<?php

namespace App\Actions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Exceptions\APIException;

class DriverHasRightToLogin
{
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->hasRoleOperator();

    }

    /**
     * @throws Exception
     */
    public function hasRoleOperator(): DriverHasRightToLogin
    {
        if (!auth()->user()->hasRole('operator')) {
            return(__('Cant login as driver'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    

    /**
     * @throws Exception
     */
  

    /**
     * @throws Exception
     */
   

}
