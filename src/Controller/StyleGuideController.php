<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StyleGuideController extends AbstractController
{
  // STYLE GUIDE
  #[Route('/styleguide', name: 'style_guide', methods: ['GET'])]
  public function styleGuide(): Response
  {
    return $this->render('style_guide/styleGuide.html.twig');
  }
}
