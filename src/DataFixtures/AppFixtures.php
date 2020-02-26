<?php

namespace App\DataFixtures;

use App\Entity\ItemType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
    	$types = ['apple', 'orange', 'watermelon'];

        for ($i = 0; $i < 3; $i++) {
            $itemType = new ItemType();
            $itemType->setName($types[$i])->setCreatedAt(new \DateTime());
            $manager->persist($itemType);
        }

        $manager->flush();
    }
}
