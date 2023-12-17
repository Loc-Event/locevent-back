<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Crée un nouvel utilisateur
        $user = new User();

        // Vérifie si la demande est une demande JSON
        if ($request->getContentType() === 'json') {
            $data = json_decode($request->getContent(), true);

            if ($this->isValidJsonData($data)) {
                $user->setFirstname($data['firstname']);
                $user->setLastname($data['lastname']);
                $user->setEmail($data['email']);

                $this->processRegistration($user, $data['plainPassword'], $userPasswordHasher, $entityManager, $request);

                // Retourne une réponse JSON pour indiquer le succès de l'inscription
                return new JsonResponse(['message' => 'Inscription réussie'], Response::HTTP_CREATED);
            }

            // Retourne une réponse JSON pour indiquer une demande invalide
            return new JsonResponse(['error' => 'Données manquantes ou invalides dans la demande JSON.'], Response::HTTP_BAD_REQUEST);
        }

        // Traitement pour les demandes non-JSON (HTML)
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processRegistration($user, $form->get('plainPassword')->getData(), $userPasswordHasher, $entityManager, $request);

            // Retourne à l'index après l'inscription réussie
            return $this->redirectToRoute('app_index');
        }

        // Retourne la vue HTML pour le formulaire d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function isValidJsonData(array $data): bool
    {
        return isset($data['firstname'], $data['lastname'], $data['email'], $data['plainPassword']);
    }

    private function processRegistration(User $user, string $plainPassword, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,Request $request): void
    {
        // Encodage du mot de passe et autres traitements
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
        $user->setCreatedAt(new DateTimeImmutable);
        $entityManager->persist($user);
        $entityManager->flush();

        // Envoie de l'e-mail de confirmation
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($request->server->get('EMAIL'), $request->server->get('NAME_EMAIL')))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre compte a été vérifié.');

        return $this->redirectToRoute('app_login');
    }
}
