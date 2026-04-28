<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::all();
    }

    public function store(Request $request)
    {
        return UserModel::create($request->all());
    }

    public function show($id)
    {
        return UserModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = UserModel::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        UserModel::destroy($id);
        return response()->json(['message' => 'User dihapus']);
    }
}