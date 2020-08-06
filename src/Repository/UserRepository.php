<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    protected $db;
    public function __construct(ManagerRegistry $registry,Connection $db)
    {
        parent::__construct($registry, User::class);
        $this->db = $db;
    }

    public function createUser(EntityManagerInterface $manager,UserPasswordEncoderInterface $passwordEncoder){
        $user = new User();
        $password = $passwordEncoder->encodePassword($user,"admin");
        $roles[] = "ROLE_SUPER_ADMIN";
        $user->setMatricule(strtoupper(substr(md5(uniqid(rand(1, 6))), 0, 6)));
        $user->setUsername("root");
        $user->setImage("https://cdn4.iconfinder.com/data/icons/e-commerce-icon-set/48/Username_2-512.png");
        $user->setEmail("admin@default.mg");
        $user->setPassword($password);
        $user->setRoles($roles);

        $manager->persist($user);
        $manager->flush();

        return new Response();
    }

    public function findOneByEmail($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByResetToken($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.resetToken = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
