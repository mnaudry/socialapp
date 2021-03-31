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
use Google\Client as GoogleClient;

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
        $google_app_id = $this->getParameter('google_client_id');

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'facebook_id' => $facebook_app_id,
             'google_id' => $google_app_id,
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
     * @Route("/login/check/google",name="app_login_check_google")
     */
    public function loginCheckGoogle(Request $request)
    {
        /*$client = $clientRegistry->getClient('google_main');

         $user = $client->fetchUser();
         dd($user);*/
    }


    /**
     * @Route("/login/google",name="app_login_google")
     */
    public function loginGoogle(Request $request, ClientRegistry $clientRegistry )
    {
       // dd($request->server->get('SYMFONY_PROJECT_DEFAULT_ROUTE_URL'));
//dd($request->getBaseUrlReal());

        

       /* $google_app_id = $this->getParameter('google_client_id');

        $client = new GoogleClient(['client_id' =>  $google_app_id ]);
        $id_token = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEzZThkNDVhNDNjYjIyNDIxNTRjN2Y0ZGFmYWMyOTMzZmVhMjAzNzQiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXpwIjoiNTA3NTQ0NTE4MzAtNnM5MDQ0OWI4czR0cTc2cWJvMXE0M2c0bzlkbGhoMHQuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI1MDc1NDQ1MTgzMC02czkwNDQ5YjhzNHRxNzZxYm8xcTQzZzRvOWRsaGgwdC5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsInN1YiI6IjEwMjI1MDQxNTEyNDczNDA0MTUzOSIsImVtYWlsIjoiY29udGFjdC50ZW5kaUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6ImYyY1VyYlVobUhDZThNRVFzOGRYeFEiLCJuYW1lIjoiTmVpbCBUZW5kaSIsInBpY3R1cmUiOiJodHRwczovL2xoNS5nb29nbGV1c2VyY29udGVudC5jb20vLTVYUkZ6anVkRDE4L0FBQUFBQUFBQUFJL0FBQUFBQUFBQUFBL0FNWnV1Y24tR0VSUWlsSko4UzVMcExJVXhMdUtFenpnSXcvczk2LWMvcGhvdG8uanBnIiwiZ2l2ZW5fbmFtZSI6Ik5laWwiLCJmYW1pbHlfbmFtZSI6IlRlbmRpIiwibG9jYWxlIjoiZnIiLCJpYXQiOjE2MTcxMzg4NTEsImV4cCI6MTYxNzE0MjQ1MSwianRpIjoiOWY3N2ZjMjBiZDNlNjk5ODI3OGJiYTg3MjQ0NmI5MGRjYzYxNmI3OSJ9.CiekoXgMwGjG8m422Xub6cX4yEX5mKlrw-adnSxPp7dfW7xxO6UVYgoJXOFN83a_qFkywU-FR1lP9gNLetM5Zl60t9ylq8dQlW2AXy5mtW3-m5CqzG6yQvTVrcsAd2AeN4eSDXWmOT0UKTHElHkE80UvIOy8kSg-3MdeR9e3dnis9r4MdW76ZOTtDR43bo88Skw4BcGIazgPRa7V4glrc6ahRomzTLPx-mvucQ9X3AVOU0yuK0xeqAwiCHN-uxJYQ9ofRPt6VG8gtIyPu7o-74OuheaBqsTrPt5kdrygV7EuOcg5DLvqzYV-1jYFWhWlu6bjQhXrOHDVxRyzquh6NA";

        $payload = $client->verifyIdToken($id_token);

        dd($payload);*/
       
       /* return $clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                 // the scopes you want to access
            ]);*/
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
