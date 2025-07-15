<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'vegetables')]
#[ORM\HasLifecycleCallbacks]
class Vegetable extends AbstractProductEntity
{
    // Specific logic for Vegetable entity (if any) goes here
}
