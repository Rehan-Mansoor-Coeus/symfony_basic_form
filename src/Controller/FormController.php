<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Fetcher;
use Psr\Log\LoggerInterface;

use App\Entity\Post;
use App\Form\PostType;

class FormController extends AbstractController
{
    /**
     * @Route("/form", name="form")
     */
    public function index(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostTYpe::class , $post , [
            'action' => $this->generateUrl('form')
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->render('form/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/store", name="store")
     */
    public function store(Request $request)
    {
        $post = new Post();
        $post->setTitle('title from controller');
        $post->setDescription('description from controller');
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();


        $form = $this->createForm(PostTYpe::class , $post , [
            'action' => $this->generateUrl('form')
        ]);

        $form->handleRequest($request);

        return $this->render('form/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/retrive", name="retrive")
     */
    public function retrive(Fetcher $fetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $retrive = $em->getRepository(Post::class)->findAll();
        $fetcher = $fetcher->get('https://api.publicapis.org/entries');
//        dd($fetcher['entries'][0]);
        return $this->render('form/data.html.twig', [
            'data' => $retrive,
            'fetcher' => $fetcher['entries']
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update($id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Post::class)->find($id);
        $data->setTitle('title updated');
        $em->flush();
        $this->addFlash('success', 'Data has been Updated!');
        return $this->redirect('/retrive');
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Post::class)->find($id);
        $em->remove($data);
        $em->flush();

        $this->addFlash('success', 'Data has been Removed!');
        return $this->redirect('/retrive');
    }

}