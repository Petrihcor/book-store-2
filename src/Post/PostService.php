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

    public function getPosts()
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select('*')
                ->from('posts');
            $stmt = $queryBuilder->executeQuery();
            return $stmt->fetchAllAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getPost(int $id)
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select(
                    'p.id',
                    'p.name',
                    'p.content',
                    'p.create_date',
                    'p.update_date',
                    'c.name AS category_name',
                    'u.name AS user_name'
                )
                ->from('posts', 'p') // Указываем алиас для таблицы
                ->leftJoin('p', 'categories', 'c', 'p.category_id = c.id') // Присоединяем категории
                ->leftJoin('p', 'users', 'u', 'p.user_id = u.id') // Присоединяем пользователей
                ->where('p.id = ?') // Условие для ID поста
                ->setParameter(0, $id); // Привязываем значение параметра

            $stmt = $queryBuilder->executeQuery();

            return $stmt->fetchAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updatePost(array $data): array
    {
#FIXME: убрать поиск по id через скрытое поле
        try {
            $queryBuilder = $this->database->getBuilder();

            $queryBuilder
                ->update('posts')
                ->set('name' , '?')
                ->set('category_id', '?')
                ->set('content', '?')
                ->set('update_date', 'NOW()')
                ->where('id = ?')
                ->setParameter(0, $data['form']['name'])// Привязываем значение параметра
                ->setParameter(1, $data['form']['category'])
                ->setParameter(2, $data['form']['content'])
                ->setParameter(3, $data['form']['id']);

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

    public function deletePost(int $id)
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->delete('posts')
                ->where('id = ?') // Используем именованный параметр
                ->setParameter(0, $id); // Привязываем значение параметра

            $stmt = $queryBuilder->executeQuery();

            return $stmt->fetchAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}