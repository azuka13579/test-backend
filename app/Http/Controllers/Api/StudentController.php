<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Requests\StudentRequest;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
   public function index(Request $request) {
   
    $query = Student::latest();

    $query->when($request->search, function ($q, $search) {
        return $q->where(function ($subQuery) use ($search) {
            $subQuery->where('name', 'ilike', "%{$search}%");
        });
    });

    $students = $query->paginate($request->pagination);

    return StudentResource::collection($students)->additional([
        'status' => 'Success',
        'message' => 'List of students'
    ]);
}

    public function store(StudentRequest $request){
        
        
        // Gate::authorize('create', Post::class);
        $student = Student::create($request->validated());
        
        if(!$student){
            return response()->json([
                'status' => 'Error',
                'message' => 'Student tidak ditemukan'
            ], 500);
        }

        return (new StudentResource($student))->additional([
            'status' => 'Success',
            'message' => 'Student berhasil ditambahkan',
            'data' => $student
        ]);
    }

    public function show($id){
        $student = Student::find($id);

        if(!$student){
            return response()->json([
                'status' => 'Error',
                'message' => 'Student tidak ditemukan'
            ], 500);
        }

        return (new StudentResource($student))->additional([
            'status' => 'Success',
            'data' => $student
        ]);
    }

    public function update(StudentRequest $request, $id){
        $student = Student::find($id);

        if(!$student){
            return response()->json([
                'status' => 'Error',
                'message' => 'Student tidak ditemukan'
            ], 500);
        }

        $student->update($request->validated());

        return response()->json([
            'status' => 'Success',
            'message' => 'Student berhasil diupdate',
            'data' => $student
        ], 201);
    }

     public function destroy($id){

        $student = Student::find($id);

        if(!$student){
            return response()->json([
                'status' => 'Error',
                'message' => 'Student tidak ditemukan'
            ], 500);
        }

        $student->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Student berhasil dihapus',
            'data' => $student
        ], 201);
    }


}
