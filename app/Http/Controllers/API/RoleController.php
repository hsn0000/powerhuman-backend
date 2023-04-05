<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

use App\Models\Role;

class RoleController extends Controller
{
    /**
     * get fetch role
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
        $with_responsibilities = $request->input('with_responsibilities', false);

        // query role
        $roles = Role::query();

        if($id) 
        {
            // if id exisit 
            $role = $roles->with('responsibilities')->find($id);

            if($role) 
            {
                return ResponseFormatter::success($tean, 'Role found');
            }

            return ResponseFormatter::error('Role not found!', 404);
        }

        // get multiple data
        $roles = $roles->where('company_id', $request->company_id);

        if($name) 
        {
            // if name exisit 
            $roles->where('name', 'like', '%'.$name.'%');
        }

        if($with_responsibilities) 
        {
            // with responsibilities
            $roles->with('responsibilities');
        }

        // return response
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Role found'
        );
    }


    /**
     * create role
     * 
     * @param CreateRoleRequest
     * @return \JsonResponse
     * */ 
    public function create(CreateRoleRequest $request)
    {
        try {
            // create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]); 
            
            // check if role exists
            if(!$role) {
                throw new \Exception("Role not create");
            }
            
            return ResponseFormatter::success($role, 'Role Created');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    /**
     * update role
     * 
     * @param UpdateRoleRequest
     * @return \JsonResponse
     * */ 
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            // get role
            $role = Role::find($id);

            // check if role exist
            if(!$role) {
                throw new \Exception("Role not found");
            }

            // update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]); 

            return ResponseFormatter::success($role, 'Role Updated');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }

    /**
     * delete role
     * 
     * @param integer $id
     * @return \JsonResponse
     * */ 
    public function destroy($id)
    {
        try {
            // get role
            $role = Role::find($id);

            // TODO: check if role is owned by user

            // check if role exist
            if(!$role) {
                throw new \Exception("Role not found");
            }

            // delete role
            $role->delete();

            return ResponseFormatter::success($role, 'Role Delete');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
