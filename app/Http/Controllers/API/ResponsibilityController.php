<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

use App\Models\Responsibility;

class ResponsibilityController extends Controller
{
    /**
     * get fetch responsibility
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function fetch(Request $request)
    {
        // set devine variable
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // query responsibility
        $responsibilitis = Responsibility::query();

        if($id) 
        {
            // if id exisit 
            $responsibility = $responsibilitis->find($id);

            if($responsibility) 
            {
                return ResponseFormatter::success($tean, 'Responsibility found');
            }

            return ResponseFormatter::error('Responsibility not found!', 404);
        }

        // get multiple data
        $responsibilitis = $responsibilitis->where('role_id', $request->role_id);

        if($name) 
        {
            // if name exisit 
            $responsibilitis->where('name', 'like', '%'.$name.'%');
        }

        // return response
        return ResponseFormatter::success(
            $responsibilitis->paginate($limit),
            'Responsibility found'
        );
    }


    /**
     * create responsibility
     * 
     * @param CreateResponsibilityRequest
     * @return \JsonResponse
     * */ 
    public function create(CreateResponsibilityRequest $request)
    {
        try {
            // create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]); 
            
            // check if responsibility exists
            if(!$responsibility) {
                throw new \Exception("Responsibility not create");
            }
            
            return ResponseFormatter::success($responsibility, 'Responsibility Created');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    /**
     * delete responsibility
     * 
     * @param integer $id
     * @return \JsonResponse
     * */ 
    public function destroy($id)
    {
        try {
            // get responsibility
            $responsibility = Responsibility::find($id);

            // TODO: check if responsibility is owned by user

            // check if responsibility exist
            if(!$responsibility) {
                throw new \Exception("Responsibility not found");
            }

            // delete responsibility
            $responsibility->delete();

            return ResponseFormatter::success($responsibility, 'Responsibility Delete');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
