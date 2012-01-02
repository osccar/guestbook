<?php
class Pager
{
    /**
     * Pagination class to page through datasets
     *
     * @calculate paging information
     * @access public static
     * @param int $num_pages
     * @param int $limit
     * @param int $page
     * @return object
     *
     */
    public static function getPagerData($num_pages, $limit, $page)
    {
        // the number of pages
        $num_pages = ceil($num_pages / $limit);
        $page = max($page, 1);
        $page = min($page, $num_pages);
        //calculate the offset
        $offset = ($page - 1) * $limit;

        // new instance of stdClass
        $inst = new stdClass;

        // assign the variables to the return class object
        $inst->offset = $offset;
        $inst->limit = $limit;
        $inst->num_pages = $num_pages;
        $inst->page = $page;

        return $inst;
    }
}