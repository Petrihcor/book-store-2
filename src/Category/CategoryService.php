<?php

namespace App\Category;

use Kernel\Database\Database;
use Symfony\Component\HttpFoundation\Request;

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
                    'name' => '?',
                    'description' => '?',
                    'user_id' => '?'// Используем позиционные параметры
                ])
                ->setParameter(0, $data['form']['name'])
                ->setParameter(1, $data['form']['description'])
                ->setParameter(2, $data['form']['user'])
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

    public function getCategories(?int $page = null, ?int $itemsPerPage = null)
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            if ($page !== null && $itemsPerPage !== null) {
                $offset = ($page - 1) * $itemsPerPage;
                $queryBuilder
                    ->setFirstResult($offset)
                    ->setMaxResults($itemsPerPage);
            }

            $queryBuilder
                ->select('*')
                ->from('categories');
            $stmt = $queryBuilder->executeQuery();
            $categories = $stmt->fetchAllAssociative();

            $countQueryBuilder = $this->database->getBuilder();
            $countQueryBuilder
                ->select('COUNT(*) as total')
                ->from('categories');
            $totalCategories = $countQueryBuilder->executeQuery()->fetchOne();
            return [
                'categories' => $categories,
                'total' =>(int)$totalCategories
            ];

        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getCategory(int $id)
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select(
                    'c.id',
                    'c.name',
                    'c.description',
                    'u.name AS user_name',
                    'c.create_date',
                    'c.update_date',
                )
                ->from('categories', 'c')
                ->leftJoin('c', 'users', 'u', 'c.user_id = u.id')
                ->where('c.id = ?') // Используем именованный параметр
                ->setParameter(0, $id); // Привязываем значение параметра

            $stmt = $queryBuilder->executeQuery();

            return $stmt->fetchAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updateCategory(array $data): array
    {
#FIXME: убрать поиск по id через скрытое поле
        try {
            $queryBuilder = $this->database->getBuilder();

            $queryBuilder
                ->update('categories')
                ->set('name' , '?')
                ->set('description', '?')
                ->set('update_date', 'NOW()')
                ->where('id = ?')
                ->setParameter(0, $data['form']['name'])// Привязываем значение параметра
                ->setParameter(1, $data['form']['description'])
                ->setParameter(2, $data['form']['id']);

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

    public function deleteCategory(int $id)
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->delete('categories')
                ->where('id = ?') // Используем именованный параметр
                ->setParameter(0, $id); // Привязываем значение параметра

            $stmt = $queryBuilder->executeQuery();

            return $stmt->fetchAssociative();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
            // Если нарушение внешнего ключа, выбрасываем понятное исключение
            throw new \Exception("Невозможно удалить категорию, так как с ней связаны посты.");
        } catch (\Exception $e) {
            // Обрабатываем другие ошибки
            throw new \Exception("Ошибка при удалении категории: " . $e->getMessage());
        }
    }
}