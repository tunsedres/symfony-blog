<?php
// src/Controller/LuckyController.php
namespace App\Controller\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Blog;

class HomeController extends Controller
{
    public function Home()
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        return $this->render('frontend/index.html.twig', array(
            'blogs' => $blogs,
        ));
    }
}