<?php
    namespace App\Http\Resources\Api;

    use Illuminate\Http\Resources\Json\JsonResource;

    class WalletTransactionResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            //check language and translate to arabic
            if($request->header('lang') == 'ar'){
                $type=$this->type=='deposit'?'ايداع':'سحب';

            }else{
                $type=$this->type;
            }

            return [
                'id' => $this->id,
                'amount' => round($this->amount,2).'',
                'type' => $type,
                'description' => $this->description,
                'model_id' => $this->model_id,
                'model_type' => $this->model_type,
                'date' => $this->created_at,
            ];
        }
    }
