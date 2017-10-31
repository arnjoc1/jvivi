<?php

namespace vivi\mainBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use vivi\mainBundle\Entity\Articles;
use vivi\mainBundle\Entity\Image;
use vivi\mainBundle\Entity\Comment;
use vivi\mainBundle\Entity\Category;
use vivi\mainBundle\Form\ArticlesType;
use vivi\mainBundle\Form\CommentType;
use vivi\mainBundle\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="vivimain_admin")
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
    

    /**
     * @Route("/admin/articles", name="vivimain_admin_articles")
     *
     */
    public function adminArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $arts = $em->getRepository('vivimainBundle:Articles')->findAll();
        
        return $this->render('vivimainBundle:Admin:AdminArticles.html.twig', array(
                         'articles' => $arts,                                        
                         'showDiv' => "none"                                       
                              ));
    }
     
    /**
     * @Route("/admin/article/{id}", name="vivimain_admin_article_view")
     *
     */
    public function adminArticlesViewAction(Articles $article)
    {
        $em = $this->getDoctrine()->getManager();
        $art = $em->getRepository('vivimainBundle:Articles')->findAll();
        
        return $this->render('vivimainBundle:Admin:AdminArticlesView.html.twig', array(
                         'articles' => $art,                                         
                         'article' => $article,                                  
                         'showDiv' => "block"                                            
        ));
    }
    
    /**
     * @Route("/admin/addArticle", name="vivimain_admin_addArticles")
     *
     */
    public function adminAddArticlesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $arts = $em->getRepository('vivimainBundle:Articles')->findAll();
        $article = new Articles();
        
        $form = $this->createForm(articlesType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
          
          /*
           * Traitement de l'image
           
            $image = new Image();
            $file = $form->get('image')->getData();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            $image->setUrl($filename);
            $image->setAlt($file->getClientOriginalName());
            $article->setImage($image);*/
            $article->addCategory($form->get('categories')->getData());

            $em->persist($article);
            $em->flush();
            /*$file->move($this->getParameter('img_upload_directory'), $filename);
*/
            $this->addFlash("info", "<p class='bg-success'>Article bien ajouté</p>");
            return $this->redirectToRoute('vivimain_admin_articles');
        
        }
        return $this->render('vivimainBundle:Admin:adminArticles.html.twig', array(
                           'form' => $form->createView(),                                          
                           'articles' => $arts,                                               
                           'showDiv' => 'block'                                               
        ));
    }

    /**
     * @Route("/admin/{id}/article_edit", name="vivimain_admin_article_edit")
     *
     */
    public function adminArticleEditAction(Request $request, Articles $article)
    {
        $em = $this->getDoctrine()->getManager();
        $arts = $em->getRepository('vivimainBundle:Articles')->findAll();
        $form = $this->createForm(articlesType::class, $article);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {

            $em->flush();
            $this->addFlash("info", "<p class='bg-success'>Article bien mise à jour </p>");
            return $this->redirectToRoute('vivimain_admin_articles');
        
        }
        return $this->render('vivimainBundle:Admin:adminArticles.html.twig', array(
                           'form' => $form->createView(),                                          
                           'articles' => $arts,                                               
                           'showDiv' => 'block'                                               
        ));
    }
    
    /**
     * @Route("/admin/{id}/delete", name="vivimain_admin_article_delete")
     *
     */
    public function adminArticleDeleteAction($id, Articles $article)
    {
        $em = $this->getDoctrine()->getManager();
        $coms = $em->getRepository('vivimainBundle:Comment')->findBy(['articles'=>$id]);
        
        if(0 !== count($coms))
        {
            foreach( $coms as $com )
            {
              $em->remove($com);
            }
        }
       
        $em->remove($article);
        $em->flush();
        
        
        $this->addFlash("info", "<p class='bg-success'>Article bien supprimé</p>");
        return $this->redirectToRoute('vivimain_admin_articles');
    }
    

    /**
     * @Route("/admin/article/{id}/comments", name="vivimain_admin_art_com")
     *
     */
    public function adminArtComAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $arts = $em->getRepository('vivimainBundle:Articles')->findAll();
        $coms = $em->getRepository('vivimainBundle:Comment')->findBy(array('articles'=>$id));
        //dump($coms); die();
        return $this->render('vivimainBundle:Admin:adminArticlesCom.html.twig', array(
                           'articles' => $arts,                                               
                           'coms' => $coms,                                               
                           'artCom' => $id,                                               
                           'showDiv' => 'block'                                               
        ));        
    }
    
    /**
     * @Route("/admin/article/{art}/comment/{id}/delete", name="vivimain_admin_art_com_del")
     * @ParamConverter("com", options={"mapping": {"id": "id"}})
     */
    public function adminArtComDelAction($art, Comment $com)
    {   
        /*dump($art);
        dump($com); die();*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($com);
        $em->flush();
        
        $this->addFlash("info", "<p class='bg-success'>Comment deleted successfully</p>");
        return $this->redirectToRoute('vivimain_admin_art_com', ['id'=>$art]);
    }
    
    /**
     * @Route("/admin/categories", name="vivimain_admin_cats")
     *
     */
    public function adminCatsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(         
                           'cats' => $cats,                                               
                           'showDiv' => 'none'                                               
        ));
    }
    
    /**
     * @Route("/admin/Category/add", name="vivimain_admin_addCat")
     */
    public function adminCatAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
           $em->persist($category);
           $em->flush();
           
           $this->addFlash("info", "<p class='bg-success'> Category added successfully</p>");
           return $this->redirectToRoute('vivimain_admin_cats');
        }
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(
                           'cats'    => $cats,                                               
                           'showDiv' => 'block',
                           'form'    => $form->createView()
        ));
    }
    
    /**
     * @Route("/admin/category/{id}/edit", name="vivimain_admin_cat_edit")
     */
    public function adminCatEditAction(Category $category, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cats = $em->getRepository('vivimainBundle:Category')->findAll();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash("info", "<p class='bg-success'> Category edited successfully</p>");
            return $this->redirectToRoute('vivimain_admin_cats');
        }
        return $this->render('vivimainBundle:Admin:adminCats.html.twig', array(       
                           'cats'    => $cats,                                               
                           'showDiv' => 'block',
                           'form'    => $form->createView()
        ));
        
    }
    
    /**
     * @Route("/admin/category/{id}/delete", name="vivimain_admin_cat_delete")
     */
    public function adminCatDeleteAction(Category $cat)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cat);
        $em->flush();
        
        $this->addFlash("info", "<p class='bg-success'> Category removed successfully</p>");
        return $this->redirectToRoute('vivimain_admin_cats');
    }
    
    /**
     * @Route("/admin/users", name="vivimain_admin_user")
     */
    public function adminUsersAction()
    {
        $users = $this->get('fos_user.user_manager')->findUsers();
             
        return $this->render('vivimainBundle:Admin:adminUsers.html.twig', array(                                
                           'showDiv' => 'none',
                           'users'    => $users
        ));
    }

    /**
     * @Route("/admin/user/register", name="vivimain_admin_user_register")
     */
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
                           'showDiv' => 'block',
                           'users'    => $users,
                           'errors'    => $error,
                           'data'    => $data
          ));
          
          }else{
          
          
          return $this->render('vivimainBundle:Admin:adminUsers.html.twig', array(                                
                           'showDiv' => 'block',
                           'users'    => $users,
          ));
        }
    }
    /**
     * @Route("/admin/user/{id}/edit/", name="vivimain_admin_user_edit")
     */
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
                           'showDiv' => 'block',
                           'users'    => $users,
                           'data'    => $data,
                           'errors'    => $errors,
          ));
    }


}