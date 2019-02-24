<?php
namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use Symfony\Component\Config\Definition\Exception\Exception;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{

    public function indexAction($page, Request $request)
    {
        if ($page < 1) {

            throw new NotFoundHttpException("la page demandée n'existe pas".$page);
        }

        // Notre liste d'annonce en dur
      
        $em = $this->getDoctrine()
                   ->getManager();
        
        $repoAdvert = $em->getRepository(Advert::class);
        
       // $listAdverts = $repoAdvert->findAll();
        $listAdverts = $repoAdvert->myFindAll();

        // return new Response(json_encode(array('nom'=>'hanafi')));
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function viewAction($id, Request $request)
    {
        
        $repo = $this->getDoctrine()
                     ->getManager()
                     ->getRepository(Advert::class);
        
       // $advert = $repo->find($id);
          $advert = $repo->myFindOne($id);
        
        
        if($advert === null){
            
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        
        $em = $this->getDoctrine()
                   ->getManager()
        ;
        
         
        
        $listeApplication = $em->getRepository(Application::class)
                               ->findBy(array('advert'=>$advert));

        $listAdvertSkille = $em->getRepository(AdvertSkill::class)
                               ->findBy(array('advert'=>$advert));
        // $this->generateUrl("");
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listeApplication' => $listeApplication,
            'listAdvertSkille' =>  $listAdvertSkille,
            // ...
        ));
    }

    public function addAction(Request $request)
    {
        
        $em = $this->getDoctrine()
                    ->getManager();
        
        $advert  =  new Advert();
        $advert->setTitle("Recherche développpeur Symfony3")
                ->setAuthor('Abdennassir')
                ->setContent('Nous recherchons un développeur Symfony2 débutant sur Lyo')
                ;
        
         $image = new Image();
         $image->setAlt("Mon logo")
               ->setUrl("https://fr.freelogodesign.org/Content/img/logo-ex-7.png")
               ;
         
         $application =  new Application();
         $application->setAdvert($advert)
                     ->setAuthor('Abdennassir')
                     ->setDate(new \DateTime())
                     ->setContent('Application pour un poste de symfony');
         
         
         $advert->setImage($image);
         
         $listCategory = $em->getRepository(Category::class)
                            ->findAll();
         
         $listSkill = $em->getRepository(Skill::class)
                            ->findAll();
         
         foreach($listCategory as $category)
         {
             if(rand()%2){
                 
                 $advert->addCategory($category);
             }
         }
         
         $levels = ['junior','senior'];
         foreach($listSkill as $skill)
         {
             if(rand()%2){
                 $advertSkill  = new AdvertSkill();
                 $advertSkill->setAdvert($advert)
                             ->setSkill($skill)
                             ->setLevel($levels[rand()%2])
                 ;
                 $em->persist($advertSkill);
             }
         }
        
        
         
         $em->persist($advert);
         $em->persist($application);
         
         $em->flush();
        
        if ($request->isMethod('POST')) {

            $session = $request->getSession()->getFlashBag();

            $session->add('notice', 'Advert ajout� avec success');

            return $this->redirectToRoute('oc_plateform_view', [
                'id' => $id
            ]);
        }

        $antiSpam = $this->get('oc_platform.antispam');

        if ($antiSpam->isSpam('text')) {

           // throw new \Exception('Votre message est un spam');
        }

        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            // ...
        ));
    }

    public function editAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()
                   ->getManager();
        
         $advert  =  $em->getRepository('OCPlatformBundle:Advert')
                        ->find($id);
         
         $advert->setPublished(false);
         $em->flush();
         
        if ($request->isMethod('POST')) {

            $session = $request->getSession()->getFlashBag();

            $session->add('notice', 'Advert modifiée avec success');

            return $this->redirectToRoute('oc_plateform_home');
        }

        $advert = array(
            'title' => 'Recherche développpeur Symfony',
            'id' => $id,
            'author' => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date' => new \Datetime()
        );

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert
        ));
    }

    public function deleteAction(Advert $advert, Request $request)
    {
      

            $em = $this->getDoctrine()
                       ->getManager();
            
            
            
            foreach ($advert->getCategories() as $category){
                
                $advert->removeCategory($category);
            }
            
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('notice', 'Advert supprimer');
            
                

            return $this->redirectToRoute('oc_platform_home');
       
    }

    public function menuAction($limit)
    {

        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
       

        $em = $this->getDoctrine()
        ->getManager();
        
        $repoAdvert = $em->getRepository(Advert::class);
        
        $listAdverts = $repoAdvert->findBy(array(),array('date'=>'desc'),3,0);
        
        
        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }
}
