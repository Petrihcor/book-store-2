<?php

namespace Kernel\Upload;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader
{
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        // Устанавливаем директорию для загрузки файлов
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    public function upload(array $file): ?string
    {
            #FIXME: раскомментировать нужное, убрать ненужное
//        // Проверка на наличие ошибок при загрузке файла
//        if ($file['error'] !== UPLOAD_ERR_OK) {
//            throw new \Exception("Ошибка загрузки файла: " . $this->getUploadError($file['error']));
//        }

//        // Проверка типа файла
//        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
//        if (!in_array($file['type'], $allowedMimeTypes)) {
//            throw new \Exception("Недопустимый тип файла. Разрешены только JPEG, PNG и GIF.");
//        }

//        // Проверка размера файла (максимум 2 МБ)
//        $maxFileSize = 2 * 1024 * 1024; // 2 МБ
//        if ($file['size'] > $maxFileSize) {
//            throw new \Exception("Размер файла превышает допустимый лимит в 2 МБ.");
//        }

        // Генерация уникального имени файла
        $extension = pathinfo($file['name']['image'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('img_', true) . '.' . $extension;

        // Полный путь для сохранения файла
        $targetFilePath = $this->uploadDir . $uniqueFileName;

        // Проверка наличия директории и создание, если нужно
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true) && !is_dir($this->uploadDir)) {
                throw new \Exception("Не удалось создать директорию для загрузки файлов.");
            }
        }

        // Перемещение загруженного файла в целевую директорию
        if (!move_uploaded_file($file['tmp_name']['image'], $targetFilePath)) {
            throw new \Exception("Не удалось сохранить файл.");
        }

        // Возврат имени файла для сохранения в базе данных
        return $uniqueFileName;
    }

    private function getUploadError(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => "Размер файла превышает значение upload_max_filesize в php.ini.",
            UPLOAD_ERR_FORM_SIZE => "Размер файла превышает указанное значение MAX_FILE_SIZE в HTML-форме.",
            UPLOAD_ERR_PARTIAL => "Файл был загружен только частично.",
            UPLOAD_ERR_NO_FILE => "Файл не был загружен.",
            UPLOAD_ERR_NO_TMP_DIR => "Отсутствует временная директория.",
            UPLOAD_ERR_CANT_WRITE => "Не удалось записать файл на диск.",
            UPLOAD_ERR_EXTENSION => "PHP-расширение остановило загрузку файла."
        ];

        return $errors[$errorCode] ?? "Неизвестная ошибка.";
    }
}