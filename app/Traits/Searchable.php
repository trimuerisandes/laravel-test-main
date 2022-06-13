<?php

namespace App\Traits;

use App\Models\Base\SelfModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait Searchable
{
    /**
     * @param Request $request
     * @param SelfModel|Model $model
     * @param \Closure|null $customSearch
     * @return array
     */
    public static function search(Request $request, Model $model,
                                  \Closure $customSearch = null)
    {
        $data = $model::select($model->selectCols ?? ['*']);
        $params = $request->all();
        if (isset($params['search'])) {
            // keyword
            $keyword = $params['search'];
            if (strstr($keyword, ':')) {
                if (!strstr($keyword, ';')) {
                    $keyword .= ';';
                }
                $parts = explode(';',$keyword);
                foreach ($parts as $val) {
                    if (strstr($val, ':')) {
                        $wordSearch = explode(':', $val);
                        $data->whereRaw("({$wordSearch[0]} LIKE '%{$wordSearch[1]}%')");
                    } else {
                        $data = self::searchAll($val, $model, $data);
                    }
                }
            } else {
                $data = self::searchAll($keyword, $model, $data);
            }
        }

        $search = [];
        foreach ($model->searchable as $column) {
            if (isset($params[$column]) && !in_array($params[$column], ['null', ''])) {
                $search[] = "{$column} LIKE '%{$params[$column]}%'";
            }
        }

        if (count($search)) {
            $search = implode(' OR ', $search);
            $data->whereRaw("({$search})");
        }

        if ($customSearch) {
            $data = $customSearch($data);
        }

        $data = self::simplePaginate($request, $data, $model);

        if ($data->count() > 0) {
            return $data;
        } else {
            return [];
        }
    }

    /**
     * @param $keyword
     * @param SelfModel|Model $model
     * @param $data
     * @return mixed
     */
    static function searchAll($keyword, $model, $data)
    {
        if (!in_array($keyword, ['null', ''])) {
            $search = [];

            if ($model->selectCols == ['*']) {
                $model->selectCols = $model->getFillable();
            }

            foreach ($model->selectCols as $column) {
                $search[] = "{$column} LIKE '%{$keyword}%'";
            }

            if (count($search)) {
                $search = implode(' OR ', $search);
                $data->whereRaw("({$search})");
            }
        }
        return $data;
    }

    /**
     * @param Request $request
     * @param SelfModel $data
     * @return mixed
     */
    static function simplePaginate(Request $request, $data, $model)
    {
        if ($request->input('per_page') &&
            $request->input('per_page') != 'false') {
            $sorted = self::sorting($request, $data, $model);
            $data = $sorted['data']->paginate($request->input('per_page'));

//            /** @var Collection $data */
//            $data = (new Collection($data));
//
//            foreach ($sorted['appendsSort'] as $value) {
//                $func = null;
//                eval('$func=$value[0];');
//                $data = $data->sortBy($func,SORT_REGULAR,$value[1]);
//            }

        } else {
            $data = self::sorting($request, $data, $model)->paginate();
        }
        return $data;
    }

    /**
     * @param Request $request
     * @param SelfModel $data
     * @return mixed
     */
    static function sorting(Request $request, $data, $model)
    {
        $appendsSort = [];
        if ($request->has('sortBy')) {
            $sortBy = json_decode($request->input('sortBy'));
            $sortDesc = json_decode($request->input('sortDesc'));

            foreach ($sortBy as $key => $value) {
                $direction = ($sortDesc[$key] == 'true') ? 'DESC' : 'ASC';
                if (!in_array($value, $model->appends)) {
                    $value = str_replace('.', '->', $value);
                    $data = $data->orderBy($value, $direction);
                } else {
                    $appendsSort[] = ['function($data){return $data->get' . htmlspecialchars(ucwords($value), ENT_QUOTES) . 'Attribute();}', ($sortDesc[$key] == 'true')];
                }
            }
            return compact('data', 'appendsSort');
        }

        return $data;
    }
}
