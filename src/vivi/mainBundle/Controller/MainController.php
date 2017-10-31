<?php

namespace vivi\mainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use vivi\mainBundle\Entity\Articles;
use vivi\mainBundle\Entity\Image;
use vivi\mainBundle\Entity\Comment;
use vivi\mainBundle\Form\ArticlesType;
use vivi\mainBundle\Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="vivimain_homepage")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $newArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 5, 0);
        $oldArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'asc'], 5, 0);
        $reArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 3, 0);
        
        return $this->render('vivimainBundle:Default:index.html.twig', array(
                         'cats' => $cats,                                        
                         'newArts' => $newArts,                                        
                         'oldArts' => $oldArts ,                                       
                         'reArts' => $reArts                                        
                              ));
    }
    
    /**
     * @Route("/article/{slug}", name="vivimain_article_view")
     * @ParamConverter("article", options={"mapping": {"slug": "slug"}})
     */
    public function view_articleAction(Articles $article)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $newArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 5, 0);
        $oldArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'asc'], 5, 0);
        $reArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 3, 0);
        
        $em = $this->getDoctrine()->getManager();
        $comment = new comment();
        $comments = $em->getRepository('vivimainBundle:Comment')->findBy(['articles'=>$article]);
        $nbCom = count($comments);
        
        return $this->render('vivimainBundle:Default:view_article2.html.twig', array(
                         'article' => $article,
                         'comments'   => $comments,
                         'nbCom'   => $nbCom,
                         'cats' => $cats,                                        
                         'newArts' => $newArts,                                        
                         'oldArts' => $oldArts ,                                       
                         'reArts' => $reArts,
                              ));
    } 
    
    /**
     * @Route("/category/{id}", name="vivimain_cat_articles")
     *
     */
    public function cat_articlesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $newArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 5, 0);
        $oldArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'asc'], 5, 0);
        $reArts = $em->getRepository('vivimainBundle:Articles')->findBy([], ['id'=>'desc'], 3, 0);
        
        $cat = $em->getRepository('vivimainBundle:Category')->find($id);
        //$catArts = $em->getRepository('vivimainBundle:Articles')->findBy(['categories'=>$cat]);
        //dump($cat); die();
        return $this->render('vivimainBundle:Default:view_category.html.twig', array(
                           'cat' => $cat ,
                           'cats' => $cats,                                        
                         'newArts' => $newArts,                                        
                         'oldArts' => $oldArts ,                                       
                         'reArts' => $reArts,
                                                                             ));
    }

    /**
     * @Route("/Comment/{Slug}", name="vivimain_add_com")
     * @Method("POST")
     * @ParamConverter("art", options={"mapping": {"Slug": "slug"}})
     */
    public function addComAction(Request $request, Articles $art)
    {
        
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $comment = $form->getData();
            $comment->setArticles($art);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            
            return $this->redirectToRoute('vivimain_article_view', ['id' => $art->getId()]);
        }
       return $this->render('vivimainBundle:Default:comForm.html.twig', array(
                    'art'=>$art,
                    'form'=>$form->createView(),
        ));
    }

    /**
     * @Route("/slugger", name="vivimain_slugger")
     */
    public function slugAction()
    {
        $em = $this->getDoctrine()->getManager();
        $arts = $em->getRepository(Articles::class)->findAll();
        foreach ($arts as $art) {
            $art->setSlug($this->get('slugger')->slugify($art->getTitle()));
            $em->flush();
        }
       throw $this->createAccessDeniedException('Vas voir dans la base de donnee.');
       dump($arts);die();
    }
}