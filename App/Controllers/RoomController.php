<?php

namespace App\Controllers;

use App\Libs\Controller;
use App\Models\Room;
use App\Libs\Request;
use App\Libs\Response;

class RoomController extends Controller
{
    public function getAll(Request $request)
    {       
        Response::json(
            Room::all()->get()
        );
    }

    public function getOne(Request $request)
    {
        Response::json(
            Room::find($request->id)
                ->get()
        );
    }

    public function store(Request $request)
    {
        Response::json(
            (new Room($request->getBody()))
                ->save()
        );
    }

    public function update(Request $request)
    {
        Response::json(
            Room::find($request->id)
                ->fill($request->getBody())
                ->save()
        );
    }

    public function delete(Request $request)
    {
        Response::json(
            Room::destroy($request->id)
        );
        // Room::find($request->id)->delete()->toJson();
    }

    public function temp1(Request $request)
    {
        Response::json(
            Room::where(column: 'temp1', operator: '>', value: $request->temp1)
                ->get()
        );
    }

    public function index(Request $request)
    {
        Response::render('room', ["rooms" => Room::all()->get()]);
    }
}
