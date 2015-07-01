<?php

namespace SebastianBerc\GridDispatcher\Tests;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery as m;
use SebastianBerc\GridDispatcher\Jobs\ConvertRequest;
use SebastianBerc\GridDispatcher\Jobs\CreateGridForRequest;
use SebastianBerc\Repositories\Repository;

/**
 * Class CreateGridForRequestTest
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Tests
 */
class CreateGridForRequestTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $items = [
        ['name' => 'Ava'],
        ['name' => 'Emma'],
        ['name' => 'Olivia'],
        ['name' => 'Sophia']
    ];

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

        $dispatcher->shouldReceive('dispatch')->once()->andReturn((new ConvertRequest($this->request))->handle());

        $repository = m::mock(Repository::class);
        $repository->shouldReceive('fetch')
            ->andReturn(new LengthAwarePaginator($this->items, sizeof($this->items), 11, 1));

        $paginator = (new CreateGridForRequest($this->request, $repository))->handle();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals($this->items, $paginator->items());
        $this->assertEquals(1, $paginator->currentPage());
        $this->assertEquals(sizeof($this->items), $paginator->total());
        $this->assertEquals(1, $paginator->lastPage());
    }
}
