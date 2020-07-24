<?php

namespace App\Http\Controllers;

use App\Cost;
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
     *   "data": [
     *       {
     *           "id": 1,
     *           "name": "Odontología",
     *           "comment": "Why, I haven't had a pencil that squeaked. This of course, I meant,' the King repeated angrily, 'or I'll have you executed on.",
     *           "price": "325.07",
     *           "currency": 1
     *       }
     *   ]
     *  }
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CostResource::collection(
            Cost::paginate($request->pagination ?? 10)
        );
    }

    /**
     * @authenticated
     * Display a listing of registered costs (for admins).
     * @response 200 {
     *   "data": [
     *           {
     *               "id": 1,
     *               "modified_by": {
     *                   "id": 1,
     *                   "username": "mari_conazo",
     *                   "status": 1,
     *                   "type": 1,
     *                   "profile": {
     *                       "name": "Adalberto Klein",
     *                       "lastname": "Prosacco",
     *                       "email": "hintz.bailey@example.org"
     *                   }
     *               },
     *               "name": "Odontología",
     *               "comment": "Why, I haven't had a pencil that squeaked. This of course, I meant,' the King repeated angrily, 'or I'll have you executed on.",
     *               "price": "325.07",
     *               "currency": 1
     *           }
     *       ]
     *   }
     * @return \Illuminate\Http\Response
     */
    public function index_admin(Request $request)
    {
        return CostResource::collection(
            Cost::paginate($request->pagination ?? 10)->load('modified_by')
        );
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

        return new CostResource(Cost::create($request_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cost $cost)
    {
        return new CostResource($cost);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cost $cost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
