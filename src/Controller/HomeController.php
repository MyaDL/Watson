<?php

namespace Watson\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;

class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app, Request $request) {
        $limit = 6;
        $page = 1;
        $links = $app['dao.link']->findAllPaginated($page, $limit);

        return $app['twig']->render('index.html.twig', array(
            'links' => $links,
        ));
    }

    public function articlesAction(Application $app, Request $request, $page) {
        $limit = 15;
        $links = $app['dao.link']->findAllPaginated($page,$limit);

        // Récupérer le nombre total de liens
        $totalLinks = count($app['dao.link']->findAll()); // Adapter cette méthode selon votre DAO

        // Calculer le nombre total de pages
        $totalPages = ceil($totalLinks / $limit);

        return $app['twig']->render('articles.html.twig', array(
            'links' => $links,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ));
    }

    /**
     * Link details controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function linkAction($id, Request $request, Application $app) {
        $link = $app['dao.link']->find($id);
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            // Check if he's author for manage link

        }
        return $app['twig']->render('link.html.twig', array(
            'link' => $link
        ));
    }

    /**
     * Links by tag controller.
     *
     * @param integer $id Tag id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function tagAction($id, Request $request, Application $app) {
        $links = $app['dao.link']->findAllByTag($id);
        $tag   = $app['dao.tag']->findTagName($id);

        return $app['twig']->render('result_tag.html.twig', array(
            'links' => $links,
            'tag'   => $tag
        ));
    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            )
        );
    }

        /**
     * RSS controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function generateRss(Request $request, Application $app){

        // Création du document XML
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;

        // Cration de la structure du flux
        $rss= $xml->createElement("rss");
        $rss->setAttribute("version", '2.0');
        $xml->appendChild($rss);

        // Créationd de la balise channel
        $channel = $xml->createElement("channel");
        $rss->appendChild($channel);
        
        // Ajout des éléments du flux
        $title = $xml->createElement('title', 'FLUX RSS WATSON');
        $link = $xml->createElement('link', 'http://localhost/uel313/Watson/web/RSS');
        $description = $xml->createElement('description', 'Voici notre flux RSS');
        $channel->appendChild($title);
        $channel->appendChild($link);
        $channel->appendChild($description);

        // Ajout d'articles au flux
        $links = $app['dao.link']->findAll();

        for ($i = 1; $i <= count($links); $i++) {
            $item = $xml->createElement('item');

            $itemTitle = $xml->createElement('title', $links[$i]->getTitle());
            $itemLink = $xml->createElement('link', $links[$i]->getUrl());
            $itemDescription = $xml->createElement('description', $links[$i]->getDesc());
    
            $item->appendChild($itemTitle);
            $item->appendChild($itemLink);
            $item->appendChild($itemDescription);
    
            $channel->appendChild($item);

        }

        

        // Définition des en-têtes HTTP pour le contenu XML
        $content = $xml->saveXML();
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');

        return $response;

    }
}
