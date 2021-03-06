<?php
namespace OC\PlatformBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadSkill implements  FixtureInterface
{
    public function load(ObjectManager $manager) {
       
        // Liste des noms de catégorie à ajouter
        $names = array('PHP', 
                       'Symfony', 
                       'C++', 
                       'Java', 
                       'Photoshop', 
                       'Blender', 
                       'Bloc-note');
        
        foreach ($names as $name) {
            // On crée la catégorie
            $skill = new Skill();
            $skill->setName($name);
            
            // On la persiste
            $manager->persist($skill);
        }
        
        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }
}

