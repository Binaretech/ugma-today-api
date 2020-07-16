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
            "data": [
                {
                    "id": 1,
                    "name": "Odontología",
                    "comment": "Why, I haven't had a pencil that squeaked. This of course, I meant,' the King repeated angrily, 'or I'll have you executed on.",
                    "price": "325.07",
                    "currency": 1
                }
            ]
        }
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
            "data": [
                {
                    "id": 1,
                    "modified_by": {
                        "id": 1,
                        "username": "mari_conazo",
                        "status": 1,
                        "type": 1,
                        "profile": {
                            "name": "Adalberto Klein",
                            "lastname": "Prosacco",
                            "email": "hintz.bailey@example.org"
                        }
                    },
                    "name": "Odontología",
                    "comment": "Why, I haven't had a pencil that squeaked. This of course, I meant,' the King repeated angrily, 'or I'll have you executed on.",
                    "price": "325.07",
                    "currency": 1
                }
            ]
        }
     * @return \Illuminate\Http\Response
     */
    public function index_admin(Request $request)
    {
        return CostResource::collection(
            Cost::paginate($request->pagination ?? 10)->load('modified_by')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_data = $request->validate(Cost::STORE_RULES);

        $cost = Cost::create($request_data);

        return new CostResource($cost);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
