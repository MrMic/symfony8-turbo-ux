<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class MessagesController extends AbstractController
{
    #[Route("/contact", name: "app_contact")]
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add("name", TextType::class, [
                "constraints" => [new NotBlank(), new Length(min: 2)],
            ])
            ->add("email", EmailType::class, [
                "constraints" => [new NotBlank(), new Email()],
            ])
            ->add("message", TextareaType::class, [
                "constraints" => [new NotBlank(), new Length(min: 8)],
            ])
            ->getForm();

        $form->handleRequest($request);

        // INFO: 🔥 The magic happens here!
        // - If the form is submitted, valid, and the request format is
        // Turbo Stream, we return a Turbo Stream response with the rendered success template.
        // - Otherwise, if the form is submitted and valid, we add a flash message and redirect to
        // the home page.
        // - If the form is not valid, we render the form with an appropriate HTTP status code.
        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $request->getPreferredFormat() === TurboBundle::STREAM_FORMAT
        ) {
            return new Response(
                $this->renderView("messages/success.stream.html.twig", [
                    "name" => $form->get("name")->getData(),
                ]),
                Response::HTTP_OK,
                ["Content-Type" => "text/vnd.turbo-stream.html"],
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dump("Sending mail...");

            $this->addFlash("success", "Message envoyé avec succès");
            return $this->redirectToRoute(
                "app_home",
                [],
                Response::HTTP_SEE_OTHER,
            );
        }

        return $this->render(
            "messages/new.html.twig",
            [
                "form" => $form->createView(),
            ],
            $form->isSubmitted() && !$form->isValid()
                ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
                : null,
        );
    }
}
