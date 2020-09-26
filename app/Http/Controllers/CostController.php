<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Exceptions\DatabaseException;
use App\Http\Resources\CostResource;
use Illuminate\Http\Request;

/**
 * @group Cost
 * 
 * Cost routes
 */
class CostController extends Controller
{
    /**
     * Display a listing of registered costs.
     * @response 200 {
     *   "ids": [
     *      1
     *   ],
     * "data": {
     *      "1": {
     *         "id": 1,
     *             "name": "Odontología",
     *             "comment": "Said his father; 'don't give yourself airs! Do you think, at your age, it is right?' 'In my youth,' said the Mock Turtle.",
     *             "price": "27885316.06",
     *             "currency": 0,
     *             "currencyName": "Bs",
     *         }
     *     },
     *  }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = $request->pagination ?? 10;

        if ($request->is('api/admin/cost')) {
            $costs = Cost::with('modified_by')->paginate($pagination);

            return (CostResource::collection($costs))->resource;
        }

        return (CostResource::collection(Cost::paginate($pagination)))->resource;
    }

    /**
     * @authenticated
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @bodyParam name string required User's first name. Example: lorem@gmail.com
     * @bodyParam price string required price to register.
     * @bodyParam currency string required cost currency.
     * @bodyParam comment string optional aditional comment.
     * @bodyParam withTimestamps boolean optional Show records timestamps
     * @response 201 {
     * "data": {
     *     "id": 73,
     *     "name": "Ingeniería",
     *     "price": 10000,
     *     "currency": 0
     *  }
     * }
     */
    public function store(Request $request)
    {
        $request_data = $request->validate(Cost::STORE_RULES);

        Cost::create($request_data);

        return response()->json(['message' => trans('responses.success')], 201);
    }

    /**
     * Show cost record by id
     *
     * @param  Cost  $cost
     * @return CostResource
     * 
     * @urlParam cost required Cost by id. Example: 2
     * @response 200 {
     * "data": {
     *     "id": 73,
     *     "name": "Ingeniería",
     *     "price": 10000,
     *     "currency": 0
     *  }
     * }
     */
    public function show(Cost $cost)
    {
        return new CostResource($cost);
    }

    /**
     * @authenticated
     * Show cost record by id with admin data
     *
     * @param  Cost  $cost
     * @return CostResource
     * 
     * @urlParam cost required Cost by id. Example: 2
     * @response 200 {
     *  "data": {
     *       "id": 1,
     *       "modified_by": {
     *           "id": 1,
     *           "username": "mari_conazo",
     *           "status": 1,
     *           "type": 1,
     *           "profile": {
     *            "name": "Dr. Zoe Corkery DDS",
     *            "lastname": "DuBuque",
     *            "email": "fprosacco@example.com"
     *          }
     *      },
     *       "name": "Odontología",
     *       "comment": "It was all ridges and furrows; the balls were live hedgehogs, the mallets live flamingoes, and the roof was thatched with fur.",
     *       "price": "1018952.61",
     *       "currency": 0
     *      }
     *  }
     */
    public function show_admin(Cost $cost)
    {
        return new CostResource($cost->load('modified_by'));
    }

    /**
     * Update the specified cost in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cost $cost)
    {
        $request_data = $request->validate(Cost::UPDATE_RULES);

        if (!$cost->fill($request_data)->save()) {
            throw new DatabaseException(trans('exception.CostController.update'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }

    /**
     * Remove the specified cost from storage.
     *
     * @param  Cost  $cost
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cost $cost)
    {
        if (!$cost->delete()) {
            throw new DatabaseException(trans('exception.CostController.update'), 500);
        }

        return response()->json(['message' => trans('responses.success')]);
    }
}
