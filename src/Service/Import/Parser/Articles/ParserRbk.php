<?php

namespace App\Service\Import\Parser\Articles;

use App\Service\Import\Parser\ParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserRbk implements ParserInterface
{
    /** @var HttpClientInterface  */
    private $client;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }


    /**
     * @param string $url
     * @return array
     */
    public function parse(string $url): array
    {
        $response = $this->client->request( 'GET', $url);

        $crawler = new Crawler($response->getContent());

        $i = 0;
        $result = $crawler->filter('#js_news_feed_banner .js-news-feed-list > a')->each(
            function (Crawler $node) use (&$i) {
                if ($i > 15) {
                    exit;
                }
                $article = [];
                $article['href'] = $node->attr('href');
                $article['itemId'] = $node->attr('id');
                $dateNode = $node->filter('.news-feed__item__date-text');

                if ($dateNode->count() > 0) {
                    $this->setDateAndCategory($dateNode->text(), $article);
                }
                $article['title'] = $node->filter('.news-feed__item__title')->first()->text();

                $this->getAdditionalData($node, $article);
                $i++;

                return $article;
            });

        return $result;
    }
    
    /**
     * @param string $categoryDate
     * @param $article
     * @throws \Exception
     */
    private function setDateAndCategory(string $categoryDate, &$article)
    {
        $pieces = explode(',', $categoryDate);
        $article['category'] = $pieces[0] ?? '';
        $dateString = $pieces[1] ?? '';
        $dateString = preg_replace("/[^0-9:]/",'', $dateString);
        $pieces = explode(':', $dateString);
        if (count($pieces) > 1) {
            $date = new \DateTime();
            $date->setTime($pieces[0], $pieces[1]);
            $article['date'] = $date;
        }
    }


    /**
     * @param Crawler $node
     * @param array $article
     */
    private function getAdditionalData(Crawler $node, array &$article)
    {
        try {
            $articleInfo = $this->client->request('GET', $node->attr('href'));

            $crawler = new Crawler($articleInfo->getContent());

            $overview = $crawler->filter('.article__text__overview');
            if ($overview->count() > 0) {
                $article['overview']  = trim($overview->text());
            }

            $img = $crawler->filter('.article__main-image__image');
            if ($img->count() > 0) {
                $article['image'] = $img->first()->attr('src');
                $crawler->filter('.article__main-image')->each(function (Crawler $cr) {
                    foreach ($cr as $n) {
                        $n->parentNode->removeChild($n);
                    }
                });
            }

            $articleText = $crawler->filter('.article__text');
            if ($articleText->count() > 0) {
                
                $article['content'] = trim($articleText->first()->html());
            }
            $articleContent = $crawler->filter('.article__text__pro');
            if ($articleContent->count() > 0) {
                $content = trim($articleContent->first()->html());
                $article['content'] = isset($article['content']) ? $article['content'] . $content : $content;
            }
        } catch (BadRequestException $e) {

            $this->logger->info('Cannot get info form' . $node->attr('href'));
        } catch (TimeoutException $e) {

            $this->logger->info('Cannot get info form' . $node->attr('href'));
        }
    }
}
