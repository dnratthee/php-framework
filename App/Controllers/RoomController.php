<?php

namespace App\Controllers;

use App\Libs\Auth\Authorizable;
use App\Libs\Controller;
use App\Models\Room;
use App\Libs\Request;
use App\Libs\Response;

class RoomController extends Controller
{
    use Authorizable;

    public function getAll(Request $request)
    {

        $rooms = new Room;

        if ($request->select) {
            $rooms->select($request->select);
        }

        if ($request->where) {
            $rooms->where($request->where);
        }

        $rooms->orderBy("datesave", "Desc")
            ->orderBy("timesave", "Desc");

        if ($request->limit) {
            $rooms->limit($request->limit);
        }

        if ($request->offset) {
            $rooms->offset($request->offset);
        }

        Response::json(
            $rooms->get()
        );
    }

    public function getOne(Request $request)
    {
        Response::json(
            Room::find($request->id)
                ->first()
        );
    }

    public function store(Request $request)
    {
        Response::json(
            (new Room($request->all()))
                ->save()
        );
    }

    public function update(Request $request)
    {
        Response::json(
            Room::find($request->id)
                ->fill($request->all())
                ->save()
        );
    }

    public function delete(Request $request)
    {
        Response::json(
            Room::destroy($request->id)
        );
    }

    public function index(Request $request)
    {
        Response::render('room', ["rooms" => Room::all()->get()]);
    }
}
