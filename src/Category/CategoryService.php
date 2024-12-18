<?php

namespace App\Category;

use Kernel\Database\Database;

class CategoryService
{
    private Database $database;

    public function __construct(Database $database)
    {
        // Инициализируем свойство через конструктор
        $this->database = $database;
    }

    public function addCategory(array $data): array
    {

        try {
            $queryBuilder = $this->database->getBuilder();

            $queryBuilder
                ->insert('categories')
                ->values([
                    'name' => '?', // Используем позиционные параметры
                ])
                ->setParameter(0, $data['form']['name'])
            ; // Позиционные параметры начинаются с 0

            // Выполняем запрос и возвращаем результат
            $affectedRows = $queryBuilder->executeStatement();

            return [
                'success' => true,
                'message' => "$affectedRows row(s) inserted successfully.",
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Обрабатываем общие исключения
            return [
                'success' => false,
                'error' => "Error: " . $e->getMessage(),
            ];
        }
    }

    public function getCategories()
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select('id', 'name')
                ->from('categories');
            $stmt = $queryBuilder->executeQuery();
            return $stmt->fetchAllAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }


}