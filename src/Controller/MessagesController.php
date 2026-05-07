<?php

namespace App\Controller;

use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessagesController extends AbstractController
{
    #[Route("/contact", name: "app_contact")]
    public function new(Request $request): Response
    {
        $form = $this->createForm(MessageType::class);

        // INFO: Clone de la version vierge du formulaire.
        $emptyForm = clone $form;

        $form->handleRequest($request);

        // INFO: 🔥 The magic happens here!
        // - If the form is submitted, valid, and the request format is
        // Turbo Stream, we return a Turbo Stream response with the rendered success template.
        // - Otherwise, if the form is submitted and valid, we add a flash message and redirect to
        // the home page.
        // - If the form is not valid, we render the form with an appropriate HTTP status code.

        //          ╒═════════════════════════════════════════════════════════╕
        //                       submitted & valid & turbo stream
        //          └─────────────────────────────────────────────────────────┘
        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $request->getPreferredFormat() === TurboBundle::STREAM_FORMAT
        ) {
            /* $form = $this->createForm(MessageType::class); */

            return new Response(
                $this->renderView("messages/success.stream.html.twig", [
                    "name" => $form->get("name")->getData(),
                    "form" => $emptyForm->createView(),
                ]),
                Response::HTTP_OK,
                ["Content-Type" => "text/vnd.turbo-stream.html"],
            );
        }

        //          ╒═════════════════════════════════════════════════════════╕
        //                               submitted & valid
        //          └─────────────────────────────────────────────────────────┘
        if ($form->isSubmitted() && $form->isValid()) {
            dump("Sending mail...");

            $this->addFlash("success", "Message envoyé avec succès");
            return $this->redirectToRoute(
                "app_home",
                [],
                Response::HTTP_SEE_OTHER,
            );
        }

        //          ╒═════════════════════════════════════════════════════════╕
        //                                    render
        //          └─────────────────────────────────────────────────────────┘
        return $this->render(
            "messages/new.html.twig",
            ["form" => $form->createView()],
            $form->isSubmitted()
                ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
                : null,
        );
    }
}
