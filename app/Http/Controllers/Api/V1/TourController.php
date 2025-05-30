<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    public function index(Travel $travel, ToursListRequest $request)
    {
        $tours = $travel->tours()
            ->when($request->dateFrom, function ($query) use ($request) {
                return $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                return $query->where('ending_date', '<=', $request->dateTo);
            })
            ->when($request->priceFrom, function ($query) use ($request) {
                return $query->where('price', '>=', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                return $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                return $query->orderBy($request->sortBy, $request->sortOrder);
            })
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }
}
