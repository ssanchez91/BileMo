<?php

namespace App\Service;

class MetaService{

    protected $meta;
    
    public function __construct()
    {
        $this->meta = [];
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

        return $this->meta;
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

    
}