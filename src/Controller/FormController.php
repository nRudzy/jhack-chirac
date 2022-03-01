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
            return $this->redirectToRoute('app_form_attacked', ['email' => $form->get('email')->getData()]);
        }

        return $this->render('form/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/attacked/{email}')]
    public function attacked(string $email): Response
    {
        $payload['text'] = sprintf(
            'Le partner %s vient de se faire avoir ! Il est urgent de lui envoyer un message ! ;-) Le membre de Jhack Chirac qui le contacte doit glisser un petit emoji Jhack sur ce post, afin d’éviter de le sursolliciter !',
            $email,
        );

        try {
            $this->client->request(
                'POST',
                sprintf('%s/%s/%s/%s', self::URL_WEBHOOK, $this->keyT, $this->keyB, $this->token),
                ['json' => $payload],
            );
        } catch (\Exception | TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }

        return $this->render('form/trapped.html.twig');
    }
}
