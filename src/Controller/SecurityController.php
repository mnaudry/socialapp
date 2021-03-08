<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {

            return $this->redirectToRoute('app_homepage');
        }

        // get the login error if there is one
       // $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $facebook_app_id =  $this->getParameter('facebook_client_id');


        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'facebook_id' => $facebook_app_id,
            ]);
    }

    /**
     * @Route("/login/facebook/{token}", name="app_login_facebook")
     */
    public function loginFacebook(Request $request, ClientRegistry $clientRegistry , ?string $token ,CsrfTokenManagerInterface $csrfTokenManager){
        
        $crsf = $token ;

        $crsf_token = new CsrfToken('authenticate', $token);
        if (!$csrfTokenManager->isTokenValid( $crsf_token )) {
            $this->addFlash('error','You cannot access this page!');
            throw $this->createAccessDeniedException('You cannot access this page!');
           
        }

        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
	    	'public_profile', 'email' // the scopes you want to access
            ]);

    }

    /**
     * @Route("/login/check", name="app_login_check", methods={"POST"})
     */
    public function loginCheck(){

    }


    /**
     * @Route("login/check/facebook",name="app_login_check_facebook")
     */
    public function loginCheckFacebook(Request $request)
    {

    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
