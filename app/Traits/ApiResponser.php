<?php
/**
 * Codigo necesario para construir respuestas de Api
 */

namespace App\Traits;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

trait ApiResponser
{
    private function successResponse($data,$code)
    {
        return response()->json($data,$code);
    }

    protected function errorResponse($message,$code)
    {
        return response()->json(['error' => $message,'code' => $code],$code);
    }

    public function errorsResponse($errors, $code)
    {
        return response()->json(['errors' => $errors,'code' => $code],$code);
    }

    protected function showAll(Collection $collection, $code=200)
    {
        if($collection->isEmpty()){
            return response()->json(['data' => $collection],$code);            
        }

        #$transformer = $collection->first()->transformer;

        #$collection = $this->filterData($collection,$transformer);
        #$collection = $this->sortData($collection,$transformer);
        $collection = $this->paginate($collection);
        #$collection = $this->transformData($collection, $transformer);
        $collection = $this->cacheResponse($collection);

        return response()->json($collection,$code);
    }

    protected function showOne(Model $instance, $message='', $code=200)
    {
        #$transformer = $instance->transformer;

        #$instance = $this->transformData($instance, $transformer);

        return response()->json(['data' => $instance, 'message' => $message, 'code' => $code],$code);
    }

    protected function showString(string $instance, $message='', $code=200)
    {
        #$transformer = $instance->transformer;

        #$instance = $this->transformData($instance, $transformer);

        return response()->json(['data' => $instance, 'message' => $message, 'code' => $code],$code);
    }    

    protected function showMessage($message, $code=200)
    {
        return response()->json(['data' => $message],$code);
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if(request()->has('sort_by')){
            $atribute = $transformer::originalAttribute(request()->sort_by);
            //$collection = $collection->sortBy($atribute);
            $collection = $collection->sortBy->{$atribute};
        }

        return $collection;

    }

    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:30'
        ];

        Validator::validate(request()->all(), $rules);

        //pagina actual
        $page = LengthAwarePaginator::resolveCurrentPage();

        //cantidad de elementos por pagina
        $perPage = 10;

        if(request()->has('per_page')){
            $perPage = (int) request()->per_page;
        }

        //dividir coleccion completa en secciones(primerElemento, Cantidad)
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        //coleccion paginada
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        //agregar a resultados paginados la lista de los parametros
        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach(request()->query() as $query => $valor){
            $atribute = $transformer::originalAttribute($query);

            if(isset($atribute,$valor)){
                $collection = $collection->where($atribute,$valor);
            }
        }

        return $collection;

    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query(); 
        ksort($queryParams);

        $queryStrings = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryStrings}";

        return Cache::remember($fullUrl, 15/60, function() use($data){
            return $data;
        });
    }
}