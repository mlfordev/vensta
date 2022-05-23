<?php

namespace App\Lib;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Pagination
{
    protected QueryBuilder $provider;
    protected int $page = 1;
    protected int $pageSize = 5;
    protected ?int $total = null;
    protected ?int $lastPage = null;
    protected string $orderBy = 'id';
    protected string $orderDirection = 'DESC';
    protected UrlGeneratorInterface $router;

    public function __construct(QueryBuilder $provider, UrlGeneratorInterface $router, int $page)
    {
        $this->provider = $provider;
        $this->router = $router;
        $this->page = $page;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLastPage(): int
    {
        if (is_null($this->lastPage)) {
            $pageSize = $this->getPageSize();
            $total = $this->getTotal();
            $result = (int)ceil($total / $pageSize);
            $this->lastPage = max($result, 1);
        }
        return $this->lastPage;
    }

    /**
     * @return int|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPreviousPage(): ?int
    {
        $page = $this->getPage() - 1;
        if ($this->hasPage($page)) {
            return $page;
        }
        return null;
    }

    /**
     * @return int|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNextPage(): ?int
    {
        $page = $this->getPage() + 1;
        if ($this->hasPage($page)) {
            return $page;
        }
        return null;
    }

    /**
     * @param int $page
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function hasPage(int $page): bool
    {
        $lastPage = $this->getLastPage();
        return $page >= 1 && $page <= $lastPage;
    }

    public function getUrl(int $page): string
    {
        return $this->router->generate('ad_index', ['page' => $page]);
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTotal(): int
    {
        if (is_null($this->total)) {
            $this->total = (clone $this->provider)
                ->resetQueryPart('select')
                ->select('1')
                ->executeQuery()
                ->rowCount();
        }
        return $this->total;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getData(): array
    {
        $page = $this->getPage();
        $pageSize = $this->getPageSize();

        $limit = $pageSize;
        $offset = ($page - 1) * $pageSize;

        return $this->getTotal() > 0
            ? $this->provider
                ->orderBy($this->orderBy, $this->orderDirection)
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->fetchAllAssociative()
            : [];
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPager(): array
    {
        $pager = [];
        $firstPage = $this->getFirstPage();
        $lastPage = $this->getLastPage();
        if ($firstPage !== $lastPage) {
            $page = $this->getPage();
            $prevPage = $this->getPreviousPage();
            $nextPage = $this->getNextPage();
            $pager['current'] = $page;
            if ($firstPage !== $page) {
                $pager['first'] = $this->getUrl($firstPage);
            }
            if ($prevPage && ($firstPage !== $prevPage)) {
                $pager['prev'] = $this->getUrl($prevPage);
            }
            if ($nextPage && ($nextPage !== $lastPage)) {
                $pager['next'] = $this->getUrl($nextPage);
            }
            if ($lastPage && $lastPage !== $page) {
                $pager['last'] = $this->getUrl($lastPage);
            }
        }
        return $pager;
    }
}