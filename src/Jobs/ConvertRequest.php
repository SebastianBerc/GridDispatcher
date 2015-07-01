<?php

namespace SebastianBerc\GridDispatcher\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Http\Request;

/**
 * Class ConvertRequest
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Jobs
 */
class ConvertRequest implements SelfHandling
{
    /**
     * Contains request to convert.
     *
     * @var Request
     */
    private $request;

    /**
     * Create a new job instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        return [
            'page'    => $this->getPage(),
            'perPage' => $this->getPerPage(),
            'columns' => $this->getColumns(),
            'filters' => $this->getFilters(),
            'sorting' => $this->getSorting()
        ];
    }

    /**
     * Returns current page.
     *
     * @return int
     */
    protected function getPage()
    {
        return $this->request->has('page') ? $this->request->get('page') : 1;
    }

    /**
     * Returns count of items per page.
     *
     * @return int
     */
    protected function getPerPage()
    {
        return $this->request->has('perPage') ? $this->request->get('perPage') : 15;
    }

    /**
     * Returns required columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return $this->request->has('columns') ? explode(',', $this->request->get('columns')) : ['*'];
    }

    /**
     * Returns filters.
     *
     * @return array
     */
    protected function getFilters()
    {
        return $this->request->has('filterBy') ? $this->extract($this->request->get('filterBy')) : [];
    }

    /**
     * Returns sorting.
     *
     * @return array
     */
    protected function getSorting()
    {
        return $this->request->has('orderBy') ? $this->extract($this->request->get('orderBy')) : [];
    }

    /**
     * Extracts combined arguments in string to array.
     *
     * For example: 'key1:value,key2:value' => ['key1' => 'value', 'key2' => 'value']
     *
     * @param string $combinedArguments
     *
     * @return array
     */
    protected function extract($combinedArguments)
    {
        $pairs   = explode(',', $combinedArguments);
        $columns = $values = [];

        foreach ($pairs as $combined) {
            list($columns[], $values[]) = explode(':', $combined);
        }

        return array_combine($columns, $values);
    }
}
