<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
//use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginAuthenticator extends AbstractGuardAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_CHECK_ROUTE = 'app_login_check';
    public const LOGIN_ROUTE = "app_login";

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_CHECK_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
             //throw new InvalidCsrfTokenException();
             // invalid csfr token error
             throw new CustomUserMessageAuthenticationException('{"global" : "Forbiden : You don\'t have permission to access on this web site."}');
        }

       /* if($this->isEmail( $credentials['email'])){
          
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        }else {
            $credentials['userName'] = $credentials['email'] ;
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['userName' =>  $credentials['userName']]);
        }*/

        $user = $this->entityManager->getRePository(User::class)->findUserByEmailOrUsername( $credentials['email']);
       
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('{"email" : "Your email or username could not be found."}');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $isPassword = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        if(!$isPassword)
             throw new CustomUserMessageAuthenticationException('{"password" : "Your password is not correct."}');
        else
            return $isPassword;

    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $url = $this->urlGenerator->generate('app_homepage');
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            $url = $targetPath ;
        }

        $success = [ "success" => [ "redirect_url" => $url ]];

        return new JsonResponse($success, Response::HTTP_OK );
    }



    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
         /*$data = [
            // you may want to customize or obfuscate the message first
            'error' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];*/

        $error = '{ "error" : '.$exception->getMessageKey().'}';

        return new JsonResponse($error, Response::HTTP_UNAUTHORIZED,[],true);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->getLoginUrl());
    }


    public function supportsRememberMe()
    {
        return true;
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function isEmail($email){

        $emailConstraint = new Assert\Email();
        $errors = $this->validator->validate(
            $email,
            $emailConstraint
        );

        return(0 === count($errors))?true:false;
    }
}
