<?php
    namespace App\Http\Resources\Api;

    use Illuminate\Http\Resources\Json\ResourceCollection;

    class DriverOrderCollection extends ResourceCollection
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            return [
                'data' => OrderResource::collection($this->collection),
                'pagination' => [
                    'total' => $this->total(),
                    'count' => $this->count(),
                    'per_page' => $this->perPage(),
                    'current_page' => $this->currentPage(),
                    'total_pages' => $this->lastPage(),
                    'is_pagination' => $this->lastPage() <= $this->currentPage() ? FALSE : TRUE,
                ],
            ];
        }
    }
