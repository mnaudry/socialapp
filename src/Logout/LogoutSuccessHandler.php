<?php
namespace App\Logout;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Environment;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface {

    private $twig;
    private $urlGenerator;
    private $params;
    //private $session;
    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator, ContainerBagInterface $params)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->params = $params;
       // $this->session = $session;
    }
    public function onLogoutSuccess(Request $request){

       // dd($request->getSession()->get('facebookId'));
        //dd($this->session);
        //dd(array($this->params->get('facebook_client_id'),$request->getSession()->get('facebookId')));
        //dd($this->params->get('facebook_client_id'));
        $redirect_url = $this->urlGenerator->generate('app_login') ;
    
       if($request->getSession()->get('facebookId')){
           //connected with facebook
           
           return new Response($this->twig->render('security/logout.html.twig',[
             'call_facebook_logout' => true ,
             'app_id' => $this->params->get('facebook_client_id'),
             'redirect_url' => $redirect_url,
            ]));
       }

       

      return new RedirectResponse($redirect_url);

    }
}



?>