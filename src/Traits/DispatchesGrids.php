<?php

namespace SebastianBerc\GridDispatcher\Traits;

use SebastianBerc\GridDispatcher\Jobs\CreateGridForRequest;
use Illuminate\Http\Request;
use SebastianBerc\Repositories\Repository;

/**
 * Class DispatchesGrids
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Traits
 */
trait DispatchesGrids
{
    /**
     * Dispatch a job to create data for grid.
     *
     * @param Request    $request
     * @param Repository $repository
     *
     * @return mixed
     */
    protected function dispatchGrid(Request $request, Repository $repository)
    {
        return app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new CreateGridForRequest($request, $repository));
    }
}
