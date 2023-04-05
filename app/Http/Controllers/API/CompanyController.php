<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class CompanyController extends Controller
{
    /**
     * get fetch company
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

        // query companies
        $companies = Company::with(['users'])->whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        });

        if($id) 
        {
            // if id exisit 
            $company = $companies->find($id);

            if($company) 
            {
                return ResponseFormatter::success($company, 'Company found');
            }

            return ResponseFormatter::error('Company not found!', 404);
        }

        if($name) 
        {
            // if name exisit 
            $companies->where('name', 'like', '%'.$name.'%');
        }

        // return response
        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
        );
    }

    /**
     * create company
     * 
     * @param CreateCompanyRequest
     * @return \JsonResponse
     * */ 
    public function create(CreateCompanyRequest $request)
    {
        try {
            // upload photo
            $path = null;
            if($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]); 
            
            // check if company exists
            if(!$company) {
                throw new Exception("Company not create");
            }
            
            // attach company to user
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // load users at company 
            $company->load("users");
            
            return ResponseFormatter::success($company, 'Company Created');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    /**
     * updatae company
     * 
     * @param UpdateCompanyRequest
     * @return \JsonResponse
     * */ 
    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            // get company
            $company = Company::find($id);

            // check if company exist
            if(!$company) {
                throw new \Exception("Company not found");
            }

            // upload logo
            $path = $company->logo;
            if($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // update company
            $company->update([
                'name' => $request->name,
                'logo' => $path,
            ]); 

            return ResponseFormatter::success($company, 'Company Updated');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
