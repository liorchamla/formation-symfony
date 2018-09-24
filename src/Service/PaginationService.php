<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Permet de gérer la pagination au sein de mes pages de liste
 */
class PaginationService {
    
    /**
     * Le nom de l'entité sur laquelle on pagine
     *
     * @var string
     */
    private $entityName;

    /**
     * Le manager de Doctrine qui permet de créer des requête DQL
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * La limite de pagination
     *
     * @var int
     */
    private $limit;

    /**
     * La page sur laquelle on se trouve
     *
     * @var int
     */
    private $page;

    /**
     * La route sur laquelle on veut que les boutons pointent
     *
     * @var string
     */
    private $route;

    /**
     * Constructeur
     *
     * @param ObjectManager $manager
     * @param integer $limit
     */
    public function __construct(ObjectManager $manager, $limit = 10){
        $this->manager = $manager;
        $this->limit = $limit;
    }

    /**
     * Permet de modifier la route qu'on veut appeler sur les boutons de pagination
     *
     * @param string $route
     * @return self
     */
    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    /**
     * Permet de récupérer la route configurée
     *
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Permet de préciser de quelle entité il s'agit
     *
     * @param string $entityName
     * @return self
     */
    public function setEntity($entityName) {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Permet de savoir de quelle entité on parle
     *
     * @return string
     */
    public function getEntity() {
        return $this->entityName;
    }

    /**
     * Permet de mettre en place une limite personnalisée
     *
     * @param int $limit
     * @return self
     */
    public function setLimit($limit) {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Permet de connaitre la limite
     *
     * @return int
     */
    public function getLimit(){
        return $this->limit;
    }

    /**
     * Permet de donner la page courante sur laquelle on se trouve
     *
     * @param int $page
     * @return self
     */
    public function setPage($page) {
        $this->page = $page;

        return $this;
    }

    /**
     * Permet de connaitre la page courante sur laquelle on se trouve
     *
     * @return int
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Permet de récupérer les données paginées
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getData($page = null, $limit = null) {
        if(empty($this->entityName)) {
            throw new \Exception("Vous devez spécifier l'Entité sur laquelle vous voulez paginer");
        }

        if(!$limit) $limit = $this->limit;
        if(!$page) $page = $this->page;

        $start = $page * $limit - $limit;

        return $this->manager->createQueryBuilder()
                ->select('x')
                ->from($this->entityName, 'x')
                ->setFirstResult($start)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

    }

    /**
     * Permet de connaitre le nombre total de pages
     *
     * @param int $limit
     * @return int
     */
    public function getPages($limit = null) {
        if(!$limit) $limit = $this->limit;

        $total = $this->manager->createQueryBuilder()
                    ->select('COUNT(x)')
                    ->from($this->entityName, 'x')
                    ->getQuery()
                    ->getSingleScalarResult();
        
        return ceil($total / $limit);
    }
    
}