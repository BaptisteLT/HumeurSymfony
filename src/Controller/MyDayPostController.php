<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\MyDayPost;
use Cocur\Slugify\Slugify;
use App\Form\MyDayPostType;
use Doctrine\ORM\EntityManager;
use App\Repository\MyDayPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyDayPostController extends AbstractController
{
    #[Route('/my/day/post', name: 'my_day_post')]
    public function index(PaginatorInterface $paginator, MyDayPostRepository $myDayPostRepository, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $myDayPostRepository->findBy(['private'=>false],['created_at'=>'DESC']), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        return $this->render('my_day_post/index.html.twig', [
            'posts' => $pagination
        ]);
    }

    #[Route('/my/own/posts', name: 'my_own_posts')]
    public function myPosts(PaginatorInterface $paginator, MyDayPostRepository $myDayPostRepository, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $myDayPostRepository->findBy(['user'=>$this->getUser()],['created_at'=>'DESC']), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        return $this->render('my_day_post/index.html.twig', [
            'posts' => $pagination
        ]);
    }


    #[Route('/my/day/post/view/{slug}', name: 'my_day_post_view')]
    public function viewPost(MyDayPost $post): Response
    {
  
        return $this->render('my_day_post/view.html.twig', [
            'post' => $post
        ]);
    }

    
    #[Route('/my/day/post/edit/{id}', name: 'my_day_post_edit')]
    public function editPost(MyDayPost $post, Request $request, EntityManagerInterface $entityManager, MyDayPostRepository $myDayPostRepository): Response
    {
        //Protection contre la modification si on n'est pas le propriétaire
        if($this->getUser()!==$post->getUser())
        {
            return $this->redirectToRoute('my_day_post_view',['slug'=>$post->getSlug()]);
        }

        $form = $this->createForm(MyDayPostType::class, $post);

        $form->handleRequest($request);
        //dump($request->query->all());die;

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            //On met à jour le slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($post->getTitle());
            //Si il y a une duplication de slug
            
            if(count($myDayPostRepository->findBySlugAndNotId($slug, $post->getUser())) > 0)
            {
                $post->setSlug($slug.'-'.$post->getId());
            }
            else
            {
                $post->setSlug($slug);
            }

            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post edited!');

            return $this->redirectToRoute('my_day_post_view',['slug'=>$post->getSlug()]);
        }

        return $this->renderForm('my_day_post/edit.html.twig', [
            'form' => $form
        ]);
    }



    #[Route('/my/day/post/create', name: 'my_day_post_create')]
    public function createPost(MyDayPostRepository $myDayPostRepository , Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new MyDayPost(); 
        $form = $this->createForm(MyDayPostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$post` variable has also been updated
            $post = $form->getData();
            $post->setUser($this->getUser())
                 ->setCreatedAt(new DateTimeImmutable('now'))
                 ->setSlug('');
 
            $entityManager->persist($post);
            $entityManager->flush();

           //On crée le slug
           $slugify = new Slugify();
           $slug = $slugify->slugify($post->getTitle());
           //Si il y a une duplication de slug
           if(count($myDayPostRepository->findBySlugAndNotId($slug, $post->getId())) > 0)
           {
               $post->setSlug($slug.'-'.$post->getId());
           }
           else
           {
               $post->setSlug($slug);
           }
           $entityManager->persist($post);
           $entityManager->flush();
           $this->addFlash('success', 'Post created!');

            return $this->redirectToRoute('my_day_post_view',['slug'=>$post->getSlug()]);
        }

        return $this->renderForm('my_day_post/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/my/day/post/delete/{id}', name: 'my_day_post_delete')]
    public function deletePost(MyDayPost $post, EntityManagerInterface $entityManager, Request $request): Response
    {
        //Protection contre la modification si on n'est pas le propriétaire, plus vérification du CSRF token
        if($this->getUser()!==$post->getUser() || !$this->isCsrfTokenValid('delete-item', $request->request->get('token')))
        {
            return $this->redirectToRoute('my_day_post_view',['id'=>$post->getId()]);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post deleted!');

        return $this->redirectToRoute('my_own_posts');

        return $this->render('my_day_post/delete.html.twig', [
            'post' => $post
        ]);
    }
}
