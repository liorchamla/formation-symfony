<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService {
    private $fs;

    public function __construct(Filesystem $fs) {
        $this->fs = $fs;
    }

    public function upload($entity, $field, $directory){
        $getter = "get" . ucfirst($field);
        $setter = "set" . ucfirst($field);

        if(!method_exists($entity, $getter)) throw new \Exception("La méthode $getter n'existe pas sur l'entité " . get_class($entity));
        if(!method_exists($entity, $setter)) throw new \Exception("La méthode $setter n'existe pas sur l'entité " . get_class($entity));

        $file = $entity->$getter();

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($directory, $fileName);

        $entity->$setter($fileName);
    }

    public function initialize($entity, $field, $directory) {
        $getter = "get" . ucfirst($field);
        $setter = "set" . ucfirst($field);

        if(!method_exists($entity, $getter)) throw new \Exception("La méthode $getter n'existe pas sur l'entité " . get_class($entity));
        if(!method_exists($entity, $setter)) throw new \Exception("La méthode $setter n'existe pas sur l'entité " . get_class($entity));

        $entity->$setter(
            new File($directory . '/' . $entity->$getter())
        );
    }
}