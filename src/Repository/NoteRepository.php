<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findAllNotesByUser(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Note $note): void
    {
        $this->getEntityManager()->persist($note);
        $this->getEntityManager()->flush();
    }

    public function delete(Note $note): void
    {
        $this->getEntityManager()->remove($note);
        $this->getEntityManager()->flush();
    }

    public function findOneByUser(User $user, int $id): ?Note
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.id = :id')
            ->setParameter('user', $user)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAllByType(int $type): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.type = :type')
            ->setParameter('type', $type)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByMessage(string $message): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.message LIKE :message')
            ->setParameter('message', "%$message%")
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByMessageAndType(string $message, int $type): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.message LIKE :message')
            ->andWhere('n.type = :type')
            ->setParameter('message', "%$message%")
            ->setParameter('type', $type)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Note[] Returns an array of Note objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
