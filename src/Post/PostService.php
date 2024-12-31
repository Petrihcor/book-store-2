<?php

namespace App\Post;

use Kernel\Database\Database;

class PostService
{
    private Database $database;

    public function __construct(Database $database)
    {
        // Инициализируем свойство через конструктор
        $this->database = $database;
    }

    public function addPost(array $data): array
    {

        try {
            $queryBuilder = $this->database->getBuilder();

            $queryBuilder
                ->insert('Posts')
                ->values([
                    'name' => '?',
                    'category_id' => '?',
                    'user_id' => '?',
                    'content' => '?',
                ])
                ->setParameter(0, $data['form']['name'])
                ->setParameter(1, $data['form']['category'])
                ->setParameter(2, $data['form']['user'])
                ->setParameter(3, $data['form']['content'])
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
}