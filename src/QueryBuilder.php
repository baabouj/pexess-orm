<?php

namespace Pexess\ORM;

use Pexess\ORM\Queries\DeleteQuery;
use Pexess\ORM\Queries\InsertQuery;
use Pexess\ORM\Queries\SelectQuery;
use Pexess\ORM\Queries\UpdateQuery;

class QueryBuilder
{
    private string $table;
    private Database $db;

    public function __construct($table)
    {
        $this->db = Database::instance();
        $this->table = $table;
    }

    public function create(array $options): bool
    {
        $builder = new InsertQuery();

        $builder
            ->into($this->table)
            ->insert($options["data"]);

        [$query, $bindings] = [$builder->getQuery(), $builder->getBindings()];

        $this->db->query($query);

        return $this->db->execute($bindings);
    }

    public function update(array $options): bool
    {
        $builder = new UpdateQuery();

        $builder
            ->from($this->table)
            ->update(array_keys($options['data']))
            ->to(array_values($options['data']))
            ->where($options['where']);

        $this->db->query($builder->getQuery());

        return $this->db->execute($builder->getBindings());
    }

    public function delete(array $options): bool
    {
        $builder = new DeleteQuery();

        $builder->from($this->table)->where($options['where']);

        $this->db->query($builder->getQuery());
        return $this->db->execute($builder->getBindings());
    }

    public function findUnique(array $options)
    {
        $builder = new SelectQuery();
        $builder->from($this->table);

        if (isset($options['select'])) {
            $builder->select($options['select']);
        }

        if (isset($options['where'])) {
            $builder->where($options['where']);
        }

        [$query, $bindings] = [$builder->getQuery(), $builder->getBindings()];

        $this->db->query($query);
        return $this->db->single($bindings) ?? false;
    }

    public function findMany(array $options = []): bool|array
    {
        $builder = new SelectQuery();
        $builder->from($this->table);

        if (isset($options['select'])) {
            $builder->select($options['select']);
        }

        if (isset($options['where'])) {
            $builder->where($options['where']);
        }

        if (isset($options['orderBy'])) {
            is_array($options['orderBy'])
                ? [$by, $order] = [...$options['orderBy'], 'asc']
                : [$by, $order] = [$options['orderBy'], 'asc'];
            $builder->orderBy($by, $order);
        }

        if (isset($options['take'])) {
            $builder->take($options['take']);
        }

        if (isset($options['skip'])) {
            $builder->skip($options['take']);
        }

        [$query, $bindings] = [$builder->getQuery(), $builder->getBindings()];

        $this->db->query($query);

        return $this->db->resultSet($bindings);
    }

    public function count(array $options = []): int|bool
    {
        $builder = new SelectQuery();
        $builder->from($this->table);

        $builder->select('COUNT(*) AS count');

        if (isset($options['where'])) {
            $builder->where($options['where']);
        }

        [$query, $bindings] = [$builder->getQuery(), $builder->getBindings()];

        $this->db->query($query);
        return $this->db->single($bindings)['count'] ?? false;
    }

    public function groupBy(array $options)
    {
        // TODO: Implement groupBy functionality
    }
}
