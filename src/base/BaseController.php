<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * 列表
     * @param $param
     * @return
     */
    public function index()
    {
        $param = request()->all();
        $result = $this->service->filterList($param);

        return $this->success($result);
    }

    /**
     * 创建
     * @param $param
     * @return
     */
    public function store()
    {
        return $this->success($this->service->store(request()->all()));
    }

    /**
     * 更新
     * @param $id ,$param
     * @return
     */
    public function update($id)
    {
        return $this->success($this->service->update($id, request()->all()));
    }

    /**
     * 删除
     * @param $id ,$param
     * @return
     */
    public function destroy($id)
    {
        $ret = $this->service->destroy($id);

        return $this->success($ret, '删除成功');
    }

    /**
     * 上一个
     *
     * @param $id
     * @return
     */
    public function prev($id)
    {
        $result = $this->service->prev($id);

        return $this->success($result);
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

        return $this->success($result);
    }

    public function batchCreate()
    {
        $data = request()->input('data');
        $ids = $this->service->batchCreate($data);
        return $this->success($ids);
    }

    public function batchDelete()
    {
        $ids = $this->service->batchDelete();
        return $this->success($ids);
    }

    /**
     * 详情
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        return $this->success($this->service->show($id));
    }

    /**
     * 禁用
     *
     * @param $id
     * @return Response
     */
    public function forbid($id)
    {
        $ret = $this->service->forbid($id);

        return $this->success($ret, '禁用成功');
    }

    /**
     * 启用
     *
     * @param $id
     * @return Response
     */
    public function resume($id)
    {
        $ret = $this->service->resume($id);

        return $this->success($ret, '启用成功');
    }

    function success($data = [], $info = '成功')
    {
        if (is_string($data) && $temp = json_decode($data, 1)) {
            $data = $temp;
        }

        empty($data) && $data = (object)[];

        return response([
            'iRet' => 1,
            'info' => $info,
            'data' => $data,
        ]);
    }


}
