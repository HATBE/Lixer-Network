<?php
  namespace App\Core;

  use PDO;

  abstract class AbstractRepository {

    protected $pdo;
    protected $config;

    public function __construct(Container $container) {
      $this->config = $container->getConfig();
      $this->pdo = $container->getPDO();
    }

    abstract public function getTableName();

    abstract public function getModelName();

    function all($limit = null) {
      $table = $this->getTableName();
      $model = $this->getModelName();

      $query = "SELECT * FROM {$table} " . ($limit != null ? "LIMIT {$limit}" : "").";";
      $stmt = $this->pdo->query($query);
      $finder = $stmt->fetchAll(PDO::FETCH_CLASS, $model);
      return $finder;
    }

    function find($id) {
      $table = $this->getTableName();
      $model = $this->getModelName();
      
      $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = :id;");
      $stmt->execute(['id' => $id]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, $model);
      $find = $stmt->fetch(PDO::FETCH_CLASS);

      return $find;
    }
  }