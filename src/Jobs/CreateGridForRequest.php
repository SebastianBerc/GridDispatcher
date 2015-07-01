<?php

namespace SebastianBerc\GridDispatcher\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use SebastianBerc\Repositories\Repository;

/**
 * Class CreateGridForRequest
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Jobs
 */
class CreateGridForRequest implements SelfHandling
{
    use DispatchesJobs;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * Create a new job instance.
     *
     * @param Request    $request
     * @param Repository $repository
     */
    public function __construct(Request $request, Repository $repository)
    {
        $this->request    = $request;
        $this->repository = $repository;
    }

    /**
     * Execute the job.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function handle()
    {
        return call_user_func_array([$this->repository, 'fetch'], $this->getConvertedRequest());
    }

    /**
     * Returns converted request as array of repository fetch metod arguments.
     *
     * @return array
     */
    public function getConvertedRequest()
    {
        return $this->dispatch(new ConvertRequest($this->request));
    }
}
