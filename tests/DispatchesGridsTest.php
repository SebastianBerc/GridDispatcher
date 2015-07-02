<?php

namespace SebastianBerc\GridDispatcher\Tests;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use SebastianBerc\GridDispatcher\Jobs\ConvertRequest;
use SebastianBerc\GridDispatcher\Jobs\CreateGridForRequest;
use SebastianBerc\GridDispatcher\Traits\DispatchesGrids;
use Mockery as m;
use SebastianBerc\Repositories\Repository;

/**
 * Class DispatchesGridsTest
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Tests
 */
class DispatchesGridsTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    public function setUp()
    {
        $this->request = new Request([
            'page'     => 2,
            'perPage'  => 11,
            'columns'  => 'name',
            'filterBy' => 'name:asd',
            'orderBy'  => 'name:asc',
        ]);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCreatingGridForRequest()
    {
        $application = m::mock('overload:Illuminate\Container\Container');
        $application->shouldReceive('getInstance')->once()->andReturnSelf();

        $dispatcher = m::mock('overload:Illuminate\Contracts\Bus\Dispatcher');
        $application->shouldReceive('make')->once()->andReturn($dispatcher);

        $repository = m::mock(Repository::class);
        $repository->shouldReceive('fetch')
            ->andReturn(new LengthAwarePaginator([], 0, 11, 1));

        $dispatcher->shouldReceive('dispatch')
            ->withArgs([ConvertRequest::class])
            ->andReturn((new ConvertRequest($this->request))->handle());

        $dispatcher->shouldReceive('dispatch')
            ->withArgs([CreateGridForRequest::class])
            ->andReturn((new CreateGridForRequest($this->request, $repository))->handle());

        $paginator = $this->asPublic(ControllerStub::class, 'dispatchGrid')
            ->invokeArgs(new ControllerStub(), [$this->request, $repository]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals([], $paginator->items());
        $this->assertEquals(1, $paginator->currentPage());
        $this->assertEquals(0, $paginator->total());
        $this->assertEquals(0, $paginator->lastPage());
    }
}

class ControllerStub
{
    use DispatchesGrids;
}
