<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\LogbookModel;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index()
    {
        return LogbookModel::with('pendaftaran')->get();
    }

    public function store(Request $request)
    {
        return LogbookModel::create($request->all());
    }

    public function show($id)
    {
        return LogbookModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = LogbookModel::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        LogbookModel::destroy($id);
        return response()->json(['message' => 'Logbook dihapus']);
    }
}