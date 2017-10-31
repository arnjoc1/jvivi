<?php

namespace vivi\mainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use vivi\mainBundle\Entity\Articles;
use vivi\mainBundle\Entity\Image;
use vivi\mainBundle\Entity\Comment;
use vivi\mainBundle\Entity\Category;
use vivi\mainBundle\Form\ArticlesType;
use vivi\mainBundle\Form\CommentType;
use vivi\mainBundle\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/adminooo", name="vivimain_adminooo")
     *
     */
    public function adminHomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $art = $em->getRepository('vivimainBundle:Articles')->findAll();
        $artNum = count($art);
        
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $catNum = count($cats);
        $userNum = count($this->get('fos_user.user_manager')->findUsers());
        
        //dump($artNum); die();
        return $this->render('vivimainBundle:Admin:adminPanel.html.twig', array(
                             'artNum' => $artNum,                                  
                             'catNum' => $catNum,                                  
                             'userNum' => $userNum,                                  
                              ));
    }
    
    public function adminArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $art = $em->getRepository('vivimainBundle:Articles')->findAll();
        
        return $this->render('vivimainBundle:Admin:AdminArticles.html.twig', array(
                         'articles' => $art,                                        
                         'showDiv' => "hidden"                                       
                              ));
    }
    
    /**
     * @Route("/article/{id}", name="vivimain_admin_articles_view")
     *
     */
    public function adminArticlesViewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $art = $em->getRepository('vivimainBundle:Articles')->findAll();
        $article = $em->getRepository('vivimainBundle:Articles')->find($id);
        
        return $this->render('vivimainBundle:Admin:AdminArticlesView.html.twig', array(
                         'articles' => $art,                                        
                         'article' => $article,                                        
                         'showDiv' => ""                                       
                              ));
    }
    
    public function adminAddArticlesAction($id=null, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $art = $em->getRepository('vivimainBundle:Articles')->findAll();
        $categories = $em->getRepository('vivimainBundle:Category')->findAll();
        $articles = new Articles();
        
        $form = $this->createForm(articlesType::class, $articles);
   
        if($request->isMethod('POST'))
        {
            
            $cat = $em->getRepository('vivimainBundle:Category')->find($_POST['categorie']);
            $form->handleRequest($request);
            $articles->addCategory($cat);
            $image = new Image();
            
            if($articles->getImage() !== null)
            {
            $file = $articles->getImage();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('img_upload_directory'), $filename);
            $image->setUrl($filename);
            
            $image->setAlt($file->getClientOriginalName());
            $articles->setImage($image);
            }
            if($form->isValid())
            {
                
               $em->persist($articles);
               $em->flush();
               $this->addFlash("info", "<p class='bg-success'>Article bien ajouté</p>");
               return $this->redirectToRoute('vivimain_admin_articles');
            }
        }
        return $this->render('vivimainBundle:Admin:adminArticles.html.twig', array(
                           'form' => $form->createView(),                                                
                           'img' => $img,                                               
                           'articles' => $art,                                               
                           'categories' => $categories,                                               
                           'cat' => $cat,                                               
                           'showDiv' => ''                                               
                                                                             ));
    }

    public function adminArticlesEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $articles = $em->getRepository('vivimainBundle:Articles')->find($id);
        $imgid = (int) $articles->getImage()->getId();
        $img = $em->getRepository('vivimainBundle:Image')->find($imgid);
        $articles->setImage(null);
        $form = $this->createForm(articlesType::class, $articles);
        $cat = $articles;
        
        return $this->render('vivimainBundle:Admin:AdminArticlesView.html.twig', array(
                         'articles' => $art,                                        
                         'article' => $article,                                        
                         'showDiv' => ""                                       
                              ));
    }
    
    public function adminArticlesDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('vivimainBundle:Articles')->find($id);
        $coms = $em->getRepository('vivimainBundle:Comment')->findBy(['articles'=>$id]);
        $comCount = count($coms);
        if(0 !== $comCount)
        {
            foreach( $coms as $com )
            {
              $em->remove($com);
            }
        }
       
        $em->remove($articles);
        $dir = $this->getParameter('img_upload_directory');
        $filenname = $articles->getImage()->getUrl();
        $compfile = $dir.'/'.$filenname;
        unlink($compfile);
        
        $em->flush();
        
        
        $this->addFlash("info", "<p class='bg-success'>Article bien supprimé</p>");
        return $this->redirectToRoute('vivimain_admin_articles');
    }
    
    public function adminArtComAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('vivimainBundle:Articles')->findAll();
        $coms = $em->getRepository('vivimainBundle:Comment')->findBy(array('articles'=>$id));
        
        
        return $this->render('vivimainBundle:Admin:adminArticlesCom.html.twig', array(                                
                           'articles' => $articles,                                               
                           'coms' => $coms,                                               
                           'artCom' => $id,                                               
                           'showDiv' => ''                                               
        ));        
    }
    
    public function adminArtComDelAction($artCom, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $com = $em->getRepository('vivimainBundle:Comment')->find($id);
        $em->remove($com);
        $em->flush();
        
        $this->addFlash("info", "<p class='bg-success'>Comment deleted successfully</p>");
        return $this->redirectToRoute('vivimain_admin_art_com', ['id'=>$artCom]);
    }
    
    public function adminCatsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(                                
                           'cats' => $cats,                                               
                           'showDiv' => 'hidden'                                               
        ));
    }
    
    public function adminCatAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($request->isMethod('POST') && $form->isValid())
        {
           $em->persist($category);
           $em->flush();
           
           $this->addFlash("info", "<p class='bg-success'> Category added successfully</p>");
           return $this->redirectToRoute('vivimain_admin_cats');
        }
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(                                
                           'cats'    => $cats,                                               
                           'showDiv' => '',
                           'form'    => $form->createView()
        ));
    }
    
    public function adminCatEditAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $category = $em->getRepository('vivimainBundle:Category')->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($request->isMethod('POST') && $form->isValid())
        {
            $em->flush();
            $this->addFlash("info", "<p class='bg-success'> Category removed successfully</p>");
            return $this->redirectToRoute('vivimain_admin_cats');
        }
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(                                
                           'cats'    => $cats,                                               
                           'showDiv' => '',
                           'form'    => $form->createView()
        ));
        
    }
    
    public function adminCatDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->find($id);
        $em->remove($cats);
        $em->flush();
        
        $this->addFlash("info", "<p class='bg-success'> Category removed successfully</p>");
        return $this->redirectToRoute('vivimain_admin_cats');
    }
    
    public function adminUsersAction()
    {
        $users = $this->get('fos_user.user_manager')->findUsers();
             
        return $this->render('vivimainBundle:Admin:adminUsers.html.twig', array(                                
                           'showDiv' => 'hidden',
                           'users'    => $users
        ));
    }
    
    public function adminUsersRegisterAction()
    {
        // Control inserted infos
        $error = [];
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        if(isset($_POST['fos_user_registration_form']))
        {
          $data = $_POST['fos_user_registration_form'];  
        
        if(null !== $userManager->findUserByEmail($data['email']))
        {
            $error[] = "<p>The Email is already used</p>";
        }
        if(null !== $userManager->findUserByUsername($data['username']))
        {
            $error[] = "<p>The username is already used</p>";
        }
        if($data['plainPassword']['first'] !== $data['plainPassword']['second'])
        {
            $error[] = "<p>The entered passwords don't match</p>";
        }
        
        
          $user = $userManager->createUser();
          $user->setEmail($data['email']);
          $user->setUsername($data['username']);
          $encoder = $this->get('security.password_encoder');
          $encoded = $encoder->encodePassword($user, $data['plainPassword']['first']);
          $user->setPassword($encoded);
          $user->setRoles($data['role']);
          $user->setEnabled(1);
          
          if(0 === count($error))
          {
            
            $userManager->updateUser($user);
            $this->addFlash("info", "<p class='bg-success'> User added successfully</p>");
            return $this->redirectToRoute('vivimain_admin_user');
          }
          
            return $this->render('vivimainBundle:Admin:adminUsers.html.twig', array(                                
                           'showDiv' => '',
                           'users'    => $users,
                           'errors'    => $error,
                           'data'    => $data
          ));
          
          }else{
          
          
          return $this->render('vivimainBundle:Admin:adminUsers.html.twig', array(                                
                           'showDiv' => '',
                           'users'    => $users,
          ));
        }
    }
    
    public function adminUsersEditAction($id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        $user = $userManager->findUserBy(array('id'=>$id));
        $data['email'] = $user->getEmail();
        $data['username'] = $user->getUsername();       
        $errors = [];
        dump($user->isEnabled());
        if(!empty($_POST))
        {
            $data = $_POST['fos_user_registration_form'];
            
           if($data['email'] !== $user->getEmail())
           {
            if(null !== $userManager->findUserByEmail($data['email']))
            {
                $errors[] = "<p>The Email is already used</p>";
            }
                $user->setEmail($data['email']);
            
           }
           if($data['username'] !== $user->getUsername())
           {
            if(null !== $userManager->findUserByUsername($data['username']))
            {
                $errors[] = "<p>The username is already used</p>";
            }
                $user->setUsername($data['username']);
           }
           if(!empty($data['plainPassword']['first']) )
           {
              if($data['plainPassword']['first'] === $data['plainPassword']['second'] )
               {
                $encoder = $this->get('security.password_encoder');
                $pass = $encoder->encodePassword($user, $data['plainPassword']['second']);
                 $user->setPassword($pass);
               }else{ $errors[] = "<p>The entered passwords don't match</p>"; }
           }
           if(!empty($data['role'])){ $user->setRoles($data['role']); }
           
           if(isset($data['enabled'])){
              
              if($user->isEnabled() !== $data['enabled']){ $user->setEnabled((int) $data['enabled']); }
            }
           if(empty($errors))
           {
            
           $userManager->updateUser($user);
           $this->addFlash("info", "<p class='bg-success'> User updated successfully</p>");
           return $this->redirectToRoute('vivimain_admin_user');
           }
        }
        
        $data['id'] = $user->getId();
        return $this->render('vivimainBundle:Admin:adminUsersEdit.html.twig', array(                                
                           'showDiv' => '',
                           'users'    => $users,
                           'data'    => $data,
                           'errors'    => $errors,
          ));
    }
    
    public function adminUsersDelAction($id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));
        $userManager->deleteUser($user);
        
        $this->addFlash("info", "<p class='bg-success'> User removed successfully</p>");
        return $this->redirectToRoute('vivimain_admin_user');
    }
    
}
