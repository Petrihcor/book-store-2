<?php

namespace App\User;

use Kernel\Database\Database;


class UserService
{
    private Database $database;

    public function __construct(Database $database)
    {
        // Инициализируем свойство через конструктор
        $this->database = $database;
    }
    public function getUsers()
    {
        try {
            $queryBuilder = $this->database->getBuilder();
            $queryBuilder
                ->select('id', 'name', 'password')
                ->from('users');
            $stmt = $queryBuilder->executeQuery();
            return $stmt->fetchAllAssociative();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function addUser(array $data)
    {

        try {
            $queryBuilder = $this->database->getBuilder();

            $queryBuilder
                ->insert('users')
                ->values([
                    'name' => '?', // Используем позиционные параметры
                    'password' => '?',
                ])
                ->setParameter(0, $data['form']['name']) // Позиционные параметры начинаются с 0
                ->setParameter(1, $data['form']['password']);

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