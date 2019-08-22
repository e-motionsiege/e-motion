<?php


namespace App\Service;


use App\Entity\PictureVehicle;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadFileService
{
    private $entityManager;

    /**
     * UploadFileService constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function uploadPictures(int $i, Vehicle $vehicle)
    {
        $dossier = "upload/picture/";
        if (!is_dir($dossier)) {
            @mkdir($dossier, 0777, true);
        }
        $fichier = basename($_FILES['input2']['name'][$i]);
        $extension = pathinfo($fichier, PATHINFO_EXTENSION);
        $newName = sha1(uniqid() . $fichier) . "." . $extension;
        if (move_uploaded_file($_FILES['input2']['tmp_name'][$i], $dossier . $newName)) {
            //Mise à jour de la propriété
            $pictureVehicle = new PictureVehicle();
            $pictureVehicle->setVehicle($vehicle);
            $pictureVehicle->setName('picture'.($i+1));
            $pictureVehicle->setValue($newName);

            $em = $this->entityManager;
            $em->persist($pictureVehicle);
            $em->flush();

            return true;
        }
        return false;
    }

    public function uploadPicturesEdit(int $i, Vehicle $vehicle, PictureVehicle $pictureVehicle)
    {
        $dossier = "upload/picture/";
        if (!is_dir($dossier)) {
            @mkdir($dossier, 0777, true);
        }
        $fichier = basename($_FILES['input2']['name'][$i]);
        $extension = pathinfo($fichier, PATHINFO_EXTENSION);
        $newName = sha1(uniqid() . $fichier) . "." . $extension;
        if (move_uploaded_file($_FILES['input2']['tmp_name'][$i], $dossier . $newName)) {
            $oldName = $pictureVehicle->getValue();
            if (file_exists($dossier . $oldName) && $oldName != NULL)
                unlink($dossier . $oldName);
            //Mise à jour de la propriété
            $pictureVehicle->setVehicle($vehicle);
            $pictureVehicle->setName('picture'.($i+1));
            $pictureVehicle->setValue($newName);

            $em = $this->entityManager;
            $em->persist($pictureVehicle);
            $em->flush();

            return true;
        }
        return false;
    }
}
