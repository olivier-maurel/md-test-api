<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;


class ApplicationService
{
    public function __construct(
            ContainerInterface $container
        )
    {
        $this->container    = $container;  
    }

   
}