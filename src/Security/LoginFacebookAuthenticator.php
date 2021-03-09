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
use League\OAuth2\Client\Provider\Facebook;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFacebookAuthenticator extends SocialAuthenticator implements PasswordAuthenticatedInterface
{ 

    use TargetPathTrait;
    public const LOGIN_CHECK_ROUTE = 'app_login_check_facebook';
    public const LOGIN_ROUTE = "app_login";

    private $clientRegistry;
    private $entityManager;
    private $session;
    private $urlGenerator;
    private $encoder;
    private $csrfTokenManager;
    //private $client;

    public function __construct(ClientRegistry $clientRegistry,EntityManagerInterface $entityManager,SessionInterface $session, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $encoder, CsrfTokenManagerInterface $csrfTokenManager){
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->session = $session ;
        $this->urlGenerator = $urlGenerator;
        $this->encoder = $encoder;
        $this->csrfTokenManager = $csrfTokenManager ;
        //$this->client = $client;
    }
    public function supports(Request $request)
    {
         return $request->attributes->get('_route') === self::LOGIN_CHECK_ROUTE && $request->isMethod('POST');
        ///return $request->attributes->get('_route') === self::LOGIN_CHECK_ROUTE ;
    }

    public function getCredentials(Request $request)
    {

        //if($request->isMethod('POST') && $request->get('action') ==='login') {

      //  $client  = $this->getFacebookClient();
      /*  $client->redirect([
                'public_profile', 'email' // the scopes you want to access
                ]);
        
        $credentials['access_token'] = $this->fetchAccessToken($client);
       // }
        
        $credentials['csrf_token'] = $request->request->get('_csrf_token');*/
       /* $url = $this->getFacebookClient()->redirect([
            'public_profile', 'email' // the scopes you want to access
            ]);*/

       // dd($url);
       // $accessToken = $this->fetchAccessToken($this->getFacebookClient());
        //$this->session->set('access_token_fb', $accessToken);


       // return $this->fetchAccessToken($this->getFacebookClient());
       $client = $this->getFacebookClient() ;
       $provider =  $client->getOAuth2Provider();

       $credentials = [
        'accessToken' =>  $provider->getLongLivedAccessToken($request->request->get('accessToken')),
        'csrf_token' => $request->request->get('_csrf_token'),
        ];

       return $credentials;
       
    }

    /**
     * @return FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry
            // "facebook_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('facebook_main');
	}

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
             //throw new InvalidCsrfTokenException();
             // invalid csfr token error
             throw new CustomUserMessageAuthenticationException('{"global" : "Forbiden : You don\'t have permission to access on this web site."}');
        }


        $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);

        $email = $facebookUser->getEmail();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if(!$user){

           //throw new CustomUserMessageAuthenticationException('oh man');
           $user = new User();
           $user->setEmail($email);
           $user->setFirstName( $facebookUser->getFirstName());
           $user->setName($facebookUser->getLastName());
           $user->setFacebookId($facebookUser->getId());
           $new_password = random_bytes(10);
           $user->setPassword($this->encoder->encodePassword($user,$new_password));
           $this->entityManager->persist($user);
           $this->entityManager->flush();

        }

        $this->session->set('accessTokenFb', $credentials);
        $this->session->set('facebookId', $facebookUser->getId());
  
        return $user;


          /*   $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);

             $email = $facebookUser->getEmail();

             $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

             if(!$user){

                //throw new CustomUserMessageAuthenticationException('oh man');
                $user = new User();
                $user->setEmail($email);
                $user->setFirstName( $facebookUser->getFirstName());
                $user->setName($facebookUser->getLastName());
                $user->setFacebookId($facebookUser->getId());
                $new_password = random_bytes(10);
                $user->setPassword($this->encoder->encodePassword($user,$new_password));
                $this->entityManager->persist($user);
                $this->entityManager->flush();

             }

            // $accessToken = $this->getFacebookClient()->getAccessToken();
             $this->session->set('access_token_fb', $credentials);
             $this->session->set('facebookId', $facebookUser->getId());
             dd($user);
            return $user;*/
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
       /* $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        $request->getSession()->getFlashBag()->add("error", $message);

        $url = $this->urlGenerator->generate(self::LOGIN_ROUTE);
       // dd($url);
        return  new RedirectResponse($url);*/

        $error = '{ "error" : '.$exception->getMessageKey().'}';

        return new JsonResponse($error, Response::HTTP_UNAUTHORIZED,[],true);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
      /*  $url = $this->urlGenerator->generate('app_homepage');
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            $url = $targetPath ;
        }

        return new RedirectResponse($url);*/
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        //$url = $this->urlGenerator->generate(self::LOGIN_ROUTE);
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
