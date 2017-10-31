<?php

namespace vivi\mainBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = [
               'Edification',
               'Evangelisation',
               'Sermon',
               'Predication',
               'Encouragement',
               'Vie Chretienne',
               'Vie de couvre'
    ];
    
    foreach($names as $name)
    {
        $category = new Category();
        $category->setName($name);
        $manager->persist($category);
    }
    
    $manager->flush();
  }
  
}