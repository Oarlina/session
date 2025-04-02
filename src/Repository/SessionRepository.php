<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findNonInscrits($session_id) {
        // on va cree une requete embriqué 
        $em = $this->getEntityManager(); // on recupere l'entity manager
        $sub = $em->createQueryBuilder(); // appelle la méthode pour construire la requette

        $qb = $sub; // on cree une nouvelle variable ou on donne la méthode pour faire une requete
        // on cree la requete DQL
        $qb->select('s') // donne un alias a la table que l'ont recupere
            ->from('App\Entity\Intern', 's') // on donne la table que l'on prend et donne son alias
            ->leftJoin('s.sessions', 'se') // on fait un left join sur la table sessions
            ->where('se.id = :id'); // ou l'id de la table session est egale a id du stagiaire
         
        $sub = $em->createQueryBuilder(); // appelle la méthode pour construire la requette
        //on cree la requette DQL
        $sub->select('st') // on donne un alias a la table
            -> from('App\Entity\Intern', 'st') // on donne la table que l'on cherche
            ->where($sub->expr()->notIn('st.id', $qb->getDQL())) // on dit que l'on veut tous les stagiaires qui ne sont pas dans le session
            ->setParameter ('id', $session_id) // on donne a id la vaaleur de sesssion_id
            ->orderBy('st.name'); // et on ordnonne les réponses par les nom des stagiares
        
        $query = $sub->getQuery(); // on recupere la requete sub
        return $query->getResult(); // on retourne la requette DQL
    }

    public function NonCourse($session_id) {
        $entityManager = $this->getEntityManager();
        $sub = $entityManager->createQueryBuilder();
        
        $qb = $sub;
        // on cree la premiere requette
        $qb->select('c')
            ->from('App\Entity\Course', 'c')
            ->leftJoin('c.programs', 'p')
            ->where('p.session = :id');
        $sub = $entityManager->createQueryBuilder();
        // on cree la requete embriqué 
        $sub->select('cc')
            ->from('App\Entity\Course', 'cc')
            ->where($sub->expr()->notIn('cc.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('cc.category, cc.nameCourse'); // il triera d'abord par l'id de la category puis par le nom du module
        
        // on renvoie le resultat
        $query = $sub->getQuery();
        return $query->getResult();
    }
    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
