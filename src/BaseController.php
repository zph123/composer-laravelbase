<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BaseService;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * @var BaseService $service
     */
    protected $service;

    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * 列表
     *
     * @return Response
     */
    public function index()
    {
        $param['condition'] [] = ['name1', '=', 123];
        $result = $this->service->filterList($param);

        return ($result);
    }

    /**
     * 上一个
     *
     * @param $id
     * @return Response
     */
    public function prev($id)
    {
        $result = $this->service->prev($id);

        return success($result);
    }

    /**
     * 下一个
     *
     * @param $id
     * @return Response
     */
    public function next($id)
    {
        $result = $this->service->next($id);

        return success($result);
    }

    public function batchCreate()
    {
        $data = request()->input('data');
        $ids = $this->service->batchCreate($data);
        return success($ids);
    }

    public function batchDelete()
    {
        $ids = $this->service->batchDelete();
        return success($ids);
    }
    /**
     * 详情
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        return success($this->service->show($id));
    }


    /**
     * 数据创建
     *
     * @param $param
     * @return Response
     */
    public function store()
    {

        return success($this->service->store(request()->all()));

    }

    /**
     * 更新
     *
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        return success($this->service->update($id, request()->all()));
    }


    /**
     * 禁用
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function forbid($id)
    {
        $ret = $this->service->forbid($id);

        return success($ret, '禁用成功');
    }

    /**
     * 启用
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function resume($id)
    {
        $ret = $this->service->resume($id);

        return success($ret, '启用成功');
    }

    /**
     * 删除
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        $ret = $this->service->destroy($id);

        return success($ret, '删除成功');
    }


}
