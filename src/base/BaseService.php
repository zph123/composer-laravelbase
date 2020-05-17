<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class BaseService
{
    /**
     * 数据创建
     *
     * @param $param
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function store($param)
    {
        method_exists($this, 'createValidation') && $this->createValidation($param);

        return $this->model->newQuery()->create($param);
    }

    /**
     * 详情
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function show($id)
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * 更新
     *
     * @param $id
     * @param $param
     * @return bool
     */
    public function update($id, $param)
    {
        method_exists($this, 'updateValidation') && $this->updateValidation($id, $param);

        $data = $this->model->newQuery()->findOrFail($id);

        return $data->fill($param)->save();
    }

    /**
     * 删除
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->model->newQuery()->where('id', $id)->delete();
    }

    /**
     * 禁用
     *
     * @param $id
     * @return int
     */
    public function forbid($id)
    {
        $ret = $this->model->newQuery()->where('id', $id)->update(['status' => 0]);

        return $ret;
    }

    /**
     * 启用
     *
     * @param $id
     * @return int
     */
    public function resume($id)
    {
        $ret = $this->model->newQuery()->where('id', $id)->update(['status' => 1]);

        return $ret;
    }

    /**
     * 基类查询信息
     *
     * @return array
     */
    public function filterList($param)
    {
        $page             = data_get($param, 'page', 1);
        $per_page         = data_get($param, 'per_page', 20);
        $order_field      = data_get($param, 'order_field', "id");
        $order_type       = data_get($param, 'order_type', 'desc');
        $select           = data_get($param, 'select', ['*']);
        $condition        = data_get($param, 'condition', []);
        $has_or_condition = data_get($param, 'has_or_condition', []);
        $with             = data_get($param, 'with');
        $with_count       = data_get($param, 'with_count');
        $has_condition    = data_get($param, 'has_condition');
        $where_in         = data_get($param, 'where_in', []);
        $where_not_in     = data_get($param, 'where_not_in', []);
        $diy_order        = data_get($param, 'diy_order');
        $with             = $with ? json_decode($with, 1) : [];
        $with_count       = $with_count ? json_decode($with_count, 1) : [];
        $has_condition    = $has_condition ? json_decode($has_condition, 1) : [];
        $has_or_condition = $has_or_condition ? json_decode($has_or_condition, 1) : [];

        $query = $this->model->newQuery();

        $columns = Schema::getColumnListing($this->model->getTable());
        foreach ($condition as $key => $item) {
            if (is_numeric($key)) {
                // 过滤字段在数组中,
                if (!in_array(Arr::get($item, 0), $columns) || Arr::get($item, 0) === '') {
                    unset($condition[$key]);
                }
            } else {
                // 过滤字段在键名中
                if (!in_array($key, $columns) || $condition[$key] === '') {
                    unset($condition[$key]);
                }
            }
        }
        $data = $condition;
        unset($condition['name']);
        unset($condition['title']);
        //         Log::info('打印参数', $condition);
        $query->where($condition);
        $query->select($select);

        if (isset($data['name']) && $data['name']) {
            $query->where(
                function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['name'] . '%');
                }
            );
        }
        if (isset($data['title']) && $data['title']) {
            $query->where(
                function ($query) use ($data) {
                    $query->where('title', 'like', '%' . $data['title'] . '%');
                }
            );
        }

        foreach ($with as $info) {
            $query->with(
                [
                    $info['name'] => function ($query) use ($info) {
                        (isset($info['condition']) && $info['condition']) && $query->where($info['condition']);
                        (isset($info['order_field']) && $info['order_field']) && $query->orderBy(
                            $info['order_field'],
                            $info['order_type'] ?? 'asc'
                        );
                        (isset($info['select']) && $info['select']) && $query->select($info['select']);
                    },
                ]
            );
        }
        // 多查询条件模糊查询
        if ($has_or_condition && is_array($has_or_condition)) {
            foreach ($has_or_condition as $or_condition) {
                if ($or_condition) {
                    $query->where(
                        function ($query) use ($or_condition) {
                            if (is_array($or_condition)) {
                                foreach ($or_condition as $or_c) {
                                    if (isset($or_c['name']) && isset($or_c['operator']) && isset($or_c['value'])) {
                                        $query->orWhere($or_c['name'], $or_c['operator'], $or_c['value']);
                                    }
                                }
                            }
                        }
                    );
                }
            }
        }

        foreach ($has_condition as $info) {
            $query->whereHas(
                $info['name'],
                function ($query) use ($info) {
                    (isset($info['condition']) && $info['condition']) && $query->where($info['condition']);
                    if (isset($info['where_in']) && $info['where_in']) {
                        foreach ($info['where_in'] as $info) {
                            if (isset($info['list']) && $info['list']) {
                                $query->whereIn($info['field'], $info['list']);
                            }
                        }
                    }
                }
            );
        }
        foreach ($with_count as $info) {
            $query->withCount(
                [
                    $info['name'] => function ($query) use ($info) {
                        (isset($info['condition']) && $info['condition']) && $query->where($info['condition']);
                    }
                ]
            );
        }

        foreach ($where_in as $info) {
            if (isset($info['list']) && $info['list']) {
                $query->whereIn($info['field'], $info['list']);
            }
        }
        foreach ($where_not_in as $info) {
            if (isset($info['list']) && $info['list']) {
                $query->whereNotIn($info['field'], $info['list']);
            }
        }

        // 总数
        $total = $query->count('id');
        if ($diy_order) {
            $query = $query->forPage($page, $per_page);
            foreach ($diy_order as $item) {
                $query = $query->orderBy($item['order_field'], $item['order_type']);
            }
            $list = $query->get();
        } else {
            $list = $query->forPage($page, $per_page)->orderBy($order_field, $order_type)->get();
        }

        return compact('total', 'list');
    }

    public function prev($id)
    {
        $page     = request()->get('page', 1);
        $per_page = request()->get('per_page', 1);
        // $order_field      = request()->get('order_field', 'id');
        // $order_type       = request()->get('order_type', 'asc');
        $condition     = request()->get('condition', request()->all());
        $has_condition = request()->get('has_condition');
        $has_condition = $has_condition ? json_decode($has_condition, 1) : [];

        $query = $this->model->newQuery();

        $columns = Schema::getColumnListing($this->model->getTable());
        foreach ($condition as $key => $item) {
            if (is_numeric($key)) {
                // 过滤字段在数组中,
                if (!in_array(Arr::get($item, 0), $columns) || Arr::get($item, 0) === '') {
                    unset($condition[$key]);
                }
            } else {
                // 过滤字段在键名中
                if (!in_array($key, $columns) || $condition[$key] === '') {
                    unset($condition[$key]);
                }
            }
        }
        $data = $condition;
        unset($condition['name']);
        unset($condition['title']);
        //         Log::info('打印参数', $condition);
        $query->where($condition);

        foreach ($has_condition as $info) {
            $query->whereHas(
                $info['name'],
                function ($query) use ($info) {
                    (isset($info['condition']) && $info['condition']) && $query->where($info['condition']);
                    if (isset($info['where_in']) && $info['where_in']) {
                        foreach ($info['where_in'] as $info) {
                            if (isset($info['list']) && $info['list']) {
                                $query->whereIn($info['field'], $info['list']);
                            }
                        }
                    }
                }
            );
        }

        // $query->forPage($page, $per_page);

        // if($order_type == 'asc'){
        //     $query->where('id','<',$id);
        // }else{
        $query->where('id', '>', $id);
        // }
        return $query->forPage($page, $per_page)->first();
    }

    public function next($id)
    {
        $page     = request()->get('page', 1);
        $per_page = request()->get('per_page', 1);
        // $order_field      = request()->get('order_field', 'id');
        // $order_type       = request()->get('order_type', 'asc');
        $condition     = request()->get('condition', request()->all());
        $has_condition = request()->get('has_condition');
        $has_condition = $has_condition ? json_decode($has_condition, 1) : [];

        $query = $this->model->newQuery();

        $columns = Schema::getColumnListing($this->model->getTable());
        foreach ($condition as $key => $item) {
            if (is_numeric($key)) {
                // 过滤字段在数组中,
                if (!in_array(Arr::get($item, 0), $columns) || Arr::get($item, 0) === '') {
                    unset($condition[$key]);
                }
            } else {
                // 过滤字段在键名中
                if (!in_array($key, $columns) || $condition[$key] === '') {
                    unset($condition[$key]);
                }
            }
        }

        unset($condition['name']);
        unset($condition['title']);
        //         Log::info('打印参数', $condition);
        $query->where($condition);

        foreach ($has_condition as $info) {
            $query->whereHas(
                $info['name'],
                function ($query) use ($info) {
                    (isset($info['condition']) && $info['condition']) && $query->where($info['condition']);
                    if (isset($info['where_in']) && $info['where_in']) {
                        foreach ($info['where_in'] as $info) {
                            if (isset($info['list']) && $info['list']) {
                                $query->whereIn($info['field'], $info['list']);
                            }
                        }
                    }
                }
            );
        }

        // $query->orderBy($order_field, $order_type);

        // if($order_type == 'asc'){
        //     $query->where('id','>',$id);
        // }else{
        $query->where('id', '<', $id);
        // }
        $query->orderBy('id','desc');
        return $query->forPage($page, $per_page)->first();
    }


    public function batchCreate($data_arr)
    {
        $ids = [];
        foreach ($data_arr as $data) {
            $ids[] = $this->model->newQuery()->insertGetId($data);
        }
        return $ids;
    }


    public function batchDelete()
    {
        $query = $this->model->newQuery();

        $condition    = request()->get('condition', request()->all());
        $where_in     = request()->get('where_in', []);
        $where_not_in = request()->get('where_not_in', []);

        $columns = Schema::getColumnListing($this->model->getTable());

        foreach ($condition as $key => $item) {
            if (is_numeric($key)) {
                // 过滤字段在数组中,
                if (!in_array(Arr::get($item, 0), $columns) || Arr::get($item, 0) === '') {
                    unset($condition[$key]);
                }
            } else {
                // 过滤字段在键名中
                if (!in_array($key, $columns) || $condition[$key] === '') {
                    unset($condition[$key]);
                }
            }
        }
        $query->where($condition);

        foreach ($where_in as $info) {
            if (isset($info['list']) && $info['list']) {
                $query->whereIn($info['field'], $info['list']);
            }
        }
        foreach ($where_not_in as $info) {
            if (isset($info['list']) && $info['list']) {
                $query->whereNotIn($info['field'], $info['list']);
            }
        }

        return $query->delete();
    }

    /**
     * 基类关联查询详情
     *
     * @param       $id
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function withShow($id)
    {
        $select        = request()->get('select', ['*']);
        $with          = request()->get('with');
        $has_condition = request()->get('has_condition');
        $with          = $with ? json_decode($with, 1) : [];
        $has_condition = $has_condition ? json_decode($has_condition, 1) : [];

        $query = $this->model->newQuery();
        foreach ($with as $info) {
            $query->with(
                [
                    $info['name'] => function ($query) use ($info) {
                        $info['condition'] && $query->where($info['condition']);
                        $info['order_field'] && $query->orderBy($info['order_field'], $info['order_type'] ?? 'asc');
                        $info['select'] && $query->select($info['select']);
                    },
                ]
            );
        }

        foreach ($has_condition as $info) {
            $query->whereHas(
                $info['name'],
                function ($query) use ($info) {
                    $info['condition'] && $query->where($info['condition']);
                }
            );
        }

        return $query->where('id', $id)->select($select)->first();
    }
}
