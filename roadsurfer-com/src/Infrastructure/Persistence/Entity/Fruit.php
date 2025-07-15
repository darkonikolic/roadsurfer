<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fruits')]
#[ORM\HasLifecycleCallbacks]
class Fruit extends AbstractProductEntity
{
    // Specific logic for Fruit entity (if any) goes here
}
