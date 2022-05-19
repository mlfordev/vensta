<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    #[Route('/{page<\d+>?1}', name: 'ad_index', methods: ['GET'])]
    public function index(int $page): Response
    {
        return $this->render('ad/index.html.twig', []);
    }

    #[Route('view/{pageId<\d+>?1}', name: 'ad_show', methods: ['GET'])]
    public function show(int $pageId): Response
    {
        return $this->render('ad/show.html.twig', [
            'page_id' => $pageId,
        ]);
    }

    /**
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param AdRepository $adRepository
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route('create', name: 'ad_create', methods: ['GET', 'POST'])]
    public function create(Request $request, FileUploader $fileUploader, AdRepository $adRepository): Response
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ad = $form->getData();
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $ad->setImage($imageFileName);
            }
            $adRepository->add($ad);
            return $this->redirectToRoute('ad_index');
        }
        return $this->renderForm('ad/create.html.twig', [
            'form' => $form,
        ]);
    }
}