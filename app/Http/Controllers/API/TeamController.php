<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

use App\Models\Team;

class TeamController extends Controller
{
    /**
     * get fetch team
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

        // query team
        $teams = Team::query();

        if($id) 
        {
            // if id exisit 
            $team = $teams->find($id);

            if($team) 
            {
                return ResponseFormatter::success($team, 'Team found');
            }

            return ResponseFormatter::error('Team not found!', 404);
        }

        // get multiple data
        $teams = $teams->where('company_id', $request->company_id);

        if($name) 
        {
            // if name exisit 
            $teams->where('name', 'like', '%'.$name.'%');
        }

        // return response
        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Team found'
        );
    }


    /**
     * create team
     * 
     * @param CreateTeamRequest
     * @return \JsonResponse
     * */ 
    public function create(CreateTeamRequest $request)
    {
        try {
            // upload icon
            $path = null;
            if($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id
            ]); 
            
            // check if team exists
            if(!$team) {
                throw new \Exception("Team not create");
            }
            
            return ResponseFormatter::success($team, 'Team Created');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    /**
     * update team
     * 
     * @param UpdateTeamRequest
     * @return \JsonResponse
     * */ 
    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            // get team
            $team = Team::find($id);

            // check if team exist
            if(!$team) {
                throw new \Exception("Team not found");
            }

            // upload icon
            $path = $team->icon;
            if($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // update team
            $team->update([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id
            ]); 

            return ResponseFormatter::success($team, 'Team Updated');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }

    /**
     * delete team
     * 
     * @param integer $id
     * @return \JsonResponse
     * */ 
    public function destroy($id)
    {
        try {
            // get team
            $team = Team::find($id);

            // TODO: check if team is owned by user

            // check if team exist
            if(!$team) {
                throw new \Exception("Team not found");
            }

            // delete team
            $team->delete();

            return ResponseFormatter::success($team, 'Team Delete');

        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
