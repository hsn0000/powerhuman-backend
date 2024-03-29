<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * get fetch employee
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function fetch(Request $request)
    {
        // set devine variable
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $company_id = $request->input('company_id');
        $limit = $request->input('limit', 10);

        // query employee
        $employees = Employee::query();

        if($id) 
        {
            // if id exisit 
            $employee = $employees->with(['team','role'])->find($id);

            if($employee) 
            {
                return ResponseFormatter::success($employee, 'Employee found');
            }

            return ResponseFormatter::error('Employee not found!', 404);
        }

        // get multiple data
        if($name) 
        {
            $employees->where('name', 'like', '%'.$name.'%');
        }

        if($email) 
        {
            $employees->where('email', $email);
        }

        if($age) 
        {
            $employees->where('age', $age);
        }

        if($phone) 
        {
            $employees->where('phone', 'like', '%'.$phone.'%');
        }

        if($team_id) 
        {
            $employees->where('team_id', $team_id);
        }

        if($role_id) 
        {
            $employees->where('role_id', $role_id);
        }

        if($company_id)
        {
            $employees->whereHas('team', function($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }

        // return response
        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employee found'
        );
    }


    /**
     * create employee
     * 
     * @param CreateEmployeeRequest
     * @return \JsonResponse
     * */ 
    public function create(CreateEmployeeRequest $request)
    {
        try {
            // upload photo
            $path = null;
            if($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // create employee
            $employee = Employee::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'gender'  => $request->gender,
                'age'     => $request->age,
                'phone'   => $request->phone,
                'photo'   => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id
            ]); 
            
            // check if employee exists
            if(!$employee) {
                throw new \Exception("Employee not create");
            }
            
            return ResponseFormatter::success($employee, 'Employee Created');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    /**
     * update employee
     * 
     * @param UpdateEmployeeRequest
     * @return \JsonResponse
     * */ 
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            // get employee
            $employee = Employee::find($id);

            // check if employee exist
            if(!$employee) {
                throw new \Exception("Employee not found");
            }

            // upload photo
            $path = $employee->photo;
            if($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // update employee
            $employee->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'gender'  => $request->gender,
                'age'     => $request->age,
                'phone'   => $request->phone,
                'photo'   => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id
            ]); 

            return ResponseFormatter::success($employee, 'Employee Updated');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }

    /**
     * delete employee
     * 
     * @param integer $id
     * @return \JsonResponse
     * */ 
    public function destroy($id)
    {
        try {
            // get employee
            $employee = Employee::find($id);

            // TODO: check if employee is owned by user

            // check if employee exist
            if(!$employee) {
                throw new \Exception("Employee not found");
            }

            // delete employee
            $employee->delete();

            return ResponseFormatter::success($employee, 'Employee Delete');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
