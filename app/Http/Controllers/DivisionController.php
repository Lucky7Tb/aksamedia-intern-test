<?php

namespace App\Http\Controllers;

use App\Http\Requests\Division\DivisionFilterRequest;
use App\Models\Division;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DivisionController extends Controller
{
    public function __invoke(DivisionFilterRequest $request): JsonResponse
    {
        try {
            $filter = $request->validated();

            $divisions = Division::select('id', 'name')->when($filter, function (Builder $query) use ($filter) {
                return $query->whereLike('name', '%'.$filter['name'].'%', false);
            })->paginate();
            $pagination = $divisions->toArray();
            unset($pagination['data']);

            return response()->json([
                'status' => 'success',
                'message' => 'success get divisions data',
                'data' => [
                    'divisions' => $divisions->items(),
                    'pagination' => $pagination,
                ],
            ], Response::HTTP_OK);
        } catch (ValidationException $th) {
            throw $th;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'error get divisions data',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
