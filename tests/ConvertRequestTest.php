<?php

namespace SebastianBerc\GridDispatcher\Tests;

use Illuminate\Http\Request;
use SebastianBerc\GridDispatcher\Jobs\ConvertRequest;

class ConvertRequestTest extends TestCase
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

    public function testRequest()
    {
        $this->assertEquals(2, $this->request->get('page'));
        $this->assertEquals(11, $this->request->get('perPage'));
        $this->assertEquals('name', $this->request->get('columns'));
        $this->assertEquals('name:asd', $this->request->get('filterBy'));
        $this->assertEquals('name:asc', $this->request->get('orderBy'));
    }

    public function testRequestConverting()
    {
        $converter = new ConvertRequest($this->request);

        $page = $this->asPublic(ConvertRequest::class, 'getPage')->invokeArgs($converter, []);
        $this->assertEquals($this->request->get('page'), $page);

        $perPage = $this->asPublic(ConvertRequest::class, 'getPerPage')->invokeArgs($converter, []);
        $this->assertEquals($this->request->get('perPage'), $perPage);

        $columns = $this->asPublic(ConvertRequest::class, 'getColumns')->invokeArgs($converter, []);
        $this->assertEquals(['name'], $columns);

        $filters = $this->asPublic(ConvertRequest::class, 'getFilters')->invokeArgs($converter, []);
        $this->assertEquals(['name' => 'asd'], $filters);

        $filters = $this->asPublic(ConvertRequest::class, 'getSorting')->invokeArgs($converter, []);
        $this->assertEquals(['name' => 'asc'], $filters);

        $this->assertEquals([
            'page'    => 2,
            'perPage' => 11,
            'columns' => ['name'],
            'filters' => ['name' => 'asd'],
            'sorting' => ['name' => 'asc'],
        ], $converter->handle());
    }
}
