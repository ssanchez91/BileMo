<?php

namespace App\Representation;

use App\Entity\Customer;
use App\Service\MetaService;
use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

/**
 * representation Users class
 */
class Users
{
    /**
     * @Type("array<App\Entity\User>")
     */
    public $data;

    public $meta;

    public function __construct(Paginator $data, Customer $customer)
    {
        $this->customer = $customer;
        $this->offset = $data->getQuery()->getFirstResult();
        $this->limit = $data->getQuery()->getMaxResults();
        $this->total = $data->count();

        $paginatedCollection = new PaginatedRepresentation(
            new CollectionRepresentation($data->getIterator()),
            'https://127.0.0.1:8000/api/customer/'.$this->customer->getId().'/users?', // route
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
        $metaService = new MetaService();
        $this->meta = $metaService->generateMetaNav($paginatedCollection);

    }   
}
