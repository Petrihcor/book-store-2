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
                    'image' => '?',
                    'content' => '?',
                ])
                ->setParameter(0, $data['form']['name'])
                ->setParameter(1, $data['form']['category'])
                ->setParameter(2, $data['form']['user'])
                ->setParameter(3, $data['form']['image'])
                ->setParameter(4, $data['form']['content'])
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

    public function getPosts(int $page, int $itemsPerPage): array
    {
        try {
            $queryBuilder = $this->database->getBuilder();

            $offset = ($page - 1) * $itemsPerPage;

            $queryBuilder
                ->select('*')
                ->from('posts')
                ->setFirstResult($offset)
                ->setMaxResults($itemsPerPage)
            ;
            $stmt = $queryBuilder->executeQuery();
            $posts = $stmt->fetchAllAssociative();

            $countQueryBuilder = $this->database->getBuilder();
            $countQueryBuilder
                ->select('COUNT(*) as total')
                ->from('posts');

            $totalPosts = $countQueryBuilder->executeQuery()->fetchOne();
            return [
                'posts' => $posts,
                'total' => (int)$totalPosts,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при получении постов: " . $e->getMessage());
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
                    'p.image',
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
                ->setParameter(0, $data['form']['name'])// Привязываем значение параметра
                ->setParameter(1, $data['form']['category'])
                ->setParameter(2, $data['form']['content'])
            ;
            $parameterIndex = 3; // Индекс для последующих параметров

            // Добавляем условие для обновления изображения, если оно есть
            if (!empty($data['form']['delete_image'])) {
                // Если флажок "удалить изображение" отмечен, очищаем поле image
                $queryBuilder->set('image', 'NULL');
            } elseif (!empty($data['form']['image'])) {
                // Если передано новое изображение, обновляем поле image
                $queryBuilder
                    ->set('image', '?')
                    ->setParameter($parameterIndex++, $data['form']['image']);
            }

            // Указываем условие WHERE в конце
            $queryBuilder
                ->where('id = ?')
                ->setParameter($parameterIndex, $data['form']['id']);

            $queryBuilder->where('id = ?');

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

    public function postSearch(int $page, int $itemsPerPage, ?string $search = null)
    {
        if ($search) {
            $offset = ($page - 1) * $itemsPerPage;
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select('*')
                ->from('posts');
            if (!empty($search)) {
                $queryBuilder->where('name LIKE :search OR content LIKE :search');
                $queryBuilder->setParameter('search', '%' . $search . '%');
            }

            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($itemsPerPage);

            $stmt = $queryBuilder->executeQuery();
            $posts = $stmt->fetchAllAssociative();

            $countQueryBuilder = $this->database->getBuilder();
            $countQueryBuilder->select('COUNT(*) as total')->from('posts');

            if (!empty($search)) {
                $countQueryBuilder->where('name LIKE :search OR content LIKE :search');
                $countQueryBuilder->setParameter('search', '%' . $search . '%');
            }
            $totalPosts = $countQueryBuilder->executeQuery()->fetchOne();
            return [
                'posts' => $posts,
                'total' => (int)$totalPosts,
            ];
        } else {
            return false;
        }
    }
}