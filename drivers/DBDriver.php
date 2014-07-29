<?php

namespace AndyTruong\QueuePHP\Driver;

use AndyTruong\QueuePHP\QueueDriverInterface;
use AndyTruong\QueuePHP\QueueJobInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use RuntimeException;

class DBDriver implements QueueDriverInterface
{

    const JOB_CLASS = 'AndyTruong\\QueuePHP\\QueueJob';

    /** @var EntityManager */
    private $em;

    /**
     * Inject entity manager.
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get entity manager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            throw new RuntimeException('Entity maanger was not injected to ' . __CLASS__);
        }
        return $this->em;
    }

    /**
     * Get entity repository.
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository(static::JOB_CLASS);
    }

    public function countRetryJobs(QueueJobInterface $job)
    {
        return $this->getEntityManager();
    }

    /**
     * {@inheritdoc}
     * @param null|int $job_id
     * @return QueueJobInterface
     */
    public function get($job_id = null)
    {
        $repos = $this->getRepository();

        if (null !== $job_id) {
            return $repos->find($job_id);
        }

        return $repos->findOneBy([], ['priority' => 'DESC', 'createdAt' => 'ASC']);
    }

    public function getRetryJobs(QueueJobInterface $job, $limit = 50, $offset = 0)
    {
        return $job->getRetryJobs();
    }

    /**
     * Save job to database.
     *
     * @param QueueJobInterface $job
     */
    public function push(QueueJobInterface $job)
    {
        $em = $this->getEntityManager();
        $em->persist($job);
        $em->flush();

        return $id = $job->getId() ? $id : false;
    }

    public function pushMultiple($jobs)
    {
        // persist
        $em = $this->getEntityManager();
        foreach ($jobs as $job) {
            $em->persist($job);
        }
        $em->flush();

        // get results
        $ids = [];
        foreach ($jobs as $job) {
            $ids[] = $id = $job->getId() ? $id : false;
        }
        return $ids;
    }

    public function release(QueueJobInterface $job)
    {
        $job->setState(QueueJobInterface::STATE_FINISHED);
        $em = $this->getEntityManager();
        $em->persist($job);
        $em->flush();
        return true;
    }

    /**
     * {@inheritdoc}
     * @param QueueJobInterface $job
     * @return boolean
     */
    public function delete(QueueJobInterface $job)
    {
        $em = $this->getEntityManager();
        $em->remove($job);
        $em->flush();
        return true;
    }

}
