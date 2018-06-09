<?php

namespace App\Controller\Admin;

use App\Entity\BlogCategory;
use App\Form\BlogCategoryFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class BlogCategoryController extends Controller
{
    /**
     * @Route("/admin/blog/kategori/ekle", name="admin_blog_category_add")
     */
    public function createAction()
    {
        $blogCategory = new BlogCategory();
        $form = $this->createForm(BlogCategoryFormType::class, $blogCategory);
        
        return $this->render('admin/blog_category/create.html.twig', [
            'blogCategoryForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("admin/blog/kategori/kaydet", name="admin_blog_category_save")
     * @Method("POST")
     */
    public function store(Request $request){
        $blogCategory = new BlogCategory();
        $form = $this->createForm(BlogCategoryFormType::class, $blogCategory);
        $form->handleRequest($request);
        if($form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $blogCategory = $form->getData();
            $manager->persist($blogCategory);
            $manager->flush();
        }
        return $this->redirectToRoute('admin_blog_category_list');
    }

    /**
     * @Route("admin/blog/kategori", name="admin_blog_category_list")
     */
    public function index(){
        
        $blogCategories = $this->getDoctrine()->getRepository(BlogCategory::class)->findAll();
        return $this->render('admin/blog_category/index.html.twig', [
            'blogCategories' => $blogCategories
        ]);
    }
    /**
     * @Route("admin/blog/kategori/edit/{id}", name="admin_blog_category_edit")
     * @Method("GET")
     */
    public function edit($id){
        
        $blogCategory = $this->getDoctrine()->getRepository(BlogCategory::class)->find($id);
        $form = $this->createForm(BlogCategoryFormType::class, $blogCategory);
        
        return $this->render('admin/blog_category/edit.html.twig', [
            'blogCategoryForm'=> $form->createView()
        ]);
    }

    /**
     * @Route("admin/blog/kategori/edit/{id}", name="admin_blog_category_update")
     * @Method("POST")
     */
    public function update(Request $request, $id){
        
        $manager = $this->getDoctrine()->getManager();
        $blogCategory = $manager->getRepository(BlogCategory::class)->find($id);
        $form = $this->createForm(BlogCategoryFormType::class, $blogCategory);
        $form->handleRequest($request);
        if($form->isValid()){
            $manager->flush();
        }
        
        return $this->redirectToRoute('admin_blog_category_list');
    }

    /**
     * @Route("admin/blog/kategori/sil/{id}", name="admin_blog_category_delete")
     */
    public function destroy($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $cat = $manager->getRepository(BlogCategory::class)->find($id);
        $manager->remove($cat);
        $manager->flush();
        return $this->redirectToRoute('admin_blog_category_list');
    }
}
