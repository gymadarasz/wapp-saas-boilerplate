<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   Madsoft\Library
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */

namespace Madsoft\Library;

/**
 * Index
 *
 * @category  PHP
 * @package   Madsoft\Library
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */
class Index
{
    
    /**
     * Method index
     *
     * @return string
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function index(): string
    {
        return 'Public Index Page';
    }
    
    /**
     * Method restricted
     *
     * @return string
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function restricted(): string
    {
        return 'Restricted Index Page';
    }
}
