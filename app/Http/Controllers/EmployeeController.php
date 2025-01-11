<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\EmployeeFilterRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function getAll(EmployeeFilterRequest $request)
    {
        try {
            $filter = $request->validated();

            $employees = Employee::select('id', 'division_id', 'image', 'name', 'phone', 'position')->with('division:id,name')
                ->when(isset($filter['name']), function (Builder $query) use ($filter) {
                    return $query->whereLike('name', '%'.$filter['name'].'%', false);
                })
                ->when(isset($filter['division_id']), function (Builder $query) use ($filter) {
                    return $query->where('division_id', $filter['division_id']);
                })->paginate();
            $pagination = $employees->toArray();
            unset($pagination['data']);

            return response()->json([
                'status' => 'success',
                'message' => 'success get employee data',
                'data' => [
                    'employees' => $employees->items(),
                    'pagination' => $pagination,
                ],
            ], Response::HTTP_OK);
        } catch (ValidationException $th) {
            throw $th;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'error get employee data',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            $payload = $request->validated();
            $imageName = $request->file('image')->getFilename().'.'.$request->file('image')->getClientOriginalExtension();
            Employee::create([
                'division_id' => $payload['division'],
                'image' => url('/storage/photos/'.$imageName),
                'name' => $payload['name'],
                'phone' => $payload['phone'],
                'position' => $payload['position'],
            ]);
            $request->file('image')->move(storage_path('app/public/photos'), $imageName);

            return response()->json([
                'status' => 'success',
                'message' => 'success create data employee',
            ], Response::HTTP_OK);
        } catch (ValidationException $th) {
            throw $th;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'error create data employee',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $imageName = $request->file('image')->getFilename().'.'.$request->file('image')->getClientOriginalExtension();
        try {
            DB::beginTransaction();
            $payload = $request->validated();
            $oldImage = $employee->image;
            if ($request->has('image')) {
                $employee->image = url('/storage/photos/'.$imageName);
                $request->file('image')->move(storage_path('app/public/photos'), $imageName);
            }

            foreach ($payload as $key => $data) {
                if ($key == 'division') {
                    $employee->division_id = $data;
                }

                if ($key != 'division' && $key != 'image') {
                    $employee->{$key} = $data;
                }
            }

            $employee->save();
            DB::commit();
            if (file_exists(storage_path('app/public/photos').explode(url('/storage/photos/'), $oldImage)[1])) {
                unlink(storage_path('app/public/photos').explode(url('/storage/photos/'), $oldImage)[1]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success update data employee',
            ], Response::HTTP_OK);
        } catch (ValidationException $th) {
            DB::rollBack();
            unlink(storage_path('app/public/photos/').$imageName);
            throw $th;
        } catch (\Throwable $th) {
            DB::rollBack();
            unlink(storage_path('app/public/photos/').$imageName);

            return response()->json([
                'status' => 'error',
                'message' => 'error update data employee',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Employee $employee)
    {
        try {
            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success delete data employee',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'error delete data employee',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
