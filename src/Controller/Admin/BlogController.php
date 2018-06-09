<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Blog;
use App\Entity\BlogCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class BlogController extends Controller
{
    
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Blog::class);
        $blogs = $repository->findAll();
        return $this->render('admin/blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/admin/blog-ekle", name="admin_blog_ekle")
     * @Method({"GET"})
     */
    public function create(Request $request)
    {
        $blog = new Blog();
        $categories = $this->getDoctrine()->getRepository(BlogCategory::class)->findAll();
                
        return $this->render('admin/blog/create.html.twig', [
            'categories'=>$categories
        ]);
    }

    /**
     * @Route("/admin/blog-ekle", name="admin_blog_kaydet")
     * @Method({"POST"})
     */
    public function store(Request $request)
    {           
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection(array(            
            'title' => new Assert\Length(array('min' => 5)),
        ));
        $categories = $this->getDoctrine()->getRepository(BlogCategory::class)->findAll();
        $violations = $validator->validate(array('title'=>$request->get('title')), $constraint);
        
        if (0 ==! count($violations)) {
            return $this->render('admin/blog/create.html.twig', [
                'errors' => $violations,
                'categories' => $categories
            ]);
        }
        //kategoriyi bul
        $category = $this->getDoctrine()->getRepository(BlogCategory::class)->find($request->get('category'));

        $blog= new Blog();
        $manager = $this->getDoctrine()->getManager();
        $blog->setTitle($request->get('title'));
        $blog->setContent($request->get('content'));
        $blog->setTags($request->get('tags'));
        $blog->setCreatedAt(new \DateTime());
        $blog->setCategory($category);
        
        $manager->persist($blog);
        $manager->flush();

        $this->addFlash(
            'success',
            'Başarılı!'
        );

        return $this->redirectToRoute('admin_blog');
        
    }

    /**
     * @Route("/admin/blog/edit/{id}", name="admin_blog_edit")
     * @Method({"GET"})
     */
    public function edit($id){
        
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $categories = $this->getDoctrine()->getRepository(BlogCategory::class)->findAll();
        if (!$blog) {
            throw $this->createNotFoundException(
                'Bu id ye ait blog bulunamadı '.$id
            );
        }
        return $this->render('admin/blog/edit.html.twig', ['blog'=> $blog,'categories'=>$categories]);       
    }

    /**
     * @Route("/admin/blog/edit/{id}", name="admin_blog_update")
     * @Method({"GET", "POST"})
     */
    public function update(Request $request,$id)
    {
        $category = $this->getDoctrine()->getRepository(BlogCategory::class)->find($request->get('category'));

        $blog= $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $manager = $this->getDoctrine()->getManager();
        $blog->setTitle($request->get('title'));
        $blog->setContent($request->get('content'));
        $blog->setTags($request->get('tags'));
        $blog->setCreatedAt(new \DateTime());
        $blog->setCategory($category);
        $manager->flush();

        $this->addFlash(
            'success',
            'Başarılı!'
        );
        return $this->redirectToRoute('admin_blog');      
    }

    /**
     * @Route("/admin/blog/sil/{id}", name="admin_blog_sil")
     */
    public function destroy($id){
        try {
            $manager = $this->getDoctrine()->getManager();
            $blog = $manager->getRepository(Blog::class)->find($id);
            $manager->remove($blog);
            $sonuc = $manager->flush();
            $this->addFlash(
                'success',
                'İşlem Başarılı!'
            );
        }
        catch(Exception $e){
            $this->addFlash(
                'error',
                'İşlem başarısız'
            );
        }       
        
        return $this->redirectToRoute('admin_blog');
    }
}
