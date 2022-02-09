<?php

namespace App\Controller;

use App\Form\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(FormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data['email'] = $form->getData()['email'];
            return $this->redirectToRoute(
                'app_attacked',
                $data,
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        return $this->render('form/index.html.twig', [
            'controller_name' => 'FormController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/attacked', name: 'app_attacked')]
    public function attacked(Request $request): Response
    {
        $text = "Salut %s %s ! Tu viens d'être attaqué par une cyberattaque créée de toute pièces par Jhack Chirac ! Pas de panique, aucune donnée n'est conservée et tu n'as aucune crainte à avoir !";
        $email = strtok($request->query->get('email'), '@');
        $emailExploded = explode('.', $email);
        if (count($emailExploded) === 2) {
            $firstName = ucwords(str_replace('-', ' ', array_shift($emailExploded)));
            $lastName = ucwords(str_replace('-', ' ', end($emailExploded)));
            $text = sprintf($text, $lastName, $firstName);
        }

        return $this->render('form/trapped.html.twig', [
            'text' => $text
        ]);
    }
}
