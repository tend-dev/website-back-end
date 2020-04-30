<?php

declare(strict_types=1);

namespace App\Action\Api;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class SendContactEmailAction
{
    /**
     * @Route(
     *     "/send-contact-data",
     *     name="send_contact_data",
     *     methods={"POST"}
     * )
     * @SWG\Post(
     *     path="/send-contact-data",
     *     tags={"Contact"},
     *     summary="Send the collected data to email.",
     *    @SWG\Parameter(
     *         name="firstName",
     *         in="query",
     *         description="first name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="lastName",
     *         in="query",
     *         description="last name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success in sending"
     *     )
     * )
     */
    public function __invoke(Request $request, MailerInterface $mailer, string $toEmail): Response
    {
        $data = $this->handleRequest($request);
        $endLine = '\r\n';
        $content = 'Campaign goes to finish'. $endLine;
        $content .= 'Contact data:' . $endLine;
        $content .= "First name: " . $data['firstName'] ." " . $endLine;
        $content .= "Last name: " . $data['lastName'] ." " . $endLine;
        $content .= "Email: " . $data['email'] ." " . $endLine;
        $content .= "Phone: " . $data['phone'] ." " . $endLine;
        $content .= "Message: " . $data['message'] ." " . $endLine;

        $email = (new Email())
            ->from($toEmail)
            ->to($toEmail)
            ->subject('Contact data')
            ->text($content)
            ;

        $mailer->send($email);
        return new JsonResponse([]);
    }

    public function handleRequest(Request $request)
    {
        if (!$request->getContent()) {
            return [];
        }
        $data = json_decode($request->getContent(), true);
        if (null === $data) {
            throw new BadRequestHttpException();
        }

        return $data;
    }
}
