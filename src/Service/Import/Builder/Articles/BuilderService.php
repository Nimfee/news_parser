<?php

namespace App\Service\Import\Builder\Articles;

use App\Entity\Category;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class BuilderService
{
    /** @var EntityManagerInterface  */
    protected $entityManager;

    /** @var CategoryRepository  */
    protected $categoryRepository;

    /** @var ArticleRepository  */
    protected $articleRepository;

    /**
     * ParserManager constructor.
     * @param CategoryRepository $currencyRepository
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param array $items
     * @return int
     */
    public function createEntities(array $items): int
    {
        $count = 0;
        foreach ($items as $item) {
            $article = $this->articleRepository->findOneBy(
               ['itemId' => $item['itemId']]
            );
            if (!$article) {
                $article = new Article();
                $article->setItemId($item['itemId'] ?? '');
                $article->setLink($item['href'] ?? '');
                $article->setImage($item['image'] ?? '');
                $article->setOverview($item['overview'] ?? '');
                $article->setTitle($item['title'] ?? '');
                if (isset($item['content'])) {
                    $article->setContent(htmlentities(htmlspecialchars($item['content'])));
                }
                $article->setPublishedAt($item['date'] ?? new \DateTime());
                $article->setCategory($this->getCategory($item['category']));
                $this->entityManager->persist($article);
                $count++;
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $count;
    }

    /**
     * @param string $categoryName
     * @return Category|null
     */
    protected function getCategory(string $categoryName): ?Category
    {
        $category = $this->categoryRepository->findOneBy(['name' => $categoryName]);

        if (null === $category) {
            $category = new Category();
            $category->setName($categoryName);
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        return $category;
    }
}
