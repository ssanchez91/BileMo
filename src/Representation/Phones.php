<?php

namespace App\Representation;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

class Phones
{
    /**
     * @Type("array<App\Entity\Phone>")
     */
    public $data;

    public $meta;

    public function __construct(Paginator $data)
    {
        $this->offset = $data->getQuery()->getFirstResult();
        $this->limit = $data->getQuery()->getMaxResults();
        $this->total = $data->count();

        $paginatedCollection = new PaginatedRepresentation(
            new CollectionRepresentation($data->getIterator()),
            'https://127.0.0.1:8000/api/phones?', // route
            ['offset'], // route parameters
            ceil(($this->offset + 1) / $this->limit),       // page number
            $this->limit,      // limit
            ceil($this->total / $this->limit),       // total pages
            'offet',  // page route parameter name, optional, defaults to 'page'
            'limit', // limit route parameter name, optional, defaults to 'limit'
            false,   // generate relative URIs, optional, defaults to `false`
            $this->total       // total collection size, optional, defaults to `null`
        );

        $this->data = $paginatedCollection->getInline()->getResources();

        $this->generateMetaNav($paginatedCollection);        
    }

    public function addMeta($name, $value)
    {
        if (isset($this->meta[$name])) {
            throw new \LogicException(sprintf('This meta already exists. You are trying to override this meta, use the setMeta method instead for the %s meta.', $name));
        }

        $this->setMeta($name, $value);
    }

    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }

    public function generateMetaNav($paginatedCollection)
    {
        $this->addMeta('limit', $paginatedCollection->getLimit());
        $this->addMeta('current_items', count($paginatedCollection->getInline()->getResources()));
        $this->addMeta('total_items', $paginatedCollection->getTotal());
        $this->addMeta('first_page', $paginatedCollection->getRoute().$paginatedCollection->getPageParameterName().'=1');
        $this->addMeta('last_page', $paginatedCollection->getRoute().$paginatedCollection->getPageParameterName().'=' . $paginatedCollection->getPages());
        if (($paginatedCollection->getPage()) > 1) {
            $this->addMeta('previous_page', $paginatedCollection->getRoute().$paginatedCollection->getPageParameterName().'=' . ($paginatedCollection->getPage()-1));
        }        
        $this->addMeta('current_page', $paginatedCollection->getRoute().$paginatedCollection->getPageParameterName().'='.$paginatedCollection->getPage());
        if (($paginatedCollection->getPage()) < $paginatedCollection->getPages()) {
            $this->addMeta('next_page', $paginatedCollection->getRoute().$paginatedCollection->getPageParameterName().'=' . ($paginatedCollection->getPage() + 1));
        }
    }
}
