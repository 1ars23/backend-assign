<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    // List all timesheets
    public function index()
    {
        return response()->json(Timesheet::all());
    }

    // Show a single timesheet
    public function show($id)
    {
        $timesheet = Timesheet::findOrFail($id);
        return response()->json($timesheet);
    }

    // Create a new timesheet
    public function store(Request $request)
    {
        $timesheet = new Timesheet();
        $timesheet->task_name = $request->task_name;
        $timesheet->date = $request->date;
        $timesheet->hours = $request->hours;
        $timesheet->user_id = $request->user_id;
        $timesheet->project_id = $request->project_id;
        $timesheet->save();

        return response()->json($timesheet, 201);
    }

    // Update a timesheet
    public function update(Request $request, $id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update($request->all());

        return response()->json($timesheet);
    }

    // Delete a timesheet
    public function destroy($id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->delete();

        return response()->json(['message' => 'Timesheet deleted successfully'], 200);
    }
}
