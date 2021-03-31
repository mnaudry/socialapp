<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Entity\User;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LoginGoogleAuthenticator extends SocialAuthenticator implements PasswordAuthenticatedInterface
{ 

    use TargetPathTrait;
    public const LOGIN_CHECK_ROUTE = 'app_login_check_google';
    public const LOGIN_ROUTE = "app_login";
    private const REDIRECT_URL = "https://localhost:8000";

    private $clientRegistry;
    private $entityManager;
    private $session;
    private $urlGenerator;
    private $encoder;
    private $csrfTokenManager;
    private $params;
    //private $client;

    public function __construct(ClientRegistry $clientRegistry,EntityManagerInterface $entityManager,SessionInterface $session, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $encoder, CsrfTokenManagerInterface $csrfTokenManager, ContainerBagInterface $params){
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->session = $session ;
        $this->urlGenerator = $urlGenerator;
        $this->encoder = $encoder;
        $this->csrfTokenManager = $csrfTokenManager ;
        $this->params = $params;
        //$this->client = $client;
    }
    public function supports(Request $request)
    {
         return $request->attributes->get('_route') === self::LOGIN_CHECK_ROUTE && $request->isMethod('POST');
        ///return $request->attributes->get('_route') === self::LOGIN_CHECK_ROUTE ;
    }

    public function getCredentials(Request $request)
    {

        $credentials = [
            'csrf_token' => $request->request->get('_csrf_token'),
            'code' => $request->request->get('code'),
        ];

       return $credentials;
    
    }

    /**
     * @return Google Provider
     */
    private function getGoogleProvider()
    {

          return  $provider = new Google([
                'clientId'     => $this->params->get('google_client_id'),
                'clientSecret' => $this->params->get('google_secret_id'),
                'redirectUri'  => self::REDIRECT_URL,
            ]);
	}

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
             //throw new InvalidCsrfTokenException();
             // invalid csfr token error
             throw new CustomUserMessageAuthenticationException('{"global" : "Forbiden : You don\'t have permission to access on this web site."}');
        }


        $provider = $this->getGoogleProvider();

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $credentials['code']
        ]);

        try {

            $googleUser = $provider->getResourceOwner($token);

        }catch(Exception $e){

            throw new CustomUserMessageAuthenticationException('{"global" : "error : Something went wrong, try lo login later."}');
        }


        $email = $googleUser->getEmail();


        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
       
        if(!$user){

            //throw new CustomUserMessageAuthenticationException('oh man');
            $user = new User();
            $user->setEmail($email);
            $user->setFirstName( $googleUser->getFirstName());
            $user->setName($googleUser->getLastName());
            $user->setGoogleId($googleUser->getId());
            $new_password = random_bytes(10);
            $user->setPassword($this->encoder->encodePassword($user,$new_password));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
 
         }


         $this->session->set('access_token_g', $token);
         $this->session->set('googleId', $googleUser->getId());
             
         return $user;
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
       

        $error = '{ "error" : '.$exception->getMessageKey().'}';

        return new JsonResponse($error, Response::HTTP_UNAUTHORIZED,[],true);
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

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->getLoginUrl());
    }


    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

     /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return null;
    }
}
