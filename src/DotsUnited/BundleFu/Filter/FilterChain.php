<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *  DotsUnited_BundleFu_Filter_FilterChain
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @version @package_version@
 */
class DotsUnited_BundleFu_Filter_FilterChain implements DotsUnited_BundleFu_Filter_FilterInterface
{
    const CHAIN_APPEND  = 'append';
    const CHAIN_PREPEND = 'prepend';

    /**
     * Filter chain
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Adds a filter to the chain
     *
     * @param FilterInterface $filter
     * @param string $placement
     * @return FilterChain
     */
    public function addFilter(DotsUnited_BundleFu_Filter_FilterInterface $filter, $placement = self::CHAIN_APPEND)
    {
        if ($placement == self::CHAIN_PREPEND) {
            array_unshift($this->filters, $filter);
        } else {
            $this->filters[] = $filter;
        }
        return $this;
    }

    /**
     * Add a filter to the end of the chain
     *
     * @param FilterInterface $filter
     * @return FilterChain
     */
    public function appendFilter(DotsUnited_BundleFu_Filter_FilterInterface $filter)
    {
        return $this->addFilter($filter, self::CHAIN_APPEND);
    }

    /**
     * Add a filter to the start of the chain
     *
     * @param FilterInterface $filter
     * @return FilterChain
     */
    public function prependFilter(DotsUnited_BundleFu_Filter_FilterInterface $filter)
    {
        return $this->addFilter($filter, self::CHAIN_PREPEND);
    }

    /**
     * Get all the filters
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Reset all the filters
     *
     * @return FilterChain
     */
    public function resetFilters()
    {
        $this->filters = array();
        return $this;
    }

    /**
     * Returns $content filtered through each filter in the chain
     *
     * Filters are run in the order in which they were added to the chain (FIFO)
     *
     * @param mixed $content
     * @return mixed
     */
    public function filter($content)
    {
        $contentFiltered = $content;
        foreach ($this->filters as $filter) {
            $contentFiltered = $filter->filter($contentFiltered);
        }
        return $contentFiltered;
    }
}
